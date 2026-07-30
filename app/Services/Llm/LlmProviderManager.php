<?php

namespace App\Services\Llm;

use App\Contracts\LlmProvider;
use App\Exceptions\LlmApiException;

/**
 * Registry ของ LLM provider
 *
 * หน้าที่หลักคือ route จาก model id → provider ที่รับผิดชอบ model นั้น
 * (แต่ละ entry ใน config('ai-chat.models') ระบุ key 'provider' ไว้)
 */
class LlmProviderManager
{
    /** @var array<string, LlmProvider> */
    protected array $resolved = [];

    /**
     * @throws LlmApiException เมื่อไม่รู้จัก provider ชื่อนี้
     */
    public function forName(string $name): LlmProvider
    {
        if (isset($this->resolved[$name])) {
            return $this->resolved[$name];
        }

        $config = config("ai-chat.providers.{$name}");

        if (!is_array($config)) {
            throw new LlmApiException("Unknown LLM provider [{$name}]");
        }

        return $this->resolved[$name] = $this->make($name, $config);
    }

    /**
     * provider ที่รับผิดชอบ model นี้ — model ที่ไม่รู้จักตกไปที่ provider เริ่มต้น
     */
    public function forModel(?string $model): LlmProvider
    {
        $name = $this->providerNameForModel($model);

        return $name === null ? $this->default() : $this->forName($name);
    }

    public function default(): LlmProvider
    {
        return $this->forName((string) config('ai-chat.default_provider', 'wavespeed'));
    }

    /**
     * ชื่อ provider ของ model ตาม catalog (null = ไม่อยู่ใน catalog หรือไม่ได้ระบุไว้)
     */
    public function providerNameForModel(?string $model): ?string
    {
        if ($model === null || $model === '') {
            return null;
        }

        foreach ((array) config('ai-chat.models', []) as $entry) {
            if (($entry['id'] ?? null) === $model) {
                return $entry['provider'] ?? null;
            }
        }

        return null;
    }

    /**
     * มี provider ที่ใช้งานได้อย่างน้อยหนึ่งเจ้าไหม (feature flag ของหน้าแชท)
     */
    public function anyEnabled(): bool
    {
        return $this->enabledNames() !== [];
    }

    /**
     * @return array<int, string> ชื่อ provider ที่พร้อมใช้งาน
     */
    public function enabledNames(): array
    {
        $names = [];

        foreach (array_keys((array) config('ai-chat.providers', [])) as $name) {
            if ($this->forName($name)->isEnabled()) {
                $names[] = $name;
            }
        }

        return $names;
    }

    /**
     * model จาก catalog ที่ provider ของมันเปิดใช้งานอยู่
     *
     * ใช้ทั้ง render dropdown และ validate ฝั่ง server — environment ที่ยังไม่ได้ตั้งค่า
     * provider เจ้าไหน จะไม่เห็นและยิง model ของเจ้านั้นไม่ได้
     *
     * @return array<int, array>
     */
    public function availableModels(): array
    {
        $enabled = $this->enabledNames();
        $default = (string) config('ai-chat.default_provider', 'wavespeed');

        return array_values(array_filter(
            (array) config('ai-chat.models', []),
            fn ($entry) => in_array($entry['provider'] ?? $default, $enabled, true)
        ));
    }

    /**
     * @return array<int, string>
     */
    public function availableModelIds(): array
    {
        return array_column($this->availableModels(), 'id');
    }

    public function isModelAvailable(?string $model): bool
    {
        return $model !== null && in_array($model, $this->availableModelIds(), true);
    }

    /**
     * model ที่ใช้ได้จริง — ตกไป default ของ config หรือ model แรกที่เหลืออยู่
     * (กันกรณี default model ผูกกับ provider ที่ปิดไว้)
     */
    public function resolveModel(?string $model = null): string
    {
        if ($this->isModelAvailable($model)) {
            return $model;
        }

        $default = (string) config('ai-chat.default_model', '');
        if ($this->isModelAvailable($default)) {
            return $default;
        }

        return (string) (($this->availableModelIds()[0] ?? null) ?? $default);
    }

    /**
     * ข้อมูล provider ที่เปิดอยู่สำหรับส่งไปฝั่ง client (จัดกลุ่ม dropdown)
     *
     * @return array<int, array{name: string, label: string}>
     */
    public function enabledForClient(): array
    {
        return array_map(fn ($name) => [
            'name' => $name,
            'label' => $this->forName($name)->label(),
        ], $this->enabledNames());
    }

    /**
     * ทดสอบ: ยัด provider สำเร็จรูปเข้า registry
     */
    public function register(string $name, LlmProvider $provider): void
    {
        $this->resolved[$name] = $provider;
    }

    protected function make(string $name, array $config): LlmProvider
    {
        $driver = $config['driver'] ?? 'openai-compatible';

        return match ($driver) {
            'openai-compatible' => new OpenAiCompatibleProvider(['name' => $name] + $config),
            default => throw new LlmApiException("Unsupported LLM driver [{$driver}] for provider [{$name}]"),
        };
    }
}
