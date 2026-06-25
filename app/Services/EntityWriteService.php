<?php

namespace App\Services;

use App\Models\AiChatSession;
use App\Models\AiWriteAudit;
use App\Models\AiWriteDraft;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

/**
 * Generic writer สำหรับให้ AI จัดการ master data ผ่านแชท — CRUD (create/update/delete)
 *
 * ขับด้วย EntityWriteRegistry — เพิ่มเอนทิตีใหม่ไม่ต้องแก้ service นี้
 * ทุก action เป็น two-step (preview → confirm) ความปลอดภัย 2 ชั้น:
 *  - prepare*(): validate + เช็คสิทธิ์ + เช็ค FK/dependents + เก็บ draft (ยังไม่แตะ DB)
 *  - confirm(): ต้องมี user message ใหม่หลัง prepare (กัน AI auto-confirm) แล้วจึงทำจริงจาก draft ฝั่ง server
 *    แตกตาม draft.action: create=Model::create, update=Model::update, delete=Model::delete (soft)
 *  - delete กันเข้มสุด: บล็อกถ้ามี dependent ผูกอยู่ (เช็คทั้งตอน prepare และ confirm)
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

        $draft = $this->createDraft($actor, $session, $entityKey, 'create', $payload, $preview, null);

        return "พร้อมสร้าง{$cfg['label']}ตามนี้:\n{$preview}\n\n"
            . $this->confirmInstruction($draft->id);
    }

    /**
     * ขั้นที่ 1 (update): เตรียมแก้ไขเฉพาะฟิลด์ที่ระบุ + แสดง preview แบบ diff (ไม่แก้จริง)
     */
    public function prepareUpdate(User $actor, AiChatSession $session, string $entityKey, int $id, array $args): string
    {
        $cfg = EntityWriteRegistry::get($entityKey);
        if (!$cfg) {
            return "ไม่รู้จักชนิดข้อมูล \"{$entityKey}\" — ชนิดที่แก้ไขได้: " . implode(', ', EntityWriteRegistry::keys());
        }

        if (!$this->can($actor, $cfg['role'])) {
            return $this->denyMessage($cfg, 'แก้ไข');
        }

        $model = $cfg['model'];
        $record = $model::find($id);
        if (!$record) {
            return "ไม่พบ{$cfg['label']} id={$id} กรุณาเรียก search_*/list_entities หา id ที่ถูกต้องก่อน";
        }

        // เก็บเฉพาะฟิลด์ที่ประกาศไว้ + ตัด skip → เป็น "ฟิลด์ที่จะแก้"
        $changes = array_intersect_key($args, array_flip(array_keys($cfg['fields'])));
        foreach (($cfg['skip'] ?? []) as $skipField) {
            unset($changes[$skipField]);
        }
        if (empty($changes)) {
            return "ไม่มีฟิลด์ที่จะแก้ — ระบุค่าที่ต้องการเปลี่ยนอย่างน้อย 1 ฟิลด์ (เช่น name)";
        }

        // FK ที่ถูกแก้ ต้องมีอยู่จริง
        foreach (($cfg['fks'] ?? []) as $field => $fkModel) {
            if (array_key_exists($field, $changes) && !empty($changes[$field])
                && !$fkModel::whereKey($changes[$field])->exists()) {
                return "ไม่พบ {$field} = {$changes[$field]} ในระบบ "
                    . "กรุณาเรียก list_entities หรือ search_* เพื่อหา id ที่ถูกต้องก่อน";
            }
        }

        // validate แบบ partial: รวมค่าเดิม (เฉพาะฟิลด์ที่แก้ได้) กับค่าที่แก้ แล้ว validate ทั้งก้อน
        $editable = array_keys($cfg['fields']);
        $current = array_intersect_key($record->only($editable), array_flip($editable));
        $merged = array_merge($current, $changes);
        $validator = Validator::make($merged, $this->rulesFor($cfg));
        if ($validator->fails()) {
            return "ข้อมูลไม่ถูกต้อง:\n- " . implode("\n- ", $validator->errors()->all());
        }

        $preview = $this->buildUpdatePreview($cfg, $record, $changes);

        $draft = $this->createDraft($actor, $session, $entityKey, 'update', $changes, $preview, $id);

        return "พร้อมแก้ไข{$cfg['label']} (id {$id}) ตามนี้:\n{$preview}\n\n"
            . $this->confirmInstruction($draft->id);
    }

    /**
     * ขั้นที่ 1 (delete): เตรียมลบ — กันลบเข้มสุดถ้ามีข้อมูลลูกผูกอยู่ (ไม่ลบจริง)
     */
    public function prepareDelete(User $actor, AiChatSession $session, string $entityKey, int $id): string
    {
        $cfg = EntityWriteRegistry::get($entityKey);
        if (!$cfg) {
            return "ไม่รู้จักชนิดข้อมูล \"{$entityKey}\" — ชนิดที่ลบได้: " . implode(', ', EntityWriteRegistry::keys());
        }

        if (!$this->can($actor, $cfg['role'])) {
            return $this->denyMessage($cfg, 'ลบ');
        }

        $model = $cfg['model'];
        $record = $model::find($id);
        if (!$record) {
            return "ไม่พบ{$cfg['label']} id={$id} อาจถูกลบไปแล้ว";
        }

        $blockers = $this->dependentBlockers($cfg, $id);
        if (!empty($blockers)) {
            return "ลบ{$cfg['label']} (id {$id}) ไม่ได้ เพราะมีข้อมูลอื่นผูกอยู่:\n"
                . $this->blockerLines($blockers)
                . "\nต้องย้าย/ลบข้อมูลเหล่านี้ก่อน จึงจะลบได้";
        }

        $snapshot = $record->only(array_keys($cfg['fields']));
        $preview = $this->buildPreview($cfg, $snapshot);

        $draft = $this->createDraft($actor, $session, $entityKey, 'delete', $snapshot, $preview, $id);

        return "พร้อมลบ{$cfg['label']} (id {$id}) ตามนี้:\n{$preview}\n\n"
            . "เตือนผู้ใช้ว่าการลบจะกู้คืนยาก แล้ว" . $this->confirmInstruction($draft->id);
    }

    /** สร้าง draft (ใช้ร่วมกันทุก action) */
    protected function createDraft(User $actor, AiChatSession $session, string $entityKey, string $action, array $payload, string $preview, ?int $recordId): AiWriteDraft
    {
        return AiWriteDraft::create([
            'ai_chat_session_id' => $session->id,
            'user_id' => $actor->id,
            'entity_key' => $entityKey,
            'action' => $action,
            'payload' => $payload,
            'preview' => $preview,
            'record_id' => $recordId,
            'created_after_message_id' => (int) ($session->messages()->max('id') ?? 0),
            'status' => 'pending',
            'expires_at' => now()->addMinutes(self::DRAFT_TTL_MINUTES),
        ]);
    }

    protected function confirmInstruction(int $draftId): string
    {
        return "ให้แสดงรายละเอียดนี้แก่ผู้ใช้และถามยืนยัน เมื่อผู้ใช้ตอบยืนยันในข้อความถัดไป "
            . "จึงเรียก confirm_write ด้วย draft_id={$draftId} ห้ามเรียก confirm_write ในรอบนี้";
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
                . "แล้วค่อยเรียก confirm_write อีกครั้งในข้อความถัดไป";
        }

        $cfg = EntityWriteRegistry::get($draft->entity_key);
        if (!$cfg) {
            return "ไม่รู้จักชนิดข้อมูล \"{$draft->entity_key}\" แล้ว ไม่สามารถดำเนินการได้";
        }

        // เช็คสิทธิ์ซ้ำ (role อาจเปลี่ยนระหว่างเทิร์น)
        if (!$this->can($actor, $cfg['role'])) {
            return $this->denyMessage($cfg, $this->actionVerb($draft->action));
        }

        return match ($draft->action) {
            'update' => $this->applyUpdate($actor, $session, $draft, $cfg),
            'delete' => $this->applyDelete($actor, $session, $draft, $cfg),
            default => $this->applyCreate($actor, $session, $draft, $cfg),
        };
    }

    /** confirm → create */
    protected function applyCreate(User $actor, AiChatSession $session, AiWriteDraft $draft, array $cfg): string
    {
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = $cfg['model'];
        $record = $model::create($draft->payload); // ใช้ payload ฝั่ง server เท่านั้น

        $draft->update(['status' => 'confirmed', 'record_id' => $record->id]);
        $this->writeAudit($actor, $session, $draft, $record->id);

        return "สร้าง{$cfg['label']}เรียบร้อยแล้ว (id: {$record->id})";
    }

    /** confirm → update */
    protected function applyUpdate(User $actor, AiChatSession $session, AiWriteDraft $draft, array $cfg): string
    {
        $model = $cfg['model'];
        $record = $model::find($draft->record_id);
        if (!$record) {
            $draft->update(['status' => 'expired']);

            return "ไม่พบ{$cfg['label']} id={$draft->record_id} แล้ว อาจถูกลบไปก่อน ยกเลิกการแก้ไข";
        }

        // FK ที่จะแก้ ต้องยังมีอยู่ (กันถูกลบหลัง prepare)
        foreach (($cfg['fks'] ?? []) as $field => $fkModel) {
            if (array_key_exists($field, $draft->payload) && !empty($draft->payload[$field])
                && !$fkModel::whereKey($draft->payload[$field])->exists()) {
                return "ไม่พบ {$field} = {$draft->payload[$field]} แล้ว ยกเลิกการแก้ไข กรุณาเริ่มใหม่";
            }
        }

        $record->update($draft->payload);

        $draft->update(['status' => 'confirmed']);
        $this->writeAudit($actor, $session, $draft, $record->id);

        return "แก้ไข{$cfg['label']} (id {$record->id}) เรียบร้อยแล้ว";
    }

    /** confirm → delete (soft delete) */
    protected function applyDelete(User $actor, AiChatSession $session, AiWriteDraft $draft, array $cfg): string
    {
        $model = $cfg['model'];
        $record = $model::find($draft->record_id);
        if (!$record) {
            $draft->update(['status' => 'confirmed']);

            return "{$cfg['label']} id={$draft->record_id} ถูกลบไปแล้ว";
        }

        // เช็ค dependents ซ้ำ (อาจมีลูกเพิ่มหลัง prepare)
        $blockers = $this->dependentBlockers($cfg, (int) $draft->record_id);
        if (!empty($blockers)) {
            return "ลบ{$cfg['label']} (id {$draft->record_id}) ไม่ได้แล้ว เพราะมีข้อมูลอื่นผูกอยู่:\n"
                . $this->blockerLines($blockers);
        }

        $record->delete();

        $draft->update(['status' => 'confirmed']);
        $this->writeAudit($actor, $session, $draft, (int) $draft->record_id);

        return "ลบ{$cfg['label']} (id {$draft->record_id}) เรียบร้อยแล้ว (สามารถกู้คืนได้หากต้องการ)";
    }

    protected function writeAudit(User $actor, AiChatSession $session, AiWriteDraft $draft, int $recordId): void
    {
        AiWriteAudit::create([
            'user_id' => $actor->id,
            'ai_chat_session_id' => $session->id,
            'entity_key' => $draft->entity_key,
            'action' => $draft->action,
            'record_id' => $recordId,
            'payload' => $draft->payload,
        ]);
    }

    protected function actionVerb(string $action): string
    {
        return match ($action) {
            'update' => 'แก้ไข',
            'delete' => 'ลบ',
            default => 'สร้าง',
        };
    }

    /**
     * นับ dependent ที่บล็อกการลบ (strict) — คืน [['label'=>, 'count'=>], ...] ของตัวที่มี > 0
     */
    protected function dependentBlockers(array $cfg, int $id): array
    {
        $blockers = [];
        foreach (($cfg['dependents'] ?? []) as $dep) {
            $model = $dep['model'];
            $columns = (array) $dep['column'];
            $query = $model::query();
            $query->where(function ($q) use ($columns, $id) {
                foreach ($columns as $col) {
                    $q->orWhere($col, $id);
                }
            });
            $count = $query->count();
            if ($count > 0) {
                $blockers[] = ['label' => $dep['label'], 'count' => $count];
            }
        }

        return $blockers;
    }

    protected function blockerLines(array $blockers): string
    {
        return collect($blockers)
            ->map(fn ($b) => "- {$b['label']}: {$b['count']} รายการ")
            ->implode("\n");
    }

    /**
     * รายการเอนทิตีที่ user คนนี้มีสิทธิ์สร้าง (ใช้แสดงเช็คลิสต์ความสามารถใน UI)
     * คืน [['key' => ..., 'label' => ...], ...] เฉพาะตัวที่ผ่าน can()
     */
    public function writableEntitiesFor(User $u): array
    {
        $result = [];
        foreach (EntityWriteRegistry::all() as $key => $cfg) {
            if ($this->can($u, $cfg['role'])) {
                $result[] = ['key' => $key, 'label' => $cfg['label']];
            }
        }

        return $result;
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

    protected function denyMessage(array $cfg, string $verb = 'สร้าง'): string
    {
        return "คุณไม่มีสิทธิ์{$verb}{$cfg['label']} (ต้องมีสิทธิ์ระดับ {$cfg['role']}) "
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

    /**
     * preview แบบ diff สำหรับ update — แสดงเฉพาะฟิลด์ที่เปลี่ยน (เดิม → ใหม่)
     */
    protected function buildUpdatePreview(array $cfg, $record, array $changes): string
    {
        $lines = [];
        foreach ($changes as $field => $newValue) {
            $oldDisplay = $this->displayValue($cfg, $field, $record->{$field});
            $newDisplay = $this->displayValue($cfg, $field, $newValue);
            $lines[] = "- {$field}: {$oldDisplay} → {$newDisplay}";
        }

        return implode("\n", $lines);
    }

    /** แปลงค่าให้อ่านง่าย + resolve ชื่อ FK */
    protected function displayValue(array $cfg, string $field, $value): string
    {
        if (isset($cfg['fks'][$field]) && !empty($value)) {
            $fkModel = $cfg['fks'][$field];
            $related = $fkModel::find($value);
            if ($related && isset($related->name)) {
                return "{$related->name} (id {$value})";
            }
        }

        return (string) $value;
    }
}
