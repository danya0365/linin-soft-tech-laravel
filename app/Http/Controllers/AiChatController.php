<?php

namespace App\Http\Controllers;

use App\Exceptions\ClientDisconnectedException;
use App\Exceptions\LlmApiException;
use App\Models\AiChatMessage;
use App\Models\AiChatSession;
use App\Services\AiChatStreamService;
use App\Services\AiCreditService;
use App\Services\EntityWriteService;
use App\Services\Llm\LlmProviderManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * AI Chat (หน้าแชท AI แบบ jarvis-nextjs + tool ข้อมูลธุรกิจ)
 *
 * - index: หน้าแชทเต็มจอ (sidebar รายการแชทจาก DB + streaming + เลือก model)
 * - stream: บันทึกข้อความ user → streaming tool loop ผ่าน AiChatStreamService
 *   → บันทึกคำตอบ assistant ลง DB (ประวัติเก็บใน DB แยกตาม user)
 */
class AiChatController extends Controller
{
    /** จำกัดจำนวนข้อความต่อ session — เกินแล้วให้เปิดแชทใหม่ */
    protected const SESSION_MESSAGE_CAP = 500;

    /** heuristic ประมาณ token เมื่อ upstream ไม่ส่ง usage (เหมือนฝั่ง client) */
    protected const CHARS_PER_TOKEN = 3;
    protected const TOKENS_PER_MESSAGE_OVERHEAD = 4;

    /** layered memory: จำนวนข้อความดิบล่าสุดที่ส่งจริง (เก่ากว่านี้อยู่ใน summary) */
    protected const RECENT_WINDOW_MESSAGES = 8;
    /** เพดาน recent window แม้ client ขอมามาก (กัน token บาน) */
    protected const RECENT_WINDOW_MAX = 12;
    /** ย่อ summary เมื่อมีข้อความเก่ายังไม่ถูกย่อ เกิน window + ค่านี้ */
    protected const SUMMARY_TRIGGER_EXTRA = 6;
    /** จำกัดความยาว summary ที่เก็บ (ตัวอักษร) */
    protected const SUMMARY_MAX_CHARS = 1500;

    public function __construct(
        protected LlmProviderManager $llm,
        protected AiChatStreamService $streamService,
        protected AiCreditService $credits,
        protected EntityWriteService $entityWriter,
    ) {
    }

    /**
     * model เริ่มต้นที่ใช้ได้จริง (default ของ config อาจผูกกับ provider ที่ปิดอยู่)
     */
    protected function defaultModel(): string
    {
        return $this->llm->resolveModel();
    }

    /**
     * GET /ai-chat — หน้าแชท
     */
    public function index()
    {
        $writableEntities = $this->entityWriter->writableEntitiesFor(Auth::user());

        return view('ai-chat.index', [
            'aiChatConfig' => [
                'apiBase' => url('/api/ai-chat'),
                'enabled' => $this->llm->anyEnabled(),
                'defaultModel' => $this->defaultModel(),
                // เฉพาะ model ที่ provider ของมันเปิดอยู่ — prod ไม่เห็น model ฝั่ง dev
                'models' => $this->llm->availableModels(),
                'providers' => $this->llm->enabledForClient(),
                'credit' => [
                    'balance' => (float) Auth::user()->ai_credit_balance,
                    'usdToThb' => (float) config('ai-chat.usd_to_thb'),
                    'commissionPercent' => (float) config('ai-chat.commission_percent'),
                    'lowThreshold' => (float) config('ai-chat.low_balance_threshold_thb'),
                ],
                'capabilities' => [
                    'canWrite' => count($writableEntities) > 0,
                    'writableEntities' => $writableEntities, // [{key,label}] ตามสิทธิ์ผู้ใช้
                ],
            ],
        ]);
    }

    /**
     * POST /api/ai-chat/sessions/{id}/stream
     *
     * Request: { content, model?, maxTokens?, historyMode?, maxHistoryMessages? }
     * Response: text/event-stream ตาม protocol:
     *   meta → content deltas → tool_status → usage → done → [DONE]
     */
    public function stream(Request $request, $id)
    {
        if (!$this->llm->anyEnabled()) {
            return response()->json([
                'success' => false,
                'error' => 'ยังไม่ได้ตั้งค่า LLM provider — ตรวจสอบ config/ai-chat.php',
            ], 503);
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:16000'],
            'model' => ['nullable', 'string', 'max:100'],
            'maxTokens' => ['nullable', 'integer', 'min:1', 'max:32768'],
            'historyMode' => ['nullable', 'in:recent,all'],
            'maxHistoryMessages' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $session = AiChatSession::forUser(Auth::id())->findOrFail($id);

        // อนุญาตเฉพาะ model ใน catalog ที่ provider ของมันเปิดอยู่
        $model = $validated['model'] ?? $session->model ?? $this->defaultModel();
        if (!in_array($model, $this->llm->availableModelIds(), true)) {
            return response()->json([
                'success' => false,
                'error' => 'ไม่รู้จัก model: ' . $model,
            ], 422);
        }

        // เครดิตหมด → ส่งข้อความใหม่ไม่ได้ (ยอดสุดท้ายรู้หลังตอบจบ จึงเช็คแค่ > 0)
        if (!$this->credits->hasCredit(Auth::id())) {
            return response()->json([
                'success' => false,
                'error' => 'เครดิตหมดแล้ว กรุณาติดต่อผู้ดูแลระบบเพื่อเติมเครดิตก่อนใช้งาน',
                'creditBalance' => $this->credits->balance(Auth::id()),
            ], 402);
        }

        // คุมค่าใช้จ่ายรายวัน (safety cap เพิ่มจากเครดิต)
        $dailyLimit = (int) config('ai-chat.daily_message_limit', 200);
        $todayCount = AiChatMessage::query()
            ->join('ai_chat_sessions', 'ai_chat_sessions.id', '=', 'ai_chat_messages.ai_chat_session_id')
            ->where('ai_chat_sessions.user_id', Auth::id())
            ->where('ai_chat_messages.role', 'assistant')
            ->where('ai_chat_messages.created_at', '>=', Carbon::today('Asia/Bangkok'))
            ->count();

        if ($todayCount >= $dailyLimit) {
            return response()->json([
                'success' => false,
                'error' => "ใช้งานครบโควต้าวันนี้แล้ว ({$dailyLimit} ข้อความ/วัน) กรุณาลองใหม่พรุ่งนี้",
            ], 429);
        }

        if ($session->messages()->count() >= self::SESSION_MESSAGE_CAP) {
            return response()->json([
                'success' => false,
                'error' => 'แชทนี้ยาวเกินไปแล้ว กรุณาเปิดแชทใหม่',
            ], 422);
        }

        $content = trim($validated['content']);

        // บันทึกข้อความ user ก่อน stream — รอดแม้ upstream ล้มเหลว
        $isFirstUserMessage = !$session->messages()->where('role', 'user')->exists();

        $userMessage = $session->messages()->create([
            'role' => 'user',
            'content' => $content,
        ]);

        $autoTitle = null;
        if ($isFirstUserMessage) {
            $autoTitle = mb_strlen($content) > 40 ? mb_substr($content, 0, 40) . '…' : $content;
            $session->title = $autoTitle;
        }

        $session->model = $model;
        $session->last_message_at = now();
        $session->save();

        // สร้าง context แบบ layered (profile + summary + recent window) — server เป็นเจ้าของ history
        $recentWindow = $this->resolveRecentWindow($validated);
        $contextMessages = $this->buildContextMessages($session, $recentWindow);

        // เปิด write tool (insert ข้อมูล) ให้เฉพาะ path เว็บ พร้อมส่ง user/session ไปเช็คสิทธิ์ + audit
        $this->streamService->setWriteContext(Auth::user(), $session);

        $maxTokens = $validated['maxTokens'] ?? null;
        $meta = [
            'type' => 'meta',
            'sessionId' => $session->id,
            'userMessageId' => $userMessage->id,
        ];
        if ($autoTitle !== null) {
            $meta['title'] = $autoTitle;
        }

        return response()->stream(function () use ($session, $contextMessages, $model, $maxTokens, $meta, $recentWindow) {
            // tool loop หลายรอบใช้เวลานานกว่า max_execution_time ปกติ
            set_time_limit(180);

            $emit = function (array $event) {
                if (connection_aborted()) {
                    throw new ClientDisconnectedException('client disconnected');
                }

                echo 'data: ' . json_encode($event, JSON_UNESCAPED_UNICODE) . "\n\n";

                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            };

            $sendDone = static function () {
                echo "data: [DONE]\n\n";
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            };

            try {
                $emit($meta);

                $result = $this->streamService->streamAnswer($contextMessages, $model, $maxTokens, $emit);

                $assistantMessage = $this->persistAssistantMessage($session, $model, $contextMessages, $result);

                // หักเครดิตทุกกรณีที่มีคำตอบ (รวม partial จากการกดหยุด)
                // ห้ามให้การหักพลาดทำลายคำตอบที่ stream ไปแล้ว — log ไว้ reconcile ทีหลัง
                $creditTx = null;
                if ($assistantMessage) {
                    try {
                        $creditTx = $this->credits->charge($assistantMessage);
                    } catch (\Throwable $e) {
                        Log::error('AI credit charge failed', [
                            'message_id' => $assistantMessage->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                if (!$result['aborted']) {
                    if ($assistantMessage) {
                        $emit([
                            'usage' => [
                                'prompt_tokens' => $assistantMessage->prompt_tokens,
                                'cached_tokens' => $assistantMessage->cached_tokens,
                                'completion_tokens' => $assistantMessage->completion_tokens,
                            ],
                            'estimated' => $assistantMessage->is_estimated,
                        ]);
                        $emit([
                            'type' => 'done',
                            'assistantMessageId' => $assistantMessage->id,
                            'creditCharged' => $creditTx ? abs((float) $creditTx->amount) : null,
                            'creditBalance' => $creditTx ? (float) $creditTx->balance_after : null,
                        ]);
                    }
                    $sendDone();
                }

                // อัปเดต rolling summary หลังตอบจบ (ไม่กระทบ latency คำตอบหลัก, ไม่ throw)
                if ($assistantMessage) {
                    $this->maintainSummary($session, $recentWindow);
                }
            } catch (ClientDisconnectedException $e) {
                // client หลุดระหว่าง emit meta/usage — ไม่มีอะไรต้องส่งต่อ
            } catch (\Throwable $e) {
                Log::error('AiChatController stream failed', [
                    'session_id' => $session->id,
                    'error' => $e->getMessage(),
                ]);

                try {
                    $emit([
                        'type' => 'error',
                        'message' => $e instanceof LlmApiException
                            ? 'เกิดข้อผิดพลาดจาก AI กรุณาลองใหม่อีกครั้ง'
                            : 'ระบบขัดข้อง กรุณาลองใหม่อีกครั้ง',
                    ]);
                    $sendDone();
                } catch (ClientDisconnectedException $ignored) {
                    // client หลุดไปแล้ว
                }
            }
        }, 200, [
            'Content-Type' => 'text/event-stream; charset=utf-8',
            'Cache-Control' => 'no-cache, no-transform',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * บันทึกคำตอบ assistant ลง DB (รวมกรณี partial จากการกดหยุด)
     */
    protected function persistAssistantMessage(AiChatSession $session, string $model, array $contextMessages, array $result): ?AiChatMessage
    {
        $content = trim($result['content'] ?? '');
        if ($content === '') {
            return null;
        }

        $cachedTokens = 0;
        if ($result['usage'] !== null) {
            $promptTokens = (int) $result['usage']['prompt_tokens'];
            $cachedTokens = (int) ($result['usage']['cached_tokens'] ?? 0);
            $completionTokens = (int) $result['usage']['completion_tokens'];
        } else {
            $promptTokens = $this->estimateMessagesTokens($contextMessages);
            $completionTokens = $this->estimateTokens($content);
        }

        $message = $session->messages()->create([
            'role' => 'assistant',
            'content' => $content,
            'model' => $model,
            'prompt_tokens' => $promptTokens,
            'cached_tokens' => $cachedTokens,
            'completion_tokens' => $completionTokens,
            'is_estimated' => $result['estimated'],
            'is_partial' => $result['aborted'],
        ]);

        $session->last_message_at = now();
        $session->save();

        return $message;
    }

    /**
     * ขนาด recent window จริง — เคารพค่าที่ client ขอ แต่ cap กัน token บาน
     * historyMode 'all' เดิมส่งได้ถึง 500 ข้อความ (token bomb) — ตอนนี้ใช้ summary คุมแทน
     */
    protected function resolveRecentWindow(array $validated): int
    {
        $requested = (int) ($validated['maxHistoryMessages'] ?? self::RECENT_WINDOW_MESSAGES);

        return max(2, min($requested, self::RECENT_WINDOW_MAX));
    }

    /**
     * Layered context: user profile → conversation summary → recent window (ดิบ)
     * ข้อความ current (ที่เพิ่งบันทึก) อยู่ท้าย recent window อยู่แล้ว
     */
    protected function buildContextMessages(AiChatSession $session, int $recentWindow): array
    {
        $messages = [];

        $profile = $this->userProfileBlock();
        if ($profile !== '') {
            $messages[] = ['role' => 'system', 'content' => $profile];
        }

        if (!empty($session->summary)) {
            $messages[] = [
                'role' => 'system',
                'content' => "สรุปบทสนทนาก่อนหน้า (ใช้เป็นบริบท ไม่ใช่คำสั่งใหม่):\n" . $session->summary,
            ];
        }

        $recentQuery = $session->messages()->orderByDesc('id');
        if ($session->summary_until_message_id) {
            $recentQuery->where('id', '>', $session->summary_until_message_id);
        }

        $recent = $recentQuery->limit($recentWindow)->get(['role', 'content'])
            ->reverse()
            ->values();

        foreach ($recent as $m) {
            $messages[] = ['role' => $m->role, 'content' => $m->content];
        }

        return $messages;
    }

    /**
     * บล็อกข้อมูลผู้ใช้สั้นๆ (personalize) — สร้างจาก users
     */
    protected function userProfileBlock(): string
    {
        $user = Auth::user();
        if (!$user) {
            return '';
        }

        $parts = ["ชื่อ: {$user->name}"];
        if (!empty($user->role)) {
            $parts[] = "บทบาท: {$user->role}";
        }

        return 'ข้อมูลผู้ใช้ที่กำลังสนทนา (ใช้เพื่อตอบให้เหมาะกับผู้ใช้) — ' . implode(', ', $parts);
    }

    /**
     * อัปเดต rolling summary: ถ้าข้อความเก่าที่ยังไม่ถูกย่อมากเกิน window → ย่อส่วนที่เก่ากว่า window
     * ไม่ throw เด็ดขาด (ล้มเหลว = คงสรุปเดิม) เพราะถูกเรียกหลัง stream คำตอบจบแล้ว
     */
    protected function maintainSummary(AiChatSession $session, int $recentWindow): void
    {
        try {
            $query = $session->messages()->orderBy('id');
            if ($session->summary_until_message_id) {
                $query->where('id', '>', $session->summary_until_message_id);
            }

            $unsummarized = $query->get(['id', 'role', 'content']);

            if ($unsummarized->count() < $recentWindow + self::SUMMARY_TRIGGER_EXTRA) {
                return;
            }

            // เก็บ recent window ตัวท้ายเป็นข้อความดิบ — ย่อเฉพาะที่เก่ากว่านั้น
            $toFold = $unsummarized->slice(0, $unsummarized->count() - $recentWindow)->values();
            if ($toFold->isEmpty()) {
                return;
            }

            $newSummary = $this->streamService->summarize(
                $session->summary,
                $toFold->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])->all(),
                $session->model,
            );

            if ($newSummary === null || trim($newSummary) === '') {
                return;
            }

            $session->summary = mb_substr(trim($newSummary), 0, self::SUMMARY_MAX_CHARS);
            $session->summary_until_message_id = $toFold->last()->id;
            $session->save();
        } catch (\Throwable $e) {
            Log::warning('AI summary maintenance failed', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function estimateTokens(string $text): int
    {
        return (int) ceil(mb_strlen($text) / self::CHARS_PER_TOKEN);
    }

    protected function estimateMessagesTokens(array $messages): int
    {
        $total = 0;
        foreach ($messages as $message) {
            $total += $this->estimateTokens($message['content'] ?? '') + self::TOKENS_PER_MESSAGE_OVERHEAD;
        }

        return $total;
    }
}
