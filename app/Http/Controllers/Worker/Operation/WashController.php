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
use App\Models\OperationLinenCase;
use App\Models\OperationLinenProduct;
use App\Models\WashingMachine;
use Illuminate\Support\Facades\DB;

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
        return redirect(route('worker.operation.wash.select-washing-machine', ['operationId' => $operation->id]));
    }

    public function selectWashingMachine($operationId)
    {
        $operation = Operation::with('employee')->with('customer')->where('id', $operationId)->first();
        $washingMachines = WashingMachine::with('operation')->get();
        return view('worker.operations.wash.select-washing-machine', ['operation' => $operation->toArray(), 'washingMachines' => $washingMachines->toArray()]);
    }

    public function setSelectWashingMachine($operationId, $washingMachineId)
    {
        $operation = Operation::find($operationId);
        $prevWashingMachineId = $operation->washing_machine_id;
        $operation->washing_machine_id = $washingMachineId;
        $operation->save();

        if ($prevWashingMachineId) WashingMachine::where('id', $prevWashingMachineId)->update(['operation_id' => null]);
        WashingMachine::where('id', $washingMachineId)->update(['operation_id' => $operation->id]);
        return redirect(route('worker.operation.wash.employee-summary', ['operationId' => $operation->id]));
    }

    public function getEmployeeSummary($operationId)
    {
        $operation = Operation::with('employee')->with('customer')->with('washingMachine')->with('washEmployee')->where('id', $operationId)->first();

        $summaryReports = $operation->washSummaryReport();
        $workingDuration = $operation->washEmployee->getTotalTimeDurationOfWorkingTime();

        return view('worker.operations.wash.employee-summary', ['operation' => $operation->toArray(), 'summaryReports' => $summaryReports, 'workingDuration' => $workingDuration]);
    }

    public function selectLinenCase($operationId, $operationLinenProductId)
    {
        if ($operationLinenProductId == 0) {
            $operationLinenProduct = new OperationLinenProduct;
            $operationLinenProduct->operation_id = $operationId;
            $operationLinenProduct->save();
            $operationLinenProductId = $operationLinenProduct->id;
        }
        $operationLinenProduct = OperationLinenProduct::find($operationLinenProductId);
        $operation = Operation::with('employee')->with('customer')->with('washingMachine')->with('washEmployee')->where('id', $operationId)->first();
        $operationLinenCases = OperationLinenCase::$list;
        return view('worker.operations.wash.select-linen-case', ['operation' => $operation->toArray(), 'operationLinenProduct' => $operationLinenProduct, 'operationLinenCases' => $operationLinenCases]);
    }

    public function setSelectLinenCase($operationId, $operationLinenProductId, $linenCase)
    {
        $operationLinenProduct = OperationLinenProduct::find($operationLinenProductId);
        $operationLinenProduct->linen_case = $linenCase;
        $operationLinenProduct->save();

        $operation = Operation::find($operationId);
        $operation->generateSearchTag();
        return redirect(route('worker.operation.wash.select-linen-product', ['operation' => $operation->id, 'operationLinenProduct' => $operationLinenProduct->id]));
    }
}
