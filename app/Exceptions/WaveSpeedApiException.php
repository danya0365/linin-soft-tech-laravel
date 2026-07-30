<?php

namespace App\Exceptions;

/**
 * @deprecated ใช้ LlmApiException แทน — คงไว้เพื่อ backward compatibility
 *             หลังแยก AI chat ออกเป็นระบบ provider แบบ pluggable
 */
class WaveSpeedApiException extends LlmApiException
{
}
