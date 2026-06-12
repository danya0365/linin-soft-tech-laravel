<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiChatMessage extends Model
{
    protected $fillable = [
        'ai_chat_session_id',
        'role',
        'content',
        'model',
        'prompt_tokens',
        'completion_tokens',
        'is_estimated',
        'is_partial',
    ];

    protected $casts = [
        'is_estimated' => 'boolean',
        'is_partial' => 'boolean',
    ];

    public function session()
    {
        return $this->belongsTo(AiChatSession::class, 'ai_chat_session_id');
    }
}
