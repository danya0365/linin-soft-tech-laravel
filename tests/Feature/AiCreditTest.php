<?php

namespace Tests\Feature;

use App\Models\AiChatSession;
use App\Models\AiCreditTransaction;
use App\Models\User;
use App\Services\AiCreditService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * ทดสอบระบบเครดิต AI Chat
 *
 * ใช้ DatabaseTransactions (rollback หลังเทสต์) — ห้ามใช้ RefreshDatabase
 * ต้อง migrate ก่อนรัน (users.ai_credit_balance + ai_credit_transactions)
 */
class AiCreditTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.wavespeed.api_key' => 'test-key',
            'ai-chat.usd_to_thb' => 37,
            'ai-chat.commission_percent' => 30,
        ]);
    }

    protected function staffUser(float $credit = 0): User
    {
        $user = User::factory()->create(['is_can_access_supervisor' => true]);
        $user->ai_credit_balance = $credit;
        $user->save();

        return $user;
    }

    public function test_stream_returns_402_when_no_credit(): void
    {
        $user = $this->staffUser(0);
        $session = AiChatSession::create(['user_id' => $user->id, 'model' => 'minimax/minimax-m2.7']);

        $this->actingAs($user)
            ->postJson("/api/ai-chat/sessions/{$session->id}/stream", ['content' => 'สวัสดี'])
            ->assertStatus(402);

        // ไม่บันทึกข้อความ user เมื่อโดนบล็อกที่ pre-flight
        $this->assertSame(0, $session->messages()->count());
    }

    public function test_stream_passes_preflight_when_has_credit(): void
    {
        $user = $this->staffUser(100);
        $session = AiChatSession::create(['user_id' => $user->id, 'model' => 'minimax/minimax-m2.7']);

        // ไม่ fake LLM — แค่ยืนยันว่าไม่โดน 402 (response เป็น stream 200)
        \Illuminate\Support\Facades\Http::fake([
            'llm.wavespeed.ai/*' => \Illuminate\Support\Facades\Http::response(
                "data: {\"choices\":[{\"delta\":{\"content\":\"ok\"}}]}\n\ndata: [DONE]\n\n",
                200
            ),
        ]);

        $response = $this->actingAs($user)
            ->postJson("/api/ai-chat/sessions/{$session->id}/stream", ['content' => 'สวัสดี']);

        $response->assertStatus(200);
    }

    public function test_charge_creates_ledger_and_decrements_balance(): void
    {
        $user = $this->staffUser(100);
        $session = AiChatSession::create(['user_id' => $user->id, 'model' => 'minimax/minimax-m2.7']);
        $message = $session->messages()->create([
            'role' => 'assistant',
            'content' => 'คำตอบ',
            'model' => 'minimax/minimax-m2.7',
            'prompt_tokens' => 1_000_000,
            'completion_tokens' => 1_000_000,
        ]);

        $service = app(AiCreditService::class);
        $tx = $service->charge($message);

        // ต้นทุน 55.5 + ค่าคอม 30% = 72.15
        $this->assertSame('-72.1500', $tx->amount);
        $this->assertSame('55.5000', $tx->cost_thb);
        $this->assertSame('16.6500', $tx->commission_thb);
        $this->assertSame(AiCreditTransaction::TYPE_USAGE, $tx->type);
        $this->assertSame($message->id, $tx->ai_chat_message_id);
        $this->assertNull($tx->created_by);

        $this->assertSame(100 - 72.15, (float) $user->fresh()->ai_credit_balance);
    }

    public function test_balance_can_go_negative_on_final_charge(): void
    {
        $user = $this->staffUser(1);
        $session = AiChatSession::create(['user_id' => $user->id, 'model' => 'minimax/minimax-m2.7']);
        $message = $session->messages()->create([
            'role' => 'assistant',
            'content' => 'คำตอบยาว',
            'model' => 'minimax/minimax-m2.7',
            'prompt_tokens' => 1_000_000,
            'completion_tokens' => 1_000_000,
        ]);

        app(AiCreditService::class)->charge($message);

        $this->assertLessThan(0, (float) $user->fresh()->ai_credit_balance);
    }

    public function test_topup_and_adjust(): void
    {
        $admin = User::factory()->create(['is_can_access_admin' => true]);
        $user = $this->staffUser(0);

        $service = app(AiCreditService::class);

        $topup = $service->topUp($user->id, 100, $admin->id, 'เติมทดสอบ');
        $this->assertSame('100.0000', $topup->amount);
        $this->assertSame($admin->id, $topup->created_by);
        $this->assertSame(100.0, (float) $user->fresh()->ai_credit_balance);

        $adjust = $service->adjust($user->id, -30, $admin->id, 'แก้ยอด');
        $this->assertSame(AiCreditTransaction::TYPE_ADJUST, $adjust->type);
        $this->assertSame(70.0, (float) $user->fresh()->ai_credit_balance);

        $this->expectException(\InvalidArgumentException::class);
        $service->topUp($user->id, -5, $admin->id);
    }

    public function test_admin_can_topup_via_backoffice(): void
    {
        $admin = User::factory()->create(['is_can_access_admin' => true]);
        $user = $this->staffUser(0);

        $this->actingAs($admin)
            ->post("/ai-credits/{$user->id}/transactions", ['amount' => 50, 'note' => 'เติมจากหลังบ้าน'])
            ->assertRedirect(route('ai-credits.show', $user))
            ->assertSessionHas('success');

        $this->assertSame(50.0, (float) $user->fresh()->ai_credit_balance);
        $this->assertSame(1, $user->aiCreditTransactions()->count());
    }

    public function test_non_admin_cannot_access_credit_backoffice(): void
    {
        $user = $this->staffUser(0); // supervisor แต่ไม่ใช่ admin

        $this->actingAs($user)
            ->get('/ai-credits')
            ->assertRedirect('home');
    }

    public function test_zero_amount_fails_validation(): void
    {
        $admin = User::factory()->create(['is_can_access_admin' => true]);
        $user = $this->staffUser(0);

        $this->actingAs($admin)
            ->postJson("/ai-credits/{$user->id}/transactions", ['amount' => 0])
            ->assertStatus(422);
    }
}
