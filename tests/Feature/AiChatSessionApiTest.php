<?php

namespace Tests\Feature;

use App\Models\AiChatSession;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * ทดสอบ scoping + CRUD ของ AI Chat sessions API
 *
 * หมายเหตุ: ใช้ DatabaseTransactions (rollback หลังเทสต์) เพราะ phpunit.xml
 * ชี้ MySQL จริง — ห้ามใช้ RefreshDatabase เด็ดขาด (จะ wipe dev DB)
 * ต้องรัน migrate ก่อน (ตาราง ai_chat_sessions / ai_chat_messages ต้องมีแล้ว)
 */
class AiChatSessionApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function staffUser(): User
    {
        return User::factory()->create(['is_can_access_supervisor' => true]);
    }

    public function test_worker_only_user_cannot_access_sessions(): void
    {
        $worker = User::factory()->create([
            'is_can_access_worker' => true,
            'is_can_access_supervisor' => false,
            'is_can_access_manager' => false,
            'is_can_access_admin' => false,
        ]);

        $this->actingAs($worker)
            ->getJson('/api/ai-chat/sessions')
            ->assertStatus(403);
    }

    public function test_guest_gets_401(): void
    {
        $this->getJson('/api/ai-chat/sessions')->assertStatus(401);
    }

    public function test_session_crud_round_trip(): void
    {
        $user = $this->staffUser();

        // create
        $created = $this->actingAs($user)
            ->postJson('/api/ai-chat/sessions', ['model' => 'minimax/minimax-m2.7'])
            ->assertStatus(201)
            ->json('session');

        $this->assertSame('แชทใหม่', $created['title']);

        // list
        $sessions = $this->actingAs($user)
            ->getJson('/api/ai-chat/sessions')
            ->assertOk()
            ->json('sessions');

        $this->assertContains($created['id'], array_column($sessions, 'id'));

        // rename
        $this->actingAs($user)
            ->patchJson('/api/ai-chat/sessions/' . $created['id'], ['title' => 'คุยเรื่องยอดขาย'])
            ->assertOk()
            ->assertJsonPath('session.title', 'คุยเรื่องยอดขาย');

        // unknown model rejected
        $this->actingAs($user)
            ->patchJson('/api/ai-chat/sessions/' . $created['id'], ['model' => 'evil/unknown-model'])
            ->assertStatus(422);

        // show
        $this->actingAs($user)
            ->getJson('/api/ai-chat/sessions/' . $created['id'])
            ->assertOk()
            ->assertJsonPath('session.id', $created['id'])
            ->assertJsonPath('messages', []);

        // delete (soft)
        $this->actingAs($user)
            ->deleteJson('/api/ai-chat/sessions/' . $created['id'])
            ->assertOk();

        $this->actingAs($user)
            ->getJson('/api/ai-chat/sessions/' . $created['id'])
            ->assertStatus(404);
    }

    public function test_user_cannot_access_other_users_session(): void
    {
        $owner = $this->staffUser();
        $intruder = $this->staffUser();

        $session = AiChatSession::create([
            'user_id' => $owner->id,
            'title' => 'ของคนอื่น',
            'model' => 'minimax/minimax-m2.7',
        ]);

        $this->actingAs($intruder)
            ->getJson('/api/ai-chat/sessions/' . $session->id)
            ->assertStatus(404);

        $this->actingAs($intruder)
            ->patchJson('/api/ai-chat/sessions/' . $session->id, ['title' => 'ยึด'])
            ->assertStatus(404);

        $this->actingAs($intruder)
            ->deleteJson('/api/ai-chat/sessions/' . $session->id)
            ->assertStatus(404);

        $this->actingAs($intruder)
            ->postJson('/api/ai-chat/sessions/' . $session->id . '/stream', ['content' => 'แอบถาม'])
            ->assertStatus(404);

        // เจ้าของยังเข้าได้ปกติ
        $this->actingAs($owner)
            ->getJson('/api/ai-chat/sessions/' . $session->id)
            ->assertOk();
    }

    public function test_stats_endpoint_shape(): void
    {
        $user = $this->staffUser();

        $this->actingAs($user)
            ->getJson('/api/ai-chat/stats')
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'today' => ['calls', 'prompt', 'completion'],
                'all' => ['calls', 'prompt', 'completion'],
                'byModel',
            ]);
    }
}
