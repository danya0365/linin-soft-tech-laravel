<?php

namespace Tests\Feature\Ai;

use App\Models\AiChatSession;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * แชทจริงผ่าน endpoint จริง (/api/ai-chat/sessions/{id}/stream) ด้วย 9Router
 *
 * ต่างจาก NineRouterSmokeTest ตรงที่อันนั้นยิง provider ตรงๆ ส่วนอันนี้เดินผ่าน
 * ทั้งเส้น: middleware staff → validate model → เช็คเครดิต → stream → บันทึก DB → หักเครดิต
 * คือเส้นเดียวกับที่หน้า /ai-chat ใช้จริง
 *
 * WaveSpeed ถูกปิดใน setUp() ทุกเทสในไฟล์นี้ — มีค่าใช้จ่ายจริง จึงต้องยิงไม่ได้เลย
 * แม้โค้ดจะ fallback ผิดทาง (ถ้า fallback ไป WaveSpeed จะกลายเป็น 422 ให้เห็นทันที)
 *
 * ข้ามอัตโนมัติเมื่อต่อ 9Router ไม่ติด
 *
 * @group live
 */
class NineRouterChatEndpointTest extends TestCase
{
    use DatabaseTransactions;

    /** model ของ 9Router ที่ต้นทุน token = 0 — คิดค่าบริการคงที่ต่อข้อความแทน */
    private const MODEL = 'oc/deepseek-v4-flash-free';

    protected function setUp(): void
    {
        parent::setUp();

        $baseUrl = $this->reachableBaseUrl();

        if ($baseUrl === null) {
            $this->markTestSkipped('ต่อ 9Router ไม่ติด — ตั้ง NINEROUTER_BASE_URL ใน .env ก่อน');
        }

        config([
            // WaveSpeed เสียเงินจริง — ปิดตายไว้ ห้ามมีทางถูกเรียกจากเทสนี้
            'ai-chat.providers.wavespeed.api_key' => null,
            'ai-chat.providers.9router.base_url' => $baseUrl,
            'ai-chat.default_provider' => '9router',
            'ai-chat.default_model' => self::MODEL,
        ]);
    }

    /**
     * base_url แรกที่ต่อติดจริง (null = ไม่มีเลย)
     *
     * ลอง host.docker.internal ด้วยเมื่อค่าที่ตั้งไว้ชี้ localhost เพราะใน Sail
     * localhost คือตัว container เอง ไม่ใช่เครื่องที่รัน 9Router อยู่
     */
    protected function reachableBaseUrl(): ?string
    {
        // phpunit.xml เคลียร์ค่านี้ไว้กันเทสอื่นยิงของจริง — เทสนี้จึงอ่านค่าจริงจาก .env
        $configured = trim((string) ($_SERVER['NINEROUTER_BASE_URL'] ?? '')) ?: $this->baseUrlFromEnvFile();

        if ($configured === '') {
            return null;
        }

        $candidates = [$configured];

        if (preg_match('#^(https?://)(localhost|127\.0\.0\.1)(?=[:/]|$)#', $configured)) {
            $candidates[] = preg_replace(
                '#^(https?://)(localhost|127\.0\.0\.1)#',
                '$1host.docker.internal',
                $configured
            );
        }

        foreach ($candidates as $candidate) {
            if ($this->endpointReachable($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /** อ่านตรงจาก .env เพราะ phpunit.xml override ค่าใน environment ไปแล้ว */
    protected function baseUrlFromEnvFile(): string
    {
        $path = base_path('.env');

        if (!is_readable($path)) {
            return '';
        }

        preg_match('/^NINEROUTER_BASE_URL=(.*)$/m', (string) file_get_contents($path), $m);

        return trim($m[1] ?? '', " \t\"'");
    }

    protected function endpointReachable(string $baseUrl): bool
    {
        try {
            return Http::timeout(5)
                ->connectTimeout(3)
                ->get(rtrim($baseUrl, '/') . '/models')
                ->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    protected function staffUser(float $credit = 100.0): User
    {
        return User::factory()->create([
            'is_can_access_supervisor' => true,
            'ai_credit_balance' => $credit,
        ]);
    }

    protected function chatSession(User $user): AiChatSession
    {
        return AiChatSession::create([
            'user_id' => $user->id,
            'title' => 'เทส 9Router',
            'model' => self::MODEL,
        ]);
    }

    /**
     * @return array{0: string, 1: array<int, array>}  [content ที่ต่อกันแล้ว, event ทั้งหมด]
     */
    protected function consumeSse(string $raw): array
    {
        $content = '';
        $events = [];

        foreach (explode("\n\n", $raw) as $block) {
            $line = trim($block);

            if (strpos($line, 'data:') !== 0) {
                continue;
            }

            $payload = trim(substr($line, 5));

            if ($payload === '[DONE]') {
                $events[] = ['type' => '[DONE]'];
                continue;
            }

            $event = json_decode($payload, true);

            if (!is_array($event)) {
                continue;
            }

            $events[] = $event;

            // content delta ส่งมาในรูป OpenAI (ดู AiChatStreamService::streamAnswer)
            $delta = $event['choices'][0]['delta']['content'] ?? null;

            if (is_string($delta)) {
                $content .= $delta;
            }
        }

        return [$content, $events];
    }

    /** @param array<int, array> $events */
    protected function firstEvent(array $events, string $type): ?array
    {
        foreach ($events as $event) {
            if (($event['type'] ?? null) === $type) {
                return $event;
            }
        }

        return null;
    }

    /**
     * เส้นทางหลัก: staff ส่งข้อความ → ได้คำตอบ stream กลับ → บันทึกลง DB → หักเครดิต
     */
    public function test_staff_can_chat_through_the_stream_endpoint(): void
    {
        $user = $this->staffUser();
        $session = $this->chatSession($user);

        $response = $this->actingAs($user)->post(
            "/api/ai-chat/sessions/{$session->id}/stream",
            [
                'content' => 'ตอบกลับด้วยคำว่า PONG เท่านั้น ห้ามมีข้อความอื่น',
                'model' => self::MODEL,
                'maxTokens' => 1500,
            ],
            ['Accept' => 'text/event-stream']
        );

        $response->assertOk();

        [$content, $events] = $this->consumeSse($response->streamedContent());

        $this->assertNotEmpty($events, 'ต้องมี SSE event อย่างน้อยหนึ่งก้อน');
        $this->assertNotNull($this->firstEvent($events, 'meta'), 'ต้องได้ meta event เป็นก้อนแรก');
        $this->assertStringContainsStringIgnoringCase('pong', $content, 'ต้องได้คำตอบจริงจาก 9Router');

        $done = $this->firstEvent($events, 'done');
        $this->assertNotNull($done, 'ต้องได้ done event');
        $this->assertNotNull($this->firstEvent($events, '[DONE]'), 'stream ต้องปิดด้วย [DONE]');

        // คำตอบต้องถูกบันทึกจริง ไม่ใช่แค่ stream ผ่านไป
        $assistant = $session->messages()->where('role', 'assistant')->latest('id')->first();
        $this->assertNotNull($assistant, 'ต้องบันทึกคำตอบ assistant ลง DB');
        $this->assertSame(self::MODEL, $assistant->model);
        $this->assertGreaterThan(0, (int) $assistant->completion_tokens, 'ต้องได้ usage จริงจาก 9Router');
    }

    /**
     * ต้นทุน token = 0 จึงต้องหักเป็นค่าบริการคงที่ ไม่ใช่ 0
     * (ถ้าหัก 0 = ใช้ได้ไม่จำกัดโดยไม่มีรายได้)
     */
    public function test_zero_cost_model_is_charged_a_flat_fee(): void
    {
        $flatFee = (float) config('ai-chat.models.' . $this->modelCatalogIndex() . '.flat_fee_thb');
        $this->assertGreaterThan(0, $flatFee, 'model ที่ต้นทุน 0 ต้องตั้ง flat_fee_thb ไว้ใน catalog');

        $user = $this->staffUser(100.0);
        $session = $this->chatSession($user);

        $response = $this->actingAs($user)->post(
            "/api/ai-chat/sessions/{$session->id}/stream",
            ['content' => 'ตอบว่า OK', 'model' => self::MODEL, 'maxTokens' => 1500],
            ['Accept' => 'text/event-stream']
        );

        $response->assertOk();
        [, $events] = $this->consumeSse($response->streamedContent());

        $done = $this->firstEvent($events, 'done');
        $this->assertNotNull($done, 'ต้องได้ done event');
        $this->assertSame($flatFee, round((float) $done['creditCharged'], 4), 'ต้องหักเท่าค่าบริการคงที่');
        $this->assertSame(
            round(100.0 - $flatFee, 4),
            round((float) $done['creditBalance'], 4),
            'ยอดคงเหลือต้องลดลงเท่าที่หัก'
        );
    }

    /** ตำแหน่งของ model ใน catalog — ใช้ดึง flat_fee_thb โดยไม่ hardcode ตัวเลข */
    protected function modelCatalogIndex(): int
    {
        foreach ((array) config('ai-chat.models', []) as $i => $entry) {
            if (($entry['id'] ?? null) === self::MODEL) {
                return $i;
            }
        }

        $this->fail('ไม่พบ ' . self::MODEL . ' ใน config ai-chat.models');
    }

    /**
     * WaveSpeed ปิดอยู่ → model ของมันต้องยิงไม่ได้ (กันเผลอเสียเงินจากหน้าเว็บ)
     */
    public function test_disabled_wavespeed_model_is_rejected(): void
    {
        $user = $this->staffUser();
        $session = $this->chatSession($user);

        $this->actingAs($user)
            ->postJson("/api/ai-chat/sessions/{$session->id}/stream", [
                'content' => 'สวัสดี',
                'model' => 'minimax/minimax-m2.7',
            ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    /**
     * หน้า /ai-chat ต้องเห็น 9Router และ model ของมัน แต่ต้องไม่เห็น model ของ WaveSpeed ที่ปิดอยู่
     */
    public function test_ai_chat_page_lists_only_enabled_providers(): void
    {
        $user = $this->staffUser();

        $config = $this->actingAs($user)
            ->get('/ai-chat')
            ->assertOk()
            ->viewData('aiChatConfig');

        $this->assertTrue($config['enabled']);
        $this->assertSame([['name' => '9router', 'label' => '9Router']], $config['providers']);

        $modelIds = array_column($config['models'], 'id');
        $this->assertContains(self::MODEL, $modelIds);
        $this->assertNotContains('minimax/minimax-m2.7', $modelIds, 'model ของ provider ที่ปิดอยู่ต้องไม่โผล่');
    }
}
