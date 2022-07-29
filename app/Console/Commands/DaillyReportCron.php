<?php

namespace App\Console\Commands;

use App\Managers\OperationManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DaillyReportCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dailyReport:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info("Start generate daily report");
        OperationManager::generateDailyReport();
        Log::info("End generate daily report");
    }
}
