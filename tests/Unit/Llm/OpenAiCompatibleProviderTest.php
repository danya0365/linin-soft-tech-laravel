<?php

namespace Tests\Unit\Llm;

use App\Exceptions\LlmApiException;
use App\Services\Llm\OpenAiCompatibleProvider;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenAiCompatibleProviderTest extends TestCase
{
    protected function provider(array $overrides = []): OpenAiCompatibleProvider
    {
        return new OpenAiCompatibleProvider($overrides + [
            'name' => 'wavespeed',
            'label' => 'WaveSpeed',
            'api_key' => 'test-key',
            'base_url' => 'https://llm.wavespeed.ai/v1',
            'default_model' => 'minimax/minimax-m2.7',
            'requires_key' => true,
            'timeout' => 25,
            'max_tokens' => 1024,
        ]);
    }

    // ── streaming ────────────────────────────────────────────────────────

    public function test_stream_sends_stream_payload_and_returns_body(): void
    {
        $sse = "data: {\"choices\":[{\"delta\":{\"content\":\"สวัสดี\"}}]}\n\ndata: [DONE]\n\n";

        Http::fake(['llm.wavespeed.ai/*' => Http::response($sse, 200)]);

        $body = $this->provider()->streamChatCompletion(
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
        Http::fake(['llm.wavespeed.ai/*' => Http::response("data: [DONE]\n\n", 200)]);

        $this->provider()->streamChatCompletion([['role' => 'user', 'content' => 'hi']]);

        Http::assertSent(function ($request) {
            $data = $request->data();

            return $data['model'] === 'minimax/minimax-m2.7'
                && !array_key_exists('max_tokens', $data)
                && !array_key_exists('tools', $data);
        });
    }

    public function test_stream_sends_tools_when_provided(): void
    {
        Http::fake(['llm.wavespeed.ai/*' => Http::response("data: [DONE]\n\n", 200)]);

        $tools = [[
            'type' => 'function',
            'function' => ['name' => 'get_today_summary', 'description' => 'x', 'parameters' => ['type' => 'object']],
        ]];

        $this->provider()->streamChatCompletion([['role' => 'user', 'content' => 'hi']], null, null, $tools);

        Http::assertSent(function ($request) use ($tools) {
            $data = $request->data();

            return $data['tools'] === $tools && $data['tool_choice'] === 'auto';
        });
    }

    public function test_stream_throws_on_upstream_error(): void
    {
        Http::fake(['llm.wavespeed.ai/*' => Http::response('{"error":"rate limited"}', 429)]);

        try {
            $this->provider()->streamChatCompletion([['role' => 'user', 'content' => 'hi']]);
            $this->fail('Expected LlmApiException');
        } catch (LlmApiException $e) {
            $this->assertSame(429, $e->getHttpStatus());
        }
    }

    public function test_stream_throws_when_api_key_missing(): void
    {
        $this->expectException(LlmApiException::class);

        $this->provider(['api_key' => null])->streamChatCompletion([['role' => 'user', 'content' => 'hi']]);
    }

    // ── enablement ───────────────────────────────────────────────────────

    public function test_provider_without_required_key_is_enabled(): void
    {
        $provider = $this->provider(['api_key' => null, 'requires_key' => false]);

        $this->assertTrue($provider->isEnabled());
    }

    public function test_provider_can_be_disabled_by_flag(): void
    {
        $this->assertFalse($this->provider(['enabled' => false])->isEnabled());
    }

    /** ไม่ได้ตั้ง base_url = ยังไม่ได้ตั้งค่า provider นี้ → ปิด แม้จะไม่ต้องใช้ key */
    public function test_provider_without_base_url_is_disabled(): void
    {
        $provider = $this->provider(['base_url' => null, 'requires_key' => false]);

        $this->assertFalse($provider->isEnabled());
    }

    public function test_authorization_header_is_omitted_without_key(): void
    {
        Http::fake(['router.example.test/*' => Http::response("data: [DONE]\n\n", 200)]);

        $this->provider([
            'name' => '9router',
            'api_key' => null,
            'requires_key' => false,
            'base_url' => 'https://router.example.test/v1',
        ])->streamChatCompletion([['role' => 'user', 'content' => 'hi']]);

        Http::assertSent(fn ($request) => !$request->hasHeader('Authorization'));
    }

    // ── non-streaming + quirk handling ───────────────────────────────────

    public function test_chat_completion_returns_decoded_body(): void
    {
        Http::fake(['llm.wavespeed.ai/*' => Http::response([
            'choices' => [['message' => ['content' => 'OK']]],
            'usage' => ['total_tokens' => 10],
        ], 200)]);

        $body = $this->provider()->chatCompletion([['role' => 'user', 'content' => 'hi']]);

        $this->assertSame('OK', $body['choices'][0]['message']['content']);
    }

    /**
     * gateway บางตัวต่อ SSE sentinel ท้าย body ของ response ที่ไม่ได้ stream
     * ทำให้ JSON ไม่ valid — provider ต้องตัดทิ้งแล้ว decode ได้
     */
    public function test_chat_completion_tolerates_trailing_sse_sentinel(): void
    {
        $json = json_encode([
            'choices' => [['message' => ['content' => 'PONG }" ตัวหลอก']]],
            'usage' => ['total_tokens' => 42],
        ], JSON_UNESCAPED_UNICODE);

        Http::fake(['router.example.test/*' => Http::response($json . "data: [DONE]\n\n", 200)]);

        $body = $this->provider([
            'name' => '9router',
            'base_url' => 'https://router.example.test/v1',
        ])->chatCompletion([['role' => 'user', 'content' => 'ping']]);

        $this->assertSame('PONG }" ตัวหลอก', $body['choices'][0]['message']['content']);
        $this->assertSame(42, $body['usage']['total_tokens']);
    }

    public function test_chat_completion_throws_on_unrecoverable_body(): void
    {
        Http::fake(['llm.wavespeed.ai/*' => Http::response('not json at all', 200)]);

        $this->expectException(LlmApiException::class);

        $this->provider()->chatCompletion([['role' => 'user', 'content' => 'hi']]);
    }

    public function test_chat_completion_honours_model_override(): void
    {
        Http::fake(['llm.wavespeed.ai/*' => Http::response(['choices' => []], 200)]);

        $this->provider()->chatCompletion([['role' => 'user', 'content' => 'hi']], [], 'deepseek/deepseek-v4', 256);

        Http::assertSent(function ($request) {
            $data = $request->data();

            return $data['model'] === 'deepseek/deepseek-v4' && $data['max_tokens'] === 256;
        });
    }
}
