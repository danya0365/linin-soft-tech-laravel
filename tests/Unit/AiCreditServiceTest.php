<?php

namespace Tests\Unit;

use App\Services\AiCreditService;
use Tests\TestCase;

class AiCreditServiceTest extends TestCase
{
    protected AiCreditService $service;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'ai-chat.usd_to_thb' => 37,
            'ai-chat.commission_percent' => 30,
            'ai-chat.fallback_pricing' => ['inputPerMTok' => 5, 'outputPerMTok' => 25],
        ]);

        $this->service = new AiCreditService();
    }

    public function test_cost_for_known_model(): void
    {
        // minimax/minimax-m2.7: input 0.3, output 1.2 USD/MTok
        // 1M + 1M tokens → USD 1.5 → THB 55.5
        $this->assertSame(1.5, $this->service->costUsd('minimax/minimax-m2.7', 1_000_000, 1_000_000));
        $this->assertSame(55.5, $this->service->costThb('minimax/minimax-m2.7', 1_000_000, 1_000_000));
    }

    public function test_charge_adds_commission(): void
    {
        // ต้นทุน 55.5 บาท + ค่าคอม 30% = 72.15
        $charge = $this->service->chargeThb('minimax/minimax-m2.7', 1_000_000, 1_000_000);

        $this->assertSame(72.15, $charge);
        $this->assertSame(
            16.65,
            round($charge - $this->service->costThb('minimax/minimax-m2.7', 1_000_000, 1_000_000), 4)
        );
    }

    public function test_commission_percent_is_configurable(): void
    {
        config(['ai-chat.commission_percent' => 0]);

        $this->assertSame(
            $this->service->costThb('minimax/minimax-m2.7', 1_000_000, 1_000_000),
            $this->service->chargeThb('minimax/minimax-m2.7', 1_000_000, 1_000_000)
        );
    }

    public function test_unknown_model_uses_fallback_pricing_never_free(): void
    {
        // fallback: 5 + 25 USD/MTok → 1M+1M = USD 30 → THB 1110 → +30% = 1443
        $this->assertSame(30.0, $this->service->costUsd('evil/unknown', 1_000_000, 1_000_000));
        $this->assertSame(1443.0, $this->service->chargeThb('evil/unknown', 1_000_000, 1_000_000));
        $this->assertGreaterThan(0, $this->service->chargeThb(null, 1000, 1000));
    }

    public function test_rounds_to_4_decimals(): void
    {
        // โทเค็นน้อยๆ ต้อง round 4 ตำแหน่ง ไม่ใช่เลขทศนิยมยาว
        $charge = $this->service->chargeThb('minimax/minimax-m2.7', 123, 45);

        $this->assertSame(round($charge, 4), $charge);
    }

    public function test_zero_tokens_costs_zero(): void
    {
        $this->assertSame(0.0, $this->service->chargeThb('minimax/minimax-m2.7', 0, 0));
    }

    public function test_cached_tokens_billed_at_cache_read_rate(): void
    {
        // minimax/minimax-m2.7: input 0.3, cached 0.06 USD/MTok
        // ทั้งหมด cache hit → 1M × 0.06 = 0.06 (ถูกกว่า 0.3 เต็ม 5 เท่า)
        $this->assertSame(0.06, $this->service->costUsd('minimax/minimax-m2.7', 1_000_000, 0, 1_000_000));

        $this->assertLessThan(
            $this->service->costUsd('minimax/minimax-m2.7', 1_000_000, 0, 0),
            $this->service->costUsd('minimax/minimax-m2.7', 1_000_000, 0, 1_000_000)
        );
    }

    public function test_model_without_cache_price_never_undercharges(): void
    {
        // fallback (ไม่กำหนด cachedInputPerMTok) → cache hit คิดราคา input เต็ม
        $this->assertSame(
            $this->service->costUsd('evil/unknown', 1000, 0, 0),
            $this->service->costUsd('evil/unknown', 1000, 0, 800)
        );
    }

    public function test_cached_tokens_clamped_to_prompt(): void
    {
        // cached เกิน prompt (estimate เพี้ยน) ต้องไม่ทำให้ fresh ติดลบ
        $this->assertSame(
            $this->service->costUsd('minimax/minimax-m2.7', 1000, 0, 1000),
            $this->service->costUsd('minimax/minimax-m2.7', 1000, 0, 5000)
        );
    }
}
