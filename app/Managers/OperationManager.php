<?php

namespace App\Managers;

use App\Enums\EmployeeOperationActionType;
use App\Enums\WorkerOperationStatus;
use App\Events\EmployeeOperationLogCreated;
use App\Managers\Manager;
use App\Models\EmployeeOperationLog;
use App\Models\EmployeeWorkingTime;
use App\Models\Operation;
use App\Models\OperationLog;

class OperationManager extends Manager
{
    public static function createOperationLog($employeeId, Operation $operation, $actionName, $oldValues, $newValues)
    {
        $operationLog = new OperationLog();
        $operationLog->employee_id = $employeeId;
        $operationLog->operation_id = $operation->id;
        $operationLog->action_name = $actionName;
        $operationLog->old_values = $oldValues;
        $operationLog->new_values = $newValues;
        $operationLog->save();
    }
}
