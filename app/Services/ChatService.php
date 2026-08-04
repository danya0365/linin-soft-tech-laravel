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
use App\Models\Truck;
use App\Models\Expense;
use App\Models\Note;
use App\Models\Income;
use App\Models\AiChatSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
        '📈 รายงาน',
        '📊 สรุปวันนี้',
        '👥 ลูกค้า',
        '📦 สต๊อก',
        '⚡ พลังงาน',
        '👷 พนักงาน',
        '⚙️ เครื่องจักร',
    ];

    /**
     * ประมวลผลคำสั่ง - จุดเข้าหลัก
     */
    public function processCommand(string $text, ?User $user = null): array
    {
        $lowerText = mb_strtolower(trim($text));
        $text = trim($text);

        // LINE write: คำสั่งสร้าง/แก้/ลบ/งานประจำวัน (หรือ "ยืนยัน") ต้องถึง AI ก่อน
        // ไม่งั้นโดน menu keyword (เช่น "ลูกค้า", "สต๊อก") ดักด้วย str_contains ก่อน
        if ($user && $text !== '') {
            $aiAnswer = $this->attemptAi($user, $text, writeIntentOnly: true);
            if ($aiAnswer !== null) {
                return $aiAnswer;
            }
        }

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
            return $this->getMachineMenu();
        }

        // Report
        if (str_contains($text, 'รายงาน') || $lowerText === 'report') {
            return $this->getReportMenu();
        }

        // Unknown command - try AI assistant, else show menu
        $aiAnswer = $this->attemptAi($user, $text);
        if ($aiAnswer !== null) {
            return $aiAnswer;
        }

        return $this->getMainMenu("ขอโทษครับ ไม่เข้าใจคำสั่ง \"$text\"\n\nกรุณาเลือกเมนูด้านล่าง:");
    }

    /**
     * พยายามตอบด้วย AI — คืน null เมื่อไม่มี LLM หรือ AI path ล้มเหลว
     * เพื่อให้ caller ตกไปที่เมนูตามปกติ
     *
     * LLM เป็น optional: ไม่ว่าจะไม่ได้ตั้งค่า, config พัง หรือ DB ของ session มีปัญหา
     * ก็ต้องไม่กลายเป็น error ที่หลุดออกไปถึง webhook
     */
    protected function attemptAi(?User $user, string $text, bool $writeIntentOnly = false): ?array
    {
        try {
            $ai = app(AiChatService::class); // lazy resolve กัน container cycle

            if (!$ai->isAvailable()) {
                return null;
            }

            if ($writeIntentOnly && !$this->looksLikeWriteIntent($text)) {
                return null;
            }

            // LINE ที่ระบุ user → มี session + ประวัติ + เปิด write tool (เหมือน web)
            return $user
                ? $this->answerWithLineSession($ai, $user, $text)
                : $ai->answer($text);
        } catch (\Throwable $e) {
            Log::warning('ChatService: AI path unavailable, falling back to menu', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /** จำนวนข้อความล่าสุดที่ส่งเป็น context ให้ AI บน LINE */
    protected const LINE_CONTEXT_WINDOW = 10;

    /** คำที่บ่งบอกเจตนาสร้าง/แก้/ลบ/งานประจำวัน หรือยืนยัน — ให้ไปถึง AI ก่อน menu */
    protected const WRITE_INTENT_KEYWORDS = [
        'สร้าง', 'เพิ่ม', 'แก้', 'เปลี่ยน', 'ลบ', 'บันทึก', 'ออกบิล', 'เก็บเงิน',
        'เบิก', 'รับเข้า', 'รับสต๊อก', 'ตัดสต๊อก', 'ส่งผ้า', 'ยืนยัน', 'ยกเลิก',
    ];

    protected function looksLikeWriteIntent(string $text): bool
    {
        foreach (self::WRITE_INTENT_KEYWORDS as $kw) {
            if (mb_strpos($text, $kw) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * ตอบผ่าน AI บน LINE โดยมี session + ประวัติ → เปิด write/operational tool ได้
     * (กลไก preview→ยืนยัน reuse จาก EntityWriteService ผ่าน setWriteContext)
     */
    protected function answerWithLineSession(AiChatService $ai, User $user, string $text): array
    {
        // 1 session ต่อ LINE user (rolling)
        $session = AiChatSession::firstOrCreate(
            ['user_id' => $user->id, 'channel' => 'line'],
            ['title' => 'LINE']
        );

        // persist ข้อความ user ก่อนเรียก AI (ให้ draft.created_after_message_id รวมข้อความนี้)
        $session->messages()->create(['role' => 'user', 'content' => $text]);
        $session->forceFill(['last_message_at' => now()])->save();

        // context = N ข้อความล่าสุด (เก่า→ใหม่)
        $history = $session->messages()
            ->orderByDesc('id')
            ->limit(self::LINE_CONTEXT_WINDOW)
            ->get(['role', 'content'])
            ->reverse()
            ->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
            ->values()
            ->all();

        $ai->setWriteContext($user, $session);
        $answer = $ai->answerWithHistory($history);

        // persist คำตอบ assistant (ให้ draft_id/preview อยู่ในประวัติเทิร์นถัดไป)
        $session->messages()->create(['role' => 'assistant', 'content' => $answer['text'] ?? '']);

        return $answer;
    }

    /**
     * ประมวลผล action (postback)
     */
    public function processAction(string $action, array $params = []): array
    {
        switch ($action) {
            case 'customer_group':
                return $this->getCustomersByGroup($params['id'] ?? null);
            case 'customer_date_filter':
                return $this->getCustomerDateFilterMenu($params['id'] ?? null);
            case 'customer_detail':
                return $this->getCustomerDetail($params['id'] ?? null);
            case 'customer_detail_date':
                return $this->getCustomerDetail(
                    $params['id'] ?? null,
                    $params['dateFrom'] ?? null,
                    $params['dateTo'] ?? null
                );
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
            case 'report_type':
                return $this->getReportDateMenu($params['type'] ?? 'summary');
            case 'report_date':
                $type = $params['type'] ?? 'summary';
                $date = $params['date'] ?? null;
                $range = $params['range'] ?? 'day';
                return $this->getReportByDate($type, $date, $range);
            // Machine actions
            case 'machine_type':
                return $this->getMachineTypeMenu($params['type'] ?? 'washing');
            case 'machine_list':
                return $this->getMachineListByType($params['type'] ?? 'washing');
            case 'machine_history_type':
                return $this->getMachineHistoryDateMenuByType($params['type'] ?? 'washing');
            case 'machine_notes_date_type':
                return $this->getMachineNotesByDateAndType($params['date'] ?? null, $params['type'] ?? 'washing');
            case 'machine_notes_recent':
                return $this->getMachineRecentNotesByType($params['type'] ?? 'washing');
            // Legacy machine actions
            case 'machine_all':
                return $this->getMachineAllList();
            case 'machine_history':
                return $this->getMachineHistoryDateMenu();
            case 'machine_notes_date':
                return $this->getMachineNotesByDate($params['date'] ?? null);
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
                'action' => 'customer_date_filter',
                'data' => ['id' => $c->id],
            ])->toArray(),
        ];
    }

    /**
     * แสดงเมนูเลือกช่วงวันที่สำหรับดูรายละเอียดลูกค้า
     */
    public function getCustomerDateFilterMenu($customerId): array
    {
        $customer = Customer::find($customerId);

        if (!$customer) {
            return [
                'type' => 'text',
                'text' => '❌ ไม่พบข้อมูลลูกค้า',
            ];
        }

        // คำนวณช่วงเวลาต่างๆ
        $today = Carbon::now()->format('Y-m-d');
        
        // สัปดาห์นี้ (เริ่มวันจันทร์)
        $startOfWeek = Carbon::now()->startOfWeek()->format('Y-m-d');
        $startOfWeekLabel = Carbon::now()->startOfWeek()->format('d/m');
        
        // เดือนนี้
        $firstDayOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $monthLabel = Carbon::now()->format('m/Y');
        
        // ปีนี้
        $firstDayOfYear = Carbon::now()->startOfYear()->format('Y-m-d');
        $yearLabel = Carbon::now()->format('Y');

        return [
            'type' => 'menu',
            'title' => '📅 เลือกช่วงเวลา',
            'text' => "👥 ลูกค้า: {$customer->name}\n\n📅 เลือกช่วงเวลาที่ต้องการดูข้อมูล:",
            'quickReplies' => [
                [
                    'label' => '📊 ข้อมูลโดยรวม (ทั้งหมด)',
                    'action' => 'customer_detail',
                    'data' => ['id' => $customerId],
                ],
                [
                    'label' => '� สัปดาห์นี้ (เริ่ม ' . $startOfWeekLabel . ')',
                    'action' => 'customer_detail_date',
                    'data' => [
                        'id' => $customerId,
                        'dateFrom' => $startOfWeek,
                        'dateTo' => $today,
                    ],
                ],
                [
                    'label' => '📅 เดือนนี้ (' . $monthLabel . ')',
                    'action' => 'customer_detail_date',
                    'data' => [
                        'id' => $customerId,
                        'dateFrom' => $firstDayOfMonth,
                        'dateTo' => $today,
                    ],
                ],
                [
                    'label' => '📅 ปีนี้ (' . $yearLabel . ')',
                    'action' => 'customer_detail_date',
                    'data' => [
                        'id' => $customerId,
                        'dateFrom' => $firstDayOfYear,
                        'dateTo' => $today,
                    ],
                ],
            ],
        ];
    }

    /**
     * ดึงรายละเอียดลูกค้า
     */
    public function getCustomerDetail($customerId, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $customer = Customer::find($customerId);

        if (!$customer) {
            return [
                'type' => 'text',
                'text' => '❌ ไม่พบข้อมูลลูกค้า',
            ];
        }

        // === ดึงยอดสะสมจาก CustomerOperationDailySummary ===
        $query = \App\Models\CustomerOperationDailySummary::where('customer_id', $customerId);

        // กรองตามช่วงวันที่ถ้ามี
        if ($dateFrom && $dateTo) {
            $query->whereBetween('operation_date', [$dateFrom, $dateTo]);
        }

        $summary = $query->selectRaw('
                SUM(total_wet_weight) as total_wet_weight,
                SUM(total_dry_weight) as total_dry_weight,
                SUM(total_edit_collect_weight) as total_edit_collect_weight,
                SUM(total_edit_weight) as total_edit_weight,
                SUM(total_billing_weight) as total_billing_weight,
                SUM(total_billing_payment) as total_billing_payment
            ')
            ->first();

        // สร้าง label สำหรับช่วงวันที่
        $dateLabel = 'ยอดสะสมทั้งหมด';
        if ($dateFrom && $dateTo) {
            $dateLabel = Carbon::parse($dateFrom)->format('d/m/Y') . ' - ' . Carbon::parse($dateTo)->format('d/m/Y');
        }

        // คำนวณ % ผ้าแก้ไข (ระบบ)
        $totalBillingWeight = $summary->total_billing_weight ?? 0;
        $totalEditCollectWeight = $summary->total_edit_collect_weight ?? 0;
        $totalEditWeight = $summary->total_edit_weight ?? 0;

        $editCollectWeightPercent = ($totalBillingWeight > 0 && $totalEditCollectWeight > 0) 
            ? round(($totalEditCollectWeight / $totalBillingWeight) * 100, 2) 
            : 0;

        // คำนวณ % ผ้าแก้ไข (กรอกมือ)
        $editWeightPercent = ($totalBillingWeight > 0 && $totalEditWeight > 0) 
            ? round(($totalEditWeight / $totalBillingWeight) * 100, 2) 
            : 0;

        return [
            'type' => 'card',
            'title' => '👥 ข้อมูลลูกค้า',
            'subtitle' => $customer->name,
            'headerColor' => '#0066CC',
            'rows' => [
                ['label' => '🏥 ชื่อ', 'value' => $customer->name],
                ['label' => '🏢 กลุ่มลูกค้า', 'value' => $customer->customerGroup ? $customer->customerGroup->name : '-'],
                ['label' => '📅 ช่วงเวลา', 'value' => $dateLabel, 'valueColor' => '#0066CC'],
                ['type' => 'separator'],
                ['label' => '💧 น้ำหนักเปียก', 'value' => number_format($summary->total_wet_weight ?? 0, 2) . ' kg'],
                ['label' => '☀️ น้ำหนักแห้ง', 'value' => number_format($summary->total_dry_weight ?? 0, 2) . ' kg'],
                ['type' => 'separator'],
                ['label' => '🔧 ผ้าแก้ไข (ระบบ)', 'value' => number_format($totalEditCollectWeight, 2) . ' kg', 'valueColor' => '#28A745'],
                ['label' => '📊 % ผ้าแก้ไข (ระบบ)', 'value' => $editCollectWeightPercent . '%', 'valueColor' => '#28A745'],
                ['label' => '✏️ ผ้าแก้ไข (กรอกมือ)', 'value' => number_format($totalEditWeight, 2) . ' kg', 'valueColor' => '#FFA500'],
                ['label' => '📊 % ผ้าแก้ไข (กรอกมือ)', 'value' => $editWeightPercent . '%', 'valueColor' => '#FFA500'],
                ['type' => 'separator'],
                ['label' => '⚖️ น้ำหนักบิล', 'value' => number_format($totalBillingWeight, 2) . ' kg'],
                ['label' => '💰 ยอดเงินรวม', 'value' => number_format($summary->total_billing_payment ?? 0, 2) . ' ฿', 'valueColor' => '#1DB446'],
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
    public function getEnergyLogs($resourceId, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $resource = EnergyResource::find($resourceId);

        if (!$resource) {
            return [
                'type' => 'text',
                'text' => '❌ ไม่พบข้อมูลพลังงาน',
            ];
        }

        $query = EnergyResourceLog::where('energy_resource_id', $resourceId);

        $hasRange = $dateFrom && $dateTo;
        if ($hasRange) {
            $query->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->when(!$hasRange, fn ($q) => $q->take(7))
            ->get();

        $totalValue = $logs->sum('value');

        $rows = [
            ['label' => '📊 ' . ($hasRange ? 'รวมช่วงที่เลือก' : 'รวม 7 วัน'), 'value' => number_format($totalValue, 2), 'valueColor' => '#1DB446'],
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
    public function getEmployeeDetail($employeeId, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $employee = Employee::with('department')->find($employeeId);

        if (!$employee) {
            return [
                'type' => 'text',
                'text' => '❌ ไม่พบข้อมูลพนักงาน',
            ];
        }

        $hasRange = $dateFrom && $dateTo;

        if ($hasRange) {
            // สถิติตามช่วงวันที่ที่เลือก
            $rangeLogs = EmployeeOperationLog::where('employee_id', $employeeId)
                ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->count();

            $operationStats = EmployeeOperationLog::where('employee_id', $employeeId)
                ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->selectRaw('operation_type, COUNT(*) as count')
                ->groupBy('operation_type')
                ->pluck('count', 'operation_type')
                ->toArray();

            $rows = [
                ['label' => '👤 ชื่อ', 'value' => $employee->name],
                ['label' => '🏢 แผนก', 'value' => $employee->department->name ?? 'ไม่ระบุ'],
                ['type' => 'separator'],
                ['label' => '📊 สถิติการทำงาน', 'value' => '', 'bold' => true],
                ['label' => '📅 ช่วงเวลา', 'value' => Carbon::parse($dateFrom)->format('d/m/Y') . ' - ' . Carbon::parse($dateTo)->format('d/m/Y')],
                ['label' => '🔢 จำนวนงานทั้งหมด', 'value' => number_format($rangeLogs) . ' ครั้ง', 'valueColor' => '#1DB446'],
            ];
        } else {
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
        }

        // เพิ่มสถิติแยกตามประเภทงาน
        if (!empty($operationStats)) {
            $rows[] = ['type' => 'separator'];
            $rows[] = ['label' => '🔧 ' . ($hasRange ? 'งานในรอบช่วงเวลา' : 'งานวันนี้') . ' (แยกประเภท)', 'value' => '', 'bold' => true];

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
     * ดึงเมนูเครื่องจักร - เลือกประเภทเครื่องจักร
     */
    public function getMachineMenu(): array
    {
        return [
            'type' => 'menu',
            'title' => '⚙️ เครื่องจักร',
            'text' => "⚙️ เครื่องจักร\n\nเลือกประเภทเครื่องจักร:",
            'quickReplies' => [
                ['label' => '🧺 เครื่องซักผ้า', 'action' => 'machine_type', 'data' => ['type' => 'washing']],
                ['label' => '🌡️ เครื่องอบผ้า', 'action' => 'machine_type', 'data' => ['type' => 'dryer']],
                ['label' => '� รถบรรทุก', 'action' => 'machine_type', 'data' => ['type' => 'truck']],
            ],
        ];
    }

    /**
     * เมนูย่อยสำหรับแต่ละประเภทเครื่องจักร
     */
    public function getMachineTypeMenu(string $type): array
    {
        $typeLabels = [
            'washing' => '🧺 เครื่องซักผ้า',
            'dryer' => '🌡️ เครื่องอบผ้า',
            'truck' => '🚚 รถบรรทุก',
        ];
        $label = $typeLabels[$type] ?? 'เครื่องจักร';

        return [
            'type' => 'menu',
            'title' => $label,
            'text' => "{$label}\n\nเลือกเมนูที่ต้องการ:",
            'quickReplies' => [
                ['label' => '📋 ดูรายการทั้งหมด', 'action' => 'machine_list', 'data' => ['type' => $type]],
                ['label' => '📜 ดูประวัติ (7 วัน)', 'action' => 'machine_history_type', 'data' => ['type' => $type]],
            ],
        ];
    }

    /**
     * ดึงรายการเครื่องจักรตามประเภท
     */
    public function getMachineListByType(string $type): array
    {
        $typeLabels = [
            'washing' => '🧺 เครื่องซักผ้า',
            'dryer' => '🌡️ เครื่องอบผ้า',
            'truck' => '🚚 รถบรรทุก',
        ];
        $label = $typeLabels[$type] ?? 'เครื่องจักร';

        $rows = [];
        $availableCount = 0;
        $brokenCount = 0;

        switch ($type) {
            case 'washing':
                $machines = WashingMachine::take(20)->get();
                if ($machines->isEmpty()) {
                    $rows[] = ['label' => 'ไม่พบข้อมูล', 'value' => '-'];
                } else {
                    foreach ($machines as $machine) {
                        $statusIcon = $machine->service_status === 'available' ? '✅' : '🔧';
                        $statusColor = $machine->service_status === 'available' ? '#1DB446' : '#FF0000';
                        
                        if ($machine->service_status === 'available') {
                            $availableCount++;
                        } else {
                            $brokenCount++;
                        }
                        
                        $rows[] = [
                            'label' => "{$statusIcon} {$machine->name}",
                            'value' => number_format($machine->maximum_weight) . ' kg',
                            'valueColor' => $statusColor,
                        ];
                    }
                }
                break;

            case 'dryer':
                $machines = DryerMachine::take(20)->get();
                if ($machines->isEmpty()) {
                    $rows[] = ['label' => 'ไม่พบข้อมูล', 'value' => '-'];
                } else {
                    foreach ($machines as $machine) {
                        $statusIcon = $machine->service_status === 'available' ? '✅' : '🔧';
                        $statusColor = $machine->service_status === 'available' ? '#1DB446' : '#FF0000';
                        
                        if ($machine->service_status === 'available') {
                            $availableCount++;
                        } else {
                            $brokenCount++;
                        }
                        
                        $rows[] = [
                            'label' => "{$statusIcon} {$machine->name}",
                            'value' => number_format($machine->maximum_weight) . ' kg',
                            'valueColor' => $statusColor,
                        ];
                    }
                }
                break;

            case 'truck':
                $trucks = Truck::take(20)->get();
                if ($trucks->isEmpty()) {
                    $rows[] = ['label' => 'ไม่พบข้อมูล', 'value' => '-'];
                } else {
                    foreach ($trucks as $truck) {
                        $statusIcon = $truck->service_status === 'available' ? '✅' : '🔧';
                        $statusColor = $truck->service_status === 'available' ? '#1DB446' : '#FF0000';
                        
                        if ($truck->service_status === 'available') {
                            $availableCount++;
                        } else {
                            $brokenCount++;
                        }
                        
                        $rows[] = [
                            'label' => "{$statusIcon} {$truck->name}",
                            'value' => $truck->plate_number ?? '-',
                            'valueColor' => $statusColor,
                        ];
                    }
                }
                break;
        }

        // เพิ่มสรุปสถานะด้านบน
        $summaryRows = [
            ['label' => '📊 สรุปสถานะ', 'value' => '', 'bold' => true],
            ['label' => '✅ พร้อมใช้งาน', 'value' => "{$availableCount} เครื่อง", 'valueColor' => '#1DB446'],
            ['label' => '🔧 เสีย/ซ่อม', 'value' => "{$brokenCount} เครื่อง", 'valueColor' => '#FF0000'],
            ['type' => 'separator'],
        ];

        return [
            'type' => 'card',
            'title' => $label,
            'subtitle' => 'รายการทั้งหมด',
            'headerColor' => '#34495E',
            'rows' => array_merge($summaryRows, $rows),
        ];
    }

    /**
     * ดึงรายการเครื่องจักรทั้งหมด (legacy - kept for compatibility)
     */
    public function getMachineAllList(): array
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
     * แสดงเมนูเลือกวันที่ 7 วันล่าสุด สำหรับดูประวัติเครื่องจักร (legacy)
     */
    public function getMachineHistoryDateMenu(): array
    {
        $dates = [];
        
        // 7 วันล่าสุด
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i);
            $dayLabel = $date->format('d/m');
            
            if ($i === 0) {
                $dayLabel .= ' (วันนี้)';
            } elseif ($i === 1) {
                $dayLabel .= ' (เมื่อวาน)';
            }
            
            $dates[] = [
                'label' => $dayLabel,
                'action' => 'machine_notes_date',
                'data' => ['date' => $date->format('Y-m-d')],
            ];
        }

        return [
            'type' => 'menu',
            'title' => '📅 เลือกวันที่ดูประวัติ',
            'text' => "📅 เลือกวันที่ที่ต้องการดูประวัติเครื่องจักร:\n\n(7 วันล่าสุด)",
            'quickReplies' => $dates,
        ];
    }

    /**
     * แสดงเมนูเลือกวันที่ 7 วันล่าสุด สำหรับดูประวัติเครื่องจักรตามประเภท
     */
    public function getMachineHistoryDateMenuByType(string $type): array
    {
        $typeLabels = [
            'washing' => '🧺 เครื่องซักผ้า',
            'dryer' => '🌡️ เครื่องอบผ้า',
            'truck' => '🚚 รถบรรทุก',
        ];
        $label = $typeLabels[$type] ?? 'เครื่องจักร';

        $dates = [];
        
        // ตัวเลือกดู 7 วันล่าสุด (ไม่ต้องเลือกวัน)
        $dates[] = [
            'label' => '📊 7 วันล่าสุด (10 รายการ)',
            'action' => 'machine_notes_recent',
            'data' => ['type' => $type],
        ];
        
        // 7 วันล่าสุด แยกตามวัน
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i);
            $dayLabel = $date->format('d/m');
            
            if ($i === 0) {
                $dayLabel .= ' (วันนี้)';
            } elseif ($i === 1) {
                $dayLabel .= ' (เมื่อวาน)';
            }
            
            $dates[] = [
                'label' => $dayLabel,
                'action' => 'machine_notes_date_type',
                'data' => ['date' => $date->format('Y-m-d'), 'type' => $type],
            ];
        }

        return [
            'type' => 'menu',
            'title' => "📅 ประวัติ{$label}",
            'text' => "📅 เลือกวันที่ที่ต้องการดูประวัติ{$label}:\n\nหรือเลือก '7 วันล่าสุด' เพื่อดูรายการล่าสุด",
            'quickReplies' => $dates,
        ];
    }

    /**
     * แสดง Notes ของเครื่องจักรตามวันที่เลือก
     */
    public function getMachineNotesByDate(?string $dateString): array
    {
        $date = $dateString ? Carbon::parse($dateString) : Carbon::today();
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        // ดึง Notes ที่เกี่ยวกับเครื่องจักร (washing_machine หรือ dryer_machine)
        $notes = Note::with(['washingMachine', 'dryerMachine'])
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where(function ($query) {
                $query->whereNotNull('washing_machine_id')
                    ->orWhereNotNull('dryer_machine_id');
            })
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        if ($notes->isEmpty()) {
            return [
                'type' => 'card',
                'title' => '📜 ประวัติเครื่องจักร',
                'subtitle' => $date->format('d/m/Y'),
                'headerColor' => '#34495E',
                'rows' => [
                    ['label' => '📅 วันที่', 'value' => $date->format('d/m/Y')],
                    ['type' => 'separator'],
                    ['label' => 'ℹ️ สถานะ', 'value' => 'ไม่พบประวัติ', 'valueColor' => '#999999'],
                ],
            ];
        }

        $rows = [
            ['label' => '📅 วันที่', 'value' => $date->format('d/m/Y')],
            ['label' => '📝 จำนวนรายการ', 'value' => $notes->count() . ' รายการ'],
            ['type' => 'separator'],
        ];

        $totalCost = 0;
        foreach ($notes as $note) {
            // หาชื่อเครื่องจักร
            $machineName = '';
            $machineIcon = '';
            
            if ($note->washingMachine) {
                $machineName = $note->washingMachine->name ?? 'เครื่องซัก';
                $machineIcon = '🧺';
            } elseif ($note->dryerMachine) {
                $machineName = $note->dryerMachine->name ?? 'เครื่องอบ';
                $machineIcon = '🌡️';
            }

            $time = Carbon::parse($note->created_at)->format('H:i');
            $message = mb_substr($note->message ?? '-', 0, 30);
            if (mb_strlen($note->message ?? '') > 30) {
                $message .= '...';
            }

            $rows[] = [
                'label' => "{$machineIcon} {$machineName}",
                'value' => $time,
                'bold' => true,
            ];
            $rows[] = [
                'label' => '    💬 ' . $message,
                'value' => '',
            ];
            
            if ($note->cost && $note->cost > 0) {
                $rows[] = [
                    'label' => '    💰 ค่าใช้จ่าย',
                    'value' => number_format($note->cost, 2) . ' ฿',
                    'valueColor' => '#FF6B35',
                ];
                $totalCost += $note->cost;
            }
        }

        // แสดงรวมค่าใช้จ่ายถ้ามี
        if ($totalCost > 0) {
            $rows[] = ['type' => 'separator'];
            $rows[] = [
                'label' => '💵 รวมค่าใช้จ่าย',
                'value' => number_format($totalCost, 2) . ' ฿',
                'valueColor' => '#FF0000',
                'bold' => true,
            ];
        }

        return [
            'type' => 'card',
            'title' => '📜 ประวัติเครื่องจักร',
            'subtitle' => $date->format('d/m/Y'),
            'headerColor' => '#34495E',
            'rows' => $rows,
        ];
    }

    /**
     * แสดง Notes ของเครื่องจักรตามวันที่และประเภทที่เลือก
     */
    public function getMachineNotesByDateAndType(?string $dateString, string $type): array
    {
        $date = $dateString ? Carbon::parse($dateString) : Carbon::today();
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        $typeLabels = [
            'washing' => '🧺 เครื่องซักผ้า',
            'dryer' => '🌡️ เครื่องอบผ้า',
            'truck' => '🚚 รถบรรทุก',
        ];
        $label = $typeLabels[$type] ?? 'เครื่องจักร';

        // ดึง Notes ตามประเภท
        $query = Note::whereBetween('created_at', [$startOfDay, $endOfDay]);

        switch ($type) {
            case 'washing':
                $query->with('washingMachine')->whereNotNull('washing_machine_id');
                break;
            case 'dryer':
                $query->with('dryerMachine')->whereNotNull('dryer_machine_id');
                break;
            case 'truck':
                $query->with('truck')->whereNotNull('truck_id');
                break;
            default:
                // ถ้าไม่มีประเภท ให้ดึงทุกอย่าง
                $query->with(['washingMachine', 'dryerMachine', 'truck']);
                break;
        }

        $notes = $query->orderBy('created_at', 'desc')->take(20)->get();

        if ($notes->isEmpty()) {
            return [
                'type' => 'card',
                'title' => "📜 ประวัติ{$label}",
                'subtitle' => $date->format('d/m/Y'),
                'headerColor' => '#34495E',
                'rows' => [
                    ['label' => '📅 วันที่', 'value' => $date->format('d/m/Y')],
                    ['type' => 'separator'],
                    ['label' => 'ℹ️ สถานะ', 'value' => 'ไม่พบประวัติ', 'valueColor' => '#999999'],
                ],
            ];
        }

        $rows = [
            ['label' => '📅 วันที่', 'value' => $date->format('d/m/Y')],
            ['label' => '📝 จำนวนรายการ', 'value' => $notes->count() . ' รายการ'],
            ['type' => 'separator'],
        ];

        $totalCost = 0;
        foreach ($notes as $note) {
            // หาชื่อเครื่องจักร
            $machineName = '';
            $machineIcon = '';
            
            switch ($type) {
                case 'washing':
                    $machineName = $note->washingMachine->name ?? 'เครื่องซัก';
                    $machineIcon = '🧺';
                    break;
                case 'dryer':
                    $machineName = $note->dryerMachine->name ?? 'เครื่องอบ';
                    $machineIcon = '🌡️';
                    break;
                case 'truck':
                    $machineName = $note->truck->name ?? 'รถบรรทุก';
                    $machineIcon = '🚚';
                    break;
            }

            $time = Carbon::parse($note->created_at)->format('H:i');
            $message = mb_substr($note->message ?? '-', 0, 30);
            if (mb_strlen($note->message ?? '') > 30) {
                $message .= '...';
            }

            $rows[] = [
                'label' => "{$machineIcon} {$machineName}",
                'value' => $time,
                'bold' => true,
            ];
            $rows[] = [
                'label' => '    💬 ' . $message,
                'value' => '',
            ];
            
            if ($note->cost && $note->cost > 0) {
                $rows[] = [
                    'label' => '    💰 ค่าใช้จ่าย',
                    'value' => number_format($note->cost, 2) . ' ฿',
                    'valueColor' => '#FF6B35',
                ];
                $totalCost += $note->cost;
            }
        }

        // แสดงรวมค่าใช้จ่ายถ้ามี
        if ($totalCost > 0) {
            $rows[] = ['type' => 'separator'];
            $rows[] = [
                'label' => '💵 รวมค่าใช้จ่าย',
                'value' => number_format($totalCost, 2) . ' ฿',
                'valueColor' => '#FF0000',
                'bold' => true,
            ];
        }

        return [
            'type' => 'card',
            'title' => "📜 ประวัติ{$label}",
            'subtitle' => $date->format('d/m/Y'),
            'headerColor' => '#34495E',
            'rows' => $rows,
        ];
    }

    /**
     * แสดง Notes ล่าสุดของเครื่องจักรตามประเภท (7 วันล่าสุด, limit 10 รายการ)
     */
    public function getMachineRecentNotesByType(string $type, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $startDate = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : Carbon::now()->subDays(7)->startOfDay();
        $endDate = $dateTo ? Carbon::parse($dateTo)->endOfDay() : Carbon::now()->endOfDay();
        $hasRange = $dateFrom && $dateTo;
        $rangeLabel = $hasRange
            ? Carbon::parse($dateFrom)->format('d/m/Y') . ' - ' . Carbon::parse($dateTo)->format('d/m/Y')
            : '7 วันล่าสุด';

        $typeLabels = [
            'washing' => '🧺 เครื่องซักผ้า',
            'dryer' => '🌡️ เครื่องอบผ้า',
            'truck' => '🚚 รถบรรทุก',
        ];
        $label = $typeLabels[$type] ?? 'เครื่องจักร';

        // ดึง Notes ตามประเภท (7 วันล่าสุด, limit 10 — ปลด limit เมื่อระบุช่วงวันที่)
        $query = Note::whereBetween('created_at', [$startDate, $endDate]);

        switch ($type) {
            case 'washing':
                $query->with('washingMachine')->whereNotNull('washing_machine_id');
                break;
            case 'dryer':
                $query->with('dryerMachine')->whereNotNull('dryer_machine_id');
                break;
            case 'truck':
                $query->with('truck')->whereNotNull('truck_id');
                break;
        }

        $notes = $query->orderBy('created_at', 'desc')
            ->when(!$hasRange, fn ($q) => $q->take(10))
            ->get();

        if ($notes->isEmpty()) {
            return [
                'type' => 'card',
                'title' => "📜 ประวัติ{$label}",
                'subtitle' => $rangeLabel,
                'headerColor' => '#34495E',
                'rows' => [
                    ['label' => '📅 ช่วงเวลา', 'value' => $rangeLabel],
                    ['type' => 'separator'],
                    ['label' => 'ℹ️ สถานะ', 'value' => 'ไม่พบประวัติ', 'valueColor' => '#999999'],
                ],
            ];
        }

        $rows = [
            ['label' => '📅 ช่วงเวลา', 'value' => $rangeLabel],
            ['label' => '📝 จำนวนรายการ', 'value' => $notes->count() . ' รายการ' . (!$hasRange ? ' (สูงสุด 10)' : '')],
            ['type' => 'separator'],
        ];

        $totalCost = 0;
        foreach ($notes as $note) {
            // หาชื่อเครื่องจักร
            $machineName = '';
            $machineIcon = '';
            
            switch ($type) {
                case 'washing':
                    $machineName = $note->washingMachine->name ?? 'เครื่องซัก';
                    $machineIcon = '🧺';
                    break;
                case 'dryer':
                    $machineName = $note->dryerMachine->name ?? 'เครื่องอบ';
                    $machineIcon = '🌡️';
                    break;
                case 'truck':
                    $machineName = $note->truck->name ?? 'รถบรรทุก';
                    $machineIcon = '🚚';
                    break;
            }

            $dateTime = Carbon::parse($note->created_at)->format('d/m H:i');
            $message = mb_substr($note->message ?? '-', 0, 25);
            if (mb_strlen($note->message ?? '') > 25) {
                $message .= '...';
            }

            $rows[] = [
                'label' => "{$machineIcon} {$machineName}",
                'value' => $dateTime,
                'bold' => true,
            ];
            $rows[] = [
                'label' => '    💬 ' . $message,
                'value' => '',
            ];
            
            if ($note->cost && $note->cost > 0) {
                $rows[] = [
                    'label' => '    💰',
                    'value' => number_format($note->cost, 2) . ' ฿',
                    'valueColor' => '#FF6B35',
                ];
                $totalCost += $note->cost;
            }
        }

        // แสดงรวมค่าใช้จ่ายถ้ามี
        if ($totalCost > 0) {
            $rows[] = ['type' => 'separator'];
            $rows[] = [
                'label' => '💵 รวมค่าใช้จ่าย',
                'value' => number_format($totalCost, 2) . ' ฿',
                'valueColor' => '#FF0000',
                'bold' => true,
            ];
        }

        return [
            'type' => 'card',
            'title' => "📜 ประวัติ{$label}",
            'subtitle' => '7 วันล่าสุด (10 รายการ)',
            'headerColor' => '#34495E',
            'rows' => $rows,
        ];
    }

    /**
     * ดึงเมนูรายงาน - เลือกประเภท (รวม/แยกละเอียด)
     */
    public function getReportMenu(): array
    {
        return [
            'type' => 'menu',
            'title' => '📈 รายงานสรุป',
            'text' => "📈 รายงานสรุป\n\nเลือกรูปแบบรายงาน:",
            'quickReplies' => [
                ['label' => '📊 ดูแบบรวม', 'action' => 'report_type', 'data' => ['type' => 'summary']],
                ['label' => '📋 ดูแบบแยกละเอียด', 'action' => 'report_type', 'data' => ['type' => 'detailed']],
            ],
        ];
    }

    /**
     * แสดงเมนูเลือกวันที่ 7 วันล่าสุด + สัปดาห์/เดือน/ปี
     */
    public function getReportDateMenu(string $type): array
    {
        $typeLabel = $type === 'detailed' ? '📋 แยกละเอียด' : '📊 รวม';
        $dates = [];
        
        // คำนวณช่วงเวลาต่างๆ
        $today = Carbon::now()->format('Y-m-d');
        
        // สัปดาห์นี้ (เริ่มวันจันทร์)
        $startOfWeek = Carbon::now()->startOfWeek()->format('Y-m-d');
        $startOfWeekLabel = Carbon::now()->startOfWeek()->format('d/m');
        
        // เดือนนี้
        $monthLabel = Carbon::now()->format('m/Y');
        
        // ปีนี้
        $yearLabel = Carbon::now()->format('Y');
        
        // 7 วันล่าสุด
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i);
            $dates[] = [
                'label' => $date->format('d/m') . ($i === 0 ? ' (วันนี้)' : ($i === 1 ? ' (เมื่อวาน)' : '')),
                'action' => 'report_date',
                'data' => ['type' => $type, 'date' => $date->format('Y-m-d'), 'range' => 'day'],
            ];
        }

        // เพิ่มตัวเลือกช่วงเวลา พร้อมกำกับวันที่เริ่ม
        $dates[] = ['label' => '📆 สัปดาห์นี้ (เริ่ม ' . $startOfWeekLabel . ')', 'action' => 'report_date', 'data' => ['type' => $type, 'range' => 'week']];
        $dates[] = ['label' => '📅 เดือนนี้ (' . $monthLabel . ')', 'action' => 'report_date', 'data' => ['type' => $type, 'range' => 'month']];
        $dates[] = ['label' => '📅 ปีนี้ (' . $yearLabel . ')', 'action' => 'report_date', 'data' => ['type' => $type, 'range' => 'year']];

        return [
            'type' => 'menu',
            'title' => "📅 เลือกวันที่ ({$typeLabel})",
            'text' => "📅 เลือกวันที่ที่ต้องการดู:\n\nรูปแบบ: {$typeLabel}",
            'quickReplies' => $dates,
        ];
    }

    /**
     * แสดงรายงานตามวันที่/ช่วงเวลาที่เลือก
     */
    public function getReportByDate(string $type, ?string $dateString, string $range = 'day', ?string $dateFrom = null, ?string $dateTo = null): array
    {
        // ถ้าระบุช่วงวันที่ตามใจ → override range/date
        if ($dateFrom && $dateTo) {
            $startDate = Carbon::parse($dateFrom)->startOfDay();
            $endDate = Carbon::parse($dateTo)->endOfDay();
            $rangeLabel = Carbon::parse($dateFrom)->format('d/m/Y') . ' - ' . Carbon::parse($dateTo)->format('d/m/Y');
        } else {
            // กำหนดช่วงวันที่ตาม range
            switch ($range) {
                case 'week':
                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();
                    $rangeLabel = 'สัปดาห์นี้';
                    break;
                case 'month':
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    $rangeLabel = 'เดือนนี้';
                    break;
                case 'year':
                    $startDate = Carbon::now()->startOfYear();
                    $endDate = Carbon::now()->endOfYear();
                    $rangeLabel = 'ปีนี้';
                    break;
                case 'day':
                default:
                    $date = $dateString ? Carbon::parse($dateString) : Carbon::today();
                    $startDate = $date->copy()->startOfDay();
                    $endDate = $date->copy()->endOfDay();
                    $rangeLabel = $date->format('d/m/Y');
                    break;
            }
        }

        if ($type === 'detailed') {
            return $this->getDetailedReportByRange($startDate, $endDate, $rangeLabel);
        }

        return $this->getSummaryReportByRange($startDate, $endDate, $rangeLabel);
    }

    /**
     * รายงานแบบรวม (Summary) ตามช่วงเวลา
     */
    protected function getSummaryReportByRange(Carbon $startDate, Carbon $endDate, string $rangeLabel): array
    {
        // รายได้
        $totalIncome = Income::whereBetween('created_at', [$startDate, $endDate])->sum('amount') ?? 0;

        // ค่าใช้จ่าย
        $totalExpense = Expense::whereBetween('created_at', [$startDate, $endDate])->sum('amount') ?? 0;

        // กำไร
        $profit = $totalIncome - $totalExpense;
        $profitColor = $profit >= 0 ? '#1DB446' : '#FF0000';

        // ค่าพลังงาน
        $totalEnergyCost = EnergyResourceLog::whereBetween('created_at', [$startDate, $endDate])->sum('cost') ?? 0;

        // Operations
        $operationCount = Operation::whereBetween('created_at', [$startDate, $endDate])->count();

        return [
            'type' => 'card',
            'title' => '📊 รายงานรวม',
            'subtitle' => $rangeLabel,
            'headerColor' => '#2C3E50',
            'rows' => [
                ['label' => '📅 ช่วงเวลา', 'value' => $rangeLabel],
                ['label' => '🧺 จำนวนงาน', 'value' => number_format($operationCount) . ' รายการ'],
                ['type' => 'separator'],
                ['label' => '💰 รายได้', 'value' => number_format($totalIncome, 2) . ' ฿', 'valueColor' => '#1DB446'],
                ['label' => '💸 ค่าใช้จ่าย', 'value' => number_format($totalExpense, 2) . ' ฿', 'valueColor' => '#FF6B35'],
                ['label' => '⚡ ค่าพลังงาน', 'value' => number_format($totalEnergyCost, 2) . ' ฿', 'valueColor' => '#FFD700'],
                ['type' => 'separator'],
                ['label' => '📊 กำไร/ขาดทุน', 'value' => number_format($profit, 2) . ' ฿', 'valueColor' => $profitColor],
            ],
        ];
    }

    /**
     * รายงานแบบแยกละเอียด (Detailed) ตามช่วงเวลา
     */
    protected function getDetailedReportByRange(Carbon $startDate, Carbon $endDate, string $rangeLabel): array
    {
        $rows = [
            ['label' => '📅 ช่วงเวลา', 'value' => $rangeLabel],
        ];

        // === Operation สรุป ===
        $rows[] = ['type' => 'separator'];
        $rows[] = ['label' => '🧺 Operation (สรุป)', 'value' => '', 'bold' => true];
        
        // === Group 1: Non-payment operations - filter by created_at ===
        $nonPaymentCount = Operation::where('operation_type', '!=', 'payment')
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        $nonPaymentWetWeight = Operation::where('operation_type', '!=', 'payment')
            ->whereBetween('created_at', [$startDate, $endDate])->sum('total_wet_weight') ?? 0;
        $nonPaymentDryWeight = Operation::where('operation_type', '!=', 'payment')
            ->whereBetween('created_at', [$startDate, $endDate])->sum('total_dry_weight') ?? 0;
        $nonPaymentEditWeight = Operation::where('operation_type', '!=', 'payment')
            ->whereBetween('created_at', [$startDate, $endDate])->sum('total_edit_weight') ?? 0;
        $nonPaymentBillingWeight = Operation::where('operation_type', '!=', 'payment')
            ->whereBetween('created_at', [$startDate, $endDate])->sum('total_billing_weight') ?? 0;
        $nonPaymentBillingPayment = Operation::where('operation_type', '!=', 'payment')
            ->whereBetween('created_at', [$startDate, $endDate])->sum('total_billing_payment') ?? 0;
        
        // === Group 2: Payment operations - filter by billing_payment_date ===
        $paymentDateStart = $startDate->format('Y-m-d');
        $paymentDateEnd = $endDate->format('Y-m-d');
        
        $paymentCount = Operation::where('operation_type', 'payment')
            ->whereBetween('billing_payment_date', [$paymentDateStart, $paymentDateEnd])->count();
        $paymentWetWeight = Operation::where('operation_type', 'payment')
            ->whereBetween('billing_payment_date', [$paymentDateStart, $paymentDateEnd])->sum('total_wet_weight') ?? 0;
        $paymentDryWeight = Operation::where('operation_type', 'payment')
            ->whereBetween('billing_payment_date', [$paymentDateStart, $paymentDateEnd])->sum('total_dry_weight') ?? 0;
        $paymentEditWeight = Operation::where('operation_type', 'payment')
            ->whereBetween('billing_payment_date', [$paymentDateStart, $paymentDateEnd])->sum('total_edit_weight') ?? 0;
        $paymentBillingWeight = Operation::where('operation_type', 'payment')
            ->whereBetween('billing_payment_date', [$paymentDateStart, $paymentDateEnd])->sum('total_billing_weight') ?? 0;
        $paymentBillingPayment = Operation::where('operation_type', 'payment')
            ->whereBetween('billing_payment_date', [$paymentDateStart, $paymentDateEnd])->sum('total_billing_payment') ?? 0;
        
        // === รวมทั้ง 2 กลุ่ม ===
        $operationCount = $nonPaymentCount + $paymentCount;
        $totalWetWeight = $nonPaymentWetWeight + $paymentWetWeight;
        $totalDryWeight = $nonPaymentDryWeight + $paymentDryWeight;
        $totalEditWeight = $nonPaymentEditWeight + $paymentEditWeight;
        $totalBillingWeight = $nonPaymentBillingWeight + $paymentBillingWeight;
        $totalBillingPayment = $nonPaymentBillingPayment + $paymentBillingPayment;
        
        // === ผ้าแก้ไข จาก CustomerOperationDailySummary (คำนวณจาก linen_case='edit') ===
        $totalEditCollectWeight = \App\Models\CustomerOperationDailySummary::whereBetween('operation_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('total_edit_collect_weight') ?? 0;
        
        // คำนวณ % หักลบ (เปียก-แห้ง)
        $weightDiffPercent = ($totalWetWeight > 0 && $totalDryWeight > 0) 
            ? round(($totalWetWeight - $totalDryWeight) / $totalWetWeight * 100, 2) 
            : 0;
        
        // คำนวณ % ผ้าแก้ไข (จากระบบ)
        $editCollectWeightPercent = ($totalBillingWeight > 0 && $totalEditCollectWeight > 0) 
            ? round(($totalEditCollectWeight / $totalBillingWeight) * 100, 2) 
            : 0;
        
        // คำนวณ % ผ้าแก้ไข (กรอกมือ)
        $editWeightPercent = ($totalBillingWeight > 0 && $totalEditWeight > 0) 
            ? round(($totalEditWeight / $totalBillingWeight) * 100, 2) 
            : 0;
        
        $rows[] = ['label' => 'จำนวนงาน', 'value' => number_format($operationCount) . ' รายการ'];
        $rows[] = ['label' => '💧 น้ำหนักเปียก', 'value' => number_format($totalWetWeight, 2) . ' kg'];
        $rows[] = ['label' => '☀️ น้ำหนักแห้ง', 'value' => number_format($totalDryWeight, 2) . ' kg'];
        $rows[] = ['label' => '📉 % หักลบ (เปียก-แห้ง)', 'value' => $weightDiffPercent . '%', 'valueColor' => $weightDiffPercent > 20 ? '#FF0000' : ($weightDiffPercent > 15 ? '#FFA500' : '#1DB446')];
        $rows[] = ['label' => '🔧 ผ้าแก้ไข (จากระบบ)', 'value' => number_format($totalEditCollectWeight, 2) . ' kg', 'valueColor' => '#FFC107'];
        $rows[] = ['label' => '📊 % ผ้าแก้ไข (จากระบบ)', 'value' => $editCollectWeightPercent . '%', 'valueColor' => '#FFC107'];
        $rows[] = ['label' => '✏️ ผ้าแก้ไข (กรอกมือ)', 'value' => number_format($totalEditWeight, 2) . ' kg', 'valueColor' => '#17A2B8'];
        $rows[] = ['label' => '📊 % ผ้าแก้ไข (กรอกมือ)', 'value' => $editWeightPercent . '%', 'valueColor' => '#17A2B8'];
        $rows[] = ['label' => '⚖️ น้ำหนักบิล', 'value' => number_format($totalBillingWeight, 2) . ' kg'];
        $rows[] = ['label' => '💵 ยอดบิล', 'value' => number_format($totalBillingPayment, 2) . ' ฿', 'valueColor' => '#1DB446'];

        // === รายได้แยกประเภท ===
        $rows[] = ['type' => 'separator'];
        $rows[] = ['label' => '💰 รายได้ (แยกประเภท)', 'value' => '', 'bold' => true];
        
        $incomeByType = Income::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('type_name, SUM(amount) as total')
            ->groupBy('type_name')
            ->get();
        
        $incomeTotal = 0;
        foreach ($incomeByType as $item) {
            $incomeTotal += $item->total;
            $rows[] = ['label' => $item->type_name ?: 'อื่นๆ', 'value' => number_format($item->total, 2) . ' ฿'];
        }
        if ($incomeByType->isEmpty()) {
            $rows[] = ['label' => '-', 'value' => 'ไม่มีข้อมูล'];
        }
        $rows[] = ['label' => 'รวมรายได้', 'value' => number_format($incomeTotal, 2) . ' ฿', 'valueColor' => '#1DB446'];

        // === ค่าใช้จ่ายแยกประเภท ===
        $rows[] = ['type' => 'separator'];
        $rows[] = ['label' => '💸 ค่าใช้จ่าย (แยกประเภท)', 'value' => '', 'bold' => true];
        
        $expenseByType = Expense::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('type_name, SUM(amount) as total')
            ->groupBy('type_name')
            ->get();
        
        $expenseTotal = 0;
        foreach ($expenseByType as $item) {
            $expenseTotal += $item->total;
            $rows[] = ['label' => $item->type_name ?: 'อื่นๆ', 'value' => number_format($item->total, 2) . ' ฿'];
        }
        if ($expenseByType->isEmpty()) {
            $rows[] = ['label' => '-', 'value' => 'ไม่มีข้อมูล'];
        }
        $rows[] = ['label' => 'รวมค่าใช้จ่าย', 'value' => number_format($expenseTotal, 2) . ' ฿', 'valueColor' => '#FF6B35'];

        // === พลังงานแยกประเภท ===
        $rows[] = ['type' => 'separator'];
        $rows[] = ['label' => '⚡ พลังงาน (แยกประเภท)', 'value' => '', 'bold' => true];
        
        $energyByType = EnergyResourceLog::with('energyResource')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(fn($log) => $log->energyResource->name ?? 'อื่นๆ');
        
        $energyTotal = 0;
        foreach ($energyByType as $name => $logs) {
            $cost = $logs->sum('cost');
            $value = $logs->sum('value');
            $energyTotal += $cost;
            $rows[] = ['label' => $name, 'value' => number_format($cost, 2) . ' ฿'];
        }
        if ($energyByType->isEmpty()) {
            $rows[] = ['label' => '-', 'value' => 'ไม่มีข้อมูล'];
        }
        $rows[] = ['label' => 'รวมค่าพลังงาน', 'value' => number_format($energyTotal, 2) . ' ฿', 'valueColor' => '#FFD700'];

        // === สรุปกำไร ===
        $profit = $incomeTotal - $expenseTotal;
        $profitColor = $profit >= 0 ? '#1DB446' : '#FF0000';
        $rows[] = ['type' => 'separator'];
        $rows[] = ['label' => '📊 กำไร/ขาดทุน', 'value' => number_format($profit, 2) . ' ฿', 'valueColor' => $profitColor, 'bold' => true];

        return [
            'type' => 'card',
            'title' => '📋 รายงานละเอียด',
            'subtitle' => $rangeLabel,
            'headerColor' => '#8E44AD',
            'rows' => $rows,
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
