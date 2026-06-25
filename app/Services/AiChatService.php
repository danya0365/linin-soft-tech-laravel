<?php

namespace App\Services;

use App\Exceptions\WaveSpeedApiException;
use App\Models\AiChatSession;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EnergyResource;
use App\Models\Inventory;
use App\Models\InventoryGroup;
use App\Models\LinenProduct;
use App\Models\LinenType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * AI Chat Service
 *
 * Orchestrator สำหรับตอบคำถาม natural language ผ่าน WaveSpeed LLM
 * ใช้ tool calling ดึงข้อมูลจริงจาก ChatService / Eloquent (read-only)
 * เป็น fallback path ของ ChatService::processCommand() เมื่อไม่ตรง rule ใดๆ
 */
class AiChatService
{
    /** จำนวนรอบ tool loop สูงสุด */
    protected int $maxIterations;

    /** เวลารวมสูงสุดของ loop (วินาที) */
    protected const OVERALL_DEADLINE_SECONDS = 50;

    /** ตัดข้อความ tool result กัน token บาน */
    protected const TOOL_RESULT_MAX_CHARS = 1500;

    /** LINE text message limit 5000 — เผื่อ margin */
    protected const ANSWER_MAX_CHARS = 4500;

    /** ผู้ใช้ที่กำลังสั่งงาน — จำเป็นต่อ write tool (เช็คสิทธิ์ + audit) */
    protected ?User $actingUser = null;

    /** เซสชันแชทปัจจุบัน — ใช้ผูก draft + กลไกกัน auto-confirm */
    protected ?AiChatSession $actingSession = null;

    public function __construct(
        protected WaveSpeedLlmService $llm,
        protected ChatService $chatService,
        protected EntityWriteService $entityWriter,
        protected OperationActionService $operationActions,
    ) {
        $this->maxIterations = (int) config('services.wavespeed.max_iterations', 5);
    }

    /**
     * ตั้ง context การเขียนข้อมูล — ต้องเรียกก่อน streamAnswer ถึงจะเปิด write tool
     * (LINE path ไม่เรียก จึงไม่มี write tool — web-only ในเฟส 1)
     */
    public function setWriteContext(User $actor, AiChatSession $session): void
    {
        $this->actingUser = $actor;
        $this->actingSession = $session;
    }

    public function isAvailable(): bool
    {
        return $this->llm->isEnabled();
    }

    /**
     * ตอบคำถาม user — ไม่ throw เด็ดขาด
     * สำเร็จ: {type: text, title, text} / ล้มเหลว: เมนูแจ้งขัดข้อง
     */
    public function answer(string $question): array
    {
        try {
            $text = $this->runToolLoop($question);

            if ($text === null || trim($text) === '') {
                throw new WaveSpeedApiException('LLM returned empty answer');
            }

            return [
                'type' => 'text',
                'title' => '🤖 AI ผู้ช่วย',
                'text' => mb_substr(trim($text), 0, self::ANSWER_MAX_CHARS),
            ];
        } catch (\Throwable $e) {
            Log::error('AiChatService failed', [
                'question' => mb_substr($question, 0, 200),
                'error' => $e->getMessage(),
            ]);

            return $this->chatService->getMainMenu(
                "⚠️ ขออภัยครับ ระบบ AI ขัดข้องชั่วคราว\nกรุณาลองใหม่ หรือเลือกเมนูด้านล่าง:"
            );
        }
    }

    /**
     * ย่อบทสนทนาเป็น rolling summary — ใช้ model ถูกสุดตาม config เสมอ (chatCompletion ไม่รับ model override)
     * คืน null เมื่อล้มเหลว เพื่อให้ caller คงสรุปเดิมไว้ (ไม่ทำลายความจำเดิม)
     *
     * @param string|null $previousSummary สรุปเดิม (ถ้ามี)
     * @param array $messages [{role, content}] ข้อความที่จะรวมเข้าสรุป
     */
    public function summarize(?string $previousSummary, array $messages): ?string
    {
        if (empty($messages)) {
            return $previousSummary;
        }

        $transcript = collect($messages)
            ->map(function ($m) {
                $who = ($m['role'] ?? '') === 'user' ? 'ผู้ใช้' : 'ผู้ช่วย';
                $text = mb_substr((string) ($m['content'] ?? ''), 0, 1000);

                return "{$who}: {$text}";
            })
            ->implode("\n");

        $instruction = 'ย่อบทสนทนาด้านล่างเป็นบันทึกความจำภาษาไทยแบบกระชับ '
            . 'เก็บเฉพาะข้อเท็จจริงและบริบทที่จำเป็นต่อการสนทนาต่อ '
            . '(สิ่งที่ผู้ใช้ต้องการ ชื่อ/ตัวเลข/เงื่อนไข/ข้อสรุปสำคัญ) '
            . 'ไม่ต้องมีคำนำหรือคำลงท้าย ความยาวไม่เกิน 8-12 บรรทัด';

        if (!empty($previousSummary)) {
            $instruction .= "\n\nสรุปเดิม (รวมเข้ากับของใหม่):\n" . $previousSummary;
        }

        $instruction .= "\n\nบทสนทนาที่ต้องย่อ:\n" . $transcript;

        try {
            $response = $this->llm->chatCompletion([
                ['role' => 'system', 'content' => 'คุณคือตัวช่วยย่อบทสนทนาให้สั้น กระชับ และคงสาระสำคัญ'],
                ['role' => 'user', 'content' => $instruction],
            ]);

            $summary = $response['choices'][0]['message']['content'] ?? null;

            return is_string($summary) && trim($summary) !== '' ? trim($summary) : null;
        } catch (\Throwable $e) {
            Log::warning('AiChatService summarize failed', ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Agentic loop: เรียก LLM พร้อม tools → execute tool_calls → วนจนได้คำตอบ
     */
    protected function runToolLoop(string $question): ?string
    {
        $deadline = microtime(true) + self::OVERALL_DEADLINE_SECONDS;

        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt()],
            ['role' => 'user', 'content' => $question],
        ];

        $tools = $this->toolDefinitions();

        for ($i = 0; $i < $this->maxIterations; $i++) {
            if (microtime(true) > $deadline) {
                break;
            }

            try {
                $response = $this->llm->chatCompletion($messages, $tools);
            } catch (WaveSpeedApiException $e) {
                // model บางตัวไม่รองรับ tools param → ลอง JSON-intent mode
                if ($i === 0 && $e->getHttpStatus() === 400) {
                    return $this->runJsonIntentFallback($question);
                }
                throw $e;
            }

            $message = $response['choices'][0]['message'] ?? [];
            $toolCalls = $message['tool_calls'] ?? [];

            if (empty($toolCalls)) {
                return $message['content'] ?? null;
            }

            $messages[] = [
                'role' => 'assistant',
                'content' => $message['content'] ?? null,
                'tool_calls' => $toolCalls,
            ];

            foreach ($toolCalls as $toolCall) {
                $name = $toolCall['function']['name'] ?? '';
                $args = json_decode($toolCall['function']['arguments'] ?? '{}', true) ?: [];

                Log::info('AiChatService tool call', ['tool' => $name, 'args' => $args]);

                $messages[] = [
                    'role' => 'tool',
                    'tool_call_id' => $toolCall['id'] ?? $name,
                    'content' => $this->executeTool($name, $args),
                ];
            }
        }

        // หมดรอบ/หมดเวลา — เรียกครั้งสุดท้ายแบบไม่มี tools ให้สรุปจากข้อมูลที่มี
        $messages[] = [
            'role' => 'user',
            'content' => 'กรุณาสรุปคำตอบจากข้อมูลที่ได้มาแล้วข้างต้นทันที โดยไม่ต้องเรียกเครื่องมือเพิ่ม',
        ];

        $response = $this->llm->chatCompletion($messages);

        return $response['choices'][0]['message']['content'] ?? null;
    }

    /**
     * Fallback 2-step สำหรับ model ที่ไม่รองรับ tool calling:
     * (1) ให้ model เลือก tool เป็น JSON → (2) execute แล้วส่งผลกลับให้เรียบเรียง
     */
    protected function runJsonIntentFallback(string $question): ?string
    {
        Log::info('AiChatService: falling back to JSON-intent mode');

        $toolList = collect($this->toolDefinitions())->map(function ($tool) {
            $fn = $tool['function'];

            return "- {$fn['name']}: {$fn['description']} | params: " . json_encode($fn['parameters'], JSON_UNESCAPED_UNICODE);
        })->implode("\n");

        $selectPrompt = $this->systemPrompt()
            . "\n\nเครื่องมือที่ใช้ได้:\n{$toolList}\n\n"
            . "ตอบเป็น JSON เท่านั้น ไม่ต้องมีข้อความอื่น รูปแบบ: {\"tool\": \"ชื่อเครื่องมือ\", \"args\": {...}} "
            . "หรือถ้าตอบได้เลยโดยไม่ต้องใช้ข้อมูล: {\"answer\": \"คำตอบ\"}";

        $response = $this->llm->chatCompletion([
            ['role' => 'system', 'content' => $selectPrompt],
            ['role' => 'user', 'content' => $question],
        ]);

        $content = $response['choices'][0]['message']['content'] ?? '';
        $intent = $this->extractJson($content);

        if (isset($intent['answer'])) {
            return (string) $intent['answer'];
        }

        if (empty($intent['tool'])) {
            // model ไม่ยอมตอบ JSON — ใช้ content ตรงๆ
            return $content !== '' ? $content : null;
        }

        $toolResult = $this->executeTool($intent['tool'], $intent['args'] ?? []);

        $response = $this->llm->chatCompletion([
            ['role' => 'system', 'content' => $this->systemPrompt()],
            ['role' => 'user', 'content' => $question],
            ['role' => 'assistant', 'content' => "ข้อมูลจากระบบ ({$intent['tool']}):\n{$toolResult}"],
            ['role' => 'user', 'content' => 'กรุณาตอบคำถามข้างต้นจากข้อมูลนี้'],
        ]);

        return $response['choices'][0]['message']['content'] ?? null;
    }

    protected function extractJson(string $content): array
    {
        $decoded = json_decode(trim($content), true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // เผื่อ model ห่อด้วย ```json ... ``` หรือมีข้อความปน
        if (preg_match('/\{.*\}/s', $content, $matches)) {
            $decoded = json_decode($matches[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    protected function systemPrompt(): string
    {
        $today = Carbon::now('Asia/Bangkok');

        return "คุณคือผู้ช่วย AI ของระบบจัดการโรงซักรีด LinenSoftTech "
            . "วันนี้คือวันที่ {$today->format('Y-m-d')} ({$today->locale('th')->isoFormat('dddd D MMMM')}) เวลาประเทศไทย\n\n"
            . "กฎสำคัญ:\n"
            . "1. ต้องใช้เครื่องมือ (tools) ดึงข้อมูลจริงจากระบบเสมอ ห้ามแต่งตัวเลขหรือเดาข้อมูลเอง\n"
            . "2. ถ้าต้องใช้ id (เช่น customer_id, group_id) ให้เรียก list_* หรือ search_* หาก่อน\n"
            . "3. ตอบเป็นภาษาไทย กระชับ อ่านง่าย เป็น plain text เท่านั้น ห้ามใช้ markdown (เช่น **, #, ตาราง) เพราะแสดงผลใน LINE ไม่ได้\n"
            . "4. รูปแบบวันที่ใน args ใช้ Y-m-d เช่น {$today->format('Y-m-d')}\n"
            . "5. ถ้าหาข้อมูลไม่พบ ให้บอกตรงๆ ว่าไม่พบ และแนะนำคำสั่งที่ใกล้เคียง";
    }

    /**
     * OpenAI-style tool definitions (read-only ทั้งหมด)
     */
    protected function toolDefinitions(): array
    {
        $tools = [
            ['get_today_summary', 'สรุปการดำเนินงานวันนี้ (น้ำหนักผ้า รายรับ-จ่าย ลูกค้า)', []],
            ['get_business_report', 'รายงานธุรกิจตามวันที่/ช่วงเวลา (รายรับ รายจ่าย น้ำหนักผ้า)', [
                'type' => ['string', 'summary หรือ detailed', ['summary', 'detailed']],
                'date' => ['string', 'วันที่ Y-m-d (ไม่ระบุ=วันนี้)'],
                'range' => ['string', 'day, week หรือ month', ['day', 'week', 'month']],
            ]],
            ['list_entities', 'รายชื่อ+id ของกลุ่ม/หมวดในระบบ ตามชนิดที่เลือก', [
                'kind' => ['string', 'customer_groups=กลุ่มลูกค้า, inventory_groups=กลุ่มสต๊อก/ผ้า, energy_resources=ทรัพยากรพลังงาน, departments=แผนก, linen_types=ประเภทผ้า', ['customer_groups', 'inventory_groups', 'energy_resources', 'departments', 'linen_types']],
            ]],
            ['search_customers', 'ค้นหาลูกค้าจากชื่อและ/หรือกลุ่ม (คืน id, ชื่อ, กลุ่ม)', [
                'name' => ['string', 'ชื่อลูกค้า (บางส่วนได้)'],
                'group_id' => ['integer', 'id กลุ่มลูกค้า'],
            ]],
            ['get_customer_detail', 'ข้อมูลลูกค้ารายตัว: น้ำหนักผ้า ยอดบิล ตามช่วงวันที่', [
                'customer_id' => ['integer', 'id ลูกค้า (required)'],
                'date_from' => ['string', 'เริ่ม Y-m-d'],
                'date_to' => ['string', 'สิ้นสุด Y-m-d'],
            ]],
            ['get_inventories_by_group', 'รายการสต๊อก/ผ้าในกลุ่ม พร้อมจำนวนคงเหลือ', [
                'group_id' => ['integer', 'id กลุ่มสต๊อก (required)'],
            ]],
            ['get_energy_logs', 'ประวัติการใช้พลังงานของทรัพยากร', [
                'resource_id' => ['integer', 'id ทรัพยากรพลังงาน (required)'],
            ]],
            ['search_employees', 'ค้นหาพนักงานจากชื่อและ/หรือแผนก (คืน id, ชื่อ, แผนก)', [
                'name' => ['string', 'ชื่อพนักงาน (บางส่วนได้)'],
                'department_id' => ['integer', 'id แผนก'],
            ]],
            ['get_employee_detail', 'ข้อมูลพนักงานรายตัว: ผลงาน เวลาทำงาน', [
                'employee_id' => ['integer', 'id พนักงาน (required)'],
            ]],
            ['get_machine_list', 'รายการเครื่องจักร/รถ พร้อมสถานะ', [
                'type' => ['string', 'washing (เครื่องซัก), dryer (เครื่องอบ) หรือ truck (รถ)', ['washing', 'dryer', 'truck']],
            ]],
            ['get_machine_notes', 'บันทึก/ประวัติซ่อมบำรุงเครื่องจักร ตามวันที่หรือล่าสุด 7 วัน', [
                'type' => ['string', 'washing, dryer หรือ truck', ['washing', 'dryer', 'truck']],
                'date' => ['string', 'วันที่ Y-m-d (ไม่ระบุ=ล่าสุด 7 วัน)'],
            ]],
            ['search_inventories', 'ค้นหาสต๊อก/วัสดุจากชื่อ (คืน id, ชื่อ, หน่วย, คงเหลือ)', [
                'name' => ['string', 'ชื่อสต๊อก (บางส่วนได้)'],
            ]],
            ['list_linen_products', 'รายชื่อ+id ผลิตภัณฑ์ผ้า (กรองตามประเภทผ้าได้)', [
                'linen_type_id' => ['integer', 'id ประเภทผ้า (ไม่ระบุ=ทั้งหมด)'],
            ]],
        ];

        // write tool + operational action — เปิดเฉพาะเมื่อมี context ผู้ใช้+เซสชัน (web เท่านั้น)
        if ($this->actingUser && $this->actingSession) {
            $tools = array_merge($tools, $this->writeToolDefinitions(), $this->operationActionToolDefinitions());
        }

        return array_map(function ($tool) {
            [$name, $description, $params] = $tool;

            $properties = [];
            $required = [];
            foreach ($params as $paramName => $def) {
                // array param: $def[4] = schema ของ object ในแต่ละ item (รองรับ 1 ระดับ)
                if ($def[0] === 'array' && isset($def[4]) && is_array($def[4])) {
                    $itemProps = [];
                    $itemRequired = [];
                    foreach ($def[4] as $itemName => $itemDef) {
                        $ip = ['type' => $itemDef[0], 'description' => $itemDef[1]];
                        if (isset($itemDef[2]) && is_array($itemDef[2])) {
                            $ip['enum'] = $itemDef[2];
                        }
                        if (!empty($itemDef[3])) {
                            $itemRequired[] = $itemName;
                        }
                        $itemProps[$itemName] = $ip;
                    }
                    $properties[$paramName] = [
                        'type' => 'array',
                        'description' => $def[1],
                        'items' => [
                            'type' => 'object',
                            'properties' => (object) $itemProps,
                            'required' => $itemRequired,
                        ],
                    ];
                    if (!empty($def[3])) {
                        $required[] = $paramName;
                    }
                    continue;
                }

                $property = ['type' => $def[0], 'description' => $def[1]];
                if (isset($def[2]) && is_array($def[2])) {
                    $property['enum'] = $def[2];
                }
                if (!empty($def[3])) { // element ที่ 4 = required flag
                    $required[] = $paramName;
                }
                $properties[$paramName] = $property;
            }

            return [
                'type' => 'function',
                'function' => [
                    'name' => $name,
                    'description' => $description,
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object) $properties,
                        'required' => $required,
                    ],
                ],
            ];
        }, $tools);
    }

    /**
     * Tool สำหรับจัดการข้อมูล (CRUD) — สร้างจาก EntityWriteRegistry
     * ต่อเอนทิตี: prepare_create_* + prepare_update_*  / generic: prepare_delete_entity + confirm_write
     */
    protected function writeToolDefinitions(): array
    {
        $tools = [];

        foreach (EntityWriteRegistry::all() as $key => $cfg) {
            // create — ฟิลด์ตาม required ที่กำหนด
            $tools[] = [
                "prepare_create_{$key}",
                "เตรียมสร้าง{$cfg['label']} (ขั้นที่ 1/2 — แสดง preview ให้ผู้ใช้ยืนยันก่อน ห้ามบันทึกเองทันที)",
                $cfg['fields'],
            ];

            // update — id required + ฟิลด์อื่น optional (ส่งเฉพาะฟิลด์ที่จะแก้)
            $updateFields = ['id' => ['integer', "id ของ{$cfg['label']}ที่จะแก้ (หาด้วย search_*/list_entities ก่อน)", null, true]];
            foreach ($cfg['fields'] as $fieldName => $def) {
                $updateFields[$fieldName] = [$def[0], $def[1] . ' (ส่งเฉพาะถ้าต้องการเปลี่ยน)', $def[2] ?? null]; // ตัด required flag
            }
            $tools[] = [
                "prepare_update_{$key}",
                "เตรียมแก้ไข{$cfg['label']} เฉพาะฟิลด์ที่ระบุ (ขั้นที่ 1/2 — แสดง preview ให้ผู้ใช้ยืนยันก่อน)",
                $updateFields,
            ];
        }

        $tools[] = ['prepare_delete_entity', 'เตรียมลบข้อมูล master data (ขั้นที่ 1/2 — แสดง preview ให้ผู้ใช้ยืนยันก่อน) จะถูกบล็อกถ้ามีข้อมูลอื่นผูกอยู่', [
            'entity_key' => ['string', 'ชนิดข้อมูลที่จะลบ', EntityWriteRegistry::keys(), true],
            'id' => ['integer', 'id ของรายการที่จะลบ (หาด้วย search_*/list_entities ก่อน)', null, true],
        ]];

        $tools[] = ['confirm_write', 'ยืนยันทำรายการที่เตรียมไว้ (สร้าง/แก้ไข/ลบ/งานประจำวัน) ขั้นที่ 2/2 — เรียกเฉพาะเมื่อผู้ใช้ตอบยืนยันในข้อความถัดไปแล้วเท่านั้น', [
            'draft_id' => ['integer', 'id ของรายการที่ได้จาก prepare_*', null, true],
        ]];

        return $tools;
    }

    /**
     * Tool สำหรับงานเดินเอกสาร/ธุรกรรม (operational) — สร้างจาก OperationActionRegistry
     * 1 prepare ต่อ 1 action (prepare_log_energy ฯลฯ) ใช้ confirm_write ร่วมกัน
     */
    protected function operationActionToolDefinitions(): array
    {
        $tools = [];

        foreach (OperationActionRegistry::all() as $key => $cfg) {
            $tools[] = [
                "prepare_{$key}",
                "เตรียม{$cfg['label']} (ขั้นที่ 1/2 — แสดง preview ให้ผู้ใช้ยืนยันก่อน ห้ามทำเองทันที)",
                $cfg['params'],
            ];
        }

        return $tools;
    }

    /**
     * Execute tool — คืน string เสมอ (error ก็คืนเป็นข้อความให้ LLM อ่าน ไม่ throw)
     */
    protected function executeTool(string $name, array $args): string
    {
        try {
            // write tool (prepare_create_*/prepare_update_*/prepare_delete_entity/confirm_write) — มี context เท่านั้น
            $writeResult = $this->executeWriteTool($name, $args);
            if ($writeResult !== null) {
                return $writeResult;
            }

            $result = match ($name) {
                'get_today_summary' => $this->chatService->getTodaySummary(),

                'get_business_report' => $this->chatService->getReportByDate(
                    in_array($args['type'] ?? '', ['summary', 'detailed']) ? $args['type'] : 'summary',
                    $args['date'] ?? null,
                    in_array($args['range'] ?? '', ['day', 'week', 'month']) ? $args['range'] : 'day',
                ),

                'list_entities' => $this->listEntities($args),

                'search_customers' => $this->searchCustomers($args),

                'get_customer_detail' => $this->chatService->getCustomerDetail(
                    (int) ($args['customer_id'] ?? 0),
                    $args['date_from'] ?? null,
                    $args['date_to'] ?? null,
                ),

                'get_inventories_by_group' => $this->chatService->getInventoriesByGroup(
                    (int) ($args['group_id'] ?? 0)
                ),

                'search_inventories' => $this->searchInventories($args),

                'list_linen_products' => $this->listLinenProducts($args),

                'get_energy_logs' => $this->chatService->getEnergyLogs(
                    (int) ($args['resource_id'] ?? 0)
                ),

                'search_employees' => $this->searchEmployees($args),

                'get_employee_detail' => $this->chatService->getEmployeeDetail(
                    (int) ($args['employee_id'] ?? 0)
                ),

                'get_machine_list' => $this->chatService->getMachineListByType(
                    in_array($args['type'] ?? '', ['washing', 'dryer', 'truck']) ? $args['type'] : 'washing'
                ),

                'get_machine_notes' => $this->getMachineNotes($args),

                default => "ไม่รู้จักเครื่องมือ \"{$name}\" — เครื่องมือที่ใช้ได้: get_today_summary, get_business_report, list_entities, search_customers, get_customer_detail, get_inventories_by_group, get_energy_logs, search_employees, get_employee_detail, get_machine_list, get_machine_notes",
            };
        } catch (\Throwable $e) {
            Log::warning('AiChatService tool execution failed', [
                'tool' => $name,
                'args' => $args,
                'error' => $e->getMessage(),
            ]);

            return "เกิดข้อผิดพลาดขณะดึงข้อมูล ({$name}): {$e->getMessage()}";
        }

        $text = is_array($result) ? $this->serializeResponse($result) : (string) $result;

        if (mb_strlen($text) > self::TOOL_RESULT_MAX_CHARS) {
            $text = mb_substr($text, 0, self::TOOL_RESULT_MAX_CHARS) . "\n... (ข้อมูลถูกตัดทอน)";
        }

        return $text;
    }

    /**
     * รายชื่อกลุ่ม/หมวดตามชนิด (ยุบจาก list_customer_groups/inventory/energy/departments เดิม)
     */
    protected function listEntities(array $args): string
    {
        return match ($args['kind'] ?? '') {
            'customer_groups' => $this->listAsText(CustomerGroup::get(['id', 'name']), 'กลุ่มลูกค้า'),
            'inventory_groups' => $this->listAsText(InventoryGroup::get(['id', 'name']), 'กลุ่มสต๊อก'),
            'energy_resources' => $this->listAsText(EnergyResource::get(['id', 'name']), 'ทรัพยากรพลังงาน'),
            'departments' => $this->listAsText(Department::get(['id', 'name']), 'แผนก'),
            'linen_types' => $this->listAsText(LinenType::get(['id', 'name']), 'ประเภทผ้า'),
            default => 'ระบุ kind ไม่ถูกต้อง — ค่าที่ใช้ได้: customer_groups, inventory_groups, energy_resources, departments, linen_types',
        };
    }

    /**
     * จัดการ write tool — คืน null ถ้าไม่ใช่ write tool หรือไม่มี context (ให้ตกไป read match)
     */
    protected function executeWriteTool(string $name, array $args): ?string
    {
        if (!$this->actingUser || !$this->actingSession) {
            return null;
        }

        if ($name === 'confirm_write') {
            return $this->entityWriter->confirm(
                $this->actingUser,
                $this->actingSession,
                (int) ($args['draft_id'] ?? 0)
            );
        }

        if ($name === 'prepare_delete_entity') {
            return $this->entityWriter->prepareDelete(
                $this->actingUser,
                $this->actingSession,
                (string) ($args['entity_key'] ?? ''),
                (int) ($args['id'] ?? 0)
            );
        }

        // operational action (prepare_log_energy/stock_in/.../operation) — guard ด้วย registry ไม่ให้ชน entity tool
        if (str_starts_with($name, 'prepare_') && OperationActionRegistry::get(substr($name, strlen('prepare_')))) {
            return $this->operationActions->prepare(
                $this->actingUser,
                $this->actingSession,
                substr($name, strlen('prepare_')),
                $args
            );
        }

        if (str_starts_with($name, 'prepare_create_')) {
            $entityKey = substr($name, strlen('prepare_create_'));

            return $this->entityWriter->prepare($this->actingUser, $this->actingSession, $entityKey, $args);
        }

        if (str_starts_with($name, 'prepare_update_')) {
            $entityKey = substr($name, strlen('prepare_update_'));

            return $this->entityWriter->prepareUpdate(
                $this->actingUser,
                $this->actingSession,
                $entityKey,
                (int) ($args['id'] ?? 0),
                $args
            );
        }

        return null;
    }

    protected function searchCustomers(array $args): string
    {
        $query = Customer::query()->with('group:id,name');

        if (!empty($args['name'])) {
            $query->where('name', 'like', '%' . $args['name'] . '%');
        }
        if (!empty($args['group_id'])) {
            $query->where('customer_group_id', (int) $args['group_id']);
        }

        $customers = $query->limit(15)->get(['id', 'name', 'customer_group_id']);

        if ($customers->isEmpty()) {
            return 'ไม่พบลูกค้าตามเงื่อนไขที่ค้นหา';
        }

        return "ลูกค้าที่พบ (สูงสุด 15 รายการ):\n" . $customers->map(function ($c) {
            $group = $c->group->name ?? '-';

            return "id: {$c->id} | {$c->name} | กลุ่ม: {$group}";
        })->implode("\n");
    }

    protected function searchEmployees(array $args): string
    {
        $query = Employee::query()->with('department:id,name');

        if (!empty($args['name'])) {
            $query->where('name', 'like', '%' . $args['name'] . '%');
        }
        if (!empty($args['department_id'])) {
            $query->where('department_id', (int) $args['department_id']);
        }

        $employees = $query->limit(15)->get(['id', 'name', 'code', 'department_id']);

        if ($employees->isEmpty()) {
            return 'ไม่พบพนักงานตามเงื่อนไขที่ค้นหา';
        }

        return "พนักงานที่พบ (สูงสุด 15 รายการ):\n" . $employees->map(function ($e) {
            $dept = $e->department->name ?? '-';

            return "id: {$e->id} | {$e->name} (รหัส {$e->code}) | แผนก: {$dept}";
        })->implode("\n");
    }

    protected function searchInventories(array $args): string
    {
        $query = Inventory::query()->with('inventoryGroup:id,name');

        if (!empty($args['name'])) {
            $query->where('name', 'like', '%' . $args['name'] . '%');
        }

        $inventories = $query->limit(15)->get(['id', 'name', 'unit', 'remain_quantity', 'inventory_group_id']);

        if ($inventories->isEmpty()) {
            return 'ไม่พบสต๊อกตามเงื่อนไขที่ค้นหา';
        }

        return "สต๊อกที่พบ (สูงสุด 15 รายการ):\n" . $inventories->map(function ($i) {
            $group = $i->inventoryGroup->name ?? '-';

            return "id: {$i->id} | {$i->name} | คงเหลือ {$i->remain_quantity} {$i->unit} | กลุ่ม: {$group}";
        })->implode("\n");
    }

    protected function listLinenProducts(array $args): string
    {
        $query = LinenProduct::query()->with('linenType:id,name');

        if (!empty($args['linen_type_id'])) {
            $query->where('linen_type_id', (int) $args['linen_type_id']);
        }

        $products = $query->limit(50)->get(['id', 'name', 'linen_type_id']);

        if ($products->isEmpty()) {
            return 'ไม่พบผลิตภัณฑ์ผ้าตามเงื่อนไขที่ค้นหา';
        }

        return "ผลิตภัณฑ์ผ้า (สูงสุด 50 รายการ):\n" . $products->map(function ($p) {
            $type = $p->linenType->name ?? '-';

            return "id: {$p->id} | {$p->name} | ประเภท: {$type}";
        })->implode("\n");
    }

    protected function getMachineNotes(array $args): array
    {
        $type = in_array($args['type'] ?? '', ['washing', 'dryer', 'truck']) ? $args['type'] : 'washing';

        if (!empty($args['date'])) {
            return $this->chatService->getMachineNotesByDateAndType($args['date'], $type);
        }

        return $this->chatService->getMachineRecentNotesByType($type);
    }

    /**
     * แปลง response array ของ ChatService (card/menu/text) เป็น plain text กระชับ
     */
    protected function serializeResponse(array $response): string
    {
        $lines = [];

        if (!empty($response['title'])) {
            $lines[] = $response['title'];
        }
        if (!empty($response['subtitle'])) {
            $lines[] = $response['subtitle'];
        }
        if (!empty($response['text'])) {
            $lines[] = $response['text'];
        }

        foreach ($response['rows'] ?? [] as $row) {
            $label = $row['label'] ?? '';
            $value = $row['value'] ?? '';
            $lines[] = trim("{$label}: {$value}", ': ');
        }

        if (!empty($response['quickReplies'])) {
            $labels = array_filter(array_column($response['quickReplies'], 'label'));
            if ($labels) {
                $lines[] = 'ตัวเลือกที่เกี่ยวข้อง: ' . implode(', ', $labels);
            }
        }

        return implode("\n", array_filter($lines, fn ($l) => trim($l) !== ''));
    }

    protected function listAsText($items, string $label): string
    {
        if ($items->isEmpty()) {
            return "ไม่พบข้อมูล{$label}";
        }

        return "{$label}ทั้งหมด:\n" . $items->map(
            fn ($item) => "id: {$item->id} | {$item->name}"
        )->implode("\n");
    }
}
