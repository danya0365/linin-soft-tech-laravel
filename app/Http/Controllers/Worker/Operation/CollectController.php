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

    public function selectCustomer($operationId)
    {
        $operation = Operation::with('employee')->where('id', $operationId)->first();
        $customerGroup = CustomerGroup::with('customers')->get();
        return view('worker.operations.collect.select-customer', ['customerGroups' => $customerGroup->toArray(), 'operation' => $operation->toArray()]);
    }

    public function setSelectCustomer($operationId, $customerId)
    {
        $operation = Operation::find($operationId);
        $operation->customer_id = $customerId;
        $operation->save();
        return redirect(route('worker.operation.collect.employee-summary', ['operationId' => $operation->id]));
    }

    public function getEmployeeSummary($operationId)
    {
        $operation = Operation::with('employee')->with('customer')->with('collectEmployee')->where('id', $operationId)->first();

        $summaryReports = $operation->collectSummaryReport();
        $workingDuration = $operation->collectEmployee->getTotalTimeDurationOfWorkingTime();
        $operationTimeDuration = $operation->timeDuration();
        $operationLinenProducts = OperationLinenProduct::with('linenProduct')->where('operation_id', $operationId)->get();

        return view('worker.operations.collect.employee-summary', ['operation' => $operation->toArray(), 'summaryReports' => $summaryReports, 'workingDuration' => $workingDuration, 'operationLinenProducts' => $operationLinenProducts->toArray(), 'operationTimeDuration' => $operationTimeDuration]);
    }

    public function setClose($operationId)
    {
        $operation = Operation::find($operationId);
        $operation->status = OperationStatus::Close();
        $operation->save();

        OperationManager::createCustomerOperationDailySummary($operation);

        EmployeeManager::createEmployeeOperationLog($operation->collect_employee_id, WorkerOperationStatus::Collect(), EmployeeOperationActionType::Stop());
        return redirect(route('worker.operation.collect.employee-summary', ['operationId' => $operation->id]));
    }

    public function setInProgress($operationId)
    {
        $operation = Operation::find($operationId);
        $operation->status = OperationStatus::InProgress();
        $operation->save();

        EmployeeManager::createEmployeeOperationLog($operation->collect_employee_id, WorkerOperationStatus::Collect(), EmployeeOperationActionType::Progress());
        return redirect(route('worker.operation.collect.employee-summary', ['operationId' => $operation->id]));
    }

    public function selectOperationLinenProduct($operationId)
    {
        $operation = Operation::with('employee')->with('customer')->with('collectEmployee')->where('id', $operationId)->first();
        $operationLinenProducts = OperationLinenProduct::with('linenProduct')->where('operation_id', $operationId)->get();

        return view('worker.operations.collect.select-operation-linen-product', ['operation' => $operation->toArray(), 'operationLinenProducts' => $operationLinenProducts->toArray()]);
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
        $operation = Operation::with('employee')->with('customer')->with('collectEmployee')->where('id', $operationId)->first();

        $operationLinenCases = OperationLinenCase::$list;
        return view('worker.operations.collect.select-linen-case', ['operation' => $operation->toArray(), 'operationLinenProduct' => $operationLinenProduct, 'operationLinenCases' => $operationLinenCases]);
    }

    public function setSelectLinenCase($operationId, $operationLinenProductId, $linenCase)
    {
        $operationLinenProduct = OperationLinenProduct::find($operationLinenProductId);
        $operationLinenProduct->linen_case = $linenCase;
        $operationLinenProduct->save();

        $operation = Operation::find($operationId);
        $operation->updateRelateFields();

        return redirect(route('worker.operation.collect.select-linen-product', ['operationId' => $operation->id, 'operationLinenProductId' => $operationLinenProduct->id]));
    }

    public function selectLinenProduct($operationId, $operationLinenProductId)
    {
        $operation = Operation::with('employee')->with('customer')->with('collectEmployee')->where('id', $operationId)->first();
        $operationLinenProduct = OperationLinenProduct::find($operationLinenProductId);
        $linenTypes = LinenType::with('linenProducts')->get();
        $linenProducts = LinenProduct::all();
        return view('worker.operations.collect.select-linen-product', ['operation' => $operation->toArray(), 'operationLinenProduct' => $operationLinenProduct->toArray(), 'linenTypes' => $linenTypes->toArray(), 'linenProductJson' => $linenProducts->toJson()]);
    }

    public function setSelectLinenProduct($operationId, $operationLinenProductId, $linenProductId)
    {
        $linenProduct = LinenProduct::find($linenProductId);

        $operationLinenProduct = OperationLinenProduct::find($operationLinenProductId);
        $operationLinenProduct->linen_product_id = $linenProduct->id;
        $operationLinenProduct->save();

        $operation = Operation::find($operationId);
        $operation->updateRelateFields();

        return redirect(route('worker.operation.collect.select-weight-and-color', ['operationId' => $operation->id, 'operationLinenProductId' => $operationLinenProduct->id]));
    }

    public function selectWeightAndColor($operationId, $operationLinenProductId)
    {
        $operation = Operation::with('employee')->with('customer')->with('collectEmployee')->where('id', $operationId)->first();
        $operationLinenProduct = OperationLinenProduct::with('linenProduct')->where('id', $operationLinenProductId)->first();
        return view('worker.operations.collect.select-weight-and-color', ['operation' => $operation->toArray(), 'operationLinenProduct' => $operationLinenProduct->toArray()]);
    }

    public function setSelectWeightAndColor($operationId, $operationLinenProductId)
    {
        request()->validate(['collect_weight' => 'required', 'collect_pack' => 'required', 'color' => 'required']);

        $operationLinenProduct = OperationLinenProduct::find($operationLinenProductId);
        $operationLinenProductOldValue = $operationLinenProduct->getAttributes();
        $operationLinenProduct->collect_weight = request()->get('collect_weight');
        $operationLinenProduct->collect_pack = request()->get('collect_pack');
        $operationLinenProduct->color = request()->get('color');
        $operationLinenProductNewValue = $operationLinenProduct->getDirty();
        $operationLinenProduct->save();

        $operation = Operation::find($operationId);
        $operation->updateRelateFields();

        OperationManager::createOperationLog($operation->collect_employee_id, $operation, 'set_weight_and_color', $operationLinenProductOldValue, $operationLinenProductNewValue);
        EmployeeManager::createEmployeeOperationLog($operation->collect_employee_id, WorkerOperationStatus::Packing(), EmployeeOperationActionType::Progress());

        return redirect(route('worker.operation.collect.employee-summary', ['operationId' => $operation->id]));
    }

    public function deleteOperationLinenProduct($operationId, $operationLinenProductId)
    {
        OperationLinenProduct::where('id', $operationLinenProductId)->delete();
        $operation = Operation::find($operationId);
        $operation->updateRelateFields();

        OperationManager::createCustomerOperationDailySummary($operation);

        return redirect(route('worker.operation.collect.select-operation-linen-product', ['operationId' => $operationId]));
    }
}
