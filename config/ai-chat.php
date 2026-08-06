<?php

/*
|--------------------------------------------------------------------------
| AI Chat (หน้าแชท AI แบบ jarvis)
|--------------------------------------------------------------------------
|
| Model catalog สำหรับหน้า /ai-chat — ใช้ทั้ง render dropdown ฝั่ง client
| และ validate model ฝั่ง server (อนุญาตเฉพาะ model ในรายการนี้ ที่ provider เปิดอยู่)
| ราคาโดยประมาณจาก https://wavespeed.ai/llm — แก้ที่นี่จุดเดียวเมื่อราคาเปลี่ยน
| pricing หน่วยเป็น USD ต่อ 1M tokens
|
| แต่ละ model ผูกกับ provider ผ่าน key 'provider' → config 'providers' ด้านล่าง
| เพิ่มเจ้าใหม่ที่พูดภาษา OpenAI chat completions = เพิ่ม block ใน 'providers'
| ไม่ต้องเขียนคลาสใหม่ (ดู App\Services\Llm\OpenAiCompatibleProvider)
|
*/

return [

    'default_provider' => env('AI_CHAT_DEFAULT_PROVIDER', 'wavespeed'),

    'default_model' => env('AI_CHAT_DEFAULT_MODEL', env('WAVESPEED_LLM_MODEL', 'minimax/minimax-m2.7')),

    // จำนวนรอบสูงสุดของ tool loop ต่อหนึ่งคำตอบ (ของเราเอง ไม่ใช่ข้อจำกัดของ provider)
    'max_iterations' => (int) env('AI_CHAT_MAX_ITERATIONS', env('WAVESPEED_MAX_ITERATIONS', 5)),

    'providers' => [

        'wavespeed' => [
            'label' => 'WaveSpeed',
            'driver' => 'openai-compatible',
            'api_key' => env('WAVESPEED_API_KEY'),
            'base_url' => env('WAVESPEED_BASE_URL', 'https://llm.wavespeed.ai/v1'),
            'default_model' => env('WAVESPEED_LLM_MODEL', 'minimax/minimax-m2.7'),
            'requires_key' => true,
            'timeout' => (int) env('WAVESPEED_TIMEOUT', 25),
            'max_tokens' => (int) env('WAVESPEED_MAX_TOKENS', 1024),
        ],

        // 9Router — AI router แบบ OpenAI-compatible (รวมหลายเจ้าไว้หลัง endpoint เดียว
        // ต้นทุนต่อ model ต่างกันมาก) โฮสต์ที่ไหนก็ได้: เครื่อง dev, server ในองค์กร, cloud
        // ที่อยู่เป็นแค่ค่า config — ตั้ง NINEROUTER_BASE_URL เมื่อไหร่ = เปิดใช้งานเมื่อนั้น (ว่าง = ปิด)
        '9router' => [
            'label' => '9Router',
            'driver' => 'openai-compatible',
            'api_key' => env('NINEROUTER_API_KEY'),
            'base_url' => env('NINEROUTER_BASE_URL'), // ไม่มีค่า default โดยตั้งใจ — ว่าง = provider ปิด
            'default_model' => env('NINEROUTER_MODEL', 'oc/deepseek-v4-flash-free'),
            'requires_key' => false, // 9Router ไม่บังคับ auth; ถ้าโฮสต์ไว้หลัง auth ค่อยตั้ง API key
            'timeout' => (int) env('NINEROUTER_TIMEOUT', 60),
            'max_tokens' => (int) env('NINEROUTER_MAX_TOKENS', 2048),
        ],

    ],

    // คุมค่าใช้จ่าย: จำนวนข้อความที่ส่งได้ต่อนาทีต่อ user (stream endpoint)
    'rate_limit_per_minute' => env('AI_CHAT_RATE_LIMIT', 6),

    // ── ระบบ documents — เอกสารที่ staff อัปโหลด ให้ AI agent ใช้ตอบ ──
    // รูป: OCR ตอนอัปโหลด (tha+eng) เก็บเป็น text → ค้นได้ทุกโมเดล
    //      + ส่งภาพจริงให้โมเดล vision ตอนถาม (โมเดลที่ `vision: true` ใน catalog
    //      หรือ vision_fallback_model เมื่อโมเดลปัจจุบันอ่านรูปไม่ได้)
    'documents' => [
        'enabled' => (bool) env('AI_CHAT_DOCUMENTS_ENABLED', true),

        // ไฟล์ที่รับอัปโหลด (extension) — แยก kind: image / pdf / office
        'allowed_extensions' => [
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'pdf' => ['pdf'],
            'office' => ['doc', 'docx', 'xls', 'xlsx', 'txt'],
        ],
        'max_file_size_mb' => (int) env('AI_CHAT_DOCUMENT_MAX_MB', 20),

        // ขีดจำกัดการสกัด/ประมวลผล — ป้องกัน OCR เทลากับไฟล์ยักษ์
        'max_pdf_pages' => (int) env('AI_CHAT_DOCUMENT_MAX_PDF_PAGES', 30),
        'max_extracted_chars' => (int) env('AI_CHAT_DOCUMENT_MAX_EXTRACTED_CHARS', 50000),

        // chunk เนื้อหาสำหรับค้น (search_documents เอาคำตรงสุดใน chunks)
        'chunk_size_chars' => (int) env('AI_CHAT_DOCUMENT_CHUNK_SIZE', 2000),
        'chunk_overlap_chars' => (int) env('AI_CHAT_DOCUMENT_CHUNK_OVERLAP', 200),

        // tesseract | off (off = ข้าม OCR: รูป/PDF ได้แค่ชื่อไฟล์, เนื้อหาเป็นค่าว่าง)
        'ocr_mode' => env('AI_CHAT_DOCUMENT_OCR_MODE', 'tesseract'),

        // เก็บไฟล์ที่ disk นี้ (ใช้ config filesystems.php)
        'storage_disk' => env('AI_CHAT_DOCUMENT_STORAGE_DISK', 'local'),
        'storage_dir' => 'documents',

        // โมเดลที่ใช้เมื่อต้องดูรูปจริง แต่โมเดลที่ผู้ใช้เลือกเป็น text-only
        // (ต้องเป็น model ที่ vision: true ใน catalog)
        'vision_fallback_model' => env('AI_CHAT_DOCUMENT_VISION_FALLBACK_MODEL', 'google/gemini-3.5-flash'),
    ],

    // คุมค่าใช้จ่าย: จำนวนคำตอบ AI สูงสุดต่อวันต่อ user
    'daily_message_limit' => env('AI_CHAT_DAILY_LIMIT', 200),

    // ── ระบบเครดิต (หน่วยบาท) ──

    // อัตราแลกเปลี่ยน USD → THB สำหรับคิดต้นทุน
    'usd_to_thb' => (float) env('AI_CHAT_USD_TO_THB', 37),

    // ค่าคอม (%) บวกบนต้นทุนจริง — รายได้ dev เช่น 30 = บวก 30%
    'commission_percent' => (float) env('AI_CHAT_COMMISSION_PERCENT', 30),

    // model ที่ไม่มีราคาใน catalog ใช้ราคานี้ — กันหลุดเป็นไม่คิดเงิน (USD ต่อ 1M tokens)
    // ไม่กำหนด cachedInputPerMTok → AiCreditService คิด cache hit ที่ราคา input เต็ม (ไม่ undercharge)
    'fallback_pricing' => [
        'inputPerMTok' => (float) env('AI_CHAT_FALLBACK_INPUT_PER_MTOK', 5),
        'outputPerMTok' => (float) env('AI_CHAT_FALLBACK_OUTPUT_PER_MTOK', 25),
    ],

    // เตือนเครดิตใกล้หมดเมื่อต่ำกว่า (บาท)
    'low_balance_threshold_thb' => (float) env('AI_CHAT_LOW_BALANCE_THB', 5),

    'models' => [
        // ── MiniMax ──
        [
            'id' => 'minimax/minimax-m2.7',
            'provider' => 'wavespeed',
            'label' => 'MiniMax M2.7',
            'description' => 'ค่าเริ่มต้น — เร็ว ราคาถูก context 205K',
            'vendor' => 'MiniMax',
            'vision' => false,
            'pricing' => ['inputPerMTok' => 0.3, 'cachedInputPerMTok' => 0.06, 'outputPerMTok' => 1.2],
        ],
        [
            'id' => 'minimax/minimax-m3',
            'provider' => 'wavespeed',
            'label' => 'MiniMax M3',
            'description' => 'รุ่นใหม่กว่า M2.7 — งาน agent/วิเคราะห์เอกสาร context 1M ราคาประหยัด',
            'vendor' => 'MiniMax',
            'vision' => true,
            'pricing' => ['inputPerMTok' => 0.6, 'cachedInputPerMTok' => 0.12, 'outputPerMTok' => 2.4],
        ],
        // ── Anthropic ──
        [
            'id' => 'anthropic/claude-fable-5',
            'provider' => 'wavespeed',
            'label' => 'Claude Fable 5',
            'description' => 'เรือธงตระกูล Claude 5 — reasoning ขั้นสูง งาน agent/โค้ดระยะยาว context 1M',
            'vendor' => 'Anthropic',
            'vision' => true,
            'pricing' => ['inputPerMTok' => 10, 'outputPerMTok' => 50],
        ],
        [
            'id' => 'anthropic/claude-opus-4.8',
            'provider' => 'wavespeed',
            'label' => 'Claude Opus 4.8',
            'description' => 'โค้ดและงาน agent ซับซ้อน context 1M — ถูกกว่า Opus รุ่นก่อน',
            'vendor' => 'Anthropic',
            'vision' => true,
            'pricing' => ['inputPerMTok' => 4.75, 'outputPerMTok' => 23.75],
        ],
        [
            'id' => 'anthropic/claude-sonnet-4.6',
            'provider' => 'wavespeed',
            'label' => 'Claude Sonnet 4.6',
            'description' => 'สมดุลคุณภาพ/ราคา — context 1M รองรับงานทั่วไปถึงซับซ้อน',
            'vendor' => 'Anthropic',
            'vision' => true,
            'pricing' => ['inputPerMTok' => 2.85, 'outputPerMTok' => 14.25],
        ],
        // ── OpenAI ──
        [
            'id' => 'openai/gpt-5.5',
            'provider' => 'wavespeed',
            'label' => 'GPT-5.5',
            'description' => 'agentic coding/computer use/deep research — context 1M',
            'vendor' => 'OpenAI',
            'vision' => true,
            'pricing' => ['inputPerMTok' => 5, 'outputPerMTok' => 30],
        ],
        [
            'id' => 'openai/gpt-5.2-pro',
            'provider' => 'wavespeed',
            'label' => 'GPT-5.2 Pro',
            'description' => 'โมเดลเรือธงจาก OpenAI',
            'vendor' => 'OpenAI',
            'vision' => true,
            'pricing' => ['inputPerMTok' => 15, 'outputPerMTok' => 120],
        ],
        // ── Google ──
        [
            'id' => 'google/gemini-3.5-flash',
            'provider' => 'wavespeed',
            'label' => 'Gemini 3.5 Flash',
            'description' => 'โค้ดขั้นสูง/งาน agent ขนาน context 1M รองรับ multimodal',
            'vendor' => 'Google',
            'vision' => true,
            'pricing' => ['inputPerMTok' => 1.5, 'outputPerMTok' => 9],
        ],
        [
            'id' => 'google/gemini-3-flash-preview',
            'provider' => 'wavespeed',
            'label' => 'Gemini 3 Flash',
            'description' => 'ตอบเร็ว เหมาะกับบทสนทนาทั่วไป',
            'vendor' => 'Google',
            'vision' => true,
            'pricing' => ['inputPerMTok' => 0.3, 'outputPerMTok' => 2.5],
        ],
        // ── DeepSeek ──
        [
            'id' => 'deepseek/deepseek-v4',
            'provider' => 'wavespeed',
            'label' => 'DeepSeek V4',
            'description' => 'ราคาประหยัด ความสามารถสูง',
            'vendor' => 'DeepSeek',
            'vision' => false,
            'pricing' => ['inputPerMTok' => 0.28, 'outputPerMTok' => 1.1],
        ],
        [
            'id' => 'deepseek/deepseek-v4-pro',
            'provider' => 'wavespeed',
            'label' => 'DeepSeek V4 Pro',
            'description' => 'เก่งโค้ด/คณิต/งาน agent ระดับท็อป context 1M ราคาคุ้ม',
            'vendor' => 'DeepSeek',
            'vision' => false,
            'pricing' => ['inputPerMTok' => 1.84, 'outputPerMTok' => 3.66],
        ],
        // ── 9Router ──
        // ต้นทุน token เป็น 0 จึงคิดค่าบริการคงที่ต่อข้อความแทนค่าคอมแบบ % (% ของ 0 คือ 0)
        //
        // label/description ที่ผู้ใช้เห็น ห้ามบอกว่า model ไหนไม่มีต้นทุน — ทุก model
        // มีค่าบริการเสมอ ต่างกันแค่มาก/น้อย ถ้าเพิ่ม model ที่มีต้นทุนจริงให้ใส่ pricing แทน flat_fee_thb
        [
            'id' => 'oc/deepseek-v4-flash-free',
            'provider' => '9router',
            'label' => 'DeepSeek V4 Flash',
            'description' => 'ประหยัดที่สุด — ค่าบริการต่อข้อความต่ำมาก',
            'vendor' => 'DeepSeek',
            'vision' => false,
            'pricing' => ['inputPerMTok' => 0, 'cachedInputPerMTok' => 0, 'outputPerMTok' => 0],
            'flat_fee_thb' => (float) env('AI_CHAT_FREE_MODEL_FEE_THB', 0.25),
        ],
    ],

];
