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

        // endpoint OpenAI-compatible ในเครื่อง สำหรับ dev/ทดสอบ flow เต็มรูปแบบ
        // โดยไม่กิน token จริง — ปิดไว้เป็นค่าเริ่มต้น เปิดเฉพาะเครื่อง dev
        'local' => [
            'label' => 'Local (dev)',
            'driver' => 'openai-compatible',
            'api_key' => env('LLM_LOCAL_API_KEY'),
            'base_url' => env('LLM_LOCAL_BASE_URL', 'http://localhost:20128/v1'),
            'default_model' => env('LLM_LOCAL_MODEL', 'oc/deepseek-v4-flash-free'),
            'requires_key' => false,
            'enabled' => filter_var(env('LLM_LOCAL_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
            'timeout' => (int) env('LLM_LOCAL_TIMEOUT', 60),
            'max_tokens' => (int) env('LLM_LOCAL_MAX_TOKENS', 2048),
        ],

    ],

    // คุมค่าใช้จ่าย: จำนวนข้อความที่ส่งได้ต่อนาทีต่อ user (stream endpoint)
    'rate_limit_per_minute' => env('AI_CHAT_RATE_LIMIT', 6),

    // คุมค่าใช้จ่าย: จำนวนคำตอบ AI สูงสุดต่อวันต่อ user
    'daily_message_limit' => env('AI_CHAT_DAILY_LIMIT', 200),

    // ── ระบบเครดิต (หน่วยบาท) ──

    // อัตราแลกเปลี่ยน USD → THB สำหรับคิดต้นทุน
    'usd_to_thb' => (float) env('AI_CHAT_USD_TO_THB', 37),

    // ค่าคอม (%) บวกบนต้นทุนจริง — รายได้ dev เช่น 30 = บวก 30%
    'commission_percent' => (float) env('AI_CHAT_COMMISSION_PERCENT', 30),

    // model ที่ไม่มีราคาใน catalog ใช้ราคานี้ — กันใช้ฟรี (USD ต่อ 1M tokens)
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
            'pricing' => ['inputPerMTok' => 0.3, 'cachedInputPerMTok' => 0.06, 'outputPerMTok' => 1.2],
        ],
        [
            'id' => 'minimax/minimax-m3',
            'provider' => 'wavespeed',
            'label' => 'MiniMax M3',
            'description' => 'รุ่นใหม่กว่า M2.7 — งาน agent/วิเคราะห์เอกสาร context 1M ราคาประหยัด',
            'vendor' => 'MiniMax',
            'pricing' => ['inputPerMTok' => 0.6, 'cachedInputPerMTok' => 0.12, 'outputPerMTok' => 2.4],
        ],
        // ── Anthropic ──
        [
            'id' => 'anthropic/claude-fable-5',
            'provider' => 'wavespeed',
            'label' => 'Claude Fable 5',
            'description' => 'เรือธงตระกูล Claude 5 — reasoning ขั้นสูง งาน agent/โค้ดระยะยาว context 1M',
            'vendor' => 'Anthropic',
            'pricing' => ['inputPerMTok' => 10, 'outputPerMTok' => 50],
        ],
        [
            'id' => 'anthropic/claude-opus-4.8',
            'provider' => 'wavespeed',
            'label' => 'Claude Opus 4.8',
            'description' => 'โค้ดและงาน agent ซับซ้อน context 1M — ถูกกว่า Opus รุ่นก่อน',
            'vendor' => 'Anthropic',
            'pricing' => ['inputPerMTok' => 4.75, 'outputPerMTok' => 23.75],
        ],
        [
            'id' => 'anthropic/claude-sonnet-4.6',
            'provider' => 'wavespeed',
            'label' => 'Claude Sonnet 4.6',
            'description' => 'สมดุลคุณภาพ/ราคา — context 1M รองรับงานทั่วไปถึงซับซ้อน',
            'vendor' => 'Anthropic',
            'pricing' => ['inputPerMTok' => 2.85, 'outputPerMTok' => 14.25],
        ],
        // ── OpenAI ──
        [
            'id' => 'openai/gpt-5.5',
            'provider' => 'wavespeed',
            'label' => 'GPT-5.5',
            'description' => 'agentic coding/computer use/deep research — context 1M',
            'vendor' => 'OpenAI',
            'pricing' => ['inputPerMTok' => 5, 'outputPerMTok' => 30],
        ],
        [
            'id' => 'openai/gpt-5.2-pro',
            'provider' => 'wavespeed',
            'label' => 'GPT-5.2 Pro',
            'description' => 'โมเดลเรือธงจาก OpenAI',
            'vendor' => 'OpenAI',
            'pricing' => ['inputPerMTok' => 15, 'outputPerMTok' => 120],
        ],
        // ── Google ──
        [
            'id' => 'google/gemini-3.5-flash',
            'provider' => 'wavespeed',
            'label' => 'Gemini 3.5 Flash',
            'description' => 'โค้ดขั้นสูง/งาน agent ขนาน context 1M รองรับ multimodal',
            'vendor' => 'Google',
            'pricing' => ['inputPerMTok' => 1.5, 'outputPerMTok' => 9],
        ],
        [
            'id' => 'google/gemini-3-flash-preview',
            'provider' => 'wavespeed',
            'label' => 'Gemini 3 Flash',
            'description' => 'ตอบเร็ว เหมาะกับบทสนทนาทั่วไป',
            'vendor' => 'Google',
            'pricing' => ['inputPerMTok' => 0.3, 'outputPerMTok' => 2.5],
        ],
        // ── DeepSeek ──
        [
            'id' => 'deepseek/deepseek-v4',
            'provider' => 'wavespeed',
            'label' => 'DeepSeek V4',
            'description' => 'ราคาประหยัด ความสามารถสูง',
            'vendor' => 'DeepSeek',
            'pricing' => ['inputPerMTok' => 0.28, 'outputPerMTok' => 1.1],
        ],
        [
            'id' => 'deepseek/deepseek-v4-pro',
            'provider' => 'wavespeed',
            'label' => 'DeepSeek V4 Pro',
            'description' => 'เก่งโค้ด/คณิต/งาน agent ระดับท็อป context 1M ราคาคุ้ม',
            'vendor' => 'DeepSeek',
            'pricing' => ['inputPerMTok' => 1.84, 'outputPerMTok' => 3.66],
        ],
        // ── Local (dev) ──
        // ยิง endpoint ในเครื่อง ไม่มีค่าใช้จ่าย — ยังผ่านระบบเครดิตตามปกติ
        // แต่ pricing 0 ทำให้ยอดหักเป็น 0 บาท
        [
            'id' => 'oc/deepseek-v4-flash-free',
            'provider' => 'local',
            'label' => 'DeepSeek V4 Flash (local · free)',
            'description' => 'endpoint ในเครื่องสำหรับทดสอบ — ไม่มีค่าใช้จ่าย',
            'vendor' => 'DeepSeek',
            'pricing' => ['inputPerMTok' => 0, 'cachedInputPerMTok' => 0, 'outputPerMTok' => 0],
        ],
    ],

];
