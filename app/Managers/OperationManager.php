<?php

namespace App\Managers;

use App\Managers\Manager;
use App\Models\Operation;
use App\Models\OperationLog;

class OperationManager extends Manager
{
    public static function createOperationLog($employeeId, Operation $operation, $actionName, $oldValues, $newValues)
    {
        $newValueKeys = array_keys($newValues);
        $oldValues = array_filter($oldValues, fn ($key) => in_array($key, $newValueKeys), ARRAY_FILTER_USE_KEY);
        $operationLog = new OperationLog();
        $operationLog->employee_id = $employeeId;
        $operationLog->operation_id = $operation->id;
        $operationLog->action_name = $actionName;
        $operationLog->old_values = json_encode($oldValues);
        $operationLog->new_values = json_encode($newValues);
        $operationLog->save();
    }
}
