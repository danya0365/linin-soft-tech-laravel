<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception สำหรับความผิดพลาดจากการเรียก WaveSpeed LLM API
 */
class WaveSpeedApiException extends Exception
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
