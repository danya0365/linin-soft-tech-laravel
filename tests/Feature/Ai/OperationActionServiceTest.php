<?php

namespace Tests\Feature\Ai;

use App\Models\AiChatSession;
use App\Models\AiWriteDraft;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EnergyResource;
use App\Models\Inventory;
use App\Models\InventoryGroup;
use App\Models\LinenProduct;
use App\Models\LinenType;
use App\Models\Operation;
use App\Models\OperationLinenProduct;
use App\Models\User;
use App\Services\EntityWriteService;
use App\Services\OperationActionService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * ทดสอบ OperationActionService — ให้ AI ทำงาน operational ผ่านแชท
 * confirm ผ่าน EntityWriteService::confirm เพื่อทดสอบ branch "action:" จริง
 */
class OperationActionServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected OperationActionService $actions;
    protected EntityWriteService $writer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actions = app(OperationActionService::class);
        $this->writer = app(EntityWriteService::class);
    }

    protected function admin(): User
    {
        // admin ผ่าน can() ทุก role (worker/supervisor) ด้วย role-climbing
        return User::factory()->create(['is_can_access_admin' => true]);
    }

    protected function makeSession(User $user): AiChatSession
    {
        return AiChatSession::create(['user_id' => $user->id, 'model' => 'minimax/minimax-m2.7']);
    }

    protected function userSays(AiChatSession $session, string $text = 'ยืนยัน'): void
    {
        $session->messages()->create(['role' => 'user', 'content' => $text]);
    }

    /** prepare → userSays → confirm (ครบ flow) คืนผล confirm */
    protected function runFlow(User $actor, AiChatSession $session, string $key, array $args): string
    {
        $this->actions->prepare($actor, $session, $key, $args);
        $draft = AiWriteDraft::latest('id')->first();
        $this->userSays($session);

        return $this->writer->confirm($actor, $session, $draft->id);
    }

    public function test_energy_log_creates_log_and_expense(): void
    {
        $admin = $this->admin();
        $session = $this->makeSession($admin);
        EnergyResource::firstOrCreate(['id' => 1], ['name' => 'น้ำ']);

        $result = $this->runFlow($admin, $session, 'log_energy', [
            'energy_resource_id' => 1,
            'value' => 1200,
            'cost' => 850,
            'created_at' => '2026-06-20',
        ]);

        $this->assertStringContainsString('เรียบร้อย', $result);
        $log = \App\Models\EnergyResourceLog::where('energy_resource_id', 1)->where('value', 1200)->first();
        $this->assertNotNull($log);
        $this->assertSame('litre', $log->unit);
        $this->assertDatabaseHas('expenses', [
            'type_name' => 'water',
            'table_name' => 'energy_resource_logs',
            'table_id' => $log->id,
        ]);
    }

    public function test_stock_in_increases_quantity_no_expense(): void
    {
        $admin = $this->admin();
        $session = $this->makeSession($admin);
        $group = InventoryGroup::create(['name' => 'เคมี']);
        $inv = Inventory::create(['inventory_group_id' => $group->id, 'name' => 'ผงซักฟอก', 'unit' => 'ลิตร', 'total_quantity' => 100, 'remain_quantity' => 100]);

        $result = $this->runFlow($admin, $session, 'stock_in', [
            'inventory_id' => $inv->id,
            'quantity' => 50,
            'created_at' => '2026-06-20',
        ]);

        $this->assertStringContainsString('เรียบร้อย', $result);
        $this->assertSame(150.0, (float) $inv->fresh()->total_quantity);
        $this->assertSame(150.0, (float) $inv->fresh()->remain_quantity);
        $this->assertDatabaseHas('inventory_stock_logs', ['inventory_id' => $inv->id, 'type' => 'import']);
        $this->assertDatabaseMissing('expenses', ['table_name' => 'inventory_stock_logs', 'type_name' => 'inventory', 'table_id' => $inv->id]);
    }

    public function test_stock_out_decreases_remain_and_creates_expense(): void
    {
        $admin = $this->admin();
        $session = $this->makeSession($admin);
        $group = InventoryGroup::create(['name' => 'เคมี']);
        $inv = Inventory::create(['inventory_group_id' => $group->id, 'name' => 'ผงซักฟอก', 'unit' => 'ลิตร', 'total_quantity' => 100, 'remain_quantity' => 100]);

        $result = $this->runFlow($admin, $session, 'stock_out', [
            'inventory_id' => $inv->id,
            'quantity' => 30,
            'cost' => 600,
            'created_at' => '2026-06-20',
        ]);

        $this->assertStringContainsString('เรียบร้อย', $result);
        $this->assertSame(70.0, (float) $inv->fresh()->remain_quantity);
        $log = \App\Models\InventoryStockLog::where('inventory_id', $inv->id)->where('type', 'export')->first();
        $this->assertNotNull($log);
        $this->assertDatabaseHas('expenses', ['type_name' => 'inventory', 'table_name' => 'inventory_stock_logs', 'table_id' => $log->id]);
    }

    public function test_stock_out_over_remaining_warns_but_allows(): void
    {
        $admin = $this->admin();
        $session = $this->makeSession($admin);
        $group = InventoryGroup::create(['name' => 'เคมี']);
        $inv = Inventory::create(['inventory_group_id' => $group->id, 'name' => 'ผงซักฟอก', 'unit' => 'ลิตร', 'total_quantity' => 10, 'remain_quantity' => 10]);

        // preview ต้องเตือน
        $preview = $this->actions->prepare($admin, $session, 'stock_out', ['inventory_id' => $inv->id, 'quantity' => 30, 'cost' => 600]);
        $this->assertStringContainsString('เกินคงเหลือ', $preview);

        $draft = AiWriteDraft::latest('id')->first();
        $this->userSays($session);
        $this->writer->confirm($admin, $session, $draft->id);

        // ทำได้ คงเหลือติดลบ
        $this->assertSame(-20.0, (float) $inv->fresh()->remain_quantity);
    }

    public function test_billing_creates_payment_operation_income_and_summary(): void
    {
        $admin = $this->admin();
        $session = $this->makeSession($admin);
        $cgroup = CustomerGroup::create(['name' => 'โรงแรม']);
        $customer = Customer::create(['name' => 'โรงแรม A', 'customer_group_id' => $cgroup->id]);

        $result = $this->runFlow($admin, $session, 'billing', [
            'customer_id' => $customer->id,
            'total_billing_weight' => 500,
            'total_billing_payment' => 25000,
            'billing_payment_date' => '2026-06-20',
        ]);

        $this->assertStringContainsString('เรียบร้อย', $result);
        $op = Operation::where('customer_id', $customer->id)->where('operation_type', 'payment')->first();
        $this->assertNotNull($op);
        $this->assertSame('close', $op->status);
        $this->assertNotNull($op->billing_payment_date, 'billing_payment_date ต้องถูกบันทึก (non-fillable)');
        $this->assertDatabaseHas('incomes', ['type_name' => 'customer_billing', 'table_name' => 'operations', 'table_id' => $op->id]);
        $this->assertDatabaseHas('customer_operation_daily_summaries', ['customer_id' => $customer->id, 'operation_date' => '2026-06-20']);
    }

    public function test_create_operation_wash_two_items_totals_and_summary(): void
    {
        $admin = $this->admin();
        $session = $this->makeSession($admin);
        $cgroup = CustomerGroup::create(['name' => 'โรงแรม']);
        $customer = Customer::create(['name' => 'โรงแรม A', 'customer_group_id' => $cgroup->id]);
        $dept = Department::create(['var_name' => 'wash_dept', 'name' => 'ซัก', 'input_unit' => 'weight']);
        $emp = Employee::create(['code' => 'E1', 'name' => 'สมชาย', 'department_id' => $dept->id]);
        $ltype = LinenType::create(['name' => 'ผ้าปู']);
        $lp1 = LinenProduct::create(['linen_type_id' => $ltype->id, 'name' => 'ผ้าปูที่นอน']);
        $lp2 = LinenProduct::create(['linen_type_id' => $ltype->id, 'name' => 'ปลอกหมอน']);

        $result = $this->runFlow($admin, $session, 'operation', [
            'operation_type' => 'wash',
            'employee_id' => $emp->id,
            'customer_id' => $customer->id,
            'operation_date' => '2026-06-20',
            'items' => [
                ['linen_product_id' => $lp1->id, 'linen_case' => 'new', 'color' => 'ขาว', 'amount' => 12.5],
                ['linen_product_id' => $lp2->id, 'linen_case' => 'edit', 'color' => 'ฟ้า', 'amount' => 4.0],
            ],
        ]);

        $this->assertStringContainsString('เรียบร้อย', $result);
        $op = Operation::where('customer_id', $customer->id)->where('operation_type', 'wash')->first();
        $this->assertNotNull($op);
        $this->assertSame('close', $op->status);
        $this->assertSame($emp->id, $op->wash_employee_id);
        $this->assertSame(16.5, (float) $op->total_wet_weight);
        $this->assertSame(2, $op->linenProducts()->count());
        $this->assertDatabaseHas('customer_operation_daily_summaries', [
            'customer_id' => $customer->id,
            'operation_date' => '2026-06-20',
            'total_wet_weight' => 16.5,
        ]);
    }

    public function test_create_operation_collect_sets_collect_pack(): void
    {
        $admin = $this->admin();
        $session = $this->makeSession($admin);
        $cgroup = CustomerGroup::create(['name' => 'โรงแรม']);
        $customer = Customer::create(['name' => 'โรงแรม A', 'customer_group_id' => $cgroup->id]);
        $dept = Department::create(['var_name' => 'collect_dept', 'name' => 'เก็บ', 'input_unit' => 'weight']);
        $emp = Employee::create(['code' => 'E2', 'name' => 'สมหญิง', 'department_id' => $dept->id]);
        $ltype = LinenType::create(['name' => 'ผ้าเช็ดตัว']);
        $lp = LinenProduct::create(['linen_type_id' => $ltype->id, 'name' => 'ผ้าเช็ดตัวใหญ่']);

        $this->runFlow($admin, $session, 'operation', [
            'operation_type' => 'collect',
            'employee_id' => $emp->id,
            'customer_id' => $customer->id,
            'operation_date' => '2026-06-20',
            'items' => [
                ['linen_product_id' => $lp->id, 'linen_case' => 'new', 'color' => 'ขาว', 'amount' => 20, 'collect_pack' => 5],
            ],
        ]);

        $op = Operation::where('customer_id', $customer->id)->where('operation_type', 'collect')->first();
        $this->assertNotNull($op);
        $this->assertSame(20.0, (float) $op->total_collect_weight);
        $this->assertSame(5, (int) $op->total_collect_pack);
    }

    public function test_worker_without_supervisor_cannot_bill(): void
    {
        $worker = User::factory()->create(['is_can_access_worker' => true]);
        $session = $this->makeSession($worker);
        $cgroup = CustomerGroup::create(['name' => 'โรงแรม']);
        $customer = Customer::create(['name' => 'โรงแรม A', 'customer_group_id' => $cgroup->id]);

        $result = $this->actions->prepare($worker, $session, 'billing', [
            'customer_id' => $customer->id,
            'total_billing_weight' => 500,
            'total_billing_payment' => 25000,
            'billing_payment_date' => '2026-06-20',
        ]);

        $this->assertStringContainsString('ไม่มีสิทธิ์', $result);
        $this->assertSame(0, AiWriteDraft::count());
    }

    public function test_confirm_same_turn_is_rejected(): void
    {
        $admin = $this->admin();
        $session = $this->makeSession($admin);
        EnergyResource::firstOrCreate(['id' => 1], ['name' => 'น้ำ']);

        $this->actions->prepare($admin, $session, 'log_energy', ['energy_resource_id' => 1, 'value' => 1200, 'cost' => 850]);
        $draft = AiWriteDraft::latest('id')->first();

        // ไม่มี user message ใหม่ → ต้องปฏิเสธ ไม่มี side-effect
        $result = $this->writer->confirm($admin, $session, $draft->id);

        $this->assertStringContainsString('ยังไม่ได้รับการยืนยัน', $result);
        $this->assertDatabaseMissing('energy_resource_logs', ['value' => 1200, 'cost' => 850]);
    }

    // ── Deliver ──────────────────────────────────────────────────

    /** สร้างงานเก็บ (collect) ที่ปิดแล้ว พร้อม pivot 1 รายการ (ไว้เป็นของส่ง) */
    protected function closedCollectPivot(Customer $customer, Employee $emp, LinenProduct $lp): OperationLinenProduct
    {
        $op = new Operation();
        $op->operation_type = 'collect';
        $op->status = 'close';
        $op->customer_id = $customer->id;
        $op->employee_id = $emp->id;
        $op->save();

        $pivot = new OperationLinenProduct();
        $pivot->operation_id = $op->id;
        $pivot->linen_product_id = $lp->id;
        $pivot->linen_case = 'new';
        $pivot->color = 'ขาว';
        $pivot->collect_weight = 20;
        $pivot->collect_pack = 5;
        $pivot->save();

        return $pivot;
    }

    public function test_deliver_assigns_pack_to_collect_pivot(): void
    {
        $admin = $this->admin();
        $session = $this->makeSession($admin);
        $cgroup = CustomerGroup::create(['name' => 'โรงแรม']);
        $customer = Customer::create(['name' => 'โรงแรม A', 'customer_group_id' => $cgroup->id]);
        $dept = Department::create(['var_name' => 'deliver_dept', 'name' => 'ส่ง', 'input_unit' => 'pack']);
        $emp = Employee::create(['code' => 'E9', 'name' => 'สมศักดิ์', 'department_id' => $dept->id]);
        $ltype = LinenType::create(['name' => 'ผ้าเช็ดตัว']);
        $lp = LinenProduct::create(['linen_type_id' => $ltype->id, 'name' => 'ผ้าเช็ดตัว']);
        $pivot = $this->closedCollectPivot($customer, $emp, $lp);

        $result = $this->runFlow($admin, $session, 'deliver', [
            'employee_id' => $emp->id,
            'deliver_date' => '2026-06-20',
            'items' => [
                ['operation_linen_product_id' => $pivot->id, 'deliver_pack' => 5],
            ],
        ]);

        $this->assertStringContainsString('เรียบร้อย', $result);
        $deliverOp = Operation::where('operation_type', 'deliver')->latest('id')->first();
        $this->assertNotNull($deliverOp);
        $this->assertSame('close', $deliverOp->status);
        $this->assertSame($emp->id, $deliverOp->deliver_employee_id);

        $fresh = $pivot->fresh();
        $this->assertSame(5, (int) $fresh->deliver_pack);
        $this->assertSame($deliverOp->id, (int) $fresh->deliver_operation_id);
    }

    public function test_deliver_rejects_non_deliverable_item(): void
    {
        $admin = $this->admin();
        $session = $this->makeSession($admin);
        $dept = Department::create(['var_name' => 'deliver_dept', 'name' => 'ส่ง', 'input_unit' => 'pack']);
        $emp = Employee::create(['code' => 'E9', 'name' => 'สมศักดิ์', 'department_id' => $dept->id]);

        // id ที่ไม่มีจริง → ใช้ไม่ได้
        $result = $this->actions->prepare($admin, $session, 'deliver', [
            'employee_id' => $emp->id,
            'items' => [
                ['operation_linen_product_id' => 999999, 'deliver_pack' => 3],
            ],
        ]);

        $this->assertStringContainsString('ใช้ไม่ได้', $result);
        $this->assertSame(0, AiWriteDraft::count());
    }
}
