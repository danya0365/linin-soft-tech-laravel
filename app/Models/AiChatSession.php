<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AiChatSession extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'channel', 'title', 'model', 'summary', 'summary_until_message_id', 'last_message_at'];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function messages()
    {
        return $this->hasMany(AiChatMessage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
