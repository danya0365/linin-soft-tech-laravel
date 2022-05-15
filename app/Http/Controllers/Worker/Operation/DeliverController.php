<?php

namespace App\Http\Controllers\Worker\Operation;

use App\Enums\DepartmentNameId;
use App\Enums\EmployeeOperationActionType;
use App\Enums\OperationStatus;
use App\Enums\OperationType;
use App\Enums\WorkerOperationStatus;
use App\Http\Controllers\Controller;
use App\Managers\EmployeeManager;
use App\Models\Department;
use App\Models\Operation;

class DeliverController extends Controller
{
    public function index()
    {
        return redirect(route('worker.operation.deliver.select-employee'));
    }

    public function selectEmployee()
    {
        $departments = Department::with('employees')->where('id', DepartmentNameId::Deliver())->get();
        return view('worker.operations.deliver.select-employee', ['departments' => $departments->toArray()]);
    }

    public function setSelectEmployee($employeeId)
    {
        $operation = new Operation();
        $operation->employee_id = $employeeId;
        $operation->collect_employee_id = $employeeId;
        $operation->operation_type = OperationType::Deliver();
        $operation->status = OperationStatus::InProgress();
        $operation->save();

        EmployeeManager::createEmployeeOperationLog($employeeId, WorkerOperationStatus::Deliver(), EmployeeOperationActionType::Start());

        return redirect(route('worker.operation.deliver.select-collect-operation', ['operationId' => $operation->id]));
    }

    public function selectCollectOperation()
    {
        $departments = Department::with('employees')->where('id', DepartmentNameId::Deliver())->get();
        return view('worker.operations.deliver.select-employee', ['departments' => $departments->toArray()]);
    }

    public function setSelectCollectOperation($employeeId)
    {
        $operation = new Operation();
        $operation->employee_id = $employeeId;
        $operation->collect_employee_id = $employeeId;
        $operation->operation_type = OperationType::Deliver();
        $operation->status = OperationStatus::InProgress();
        $operation->save();

        EmployeeManager::createEmployeeOperationLog($employeeId, WorkerOperationStatus::Deliver(), EmployeeOperationActionType::Start());

        return redirect(route('worker.operation.deliver.select-collect-operation', ['operationId' => $operation->id]));
    }
}