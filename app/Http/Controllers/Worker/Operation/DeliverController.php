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
use App\Models\Department;
use App\Models\Operation;
use App\Models\OperationLinenProduct;
use App\Models\Truck;

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

        return redirect(route('worker.operation.deliver.select-truck', ['operationId' => $operation->id]));
    }


    public function selectTruck($operationId)
    {
        $operation = Operation::withSum('deliverOperationLinenProducts', 'deliver_pack')->with('employee')->with('deliverEmployee')->where('id', $operationId)->first();
        $trucks = Truck::with('operation')->get();
        return view('worker.operations.deliver.select-truck', ['trucks' => $trucks->toArray(), 'operation' => $operation->toArray()]);
    }

    public function setSelectTruck($operationId, $truckId)
    {
        $operation = Operation::find($operationId);
        $truck = Truck::find($truckId);
        if ($truck->operation_id && $truck->operation_id != $operation->id) {
            return back()->with('error', 'กรุณาเลือกรถคันอื่น - Please select another truck.')->with('operation_id', $truck->operation_id);
        }

        $prevTruckId = $operation->truck_id;
        $operation->truck_id = $truckId;
        $operation->save();

        if ($prevTruckId) Truck::where('id', $prevTruckId)->update(['operation_id' => null]);
        Truck::where('id', $truckId)->update(['operation_id' => $operation->id]);

        return redirect(route('worker.operation.deliver.employee-summary', ['operationId' => $operation->id]));
    }

    public function getEmployeeSummary($operationId)
    {
        $operation = Operation::withSum('deliverOperationLinenProducts', 'deliver_pack')->with('employee')->with('truck')->with('deliverEmployee')->where('id', $operationId)->first();

        $summaryReports = $operation->deliverSummaryReport();
        $workingDuration = $operation->deliverEmployee->getTotalTimeDurationOfWorkingTime();
        $operationTimeDuration = $operation->timeDuration();
        $operationLinenProducts = OperationLinenProduct::with('linenProduct')->where('deliver_operation_id', $operationId)->get();

        return view('worker.operations.deliver.employee-summary', ['operation' => $operation->toArray(), 'summaryReports' => $summaryReports, 'workingDuration' => $workingDuration, 'operationLinenProducts' => $operationLinenProducts->toArray(), 'operationTimeDuration' => $operationTimeDuration]);
    }

    public function setClose($operationId)
    {
        $operation = Operation::find($operationId);
        $operation->status = OperationStatus::Close();
        $operation->save();

        if ($operation->truck_id) Truck::where('id', $operation->truck_id)->update(['operation_id' => null]);
        OperationManager::createCustomerOperationDailySummary($operation);

        EmployeeManager::createEmployeeOperationLog($operation->deliver_employee_id, WorkerOperationStatus::Deliver(), EmployeeOperationActionType::Stop());
        return redirect(route('worker.operation.deliver.employee-summary', ['operationId' => $operation->id]));
    }

    public function setInProgress($operationId)
    {
        $operation = Operation::find($operationId);
        $operation->status = OperationStatus::InProgress();
        $operation->save();

        if ($operation->truck_id) Truck::where('id', $operation->truck_id)->update(['operation_id' => null]);
        OperationManager::createCustomerOperationDailySummary($operation);

        EmployeeManager::createEmployeeOperationLog($operation->deliver_employee_id, WorkerOperationStatus::Deliver(), EmployeeOperationActionType::Progress());
        return redirect(route('worker.operation.deliver.employee-summary', ['operationId' => $operation->id]));
    }

    public function selectCollectOperation($operationId)
    {
        $operation = Operation::withSum('deliverOperationLinenProducts', 'deliver_pack')->with('employee')->with('truck')->with('deliverEmployee')->where('id', $operationId)->first();
        if (request()->isMethod('post')) {

            OperationLinenProduct::where('deliver_operation_id', $operationId)->update(
                [
                    'deliver_pack' => null,
                    'deliver_operation_id' => null
                ]
            );

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

            return redirect(route('worker.operation.deliver.employee-summary', ['operationId' => $operation->id]));
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
                $query->where('status', OperationStatus::Close());
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
