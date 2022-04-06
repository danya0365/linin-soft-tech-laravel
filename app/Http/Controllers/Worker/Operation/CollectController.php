<?php

namespace App\Http\Controllers\Worker\Operation;

use App\Enums\DepartmentNameId;
use App\Enums\EmployeeOperationActionType;
use App\Enums\OperationStatus;
use App\Enums\OperationType;
use App\Enums\WorkerOperationStatus;
use App\Http\Controllers\Controller;
use App\Managers\EmployeeManager;
use App\Models\CustomerGroup;
use App\Models\Department;
use App\Models\LinenProduct;
use App\Models\LinenType;
use App\Models\Operation;
use App\Models\OperationLinenCase;
use App\Models\OperationLinenProduct;

class CollectController extends Controller
{
    public function index()
    {
        return redirect(route('worker.operation.collect.select-employee'));
    }

    public function selectEmployee()
    {
        $departments = Department::with('employees')->where('id', DepartmentNameId::Collect())->get();
        return view('worker.operations.collect.select-employee', ['departments' => $departments->toArray()]);
    }

    public function setSelectEmployee($employeeId)
    {
        $operation = new Operation();
        $operation->employee_id = $employeeId;
        $operation->collect_employee_id = $employeeId;
        $operation->operation_type = OperationType::Collect();
        $operation->status = OperationStatus::InProgress();
        $operation->save();

        EmployeeManager::createEmployeeOperationLog($employeeId, WorkerOperationStatus::Collect(), EmployeeOperationActionType::Start());

        return redirect(route('worker.operation.collect.select-customer', ['operationId' => $operation->id]));
    }
}
