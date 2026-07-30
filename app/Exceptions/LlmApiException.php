<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception สำหรับความผิดพลาดจากการเรียก LLM API (ทุก provider)
 *
 * getHttpStatus() === 400 ถูกใช้เป็นสัญญาณว่า model ไม่รองรับ tool calling
 * — ดู AiChatService::answer() และ AiChatStreamService::runStreamingToolLoop()
 */
class LlmApiException extends Exception
{
    protected ?int $httpStatus;

    public function __construct(string $message, ?int $httpStatus = null, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->httpStatus = $httpStatus;
    }

    public function getHttpStatus(): ?int
    {
        return $this->httpStatus;
    }
}
