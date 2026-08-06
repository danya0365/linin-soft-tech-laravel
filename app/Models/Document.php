<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * เอกสารที่ staff อัปโหลด (รูป/PDF/office) — เนื้อหาที่สกัดได้ให้ AI agent ใช้ตอบ
 * status: pending (รอประมวลผล) → ready (พร้อมให้ AI ใช้) | failed (อ่านไม่ได้)
 */
class Document extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_READY = 'ready';
    public const STATUS_FAILED = 'failed';

    public const KIND_IMAGE = 'image';
    public const KIND_PDF = 'pdf';
    public const KIND_OFFICE = 'office';

    protected $fillable = [
        'user_id', 'title', 'original_filename', 'mime_type', 'extension',
        'size_bytes', 'storage_path', 'kind', 'status', 'fail_reason',
        'extracted_text', 'chunks_json', 'page_count', 'extracted_chars',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
        'page_count' => 'integer',
        'extracted_chars' => 'integer',
        'chunks_json' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeReady($query)
    {
        return $query->where('status', self::STATUS_READY);
    }

    /**
     * Chunks เนื้อหาที่สกัดได้ (กรองเฉพาะที่มีคำค้น — กันส่งเนื้อหาทั้งหมดไป)
     */
    public function chunks(?string $query = null): array
    {
        $chunks = $this->chunks_json ?: [];

        if ($query === null || $query === '') {
            return $chunks;
        }

        $terms = preg_split('/[\s,]+/u', mb_strtolower(trim($query))) ?: [];
        $terms = array_filter(array_map('trim', $terms), fn ($t) => mb_strlen($t) > 0);

        if (empty($terms)) {
            return $chunks;
        }

        return array_values(array_filter($chunks, function ($chunk) use ($terms) {
            $lower = mb_strtolower((string) ($chunk['text'] ?? ''));
            foreach ($terms as $term) {
                if (mb_strpos($lower, $term) !== false) {
                    return true;
                }
            }
            return false;
        }));
    }
}
