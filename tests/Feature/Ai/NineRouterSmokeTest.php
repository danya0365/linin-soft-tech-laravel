<?php

namespace Tests\Feature\Ai;

use App\Contracts\LlmProvider;
use App\Services\Llm\LlmProviderManager;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * ยิง 9Router ตัวจริง (ไม่ mock) — โฮสต์ที่ไหนก็ได้ตาม NINEROUTER_BASE_URL
 *
 * ข้ามอัตโนมัติเมื่อไม่ได้ตั้ง base_url หรือต่อไม่ติด
 * (phpunit.xml บังคับ base_url ว่างไว้ เทสนี้จึงต้องอ่านจาก .env เอง)
 *
 * @group live
 */
class NineRouterSmokeTest extends TestCase
{
    protected LlmProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        // phpunit.xml เคลียร์ค่านี้ไว้กันเทสอื่นยิงของจริง — smoke test อ่านค่าจริงจาก .env
        $baseUrl = trim((string) ($_SERVER['NINEROUTER_BASE_URL'] ?? '')) ?: $this->baseUrlFromEnvFile();

        if ($baseUrl === '') {
            $this->markTestSkipped('ยังไม่ได้ตั้ง NINEROUTER_BASE_URL');
        }

        config(['ai-chat.providers.9router.base_url' => $baseUrl]);

        if (!$this->endpointReachable($baseUrl)) {
            $this->markTestSkipped("ต่อ 9Router ไม่ติด ({$baseUrl})");
        }

        $this->provider = (new LlmProviderManager())->forName('9router');
    }

    /** อ่านตรงจาก .env เพราะ phpunit.xml override ค่าใน environment ไปแล้ว */
    protected function baseUrlFromEnvFile(): string
    {
        $path = base_path('.env');
        if (!is_readable($path)) {
            return '';
        }

        preg_match('/^NINEROUTER_BASE_URL=(.*)$/m', (string) file_get_contents($path), $m);

        return trim($m[1] ?? '', " \t\"'");
    }

    /**
     * ต้องเป็น HTTP จริง ไม่ใช่ socket ไป localhost — 9Router อยู่คนละ server หรือหลัง HTTPS ก็ได้
     */
    protected function endpointReachable(string $baseUrl): bool
    {
        try {
            return Http::timeout(5)
                ->connectTimeout(3)
                ->get(rtrim($baseUrl, '/') . '/models')
                ->successful();
        } catch (\Throwable $e) {
            return false;
        }
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
