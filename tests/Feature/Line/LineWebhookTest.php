<?php

namespace Tests\Feature\Line;

use App\Contracts\LlmProvider;
use App\Exceptions\LlmApiException;
use App\Models\User;
use App\Services\Llm\LlmProviderManager;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\StreamInterface;
use Tests\TestCase;

/**
 * ทดสอบ endpoint POST /api/line/webhook
 *
 * สองสัญญาหลักที่ต้องไม่พัง:
 *   1. signature ไม่ผ่าน → 401 เสมอ (ทุกเคส response body เหมือนกัน ไม่รั่วข้อมูล)
 *   2. signature ผ่านแล้ว → 200 เสมอ ไม่ว่าจะมี LLM หรือไม่ หรือ LLM พังยังไง
 *      (LINE ปิด webhook อัตโนมัติถ้าเจอ non-2xx ติดกัน — LLM เป็น optional ห้ามทำให้ล้ม)
 */
class LineWebhookTest extends TestCase
{
    use DatabaseTransactions;

    private const SECRET = 'test-channel-secret';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.line.channel_secret' => self::SECRET,
            'services.line.channel_access_token' => 'test-access-token',
        ]);

        // กันยิง LINE Messaging API จริง
        Http::fake(['api.line.me/*' => Http::response(['ok' => true], 200)]);
    }

    /** ยิง webhook พร้อม signature ที่ถูกต้อง */
    private function postSigned(array $payload)
    {
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE);

        return $this->call(
            'POST',
            '/api/line/webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_LINE_SIGNATURE' => $this->sign($body),
            ],
            $body
        );
    }

    private function sign(string $body): string
    {
        return base64_encode(hash_hmac('sha256', $body, self::SECRET, true));
    }

    private function textMessageEvent(string $text): array
    {
        return [
            'type' => 'message',
            'replyToken' => 'reply-token-123',
            'source' => ['userId' => 'U-line-user-1'],
            'message' => ['type' => 'text', 'text' => $text],
        ];
    }

    /** ปิด provider ทุกเจ้า = ไม่มี LLM ให้ใช้ */
    private function disableAllLlmProviders(): void
    {
        config([
            'ai-chat.providers.wavespeed.api_key' => null,
            'ai-chat.providers.wavespeed.base_url' => null,
            'ai-chat.providers.9router.api_key' => null,
            'ai-chat.providers.9router.base_url' => null,
        ]);
    }

    // ── signature ────────────────────────────────────────────────────────────

    /** ปุ่ม Verify ของ LINE ส่ง events ว่างมาพร้อม signature ที่ถูกต้อง */
    public function test_valid_signature_with_empty_events_returns_ok(): void
    {
        $this->postSigned(['events' => []])
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);
    }

    public function test_missing_channel_secret_returns_401(): void
    {
        config(['services.line.channel_secret' => null]);

        $this->postJson('/api/line/webhook', ['events' => []], ['X-Line-Signature' => 'anything'])
            ->assertStatus(401)
            ->assertExactJson(['error' => 'Invalid signature']);
    }

    public function test_missing_signature_header_returns_401(): void
    {
        $this->postJson('/api/line/webhook', ['events' => []])
            ->assertStatus(401)
            ->assertExactJson(['error' => 'Invalid signature']);
    }

    public function test_wrong_signature_returns_401(): void
    {
        $this->postJson(
            '/api/line/webhook',
            ['events' => []],
            ['X-Line-Signature' => base64_encode(hash_hmac('sha256', '{"events":[]}', 'wrong-secret', true))]
        )
            ->assertStatus(401)
            ->assertExactJson(['error' => 'Invalid signature']);
    }

    /** signature คำนวณจาก body ดิบ — body ที่ต่างกันแม้แต่ตัวเดียวต้องไม่ผ่าน */
    public function test_signature_of_a_different_body_returns_401(): void
    {
        $this->call(
            'POST',
            '/api/line/webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_LINE_SIGNATURE' => $this->sign('{"events":[]}'),
            ],
            '{"events":[],"extra":1}'
        )->assertStatus(401);
    }

    // ── ไม่มี LLM ต้องไม่ error (ข้อกำหนดหลัก) ────────────────────────────────

    public function test_message_event_succeeds_when_no_llm_is_configured(): void
    {
        $this->disableAllLlmProviders();
        User::factory()->create(['line_user_id' => 'U-line-user-1']);

        $this->postSigned(['events' => [$this->textMessageEvent('ข้อความที่ไม่ตรงเมนูใดเลย')]])
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);

        // ต้องตอบเป็นเมนูปกติ ไม่ใช่ข้อความแจ้ง error
        $this->assertReplySent(fn (string $reply) => !str_contains($reply, 'เกิดข้อผิดพลาด')
            && !str_contains($reply, 'ระบบ AI ขัดข้อง'));
    }

    /** ผู้ใช้ที่ยังไม่ผูกบัญชี + ไม่มี LLM ก็ต้องไม่ล้ม */
    public function test_unlinked_user_message_succeeds_when_no_llm_is_configured(): void
    {
        $this->disableAllLlmProviders();

        $this->postSigned(['events' => [$this->textMessageEvent('สวัสดี')]])
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);
    }

    /** เมนูที่ไม่ต้องใช้ AI ต้องทำงานได้ตามปกติเมื่อไม่มี LLM */
    public function test_menu_command_works_without_llm(): void
    {
        $this->disableAllLlmProviders();
        User::factory()->create(['line_user_id' => 'U-line-user-1']);

        $this->postSigned(['events' => [$this->textMessageEvent('เมนู')]])->assertOk();

        $this->assertReplySent(fn (string $reply) => !str_contains($reply, 'เกิดข้อผิดพลาด'));
    }

    /** write intent ("สร้าง...") ที่ปกติวิ่งเข้า AI — ไม่มี LLM ต้องตกไปเมนู ไม่ใช่ error */
    public function test_write_intent_falls_back_to_menu_without_llm(): void
    {
        $this->disableAllLlmProviders();
        User::factory()->create(['line_user_id' => 'U-line-user-1']);

        $this->postSigned(['events' => [$this->textMessageEvent('สร้างลูกค้าใหม่ชื่อ ทดสอบ')]])
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);

        $this->assertReplySent(fn (string $reply) => !str_contains($reply, 'เกิดข้อผิดพลาด'));
    }

    // ── LLM พังก็ต้องไม่ error ────────────────────────────────────────────────

    public function test_message_event_succeeds_when_llm_throws(): void
    {
        $this->app->instance(LlmProviderManager::class, new AlwaysFailingLlmManager());
        User::factory()->create(['line_user_id' => 'U-line-user-1']);

        $this->postSigned(['events' => [$this->textMessageEvent('ข้อความที่ไม่ตรงเมนูใดเลย')]])
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);
    }

    /**
     * provider config พัง (driver ไม่รู้จัก) — isAvailable() ต้องไม่ throw
     * ผู้ใช้ต้องได้เมนูปกติ ไม่ใช่ข้อความแจ้ง error (200 อย่างเดียวไม่พอ:
     * โค้ดที่ throw ก็ยังตอบ 200 ได้ เพราะ controller ดักไว้แล้วส่งข้อความ error กลับไป)
     */
    public function test_message_event_succeeds_when_provider_config_is_broken(): void
    {
        // ปิด provider จริงให้หมด เหลือแต่ตัวที่ config พัง — กันผลลัพธ์ขึ้นกับ .env ของเครื่องที่รัน
        $this->disableAllLlmProviders();
        config(['ai-chat.providers.broken' => [
            'label' => 'Broken',
            'driver' => 'no-such-driver',
            'base_url' => 'https://broken.example.test/v1',
        ]]);
        User::factory()->create(['line_user_id' => 'U-line-user-1']);

        $this->postSigned(['events' => [$this->textMessageEvent('ข้อความที่ไม่ตรงเมนูใดเลย')]])
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);

        $this->assertReplySent(fn (string $reply) => !str_contains($reply, 'เกิดข้อผิดพลาด')
            && !str_contains($reply, 'ระบบ AI ขัดข้อง'));
    }

    // ── payload แปลกๆ ต้องไม่เป็น 500 ────────────────────────────────────────

    public function test_malformed_events_do_not_crash(): void
    {
        $this->disableAllLlmProviders();

        $this->postSigned(['events' => [
            [],                                             // event ว่าง
            ['type' => 'message'],                          // ไม่มี replyToken/source/message
            ['type' => 'message', 'replyToken' => 'r', 'source' => ['userId' => 'U-x']], // ไม่มี message
            ['type' => 'unknown-type', 'replyToken' => 'r', 'source' => ['userId' => 'U-x']],
            'not-an-array',
        ]])
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);
    }

    public function test_body_without_events_key_returns_ok(): void
    {
        $this->postSigned(['destination' => 'Uabc'])
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);
    }

    /** body ที่ไม่ใช่ JSON object (signature ถูกต้อง) ต้องไม่เป็น 500 */
    public function test_non_object_json_body_returns_ok(): void
    {
        $body = '"just a string"';

        $this->call(
            'POST',
            '/api/line/webhook',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_X_LINE_SIGNATURE' => $this->sign($body)],
            $body
        )
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);
    }

    // ── helper ───────────────────────────────────────────────────────────────

    /** ยืนยันว่ามี reply ถูกส่งไป LINE และข้อความผ่านเงื่อนไขที่กำหนด */
    private function assertReplySent(callable $predicate): void
    {
        $matched = false;

        Http::assertSent(function ($request) use ($predicate, &$matched) {
            if (!str_contains($request->url(), '/message/reply')) {
                return false;
            }

            $text = json_encode($request->data()['messages'] ?? [], JSON_UNESCAPED_UNICODE);
            $matched = $matched || $predicate($text);

            return true;
        });

        $this->assertTrue($matched, 'ข้อความที่ตอบกลับไป LINE ไม่ผ่านเงื่อนไขที่คาดไว้');
    }
}

/** manager ที่ทุก provider พังหมด แต่รายงานว่า "มี LLM" — จำลอง LLM ล่ม */
class AlwaysFailingLlmManager extends LlmProviderManager
{
    public function forName(string $name): LlmProvider
    {
        return new AlwaysFailingLlm();
    }

    public function forModel(?string $model): LlmProvider
    {
        return new AlwaysFailingLlm();
    }

    public function default(): LlmProvider
    {
        return new AlwaysFailingLlm();
    }

    public function anyEnabled(): bool
    {
        return true;
    }
}

class AlwaysFailingLlm implements LlmProvider
{
    public function name(): string
    {
        return 'failing';
    }

    public function label(): string
    {
        return 'Failing';
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function defaultModel(): string
    {
        return 'failing/model';
    }

    public function chatCompletion(
        array $messages,
        array $tools = [],
        ?string $model = null,
        ?int $maxTokens = null
    ): array {
        throw new LlmApiException('LLM provider [failing] returned HTTP 500', 500);
    }

    public function streamChatCompletion(
        array $messages,
        ?string $model = null,
        ?int $maxTokens = null,
        array $tools = []
    ): StreamInterface {
        throw new LlmApiException('LLM provider [failing] returned HTTP 500', 500);
    }
}
