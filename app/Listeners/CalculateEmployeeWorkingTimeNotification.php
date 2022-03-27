<?php

namespace App\Listeners;

use App\Events\EmployeeOperationLogCreated;
use App\Managers\EmployeeManager;
use App\Models\EmployeeOperationLog;
use App\Models\EmployeeWorkingTime;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CalculateEmployeeWorkingTimeNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\EmployeeOperationLogCreated  $event
     * @return void
     */
    public function handle(EmployeeOperationLogCreated $event)
    {
        $employeeOperationLog = $event->employeeOperationLog;

        $todayDate = Carbon::today();

        EmployeeManager::calculateEmployeeWorkingTime($employeeOperationLog, $todayDate);
    }
}
