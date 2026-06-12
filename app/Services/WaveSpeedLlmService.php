<?php

namespace App\Services;

use App\Exceptions\WaveSpeedApiException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * WaveSpeed LLM Service
 *
 * HTTP wrapper สำหรับ WaveSpeed LLM API (OpenAI-compatible chat completions)
 * ใช้โดย AiChatService
 */
class WaveSpeedLlmService
{
    protected ?string $apiKey;
    protected string $baseUrl;
    protected string $model;
    protected int $timeout;
    protected int $maxTokens;

    public function __construct()
    {
        $this->apiKey = config('services.wavespeed.api_key');
        $this->baseUrl = rtrim(config('services.wavespeed.base_url', 'https://llm.wavespeed.ai/v1'), '/');
        $this->model = config('services.wavespeed.model', 'minimax/minimax-m2.7');
        $this->timeout = (int) config('services.wavespeed.timeout', 25);
        $this->maxTokens = (int) config('services.wavespeed.max_tokens', 1024);
    }

    /**
     * มี API key หรือไม่ (feature flag)
     */
    public function isEnabled(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * เรียก POST /chat/completions
     *
     * @param array $messages OpenAI-style messages
     * @param array $tools OpenAI-style tool definitions (ว่าง = ไม่ส่ง tools)
     * @return array decoded response body
     * @throws WaveSpeedApiException เมื่อ non-2xx หรือ connection ล้มเหลว
     */
    public function chatCompletion(array $messages, array $tools = []): array
    {
        if (!$this->isEnabled()) {
            throw new WaveSpeedApiException('WaveSpeed API key is not configured');
        }

        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'max_tokens' => $this->maxTokens,
            'temperature' => 0.2,
        ];

        if (!empty($tools)) {
            $payload['tools'] = $tools;
            $payload['tool_choice'] = 'auto';
        }

        $startedAt = microtime(true);

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout($this->timeout)
                ->connectTimeout(5)
                ->post($this->baseUrl . '/chat/completions', $payload);
        } catch (\Exception $e) {
            Log::error('WaveSpeed API connection failed', ['error' => $e->getMessage()]);
            throw new WaveSpeedApiException('Connection failed: ' . $e->getMessage(), null, $e);
        }

        $latencyMs = (int) round((microtime(true) - $startedAt) * 1000);

        if (!$response->successful()) {
            Log::error('WaveSpeed API error', [
                'status' => $response->status(),
                'body' => mb_substr($response->body(), 0, 1000),
                'latency_ms' => $latencyMs,
            ]);
            throw new WaveSpeedApiException(
                'WaveSpeed API returned HTTP ' . $response->status(),
                $response->status()
            );
        }

        $body = $response->json();

        Log::info('WaveSpeed API call', [
            'model' => $this->model,
            'latency_ms' => $latencyMs,
            'usage' => $body['usage'] ?? null,
        ]);

        return $body;
    }

    /**
     * เรียก POST /chat/completions แบบ streaming (SSE)
     *
     * คืน PSR-7 body stream ของ upstream เพื่อ pass-through ไปยัง client ตรงๆ
     * (รูปแบบ chunk: data: {"choices":[{"delta":{"content":"..."}}]} ... data: [DONE])
     *
     * @param array $messages OpenAI-style messages
     * @param string|null $model override model (null = ใช้ค่า config)
     * @param int|null $maxTokens override max_tokens (null = ไม่ส่ง ให้ upstream ใช้ค่า default)
     * @param array $tools OpenAI-style tool definitions (ว่าง = ไม่ส่ง tools)
     * @return \Psr\Http\Message\StreamInterface
     * @throws WaveSpeedApiException เมื่อ non-2xx หรือ connection ล้มเหลว
     */
    public function streamChatCompletion(array $messages, ?string $model = null, ?int $maxTokens = null, array $tools = [])
    {
        if (!$this->isEnabled()) {
            throw new WaveSpeedApiException('WaveSpeed API key is not configured');
        }

        $payload = [
            'model' => $model ?: $this->model,
            'messages' => $messages,
            'stream' => true,
            // ขอ usage จริง (prompt/completion tokens) มากับ chunk สุดท้ายของ stream
            'stream_options' => ['include_usage' => true],
        ];

        if ($maxTokens !== null) {
            $payload['max_tokens'] = $maxTokens;
        }

        if (!empty($tools)) {
            $payload['tools'] = $tools;
            $payload['tool_choice'] = 'auto';
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->withHeaders(['Accept' => 'text/event-stream'])
                ->withOptions(['stream' => true])
                // streaming ใช้เวลานานกว่า request ปกติ — ไม่ใช้ timeout สั้นของ tool loop
                ->timeout(max($this->timeout, 120))
                ->connectTimeout(5)
                ->post($this->baseUrl . '/chat/completions', $payload);
        } catch (\Exception $e) {
            Log::error('WaveSpeed API connection failed (stream)', ['error' => $e->getMessage()]);
            throw new WaveSpeedApiException('Connection failed: ' . $e->getMessage(), null, $e);
        }

        if (!$response->successful()) {
            Log::error('WaveSpeed API error (stream)', [
                'status' => $response->status(),
                'body' => mb_substr($response->body(), 0, 1000),
            ]);
            throw new WaveSpeedApiException(
                'WaveSpeed API returned HTTP ' . $response->status(),
                $response->status()
            );
        }

        return $response->toPsrResponse()->getBody();
    }
}
