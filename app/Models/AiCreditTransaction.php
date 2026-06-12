<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiCreditTransaction extends Model
{
    public const TYPE_TOPUP = 'topup';
    public const TYPE_USAGE = 'usage';
    public const TYPE_ADJUST = 'adjust';

    protected $fillable = [
        'user_id',
        'amount',
        'balance_after',
        'type',
        'cost_thb',
        'commission_thb',
        'ai_chat_message_id',
        'created_by',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'balance_after' => 'decimal:4',
        'cost_thb' => 'decimal:4',
        'commission_thb' => 'decimal:4',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function message()
    {
        return $this->belongsTo(AiChatMessage::class, 'ai_chat_message_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
