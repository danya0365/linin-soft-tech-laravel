<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * บันทึก audit ว่าใครสั่งสร้างข้อมูลอะไรผ่าน AI (เก็บ snapshot payload)
 *
 * @property $id
 * @property $user_id
 * @property $ai_chat_session_id
 * @property $entity_key
 * @property $record_id
 * @property $payload
 * @property $created_at
 */
class AiWriteAudit extends Model
{
    /** มีเฉพาะ created_at */
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'ai_chat_session_id',
        'entity_key',
        'action',
        'record_id',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
