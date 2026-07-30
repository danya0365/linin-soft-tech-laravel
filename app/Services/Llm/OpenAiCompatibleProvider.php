<?php

namespace App\Services\Llm;

use App\Contracts\LlmProvider;
use App\Exceptions\LlmApiException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\StreamInterface;

/**
 * Adapter สำหรับ LLM endpoint ที่พูดภาษา OpenAI chat completions
 *
 * ครอบคลุมทั้ง WaveSpeed, gateway ในเครื่อง และเจ้าอื่นที่ compatible กัน
 * ต่างกันแค่ config array ที่ฉีดเข้ามา — เพิ่ม provider ใหม่ไม่ต้องเขียนคลาสใหม่
 * ดู config('ai-chat.providers')
 */
class OpenAiCompatibleProvider implements LlmProvider
{
    public function __construct(protected array $config)
    {
    }

    public function name(): string
    {
        return (string) ($this->config['name'] ?? 'unknown');
    }

    public function label(): string
    {
        return (string) ($this->config['label'] ?? $this->name());
    }

    public function defaultModel(): string
    {
        return (string) ($this->config['default_model'] ?? '');
    }

    /**
     * ปิดด้วย flag ได้ (prod ปิด provider ที่ใช้เฉพาะ dev)
     * provider ที่ requires_key = false เช่น endpoint ในเครื่อง ไม่ต้องมี key ก็ถือว่าพร้อม
     */
    public function isEnabled(): bool
    {
        if (($this->config['enabled'] ?? true) === false) {
            return false;
        }

        if (empty($this->config['base_url'])) {
            return false;
        }

        if (($this->config['requires_key'] ?? true) === false) {
            return true;
        }

        return !empty($this->config['api_key']);
    }

    public function chatCompletion(
        array $messages,
        array $tools = [],
        ?string $model = null,
        ?int $maxTokens = null
    ): array {
        $this->guardEnabled();

        $model = $model ?: $this->defaultModel();

        $payload = [
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => $maxTokens ?? $this->maxTokens(),
            'temperature' => 0.2,
        ];

        if (!empty($tools)) {
            $payload['tools'] = $tools;
            $payload['tool_choice'] = 'auto';
        }

        $startedAt = microtime(true);

        try {
            $response = $this->request()
                ->timeout($this->timeout())
                ->connectTimeout(5)
                ->post($this->endpoint(), $payload);
        } catch (\Exception $e) {
            $this->logError('connection failed', ['error' => $e->getMessage()]);
            throw new LlmApiException('Connection failed: ' . $e->getMessage(), null, $e);
        }

        $latencyMs = (int) round((microtime(true) - $startedAt) * 1000);

        $this->guardSuccessful($response, ['latency_ms' => $latencyMs]);

        $body = $this->decodeBody($response);

        Log::info('LLM API call', [
            'provider' => $this->name(),
            'model' => $model,
            'latency_ms' => $latencyMs,
            'usage' => $body['usage'] ?? null,
        ]);

        return $body;
    }

    public function streamChatCompletion(
        array $messages,
        ?string $model = null,
        ?int $maxTokens = null,
        array $tools = []
    ): StreamInterface {
        $this->guardEnabled();

        $payload = [
            'model' => $model ?: $this->defaultModel(),
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
            $response = $this->request()
                ->withHeaders(['Accept' => 'text/event-stream'])
                ->withOptions(['stream' => true])
                // streaming ใช้เวลานานกว่า request ปกติ — ไม่ใช้ timeout สั้นของ tool loop
                ->timeout(max($this->timeout(), 120))
                ->connectTimeout(5)
                ->post($this->endpoint(), $payload);
        } catch (\Exception $e) {
            $this->logError('connection failed (stream)', ['error' => $e->getMessage()]);
            throw new LlmApiException('Connection failed: ' . $e->getMessage(), null, $e);
        }

        $this->guardSuccessful($response, ['stream' => true]);

        return $response->toPsrResponse()->getBody();
    }

    // ── internals ─────────────────────────────────────────────────────────

    protected function endpoint(): string
    {
        return rtrim((string) $this->config['base_url'], '/') . '/chat/completions';
    }

    protected function timeout(): int
    {
        return (int) ($this->config['timeout'] ?? 25);
    }

    protected function maxTokens(): int
    {
        return (int) ($this->config['max_tokens'] ?? 1024);
    }

    /**
     * ส่ง Authorization เฉพาะเมื่อมี key — gateway ในเครื่องบางตัวปฏิเสธ bearer ว่าง
     */
    protected function request(): \Illuminate\Http\Client\PendingRequest
    {
        $key = $this->config['api_key'] ?? null;

        return empty($key) ? Http::asJson() : Http::withToken($key);
    }

    protected function guardEnabled(): void
    {
        if (!$this->isEnabled()) {
            throw new LlmApiException("LLM provider [{$this->name()}] is not configured or disabled");
        }
    }

    protected function guardSuccessful(Response $response, array $context = []): void
    {
        if ($response->successful()) {
            return;
        }

        $this->logError('http error', $context + [
            'status' => $response->status(),
            'body' => mb_substr($response->body(), 0, 1000),
        ]);

        throw new LlmApiException(
            "LLM provider [{$this->name()}] returned HTTP " . $response->status(),
            $response->status()
        );
    }

    /**
     * Decode body แบบทนต่อขยะท้าย response
     *
     * บาง gateway (เช่น endpoint OpenAI-compatible ในเครื่อง) ต่อ SSE sentinel
     * `data: [DONE]` ท้าย body ของ response ที่ไม่ได้ stream ทำให้ JSON ไม่ valid
     * และ $response->json() คืน null — ตัดส่วนเกินทิ้งก่อนแล้วค่อย decode ใหม่
     */
    protected function decodeBody(Response $response): array
    {
        $raw = trim($response->body());

        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        $trimmed = $this->stripTrailingSseSentinel($raw);
        if ($trimmed !== null) {
            $decoded = json_decode($trimmed, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        $this->logError('malformed JSON body', [
            'body' => mb_substr($raw, 0, 1000),
            'json_error' => json_last_error_msg(),
        ]);

        throw new LlmApiException(
            "LLM provider [{$this->name()}] returned a malformed JSON body"
        );
    }

    /**
     * คืน JSON object ตัวแรกของ string โดยนับวงเล็บปีกกานอก string literal
     * (null = หา object ที่สมบูรณ์ไม่เจอ)
     */
    protected function stripTrailingSseSentinel(string $raw): ?string
    {
        $start = strpos($raw, '{');
        if ($start === false) {
            return null;
        }

        $depth = 0;
        $inString = false;
        $escaped = false;
        $length = strlen($raw);

        for ($i = $start; $i < $length; $i++) {
            $char = $raw[$i];

            if ($inString) {
                if ($escaped) {
                    $escaped = false;
                } elseif ($char === '\\') {
                    $escaped = true;
                } elseif ($char === '"') {
                    $inString = false;
                }
                continue;
            }

            if ($char === '"') {
                $inString = true;
            } elseif ($char === '{') {
                $depth++;
            } elseif ($char === '}') {
                $depth--;
                if ($depth === 0) {
                    return substr($raw, $start, $i - $start + 1);
                }
            }
        }

        return null;
    }

    protected function logError(string $message, array $context = []): void
    {
        Log::error('LLM API ' . $message, ['provider' => $this->name()] + $context);
    }
}
