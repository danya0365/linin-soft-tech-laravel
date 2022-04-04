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
use App\Models\WashingMachine;

class DryController extends Controller
{
    public function index()
    {
        return redirect(route('worker.operation.dry.select-employee'));
    }

    public function selectEmployee()
    {
        $departments = Department::with('employees')->where('id', DepartmentNameId::Dry())->get();
        return view('worker.operations.dry.select-employee', ['departments' => $departments->toArray()]);
    }

    public function setSelectEmployee($employeeId)
    {
        $operation = new Operation();
        $operation->employee_id = $employeeId;
        $operation->dry_employee_id = $employeeId;
        $operation->operation_type = OperationType::Dry();
        $operation->status = OperationStatus::InProgress();
        $operation->save();

        EmployeeManager::createEmployeeOperationLog($employeeId, WorkerOperationStatus::Dry(), EmployeeOperationActionType::Start());

        return redirect(route('worker.operation.dry.select-customer', ['operationId' => $operation->id]));
    }

    public function selectCustomer($operationId)
    {
        $operation = Operation::with('employee')->where('id', $operationId)->first();
        $customerGroup = CustomerGroup::with('customers')->get();
        return view('worker.operations.dry.select-customer', ['customerGroups' => $customerGroup->toArray(), 'operation' => $operation->toArray()]);
    }

    public function setSelectCustomer($operationId, $customerId)
    {
        $operation = Operation::find($operationId);
        $operation->customer_id = $customerId;
        $operation->save();
        return redirect(route('worker.operation.dry.select-dryer-machine', ['operationId' => $operation->id]));
    }

    public function selectDryerMachine($operationId)
    {
        $operation = Operation::with('employee')->with('customer')->where('id', $operationId)->first();
        $dryerMachines = DryerMachine::with('operation')->get();
        return view('worker.operations.dry.select-dryer-machine', ['operation' => $operation->toArray(), 'dryerMachines' => $dryerMachines->toArray()]);
    }

    public function setSelectDryerMachine($operationId, $dryerMachineId)
    {
        $operation = Operation::find($operationId);
        $prevDryerMachineId = $operation->dryer_machine_id;
        $operation->washing_machine_id = $dryerMachineId;
        $operation->save();

        if ($prevDryerMachineId) DryerMachine::where('id', $prevDryerMachineId)->update(['operation_id' => null]);
        DryerMachine::where('id', $dryerMachineId)->update(['operation_id' => $operation->id]);
        return redirect(route('worker.operation.dry.employee-summary', ['operationId' => $operation->id]));
    }
}
