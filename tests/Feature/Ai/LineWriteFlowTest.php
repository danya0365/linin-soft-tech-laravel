<?php

namespace Tests\Feature\Ai;

use App\Models\AiChatSession;
use App\Models\AiWriteDraft;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\User;
use App\Contracts\LlmProvider;
use App\Services\ChatService;
use App\Services\Llm\LlmProviderManager;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Psr\Http\Message\StreamInterface;
use Tests\TestCase;

/**
 * ทดสอบ write flow บน LINE (ผ่าน ChatService::processCommand พร้อม User)
 * ใช้ LlmProvider ปลอม ขับ tool_call เอง — ไม่ต้องเรียก API จริง
 * พิสูจน์: LINE มี session(channel=line) + persist message + ด่านกัน auto-confirm + confirm ข้ามเทิร์น
 */
class LineWriteFlowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_line_create_customer_preview_then_confirm(): void
    {
        $admin = User::factory()->create(['is_can_access_admin' => true]);
        $group = CustomerGroup::create(['name' => 'โรงแรม']);

        // bind LLM ปลอมที่ขับ prepare_create_customer → (เทิร์น 2) confirm_write
        $this->fakeLlm(new FakeWriteFlowLlm($group->id));

        $chat = app(ChatService::class);

        // เทิร์น 1: ผู้ใช้สั่งสร้าง → ได้ preview ยังไม่บันทึก
        $before = Customer::count();
        $r1 = $chat->processCommand('สร้างลูกค้าใหม่ชื่อ โรงแรม LINE กลุ่มโรงแรม', $admin);

        $this->assertSame($before, Customer::count(), 'เทิร์น preview ยังไม่ควรสร้าง');
        $session = AiChatSession::where('user_id', $admin->id)->where('channel', 'line')->first();
        $this->assertNotNull($session, 'ต้องมี session channel=line');
        $this->assertDatabaseHas('ai_write_drafts', ['ai_chat_session_id' => $session->id, 'status' => 'pending']);
        // มีข้อความ user+assistant ถูก persist
        $this->assertSame(2, $session->messages()->count());

        // เทิร์น 2: ผู้ใช้พิมพ์ "ยืนยัน" → confirm (draft_id ไม่ระบุ → ใช้ตัวล่าสุด)
        $r2 = $chat->processCommand('ยืนยัน', $admin);

        $this->assertDatabaseHas('customers', ['name' => 'โรงแรม LINE', 'customer_group_id' => $group->id]);
        $this->assertSame('confirmed', AiWriteDraft::latest('id')->first()->status);
    }

    public function test_line_confirm_blocked_without_user_turn(): void
    {
        // ป้องกัน auto-confirm: ถ้า LLM พยายาม confirm ในเทิร์นเดียวกับ prepare ต้องไม่สร้าง
        $admin = User::factory()->create(['is_can_access_admin' => true]);
        $group = CustomerGroup::create(['name' => 'โรงแรม']);

        // LLM ปลอมที่ "โกง" — สั่ง prepare แล้ว confirm ในเทิร์นเดียว
        $this->fakeLlm(new FakeAutoConfirmLlm($group->id));

        $chat = app(ChatService::class);
        $chat->processCommand('สร้างลูกค้าใหม่ชื่อ โกง กลุ่มโรงแรม', $admin);

        // ด่านกัน auto-confirm ต้องบล็อก — ไม่มี customer ถูกสร้าง
        $this->assertDatabaseMissing('customers', ['name' => 'โกง']);
    }

    /** ให้ทุก provider ที่ manager คืนออกมาเป็นตัวปลอมตัวนี้ */
    protected function fakeLlm(LlmProvider $provider): void
    {
        $this->app->instance(LlmProviderManager::class, new FakeLlmProviderManager($provider));
    }
}

/** manager ที่ route ทุก model ไปยัง provider ปลอมตัวเดียว */
class FakeLlmProviderManager extends LlmProviderManager
{
    public function __construct(protected LlmProvider $fake)
    {
    }

    public function forName(string $name): LlmProvider
    {
        return $this->fake;
    }

    public function forModel(?string $model): LlmProvider
    {
        return $this->fake;
    }

    public function default(): LlmProvider
    {
        return $this->fake;
    }

    public function anyEnabled(): bool
    {
        return true;
    }
}

/**
 * LLM ปลอม: เทิร์นที่ user ยังไม่พิมพ์ "ยืนยัน" → prepare_create_customer
 * เทิร์นที่พิมพ์ "ยืนยัน" → confirm_write; หลังมี tool result แล้ว → คืนข้อความปิดท้าย
 */
class FakeWriteFlowLlm implements LlmProvider
{
    public function __construct(public int $customerGroupId)
    {
    }

    public function name(): string
    {
        return 'fake';
    }

    public function label(): string
    {
        return 'Fake';
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function defaultModel(): string
    {
        return 'fake/model';
    }

    public function streamChatCompletion(
        array $messages,
        ?string $model = null,
        ?int $maxTokens = null,
        array $tools = []
    ): StreamInterface {
        throw new \LogicException('LINE path ไม่ใช้ streaming');
    }

    public function chatCompletion(
        array $messages,
        array $tools = [],
        ?string $model = null,
        ?int $maxTokens = null
    ): array {
        // ถ้ามี tool result แล้วในรอบนี้ → จบด้วยข้อความ
        foreach ($messages as $m) {
            if (($m['role'] ?? '') === 'tool') {
                return $this->content('ดำเนินการเรียบร้อยแล้วครับ');
            }
        }

        $lastUser = '';
        foreach (array_reverse($messages) as $m) {
            if (($m['role'] ?? '') === 'user') {
                $lastUser = (string) ($m['content'] ?? '');
                break;
            }
        }

        if (mb_strpos($lastUser, 'ยืนยัน') !== false) {
            return $this->toolCall('confirm_write', []);
        }

        return $this->toolCall('prepare_create_customer', [
            'name' => 'โรงแรม LINE',
            'customer_group_id' => $this->customerGroupId,
        ]);
    }

    protected function content(string $text): array
    {
        return ['choices' => [['message' => ['role' => 'assistant', 'content' => $text]]]];
    }

    protected function toolCall(string $name, array $args): array
    {
        return ['choices' => [['message' => [
            'role' => 'assistant',
            'content' => null,
            'tool_calls' => [[
                'id' => 'call_' . $name,
                'type' => 'function',
                'function' => ['name' => $name, 'arguments' => json_encode($args)],
            ]],
        ]]]];
    }
}

/** LLM ปลอมที่พยายาม auto-confirm ในเทิร์นเดียว (prepare → confirm ทันที) */
class FakeAutoConfirmLlm extends FakeWriteFlowLlm
{
    public function chatCompletion(
        array $messages,
        array $tools = [],
        ?string $model = null,
        ?int $maxTokens = null
    ): array {
        // นับ tool result ที่เกิดแล้วในรอบนี้
        $toolResults = 0;
        foreach ($messages as $m) {
            if (($m['role'] ?? '') === 'tool') {
                $toolResults++;
            }
        }

        if ($toolResults === 0) {
            return $this->toolCall('prepare_create_customer', [
                'name' => 'โกง',
                'customer_group_id' => $this->customerGroupId,
            ]);
        }
        if ($toolResults === 1) {
            // โกง: พยายาม confirm ทันทีในเทิร์นเดียวกัน
            return $this->toolCall('confirm_write', []);
        }

        return $this->content('จบ');
    }
}
