<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiChatMessage;
use App\Models\AiChatSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * AI Chat Sessions API (หน้า /ai-chat)
 *
 * ทุก query ถูก scope ด้วย user_id ของผู้ login — เข้าถึง session คนอื่นได้ 404
 */
class AiChatSessionController extends Controller
{
    protected const MAX_SESSIONS_LISTED = 100;
    protected const MAX_MESSAGES_LISTED = 500;

    /**
     * GET /api/ai-chat/sessions — รายการ session ของ user
     */
    public function index()
    {
        $sessions = AiChatSession::forUser(Auth::id())
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->limit(self::MAX_SESSIONS_LISTED)
            ->get(['id', 'title', 'model', 'last_message_at']);

        return response()->json([
            'success' => true,
            'sessions' => $sessions->map(fn ($s) => $this->serializeSession($s)),
            'credit' => [
                'balance' => (float) Auth::user()->ai_credit_balance,
            ],
        ]);
    }

    /**
     * POST /api/ai-chat/sessions — สร้าง session ใหม่
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
        ]);

        $model = $this->validModelOrDefault($validated['model'] ?? null);

        $session = AiChatSession::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'] ?? 'แชทใหม่',
            'model' => $model,
            'last_message_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'session' => $this->serializeSession($session),
        ], 201);
    }

    /**
     * GET /api/ai-chat/sessions/{id} — session พร้อมข้อความ
     */
    public function show($id)
    {
        $session = AiChatSession::forUser(Auth::id())->findOrFail($id);

        $messages = $session->messages()
            ->orderBy('id')
            ->limit(self::MAX_MESSAGES_LISTED)
            ->get();

        return response()->json([
            'success' => true,
            'session' => $this->serializeSession($session),
            'messages' => $messages->map(static function (AiChatMessage $message) {
                return [
                    'id' => $message->id,
                    'role' => $message->role,
                    'content' => $message->content,
                    'model' => $message->model,
                    'createdAt' => $message->created_at?->toIso8601String(),
                    'isPartial' => $message->is_partial,
                    'usage' => $message->prompt_tokens !== null ? [
                        'promptTokens' => $message->prompt_tokens,
                        'completionTokens' => $message->completion_tokens,
                        'estimated' => $message->is_estimated,
                    ] : null,
                ];
            }),
        ]);
    }

    /**
     * PATCH /api/ai-chat/sessions/{id} — เปลี่ยนชื่อ / เปลี่ยน model
     */
    public function update(Request $request, $id)
    {
        $session = AiChatSession::forUser(Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
        ]);

        if (array_key_exists('title', $validated) && $validated['title'] !== null && trim($validated['title']) !== '') {
            $session->title = mb_substr(trim($validated['title']), 0, 100);
        }

        if (!empty($validated['model'])) {
            if (!$this->isValidModel($validated['model'])) {
                return response()->json([
                    'success' => false,
                    'error' => 'ไม่รู้จัก model: ' . $validated['model'],
                ], 422);
            }
            $session->model = $validated['model'];
        }

        $session->save();

        return response()->json([
            'success' => true,
            'session' => $this->serializeSession($session),
        ]);
    }

    /**
     * DELETE /api/ai-chat/sessions/{id} — soft delete
     */
    public function destroy($id)
    {
        $session = AiChatSession::forUser(Auth::id())->findOrFail($id);
        $session->delete();

        return response()->json(['success' => true]);
    }

    /**
     * GET /api/ai-chat/stats — สถิติ token ของ user (วันนี้ / ทั้งหมด / แยก model)
     * cost คำนวณฝั่ง client จาก pricing ใน config/ai-chat.php
     */
    public function stats()
    {
        $base = AiChatMessage::query()
            ->join('ai_chat_sessions', 'ai_chat_sessions.id', '=', 'ai_chat_messages.ai_chat_session_id')
            ->where('ai_chat_sessions.user_id', Auth::id())
            ->where('ai_chat_messages.role', 'assistant');

        $all = (clone $base)
            ->selectRaw('COUNT(*) as calls, COALESCE(SUM(prompt_tokens),0) as prompt, COALESCE(SUM(completion_tokens),0) as completion')
            ->first();

        $today = (clone $base)
            ->where('ai_chat_messages.created_at', '>=', Carbon::today('Asia/Bangkok'))
            ->selectRaw('COUNT(*) as calls, COALESCE(SUM(prompt_tokens),0) as prompt, COALESCE(SUM(completion_tokens),0) as completion')
            ->first();

        $byModel = (clone $base)
            ->selectRaw('ai_chat_messages.model, COUNT(*) as calls, COALESCE(SUM(prompt_tokens),0) as prompt, COALESCE(SUM(completion_tokens),0) as completion')
            ->whereNotNull('ai_chat_messages.model')
            ->groupBy('ai_chat_messages.model')
            ->orderByDesc('calls')
            ->get();

        return response()->json([
            'success' => true,
            'today' => [
                'calls' => (int) $today->calls,
                'prompt' => (int) $today->prompt,
                'completion' => (int) $today->completion,
            ],
            'all' => [
                'calls' => (int) $all->calls,
                'prompt' => (int) $all->prompt,
                'completion' => (int) $all->completion,
            ],
            'byModel' => $byModel->map(static function ($row) {
                return [
                    'model' => $row->model,
                    'calls' => (int) $row->calls,
                    'prompt' => (int) $row->prompt,
                    'completion' => (int) $row->completion,
                ];
            }),
        ]);
    }

    protected function serializeSession(AiChatSession $session): array
    {
        return [
            'id' => $session->id,
            'title' => $session->title,
            'model' => $session->model,
            'lastMessageAt' => $session->last_message_at?->toIso8601String(),
        ];
    }

    protected function isValidModel(string $model): bool
    {
        return collect(config('ai-chat.models'))->pluck('id')->contains($model);
    }

    protected function validModelOrDefault(?string $model): string
    {
        if ($model && $this->isValidModel($model)) {
            return $model;
        }

        return config('ai-chat.default_model');
    }
}
