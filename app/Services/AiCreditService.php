<?php

namespace App\Services;

use App\Models\AiChatMessage;
use App\Models\AiCreditTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * AI Credit Service (หน่วยบาท)
 *
 * - หักเครดิตตามใช้จริง: ต้นทุน USD × usd_to_thb × (1 + commission%/100)
 * - ค่าคอม (commission) คือส่วนต่างที่เป็นรายได้ dev — เก็บแยกใน ledger
 * - ทุกการเปลี่ยนยอดผ่าน applyTransaction (DB transaction + lockForUpdate)
 *   และเขียน ledger (ai_credit_transactions) เสมอเพื่อ audit
 */
class AiCreditService
{
    public function balance(int $userId): float
    {
        return (float) (User::query()->whereKey($userId)->value('ai_credit_balance') ?? 0);
    }

    public function hasCredit(int $userId): bool
    {
        return $this->balance($userId) > 0;
    }

    /**
     * ต้นทุนจริงเป็น USD จาก pricing ใน config/ai-chat.php
     * model ที่ไม่รู้จัก → fallback_pricing (กันใช้ฟรี)
     */
    public function costUsd(?string $model, int $promptTokens, int $completionTokens): float
    {
        $pricing = collect(config('ai-chat.models'))->firstWhere('id', $model)['pricing']
            ?? config('ai-chat.fallback_pricing');

        return ($promptTokens / 1_000_000) * (float) $pricing['inputPerMTok']
            + ($completionTokens / 1_000_000) * (float) $pricing['outputPerMTok'];
    }

    /**
     * ต้นทุนจริงเป็นบาท (ยังไม่รวมค่าคอม)
     */
    public function costThb(?string $model, int $promptTokens, int $completionTokens): float
    {
        return round(
            $this->costUsd($model, $promptTokens, $completionTokens) * (float) config('ai-chat.usd_to_thb'),
            4
        );
    }

    /**
     * ยอดที่หักจริง = ต้นทุนบาท × (1 + ค่าคอม%/100)
     */
    public function chargeThb(?string $model, int $promptTokens, int $completionTokens): float
    {
        $multiplier = 1 + ((float) config('ai-chat.commission_percent')) / 100;

        return round($this->costThb($model, $promptTokens, $completionTokens) * $multiplier, 4);
    }

    /**
     * หักเครดิตจากคำตอบ assistant ที่บันทึกแล้ว — ยอมให้ยอดติดลบ
     * (pre-flight กันไว้ที่ balance > 0 ก่อนเริ่ม; ยอดสุดท้ายรู้หลังตอบจบ)
     */
    public function charge(AiChatMessage $message): ?AiCreditTransaction
    {
        $userId = $message->session()->withTrashed()->value('user_id');
        if (!$userId) {
            return null;
        }

        $cost = $this->costThb($message->model, (int) $message->prompt_tokens, (int) $message->completion_tokens);
        $total = $this->chargeThb($message->model, (int) $message->prompt_tokens, (int) $message->completion_tokens);
        $commission = round($total - $cost, 4);

        return $this->applyTransaction(
            (int) $userId,
            -$total,
            AiCreditTransaction::TYPE_USAGE,
            $message->id,
            null,
            'AI usage: ' . ($message->model ?: 'unknown'),
            $cost,
            $commission
        );
    }

    /**
     * เติมเครดิต (admin) — amount ต้อง > 0
     */
    public function topUp(int $userId, float $amount, int $adminId, ?string $note = null): AiCreditTransaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Top-up amount must be positive');
        }

        return $this->applyTransaction($userId, $amount, AiCreditTransaction::TYPE_TOPUP, null, $adminId, $note);
    }

    /**
     * ปรับยอด (admin) — amount เป็น signed (ลบได้ เช่น แก้เติมผิด)
     */
    public function adjust(int $userId, float $amount, int $adminId, ?string $note = null): AiCreditTransaction
    {
        if ($amount == 0.0) {
            throw new \InvalidArgumentException('Adjust amount must not be zero');
        }

        return $this->applyTransaction($userId, $amount, AiCreditTransaction::TYPE_ADJUST, null, $adminId, $note);
    }

    /**
     * แกนกลาง: lock แถว user → คำนวณยอดใหม่ → เขียน ledger
     */
    protected function applyTransaction(
        int $userId,
        float $amount,
        string $type,
        ?int $messageId = null,
        ?int $createdBy = null,
        ?string $note = null,
        ?float $costThb = null,
        ?float $commissionThb = null
    ): AiCreditTransaction {
        return DB::transaction(function () use ($userId, $amount, $type, $messageId, $createdBy, $note, $costThb, $commissionThb) {
            $user = User::query()->lockForUpdate()->findOrFail($userId);

            $newBalance = round((float) $user->ai_credit_balance + $amount, 4);
            $user->ai_credit_balance = $newBalance;
            $user->save();

            return AiCreditTransaction::create([
                'user_id' => $userId,
                'amount' => round($amount, 4),
                'balance_after' => $newBalance,
                'type' => $type,
                'cost_thb' => $costThb,
                'commission_thb' => $commissionThb,
                'ai_chat_message_id' => $messageId,
                'created_by' => $createdBy,
                'note' => $note,
            ]);
        });
    }
}
