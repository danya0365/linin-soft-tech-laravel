<?php

namespace App\Http\Controllers\Worker\Operation;

use App\Enums\DepartmentNameId;
use App\Enums\EmployeeOperationActionType;
use App\Enums\OperationStatus;
use App\Enums\OperationType;
use App\Enums\WorkerOperationStatus;
use App\Http\Controllers\Controller;
use App\Managers\EmployeeManager;
use App\Managers\OperationManager;
use App\Models\CustomerGroup;
use App\Models\Department;
use App\Models\LinenProduct;
use App\Models\LinenType;
use App\Models\Operation;
use App\Models\OperationLinenCase;
use App\Models\OperationLinenProduct;
use App\Models\WashingMachine;

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
        $operationTimeDuration = $operation->timeDuration();
        $operationLinenProducts = OperationLinenProduct::with('linenProduct')->where('operation_id', $operationId)->get();

        return view('worker.operations.wash.employee-summary', ['operation' => $operation->toArray(), 'summaryReports' => $summaryReports, 'workingDuration' => $workingDuration, 'operationLinenProducts' => $operationLinenProducts->toArray(), 'operationTimeDuration' => $operationTimeDuration]);
    }


    public function setClose($operationId)
    {
        $operation = Operation::find($operationId);
        $operation->status = OperationStatus::Close();
        $operation->save();

        EmployeeManager::createEmployeeOperationLog($operation->wash_employee_id, WorkerOperationStatus::Wash(), EmployeeOperationActionType::Stop());
        return redirect(route('worker.operation.wash.employee-summary', ['operationId' => $operation->id]));
    }

    public function setInProgress($operationId)
    {
        $operation = Operation::find($operationId);
        $operation->status = OperationStatus::InProgress();
        $operation->save();

        EmployeeManager::createEmployeeOperationLog($operation->wash_employee_id, WorkerOperationStatus::Wash(), EmployeeOperationActionType::Progress());
        return redirect(route('worker.operation.wash.employee-summary', ['operationId' => $operation->id]));
    }

    public function selectOperationLinenProduct($operationId)
    {
        $operation = Operation::with('employee')->with('customer')->with('washingMachine')->with('washEmployee')->where('id', $operationId)->first();
        $operationLinenProducts = OperationLinenProduct::with('linenProduct')->where('operation_id', $operationId)->get();

        return view('worker.operations.wash.select-operation-linen-product', ['operation' => $operation->toArray(), 'operationLinenProducts' => $operationLinenProducts->toArray()]);
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
        $operation->updateRelateFields();

        return redirect(route('worker.operation.wash.select-linen-product', ['operationId' => $operation->id, 'operationLinenProductId' => $operationLinenProduct->id]));
    }

    public function selectLinenProduct($operationId, $operationLinenProductId)
    {
        $operation = Operation::with('employee')->with('customer')->with('washingMachine')->with('washEmployee')->where('id', $operationId)->first();
        $operationLinenProduct = OperationLinenProduct::find($operationLinenProductId);
        $linenTypes = LinenType::with('linenProducts')->get();
        $linenProducts = LinenProduct::all();
        return view('worker.operations.wash.select-linen-product', ['operation' => $operation->toArray(), 'operationLinenProduct' => $operationLinenProduct->toArray(), 'linenTypes' => $linenTypes->toArray(), 'linenProductJson' => $linenProducts->toJson()]);
    }

    public function setSelectLinenProduct($operationId, $operationLinenProductId, $linenProductId)
    {
        $linenProduct = LinenProduct::find($linenProductId);

        $operationLinenProduct = OperationLinenProduct::find($operationLinenProductId);
        $operationLinenProduct->linen_product_id = $linenProduct->id;
        $operationLinenProduct->save();

        $operation = Operation::find($operationId);
        $operation->updateRelateFields();

        return redirect(route('worker.operation.wash.select-weight-and-color', ['operationId' => $operation->id, 'operationLinenProductId' => $operationLinenProduct->id]));
    }

    public function selectWeightAndColor($operationId, $operationLinenProductId)
    {
        $operation = Operation::with('employee')->with('customer')->with('washingMachine')->with('washEmployee')->where('id', $operationId)->first();
        $operationLinenProduct = OperationLinenProduct::with('linenProduct')->where('id', $operationLinenProductId)->first();
        return view('worker.operations.wash.select-weight-and-color', ['operation' => $operation->toArray(), 'operationLinenProduct' => $operationLinenProduct->toArray()]);
    }

    public function setSelectWeightAndColor($operationId, $operationLinenProductId)
    {
        request()->validate(['wet_weight' => 'required', 'color' => 'required']);

        $operationLinenProduct = OperationLinenProduct::find($operationLinenProductId);
        $operationLinenProductOldValue = $operationLinenProduct->getAttributes();
        $operationLinenProduct->wet_weight = request()->get('wet_weight');
        $operationLinenProduct->color = request()->get('color');
        $operationLinenProductNewValue = $operationLinenProduct->getDirty();
        $operationLinenProduct->save();

        $operation = Operation::find($operationId);
        $operation->updateRelateFields();

        OperationManager::createOperationLog($operation->wash_employee_id, $operation, 'set_weight_and_color', $operationLinenProductOldValue, $operationLinenProductNewValue);
        EmployeeManager::createEmployeeOperationLog($operation->wash_employee_id, WorkerOperationStatus::Wash(), EmployeeOperationActionType::Progress());

        return redirect(route('worker.operation.wash.employee-summary', ['operationId' => $operation->id]));
    }

    public function deleteOperationLinenProduct($operationId, $operationLinenProductId)
    {
        OperationLinenProduct::where('id', $operationLinenProductId)->delete();
        $operation = Operation::find($operationId);
        $operation->updateRelateFields();

        return redirect(route('worker.operation.wash.select-operation-linen-product', ['operationId' => $operationId]));
    }
}
