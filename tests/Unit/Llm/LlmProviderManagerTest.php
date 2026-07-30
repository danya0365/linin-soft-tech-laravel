<?php

namespace Tests\Unit\Llm;

use App\Exceptions\LlmApiException;
use App\Services\Llm\LlmProviderManager;
use Tests\TestCase;

class LlmProviderManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'ai-chat.default_provider' => 'wavespeed',
            'ai-chat.default_model' => 'paid/model',
            'ai-chat.providers' => [
                'wavespeed' => [
                    'label' => 'WaveSpeed',
                    'api_key' => 'test-key',
                    'base_url' => 'https://llm.wavespeed.ai/v1',
                    'default_model' => 'paid/model',
                    'requires_key' => true,
                ],
                '9router' => [
                    'label' => '9Router',
                    'base_url' => 'https://router.example.test/v1',
                    'default_model' => 'free/model',
                    'requires_key' => false,
                ],
            ],
            'ai-chat.models' => [
                ['id' => 'paid/model', 'provider' => 'wavespeed', 'vendor' => 'X'],
                ['id' => 'free/model', 'provider' => '9router', 'vendor' => 'Y'],
            ],
        ]);
    }

    protected function manager(): LlmProviderManager
    {
        return new LlmProviderManager();
    }

    public function test_routes_model_to_its_provider(): void
    {
        $manager = $this->manager();

        $this->assertSame('wavespeed', $manager->forModel('paid/model')->name());
        $this->assertSame('9router', $manager->forModel('free/model')->name());
    }

    public function test_unknown_model_falls_back_to_default_provider(): void
    {
        $this->assertSame('wavespeed', $this->manager()->forModel('who/knows')->name());
        $this->assertSame('wavespeed', $this->manager()->forModel(null)->name());
    }

    public function test_unknown_provider_throws(): void
    {
        $this->expectException(LlmApiException::class);

        $this->manager()->forName('nope');
    }

    /** provider ที่ยังไม่ได้ตั้ง base_url = ยังไม่ได้ตั้งค่า → ปิด (ไม่ต้องมี flag แยก) */
    public function test_provider_without_base_url_is_not_available(): void
    {
        config(['ai-chat.providers.9router.base_url' => null]);

        $manager = $this->manager();

        $this->assertSame(['wavespeed'], $manager->enabledNames());
        $this->assertSame(['paid/model'], $manager->availableModelIds());
        $this->assertFalse($manager->isModelAvailable('free/model'));
    }

    /** ปิดชั่วคราวด้วย flag ได้ แม้ตั้งค่าอย่างอื่นครบ */
    public function test_provider_disabled_by_flag_is_not_available(): void
    {
        config(['ai-chat.providers.9router.enabled' => false]);

        $this->assertSame(['wavespeed'], $this->manager()->enabledNames());
    }

    public function test_provider_without_credentials_is_not_available(): void
    {
        config(['ai-chat.providers.wavespeed.api_key' => null]);

        $manager = $this->manager();

        $this->assertSame(['9router'], $manager->enabledNames());
        $this->assertSame(['free/model'], $manager->availableModelIds());
    }

    public function test_resolve_model_prefers_valid_input_then_config_default(): void
    {
        $manager = $this->manager();

        $this->assertSame('free/model', $manager->resolveModel('free/model'));
        $this->assertSame('paid/model', $manager->resolveModel('who/knows'));
        $this->assertSame('paid/model', $manager->resolveModel(null));
    }

    /** default model ผูกกับ provider ที่ยังไม่ได้ตั้งค่า → ต้องตกไป model แรกที่ยังใช้ได้ */
    public function test_resolve_model_falls_back_when_default_provider_is_unavailable(): void
    {
        config(['ai-chat.providers.wavespeed.api_key' => null]);

        $this->assertSame('free/model', $this->manager()->resolveModel(null));
    }

    public function test_any_enabled_is_false_when_no_provider_is_configured(): void
    {
        config([
            'ai-chat.providers.wavespeed.api_key' => null,
            'ai-chat.providers.9router.base_url' => null,
        ]);

        $this->assertFalse($this->manager()->anyEnabled());
    }

    public function test_enabled_for_client_exposes_labels(): void
    {
        $this->assertSame([
            ['name' => 'wavespeed', 'label' => 'WaveSpeed'],
            ['name' => '9router', 'label' => '9Router'],
        ], $this->manager()->enabledForClient());
    }
}
