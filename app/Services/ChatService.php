<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeOperationLog;
use App\Models\EnergyResource;
use App\Models\EnergyResourceLog;
use App\Models\Inventory;
use App\Models\InventoryGroup;
use App\Models\Operation;
use App\Models\WashingMachine;
use App\Models\DryerMachine;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;

/**
 * Chat Service - Single Source of Truth
 * 
 * จัดการ business logic สำหรับ chat ทั้ง LINE และ Web
 * ใช้โดย LineChatbotController และ WebChatController
 */
class ChatService
{
    /**
     * Main menu commands
     */
    protected array $mainMenuCommands = [
        '📊 สรุปวันนี้',
        '👥 ลูกค้า',
        '📦 สต๊อก',
        '⚡ พลังงาน',
        '👷 พนักงาน',
        '⚙️ เครื่องจักร',
        '📈 รายงาน',
    ];

    /**
     * ประมวลผลคำสั่ง - จุดเข้าหลัก
     */
    public function processCommand(string $text): array
    {
        $lowerText = mb_strtolower(trim($text));
        $text = trim($text);

        // Menu command
        if (in_array($text, ['เมนู', 'menu', 'help', 'ช่วยเหลือ', 'start'])) {
            return $this->getMainMenu();
        }

        // Today summary
        if (str_contains($text, 'สรุปวันนี้') || $lowerText === 'summary') {
            return $this->getTodaySummary();
        }

        // Customer
        if (str_contains($text, 'ลูกค้า') || $lowerText === 'customer') {
            return $this->getCustomerMenu();
        }

        // Inventory
        if (str_contains($text, 'สต๊อก') || $lowerText === 'stock' || $lowerText === 'inventory') {
            return $this->getInventoryMenu();
        }

        // Energy
        if (str_contains($text, 'พลังงาน') || $lowerText === 'energy') {
            return $this->getEnergyMenu();
        }

        // Employee
        if (str_contains($text, 'พนักงาน') || $lowerText === 'employee') {
            return $this->getEmployeeMenu();
        }

        // Machine
        if (str_contains($text, 'เครื่องจักร') || $lowerText === 'machine') {
            return $this->getMachineStatus();
        }

        // Report
        if (str_contains($text, 'รายงาน') || $lowerText === 'report') {
            return $this->getReportMenu();
        }

        // Unknown command - show menu
        return $this->getMainMenu("ขอโทษครับ ไม่เข้าใจคำสั่ง \"$text\"\n\nกรุณาเลือกเมนูด้านล่าง:");
    }

    /**
     * ประมวลผล action (postback)
     */
    public function processAction(string $action, array $params = []): array
    {
        switch ($action) {
            case 'customer_group':
                return $this->getCustomersByGroup($params['id'] ?? null);
            case 'customer_detail':
                return $this->getCustomerDetail($params['id'] ?? null);
            case 'inventory_group':
                return $this->getInventoriesByGroup($params['id'] ?? null);
            case 'energy_type':
                return $this->getEnergyLogs($params['id'] ?? null);
            case 'department':
                return $this->getEmployeesByDepartment($params['id'] ?? null);
            case 'employee_detail':
                return $this->getEmployeeDetail($params['id'] ?? null);
            case 'report_period':
                return $this->getReportSummary($params['period'] ?? 'all');
            default:
                return $this->getMainMenu();
        }
    }

    /**
     * ดึงเมนูหลัก
     */
    public function getMainMenu(?string $greeting = null): array
    {
        return [
            'type' => 'menu',
            'title' => '📋 เมนูหลัก LinenSoftTech',
            'text' => $greeting ?? "📋 เมนูหลัก LinenSoftTech\n\nกรุณาเลือกข้อมูลที่ต้องการ:",
            'quickReplies' => $this->mainMenuCommands,
        ];
    }

    /**
     * ดึงสรุปวันนี้
     */
    public function getTodaySummary(): array
    {
        $today = Carbon::today();

        // นับจำนวนลูกค้า
        $customerCount = Customer::count();

        // นับจำนวน Operations วันนี้
        $operationCount = Operation::whereDate('created_at', $today)->count();

        // สรุปพลังงานวันนี้
        $energyLogs = EnergyResourceLog::with('energyResource')
            ->whereDate('created_at', $today)
            ->get();

        $energySummary = [];
        foreach ($energyLogs as $log) {
            $name = $log->energyResource->name ?? 'ไม่ระบุ';
            if (!isset($energySummary[$name])) {
                $energySummary[$name] = 0;
            }
            $energySummary[$name] += $log->value;
        }

        return [
            'type' => 'card',
            'title' => '📊 สรุปวันนี้',
            'subtitle' => 'LinenSoftTech',
            'headerColor' => '#1DB446',
            'rows' => [
                ['label' => '📅 วันที่', 'value' => $today->format('d/m/Y')],
                ['label' => '👥 ลูกค้าทั้งหมด', 'value' => number_format($customerCount) . ' ราย'],
                ['label' => '🧺 งานวันนี้', 'value' => number_format($operationCount) . ' รายการ'],
                ['type' => 'separator'],
                ['label' => '⚡ พลังงานวันนี้', 'value' => '', 'bold' => true],
                ...(!empty($energySummary) 
                    ? array_map(fn($name, $value) => ['label' => $name, 'value' => number_format($value, 2)], array_keys($energySummary), array_values($energySummary))
                    : [['label' => '⚡ พลังงาน', 'value' => 'ยังไม่มีข้อมูล']]),
            ],
        ];
    }

    /**
     * ดึงเมนูลูกค้า
     */
    public function getCustomerMenu(): array
    {
        $groups = CustomerGroup::take(10)->get();

        if ($groups->isEmpty()) {
            return [
                'type' => 'text',
                'text' => '❌ ไม่พบข้อมูลกลุ่มลูกค้า',
            ];
        }

        return [
            'type' => 'menu',
            'title' => '👥 เลือกกลุ่มลูกค้า',
            'text' => '👥 เลือกกลุ่มลูกค้า:',
            'quickReplies' => $groups->map(fn($g) => [
                'label' => $g->name,
                'action' => 'customer_group',
                'data' => ['id' => $g->id],
            ])->toArray(),
        ];
    }

    /**
     * ดึงลูกค้าตามกลุ่ม
     */
    public function getCustomersByGroup($groupId): array
    {
        $customers = Customer::where('customer_group_id', $groupId)->take(10)->get();

        if ($customers->isEmpty()) {
            return [
                'type' => 'text',
                'text' => '❌ ไม่พบลูกค้าในกลุ่มนี้',
            ];
        }

        return [
            'type' => 'menu',
            'title' => '👥 เลือกลูกค้า',
            'text' => '👥 เลือกลูกค้า:',
            'quickReplies' => $customers->map(fn($c) => [
                'label' => $c->name,
                'action' => 'customer_detail',
                'data' => ['id' => $c->id],
            ])->toArray(),
        ];
    }

    /**
     * ดึงรายละเอียดลูกค้า
     */
    public function getCustomerDetail($customerId): array
    {
        $customer = Customer::find($customerId);

        if (!$customer) {
            return [
                'type' => 'text',
                'text' => '❌ ไม่พบข้อมูลลูกค้า',
            ];
        }

        return [
            'type' => 'card',
            'title' => '👥 ข้อมูลลูกค้า',
            'subtitle' => $customer->name,
            'headerColor' => '#0066CC',
            'rows' => [
                ['label' => '🏥 ชื่อ', 'value' => $customer->name],
                ['type' => 'separator'],
                ['label' => '💧 น้ำหนักเปียก', 'value' => number_format($customer->total_wet_weight, 2) . ' kg'],
                ['label' => '☀️ น้ำหนักแห้ง', 'value' => number_format($customer->total_dry_weight, 2) . ' kg'],
                ['label' => '📝 น้ำหนักแก้ไข', 'value' => number_format($customer->total_edit_weight, 2) . ' kg'],
                ['type' => 'separator'],
                ['label' => '💰 ยอดเงินรวม', 'value' => number_format($customer->total_billing_payment, 2) . ' ฿', 'valueColor' => '#1DB446'],
            ],
        ];
    }

    /**
     * ดึงเมนูสต๊อก
     */
    public function getInventoryMenu(): array
    {
        $groups = InventoryGroup::take(10)->get();

        if ($groups->isEmpty()) {
            return [
                'type' => 'text',
                'text' => '❌ ไม่พบข้อมูลกลุ่มวัตถุดิบ',
            ];
        }

        return [
            'type' => 'menu',
            'title' => '📦 เลือกประเภทวัตถุดิบ',
            'text' => '📦 เลือกประเภทวัตถุดิบ:',
            'quickReplies' => $groups->map(fn($g) => [
                'label' => $g->name,
                'action' => 'inventory_group',
                'data' => ['id' => $g->id],
            ])->toArray(),
        ];
    }

    /**
     * ดึงวัตถุดิบตามกลุ่ม
     */
    public function getInventoriesByGroup($groupId): array
    {
        $inventories = Inventory::where('inventory_group_id', $groupId)->take(10)->get();

        if ($inventories->isEmpty()) {
            return [
                'type' => 'text',
                'text' => '❌ ไม่พบวัตถุดิบในกลุ่มนี้',
            ];
        }

        $group = InventoryGroup::find($groupId);

        return [
            'type' => 'card',
            'title' => '📦 สต๊อกวัตถุดิบ',
            'subtitle' => $group->name ?? 'วัตถุดิบ',
            'headerColor' => '#FF6B35',
            'rows' => $inventories->map(function ($item) {
                $remainColor = $item->remain_quantity < ($item->total_quantity * 0.2) ? '#FF0000' : '#1DB446';
                return [
                    'label' => $item->name,
                    'value' => number_format($item->remain_quantity) . ' ' . $item->unit,
                    'valueColor' => $remainColor,
                ];
            })->toArray(),
        ];
    }

    /**
     * ดึงเมนูพลังงาน
     */
    public function getEnergyMenu(): array
    {
        $resources = EnergyResource::take(10)->get();

        if ($resources->isEmpty()) {
            return [
                'type' => 'text',
                'text' => '❌ ไม่พบข้อมูลพลังงาน',
            ];
        }

        return [
            'type' => 'menu',
            'title' => '⚡ เลือกประเภทพลังงาน',
            'text' => '⚡ เลือกประเภทพลังงาน:',
            'quickReplies' => $resources->map(fn($r) => [
                'label' => $r->name,
                'action' => 'energy_type',
                'data' => ['id' => $r->id],
            ])->toArray(),
        ];
    }

    /**
     * ดึง Log พลังงาน
     */
    public function getEnergyLogs($resourceId): array
    {
        $resource = EnergyResource::find($resourceId);

        if (!$resource) {
            return [
                'type' => 'text',
                'text' => '❌ ไม่พบข้อมูลพลังงาน',
            ];
        }

        $logs = EnergyResourceLog::where('energy_resource_id', $resourceId)
            ->orderBy('created_at', 'desc')
            ->take(7)
            ->get();

        $totalValue = $logs->sum('value');

        $rows = [
            ['label' => '📊 รวม 7 วัน', 'value' => number_format($totalValue, 2), 'valueColor' => '#1DB446'],
            ['type' => 'separator'],
        ];

        foreach ($logs as $log) {
            $date = Carbon::parse($log->created_at)->format('d/m');
            $rows[] = [
                'label' => $date,
                'value' => number_format($log->value, 2) . ' ' . ($log->unit ?? ''),
            ];
        }

        return [
            'type' => 'card',
            'title' => '⚡ ' . $resource->name,
            'subtitle' => 'ประวัติการใช้งาน',
            'headerColor' => '#FFD700',
            'rows' => $rows,
        ];
    }

    /**
     * ดึงเมนูพนักงาน
     */
    public function getEmployeeMenu(): array
    {
        $departments = Department::take(10)->get();

        if ($departments->isEmpty()) {
            return [
                'type' => 'text',
                'text' => '❌ ไม่พบข้อมูลแผนก',
            ];
        }

        return [
            'type' => 'menu',
            'title' => '👷 เลือกแผนก',
            'text' => '👷 เลือกแผนก:',
            'quickReplies' => $departments->map(fn($d) => [
                'label' => $d->name,
                'action' => 'department',
                'data' => ['id' => $d->id],
            ])->toArray(),
        ];
    }

    /**
     * ดึงพนักงานตามแผนก
     */
    public function getEmployeesByDepartment($deptId): array
    {
        $employees = Employee::where('department_id', $deptId)->take(10)->get();
        $department = Department::find($deptId);

        if ($employees->isEmpty()) {
            return [
                'type' => 'text',
                'text' => '❌ ไม่พบพนักงานในแผนกนี้',
            ];
        }

        $deptName = $department->name ?? 'ไม่ระบุ';

        return [
            'type' => 'menu',
            'title' => "👷 แผนก{$deptName}",
            'text' => "👷 แผนก{$deptName}\n\nเลือกพนักงานเพื่อดูสถิติ:",
            'quickReplies' => $employees->map(fn($e) => [
                'label' => $e->name,
                'action' => 'employee_detail',
                'data' => ['id' => $e->id],
            ])->toArray(),
        ];
    }

    /**
     * ดึงรายละเอียดพนักงาน
     */
    public function getEmployeeDetail($employeeId): array
    {
        $employee = Employee::with('department')->find($employeeId);

        if (!$employee) {
            return [
                'type' => 'text',
                'text' => '❌ ไม่พบข้อมูลพนักงาน',
            ];
        }

        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        // นับจำนวน Operation logs
        $todayLogs = EmployeeOperationLog::where('employee_id', $employeeId)
            ->whereDate('created_at', $today)
            ->count();

        $weekLogs = EmployeeOperationLog::where('employee_id', $employeeId)
            ->where('created_at', '>=', $thisWeek)
            ->count();

        $monthLogs = EmployeeOperationLog::where('employee_id', $employeeId)
            ->where('created_at', '>=', $thisMonth)
            ->count();

        // นับแยกตามประเภทงาน (วันนี้)
        $operationStats = EmployeeOperationLog::where('employee_id', $employeeId)
            ->whereDate('created_at', $today)
            ->selectRaw('operation_type, COUNT(*) as count')
            ->groupBy('operation_type')
            ->pluck('count', 'operation_type')
            ->toArray();

        $rows = [
            ['label' => '👤 ชื่อ', 'value' => $employee->name],
            ['label' => '🏢 แผนก', 'value' => $employee->department->name ?? 'ไม่ระบุ'],
            ['type' => 'separator'],
            ['label' => '📊 สถิติการทำงาน', 'value' => '', 'bold' => true],
            ['label' => '📅 วันนี้', 'value' => number_format($todayLogs) . ' ครั้ง', 'valueColor' => '#1DB446'],
            ['label' => '📆 สัปดาห์นี้', 'value' => number_format($weekLogs) . ' ครั้ง'],
            ['label' => '📅 เดือนนี้', 'value' => number_format($monthLogs) . ' ครั้ง'],
        ];

        // เพิ่มสถิติแยกตามประเภทงาน
        if (!empty($operationStats)) {
            $rows[] = ['type' => 'separator'];
            $rows[] = ['label' => '🔧 งานวันนี้ (แยกประเภท)', 'value' => '', 'bold' => true];

            $operationLabels = [
                'wash' => '🧺 ซัก',
                'dry' => '☀️ อบ',
                'iron' => '👔 รีด',
                'packing' => '📦 พับแพ็ค',
                'collect' => '🏠 จัดเก็บ',
            ];

            foreach ($operationStats as $type => $count) {
                $label = $operationLabels[$type] ?? $type;
                $rows[] = ['label' => $label, 'value' => number_format($count) . ' ครั้ง'];
            }
        }

        return [
            'type' => 'card',
            'title' => '👷 ข้อมูลพนักงาน',
            'subtitle' => $employee->name,
            'headerColor' => '#9B59B6',
            'rows' => $rows,
        ];
    }

    /**
     * ดึงสถานะเครื่องจักร
     */
    public function getMachineStatus(): array
    {
        $washingMachines = WashingMachine::take(10)->get();
        $dryerMachines = DryerMachine::take(10)->get();

        $rows = [
            ['label' => '🧺 เครื่องซัก', 'value' => '', 'bold' => true],
        ];

        foreach ($washingMachines as $machine) {
            $rows[] = [
                'label' => $machine->name,
                'value' => number_format($machine->maximum_weight) . ' kg',
            ];
        }

        $rows[] = ['type' => 'separator'];
        $rows[] = ['label' => '🌡️ เครื่องอบ', 'value' => '', 'bold' => true];

        foreach ($dryerMachines as $machine) {
            $rows[] = [
                'label' => $machine->name,
                'value' => number_format($machine->maximum_weight) . ' kg',
            ];
        }

        return [
            'type' => 'card',
            'title' => '⚙️ เครื่องจักร',
            'subtitle' => 'รายการทั้งหมด',
            'headerColor' => '#34495E',
            'rows' => $rows,
        ];
    }

    /**
     * ดึงเมนูรายงาน
     */
    public function getReportMenu(): array
    {
        return [
            'type' => 'menu',
            'title' => '📈 รายงานสรุป',
            'text' => "📈 รายงานสรุป\n\nเลือกช่วงเวลาที่ต้องการ:",
            'quickReplies' => [
                ['label' => '📊 สรุปวันนี้', 'action' => 'report_period', 'data' => ['period' => 'today']],
                ['label' => '📆 สัปดาห์นี้', 'action' => 'report_period', 'data' => ['period' => 'week']],
                ['label' => '📅 เดือนนี้', 'action' => 'report_period', 'data' => ['period' => 'month']],
                ['label' => '📈 ทั้งหมด', 'action' => 'report_period', 'data' => ['period' => 'all']],
            ],
        ];
    }

    /**
     * ดึงรายงานตามช่วงเวลา
     */
    public function getReportSummary(string $period): array
    {
        $periodLabels = [
            'today' => 'วันนี้',
            'week' => 'สัปดาห์นี้',
            'month' => 'เดือนนี้',
            'all' => 'ทั้งหมด',
        ];

        $periodLabel = $periodLabels[$period] ?? 'ทั้งหมด';

        // คำนวณช่วงเวลา
        $startDate = null;
        $endDate = Carbon::now();

        switch ($period) {
            case 'today':
                $startDate = Carbon::today();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                break;
            case 'all':
            default:
                $startDate = null;
                break;
        }

        // คำนวณรายได้
        $incomeQuery = Income::query();
        if ($startDate) {
            $incomeQuery->where('created_at', '>=', $startDate);
        }
        $totalIncome = $incomeQuery->sum('amount') ?? 0;

        // คำนวณค่าใช้จ่าย
        $expenseQuery = Expense::query();
        if ($startDate) {
            $expenseQuery->where('created_at', '>=', $startDate);
        }
        $totalExpense = $expenseQuery->sum('amount') ?? 0;

        // คำนวณกำไร
        $profit = $totalIncome - $totalExpense;
        $profitColor = $profit >= 0 ? '#1DB446' : '#FF0000';

        $rows = [
            ['label' => '📅 ช่วงเวลา', 'value' => $periodLabel],
            ['type' => 'separator'],
            ['label' => '💰 รายได้', 'value' => number_format($totalIncome, 2) . ' ฿', 'valueColor' => '#1DB446'],
            ['label' => '💸 ค่าใช้จ่าย', 'value' => number_format($totalExpense, 2) . ' ฿', 'valueColor' => '#FF6B35'],
            ['type' => 'separator'],
            ['label' => '📊 กำไร/ขาดทุน', 'value' => number_format($profit, 2) . ' ฿', 'valueColor' => $profitColor],
        ];

        // เพิ่มสรุปพลังงานถ้าไม่ใช่ all
        if ($startDate) {
            $energyQuery = EnergyResourceLog::with('energyResource')
                ->where('created_at', '>=', $startDate);
            $totalEnergyCost = $energyQuery->sum('cost') ?? 0;

            $rows[] = ['type' => 'separator'];
            $rows[] = ['label' => '⚡ ค่าพลังงาน', 'value' => '', 'bold' => true];
            $rows[] = ['label' => 'รวมทั้งหมด', 'value' => number_format($totalEnergyCost, 2) . ' ฿', 'valueColor' => '#FFD700'];
        }

        return [
            'type' => 'card',
            'title' => '📈 รายงานสรุป',
            'subtitle' => $periodLabel,
            'headerColor' => '#2C3E50',
            'rows' => $rows,
        ];
    }
}
