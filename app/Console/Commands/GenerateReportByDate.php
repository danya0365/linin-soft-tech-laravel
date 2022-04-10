<?php

namespace App\Console\Commands;

use App\Managers\OperationManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateReportByDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generateReport:date {date}';

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
        $date = $this->argument('date');
        Log::info("Start generate report by {$date}");
        OperationManager::generateReportByDate($date);
        Log::info("End generate report by {$date}");
    }
}
