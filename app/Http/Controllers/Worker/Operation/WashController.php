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
use App\Models\Operation;

class WashController extends Controller
{
    public function index()
    {
        return redirect(route('worker.operation.wash.select-employee'));
    }

    public function selectEmployee()
    {
        $departments = Department::with('employees')->where('id', DepartmentNameId::Wash())->get();
        return view('worker.operations.wash.select-employee', ['departments' => $departments->toArray()]);
    }

    public function setSelectEmployee($employeeId)
    {
        $operation = new Operation();
        $operation->employee_id = $employeeId;
        $operation->wash_employee_id = $employeeId;
        $operation->operation_type = OperationType::Wash();
        $operation->status = OperationStatus::InProgress();
        $operation->save();

        EmployeeManager::createEmployeeOperationLog($employeeId, WorkerOperationStatus::Wash(), EmployeeOperationActionType::Start());

        return redirect(route('worker.operation.wash.select-customer', ['operationId' => $operation->id]));
    }

    public function selectCustomer($operationId)
    {
        $operation = Operation::with('employee')->where('id', $operationId)->first();
        $customerGroup = CustomerGroup::with('customers')->get();
        return view('worker.operations.wash.select-customer', ['customerGroups' => $customerGroup->toArray(), 'operation' => $operation->toArray()]);
    }

    public function setSelectCustomer($operationId, $customerId)
    {
        $operation = Operation::find($operationId);
        $operation->customer_id = $customerId;
        $operation->save();
        return redirect(route('worker.operation.wash.select-linen-case', ['operationId' => $operation->id]));
    }
}
