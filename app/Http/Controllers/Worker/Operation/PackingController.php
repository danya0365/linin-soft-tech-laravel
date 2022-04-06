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

class PackingController extends Controller
{
    public function index()
    {
        return redirect(route('worker.operation.packing.select-employee'));
    }

    public function selectEmployee()
    {
        $departments = Department::with('employees')->where('id', DepartmentNameId::Packing())->get();
        return view('worker.operations.packing.select-employee', ['departments' => $departments->toArray()]);
    }

    public function setSelectEmployee($employeeId)
    {
        $operation = new Operation();
        $operation->employee_id = $employeeId;
        $operation->packing_employee_id = $employeeId;
        $operation->operation_type = OperationType::Packing();
        $operation->status = OperationStatus::InProgress();
        $operation->save();

        EmployeeManager::createEmployeeOperationLog($employeeId, WorkerOperationStatus::Packing(), EmployeeOperationActionType::Start());

        return redirect(route('worker.operation.packing.select-customer', ['operationId' => $operation->id]));
    }

    public function selectCustomer($operationId)
    {
        $operation = Operation::with('employee')->where('id', $operationId)->first();
        $customerGroup = CustomerGroup::with('customers')->get();
        return view('worker.operations.packing.select-customer', ['customerGroups' => $customerGroup->toArray(), 'operation' => $operation->toArray()]);
    }

    public function setSelectCustomer($operationId, $customerId)
    {
        $operation = Operation::find($operationId);
        $operation->customer_id = $customerId;
        $operation->save();
        return redirect(route('worker.operation.packing.employee-summary', ['operationId' => $operation->id]));
    }

    public function getEmployeeSummary($operationId)
    {
        $operation = Operation::with('employee')->with('customer')->with('packingEmployee')->where('id', $operationId)->first();

        $summaryReports = $operation->packingSummaryReport();
        $workingDuration = $operation->packingEmployee->getTotalTimeDurationOfWorkingTime();
        $operationTimeDuration = $operation->timeDuration();
        $operationLinenProducts = OperationLinenProduct::with('linenProduct')->where('operation_id', $operationId)->get();

        return view('worker.operations.packing.employee-summary', ['operation' => $operation->toArray(), 'summaryReports' => $summaryReports, 'workingDuration' => $workingDuration, 'operationLinenProducts' => $operationLinenProducts->toArray(), 'operationTimeDuration' => $operationTimeDuration]);
    }

    public function setClose($operationId)
    {
        $operation = Operation::find($operationId);
        $operation->status = OperationStatus::Close();
        $operation->save();

        EmployeeManager::createEmployeeOperationLog($operation->packing_employee_id, WorkerOperationStatus::Packing(), EmployeeOperationActionType::Stop());
        return redirect(route('worker.operation.packing.employee-summary', ['operationId' => $operation->id]));
    }

    public function setInProgress($operationId)
    {
        $operation = Operation::find($operationId);
        $operation->status = OperationStatus::InProgress();
        $operation->save();

        EmployeeManager::createEmployeeOperationLog($operation->packing_employee_id, WorkerOperationStatus::Packing(), EmployeeOperationActionType::Progress());
        return redirect(route('worker.operation.packing.employee-summary', ['operationId' => $operation->id]));
    }

    public function selectOperationLinenProduct($operationId)
    {
        $operation = Operation::with('employee')->with('customer')->with('packingEmployee')->where('id', $operationId)->first();
        $operationLinenProducts = OperationLinenProduct::with('linenProduct')->where('operation_id', $operationId)->get();

        return view('worker.operations.packing.select-operation-linen-product', ['operation' => $operation->toArray(), 'operationLinenProducts' => $operationLinenProducts->toArray()]);
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
        $operation = Operation::with('employee')->with('customer')->with('packingEmployee')->where('id', $operationId)->first();

        $operationLinenCases = OperationLinenCase::$list;
        return view('worker.operations.packing.select-linen-case', ['operation' => $operation->toArray(), 'operationLinenProduct' => $operationLinenProduct, 'operationLinenCases' => $operationLinenCases]);
    }

    public function setSelectLinenCase($operationId, $operationLinenProductId, $linenCase)
    {
        $operationLinenProduct = OperationLinenProduct::find($operationLinenProductId);
        $operationLinenProduct->linen_case = $linenCase;
        $operationLinenProduct->save();

        $operation = Operation::find($operationId);
        $operation->updateRelateFields();

        return redirect(route('worker.operation.packing.select-linen-product', ['operationId' => $operation->id, 'operationLinenProductId' => $operationLinenProduct->id]));
    }

    public function selectLinenProduct($operationId, $operationLinenProductId)
    {
        $operation = Operation::with('employee')->with('customer')->with('packingEmployee')->where('id', $operationId)->first();
        $operationLinenProduct = OperationLinenProduct::find($operationLinenProductId);
        $linenTypes = LinenType::with('linenProducts')->get();
        $linenProducts = LinenProduct::all();
        return view('worker.operations.packing.select-linen-product', ['operation' => $operation->toArray(), 'operationLinenProduct' => $operationLinenProduct->toArray(), 'linenTypes' => $linenTypes->toArray(), 'linenProductJson' => $linenProducts->toJson()]);
    }

    public function setSelectLinenProduct($operationId, $operationLinenProductId, $linenProductId)
    {
        $linenProduct = LinenProduct::find($linenProductId);

        $operationLinenProduct = OperationLinenProduct::find($operationLinenProductId);
        $operationLinenProduct->linen_product_id = $linenProduct->id;
        $operationLinenProduct->save();

        $operation = Operation::find($operationId);
        $operation->updateRelateFields();

        return redirect(route('worker.operation.packing.select-weight-and-color', ['operationId' => $operation->id, 'operationLinenProductId' => $operationLinenProduct->id]));
    }
}
