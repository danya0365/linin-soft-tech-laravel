<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EnergyResource;
use App\Models\EnergyResourceLog;
use App\Models\Inventory;
use App\Models\InventoryGroup;
use App\Models\Operation;
use App\Models\WashingMachine;
use App\Models\DryerMachine;
use App\Services\LineMessagingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * LINE Chatbot Controller
 * 
 * รับ Webhook events จาก LINE และตอบกลับอัตโนมัติ
 */
class LineChatbotController extends Controller
{
    protected LineMessagingService $lineService;

    // คำสั่งเมนูหลัก
    protected array $mainMenuCommands = [
        '📊 สรุปวันนี้',
        '👥 ลูกค้า',
        '📦 สต๊อก',
        '⚡ พลังงาน',
        '👷 พนักงาน',
        '⚙️ เครื่องจักร',
        'เมนู',
        'menu',
    ];

    public function __construct(LineMessagingService $lineService)
    {
        $this->lineService = $lineService;
    }

    /**
     * รับ Webhook events จาก LINE
     */
    public function webhook(Request $request)
    {
        // ตรวจสอบ Signature
        $signature = $request->header('X-Line-Signature');
        $body = $request->getContent();

        if (!$signature || !$this->lineService->verifySignature($body, $signature)) {
            Log::warning('LINE Webhook: Invalid signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $events = $request->input('events', []);

        foreach ($events as $event) {
            $this->handleEvent($event);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * จัดการ Event
     */
    protected function handleEvent(array $event): void
    {
        $replyToken = $event['replyToken'] ?? null;

        if (!$replyToken) {
            return;
        }

        switch ($event['type']) {
            case 'message':
                $this->handleMessage($event);
                break;
            case 'postback':
                $this->handlePostback($event);
                break;
            case 'follow':
                $this->handleFollow($event);
                break;
        }
    }

    /**
     * จัดการ Message Event
     */
    protected function handleMessage(array $event): void
    {
        $replyToken = $event['replyToken'];
        $message = $event['message'];

        if ($message['type'] !== 'text') {
            $this->sendMainMenu($replyToken, 'กรุณาเลือกเมนูด้านล่าง หรือพิมพ์ "เมนู"');
            return;
        }

        $text = trim($message['text']);

        // ตรวจสอบคำสั่งเมนูหลัก
        $this->processCommand($replyToken, $text);
    }

    /**
     * จัดการ Postback Event
     */
    protected function handlePostback(array $event): void
    {
        $replyToken = $event['replyToken'];
        $data = $event['postback']['data'] ?? '';

        // Parse postback data (format: action=value)
        parse_str($data, $params);
        $action = $params['action'] ?? '';

        switch ($action) {
            case 'customer_group':
                $this->showCustomersByGroup($replyToken, $params['id'] ?? null);
                break;
            case 'customer_detail':
                $this->showCustomerDetail($replyToken, $params['id'] ?? null);
                break;
            case 'inventory_group':
                $this->showInventoriesByGroup($replyToken, $params['id'] ?? null);
                break;
            case 'energy_type':
                $this->showEnergyLogs($replyToken, $params['id'] ?? null);
                break;
            case 'department':
                $this->showEmployeesByDepartment($replyToken, $params['id'] ?? null);
                break;
            default:
                $this->sendMainMenu($replyToken);
        }
    }

    /**
     * จัดการ Follow Event (เมื่อ user เพิ่ม bot เป็นเพื่อน)
     */
    protected function handleFollow(array $event): void
    {
        $replyToken = $event['replyToken'];
        $this->sendMainMenu($replyToken, "🎉 ยินดีต้อนรับสู่ LinenSoftTech!\n\nผมพร้อมช่วยเหลือคุณเรื่องข้อมูลโรงซักรีด กรุณาเลือกเมนูด้านล่าง:");
    }

    /**
     * ประมวลผลคำสั่ง
     */
    protected function processCommand(string $replyToken, string $text): void
    {
        // แปลงเป็น lowercase สำหรับเปรียบเทียบ
        $lowerText = mb_strtolower($text);

        if (in_array($text, ['เมนู', 'menu', 'help', 'ช่วยเหลือ', 'start'])) {
            $this->sendMainMenu($replyToken);
            return;
        }

        if (str_contains($text, 'สรุปวันนี้') || $lowerText === 'summary') {
            $this->sendTodaySummary($replyToken);
            return;
        }

        if (str_contains($text, 'ลูกค้า') || $lowerText === 'customer') {
            $this->sendCustomerMenu($replyToken);
            return;
        }

        if (str_contains($text, 'สต๊อก') || $lowerText === 'stock' || $lowerText === 'inventory') {
            $this->sendInventoryMenu($replyToken);
            return;
        }

        if (str_contains($text, 'พลังงาน') || $lowerText === 'energy') {
            $this->sendEnergyMenu($replyToken);
            return;
        }

        if (str_contains($text, 'พนักงาน') || $lowerText === 'employee') {
            $this->sendEmployeeMenu($replyToken);
            return;
        }

        if (str_contains($text, 'เครื่องจักร') || $lowerText === 'machine') {
            $this->sendMachineStatus($replyToken);
            return;
        }

        // ไม่รู้จักคำสั่ง - แสดงเมนู
        $this->sendMainMenu($replyToken, "ขอโทษครับ ไม่เข้าใจคำสั่ง \"$text\"\n\nกรุณาเลือกเมนูด้านล่าง:");
    }

    /**
     * ส่งเมนูหลัก
     */
    protected function sendMainMenu(string $replyToken, string $greeting = null): void
    {
        $text = $greeting ?? "📋 เมนูหลัก LinenSoftTech\n\nกรุณาเลือกข้อมูลที่ต้องการ:";

        $quickReplyItems = [
            $this->lineService->quickReplyItem('📊 สรุปวันนี้', 'message'),
            $this->lineService->quickReplyItem('👥 ลูกค้า', 'message'),
            $this->lineService->quickReplyItem('📦 สต๊อก', 'message'),
            $this->lineService->quickReplyItem('⚡ พลังงาน', 'message'),
            $this->lineService->quickReplyItem('👷 พนักงาน', 'message'),
            $this->lineService->quickReplyItem('⚙️ เครื่องจักร', 'message'),
        ];

        $message = $this->lineService->quickReply($text, $quickReplyItems);
        $this->lineService->replyMessage($replyToken, [$message]);
    }

    /**
     * ส่งสรุปวันนี้
     */
    protected function sendTodaySummary(string $replyToken): void
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

        // สร้าง Flex Message
        $bodyContents = [
            $this->lineService->infoRow('📅 วันที่', $today->format('d/m/Y')),
            $this->lineService->infoRow('👥 ลูกค้าทั้งหมด', number_format($customerCount) . ' ราย'),
            $this->lineService->infoRow('🧺 งานวันนี้', number_format($operationCount) . ' รายการ'),
            $this->lineService->separator(),
        ];

        // เพิ่มสรุปพลังงาน
        if (!empty($energySummary)) {
            $bodyContents[] = [
                'type' => 'text',
                'text' => '⚡ พลังงานวันนี้',
                'size' => 'sm',
                'weight' => 'bold',
                'margin' => 'md',
            ];

            foreach ($energySummary as $name => $value) {
                $bodyContents[] = $this->lineService->infoRow($name, number_format($value, 2));
            }
        } else {
            $bodyContents[] = $this->lineService->infoRow('⚡ พลังงาน', 'ยังไม่มีข้อมูล');
        }

        $bubble = $this->lineService->bubbleContainer(
            '📊 สรุปวันนี้',
            'LinenSoftTech',
            $bodyContents,
            '#1DB446'
        );

        $flexMessage = $this->lineService->flexMessage('สรุปวันนี้', $bubble);
        $this->lineService->replyMessage($replyToken, [$flexMessage]);
    }

    /**
     * ส่งเมนูลูกค้า
     */
    protected function sendCustomerMenu(string $replyToken): void
    {
        $groups = CustomerGroup::take(10)->get();

        if ($groups->isEmpty()) {
            $this->lineService->replyMessage($replyToken, [
                $this->lineService->textMessage('❌ ไม่พบข้อมูลกลุ่มลูกค้า')
            ]);
            return;
        }

        $quickReplyItems = [];
        foreach ($groups as $group) {
            $quickReplyItems[] = $this->lineService->quickReplyItem(
                $group->name,
                'postback',
                'action=customer_group&id=' . $group->id
            );
        }

        $message = $this->lineService->quickReply('👥 เลือกกลุ่มลูกค้า:', $quickReplyItems);
        $this->lineService->replyMessage($replyToken, [$message]);
    }

    /**
     * แสดงลูกค้าตามกลุ่ม
     */
    protected function showCustomersByGroup(string $replyToken, $groupId): void
    {
        $customers = Customer::where('customer_group_id', $groupId)->take(10)->get();

        if ($customers->isEmpty()) {
            $this->lineService->replyMessage($replyToken, [
                $this->lineService->textMessage('❌ ไม่พบลูกค้าในกลุ่มนี้')
            ]);
            return;
        }

        $quickReplyItems = [];
        foreach ($customers as $customer) {
            $quickReplyItems[] = $this->lineService->quickReplyItem(
                $customer->name,
                'postback',
                'action=customer_detail&id=' . $customer->id
            );
        }

        $message = $this->lineService->quickReply('👥 เลือกลูกค้า:', $quickReplyItems);
        $this->lineService->replyMessage($replyToken, [$message]);
    }

    /**
     * แสดงรายละเอียดลูกค้า
     */
    protected function showCustomerDetail(string $replyToken, $customerId): void
    {
        $customer = Customer::find($customerId);

        if (!$customer) {
            $this->lineService->replyMessage($replyToken, [
                $this->lineService->textMessage('❌ ไม่พบข้อมูลลูกค้า')
            ]);
            return;
        }

        $bodyContents = [
            $this->lineService->infoRow('🏥 ชื่อ', $customer->name),
            $this->lineService->separator(),
            $this->lineService->infoRow('💧 น้ำหนักเปียก', number_format($customer->total_wet_weight, 2) . ' kg'),
            $this->lineService->infoRow('☀️ น้ำหนักแห้ง', number_format($customer->total_dry_weight, 2) . ' kg'),
            $this->lineService->infoRow('📝 น้ำหนักแก้ไข', number_format($customer->total_edit_weight, 2) . ' kg'),
            $this->lineService->separator(),
            $this->lineService->infoRow('💰 ยอดเงินรวม', number_format($customer->total_billing_payment, 2) . ' ฿', '#1DB446'),
        ];

        $bubble = $this->lineService->bubbleContainer(
            '👥 ข้อมูลลูกค้า',
            $customer->name,
            $bodyContents,
            '#0066CC'
        );

        $flexMessage = $this->lineService->flexMessage('ข้อมูลลูกค้า: ' . $customer->name, $bubble);
        $this->lineService->replyMessage($replyToken, [$flexMessage]);
    }

    /**
     * ส่งเมนูสต๊อก
     */
    protected function sendInventoryMenu(string $replyToken): void
    {
        $groups = InventoryGroup::take(10)->get();

        if ($groups->isEmpty()) {
            $this->lineService->replyMessage($replyToken, [
                $this->lineService->textMessage('❌ ไม่พบข้อมูลกลุ่มวัตถุดิบ')
            ]);
            return;
        }

        $quickReplyItems = [];
        foreach ($groups as $group) {
            $quickReplyItems[] = $this->lineService->quickReplyItem(
                $group->name,
                'postback',
                'action=inventory_group&id=' . $group->id
            );
        }

        $message = $this->lineService->quickReply('📦 เลือกประเภทวัตถุดิบ:', $quickReplyItems);
        $this->lineService->replyMessage($replyToken, [$message]);
    }

    /**
     * แสดงวัตถุดิบตามกลุ่ม
     */
    protected function showInventoriesByGroup(string $replyToken, $groupId): void
    {
        $inventories = Inventory::where('inventory_group_id', $groupId)->take(10)->get();

        if ($inventories->isEmpty()) {
            $this->lineService->replyMessage($replyToken, [
                $this->lineService->textMessage('❌ ไม่พบวัตถุดิบในกลุ่มนี้')
            ]);
            return;
        }

        $bodyContents = [];
        foreach ($inventories as $index => $item) {
            $remainColor = $item->remain_quantity < ($item->total_quantity * 0.2) ? '#FF0000' : '#1DB446';
            $bodyContents[] = $this->lineService->infoRow(
                $item->name,
                number_format($item->remain_quantity) . ' ' . $item->unit,
                $remainColor
            );

            if ($index < count($inventories) - 1) {
                $bodyContents[] = [
                    'type' => 'separator',
                    'margin' => 'sm',
                ];
            }
        }

        $group = InventoryGroup::find($groupId);
        $bubble = $this->lineService->bubbleContainer(
            '📦 สต๊อกวัตถุดิบ',
            $group->name ?? 'วัตถุดิบ',
            $bodyContents,
            '#FF6B35'
        );

        $flexMessage = $this->lineService->flexMessage('สต๊อกวัตถุดิบ', $bubble);
        $this->lineService->replyMessage($replyToken, [$flexMessage]);
    }

    /**
     * ส่งเมนูพลังงาน
     */
    protected function sendEnergyMenu(string $replyToken): void
    {
        $resources = EnergyResource::take(10)->get();

        if ($resources->isEmpty()) {
            $this->lineService->replyMessage($replyToken, [
                $this->lineService->textMessage('❌ ไม่พบข้อมูลพลังงาน')
            ]);
            return;
        }

        $quickReplyItems = [];
        foreach ($resources as $resource) {
            $quickReplyItems[] = $this->lineService->quickReplyItem(
                $resource->name,
                'postback',
                'action=energy_type&id=' . $resource->id
            );
        }

        $message = $this->lineService->quickReply('⚡ เลือกประเภทพลังงาน:', $quickReplyItems);
        $this->lineService->replyMessage($replyToken, [$message]);
    }

    /**
     * แสดง Log พลังงาน
     */
    protected function showEnergyLogs(string $replyToken, $resourceId): void
    {
        $resource = EnergyResource::find($resourceId);

        if (!$resource) {
            $this->lineService->replyMessage($replyToken, [
                $this->lineService->textMessage('❌ ไม่พบข้อมูลพลังงาน')
            ]);
            return;
        }

        $logs = EnergyResourceLog::where('energy_resource_id', $resourceId)
            ->orderBy('created_at', 'desc')
            ->take(7)
            ->get();

        $totalValue = $logs->sum('value');

        $bodyContents = [
            $this->lineService->infoRow('📊 รวม 7 วัน', number_format($totalValue, 2), '#1DB446'),
            $this->lineService->separator(),
        ];

        foreach ($logs as $log) {
            $date = Carbon::parse($log->created_at)->format('d/m');
            $bodyContents[] = $this->lineService->infoRow(
                $date,
                number_format($log->value, 2) . ' ' . ($log->unit ?? '')
            );
        }

        $bubble = $this->lineService->bubbleContainer(
            '⚡ ' . $resource->name,
            'ประวัติการใช้งาน',
            $bodyContents,
            '#FFD700'
        );

        $flexMessage = $this->lineService->flexMessage('พลังงาน: ' . $resource->name, $bubble);
        $this->lineService->replyMessage($replyToken, [$flexMessage]);
    }

    /**
     * ส่งเมนูพนักงาน
     */
    protected function sendEmployeeMenu(string $replyToken): void
    {
        $departments = Department::take(10)->get();

        if ($departments->isEmpty()) {
            $this->lineService->replyMessage($replyToken, [
                $this->lineService->textMessage('❌ ไม่พบข้อมูลแผนก')
            ]);
            return;
        }

        $quickReplyItems = [];
        foreach ($departments as $dept) {
            $quickReplyItems[] = $this->lineService->quickReplyItem(
                $dept->name,
                'postback',
                'action=department&id=' . $dept->id
            );
        }

        $message = $this->lineService->quickReply('👷 เลือกแผนก:', $quickReplyItems);
        $this->lineService->replyMessage($replyToken, [$message]);
    }

    /**
     * แสดงพนักงานตามแผนก
     */
    protected function showEmployeesByDepartment(string $replyToken, $deptId): void
    {
        $employees = Employee::where('department_id', $deptId)->take(10)->get();
        $department = Department::find($deptId);

        if ($employees->isEmpty()) {
            $this->lineService->replyMessage($replyToken, [
                $this->lineService->textMessage('❌ ไม่พบพนักงานในแผนกนี้')
            ]);
            return;
        }

        $bodyContents = [];
        foreach ($employees as $index => $emp) {
            $bodyContents[] = [
                'type' => 'text',
                'text' => ($index + 1) . '. ' . $emp->name,
                'size' => 'sm',
                'margin' => 'sm',
            ];
        }

        $bubble = $this->lineService->bubbleContainer(
            '👷 แผนก' . ($department->name ?? ''),
            'รายชื่อพนักงาน',
            $bodyContents,
            '#9B59B6'
        );

        $flexMessage = $this->lineService->flexMessage('พนักงาน', $bubble);
        $this->lineService->replyMessage($replyToken, [$flexMessage]);
    }

    /**
     * ส่งสถานะเครื่องจักร
     */
    protected function sendMachineStatus(string $replyToken): void
    {
        $washingMachines = WashingMachine::take(10)->get();
        $dryerMachines = DryerMachine::take(10)->get();

        $bodyContents = [
            [
                'type' => 'text',
                'text' => '🧺 เครื่องซัก',
                'size' => 'sm',
                'weight' => 'bold',
            ],
        ];

        foreach ($washingMachines as $machine) {
            $bodyContents[] = $this->lineService->infoRow(
                $machine->name,
                number_format($machine->maximum_weight) . ' kg'
            );
        }

        $bodyContents[] = $this->lineService->separator();
        $bodyContents[] = [
            'type' => 'text',
            'text' => '🌡️ เครื่องอบ',
            'size' => 'sm',
            'weight' => 'bold',
            'margin' => 'md',
        ];

        foreach ($dryerMachines as $machine) {
            $bodyContents[] = $this->lineService->infoRow(
                $machine->name,
                number_format($machine->maximum_weight) . ' kg'
            );
        }

        $bubble = $this->lineService->bubbleContainer(
            '⚙️ เครื่องจักร',
            'รายการทั้งหมด',
            $bodyContents,
            '#34495E'
        );

        $flexMessage = $this->lineService->flexMessage('เครื่องจักร', $bubble);
        $this->lineService->replyMessage($replyToken, [$flexMessage]);
    }
}
