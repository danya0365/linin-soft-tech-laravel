<?php

namespace Database\Seeders;

use App\Enums\OperationType;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MockSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     * Creates comprehensive mock data for 1 year back for testing purposes.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Starting MockSeeder - Creating 1 year of historical data...');

        // Get existing data IDs for foreign key references
        $customerIds = DB::table('customers')->pluck('id')->toArray();
        $employeeIds = DB::table('employees')->pluck('id')->toArray();
        $linenProductIds = DB::table('linen_products')->pluck('id')->toArray();
        $washingMachineIds = DB::table('washing_machines')->pluck('id')->toArray();
        $dryerMachineIds = DB::table('dryer_machines')->pluck('id')->toArray();
        $truckIds = DB::table('trucks')->pluck('id')->toArray();
        $departmentIds = DB::table('departments')->pluck('id')->toArray();
        $energyResourceIds = DB::table('energy_resources')->pluck('id')->toArray();
        $inventoryIds = DB::table('inventories')->pluck('id')->toArray();
        $userIds = DB::table('users')->pluck('id')->toArray();

        // Check if we have required data
        if (empty($customerIds) || empty($employeeIds) || empty($linenProductIds)) {
            $this->command->error('Please run UserSeeder, DepartmentSeeder, LinenSeeder, CustomerSeeder first!');
            return;
        }

        $now = Carbon::now();
        $startDate = $now->copy()->subYear(); // 1 year back

        // 1. Seed Operations (365 days * ~3 operations per day = ~1095 operations)
        $this->command->info('Seeding Operations...');
        $operationIds = $this->seedOperations(
            $startDate,
            $now,
            $customerIds,
            $employeeIds,
            $washingMachineIds,
            $dryerMachineIds,
            $truckIds
        );

        // 2. Seed Operations Linen Products
        $this->command->info('Seeding Operations Linen Products...');
        $this->seedOperationsLinenProducts($operationIds, $linenProductIds);

        // 3. Seed Customer Operation Daily Summaries
        $this->command->info('Seeding Customer Operation Daily Summaries...');
        $this->seedCustomerOperationDailySummaries($startDate, $now, $customerIds);

        // 4. Seed Employee Operation Logs
        $this->command->info('Seeding Employee Operation Logs...');
        $this->seedEmployeeOperationLogs($startDate, $now, $employeeIds);

        // 5. Seed Employee Working Times
        $this->command->info('Seeding Employee Working Times...');
        $this->seedEmployeeWorkingTimes($startDate, $now, $employeeIds);

        // 6. Seed Energy Resource Logs
        $this->command->info('Seeding Energy Resource Logs...');
        $this->seedEnergyResourceLogs($startDate, $now, $employeeIds, $energyResourceIds);

        // 7. Seed Inventory Stock Logs
        $this->command->info('Seeding Inventory Stock Logs...');
        $this->seedInventoryStockLogs($startDate, $now, $employeeIds, $inventoryIds);

        // 8. Seed Department Daily Cost Logs
        $this->command->info('Seeding Department Daily Cost Logs...');
        $this->seedDepartmentDailyCostLogs($startDate, $now, $departmentIds);

        // 9. Seed Notes (maintenance records)
        $this->command->info('Seeding Notes...');
        $this->seedNotes($startDate, $now, $washingMachineIds, $dryerMachineIds, $truckIds);

        // 10. Seed Expenses
        $this->command->info('Seeding Expenses...');
        $this->seedExpenses($startDate, $now);

        // 11. Seed Incomes
        $this->command->info('Seeding Incomes...');
        $this->seedIncomes($startDate, $now, $customerIds);

        // 12. Seed Login Histories
        $this->command->info('Seeding Login Histories...');
        $this->seedLoginHistories($startDate, $now, $userIds);

        $this->command->info('MockSeeder completed successfully!');
    }

    /**
     * Seed Operations for 1 year
     */
    private function seedOperations($startDate, $endDate, $customerIds, $employeeIds, $washingMachineIds, $dryerMachineIds, $truckIds): array
    {
        $operationIds = [];
        $operationTypes = OperationType::getValues();
        $colors = ['white', 'blue', 'green', 'yellow', 'pink', 'gray'];

        $currentDate = $startDate->copy();
        $batchData = [];

        while ($currentDate->lte($endDate)) {
            // Skip some days randomly to simulate real-world data
            if (rand(1, 100) > 90) {
                $currentDate->addDay();
                continue;
            }

            // Create 2-5 operations per day
            $operationsPerDay = rand(2, 5);

            for ($i = 0; $i < $operationsPerDay; $i++) {
                foreach ($operationTypes as $type) {
                    // Not all operation types happen every day
                    if (rand(1, 100) > 60) continue;

                    $createdAt = $currentDate->copy()->addHours(rand(6, 18))->addMinutes(rand(0, 59));

                    $batchData[] = [
                        'operation_type' => $type,
                        'employee_id' => $this->randomOrNull($employeeIds, 95),
                        'customer_id' => $this->randomOrNull($customerIds, 90),
                        'wash_employee_id' => $type === 'wash' ? $this->randomOrNull($employeeIds, 80) : null,
                        'dry_employee_id' => $type === 'dry' ? $this->randomOrNull($employeeIds, 80) : null,
                        'iron_employee_id' => $type === 'iron' ? $this->randomOrNull($employeeIds, 80) : null,
                        'packing_employee_id' => $type === 'packing' ? $this->randomOrNull($employeeIds, 80) : null,
                        'collect_employee_id' => $type === 'collect' ? $this->randomOrNull($employeeIds, 80) : null,
                        'deliver_employee_id' => $type === 'deliver' ? $this->randomOrNull($employeeIds, 80) : null,
                        'washing_machine_id' => in_array($type, ['wash']) ? $this->randomOrNull($washingMachineIds, 90) : null,
                        'dryer_machine_id' => in_array($type, ['dry']) ? $this->randomOrNull($dryerMachineIds, 90) : null,
                        'truck_id' => in_array($type, ['collect', 'deliver']) ? $this->randomOrNull($truckIds, 90) : null,
                        'total_wet_weight' => in_array($type, ['wash', 'collect']) ? round(rand(50, 500) / 10, 1) : null,
                        'total_dry_weight' => in_array($type, ['dry']) ? round(rand(40, 450) / 10, 1) : null,
                        'total_iron_piece' => $type === 'iron' ? rand(10, 200) : null,
                        'total_packing_piece' => $type === 'packing' ? rand(10, 200) : null,
                        'total_collect_weight' => $type === 'collect' ? round(rand(50, 500) / 10, 1) : null,
                        'total_collect_pack' => $type === 'collect' ? rand(1, 20) : null,
                        'total_deliver_pack' => $type === 'deliver' ? rand(1, 20) : null,
                        'total_billing_weight' => $type === 'payment' ? round(rand(100, 1000) / 10, 1) : null,
                        'total_billing_payment' => $type === 'payment' ? round(rand(1000, 50000) / 100, 2) : null,
                        'billing_payment_date' => $type === 'payment' ? $currentDate->format('Y-m-d') : null,
                        'colors' => json_encode(array_slice($colors, 0, rand(1, 4))),
                        'search_tags' => json_encode(['mock', 'test', $type]),
                        'status' => rand(1, 100) > 10 ? 'close' : 'in-progress',
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];
                }
            }

            // Batch insert every 500 records for performance
            if (count($batchData) >= 500) {
                DB::table('operations')->insert($batchData);
                $batchData = [];
            }

            $currentDate->addDay();
        }

        // Insert remaining data
        if (!empty($batchData)) {
            DB::table('operations')->insert($batchData);
        }

        // Get all operation IDs
        $operationIds = DB::table('operations')->pluck('id')->toArray();

        return $operationIds;
    }

    /**
     * Seed Operations Linen Products
     */
    private function seedOperationsLinenProducts($operationIds, $linenProductIds): void
    {
        $batchData = [];
        $colors = ['white', 'blue', 'green', 'yellow', 'pink', 'gray'];
        $cases = ['new', 'edit'];

        foreach ($operationIds as $operationId) {
            // 1-5 linen products per operation
            $itemCount = rand(1, 5);
            for ($i = 0; $i < $itemCount; $i++) {
                $batchData[] = [
                    'operation_id' => $operationId,
                    'linen_product_id' => $linenProductIds[array_rand($linenProductIds)],
                    'linen_case' => $cases[array_rand($cases)],
                    'color' => $colors[array_rand($colors)],
                    'wet_weight' => round(rand(10, 100) / 10, 1),
                    'dry_weight' => round(rand(8, 90) / 10, 1),
                    'iron_piece' => rand(0, 50),
                    'packing_piece' => rand(0, 50),
                    'collect_weight' => round(rand(10, 100) / 10, 1),
                    'collect_pack' => rand(0, 10),
                    'deliver_pack' => rand(0, 10),
                    'deliver_operation_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Batch insert every 1000 records
            if (count($batchData) >= 1000) {
                DB::table('operations_linen_products')->insert($batchData);
                $batchData = [];
            }
        }

        if (!empty($batchData)) {
            DB::table('operations_linen_products')->insert($batchData);
        }
    }

    /**
     * Seed Customer Operation Daily Summaries
     */
    private function seedCustomerOperationDailySummaries($startDate, $endDate, $customerIds): void
    {
        $batchData = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            // Create summary for 3-10 random customers per day
            $customersToday = array_rand(array_flip($customerIds), min(rand(3, 10), count($customerIds)));
            if (!is_array($customersToday)) {
                $customersToday = [$customersToday];
            }

            foreach ($customersToday as $customerId) {
                $batchData[] = [
                    'customer_id' => $customerId,
                    'operation_date' => $currentDate->format('Y-m-d'),
                    'total_wet_weight' => round(rand(100, 1000) / 10, 1),
                    'total_dry_weight' => round(rand(80, 900) / 10, 1),
                    'total_iron_piece' => rand(20, 300),
                    'total_packing_piece' => rand(20, 300),
                    'total_edit_collect_weight' => round(rand(0, 100) / 10, 1),
                    'total_collect_weight' => round(rand(100, 1000) / 10, 1),
                    'total_collect_pack' => rand(5, 50),
                    'total_delivery_pack' => rand(5, 50),
                    'total_billing_weight' => round(rand(100, 1000) / 10, 1),
                    'total_billing_payment' => round(rand(5000, 100000) / 100, 2),
                    'created_at' => $currentDate,
                    'updated_at' => $currentDate,
                ];
            }

            // Batch insert every 500 records
            if (count($batchData) >= 500) {
                DB::table('customer_operation_daily_summaries')->insert($batchData);
                $batchData = [];
            }

            $currentDate->addDay();
        }

        if (!empty($batchData)) {
            DB::table('customer_operation_daily_summaries')->insert($batchData);
        }
    }

    /**
     * Seed Employee Operation Logs
     */
    private function seedEmployeeOperationLogs($startDate, $endDate, $employeeIds): void
    {
        $batchData = [];
        $operationTypes = OperationType::getValues();
        $actionTypes = ['start', 'progress', 'stop'];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            // 5-15 logs per day
            $logsPerDay = rand(5, 15);
            for ($i = 0; $i < $logsPerDay; $i++) {
                $createdAt = $currentDate->copy()->addHours(rand(6, 18))->addMinutes(rand(0, 59));
                $batchData[] = [
                    'employee_id' => $employeeIds[array_rand($employeeIds)],
                    'operation_type' => $operationTypes[array_rand($operationTypes)],
                    'action_type' => $actionTypes[array_rand($actionTypes)],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }

            if (count($batchData) >= 500) {
                DB::table('employee_operation_logs')->insert($batchData);
                $batchData = [];
            }

            $currentDate->addDay();
        }

        if (!empty($batchData)) {
            DB::table('employee_operation_logs')->insert($batchData);
        }
    }

    /**
     * Seed Employee Working Times
     */
    private function seedEmployeeWorkingTimes($startDate, $endDate, $employeeIds): void
    {
        $batchData = [];
        $currentDate = $startDate->copy();
        $processedKeys = []; // To ensure unique employee_id + working_date

        while ($currentDate->lte($endDate)) {
            // 10-30 employees work per day
            $workingEmployees = array_rand(array_flip($employeeIds), min(rand(10, 30), count($employeeIds)));
            if (!is_array($workingEmployees)) {
                $workingEmployees = [$workingEmployees];
            }

            foreach ($workingEmployees as $employeeId) {
                $key = $employeeId . '_' . $currentDate->format('Y-m-d');
                if (isset($processedKeys[$key])) continue;
                $processedKeys[$key] = true;

                $startHour = rand(6, 9);
                $endHour = rand(15, 20);
                $startedAt = $currentDate->copy()->setHour($startHour)->setMinute(rand(0, 59));
                $endedAt = $currentDate->copy()->setHour($endHour)->setMinute(rand(0, 59));
                $duration = $endedAt->diffInMinutes($startedAt);

                $batchData[] = [
                    'employee_id' => $employeeId,
                    'working_date' => $currentDate->format('Y-m-d'),
                    'started_at' => $startedAt,
                    'ended_at' => $endedAt,
                    'time_duration' => $duration,
                    'created_at' => $startedAt,
                    'updated_at' => $endedAt,
                ];
            }

            if (count($batchData) >= 500) {
                DB::table('employee_working_times')->insert($batchData);
                $batchData = [];
            }

            $currentDate->addDay();
        }

        if (!empty($batchData)) {
            DB::table('employee_working_times')->insert($batchData);
        }
    }

    /**
     * Seed Energy Resource Logs
     */
    private function seedEnergyResourceLogs($startDate, $endDate, $employeeIds, $energyResourceIds): void
    {
        if (empty($energyResourceIds)) {
            $this->command->warn('No energy resources found, skipping energy resource logs.');
            return;
        }

        $batchData = [];
        $currentDate = $startDate->copy();
        $units = ['หน่วย', 'ลิตร', 'กิโลกรัม', 'ถัง'];

        while ($currentDate->lte($endDate)) {
            // 1-3 logs per day per resource
            foreach ($energyResourceIds as $resourceId) {
                if (rand(1, 100) > 70) continue; // Skip some days

                $createdAt = $currentDate->copy()->addHours(rand(8, 16));
                $batchData[] = [
                    'employee_id' => $this->randomOrNull($employeeIds, 90),
                    'energy_resource_id' => $resourceId,
                    'value' => rand(10, 500),
                    'unit' => $units[array_rand($units)],
                    'lot_number' => 'LOT-' . $currentDate->format('Ymd') . '-' . rand(1000, 9999),
                    'cost' => round(rand(100, 10000) / 10, 2),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }

            if (count($batchData) >= 500) {
                DB::table('energy_resource_logs')->insert($batchData);
                $batchData = [];
            }

            $currentDate->addDay();
        }

        if (!empty($batchData)) {
            DB::table('energy_resource_logs')->insert($batchData);
        }
    }

    /**
     * Seed Inventory Stock Logs
     */
    private function seedInventoryStockLogs($startDate, $endDate, $employeeIds, $inventoryIds): void
    {
        if (empty($inventoryIds)) {
            $this->command->warn('No inventories found, skipping inventory stock logs.');
            return;
        }

        $batchData = [];
        $currentDate = $startDate->copy();
        $types = ['export', 'import'];

        while ($currentDate->lte($endDate)) {
            // 2-8 stock movements per day
            $logsPerDay = rand(2, 8);
            for ($i = 0; $i < $logsPerDay; $i++) {
                $createdAt = $currentDate->copy()->addHours(rand(8, 17));
                $batchData[] = [
                    'employee_id' => $this->randomOrNull($employeeIds, 90),
                    'inventory_id' => $inventoryIds[array_rand($inventoryIds)],
                    'type' => $types[array_rand($types)],
                    'quantity' => rand(1, 100),
                    'cost' => round(rand(100, 50000) / 100, 2),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }

            if (count($batchData) >= 500) {
                DB::table('inventory_stock_logs')->insert($batchData);
                $batchData = [];
            }

            $currentDate->addDay();
        }

        if (!empty($batchData)) {
            DB::table('inventory_stock_logs')->insert($batchData);
        }
    }

    /**
     * Seed Department Daily Cost Logs
     */
    private function seedDepartmentDailyCostLogs($startDate, $endDate, $departmentIds): void
    {
        if (empty($departmentIds)) {
            $this->command->warn('No departments found, skipping department daily cost logs.');
            return;
        }

        $batchData = [];
        $currentDate = $startDate->copy();
        $messages = [
            'ค่าน้ำมัน',
            'ค่าอะไหล่',
            'ค่าซ่อมบำรุง',
            'ค่าวัสดุสิ้นเปลือง',
            'ค่าอาหาร',
            'ค่าเดินทาง',
            'ค่าใช้จ่ายเบ็ดเตล็ด',
        ];

        while ($currentDate->lte($endDate)) {
            // 1-3 departments have costs each day
            $deptCount = min(rand(1, 3), count($departmentIds));
            $selectedDepts = array_rand(array_flip($departmentIds), $deptCount);
            if (!is_array($selectedDepts)) {
                $selectedDepts = [$selectedDepts];
            }

            foreach ($selectedDepts as $deptId) {
                $createdAt = $currentDate->copy()->addHours(rand(8, 17));
                $batchData[] = [
                    'department_id' => $deptId,
                    'daily_date' => $currentDate->format('Y-m-d'),
                    'cost' => round(rand(100, 10000) / 10, 2),
                    'message' => $messages[array_rand($messages)],
                    'image_url' => null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }

            if (count($batchData) >= 500) {
                DB::table('department_daily_cost_logs')->insert($batchData);
                $batchData = [];
            }

            $currentDate->addDay();
        }

        if (!empty($batchData)) {
            DB::table('department_daily_cost_logs')->insert($batchData);
        }
    }

    /**
     * Seed Notes (maintenance records)
     */
    private function seedNotes($startDate, $endDate, $washingMachineIds, $dryerMachineIds, $truckIds): void
    {
        $batchData = [];
        $currentDate = $startDate->copy();
        $maintenanceMessages = [
            'ตรวจเช็คประจำเดือน',
            'เปลี่ยนสายพาน',
            'ซ่อมมอเตอร์',
            'เปลี่ยนน้ำมัน',
            'ตรวจเช็คระบบไฟฟ้า',
            'เปลี่ยนยาง',
            'ซ่อมระบบเบรค',
            'ล้างทำความสะอาด',
            'เปลี่ยนฟิลเตอร์',
            'ปรับแต่งเครื่อง',
        ];
        $tags = ['ซ่อม', 'บำรุงรักษา', 'ตรวจสอบ', 'เปลี่ยนอะไหล่', 'ทำความสะอาด'];

        while ($currentDate->lte($endDate)) {
            // 0-3 notes per week
            if ($currentDate->dayOfWeek === 1 && rand(1, 100) > 30) { // Mondays mostly
                $notesCount = rand(1, 3);
                for ($i = 0; $i < $notesCount; $i++) {
                    $createdAt = $currentDate->copy()->addHours(rand(8, 16));

                    // Randomly assign to washing machine, dryer machine, or truck
                    $type = rand(1, 3);
                    $washingMachineId = null;
                    $dryerMachineId = null;
                    $truckId = null;

                    if ($type === 1 && !empty($washingMachineIds)) {
                        $washingMachineId = $washingMachineIds[array_rand($washingMachineIds)];
                    } elseif ($type === 2 && !empty($dryerMachineIds)) {
                        $dryerMachineId = $dryerMachineIds[array_rand($dryerMachineIds)];
                    } elseif (!empty($truckIds)) {
                        $truckId = $truckIds[array_rand($truckIds)];
                    }

                    $selectedTags = array_slice($tags, 0, rand(1, 3));

                    $batchData[] = [
                        'message' => $maintenanceMessages[array_rand($maintenanceMessages)],
                        'image_url' => null,
                        'cost' => round(rand(100, 50000) / 100, 2),
                        'washing_machine_id' => $washingMachineId,
                        'dryer_machine_id' => $dryerMachineId,
                        'truck_id' => $truckId,
                        'tags' => json_encode($selectedTags),
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];
                }
            }

            if (count($batchData) >= 100) {
                DB::table('notes')->insert($batchData);
                $batchData = [];
            }

            $currentDate->addDay();
        }

        if (!empty($batchData)) {
            DB::table('notes')->insert($batchData);
        }
    }

    /**
     * Seed Expenses
     */
    private function seedExpenses($startDate, $endDate): void
    {
        $batchData = [];
        $currentDate = $startDate->copy();
        $expenseTypes = [
            'ค่าน้ำมัน',
            'ค่าไฟฟ้า',
            'ค่าน้ำ',
            'ค่าแก๊ส',
            'ค่าแรง',
            'ค่าซ่อมบำรุง',
            'ค่าวัสดุสิ้นเปลือง',
            'ค่าเช่า',
            'ค่าประกัน',
            'ค่าใช้จ่ายอื่นๆ',
        ];

        while ($currentDate->lte($endDate)) {
            // 1-5 expenses per day
            $expensesPerDay = rand(1, 5);
            for ($i = 0; $i < $expensesPerDay; $i++) {
                $createdAt = $currentDate->copy()->addHours(rand(8, 17));
                $batchData[] = [
                    'type_name' => $expenseTypes[array_rand($expenseTypes)],
                    'table_name' => null,
                    'table_id' => null,
                    'amount' => round(rand(100, 100000) / 100, 2),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }

            if (count($batchData) >= 500) {
                DB::table('expenses')->insert($batchData);
                $batchData = [];
            }

            $currentDate->addDay();
        }

        if (!empty($batchData)) {
            DB::table('expenses')->insert($batchData);
        }
    }

    /**
     * Seed Incomes
     */
    private function seedIncomes($startDate, $endDate, $customerIds): void
    {
        $batchData = [];
        $currentDate = $startDate->copy();
        $incomeTypes = [
            'ค่าซักผ้า',
            'ค่าอบผ้า',
            'ค่ารีดผ้า',
            'ค่าแพ็คผ้า',
            'ค่าขนส่ง',
            'รายได้อื่นๆ',
        ];

        while ($currentDate->lte($endDate)) {
            // 2-8 incomes per day
            $incomesPerDay = rand(2, 8);
            for ($i = 0; $i < $incomesPerDay; $i++) {
                $createdAt = $currentDate->copy()->addHours(rand(8, 17));
                $batchData[] = [
                    'type_name' => $incomeTypes[array_rand($incomeTypes)],
                    'table_name' => 'customers',
                    'table_id' => $customerIds[array_rand($customerIds)],
                    'amount' => round(rand(1000, 500000) / 100, 2),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }

            if (count($batchData) >= 500) {
                DB::table('incomes')->insert($batchData);
                $batchData = [];
            }

            $currentDate->addDay();
        }

        if (!empty($batchData)) {
            DB::table('incomes')->insert($batchData);
        }
    }

    /**
     * Seed Login Histories
     */
    private function seedLoginHistories($startDate, $endDate, $userIds): void
    {
        if (empty($userIds)) {
            $this->command->warn('No users found, skipping login histories.');
            return;
        }

        $batchData = [];
        $currentDate = $startDate->copy();
        $users = DB::table('users')->whereIn('id', $userIds)->get(['id', 'name', 'email'])->keyBy('id');

        while ($currentDate->lte($endDate)) {
            // 3-10 logins per day
            $loginsPerDay = rand(3, 10);
            for ($i = 0; $i < $loginsPerDay; $i++) {
                $userId = $userIds[array_rand($userIds)];
                $user = $users[$userId] ?? null;
                if (!$user) continue;

                $createdAt = $currentDate->copy()->addHours(rand(6, 22))->addMinutes(rand(0, 59));
                $batchData[] = [
                    'user_id' => $userId,
                    'name' => $user->name ?? 'Unknown',
                    'email' => $user->email ?? 'unknown@example.com',
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }

            if (count($batchData) >= 500) {
                DB::table('login_histories')->insert($batchData);
                $batchData = [];
            }

            $currentDate->addDay();
        }

        if (!empty($batchData)) {
            DB::table('login_histories')->insert($batchData);
        }
    }

    /**
     * Helper: Get random value from array or null based on probability
     */
    private function randomOrNull(array $array, int $probability = 80)
    {
        if (empty($array)) return null;
        if (rand(1, 100) <= $probability) {
            return $array[array_rand($array)];
        }
        return null;
    }
}
