<?php

namespace Tests\Unit;

use App\Services\AiChatService;
use App\Services\ChatService;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class AiChatServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.wavespeed.api_key' => 'test-key',
            'services.wavespeed.base_url' => 'https://llm.wavespeed.ai/v1',
            'services.wavespeed.model' => 'minimax/minimax-m2.7',
        ]);
    }

    public function test_answer_runs_tool_loop_and_returns_text_response(): void
    {
        Http::fake([
            'llm.wavespeed.ai/*' => Http::sequence()
                ->push([
                    'choices' => [[
                        'message' => [
                            'role' => 'assistant',
                            'content' => null,
                            'tool_calls' => [[
                                'id' => 'call_1',
                                'type' => 'function',
                                'function' => [
                                    'name' => 'get_today_summary',
                                    'arguments' => '{}',
                                ],
                            ]],
                        ],
                    ]],
                ])
                ->push([
                    'choices' => [[
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'วันนี้มีลูกค้า 12 ราย น้ำหนักผ้ารวม 350 กก.',
                        ],
                    ]],
                ]),
        ]);

        // mock ChatService กันไม่ให้ test แตะ database จริง
        $chatService = Mockery::mock(ChatService::class);
        $chatService->shouldReceive('getTodaySummary')->once()->andReturn([
            'type' => 'card',
            'title' => '📊 สรุปวันนี้',
            'rows' => [
                ['label' => 'ลูกค้า', 'value' => '12 ราย'],
                ['label' => 'น้ำหนักผ้า', 'value' => '350 กก.'],
            ],
        ]);
        $this->app->instance(ChatService::class, $chatService);

        $result = app(AiChatService::class)->answer('วันนี้ยอดเป็นยังไงบ้าง');

        $this->assertSame('text', $result['type']);
        $this->assertSame('🤖 AI ผู้ช่วย', $result['title']);
        $this->assertStringContainsString('350 กก.', $result['text']);

        Http::assertSentCount(2);
        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Bearer test-key')
                && $request['model'] === 'minimax/minimax-m2.7';
        });
    }

    public function test_answer_returns_fallback_menu_when_api_fails(): void
    {
        Http::fake([
            'llm.wavespeed.ai/*' => Http::response(['error' => 'internal'], 500),
        ]);

        $result = app(AiChatService::class)->answer('คำถามอะไรก็ได้');

        $this->assertSame('menu', $result['type']);
        $this->assertStringContainsString('ขัดข้อง', $result['text']);
    }

    public function test_unknown_command_without_api_key_keeps_old_behavior(): void
    {
        config(['services.wavespeed.api_key' => null]);
        Http::fake();

        $result = app(ChatService::class)->processCommand('คำถามมั่วๆ ที่ไม่ตรง rule');

        $this->assertSame('menu', $result['type']);
        $this->assertStringContainsString('ไม่เข้าใจคำสั่ง', $result['text']);

        Http::assertNothingSent();
    }

    public function test_rule_command_does_not_call_ai(): void
    {
        Http::fake();

        $result = app(ChatService::class)->processCommand('เมนู');

        $this->assertSame('menu', $result['type']);

        Http::assertNothingSent();
    }

    public function test_tool_definitions_consolidate_list_tools(): void
    {
        $service = app(AiChatService::class);
        $method = new \ReflectionMethod($service, 'toolDefinitions');
        $method->setAccessible(true);

        $names = array_map(fn ($t) => $t['function']['name'], $method->invoke($service));

        // ยุบ list_* 4 ตัว → list_entities ตัวเดียว
        $this->assertContains('list_entities', $names);
        $this->assertNotContains('list_customer_groups', $names);
        $this->assertNotContains('list_inventory_groups', $names);
        $this->assertNotContains('list_energy_resources', $names);
        $this->assertNotContains('list_departments', $names);
    }

    public function test_list_entities_invalid_kind_returns_error_without_db(): void
    {
        $service = app(AiChatService::class);
        $method = new \ReflectionMethod($service, 'executeTool');
        $method->setAccessible(true);

        // kind ผิด → คืน error ที่บอกค่าที่ใช้ได้ (default branch ไม่ query DB)
        $result = $method->invoke($service, 'list_entities', ['kind' => 'bogus']);

        $this->assertStringContainsString('kind', $result);
        $this->assertStringContainsString('customer_groups', $result);
    }

    public function test_summarize_compacts_messages_via_cheapest_model(): void
    {
        Http::fake([
            'llm.wavespeed.ai/*' => Http::response([
                'choices' => [['message' => ['content' => 'สรุป: ผู้ใช้ถามยอดขายวันนี้ ตอบ 12,345 บาท']]],
            ], 200),
        ]);

        $summary = app(AiChatService::class)->summarize(null, [
            ['role' => 'user', 'content' => 'ยอดขายวันนี้เท่าไร'],
            ['role' => 'assistant', 'content' => '12,345 บาท'],
        ]);

        $this->assertSame('สรุป: ผู้ใช้ถามยอดขายวันนี้ ตอบ 12,345 บาท', $summary);
        Http::assertSentCount(1);
        // ย่อด้วย model ตาม config (ถูกสุด) ไม่ส่ง tools
        Http::assertSent(function ($request) {
            return $request['model'] === 'minimax/minimax-m2.7' && !isset($request['tools']);
        });
    }
}
