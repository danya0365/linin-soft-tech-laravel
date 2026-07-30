<?php

namespace Tests\Feature\Ai;

use App\Services\Llm\LlmProviderManager;
use App\Services\Llm\OpenAiCompatibleProvider;
use Tests\TestCase;

/**
 * ยิง endpoint OpenAI-compatible ในเครื่องจริง (ไม่ mock)
 *
 * ข้ามอัตโนมัติเมื่อ endpoint ไม่พร้อม — CI และเครื่องที่ไม่ได้รัน gateway จะไม่พัง
 * รันเฉพาะกลุ่มนี้: php artisan test --group=live
 *
 * @group live
 */
class LocalProviderSmokeTest extends TestCase
{
    protected OpenAiCompatibleProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $baseUrl = env('LLM_LOCAL_BASE_URL', 'http://localhost:20128/v1');

        config([
            'ai-chat.providers.local.enabled' => true,
            'ai-chat.providers.local.base_url' => $baseUrl,
        ]);

        if (!$this->endpointReachable($baseUrl)) {
            $this->markTestSkipped("LLM endpoint ในเครื่องไม่พร้อม ({$baseUrl})");
        }

        $this->provider = (new LlmProviderManager())->forName('local');
    }

    protected function endpointReachable(string $baseUrl): bool
    {
        $parts = parse_url($baseUrl);
        $handle = @fsockopen(
            $parts['host'] ?? 'localhost',
            $parts['port'] ?? 80,
            $errno,
            $errstr,
            1.0
        );

        if ($handle === false) {
            return false;
        }

        fclose($handle);

        return true;
    }

    protected function model(): string
    {
        return $this->provider->defaultModel();
    }

    /**
     * ครอบ quirk ของ gateway: body ที่ไม่ได้ stream มี `data: [DONE]` ต่อท้าย
     * ถ้า decode พังจะ throw ตรงนี้
     */
    public function test_non_streaming_completion_returns_content(): void
    {
        $body = $this->provider->chatCompletion([
            ['role' => 'user', 'content' => 'ตอบกลับด้วยคำว่า PONG เท่านั้น ห้ามมีข้อความอื่น'],
        ]);

        $content = $body['choices'][0]['message']['content'] ?? '';

        $this->assertNotSame('', trim($content), 'ต้องได้ content กลับมา');
        $this->assertStringContainsStringIgnoringCase('pong', $content);
        $this->assertGreaterThan(0, $body['usage']['total_tokens'] ?? 0);
    }

    public function test_streaming_emits_content_deltas_and_usage(): void
    {
        $body = $this->provider->streamChatCompletion(
            [['role' => 'user', 'content' => 'นับ 1 ถึง 5 คั่นด้วยเว้นวรรค']],
            $this->model(),
            // ต้องเผื่อ reasoning token ด้วย ไม่งั้นโควตาหมดก่อนได้ content
            1500
        );

        [$content, $usage] = $this->consume($body);

        $this->assertNotSame('', trim($content), 'ต้องมี content delta อย่างน้อยหนึ่งก้อน');
        $this->assertGreaterThan(0, $usage['completion_tokens'] ?? 0, 'ต้องได้ usage จาก stream');
    }

    /**
     * tool call ผ่าน stream — ส่วนที่เปราะที่สุดของ integration
     * (โครง delta ต้องเป็นแบบ OpenAI ถึงจะ accumulate ได้)
     */
    public function test_streaming_tool_call_arrives_in_openai_delta_format(): void
    {
        $tools = [[
            'type' => 'function',
            'function' => [
                'name' => 'get_weather',
                'description' => 'ดูสภาพอากาศของเมืองที่ระบุ',
                'parameters' => [
                    'type' => 'object',
                    'properties' => ['city' => ['type' => 'string']],
                    'required' => ['city'],
                ],
            ],
        ]];

        $body = $this->provider->streamChatCompletion(
            [['role' => 'user', 'content' => 'อากาศที่กรุงเทพเป็นยังไง ใช้เครื่องมือ get_weather']],
            $this->model(),
            300,
            $tools
        );

        [, , $toolCalls] = $this->consume($body);

        $this->assertNotEmpty($toolCalls, 'ต้องมี tool_calls delta');
        $this->assertSame('get_weather', $toolCalls[0]['name']);
        $this->assertNotNull(
            json_decode($toolCalls[0]['arguments'], true),
            'arguments ที่ต่อกันแล้วต้องเป็น JSON ที่ parse ได้: ' . $toolCalls[0]['arguments']
        );
    }

    /**
     * อ่าน SSE ด้วยตรรกะเดียวกับ AiChatStreamService (data: ... / [DONE])
     *
     * @return array{0: string, 1: array, 2: array}
     */
    protected function consume($body): array
    {
        $content = '';
        $usage = [];
        $pending = [];
        $buffer = '';

        while (!$body->eof()) {
            $buffer .= $body->read(8192);

            while (($pos = strpos($buffer, "\n\n")) !== false) {
                $line = trim(substr($buffer, 0, $pos));
                $buffer = substr($buffer, $pos + 2);

                if (strpos($line, 'data:') !== 0) {
                    continue;
                }

                $payload = trim(substr($line, 5));
                if ($payload === '[DONE]') {
                    $body->close();

                    return [$content, $usage, array_values($pending)];
                }

                $chunk = json_decode($payload, true);
                if (!is_array($chunk)) {
                    continue;
                }

                $delta = $chunk['choices'][0]['delta'] ?? [];

                if (is_string($delta['content'] ?? null)) {
                    $content .= $delta['content'];
                }

                foreach ($delta['tool_calls'] ?? [] as $i => $call) {
                    $index = $call['index'] ?? $i;
                    $pending[$index]['name'] ??= $call['function']['name'] ?? null;
                    $pending[$index]['arguments'] = ($pending[$index]['arguments'] ?? '')
                        . ($call['function']['arguments'] ?? '');
                }

                if (!empty($chunk['usage'])) {
                    $usage = $chunk['usage'];
                }
            }
        }

        $body->close();

        return [$content, $usage, array_values($pending)];
    }
}
