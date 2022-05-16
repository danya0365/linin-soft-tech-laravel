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
use App\Models\OperationLinenProduct;

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
        $operation->deliver_employee_id = $employeeId;
        $operation->operation_type = OperationType::Deliver();
        $operation->status = OperationStatus::InProgress();
        $operation->save();

        EmployeeManager::createEmployeeOperationLog($employeeId, WorkerOperationStatus::Deliver(), EmployeeOperationActionType::Start());

        return redirect(route('worker.operation.deliver.select-collect-operation', ['operationId' => $operation->id]));
    }

    public function selectCollectOperation($operationId)
    {
        $operation = Operation::with('employee')->with('deliverEmployee')->where('id', $operationId)->first();
        if (request()->isMethod('post')) {

            $operationLinenProducts = request()->get('operationLinenProducts');
            foreach ($operationLinenProducts as $operationLinenProductId => $value) {
                if (!$value) continue;
                OperationLinenProduct::where('id', $operationLinenProductId)->update(
                    [
                        'deliver_pack' => $value,
                        'deliver_operation_id' => $operationId
                    ]
                );
            }

            return redirect(route('worker.operation.deliver.select-truck', ['operationId' => $operation->id]));
        }

        $sortOrderSelected = 'id-desc';
        $operationTypeSelected = OperationType::Collect();
        $query = OperationLinenProduct::with(['operation' => function ($query) {
            $query->with('employee')->with('customer');
        }]);

        $query->where(function ($query) use ($operationTypeSelected) {
            $query->whereHas('operation', function ($query) use ($operationTypeSelected) {
                if ($operationTypeSelected) {
                    $query->where('operation_type', $operationTypeSelected);
                }
            });
        });

        $dateStartAt = request()->get('date_start_at');
        $dateEndAt = request()->get('date_end_at');
        if ($dateStartAt && $dateEndAt) {
            $query->whereBetween('created_at', [$dateStartAt . ' 00:00:00', $dateEndAt . ' 23:59:59']);
        }
        if ($sortOrderSelected) {
            list($sort, $order) = explode('-', $sortOrderSelected);
            $query->orderBy($sort, $order);
        }

        $operations = $query->paginate();

        return view('worker.operations.deliver.select-collect-operation', ['operations' => $operations, 'operation' => $operation->toArray()]);
    }
}