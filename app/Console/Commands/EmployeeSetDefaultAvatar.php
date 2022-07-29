<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EmployeeSetDefaultAvatar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:set-default-avatar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'set default avatar for employee';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info("Start set default avatar for employee");
        Employee::whereNotNull('photo')->update(['photo' => null]);
        Log::info("End set default avatar for employee");
    }
}
