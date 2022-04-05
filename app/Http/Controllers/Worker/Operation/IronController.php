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
use App\Models\DryerMachine;
use App\Models\LinenProduct;
use App\Models\LinenType;
use App\Models\Operation;
use App\Models\OperationLinenCase;
use App\Models\OperationLinenProduct;

class IronController extends Controller
{
    public function index()
    {
        return redirect(route('worker.operation.iron.select-employee'));
    }

    public function selectEmployee()
    {
        $departments = Department::with('employees')->where('id', DepartmentNameId::Iron())->get();
        return view('worker.operations.iron.select-employee', ['departments' => $departments->toArray()]);
    }

    public function setSelectEmployee($employeeId)
    {
        $operation = new Operation();
        $operation->employee_id = $employeeId;
        $operation->iron_employee_id = $employeeId;
        $operation->operation_type = OperationType::Iron();
        $operation->status = OperationStatus::InProgress();
        $operation->save();

        EmployeeManager::createEmployeeOperationLog($employeeId, WorkerOperationStatus::Iron(), EmployeeOperationActionType::Start());

        return redirect(route('worker.operation.iron.select-customer', ['operationId' => $operation->id]));
    }
}
