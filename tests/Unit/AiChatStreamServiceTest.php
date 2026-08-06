<?php

namespace Tests\Unit;

use App\Exceptions\ClientDisconnectedException;
use App\Services\AiChatStreamService;
use App\Services\ChatService;
use App\Services\EntityWriteService;
use App\Services\Llm\LlmProviderManager;
use App\Services\OperationActionService;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class AiChatStreamServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'ai-chat.default_provider' => 'wavespeed',
            'ai-chat.providers.wavespeed.api_key' => 'test-key',
            'ai-chat.providers.wavespeed.base_url' => 'https://llm.wavespeed.ai/v1',
            'ai-chat.providers.wavespeed.default_model' => 'minimax/minimax-m2.7',
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function makeService(?ChatService $chatService = null): AiChatStreamService
    {
        return new AiChatStreamService(
            new LlmProviderManager(),
            $chatService ?: Mockery::mock(ChatService::class),
            app(EntityWriteService::class),
            app(OperationActionService::class),
            app(\App\Services\DocumentService::class),
        );
    }

    /** สร้าง SSE body จาก array ของ chunk (json) */
    protected function sse(array $chunks, bool $done = true): string
    {
        $body = '';
        foreach ($chunks as $chunk) {
            $body .= 'data: ' . json_encode($chunk, JSON_UNESCAPED_UNICODE) . "\n\n";
        }
        if ($done) {
            $body .= "data: [DONE]\n\n";
        }

        return $body;
    }

    public function test_tool_loop_executes_tool_and_streams_final_answer(): void
    {
        // iteration 1: tool_call แตกเป็น 2 fragment + finish_reason
        $iteration1 = $this->sse([
            ['choices' => [['delta' => ['tool_calls' => [
                ['index' => 0, 'id' => 'call_1', 'function' => ['name' => 'get_today_summary', 'arguments' => '']],
            ]]]]],
            ['choices' => [['delta' => ['tool_calls' => [
                ['index' => 0, 'function' => ['arguments' => '{}']],
            ]]]]],
            ['choices' => [['delta' => [], 'finish_reason' => 'tool_calls']]],
            ['choices' => [], 'usage' => ['prompt_tokens' => 100, 'completion_tokens' => 20]],
        ]);

        // iteration 2: คำตอบสุดท้าย
        $iteration2 = $this->sse([
            ['choices' => [['delta' => ['content' => 'วันนี้มี']]]],
            ['choices' => [['delta' => ['content' => 'ลูกค้า 12 ราย']]]],
            ['choices' => [], 'usage' => ['prompt_tokens' => 150, 'completion_tokens' => 30]],
        ]);

        Http::fake([
            'llm.wavespeed.ai/*' => Http::sequence()
                ->push($iteration1, 200)
                ->push($iteration2, 200),
        ]);

        $chatService = Mockery::mock(ChatService::class);
        $chatService->shouldReceive('getTodaySummary')->once()->andReturn([
            'type' => 'card',
            'title' => '📊 สรุปวันนี้',
            'rows' => [['label' => 'ลูกค้า', 'value' => '12 ราย']],
        ]);

        $events = [];
        $result = $this->makeService($chatService)->streamAnswer(
            [['role' => 'user', 'content' => 'สรุปวันนี้']],
            'minimax/minimax-m2.7',
            1024,
            function (array $event) use (&$events) {
                $events[] = $event;
            }
        );

        $this->assertSame('วันนี้มีลูกค้า 12 ราย', $result['content']);
        $this->assertFalse($result['aborted']);
        $this->assertFalse($result['estimated']);
        // usage บวกสะสมข้าม iteration
        $this->assertSame(250, $result['usage']['prompt_tokens']);
        $this->assertSame(50, $result['usage']['completion_tokens']);

        // tool_status running + done
        $statuses = array_values(array_filter($events, fn ($e) => ($e['type'] ?? '') === 'tool_status'));
        $this->assertCount(2, $statuses);
        $this->assertSame('running', $statuses[0]['status']);
        $this->assertSame('done', $statuses[1]['status']);
        $this->assertSame('get_today_summary', $statuses[0]['name']);

        // content delta ส่งครบ 2 ก้อน — ไม่มี [DONE] ของ upstream หลุดมา
        $contents = array_values(array_filter($events, fn ($e) => isset($e['choices'][0]['delta']['content'])));
        $this->assertCount(2, $contents);
    }

    public function test_cached_tokens_accumulate_and_tool_status_has_thai_label(): void
    {
        $iteration1 = $this->sse([
            ['choices' => [['delta' => ['tool_calls' => [
                ['index' => 0, 'id' => 'call_1', 'function' => ['name' => 'get_today_summary', 'arguments' => '{}']],
            ]]]]],
            ['choices' => [['delta' => [], 'finish_reason' => 'tool_calls']]],
            ['choices' => [], 'usage' => ['prompt_tokens' => 100, 'completion_tokens' => 20, 'prompt_tokens_details' => ['cached_tokens' => 80]]],
        ]);

        $iteration2 = $this->sse([
            ['choices' => [['delta' => ['content' => 'ตอบแล้ว']]]],
            ['choices' => [], 'usage' => ['prompt_tokens' => 150, 'completion_tokens' => 10, 'prompt_tokens_details' => ['cached_tokens' => 120]]],
        ]);

        Http::fake([
            'llm.wavespeed.ai/*' => Http::sequence()
                ->push($iteration1, 200)
                ->push($iteration2, 200),
        ]);

        $chatService = Mockery::mock(ChatService::class);
        $chatService->shouldReceive('getTodaySummary')->once()->andReturn(['type' => 'text', 'text' => 'ok']);

        $events = [];
        $result = $this->makeService($chatService)->streamAnswer(
            [['role' => 'user', 'content' => 'สรุปวันนี้']],
            'minimax/minimax-m2.7',
            null,
            function (array $event) use (&$events) {
                $events[] = $event;
            }
        );

        // cached_tokens บวกสะสมข้าม iteration (80 + 120)
        $this->assertSame(250, $result['usage']['prompt_tokens']);
        $this->assertSame(200, $result['usage']['cached_tokens']);

        // tool_status มี label ภาษาไทย
        $statuses = array_values(array_filter($events, fn ($e) => ($e['type'] ?? '') === 'tool_status'));
        $this->assertSame('สรุปวันนี้', $statuses[0]['label']);
    }

    public function test_two_parallel_tool_calls_with_fragmented_arguments(): void
    {
        $iteration1 = $this->sse([
            ['choices' => [['delta' => ['tool_calls' => [
                ['index' => 0, 'id' => 'call_a', 'function' => ['name' => 'get_today_summary', 'arguments' => '']],
                ['index' => 1, 'id' => 'call_b', 'function' => ['name' => 'get_business_report', 'arguments' => '{"ty']],
            ]]]]],
            // arguments ของ index 1 ต่ออีก 2 fragment — ไม่มี finish_reason เลย (บาง vendor ไม่ส่ง)
            ['choices' => [['delta' => ['tool_calls' => [
                ['index' => 1, 'function' => ['arguments' => 'pe":"sum']],
            ]]]]],
            ['choices' => [['delta' => ['tool_calls' => [
                ['index' => 1, 'function' => ['arguments' => 'mary"}']],
            ]]]]],
        ]);

        $iteration2 = $this->sse([
            ['choices' => [['delta' => ['content' => 'เรียบร้อย']]]],
        ]);

        Http::fake([
            'llm.wavespeed.ai/*' => Http::sequence()
                ->push($iteration1, 200)
                ->push($iteration2, 200),
        ]);

        $chatService = Mockery::mock(ChatService::class);
        $chatService->shouldReceive('getTodaySummary')->once()->andReturn(['type' => 'text', 'text' => 'ok']);
        $chatService->shouldReceive('getReportByDate')
            ->once()
            ->with('summary', null, 'day')
            ->andReturn(['type' => 'text', 'text' => 'report ok']);

        $result = $this->makeService($chatService)->streamAnswer(
            [['role' => 'user', 'content' => 'สรุปพร้อมรายงาน']],
            'minimax/minimax-m2.7',
            null,
            function () {
            }
        );

        $this->assertSame('เรียบร้อย', $result['content']);
        // ไม่มี usage chunk เลย → estimated
        $this->assertTrue($result['estimated']);
        $this->assertNull($result['usage']);
    }

    public function test_http_400_falls_back_to_json_intent_mode(): void
    {
        Http::fake([
            'llm.wavespeed.ai/*' => Http::sequence()
                ->push('{"error":"tools not supported"}', 400)
                ->push([
                    'choices' => [[
                        'message' => ['role' => 'assistant', 'content' => '{"answer":"ตอบจาก fallback"}'],
                    ]],
                ], 200),
        ]);

        $events = [];
        $result = $this->makeService()->streamAnswer(
            [['role' => 'user', 'content' => 'สวัสดี']],
            'some/no-tools-model',
            null,
            function (array $event) use (&$events) {
                $events[] = $event;
            }
        );

        $this->assertSame('ตอบจาก fallback', $result['content']);
        $this->assertTrue($result['estimated']);
        $this->assertFalse($result['aborted']);

        // คำตอบทั้งก้อนถูก emit เป็น content chunk เดียว
        $contents = array_values(array_filter($events, fn ($e) => isset($e['choices'][0]['delta']['content'])));
        $this->assertCount(1, $contents);
        $this->assertSame('ตอบจาก fallback', $contents[0]['choices'][0]['delta']['content']);
    }

    public function test_client_abort_returns_partial_content(): void
    {
        Http::fake([
            'llm.wavespeed.ai/*' => Http::response($this->sse([
                ['choices' => [['delta' => ['content' => 'ส่วนแรก']]]],
                ['choices' => [['delta' => ['content' => 'ส่วนสอง']]]],
                ['choices' => [['delta' => ['content' => 'ส่วนสาม']]]],
            ]), 200),
        ]);

        $emitCount = 0;
        $result = $this->makeService()->streamAnswer(
            [['role' => 'user', 'content' => 'เล่าเรื่องยาวๆ']],
            'minimax/minimax-m2.7',
            null,
            function () use (&$emitCount) {
                $emitCount++;
                if ($emitCount >= 2) {
                    throw new ClientDisconnectedException('client aborted');
                }
            }
        );

        $this->assertTrue($result['aborted']);
        $this->assertSame('ส่วนแรกส่วนสอง', $result['content']);
    }

    public function test_reasoning_deltas_are_not_forwarded(): void
    {
        Http::fake([
            'llm.wavespeed.ai/*' => Http::response($this->sse([
                ['choices' => [['delta' => ['reasoning_content' => 'คิดในใจ...']]]],
                ['choices' => [['delta' => ['content' => 'คำตอบจริง']]]],
            ]), 200),
        ]);

        $events = [];
        $result = $this->makeService()->streamAnswer(
            [['role' => 'user', 'content' => 'ถามอะไรก็ได้']],
            'minimax/minimax-m2.7',
            null,
            function (array $event) use (&$events) {
                $events[] = $event;
            }
        );

        $this->assertSame('คำตอบจริง', $result['content']);
        $this->assertCount(1, $events);
        $this->assertSame('คำตอบจริง', $events[0]['choices'][0]['delta']['content']);
    }

    /**
     * model ตระกูล reasoning ใช้โควตา max_tokens ไปกับการคิดจนไม่เหลือ content
     * ต้องแจ้งสาเหตุ ไม่ใช่ปล่อยข้อความว่าง
     */
    public function test_answer_starved_by_reasoning_tokens_explains_itself(): void
    {
        Http::fake([
            'llm.wavespeed.ai/*' => Http::response($this->sse([
                ['choices' => [['delta' => ['reasoning_content' => 'คิดยาวมาก...']]]],
                ['choices' => [], 'usage' => [
                    'prompt_tokens' => 100,
                    'completion_tokens' => 200,
                    'completion_tokens_details' => ['reasoning_tokens' => 200],
                ]],
            ]), 200),
        ]);

        $events = [];
        $result = $this->makeService()->streamAnswer(
            [['role' => 'user', 'content' => 'ถามอะไรก็ได้']],
            'minimax/minimax-m2.7',
            200,
            function (array $event) use (&$events) {
                $events[] = $event;
            }
        );

        $this->assertStringContainsString('reasoning 200 tokens', $result['content']);
        $this->assertStringContainsString('เพดาน 200', $result['content']);
        $this->assertSame($result['content'], $events[0]['choices'][0]['delta']['content']);
    }

    public function test_empty_answer_without_reasoning_gets_generic_notice(): void
    {
        Http::fake([
            'llm.wavespeed.ai/*' => Http::response($this->sse([
                ['choices' => [['delta' => []]]],
            ]), 200),
        ]);

        $result = $this->makeService()->streamAnswer(
            [['role' => 'user', 'content' => 'ถามอะไรก็ได้']],
            'minimax/minimax-m2.7',
            null,
            fn (array $event) => null
        );

        $this->assertStringContainsString('ไม่ได้รับคำตอบจาก AI', $result['content']);
    }
}
