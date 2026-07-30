<?php

namespace App\Contracts;

use App\Exceptions\LlmApiException;
use Psr\Http\Message\StreamInterface;

/**
 * Port สำหรับผู้ให้บริการ LLM
 *
 * ทุก adapter ต้องคืนข้อมูลในรูปแบบ OpenAI chat completions เสมอ
 * (choices[].message / choices[].delta / usage) เพื่อให้ business logic
 * ใน AiChatService และ AiChatStreamService ไม่ต้องรู้จักเจ้าไหนเป็นพิเศษ
 */
interface LlmProvider
{
    /**
     * ชื่อ provider ตาม key ใน config('ai-chat.providers')
     */
    public function name(): string;

    /**
     * ป้ายชื่อสำหรับแสดงผลฝั่ง UI
     */
    public function label(): string;

    /**
     * พร้อมใช้งานหรือไม่ (feature flag — ตั้งค่าครบ และไม่ได้ถูกปิดไว้)
     */
    public function isEnabled(): bool;

    /**
     * model เริ่มต้นของ provider นี้
     */
    public function defaultModel(): string;

    /**
     * เรียก POST /chat/completions แบบไม่ stream
     *
     * @param array $messages OpenAI-style messages
     * @param array $tools OpenAI-style tool definitions (ว่าง = ไม่ส่ง tools)
     * @param string|null $model override model (null = ใช้ default ของ provider)
     * @param int|null $maxTokens override max_tokens (null = ใช้ default ของ provider)
     * @return array decoded response body แบบ OpenAI
     * @throws LlmApiException เมื่อ non-2xx, connection ล้มเหลว หรือ body ไม่ใช่ JSON
     */
    public function chatCompletion(
        array $messages,
        array $tools = [],
        ?string $model = null,
        ?int $maxTokens = null
    ): array;

    /**
     * เรียก POST /chat/completions แบบ streaming (SSE)
     *
     * คืน PSR-7 body stream ของ upstream
     * (รูปแบบ chunk: data: {"choices":[{"delta":{"content":"..."}}]} ... data: [DONE])
     *
     * @throws LlmApiException เมื่อ non-2xx หรือ connection ล้มเหลว
     */
    public function streamChatCompletion(
        array $messages,
        ?string $model = null,
        ?int $maxTokens = null,
        array $tools = []
    ): StreamInterface;
}
