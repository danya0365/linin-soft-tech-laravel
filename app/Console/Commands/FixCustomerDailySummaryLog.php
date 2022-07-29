<?php

namespace App\Console\Commands;

use App\Managers\OperationManager;
use App\Models\CustomerOperationDailySummary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FixCustomerDailySummaryLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customerDailySummary:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate Customer Dailly Summary';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info("Start " . $this->description);
        $query = CustomerOperationDailySummary::select('operation_date')
            ->groupBy('operation_date');
        $customerOperationDailySummaries = $query->get();
        foreach ($customerOperationDailySummaries as $key => $customerOperationDailySummary) {
            $date = $customerOperationDailySummary->operation_date;
            Log::info("generateReportByDate " . $date);
            OperationManager::generateReportByDate($date);
            sleep(1);
        }
        Log::info("End " . $this->description);
    }
}
