<?php

namespace App\Console\Commands;

use App\Enums\ExpenseType;
use App\Models\DepartmentDailyCostLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixDepartmentSalaryDateSyncToExpense extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expense:department-salary-date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix Department Salary Date Sync To Expense';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info("Start " . $this->description);
        $departmentDailyCostLogs = DepartmentDailyCostLog::get();
        foreach ($departmentDailyCostLogs as $key => $departmentDailyCostLog) {
            $createdAt = \Carbon\Carbon::parse($departmentDailyCostLog->daily_date);
            try {
                DB::table('expenses')
                    ->where('type_name', ExpenseType::DepartmentSalary())
                    ->where('table_name', 'department_daily_cost_logs')
                    ->where('table_id', $departmentDailyCostLog->id)
                    ->update(['created_at' => $createdAt]);
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
        Log::info("End " . $this->description);
    }
}
