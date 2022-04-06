<?php

namespace App\Managers;

use App\Enums\EmployeeOperationActionType;
use App\Enums\WorkerOperationStatus;
use App\Events\EmployeeOperationLogCreated;
use App\Managers\Manager;
use App\Models\EmployeeOperationLog;
use App\Models\EmployeeWorkingTime;

class EmployeeManager extends Manager
{
    public static function createEmployeeOperationLog($employeeId, WorkerOperationStatus $operationType, EmployeeOperationActionType $actionType)
    {
        $employeeOperationLog = new EmployeeOperationLog;
        $employeeOperationLog->employee_id = $employeeId;
        $employeeOperationLog->operation_type = $operationType;
        $employeeOperationLog->action_type = $actionType;
        $employeeOperationLog->save();

        EmployeeOperationLogCreated::dispatch($employeeOperationLog);
    }

    public static function calculateEmployeeWorkingTime(EmployeeOperationLog $employeeOperationLog, $workingDate)
    {
        $employeeWorkingTime = EmployeeWorkingTime::whereDate('working_date', $workingDate)
            ->where('employee_id', $employeeOperationLog->employee_id)
            ->first();

        if (!$employeeWorkingTime) {
            $employeeWorkingTime = new EmployeeWorkingTime;
            $employeeWorkingTime->working_date = $workingDate;
            $employeeWorkingTime->started_at = $workingDate;
            $employeeWorkingTime->employee_id = $employeeOperationLog->employee_id;
        }
        $timeDuration = (function () use ($workingDate, $employeeOperationLog) {
            $employeeOperationLogStartOfDay = EmployeeOperationLog::whereDate('created_at', $workingDate)
                ->where('employee_id', $employeeOperationLog->employee_id)
                ->orderBy('created_at', 'asc')
                ->first();

            $employeeOperationLogEndOfDay = EmployeeOperationLog::whereDate('created_at', $workingDate)
                ->where('employee_id', $employeeOperationLog->employee_id)
                ->orderBy('created_at', 'desc')
                ->first();

            $intervalInSeconds = 0;
            if ($employeeOperationLogStartOfDay && $employeeOperationLogEndOfDay) {
                $intervalDiff = $employeeOperationLogStartOfDay->created_at->diff($employeeOperationLogEndOfDay->created_at);
                $intervalInSeconds = (function (\DateInterval $interval) {
                    return $interval->days * 86400 + $interval->h * 3600 + $interval->i * 60 + $interval->s;
                })($intervalDiff);
            }
            return $intervalInSeconds;
        })();
        $employeeWorkingTime->time_duration = $timeDuration;
        $employeeWorkingTime->ended_at = $workingDate;
        $employeeWorkingTime->save();
    }
}
