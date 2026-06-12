<?php

namespace Tests\Unit;

use App\Exceptions\WaveSpeedApiException;
use App\Services\WaveSpeedLlmService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WaveSpeedLlmStreamTest extends TestCase
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

    public function test_stream_sends_stream_payload_and_returns_body(): void
    {
        $sse = "data: {\"choices\":[{\"delta\":{\"content\":\"สวัสดี\"}}]}\n\ndata: [DONE]\n\n";

        Http::fake([
            'llm.wavespeed.ai/*' => Http::response($sse, 200),
        ]);

        $service = new WaveSpeedLlmService();
        $body = $service->streamChatCompletion(
            [['role' => 'user', 'content' => 'สวัสดี']],
            'anthropic/claude-sonnet-4.6',
            512
        );

        $this->assertSame($sse, (string) $body);

        Http::assertSent(function ($request) {
            $data = $request->data();

            return $request->url() === 'https://llm.wavespeed.ai/v1/chat/completions'
                && $data['stream'] === true
                && $data['stream_options'] === ['include_usage' => true]
                && $data['model'] === 'anthropic/claude-sonnet-4.6'
                && $data['max_tokens'] === 512
                && $data['messages'] === [['role' => 'user', 'content' => 'สวัสดี']];
        });
    }

    public function test_stream_uses_default_model_and_omits_max_tokens(): void
    {
        Http::fake([
            'llm.wavespeed.ai/*' => Http::response("data: [DONE]\n\n", 200),
        ]);

        $service = new WaveSpeedLlmService();
        $service->streamChatCompletion([['role' => 'user', 'content' => 'hi']]);

        Http::assertSent(function ($request) {
            $data = $request->data();

            return $data['model'] === 'minimax/minimax-m2.7'
                && !array_key_exists('max_tokens', $data)
                && !array_key_exists('tools', $data);
        });
    }

    public function test_stream_sends_tools_when_provided(): void
    {
        Http::fake([
            'llm.wavespeed.ai/*' => Http::response("data: [DONE]\n\n", 200),
        ]);

        $tools = [[
            'type' => 'function',
            'function' => ['name' => 'get_today_summary', 'description' => 'x', 'parameters' => ['type' => 'object']],
        ]];

        $service = new WaveSpeedLlmService();
        $service->streamChatCompletion([['role' => 'user', 'content' => 'hi']], null, null, $tools);

        Http::assertSent(function ($request) use ($tools) {
            $data = $request->data();

            return $data['tools'] === $tools
                && $data['tool_choice'] === 'auto';
        });
    }

    public function test_stream_throws_on_upstream_error(): void
    {
        Http::fake([
            'llm.wavespeed.ai/*' => Http::response('{"error":"rate limited"}', 429),
        ]);

        $service = new WaveSpeedLlmService();

        try {
            $service->streamChatCompletion([['role' => 'user', 'content' => 'hi']]);
            $this->fail('Expected WaveSpeedApiException');
        } catch (WaveSpeedApiException $e) {
            $this->assertSame(429, $e->getHttpStatus());
        }
    }

    public function test_stream_throws_when_api_key_missing(): void
    {
        config(['services.wavespeed.api_key' => null]);

        $service = new WaveSpeedLlmService();

        $this->expectException(WaveSpeedApiException::class);
        $service->streamChatCompletion([['role' => 'user', 'content' => 'hi']]);
    }
}
