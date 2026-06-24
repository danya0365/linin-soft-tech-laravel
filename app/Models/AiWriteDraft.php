<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ร่างคำสั่งสร้างข้อมูลที่รอผู้ใช้ยืนยัน (preview → confirm)
 * payload เก็บค่าที่ validate + resolve FK แล้ว — confirm จะ insert จากค่านี้เท่านั้น
 * ห้ามเชื่อค่าจากโมเดลตอน confirm
 *
 * @property $id
 * @property $ai_chat_session_id
 * @property $user_id
 * @property $entity_key
 * @property $payload
 * @property $preview
 * @property $created_after_message_id
 * @property $status
 * @property $record_id
 * @property $expires_at
 */
class AiWriteDraft extends Model
{
    protected $fillable = [
        'ai_chat_session_id',
        'user_id',
        'entity_key',
        'payload',
        'preview',
        'created_after_message_id',
        'status',
        'record_id',
        'expires_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'expires_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(AiChatSession::class, 'ai_chat_session_id');
    }
}
