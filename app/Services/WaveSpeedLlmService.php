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
}
