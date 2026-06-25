<?php

namespace App\Services;

use App\Enums\IncomeType;
use App\Enums\OperationStatus;
use App\Enums\OperationType;
use App\Managers\ExpenseManager;
use App\Managers\IncomeManager;
use App\Managers\OperationManager;
use App\Models\AiChatSession;
use App\Models\AiWriteAudit;
use App\Models\AiWriteDraft;
use App\Models\Customer;
use App\Models\Department;
use App\Models\DepartmentDailyCostLog;
use App\Models\DryerMachine;
use App\Models\Employee;
use App\Models\EnergyResource;
use App\Models\EnergyResourceLog;
use App\Models\Inventory;
use App\Models\InventoryStockLog;
use App\Models\LinenProduct;
use App\Models\Operation;
use App\Models\OperationLinenProduct;
use App\Models\Truck;
use App\Models\User;
use App\Models\WashingMachine;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * จัดการ "งานเดินเอกสาร/ธุรกรรม" (operational actions) ที่ AI สั่งผ่านแชท
 * ขับด้วย OperationActionRegistry — reuse draft/confirm-gate/permission/audit ของ EntityWriteService
 *
 * prepare(): validate + เช็คสิทธิ์ + เช็ค FK + preview + เก็บ draft (entity_key="action:<key>")
 * run():    เรียกจาก EntityWriteService::confirm หลังผ่านด่านกัน auto-confirm — ทำ side-effect จริง
 *           ห่อ DB::transaction + reuse manager เดิม (ExpenseManager/IncomeManager/OperationManager)
 *
 * ทุก method คืน string เสมอ (ไม่ throw)
 */
class OperationActionService
{
    /** energy_resource_id → หน่วย + ประเภทรายจ่าย (ผูก 1:1 ตาม EnergyResourceNameId/ExpenseType) */
    private const ENERGY_MAP = [
        1 => ['unit' => 'litre',   'expense' => 'water'],
        2 => ['unit' => 'kw/hour', 'expense' => 'electricity'],
        3 => ['unit' => 'kg/gas',  'expense' => 'gas'],
        4 => ['unit' => 'kg',      'expense' => 'biomass'],
        5 => ['unit' => 'litre',   'expense' => 'fuel_oil'],
        6 => ['unit' => 'litre',   'expense' => 'petrol'],
        7 => ['unit' => 'litre',   'expense' => 'chemical'],
    ];

    /** operation_type → คอลัมน์ pivot ที่เก็บค่าของขั้นตอนนั้น */
    private const STEP_FIELD = [
        'wash' => 'wet_weight',
        'dry' => 'dry_weight',
        'iron' => 'iron_piece',
        'packing' => 'packing_piece',
        'collect' => 'collect_weight',
    ];

    private const TYPE_LABEL = [
        'wash' => 'ซัก', 'dry' => 'อบ', 'iron' => 'รีด', 'packing' => 'แพ็ค', 'collect' => 'เก็บ',
    ];

    /**
     * ขั้นที่ 1: เตรียมทำรายการ + preview ให้ผู้ใช้ยืนยัน (ยังไม่ทำจริง)
     */
    public function prepare(User $actor, AiChatSession $session, string $key, array $args): string
    {
        $cfg = OperationActionRegistry::get($key);
        if (!$cfg) {
            return "ไม่รู้จักงาน \"{$key}\" — งานที่ทำได้: " . implode(', ', OperationActionRegistry::keys());
        }

        if (!EntityWriteService::can($actor, $cfg['role'])) {
            return $this->denyMessage($cfg);
        }

        $validator = Validator::make($args, $cfg['rules']);
        if ($validator->fails()) {
            return "ข้อมูลไม่ครบหรือไม่ถูกต้อง:\n- " . implode("\n- ", $validator->errors()->all())
                . "\nกรุณาถามข้อมูลที่ขาดจากผู้ใช้";
        }

        $resolved = $this->resolve($key, $args); // [payload, preview] หรือ string(error)
        if (is_string($resolved)) {
            return $resolved;
        }
        [$payload, $preview] = $resolved;

        $draft = EntityWriteService::createDraft($actor, $session, "action:{$key}", 'run', $payload, $preview, null);

        return "พร้อม{$cfg['label']}ตามนี้:\n{$preview}\n\n" . $this->confirmInstruction($draft->id);
    }

    /**
     * ขั้นที่ 2: ทำจริง — เรียกจาก EntityWriteService::confirm (ผ่านด่านกัน auto-confirm มาแล้ว)
     */
    public function run(User $actor, AiChatSession $session, AiWriteDraft $draft): string
    {
        $key = substr($draft->entity_key, strlen('action:'));
        $cfg = OperationActionRegistry::get($key);
        if (!$cfg) {
            return "ไม่รู้จักงาน \"{$key}\" แล้ว ไม่สามารถดำเนินการได้";
        }

        if (!EntityWriteService::can($actor, $cfg['role'])) {
            return $this->denyMessage($cfg);
        }

        // recheck FK ยังอยู่ครบ (อาจถูกลบหลัง prepare) — reuse resolve()
        $resolved = $this->resolve($key, $draft->payload);
        if (is_string($resolved)) {
            return "ทำรายการไม่ได้: {$resolved}";
        }

        try {
            $recordId = DB::transaction(fn () => $this->execute($key, $draft->payload));
        } catch (\Throwable $e) {
            Log::warning('OperationActionService run failed', ['key' => $key, 'error' => $e->getMessage()]);

            return "เกิดข้อผิดพลาดขณะทำรายการ ({$cfg['label']}): {$e->getMessage()}";
        }

        $draft->update(['status' => 'confirmed', 'record_id' => $recordId]);

        AiWriteAudit::create([
            'user_id' => $actor->id,
            'ai_chat_session_id' => $session->id,
            'entity_key' => $draft->entity_key,
            'action' => 'run',
            'record_id' => $recordId,
            'payload' => $draft->payload,
        ]);

        return "{$cfg['label']}เรียบร้อยแล้ว (id: {$recordId})";
    }

    // ── resolve (validate FK + normalize payload + build preview) ────────────

    /** @return array{0: array, 1: string}|string */
    private function resolve(string $key, array $args)
    {
        return match ($key) {
            'log_energy' => $this->resolveLogEnergy($args),
            'stock_in' => $this->resolveStockIn($args),
            'stock_out' => $this->resolveStockOut($args),
            'billing' => $this->resolveBilling($args),
            'department_expense' => $this->resolveDepartmentExpense($args),
            'operation' => $this->resolveOperation($args),
            'deliver' => $this->resolveDeliver($args),
            default => "ไม่รู้จักงาน \"{$key}\"",
        };
    }

    private function resolveLogEnergy(array $a)
    {
        $id = (int) $a['energy_resource_id'];
        if (!isset(self::ENERGY_MAP[$id])) {
            return "ทรัพยากรพลังงาน id={$id} ยังไม่รองรับการบันทึกผ่านแชท (รองรับ id 1-7)";
        }
        $resource = EnergyResource::find($id);
        if (!$resource) {
            return "ไม่พบทรัพยากรพลังงาน id={$id} กรุณาเรียก list_entities kind=energy_resources ก่อน";
        }
        $empName = '-';
        if (!empty($a['employee_id'])) {
            $emp = Employee::find($a['employee_id']);
            if (!$emp) {
                return "ไม่พบพนักงาน id={$a['employee_id']} กรุณาเรียก search_employees ก่อน";
            }
            $empName = $emp->name;
        }
        $date = $a['created_at'] ?? $this->today();
        $unit = self::ENERGY_MAP[$id]['unit'];
        $payload = [
            'energy_resource_id' => $id,
            'value' => $a['value'],
            'cost' => $a['cost'],
            'created_at' => $date,
            'employee_id' => $a['employee_id'] ?? null,
            'lot_number' => $a['lot_number'] ?? null,
        ];
        $preview = "{$resource->name} (id {$id}) | {$a['value']} {$unit} | ค่าใช้จ่าย {$a['cost']} บาท | วันที่ {$date} | พนักงาน: {$empName}";

        return [$payload, $preview];
    }

    private function resolveStockIn(array $a)
    {
        $inv = Inventory::find($a['inventory_id']);
        if (!$inv) {
            return "ไม่พบสต๊อก id={$a['inventory_id']} กรุณาเรียก search_inventories ก่อน";
        }
        $date = $a['created_at'] ?? $this->today();
        $after = $inv->remain_quantity + $a['quantity'];
        $payload = ['inventory_id' => $inv->id, 'quantity' => $a['quantity'], 'created_at' => $date];
        $preview = "รับเข้า: {$inv->name} (id {$inv->id}) | จำนวน {$a['quantity']} {$inv->unit} | คงเหลือ {$inv->remain_quantity}→{$after} | วันที่ {$date}";

        return [$payload, $preview];
    }

    private function resolveStockOut(array $a)
    {
        $inv = Inventory::find($a['inventory_id']);
        if (!$inv) {
            return "ไม่พบสต๊อก id={$a['inventory_id']} กรุณาเรียก search_inventories ก่อน";
        }
        $date = $a['created_at'] ?? $this->today();
        $after = $inv->remain_quantity - $a['quantity'];
        $warn = $after < 0 ? ' ⚠️ เกินคงเหลือ จะติดลบ' : '';
        $payload = ['inventory_id' => $inv->id, 'quantity' => $a['quantity'], 'cost' => $a['cost'], 'created_at' => $date];
        $preview = "เบิกออก: {$inv->name} (id {$inv->id}) | จำนวน {$a['quantity']} {$inv->unit} | ต้นทุน {$a['cost']} บาท | คงเหลือ {$inv->remain_quantity}→{$after}{$warn} | วันที่ {$date}";

        return [$payload, $preview];
    }

    private function resolveBilling(array $a)
    {
        $cust = Customer::find($a['customer_id']);
        if (!$cust) {
            return "ไม่พบลูกค้า id={$a['customer_id']} กรุณาเรียก search_customers ก่อน";
        }
        $editWeight = $a['total_edit_weight'] ?? 0;
        $payload = [
            'customer_id' => $cust->id,
            'total_billing_weight' => $a['total_billing_weight'],
            'total_billing_payment' => $a['total_billing_payment'],
            'billing_payment_date' => $a['billing_payment_date'],
            'total_edit_weight' => $editWeight,
        ];
        $preview = "ออกบิล: {$cust->name} (id {$cust->id}) | น้ำหนัก {$a['total_billing_weight']} กก. | เรียกเก็บ {$a['total_billing_payment']} บาท | วันเก็บเงิน {$a['billing_payment_date']}";

        return [$payload, $preview];
    }

    private function resolveDepartmentExpense(array $a)
    {
        $dept = Department::find($a['department_id']);
        if (!$dept) {
            return "ไม่พบแผนก id={$a['department_id']} กรุณาเรียก list_entities kind=departments ก่อน";
        }
        $date = $a['daily_date'] ?? $this->today();
        $note = !empty($a['message']) ? " | หมายเหตุ: {$a['message']}" : '';
        $payload = ['department_id' => $dept->id, 'cost' => $a['cost'], 'daily_date' => $date, 'message' => $a['message'] ?? null];
        $preview = "ค่าใช้จ่ายแผนก: {$dept->name} (id {$dept->id}) | {$a['cost']} บาท | วันที่ {$date}{$note}";

        return [$payload, $preview];
    }

    private function resolveOperation(array $a)
    {
        $type = $a['operation_type'];
        $emp = Employee::find($a['employee_id']);
        if (!$emp) {
            return "ไม่พบพนักงาน id={$a['employee_id']} กรุณาเรียก search_employees ก่อน";
        }
        $cust = Customer::find($a['customer_id']);
        if (!$cust) {
            return "ไม่พบลูกค้า id={$a['customer_id']} กรุณาเรียก search_customers ก่อน";
        }
        $date = $a['operation_date'] ?? $this->today();

        $machineId = null;
        if ($type === 'wash' && !empty($a['washing_machine_id'])) {
            if (!WashingMachine::find($a['washing_machine_id'])) {
                return "ไม่พบเครื่องซัก id={$a['washing_machine_id']} กรุณาเรียก get_machine_list type=washing ก่อน";
            }
            $machineId = (int) $a['washing_machine_id'];
        }
        if ($type === 'dry' && !empty($a['dryer_machine_id'])) {
            if (!DryerMachine::find($a['dryer_machine_id'])) {
                return "ไม่พบเครื่องอบ id={$a['dryer_machine_id']} กรุณาเรียก get_machine_list type=dryer ก่อน";
            }
            $machineId = (int) $a['dryer_machine_id'];
        }

        // validate ทุก item + เตรียมรายการ preview
        $unit = in_array($type, ['iron', 'packing']) ? 'ชิ้น' : 'กก.';
        $items = [];
        $missing = [];
        $lines = [];
        $total = 0;
        foreach ($a['items'] as $it) {
            $lp = LinenProduct::find($it['linen_product_id']);
            if (!$lp) {
                $missing[] = $it['linen_product_id'];
                continue;
            }
            $item = [
                'linen_product_id' => (int) $it['linen_product_id'],
                'linen_case' => $it['linen_case'],
                'color' => $it['color'],
                'amount' => $it['amount'],
            ];
            if ($type === 'collect') {
                $item['collect_pack'] = $it['collect_pack'] ?? 0;
            }
            $items[] = $item;
            $total += $it['amount'];
            $caseLabel = $it['linen_case'] === 'edit' ? 'เคสแก้ไข' : 'เคสใหม่';
            $packText = $type === 'collect' ? ' | แพ็ค ' . ($it['collect_pack'] ?? 0) : '';
            $lines[] = "- {$lp->name} (id {$lp->id}) | {$caseLabel} | สี {$it['color']} | {$it['amount']} {$unit}{$packText}";
        }
        if (!empty($missing)) {
            return "ไม่พบผลิตภัณฑ์ผ้า id: " . implode(', ', $missing) . " กรุณาเรียก list_linen_products เพื่อหา id ที่ถูกต้องก่อน";
        }

        $payload = [
            'operation_type' => $type,
            'employee_id' => (int) $a['employee_id'],
            'customer_id' => (int) $a['customer_id'],
            'operation_date' => $date,
            'washing_machine_id' => $type === 'wash' ? $machineId : null,
            'dryer_machine_id' => $type === 'dry' ? $machineId : null,
            'items' => $items,
        ];
        $typeLabel = self::TYPE_LABEL[$type] ?? $type;
        $preview = "สร้างงาน{$typeLabel} ให้ {$cust->name} | พนักงาน {$emp->name} | วันที่ {$date}\n"
            . "รายการผ้า:\n" . implode("\n", $lines) . "\nรวม: {$total} {$unit}";

        return [$payload, $preview];
    }

    private function resolveDeliver(array $a)
    {
        $emp = Employee::find($a['employee_id']);
        if (!$emp) {
            return "ไม่พบพนักงาน id={$a['employee_id']} กรุณาเรียก search_employees ก่อน";
        }
        if (!empty($a['truck_id']) && !Truck::find($a['truck_id'])) {
            return "ไม่พบรถ id={$a['truck_id']} กรุณาเรียก get_machine_list type=truck ก่อน";
        }
        $date = $a['deliver_date'] ?? $this->today();

        $items = [];
        $invalid = [];
        $lines = [];
        $total = 0;
        foreach ($a['items'] as $it) {
            $olp = OperationLinenProduct::with(['linenProduct:id,name', 'operation' => function ($q) {
                $q->with('customer:id,name');
            }])->find($it['operation_linen_product_id']);

            // ต้องเป็นรายการผ้าจากงานเก็บ (collect) ที่ปิดแล้ว และยังไม่ถูกส่ง
            if (!$olp || !$olp->operation
                || $olp->operation->operation_type !== 'collect'
                || $olp->operation->status !== 'close'
                || $olp->deliver_operation_id) {
                $invalid[] = $it['operation_linen_product_id'];
                continue;
            }
            $items[] = [
                'operation_linen_product_id' => (int) $it['operation_linen_product_id'],
                'deliver_pack' => (int) $it['deliver_pack'],
            ];
            $total += (int) $it['deliver_pack'];
            $custName = $olp->operation->customer->name ?? '-';
            $lpName = $olp->linenProduct->name ?? ('id ' . $olp->linen_product_id);
            $lines[] = "- {$lpName} (รายการ id {$olp->id}) | ลูกค้า {$custName} | ส่ง {$it['deliver_pack']} แพ็ค";
        }
        if (!empty($invalid)) {
            return "รายการผ้า id: " . implode(', ', $invalid) . " ใช้ไม่ได้ "
                . "(ต้องเป็นงานเก็บ collect ที่ปิดแล้วและยังไม่ถูกส่ง) กรุณาเรียก list_deliverable_collect_items ก่อน";
        }

        $payload = [
            'employee_id' => (int) $a['employee_id'],
            'truck_id' => !empty($a['truck_id']) ? (int) $a['truck_id'] : null,
            'deliver_date' => $date,
            'items' => $items,
        ];
        $truckText = !empty($a['truck_id']) ? " | รถ id {$a['truck_id']}" : '';
        $preview = "สร้างงานส่ง | พนักงาน {$emp->name}{$truckText} | วันที่ {$date}\n"
            . "รายการ:\n" . implode("\n", $lines) . "\nรวม {$total} แพ็ค";

        return [$payload, $preview];
    }

    // ── execute (side-effect จริง — เรียกใน DB::transaction) ─────────────────

    private function execute(string $key, array $p): int
    {
        return match ($key) {
            'log_energy' => $this->executeLogEnergy($p),
            'stock_in' => $this->executeStockIn($p),
            'stock_out' => $this->executeStockOut($p),
            'billing' => $this->executeBilling($p),
            'department_expense' => $this->executeDepartmentExpense($p),
            'operation' => $this->executeOperation($p),
            'deliver' => $this->executeDeliver($p),
        };
    }

    private function executeLogEnergy(array $p): int
    {
        $map = self::ENERGY_MAP[$p['energy_resource_id']];
        $log = new EnergyResourceLog();
        $log->energy_resource_id = $p['energy_resource_id'];
        $log->employee_id = $p['employee_id'] ?? null;
        $log->value = $p['value'];
        $log->cost = $p['cost'];
        $log->unit = $map['unit'];
        $log->lot_number = $p['lot_number'] ?? null;
        $log->timestamps = false;
        $log->created_at = Carbon::parse($p['created_at']);
        $log->updated_at = Carbon::now();
        $log->save();

        ExpenseManager::create($map['expense'], $log, $log->cost, $log->created_at);

        return $log->id;
    }

    private function executeStockIn(array $p): int
    {
        $inv = Inventory::findOrFail($p['inventory_id']);
        $log = new InventoryStockLog();
        $log->inventory_id = $inv->id;
        $log->type = 'import';
        $log->quantity = $p['quantity'];
        $log->employee_id = null;
        $log->cost = 0;
        $log->timestamps = false;
        $log->created_at = Carbon::parse($p['created_at']);
        $log->updated_at = Carbon::now();
        $log->save();

        $inv->total_quantity += $p['quantity'];
        $inv->remain_quantity += $p['quantity'];
        $inv->save();

        return $log->id;
    }

    private function executeStockOut(array $p): int
    {
        $inv = Inventory::findOrFail($p['inventory_id']);
        $log = new InventoryStockLog();
        $log->inventory_id = $inv->id;
        $log->type = 'export';
        $log->quantity = $p['quantity'];
        $log->employee_id = null;
        $log->cost = $p['cost'];
        $log->timestamps = false;
        $log->created_at = Carbon::parse($p['created_at']);
        $log->updated_at = Carbon::now();
        $log->save();

        $inv->remain_quantity -= $p['quantity'];
        $inv->save();

        ExpenseManager::create('inventory', $log, $log->cost, $log->created_at);

        return $log->id;
    }

    private function executeBilling(array $p): int
    {
        $op = new Operation();
        $op->operation_type = OperationType::Payment;
        $op->status = OperationStatus::Close;
        $op->customer_id = $p['customer_id'];
        $op->total_billing_weight = $p['total_billing_weight'];
        $op->total_edit_weight = $p['total_edit_weight'] ?? 0;
        $op->total_billing_payment = $p['total_billing_payment'];
        $op->billing_payment_date = $p['billing_payment_date']; // ไม่อยู่ใน fillable → property-assign
        // daily summary คีย์ตาม created_at — ตั้งให้ตรงวันเก็บเงิน เพื่อให้ยอด billing ลงวันถูก
        $op->created_at = Carbon::parse($p['billing_payment_date']);
        $op->save();

        OperationManager::createCustomerOperationDailySummary($op);
        IncomeManager::create(IncomeType::CustomerBilling, $op, $op->total_billing_payment, $op->billing_payment_date);

        return $op->id;
    }

    private function executeDepartmentExpense(array $p): int
    {
        $log = new DepartmentDailyCostLog();
        $log->department_id = $p['department_id'];
        $log->cost = $p['cost'];
        $log->daily_date = $p['daily_date'];
        $log->message = $p['message'] ?? null; // ไม่อยู่ใน fillable → property-assign
        $log->save();

        ExpenseManager::create('department-salary', $log, $log->cost, $log->daily_date);

        return $log->id;
    }

    private function executeOperation(array $p): int
    {
        $createdAt = Carbon::parse($p['operation_date']);
        $type = $p['operation_type'];

        $op = new Operation();
        $op->operation_type = $type;
        $op->employee_id = $p['employee_id'];
        $op->{$type . '_employee_id'} = $p['employee_id'];
        $op->customer_id = $p['customer_id'];
        $op->status = OperationStatus::InProgress;
        if ($type === 'wash' && !empty($p['washing_machine_id'])) {
            $op->washing_machine_id = $p['washing_machine_id'];
        }
        if ($type === 'dry' && !empty($p['dryer_machine_id'])) {
            $op->dryer_machine_id = $p['dryer_machine_id'];
        }
        $op->created_at = $createdAt;
        $op->save();

        $field = self::STEP_FIELD[$type];
        foreach ($p['items'] as $it) {
            $olp = new OperationLinenProduct();
            $olp->operation_id = $op->id;
            $olp->linen_product_id = $it['linen_product_id'];
            $olp->linen_case = $it['linen_case'];
            $olp->color = $it['color'];
            $olp->{$field} = $it['amount'];
            if ($type === 'collect') {
                $olp->collect_pack = $it['collect_pack'] ?? 0;
            }
            $olp->created_at = $createdAt; // ต้องตรงกับวันงาน ไม่งั้น daily summary นับไม่เจอ
            $olp->updated_at = Carbon::now();
            $olp->save();
        }

        $op->updateRelateFields();           // re-sum pivot → header totals (save ในตัว)
        $op->status = OperationStatus::Close;
        $op->save();
        OperationManager::createCustomerOperationDailySummary($op); // นับเฉพาะ status=close

        return $op->id;
    }

    private function executeDeliver(array $p): int
    {
        // งานส่งไม่มี customer_id (รวมผ้าหลายลูกค้า) — มิเรอร์ DeliverController
        $op = new Operation();
        $op->operation_type = OperationType::Deliver;
        $op->status = OperationStatus::InProgress;
        $op->employee_id = $p['employee_id'];
        $op->deliver_employee_id = $p['employee_id']; // ไม่อยู่ใน fillable → property-assign
        if (!empty($p['truck_id'])) {
            $op->truck_id = $p['truck_id']; // ไม่อยู่ใน fillable → property-assign
        }
        $op->created_at = Carbon::parse($p['deliver_date']);
        $op->save();

        foreach ($p['items'] as $it) {
            // ผูก deliver_pack เข้ากับ pivot row ของงานเก็บ (guard กันถูกส่งซ้ำ)
            OperationLinenProduct::where('id', $it['operation_linen_product_id'])
                ->whereNull('deliver_operation_id')
                ->update([
                    'deliver_pack' => $it['deliver_pack'],
                    'deliver_operation_id' => $op->id,
                ]);
        }

        $op->status = OperationStatus::Close;
        $op->save();
        // มิเรอร์ controller: deliver op ไม่มี customer_id → summary ไม่ถูกปรับ (พฤติกรรมเดียวกับหน้าจอ)
        OperationManager::createCustomerOperationDailySummary($op);

        return $op->id;
    }

    // ── helpers ──────────────────────────────────────────────────────────────

    private function today(): string
    {
        return Carbon::now('Asia/Bangkok')->format('Y-m-d');
    }

    private function denyMessage(array $cfg): string
    {
        return "คุณไม่มีสิทธิ์{$cfg['label']} (ต้องมีสิทธิ์ระดับ {$cfg['role']}) หากต้องการ กรุณาติดต่อผู้ดูแลระบบ";
    }

    private function confirmInstruction(int $draftId): string
    {
        return "ให้แสดงรายละเอียดนี้แก่ผู้ใช้และถามยืนยัน เมื่อผู้ใช้ตอบยืนยันในข้อความถัดไป "
            . "จึงเรียก confirm_write ด้วย draft_id={$draftId} ห้ามเรียก confirm_write ในรอบนี้";
    }
}
