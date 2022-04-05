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
use App\Models\ironerMachine;
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

    public function selectCustomer($operationId)
    {
        $operation = Operation::with('employee')->where('id', $operationId)->first();
        $customerGroup = CustomerGroup::with('customers')->get();
        return view('worker.operations.iron.select-customer', ['customerGroups' => $customerGroup->toArray(), 'operation' => $operation->toArray()]);
    }

    public function setSelectCustomer($operationId, $customerId)
    {
        $operation = Operation::find($operationId);
        $operation->customer_id = $customerId;
        $operation->save();
        return redirect(route('worker.operation.iron.employee-summary', ['operationId' => $operation->id]));
    }

    public function getEmployeeSummary($operationId)
    {
        $operation = Operation::with('employee')->with('customer')->with('ironEmployee')->where('id', $operationId)->first();

        $summaryReports = $operation->ironSummaryReport();
        $workingDuration = $operation->ironEmployee->getTotalTimeDurationOfWorkingTime();
        $operationTimeDuration = $operation->timeDuration();
        $operationLinenProducts = OperationLinenProduct::with('linenProduct')->where('operation_id', $operationId)->get();

        return view('worker.operations.iron.employee-summary', ['operation' => $operation->toArray(), 'summaryReports' => $summaryReports, 'workingDuration' => $workingDuration, 'operationLinenProducts' => $operationLinenProducts->toArray(), 'operationTimeDuration' => $operationTimeDuration]);
    }

    public function setClose($operationId)
    {
        $operation = Operation::find($operationId);
        $operation->status = OperationStatus::Close();
        $operation->save();

        EmployeeManager::createEmployeeOperationLog($operation->iron_employee_id, WorkerOperationStatus::Iron(), EmployeeOperationActionType::Stop());
        return redirect(route('worker.operation.iron.employee-summary', ['operationId' => $operation->id]));
    }

    public function setInProgress($operationId)
    {
        $operation = Operation::find($operationId);
        $operation->status = OperationStatus::InProgress();
        $operation->save();

        EmployeeManager::createEmployeeOperationLog($operation->iron_employee_id, WorkerOperationStatus::Iron(), EmployeeOperationActionType::Progress());
        return redirect(route('worker.operation.iron.employee-summary', ['operationId' => $operation->id]));
    }

    public function selectOperationLinenProduct($operationId)
    {
        $operation = Operation::with('employee')->with('customer')->with('ironEmployee')->where('id', $operationId)->first();
        $operationLinenProducts = OperationLinenProduct::with('linenProduct')->where('operation_id', $operationId)->get();

        return view('worker.operations.iron.select-operation-linen-product', ['operation' => $operation->toArray(), 'operationLinenProducts' => $operationLinenProducts->toArray()]);
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
        $operation = Operation::with('employee')->with('customer')->with('ironEmployee')->where('id', $operationId)->first();

        $operationLinenCases = OperationLinenCase::$list;
        return view('worker.operations.iron.select-linen-case', ['operation' => $operation->toArray(), 'operationLinenProduct' => $operationLinenProduct, 'operationLinenCases' => $operationLinenCases]);
    }
}
