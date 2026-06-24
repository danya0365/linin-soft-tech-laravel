<?php

namespace App\Services;

use App\Models\AiChatSession;
use App\Models\AiWriteAudit;
use App\Models\AiWriteDraft;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

/**
 * Generic writer สำหรับให้ AI สร้างข้อมูล master data ผ่านแชท (เฟส 1: insert อย่างเดียว)
 *
 * ขับด้วย EntityWriteRegistry — เพิ่มเอนทิตีใหม่ไม่ต้องแก้ service นี้
 * ความปลอดภัย 2 ชั้น:
 *  - prepare(): validate + เช็คสิทธิ์ + เช็ค FK + เก็บ draft (ยังไม่ insert)
 *  - confirm(): ต้องมี user message ใหม่หลัง prepare (กัน AI auto-confirm) แล้วจึง insert จาก payload ฝั่ง server
 *
 * ทุก method คืน string เสมอ (ไม่ throw) ตามสัญญาของ AiChatService::executeTool
 */
class EntityWriteService
{
    /** อายุ draft ก่อนหมดอายุ (นาที) */
    protected const DRAFT_TTL_MINUTES = 30;

    /**
     * ขั้นที่ 1: เตรียมสร้างข้อมูล + แสดง preview ให้ผู้ใช้ยืนยัน (ไม่ insert จริง)
     */
    public function prepare(User $actor, AiChatSession $session, string $entityKey, array $args): string
    {
        $cfg = EntityWriteRegistry::get($entityKey);
        if (!$cfg) {
            return "ไม่รู้จักชนิดข้อมูล \"{$entityKey}\" — ชนิดที่สร้างได้: " . implode(', ', EntityWriteRegistry::keys());
        }

        if (!$this->can($actor, $cfg['role'])) {
            return $this->denyMessage($cfg);
        }

        // เก็บเฉพาะ field ที่ประกาศไว้ + ตัด field ที่ skip (เช่น photo)
        $payload = array_intersect_key($args, array_flip(array_keys($cfg['fields'])));
        foreach (($cfg['skip'] ?? []) as $skipField) {
            unset($payload[$skipField]);
        }

        // validate ด้วย rules ของโมเดล (ลบ field ที่ skip ออก)
        $validator = Validator::make($payload, $this->rulesFor($cfg));
        if ($validator->fails()) {
            return "ข้อมูลไม่ครบหรือไม่ถูกต้อง:\n- " . implode("\n- ", $validator->errors()->all())
                . "\nกรุณาถามข้อมูลที่ขาดจากผู้ใช้";
        }

        // เช็คว่า FK มีอยู่จริง
        foreach (($cfg['fks'] ?? []) as $field => $fkModel) {
            if (!empty($payload[$field]) && !$fkModel::whereKey($payload[$field])->exists()) {
                return "ไม่พบ {$field} = {$payload[$field]} ในระบบ "
                    . "กรุณาเรียก list_entities หรือ search_* เพื่อหา id ที่ถูกต้องก่อน";
            }
        }

        $preview = $this->buildPreview($cfg, $payload);

        // เติมค่า default ของคอลัมน์ที่ skip แต่ NOT NULL (เช่น photo) — ไม่โชว์ใน preview
        $payload = array_merge($cfg['defaults'] ?? [], $payload);

        $draft = AiWriteDraft::create([
            'ai_chat_session_id' => $session->id,
            'user_id' => $actor->id,
            'entity_key' => $entityKey,
            'payload' => $payload,
            'preview' => $preview,
            'created_after_message_id' => (int) ($session->messages()->max('id') ?? 0),
            'status' => 'pending',
            'expires_at' => now()->addMinutes(self::DRAFT_TTL_MINUTES),
        ]);

        return "พร้อมสร้าง{$cfg['label']}ตามนี้:\n{$preview}\n\n"
            . "ให้แสดงรายละเอียดนี้แก่ผู้ใช้และถามยืนยัน เมื่อผู้ใช้ตอบยืนยันในข้อความถัดไป "
            . "จึงเรียก confirm_create_entity ด้วย draft_id={$draft->id} "
            . "ห้ามเรียก confirm_create_entity ในรอบนี้";
    }

    /**
     * ขั้นที่ 2: ยืนยันและ insert จริงจาก payload ที่เก็บไว้ฝั่ง server
     */
    public function confirm(User $actor, AiChatSession $session, int $draftId): string
    {
        $draft = AiWriteDraft::where('ai_chat_session_id', $session->id)
            ->where('user_id', $actor->id)
            ->find($draftId);

        if (!$draft) {
            return "ไม่พบรายการรอยืนยัน (draft_id={$draftId}) กรุณาเริ่มสร้างใหม่";
        }
        if ($draft->status !== 'pending') {
            return "รายการนี้ถูกใช้ไปแล้ว (สถานะ: {$draft->status}) ไม่สามารถยืนยันซ้ำได้";
        }
        if ($draft->expires_at && $draft->expires_at->isPast()) {
            $draft->update(['status' => 'expired']);

            return "รายการรอยืนยันหมดอายุแล้ว กรุณาเริ่มสร้างใหม่";
        }

        // กัน auto-confirm: ต้องมี user message ใหม่หลังตอน prepare (= ผู้ใช้ตอบยืนยันจริง)
        $hasNewUserMessage = $session->messages()
            ->where('role', 'user')
            ->where('id', '>', $draft->created_after_message_id)
            ->exists();
        if (!$hasNewUserMessage) {
            return "ยังไม่ได้รับการยืนยันจากผู้ใช้ กรุณาแสดง preview ถามผู้ใช้ก่อน "
                . "แล้วค่อยเรียก confirm_create_entity อีกครั้งในข้อความถัดไป";
        }

        $cfg = EntityWriteRegistry::get($draft->entity_key);
        if (!$cfg) {
            return "ไม่รู้จักชนิดข้อมูล \"{$draft->entity_key}\" แล้ว ไม่สามารถสร้างได้";
        }

        // เช็คสิทธิ์ซ้ำ (role อาจเปลี่ยนระหว่างเทิร์น)
        if (!$this->can($actor, $cfg['role'])) {
            return $this->denyMessage($cfg);
        }

        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = $cfg['model'];
        $record = $model::create($draft->payload); // ใช้ payload ฝั่ง server เท่านั้น

        $draft->update(['status' => 'confirmed', 'record_id' => $record->id]);

        AiWriteAudit::create([
            'user_id' => $actor->id,
            'ai_chat_session_id' => $session->id,
            'entity_key' => $draft->entity_key,
            'record_id' => $record->id,
            'payload' => $draft->payload,
        ]);

        return "สร้าง{$cfg['label']}เรียบร้อยแล้ว (id: {$record->id})";
    }

    /**
     * เช็คสิทธิ์ตาม role ขั้นต่ำ — ไต่สิทธิ์แบบเดียวกับ middleware Is* (admin ทำได้ทุกอย่าง)
     * อ่าน flag เป็น boolean ตรงๆ กัน null (คอลัมน์อาจเป็น null สำหรับ user ที่ไม่ได้ตั้งค่า)
     */
    protected function can(User $u, string $role): bool
    {
        $isAdmin = (bool) $u->is_can_access_admin;
        $isManager = (bool) $u->is_can_access_manager;
        $isSupervisor = (bool) $u->is_can_access_supervisor;
        $isWorker = (bool) $u->is_can_access_worker;

        return match ($role) {
            'admin' => $isAdmin,
            'manager' => $isManager || $isAdmin,
            'supervisor' => $isSupervisor || $isManager || $isAdmin,
            'worker' => $isWorker || $isAdmin,
            default => false,
        };
    }

    protected function denyMessage(array $cfg): string
    {
        return "คุณไม่มีสิทธิ์สร้าง{$cfg['label']} (ต้องมีสิทธิ์ระดับ {$cfg['role']}) "
            . "หากต้องการ กรุณาติดต่อผู้ดูแลระบบ";
    }

    /**
     * rules ของโมเดล ลบ field ที่ skip ออก (เช่น photo ที่อัปโหลดผ่านแชทไม่ได้)
     */
    protected function rulesFor(array $cfg): array
    {
        $model = $cfg['model'];
        $rules = $model::$rules ?? [];
        foreach (($cfg['skip'] ?? []) as $skipField) {
            unset($rules[$skipField]);
        }

        return $rules;
    }

    /**
     * ข้อความ preview — แสดง field:value พร้อม resolve ชื่อ FK ให้ผู้ใช้อ่านง่าย
     */
    protected function buildPreview(array $cfg, array $payload): string
    {
        $lines = [];
        foreach ($payload as $field => $value) {
            $display = (string) $value;

            // ถ้าเป็น FK ลองดึงชื่อ record มาประกอบ
            if (isset($cfg['fks'][$field])) {
                $fkModel = $cfg['fks'][$field];
                $related = $fkModel::find($value);
                if ($related && isset($related->name)) {
                    $display = "{$related->name} (id {$value})";
                }
            }

            $lines[] = "- {$field}: {$display}";
        }

        return implode("\n", $lines);
    }
}
