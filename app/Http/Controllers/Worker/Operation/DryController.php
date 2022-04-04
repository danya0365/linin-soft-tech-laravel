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
        $operation->dryer_machine_id = $dryerMachineId;
        $operation->save();

        if ($prevDryerMachineId) DryerMachine::where('id', $prevDryerMachineId)->update(['operation_id' => null]);
        DryerMachine::where('id', $dryerMachineId)->update(['operation_id' => $operation->id]);
        return redirect(route('worker.operation.dry.employee-summary', ['operationId' => $operation->id]));
    }

    public function getEmployeeSummary($operationId)
    {
        $operation = Operation::with('employee')->with('customer')->with('dryerMachine')->with('dryEmployee')->where('id', $operationId)->first();

        $summaryReports = $operation->drySummaryReport();
        $workingDuration = $operation->dryEmployee->getTotalTimeDurationOfWorkingTime();
        $operationTimeDuration = $operation->timeDuration();
        $operationLinenProducts = OperationLinenProduct::with('linenProduct')->where('operation_id', $operationId)->get();

        return view('worker.operations.dry.employee-summary', ['operation' => $operation->toArray(), 'summaryReports' => $summaryReports, 'workingDuration' => $workingDuration, 'operationLinenProducts' => $operationLinenProducts->toArray(), 'operationTimeDuration' => $operationTimeDuration]);
    }


    public function setClose($operationId)
    {
        $operation = Operation::find($operationId);
        $operation->status = OperationStatus::Close();
        $operation->save();

        EmployeeManager::createEmployeeOperationLog($operation->dry_employee_id, WorkerOperationStatus::Dry(), EmployeeOperationActionType::Stop());
        return redirect(route('worker.operation.dry.employee-summary', ['operationId' => $operation->id]));
    }

    public function setInProgress($operationId)
    {
        $operation = Operation::find($operationId);
        $operation->status = OperationStatus::InProgress();
        $operation->save();

        EmployeeManager::createEmployeeOperationLog($operation->dry_employee_id, WorkerOperationStatus::Dry(), EmployeeOperationActionType::Progress());
        return redirect(route('worker.operation.dry.employee-summary', ['operationId' => $operation->id]));
    }

    public function selectOperationLinenProduct($operationId)
    {
        $operation = Operation::with('employee')->with('customer')->with('dryerMachine')->with('dryEmployee')->where('id', $operationId)->first();
        $operationLinenProducts = OperationLinenProduct::with('linenProduct')->where('operation_id', $operationId)->get();

        return view('worker.operations.dry.select-operation-linen-product', ['operation' => $operation->toArray(), 'operationLinenProducts' => $operationLinenProducts->toArray()]);
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
        $operation = Operation::with('employee')->with('customer')->with('dryerMachine')->with('dryEmployee')->where('id', $operationId)->first();

        $operationLinenCases = OperationLinenCase::$list;
        return view('worker.operations.dry.select-linen-case', ['operation' => $operation->toArray(), 'operationLinenProduct' => $operationLinenProduct, 'operationLinenCases' => $operationLinenCases]);
    }

    public function setSelectLinenCase($operationId, $operationLinenProductId, $linenCase)
    {
        $operationLinenProduct = OperationLinenProduct::find($operationLinenProductId);
        $operationLinenProduct->linen_case = $linenCase;
        $operationLinenProduct->save();

        $operation = Operation::find($operationId);
        $operation->updateRelateFields();

        return redirect(route('worker.operation.dry.select-linen-product', ['operationId' => $operation->id, 'operationLinenProductId' => $operationLinenProduct->id]));
    }

    public function selectLinenProduct($operationId, $operationLinenProductId)
    {
        $operation = Operation::with('employee')->with('customer')->with('dryerMachine')->with('dryEmployee')->where('id', $operationId)->first();
        $operationLinenProduct = OperationLinenProduct::find($operationLinenProductId);
        $linenTypes = LinenType::with('linenProducts')->get();
        $linenProducts = LinenProduct::all();
        return view('worker.operations.dry.select-linen-product', ['operation' => $operation->toArray(), 'operationLinenProduct' => $operationLinenProduct->toArray(), 'linenTypes' => $linenTypes->toArray(), 'linenProductJson' => $linenProducts->toJson()]);
    }

    public function setSelectLinenProduct($operationId, $operationLinenProductId, $linenProductId)
    {
        $linenProduct = LinenProduct::find($linenProductId);

        $operationLinenProduct = OperationLinenProduct::find($operationLinenProductId);
        $operationLinenProduct->linen_product_id = $linenProduct->id;
        $operationLinenProduct->save();

        $operation = Operation::find($operationId);
        $operation->updateRelateFields();

        return redirect(route('worker.operation.dry.select-weight-and-color', ['operationId' => $operation->id, 'operationLinenProductId' => $operationLinenProduct->id]));
    }

    public function selectWeightAndColor($operationId, $operationLinenProductId)
    {
        $operation = Operation::with('employee')->with('customer')->with('dryerMachine')->with('dryerMachine')->where('id', $operationId)->first();
        $operationLinenProduct = OperationLinenProduct::with('linenProduct')->where('id', $operationLinenProductId)->first();
        return view('worker.operations.dry.select-weight-and-color', ['operation' => $operation->toArray(), 'operationLinenProduct' => $operationLinenProduct->toArray()]);
    }

    public function setSelectWeightAndColor($operationId, $operationLinenProductId)
    {
        request()->validate(['dry_weight' => 'required', 'color' => 'required']);

        $operationLinenProduct = OperationLinenProduct::find($operationLinenProductId);
        $operationLinenProduct->dry_weight = request()->get('dry_weight');
        $operationLinenProduct->color = request()->get('color');
        $operationLinenProduct->save();

        $operation = Operation::find($operationId);
        $operation->updateRelateFields();

        EmployeeManager::createEmployeeOperationLog($operation->dry_employee_id, WorkerOperationStatus::Dry(), EmployeeOperationActionType::Progress());

        return redirect(route('worker.operation.dry.employee-summary', ['operationId' => $operation->id]));
    }

    public function deleteOperationLinenProduct($operationId, $operationLinenProductId)
    {
        OperationLinenProduct::where('id', $operationLinenProductId)->delete();
        $operation = Operation::find($operationId);
        $operation->updateRelateFields();

        return redirect(route('worker.operation.dry.select-operation-linen-product', ['operationId' => $operationId]));
    }
}
