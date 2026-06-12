<?php

namespace App\Services;

use App\Exceptions\ClientDisconnectedException;
use App\Exceptions\WaveSpeedApiException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\StreamInterface;

/**
 * AI Chat Stream Service (หน้าเว็บ /ai-chat)
 *
 * Streaming tool loop: เรียก WaveSpeed แบบ stream + tools,
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
     * @throws WaveSpeedApiException เมื่อ upstream ล้มเหลว (ก่อนได้ content ใดๆ)
     */
    public function streamAnswer(array $messages, string $model, ?int $maxTokens, callable $emit): array
    {
        try {
            return $this->runStreamingToolLoop($messages, $model, $maxTokens, $emit);
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
            'completion_tokens' => ($this->usageTotal['completion_tokens'] ?? 0) + ((int) ($usage['completion_tokens'] ?? 0)),
        ];
    }

    protected function runStreamingToolLoop(array $messages, string $model, ?int $maxTokens, callable $emit): array
    {
        $deadline = microtime(true) + self::STREAM_DEADLINE_SECONDS;

        $this->partialContent = '';
        $this->usageTotal = null;

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
                $body = $this->llm->streamChatCompletion($workMessages, $model, $maxTokens, $tools);
            } catch (WaveSpeedApiException $e) {
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
                $emit(['type' => 'tool_status', 'status' => 'running', 'name' => $call['name']]);

                $args = json_decode($call['arguments'], true) ?: [];

                Log::info('AiChatStreamService tool call', ['tool' => $call['name'], 'args' => $args]);

                $workMessages[] = [
                    'role' => 'tool',
                    'tool_call_id' => $call['id'],
                    'content' => $this->executeTool($call['name'], $args),
                ];

                $emit(['type' => 'tool_status', 'status' => 'done', 'name' => $call['name']]);
            }
        }

        // หมดรอบ/หมดเวลา — บังคับสรุปด้วย call สุดท้ายแบบไม่มี tools (ยัง stream)
        $workMessages[] = [
            'role' => 'user',
            'content' => 'กรุณาสรุปคำตอบจากข้อมูลที่ได้มาแล้วข้างต้นทันที โดยไม่ต้องเรียกเครื่องมือเพิ่ม',
        ];

        $body = $this->llm->streamChatCompletion($workMessages, $model, $maxTokens);
        $this->consumeIteration($body, $emit);

        return [
            'content' => $this->partialContent,
            'usage' => $this->usageTotals(),
            'estimated' => $this->usageTotal === null,
            'aborted' => false,
        ];
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
            . "6. คำถามทั่วไปที่ไม่เกี่ยวกับข้อมูลในระบบ ตอบได้เลยโดยไม่ต้องเรียกเครื่องมือ";
    }
}
