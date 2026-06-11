# LINE OA Chatbot - คู่มือการตั้งค่า

สรุปสิ่งที่สร้างแล้วและขั้นตอนการตั้งค่าเพื่อใช้งาน LINE Chatbot สำหรับ LinenSoftTech

---

## ✅ ไฟล์ที่สร้างแล้ว

| ไฟล์ | คำอธิบาย |
|-----|---------|
| [LineMessagingService.php](file:///Users/marosdeeuma/linin-soft-tech/app/Services/LineMessagingService.php) | Service จัดการ LINE API |
| [LineChatbotController.php](file:///Users/marosdeeuma/linin-soft-tech/app/Http/Controllers/Api/LineChatbotController.php) | Controller รับ Webhook |

## ✅ ไฟล์ที่แก้ไขแล้ว

| ไฟล์ | การเปลี่ยนแปลง |
|-----|--------------|
| [.env.example](file:///Users/marosdeeuma/linin-soft-tech/.env.example) | เพิ่ม LINE credentials |
| [services.php](file:///Users/marosdeeuma/linin-soft-tech/config/services.php) | เพิ่ม LINE config |
| [api.php](file:///Users/marosdeeuma/linin-soft-tech/routes/api.php) | เพิ่ม Webhook route |

---

## 📋 ขั้นตอนการตั้งค่า LINE OA

### 1. สร้าง LINE Official Account

1. ไปที่ [LINE Business Center](https://manager.line.biz/)
2. คลิก **"สร้างบัญชี"** หรือ **"Create Account"**
3. กรอกข้อมูล:
   - ชื่อบัญชี: `LinenSoftTech`
   - หมวดหมู่: `บริการ` หรือ `ธุรกิจ`
4. สร้างบัญชีสำเร็จ

### 2. เปิดใช้งาน Messaging API

1. เข้าไปที่ LINE OA ที่สร้างไว้
2. ไปที่ **Settings** > **Messaging API**
3. คลิก **"Enable Messaging API"**
4. เลือก Provider หรือสร้างใหม่
5. จด **Channel ID** และ **Channel Secret**

### 3. สร้าง Channel Access Token

1. ไปที่ [LINE Developers Console](https://developers.line.biz/console/)
2. เลือก Provider > Channel ที่สร้าง
3. ในแท็บ **Messaging API**
4. Scroll ลงไปหา **Channel access token**
5. คลิก **"Issue"** เพื่อสร้าง Long-lived token
6. **Copy Token เก็บไว้**

### 4. ตั้งค่า Webhook URL

ใน LINE Developers Console:
1. ไปที่ **Messaging API** tab
2. หา **Webhook settings**
3. ตั้ง Webhook URL เป็น:
   ```
   https://www.linentechpro.com/api/line/webhook
   ```
4. **เปิด** "Use webhook"
5. คลิก **Verify** เพื่อทดสอบ

### 5. ปิด Auto-reply

1. ใน LINE Developers Console
2. หา **LINE Official Account features**
3. **ปิด** "Auto-reply messages"
4. **ปิด** "Greeting messages" (หรือเปิดทิ้งไว้ก็ได้)

---

## ⚙️ ตั้งค่าบน VPS

### 1. เพิ่ม Environment Variables

SSH เข้า VPS แล้วแก้ไข [.env](file:///Users/marosdeeuma/linin-soft-tech/.env):

```bash
nano /path/to/linin-soft-tech/.env
```

เพิ่มบรรทัดนี้:
```env
LINE_CHANNEL_ACCESS_TOKEN=ใส่_Token_ที่_Copy_มา
LINE_CHANNEL_SECRET=ใส่_Channel_Secret_ที่_Copy_มา
```

### 2. Clear Config Cache

```bash
cd /path/to/linin-soft-tech
php artisan config:clear
php artisan config:cache
```

### 3. ตรวจสอบ Route

```bash
php artisan route:list | grep line
```

ควรเห็น:
```
POST   api/line/webhook   App\Http\Controllers\Api\LineChatbotController@webhook
```

---

## 🧪 ทดสอบ

### วิธีที่ 1: ผ่าน LINE App

1. สแกน QR Code ของ LINE OA
2. เพิ่มเป็นเพื่อน
3. พิมพ์ "เมนู" หรือ "menu"
4. ควรเห็น Quick Reply แสดงเมนูหลัก

### วิธีที่ 2: ใช้ cURL ทดสอบ

```bash
curl -X POST https://www.linentechpro.com/api/line/webhook \
  -H "Content-Type: application/json" \
  -H "X-Line-Signature: test" \
  -d '{"events": []}'
```

---

## 📱 เมนูที่ใช้งานได้

| คำสั่ง | การทำงาน |
|-------|---------|
| `เมนู` / `menu` | แสดงเมนูหลัก |
| `📊 สรุปวันนี้` | สรุปยอดงาน/ลูกค้า/พลังงานวันนี้ |
| `👥 ลูกค้า` | เลือกกลุ่มลูกค้า → ดูข้อมูล |
| `📦 สต๊อก` | เลือกประเภท → ดูยอดคงเหลือ |
| `⚡ พลังงาน` | เลือกประเภท → ดูประวัติ 7 วัน |
| `👷 พนักงาน` | เลือกแผนก → ดูรายชื่อ |
| `⚙️ เครื่องจักร` | ดูรายการเครื่องซัก/อบ |

---

## 🔧 การแก้ไขปัญหา

### Bot ไม่ตอบกลับ
1. ตรวจสอบ Webhook URL ใน LINE Console
2. ตรวจสอบ SSL Certificate บน VPS
3. ดู Log: `tail -f storage/logs/laravel.log`

### Signature Invalid
1. ตรวจสอบ `LINE_CHANNEL_SECRET` ใน [.env](file:///Users/marosdeeuma/linin-soft-tech/.env)
2. รัน `php artisan config:clear`

### ไม่เห็นเมนู Quick Reply
- Quick Reply จะแสดงเฉพาะบน Mobile App
- ไม่แสดงบน LINE PC

---

## 🚀 ขั้นตอนถัดไป (อนาคต)

- [ ] เพิ่ม Rich Menu (เมนูรูปภาพด้านล่าง)
- [ ] เพิ่มการแจ้งเตือน Push Message
- [ ] เพิ่ม AI ตอบคำถามอัจฉริยะ
