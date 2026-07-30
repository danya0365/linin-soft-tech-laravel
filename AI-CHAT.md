# AI Chat (หน้า /ai-chat)

หน้าแชท AI เต็มจอแบบโปรเจค jarvis-nextjs + ตอบ**ข้อมูลธุรกิจ**ได้ผ่าน tool calling
(reuse 14 tools เดียวกับ LINE bot) — streaming ผ่าน SSE, ประวัติแชทเก็บใน **DB แยกตาม user**
และมี**ระบบเครดิต (บาท)** หักตามใช้จริง + ค่าคอม

## การตั้งค่า

```env
AI_CHAT_DEFAULT_PROVIDER=wavespeed         # provider เริ่มต้น (ดู config/ai-chat.php)
WAVESPEED_API_KEY=          # จำเป็น — ถ้าไม่มี provider ไหนพร้อมเลย หน้าแชทจะขึ้นเตือนและใช้งานไม่ได้
WAVESPEED_LLM_MODEL=minimax/minimax-m2.7   # model เริ่มต้น

# 9Router — AI router แบบ OpenAI-compatible (โฮสต์ที่ไหนก็ได้ ใช้บน production ได้)
# ตั้ง BASE_URL = เปิดใช้งาน / เว้นว่าง = ปิด
NINEROUTER_BASE_URL=
NINEROUTER_API_KEY=
NINEROUTER_MODEL=oc/deepseek-v4-flash-free

# คุมค่าใช้จ่าย
AI_CHAT_RATE_LIMIT=6        # ข้อความ/นาที/user (stream endpoint)
AI_CHAT_DAILY_LIMIT=200     # คำตอบ AI สูงสุด/วัน/user

# ระบบเครดิต (บาท)
AI_CHAT_USD_TO_THB=37          # อัตราแลกเปลี่ยนคิดต้นทุน
AI_CHAT_COMMISSION_PERCENT=30  # ค่าคอม % บวกบนต้นทุน (รายได้ dev)
```

## ระบบเครดิต

- หักเครดิตหลังได้คำตอบแต่ละครั้ง (รวมกรณีกดหยุดกลางทาง):
  `หัก (บาท) = ต้นทุน USD × usd_to_thb × (1 + commission%/100)`
- model ที่กำหนด `flat_fee_thb` ใน catalog → `หัก = ต้นทุนบาท + ค่าคอมคงที่ต่อข้อความ`
  (ใช้กับ model ฟรี เพราะค่าคอม % ของ 0 คือ 0 → dev ไม่ได้ส่วนแบ่ง)
  ค่าคอมคงที่ไม่ผันตามจำนวน token; ปรับที่ `AI_CHAT_FREE_MODEL_FEE_THB` (ค่าเริ่มต้น 0.25 บาท)
- model ที่ไม่มีราคาใน catalog ใช้ `fallback_pricing` — ไม่มีทางใช้ฟรี
- เครดิต ≤ 0 → ส่งข้อความใหม่ไม่ได้ (HTTP 402) — ยอดสุดท้ายรู้หลังตอบจบ จึงติดลบเล็กน้อยได้
- ledger ทุกรายการอยู่ใน `ai_credit_transactions` (เก็บแยก `cost_thb` / `commission_thb` ต่อแถว
  + `balance_after` + กันหักซ้ำด้วย unique `ai_chat_message_id`)
- ยอด cache ที่ `users.ai_credit_balance` (เปลี่ยนผ่าน `AiCreditService` เท่านั้น — lockForUpdate)
- **หลังบ้าน admin**: เมนู Admin → AI → เครดิต AI (`/ai-credits`)
  - รายชื่อ user + ยอดคงเหลือ + การ์ดสรุป: เครดิตรวม / ต้นทุนรวม / **รายได้ค่าคอมรวม**
  - หน้า user: เติมเครดิต (บวก) / ปรับยอด (ลบได้) + ประวัติธุรกรรม
- หน้าแชท: ยอดคงเหลือบน header (เขียว/เหลือง/แดง), ยอดหักจริงต่อข้อความ, toast เตือนใกล้หมด (< ฿5 config ได้)

รายการ model + ราคา แก้ที่ `config/ai-chat.php` จุดเดียว (server validate จากรายการนี้)

ต้องรัน migration ก่อนใช้: `sail artisan migrate`
(ตาราง `ai_chat_sessions`, `ai_chat_messages`, `ai_credit_transactions` + คอลัมน์ `users.ai_credit_balance`)

## สิทธิ์เข้าถึง

เฉพาะ **supervisor / manager / admin** ผ่าน middleware ใหม่ `staff`
(`app/Http/Middleware/IsStaff.php`) — เหมือนเงื่อนไขของ mini-chat

## เส้นทาง

| Route | หน้าที่ |
|---|---|
| `GET /ai-chat` | หน้าแชท (Blade) |
| `GET/POST /api/ai-chat/sessions` | รายการ / สร้าง session |
| `GET/PATCH/DELETE /api/ai-chat/sessions/{id}` | ดูพร้อมข้อความ / เปลี่ยนชื่อ-model / ลบ (soft) |
| `POST /api/ai-chat/sessions/{id}/stream` | ส่งข้อความ + streaming tool loop (throttle `ai-chat`) |
| `GET /api/ai-chat/stats` | สถิติ token ของ user (วันนี้/ทั้งหมด/แยก model) |

ทุก endpoint scope ด้วย `user_id` — เข้าถึง session คนอื่นได้ 404

## สถาปัตยกรรม streaming tool loop

```
browser ──POST {content,model,settings}──▶ AiChatController::stream
   ▲                                          │ บันทึก user msg ลง DB ก่อน
   │ SSE: meta → content deltas →             │ สร้าง context จากประวัติใน DB
   │      tool_status → usage → done → [DONE] ▼
   └────────────────────────── AiChatStreamService (extends AiChatService)
                                  │ เรียก provider ของ model นั้น stream:true + tools
                                  │ parse SSE ฝั่ง server, สะสม tool_call deltas
                                  │ executeTool() (14 tools เดิมของ LINE bot)
                                  │ วน loop จนได้คำตอบ → บันทึก assistant msg ลง DB
```

- client ส่งเฉพาะข้อความใหม่ — server เป็นเจ้าของ history (กัน tampering)
- tool calls กลางทางไม่เก็บใน DB (log อย่างเดียว) เก็บเฉพาะข้อความ user/assistant
- model ไม่รองรับ tools (HTTP 400) → fallback JSON-intent mode (non-stream) อัตโนมัติ
- กดหยุดกลางทาง → server บันทึก partial ไว้ (`is_partial`)
- usage จริงจาก API บวกสะสมข้ามทุก iteration; ถ้าไม่มีใช้สูตร ~3 ตัวอักษร/token (`is_estimated`)
- model ตระกูล reasoning นับ reasoning token รวมใน `max_tokens` — ถ้าเพดานต่ำจนไม่เหลือคำตอบ
  ระบบจะแจ้งสาเหตุแทนการส่งข้อความว่าง

## LLM provider (สลับเจ้าได้)

`LlmProvider` (port) + `OpenAiCompatibleProvider` (adapter) + `LlmProviderManager` (registry)

เจ้าที่ตั้งค่าไว้แล้ว:

| provider | คืออะไร | model ฟรี |
|---|---|---|
| `wavespeed` | ผู้ให้บริการ LLM โดยตรง | ไม่มี |
| `9router` | AI router รวมหลายเจ้าไว้หลัง endpoint เดียว โฮสต์ที่ไหนก็ได้ | มี |

- **"ฟรีหรือไม่" เป็นคุณสมบัติของ model ไม่ใช่ของ provider** — เจ้าเดียวกันมีได้ทั้ง model ฟรี
  และ model เสียเงิน คิดเงินตาม `pricing` / `flat_fee_thb` ของ model นั้น
- **ที่อยู่ของ provider เป็นแค่ค่า config** — `9router` จะรันบนเครื่อง dev, server ในองค์กร
  หรือ cloud ก็ได้ ไม่มีอะไรผูกกับ localhost และใช้บน production ได้ตามปกติ
- แต่ละ model ใน `config('ai-chat.models')` ระบุ `provider` → route ไปเจ้านั้นตอน runtime
- ผู้ใช้เลือก provider เองผ่าน dropdown (จัดกลุ่มตามชื่อ provider)
- เพิ่มเจ้าใหม่ที่พูดภาษา OpenAI chat completions = เพิ่ม block ใน `config('ai-chat.providers')`
  **ไม่ต้องเขียนคลาสใหม่**; เจ้าที่ใช้ protocol อื่นให้เขียน adapter ใหม่ที่ implement `LlmProvider`
- provider ถือว่า**พร้อมใช้งาน**เมื่อมี `base_url` (และมี key ถ้า `requires_key`) และไม่ได้ตั้ง
  `enabled => false` — เจ้าที่ยังไม่ได้ตั้งค่าจะไม่โผล่ใน dropdown และยิง model ของมันตรงๆ ได้ 422
- adapter ทน quirk: gateway บางตัวต่อ `data: [DONE]` ท้าย body ของ response ที่ไม่ได้ stream

## ไฟล์ที่เกี่ยวข้อง

- `app/Services/AiChatStreamService.php` — streaming tool loop (extends `AiChatService`)
- `app/Services/AiCreditService.php` — คำนวณต้นทุน/ค่าคอม + หัก/เติม/ปรับเครดิต (ledger)
- `app/Http/Controllers/AiChatController.php` — หน้าแชท + stream endpoint (เช็คเครดิต + หักหลังตอบ)
- `app/Http/Controllers/Api/AiChatSessionController.php` — sessions CRUD + stats + ยอดเครดิต
- `app/Http/Controllers/AiCreditController.php` — หลังบ้านจัดการเครดิต (admin)
- `app/Models/AiChatSession.php`, `app/Models/AiChatMessage.php`, `app/Models/AiCreditTransaction.php`
- `app/Http/Middleware/IsStaff.php` (alias `staff`)
- `app/Exceptions/ClientDisconnectedException.php`
- `app/Contracts/LlmProvider.php` — port ของผู้ให้บริการ LLM
- `app/Services/Llm/OpenAiCompatibleProvider.php` — adapter สำหรับ endpoint แบบ OpenAI
- `app/Services/Llm/LlmProviderManager.php` — registry + route model → provider + allowlist
- `app/Exceptions/LlmApiException.php` (`WaveSpeedApiException` เป็น alias เก่า deprecated)
- `config/ai-chat.php` — providers + model catalog + ราคา + limits + เครดิต/ค่าคอม
- `resources/views/ai-chat/index.blade.php`, `resources/views/ai-credit/*.blade.php`,
  `public/js/ai-chat.js`, `public/css/ai-chat.css`
- tests: `tests/Unit/AiChatStreamServiceTest.php`, `tests/Unit/Llm/OpenAiCompatibleProviderTest.php`,
  `tests/Unit/Llm/LlmProviderManagerTest.php`, `tests/Unit/AiCreditServiceTest.php`,
  `tests/Feature/AiChatSessionApiTest.php`,
  `tests/Feature/AiCreditTest.php` (Feature ใช้ DatabaseTransactions — ห้าม RefreshDatabase)
- `tests/Feature/Ai/NineRouterSmokeTest.php` — ยิง 9Router ตัวจริง (`@group live`)
  ข้ามอัตโนมัติเมื่อไม่ได้ตั้ง `NINEROUTER_BASE_URL` หรือต่อไม่ติด
  รันด้วย `php artisan test tests/Feature/Ai/NineRouterSmokeTest.php`

## ข้อแตกต่างจาก mini-chat เดิม

- mini-chat (มุมขวาล่าง) = rule-based + AI ไม่ stream, ไม่เก็บประวัติ — ไม่ถูกแตะ
- /ai-chat = streaming + tool calling + ประวัติใน DB + เลือก model + สถิติ token

settings ฝั่ง client (context window, max tokens) เก็บใน localStorage: `linin.ai-chat.settings`
