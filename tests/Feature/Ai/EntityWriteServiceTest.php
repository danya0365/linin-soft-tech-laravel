<?php

namespace Tests\Feature\Ai;

use App\Models\AiChatSession;
use App\Models\AiWriteAudit;
use App\Models\AiWriteDraft;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\User;
use App\Models\WashingMachine;
use App\Services\EntityWriteService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * ทดสอบ EntityWriteService — ให้ AI สร้าง master data ผ่านแชท (insert)
 *
 * ใช้ DatabaseTransactions (rollback หลังเทสต์) — ต้อง migrate ก่อนรัน
 */
class EntityWriteServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected EntityWriteService $writer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->writer = app(EntityWriteService::class);
    }

    protected function admin(): User
    {
        return User::factory()->create(['is_can_access_admin' => true]);
    }

    protected function makeSession(User $user): AiChatSession
    {
        return AiChatSession::create(['user_id' => $user->id, 'model' => 'minimax/minimax-m2.7']);
    }

    /** จำลองผู้ใช้พิมพ์ข้อความ (สร้าง user message ใหม่ → ผ่านด่านกัน auto-confirm) */
    protected function userSays(AiChatSession $session, string $text = 'ยืนยัน'): void
    {
        $session->messages()->create(['role' => 'user', 'content' => $text]);
    }

    public function test_prepare_creates_pending_draft_without_inserting(): void
    {
        $admin = $this->admin();
        $session = $this->makeSession($admin);
        $group = CustomerGroup::create(['name' => 'โรงแรม']);

        $before = Customer::count();

        $result = $this->writer->prepare($admin, $session, 'customer', [
            'name' => 'โรงแรม ABC',
            'customer_group_id' => $group->id,
        ]);

        $this->assertStringContainsString('พร้อมสร้างลูกค้า', $result);
        $this->assertSame($before, Customer::count(), 'ยังไม่ควร insert ตอน prepare');
        $this->assertDatabaseHas('ai_write_drafts', [
            'entity_key' => 'customer',
            'status' => 'pending',
            'user_id' => $admin->id,
        ]);
    }

    public function test_confirm_same_turn_is_rejected(): void
    {
        $admin = $this->admin();
        $session = $this->makeSession($admin);
        $group = CustomerGroup::create(['name' => 'โรงแรม']);

        $this->writer->prepare($admin, $session, 'customer', [
            'name' => 'โรงแรม ABC',
            'customer_group_id' => $group->id,
        ]);
        $draft = AiWriteDraft::latest('id')->first();

        // ยังไม่มี user message ใหม่ → ต้องปฏิเสธ
        $before = Customer::count();
        $result = $this->writer->confirm($admin, $session, $draft->id);

        $this->assertStringContainsString('ยังไม่ได้รับการยืนยัน', $result);
        $this->assertSame($before, Customer::count());
        $this->assertSame('pending', $draft->fresh()->status);
    }

    public function test_confirm_after_user_message_inserts_and_audits(): void
    {
        $admin = $this->admin();
        $session = $this->makeSession($admin);
        $group = CustomerGroup::create(['name' => 'โรงแรม']);

        $this->writer->prepare($admin, $session, 'customer', [
            'name' => 'โรงแรม ABC',
            'customer_group_id' => $group->id,
        ]);
        $draft = AiWriteDraft::latest('id')->first();

        $this->userSays($session); // ผู้ใช้ยืนยัน
        $result = $this->writer->confirm($admin, $session, $draft->id);

        $this->assertStringContainsString('เรียบร้อย', $result);
        $this->assertDatabaseHas('customers', ['name' => 'โรงแรม ABC', 'customer_group_id' => $group->id]);
        $this->assertSame('confirmed', $draft->fresh()->status);
        $this->assertDatabaseHas('ai_write_audits', [
            'entity_key' => 'customer',
            'user_id' => $admin->id,
            'record_id' => $draft->fresh()->record_id,
        ]);
    }

    public function test_confirm_twice_does_not_duplicate(): void
    {
        $admin = $this->admin();
        $session = $this->makeSession($admin);
        $group = CustomerGroup::create(['name' => 'โรงแรม']);

        $this->writer->prepare($admin, $session, 'customer', [
            'name' => 'โรงแรม ABC',
            'customer_group_id' => $group->id,
        ]);
        $draft = AiWriteDraft::latest('id')->first();
        $this->userSays($session);
        $this->writer->confirm($admin, $session, $draft->id);

        $countAfterFirst = Customer::where('name', 'โรงแรม ABC')->count();
        $result = $this->writer->confirm($admin, $session, $draft->id);

        $this->assertStringContainsString('ถูกใช้ไปแล้ว', $result);
        $this->assertSame($countAfterFirst, Customer::where('name', 'โรงแรม ABC')->count());
    }

    public function test_non_admin_is_denied(): void
    {
        $supervisor = User::factory()->create(['is_can_access_supervisor' => true]);
        $session = $this->makeSession($supervisor);
        $group = CustomerGroup::create(['name' => 'โรงแรม']);

        $result = $this->writer->prepare($supervisor, $session, 'customer', [
            'name' => 'โรงแรม ABC',
            'customer_group_id' => $group->id,
        ]);

        $this->assertStringContainsString('ไม่มีสิทธิ์', $result);
        $this->assertSame(0, AiWriteDraft::count());
    }

    public function test_missing_required_field_is_rejected(): void
    {
        $admin = $this->admin();
        $session = $this->makeSession($admin);

        $result = $this->writer->prepare($admin, $session, 'customer', [
            'name' => 'โรงแรม ABC',
        ]);

        $this->assertStringContainsString('ไม่ครบ', $result);
        $this->assertSame(0, AiWriteDraft::count());
    }

    public function test_nonexistent_fk_is_rejected(): void
    {
        $admin = $this->admin();
        $session = $this->makeSession($admin);

        $result = $this->writer->prepare($admin, $session, 'customer', [
            'name' => 'โรงแรม ABC',
            'customer_group_id' => 999999,
        ]);

        $this->assertStringContainsString('ไม่พบ', $result);
        $this->assertSame(0, AiWriteDraft::count());
    }

    public function test_machine_without_photo_succeeds_with_blank_photo(): void
    {
        $admin = $this->admin();
        $session = $this->makeSession($admin);

        $result = $this->writer->prepare($admin, $session, 'washing_machine', [
            'name' => 'เครื่องซัก A1',
            'maximum_weight' => 50,
        ]);
        $this->assertStringContainsString('พร้อมสร้างเครื่องซัก', $result);
        $this->assertStringNotContainsString('photo', $result, 'preview ไม่ควรโชว์ photo');

        $draft = AiWriteDraft::latest('id')->first();
        $this->userSays($session);
        $this->writer->confirm($admin, $session, $draft->id);

        $machine = WashingMachine::where('name', 'เครื่องซัก A1')->first();
        $this->assertNotNull($machine);
        $this->assertSame('', $machine->photo);
    }

    public function test_expired_draft_is_rejected(): void
    {
        $admin = $this->admin();
        $session = $this->makeSession($admin);
        $group = CustomerGroup::create(['name' => 'โรงแรม']);

        $this->writer->prepare($admin, $session, 'customer', [
            'name' => 'โรงแรม ABC',
            'customer_group_id' => $group->id,
        ]);
        $draft = AiWriteDraft::latest('id')->first();
        $draft->update(['expires_at' => now()->subMinute()]);
        $this->userSays($session);

        $result = $this->writer->confirm($admin, $session, $draft->id);

        $this->assertStringContainsString('หมดอายุ', $result);
        $this->assertSame('expired', $draft->fresh()->status);
        $this->assertDatabaseMissing('customers', ['name' => 'โรงแรม ABC']);
    }
}
