<?php

namespace App\Services;

use App\Exceptions\ClientDisconnectedException;
use App\Exceptions\LlmApiException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\StreamInterface;

/**
 * AI Chat Stream Service (หน้าเว็บ /ai-chat)
 *
 * Streaming tool loop: เรียก provider ของ model ที่เลือกแบบ stream + tools,
 * parse SSE ฝั่ง server, execute tool แล้ววนต่อ, ส่ง content delta
 * ให้ browser แบบ realtime ผ่าน emit callback
 *
 * สืบทอด toolDefinitions / executeTool / runJsonIntentFallback
 * จาก AiChatService (path ของ LINE bot ไม่ถูกแตะ)
 */
class AiChatStreamService extends AiChatService
{
    /** เวลารวมสูงสุดของ streaming loop (วินาที) — ยาวกว่าฝั่ง LINE */
    protected const STREAM_DEADLINE_SECONDS = 110;

    /**
     * ตอบคำถามแบบ streaming พร้อม tool loop
     *
     * @param array $messages ประวัติที่มองเห็น [{role: user|assistant, content}] ข้อความสุดท้ายคือคำถามใหม่
     * @param string $model model id (ผ่าน allowlist มาแล้ว)
     * @param int|null $maxTokens จำกัดความยาวคำตอบ (null = ไม่จำกัด)
     * @param callable $emit fn(array $event): void — ส่ง event ให้ client, โยน ClientDisconnectedException เมื่อ client หลุด
     * @return array{content: string, usage: ?array, estimated: bool, aborted: bool}
     * @throws LlmApiException เมื่อ upstream ล้มเหลว (ก่อนได้ content ใดๆ)
     */
    public function streamAnswer(array $messages, string $model, ?int $maxTokens, callable $emit): array
    {
        // ล็อก provider ตาม model ที่เลือก ให้ path ที่สืบทอดมา (fallback แบบ non-stream) ใช้เจ้าเดียวกัน
        $this->useModel($model);

        try {
            $result = $this->runStreamingToolLoop($messages, $model, $maxTokens, $emit);

            return $this->explainEmptyAnswer($result, $maxTokens, $emit);
        } catch (ClientDisconnectedException $e) {
            // client กดหยุด — คืน partial content ที่สะสมไว้ (เก็บใน exception ไม่ได้ ใช้ property)
            return [
                'content' => $this->partialContent,
                'usage' => $this->usageTotals(),
                'estimated' => $this->usageTotal === null,
                'aborted' => true,
            ];
        }
    }

    /**
     * model ตระกูล reasoning นับ reasoning token รวมใน max_tokens ด้วย
     * ถ้าเพดานต่ำเกิน โควตาจะหมดไปกับการคิดจนไม่เหลือคำตอบ — ผู้ใช้เห็นข้อความว่างเปล่า
     * แจ้งสาเหตุแทนที่จะปล่อยว่าง
     */
    protected function explainEmptyAnswer(array $result, ?int $maxTokens, callable $emit): array
    {
        if (trim($result['content']) !== '' || $result['aborted']) {
            return $result;
        }

        $notice = $this->reasoningTokens > 0
            ? 'ตอบไม่ทันครับ — โควตาความยาวคำตอบหมดไปกับขั้นตอนคิดวิเคราะห์ '
                . '(reasoning ' . $this->reasoningTokens . ' tokens'
                . ($maxTokens ? ' จากเพดาน ' . $maxTokens : '') . ') '
                . 'กรุณาเพิ่มความยาวคำตอบสูงสุด แล้วลองใหม่อีกครั้ง'
            : 'ไม่ได้รับคำตอบจาก AI กรุณาลองใหม่อีกครั้ง';

        try {
            $emit(['choices' => [['delta' => ['content' => $notice]]]]);
        } catch (ClientDisconnectedException $e) {
            // client หลุดไปแล้ว — ยังคืน content ไว้บันทึกลง DB
        }

        $result['content'] = $notice;

        return $result;
    }

    /** content ที่ emit ให้ client ไปแล้ว (สำหรับ persist partial เมื่อ abort) */
    protected string $partialContent = '';

    /** usage รวมข้ามทุก iteration — null = upstream ไม่ส่ง usage เลย */
    protected ?array $usageTotal = null;

    protected function usageTotals(): ?array
    {
        return $this->usageTotal;
    }

    protected function addUsage(array $usage): void
    {
        $this->usageTotal = [
            'prompt_tokens' => ($this->usageTotal['prompt_tokens'] ?? 0) + ((int) ($usage['prompt_tokens'] ?? 0)),
            'cached_tokens' => ($this->usageTotal['cached_tokens'] ?? 0) + $this->extractCachedTokens($usage),
            'completion_tokens' => ($this->usageTotal['completion_tokens'] ?? 0) + ((int) ($usage['completion_tokens'] ?? 0)),
        ];

        $this->reasoningTokens += $this->extractReasoningTokens($usage);
    }

    /** reasoning token ที่ถูกใช้ไป — นับรวมใน max_tokens ของ model ตระกูล reasoning */
    protected int $reasoningTokens = 0;

    protected function extractReasoningTokens(array $usage): int
    {
        return (int) ($usage['completion_tokens_details']['reasoning_tokens']
            ?? $usage['reasoning_tokens']
            ?? 0);
    }

    /**
     * จำนวน prompt token ที่เป็น cache hit จาก usage ของ upstream
     * รองรับทั้งรูปแบบ OpenAI (prompt_tokens_details.cached_tokens) และ field ตรง (cached_tokens)
     */
    protected function extractCachedTokens(array $usage): int
    {
        return (int) ($usage['prompt_tokens_details']['cached_tokens']
            ?? $usage['cached_tokens']
            ?? 0);
    }

    protected function runStreamingToolLoop(array $messages, string $model, ?int $maxTokens, callable $emit): array
    {
        $deadline = microtime(true) + self::STREAM_DEADLINE_SECONDS;

        $this->partialContent = '';
        $this->usageTotal = null;
        $this->reasoningTokens = 0;

        $workMessages = array_merge(
            [['role' => 'system', 'content' => $this->systemPrompt()]],
            $messages
        );

        $tools = $this->toolDefinitions();

        for ($i = 0; $i < $this->maxIterations; $i++) {
            if (microtime(true) > $deadline) {
                break;
            }

            try {
                $body = $this->provider($model)->streamChatCompletion($workMessages, $model, $maxTokens, $tools);
            } catch (LlmApiException $e) {
                // model บางตัวไม่รองรับ tools param → JSON-intent mode (non-stream) แล้วส่งทั้งก้อน
                if ($i === 0 && $e->getHttpStatus() === 400) {
                    return $this->runNonStreamingFallback($messages, $emit);
                }
                throw $e;
            }

            $result = $this->consumeIteration($body, $emit);

            if (empty($result['toolCalls'])) {
                // ไม่มี tool call — จบ ได้คำตอบสุดท้ายแล้ว
                return [
                    'content' => $this->partialContent,
                    'usage' => $this->usageTotals(),
                    'estimated' => $this->usageTotal === null,
                    'aborted' => false,
                ];
            }

            // มี tool calls → append assistant message + execute แต่ละ tool แล้ววนต่อ
            $workMessages[] = [
                'role' => 'assistant',
                'content' => $result['content'] !== '' ? $result['content'] : null,
                'tool_calls' => array_map(static function ($call) {
                    return [
                        'id' => $call['id'],
                        'type' => 'function',
                        'function' => [
                            'name' => $call['name'],
                            'arguments' => $call['arguments'] !== '' ? $call['arguments'] : '{}',
                        ],
                    ];
                }, $result['toolCalls']),
            ];

            foreach ($result['toolCalls'] as $call) {
                $args = json_decode($call['arguments'], true) ?: [];
                $label = $this->toolStatusLabel($call['name'], $args);

                $emit(['type' => 'tool_status', 'status' => 'running', 'name' => $call['name'], 'label' => $label]);

                Log::info('AiChatStreamService tool call', ['tool' => $call['name'], 'args' => $args]);

                $workMessages[] = [
                    'role' => 'tool',
                    'tool_call_id' => $call['id'],
                    'content' => $this->executeTool($call['name'], $args),
                ];

                $emit(['type' => 'tool_status', 'status' => 'done', 'name' => $call['name'], 'label' => $label]);
            }
        }

        // หมดรอบ/หมดเวลา — บังคับสรุปด้วย call สุดท้ายแบบไม่มี tools (ยัง stream)
        $workMessages[] = [
            'role' => 'user',
            'content' => 'กรุณาสรุปคำตอบจากข้อมูลที่ได้มาแล้วข้างต้นทันที โดยไม่ต้องเรียกเครื่องมือเพิ่ม',
        ];

        $body = $this->provider($model)->streamChatCompletion($workMessages, $model, $maxTokens);
        $this->consumeIteration($body, $emit);

        return [
            'content' => $this->partialContent,
            'usage' => $this->usageTotals(),
            'estimated' => $this->usageTotal === null,
            'aborted' => false,
        ];
    }

    /**
     * label ภาษาไทยสำหรับ tool_status (โชว์ตอนกำลังดึงข้อมูล)
     * list_entities เป็น tool รวม จึงแยก label ตาม kind ให้ผู้ใช้เห็นชัด
     */
    protected function toolStatusLabel(string $name, array $args): string
    {
        if ($name === 'list_entities') {
            return match ($args['kind'] ?? '') {
                'customer_groups' => 'กลุ่มลูกค้า',
                'inventory_groups' => 'กลุ่มสต๊อก',
                'energy_resources' => 'ทรัพยากรพลังงาน',
                'departments' => 'แผนก',
                'linen_types' => 'ประเภทผ้า',
                default => 'รายชื่อกลุ่ม/หมวด',
            };
        }

        if (str_starts_with($name, 'prepare_') && ($actionCfg = \App\Services\OperationActionRegistry::get(substr($name, strlen('prepare_'))))) {
            return "เตรียม{$actionCfg['label']}";
        }

        if (str_starts_with($name, 'prepare_create_')) {
            $key = substr($name, strlen('prepare_create_'));
            $label = \App\Services\EntityWriteRegistry::get($key)['label'] ?? $key;

            return "เตรียมสร้าง{$label}";
        }

        if (str_starts_with($name, 'prepare_update_')) {
            $key = substr($name, strlen('prepare_update_'));
            $label = \App\Services\EntityWriteRegistry::get($key)['label'] ?? $key;

            return "เตรียมแก้ไข{$label}";
        }

        if ($name === 'prepare_delete_entity') {
            return 'เตรียมลบข้อมูล';
        }

        if ($name === 'confirm_write') {
            return 'ยืนยันบันทึกข้อมูล';
        }

        return match ($name) {
            'get_today_summary' => 'สรุปวันนี้',
            'get_business_report' => 'รายงานธุรกิจ',
            'search_customers' => 'ค้นหาลูกค้า',
            'get_customer_detail' => 'ข้อมูลลูกค้า',
            'get_inventories_by_group' => 'รายการสต๊อก',
            'get_energy_logs' => 'ประวัติพลังงาน',
            'search_employees' => 'ค้นหาพนักงาน',
            'get_employee_detail' => 'ข้อมูลพนักงาน',
            'get_machine_list' => 'รายการเครื่องจักร',
            'get_machine_notes' => 'บันทึกซ่อมบำรุง',
            'search_inventories' => 'ค้นหาสต๊อก',
            'list_linen_products' => 'รายการผลิตภัณฑ์ผ้า',
            'list_deliverable_collect_items' => 'รายการผ้ารอส่ง',
            default => $name,
        };
    }

    /**
     * อ่าน SSE หนึ่ง iteration จนจบ stream
     *
     * @return array{content: string, toolCalls: array<int, array{id: string, name: string, arguments: string}>}
     */
    protected function consumeIteration(StreamInterface $body, callable $emit): array
    {
        $iterationContent = '';
        $pendingToolCalls = [];

        try {
            $this->consumeSse($body, function (array $chunk) use (&$iterationContent, &$pendingToolCalls, $emit) {
                $choice = $chunk['choices'][0] ?? [];
                $delta = $choice['delta'] ?? [];

                // content delta → ส่งต่อให้ client (normalize — ตัด field อื่นเช่น reasoning ทิ้ง)
                $content = $delta['content'] ?? '';
                if (is_string($content) && $content !== '') {
                    $iterationContent .= $content;
                    $this->partialContent .= $content;
                    $emit(['choices' => [['delta' => ['content' => $content]]]]);
                }

                if (!empty($delta['tool_calls']) && is_array($delta['tool_calls'])) {
                    $this->accumulateToolCallDelta($pendingToolCalls, $delta['tool_calls']);
                }

                if (!empty($chunk['usage']) && is_array($chunk['usage'])) {
                    $this->addUsage($chunk['usage']);
                }
            });
        } finally {
            $body->close();
        }

        // normalize tool calls — ข้ามตัวที่ไม่มีชื่อ (delta ขยะ)
        $toolCalls = [];
        foreach ($pendingToolCalls as $idx => $call) {
            if (($call['name'] ?? '') === '') {
                continue;
            }
            $toolCalls[] = [
                'id' => $call['id'] ?? ('call_' . $idx),
                'name' => $call['name'],
                'arguments' => $call['arguments'] ?? '',
            ];
        }

        return ['content' => $iterationContent, 'toolCalls' => $toolCalls];
    }

    /**
     * Parse SSE จาก upstream: split event ด้วย \n\n (decode เฉพาะ event สมบูรณ์
     * — ปลอดภัยกับ UTF-8 ไทยที่โดนตัดกลาง chunk) และกลืน [DONE] ของ upstream
     * (controller เป็นคนส่ง [DONE] สุดท้ายให้ client เองครั้งเดียว)
     */
    protected function consumeSse(StreamInterface $body, callable $onChunk): void
    {
        $buffer = '';

        while (!$body->eof()) {
            $raw = $body->read(8192);
            if ($raw === '') {
                continue;
            }

            $buffer .= $raw;

            $events = explode("\n\n", $buffer);
            $buffer = array_pop($events);

            foreach ($events as $event) {
                foreach (explode("\n", $event) as $line) {
                    if (strpos($line, 'data:') !== 0) {
                        continue;
                    }

                    $payload = trim(substr($line, 5));
                    if ($payload === '[DONE]') {
                        return;
                    }

                    $parsed = json_decode($payload, true);
                    if (is_array($parsed)) {
                        $onChunk($parsed);
                    }
                }
            }
        }
    }

    /**
     * สะสม tool_call deltas (OpenAI streaming format):
     * fragment แรกมี index/id/function.name, fragment ถัดไปต่อ function.arguments
     * defensive กับ vendor ที่ไม่ส่ง index หรือส่งทั้ง call ใน chunk เดียว
     */
    protected function accumulateToolCallDelta(array &$pending, array $deltaToolCalls): void
    {
        foreach (array_values($deltaToolCalls) as $pos => $tc) {
            if (!is_array($tc)) {
                continue;
            }

            $idx = $tc['index'] ?? $pos;

            if (!isset($pending[$idx])) {
                $pending[$idx] = ['id' => null, 'name' => null, 'arguments' => ''];
            }

            if (!empty($tc['id']) && $pending[$idx]['id'] === null) {
                $pending[$idx]['id'] = $tc['id'];
            }

            $fn = $tc['function'] ?? [];

            if (!empty($fn['name']) && $pending[$idx]['name'] === null) {
                $pending[$idx]['name'] = $fn['name'];
            }

            if (isset($fn['arguments']) && is_string($fn['arguments'])) {
                $pending[$idx]['arguments'] .= $fn['arguments'];
            }
        }
    }

    /**
     * Fallback เมื่อ model ไม่รองรับ tools (HTTP 400):
     * ใช้ JSON-intent mode (non-stream) ของ parent แล้ว emit คำตอบทั้งก้อนเป็น chunk เดียว
     */
    protected function runNonStreamingFallback(array $messages, callable $emit): array
    {
        $lastUser = '';
        foreach (array_reverse($messages) as $message) {
            if (($message['role'] ?? '') === 'user') {
                $lastUser = (string) ($message['content'] ?? '');
                break;
            }
        }

        $text = (string) ($this->runJsonIntentFallback($lastUser) ?? '');

        if ($text !== '') {
            $this->partialContent = $text;
            $emit(['choices' => [['delta' => ['content' => $text]]]]);
        }

        return [
            'content' => $text,
            'usage' => null,
            'estimated' => true,
            'aborted' => false,
        ];
    }

    /**
     * System prompt เวอร์ชันเว็บ — ไม่มีข้อจำกัด LINE (4500 ตัวอักษร)
     * UI แสดงผลเป็น plain text (textContent) จึงยังห้าม markdown
     */
    protected function systemPrompt(): string
    {
        $today = Carbon::now('Asia/Bangkok');

        return "คุณคือผู้ช่วย AI ของระบบจัดการโรงซักรีด LinenSoftTech "
            . "วันนี้คือวันที่ {$today->format('Y-m-d')} ({$today->locale('th')->isoFormat('dddd D MMMM')}) เวลาประเทศไทย\n\n"
            . "กฎสำคัญ:\n"
            . "1. คำถามเกี่ยวกับข้อมูลธุรกิจ (ยอดขาย ลูกค้า สต๊อก พลังงาน พนักงาน เครื่องจักร) ต้องใช้เครื่องมือ (tools) ดึงข้อมูลจริงจากระบบเสมอ ห้ามแต่งตัวเลขหรือเดาข้อมูลเอง\n"
            . "2. ถ้าต้องใช้ id (เช่น customer_id, group_id) ให้เรียก list_* หรือ search_* หาก่อน\n"
            . "3. ตอบเป็นภาษาไทย อ่านง่าย เป็น plain text เท่านั้น ห้ามใช้ markdown (เช่น **, #, ตาราง) "
            . "จัดโครงสร้างด้วยบรรทัดว่างและลิสต์ขีดหน้า (-) ได้\n"
            . "4. รูปแบบวันที่ใน args ใช้ Y-m-d เช่น {$today->format('Y-m-d')}\n"
            . "5. ถ้าหาข้อมูลไม่พบ ให้บอกตรงๆ ว่าไม่พบ และแนะนำคำถามที่ใกล้เคียง\n"
            . "6. คำถามทั่วไปที่ไม่เกี่ยวกับข้อมูลในระบบ ตอบได้เลยโดยไม่ต้องเรียกเครื่องมือ\n\n"
            . "การจัดการข้อมูล (สร้าง/แก้ไข/ลบ):\n"
            . "7. ทุกการเปลี่ยนแปลงข้อมูลต้องทำ 2 ขั้นเสมอ — (ก) เรียก prepare_create_* / prepare_update_* / prepare_delete_entity "
            . "เพื่อเตรียมรายการ แล้วแสดง preview ที่ได้ให้ผู้ใช้พร้อมถามยืนยัน "
            . "(ข) เมื่อผู้ใช้ตอบยืนยันในข้อความถัดไป จึงเรียก confirm_write ด้วย draft_id เดิม\n"
            . "8. ห้ามเรียก confirm_write ในรอบเดียวกับ prepare_* เด็ดขาด ต้องรอให้ผู้ใช้ยืนยันจริงก่อน\n"
            . "9. ก่อนแก้ไข/ลบ หรือก่ออ้างอิง id (เช่น customer_group_id, department_id, linen_type_id) "
            . "ต้องเรียก list_entities หรือ search_* หา id จริงก่อน ห้ามเดา id เอง\n"
            . "10. การแก้ไข (update) ส่งเฉพาะฟิลด์ที่ต้องการเปลี่ยนเท่านั้น ห้ามแต่งค่าที่ผู้ใช้ไม่ได้ระบุ ถ้าข้อมูลจำเป็นไม่ครบให้ถามผู้ใช้ก่อน\n"
            . "11. การลบจะถูกระบบบล็อกถ้ามีข้อมูลอื่นผูกอยู่ (เช่น กลุ่มที่มีลูกค้า) ให้แจ้งผู้ใช้ตามข้อความที่ระบบคืนมา\n"
            . "12. ระบบไม่รองรับการแนบรูปภาพผ่านแชท ฟิลด์รูป (photo) จะถูกเว้นว่างให้เอง ไม่ต้องถามหา\n"
            . "เอกสารที่อัปโหลด (documents): คำถามเกี่ยวกับเอกสารที่ staff อัปโหลด (สัญญา ใบเสร็จ เอกสารซ่อม รูปถ่าย) — เรียก search_documents หาก่อน แล้วอ่านรายละเอียดด้วย get_document_content; ตอบจากเนื้อหาจริงในเอกสารเท่านั้น ห้ามแต่ง คำถามเกี่ยวกับสิ่งที่เห็นในภาพถ่าย (เช่น รอยชำรุดของเครื่องจักร) — เรียก analyze_document_image; ถ้าใช้โมเดลที่อ่านรูปไม่ได้ (ตอบด้วย OCR text) ให้แจ้งว่าเห็นได้แค่ข้อความที่สกัดจากภาพ\n\n"
            . "งานประจำวัน (operational): บันทึกพลังงาน, รับ/เบิกสต๊อก, ออกบิล, ค่าใช้จ่ายแผนก, สร้างงานผ้า (ซัก/อบ/รีด/แพ็ค/เก็บ)\n"
            . "13. ใช้ขั้นตอน 2 ชั้นเหมือนกัน — prepare_* (log_energy/stock_in/stock_out/billing/department_expense/operation) แสดง preview แล้ว confirm_write เมื่อผู้ใช้ยืนยัน\n"
            . "14. ก่อนทำต้องหา id จริงก่อน: พลังงาน/แผนก→list_entities, สต๊อก→search_inventories, ลูกค้า→search_customers, พนักงาน→search_employees, ผลิตภัณฑ์ผ้า→list_linen_products, เครื่องจักร→get_machine_list\n"
            . "15. สร้างงานผ้า (prepare_operation) ให้รวบรวมรายการผ้าทั้งหมดก่อน แล้วส่ง items เป็น array ครั้งเดียว (แต่ละ item: linen_product_id, linen_case, color, amount; collect ใส่ collect_pack)\n"
            . "16. งานส่ง (prepare_deliver) เลือกจากผ้าของงานเก็บ (collect) ที่ปิดแล้ว — เรียก list_deliverable_collect_items หา id ก่อน แล้วส่ง items (operation_linen_product_id, deliver_pack)\n"
            . "17. วันที่ทุก action ไม่ระบุ = วันนี้ ลงย้อนหลังได้โดยใส่วันที่ Y-m-d";
    }
}
