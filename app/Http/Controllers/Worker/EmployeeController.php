<?php

namespace App\Http\Controllers\Worker;

use App\Enums\DepartmentNameId;
use App\Enums\OperationType;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LinenType;
use App\Models\Operation;
use App\Models\OperationLinenCase;
use App\Models\OperationLinenProduct;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index()
    {
        return redirect(route('worker.employee.select-employee'));
    }

    public function selectEmployee()
    {
        $departments = Department::with('employees')->whereIn(
            'id',
            [
                DepartmentNameId::Wash(),
                DepartmentNameId::Dry(),
                DepartmentNameId::Iron(),
                DepartmentNameId::Packing(),
                DepartmentNameId::Collect(),
            ]
        )->get();
        return view('worker.employees.select-employee', ['departments' => $departments]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getEmployeeSummary($employeeId)
    {
        $employee = Employee::with('department')->find($employeeId);
        $operationType = $employee->department->var_name;
        $summaryReports = (function () use ($employeeId, $operationType) {
            $query = OperationLinenProduct::with('linenProduct')->select(
                DB::raw('sum(wet_weight) as total_wet_weight'),
                DB::raw('sum(dry_weight) as total_dry_weight'),
                DB::raw('sum(iron_piece) as total_iron_piece'),
                DB::raw('sum(packing_piece) as total_packing_piece'),
                DB::raw('sum(collect_weight) as total_collect_weight'),
                DB::raw('sum(collect_pack) as total_collect_pack'),
                'linen_product_id'
            )
                ->join('operations', function ($join) {
                    $join->on('operations.id', '=', 'operation_id');
                })
                ->where('operations.employee_id', $employeeId)
                ->groupBy('linen_product_id');

            $dateStartAt = request()->get('date_start_at');
            $dateEndAt = request()->get('date_end_at');
            if ($dateStartAt && $dateEndAt) {
                $query->whereBetween('created_at', [$dateStartAt . ' 00:00:00', $dateEndAt . ' 23:59:59']);
            }
            $rows = $query->get();

            $totalOperationSummaries = [];
            $operationSummaries = [];

            if (!isset($totalOperationSummaries[$operationType])) {
                $totalOperationSummaries[$operationType] = 0;
            }
            if (!isset($operationSummaries[$operationType])) {
                $operationSummaries[$operationType] = [];
            }

            foreach ($rows as $row) {

                switch ($operationType) {
                    case OperationType::Wash():
                        $fieldValue = $row->total_wet_weight;
                        $totalOperationSummaries[$operationType] += $fieldValue;
                        $operationSummaries[$operationType][] = ['title' => $row->linenProduct->name, 'value' => $fieldValue];
                        break;
                    case OperationType::Dry():
                        $fieldValue = $row->total_dry_weight;
                        $totalOperationSummaries[$operationType] += $fieldValue;
                        $operationSummaries[$operationType][] = ['title' => $row->linenProduct->name, 'value' => $fieldValue];
                        break;
                    case OperationType::Iron():
                        $fieldValue = $row->total_iron_piece;
                        $totalOperationSummaries[$operationType] += $fieldValue;
                        $operationSummaries[$operationType][] = ['title' => $row->linenProduct->name, 'value' => $fieldValue];
                        break;
                    case OperationType::Packing():
                        $fieldValue = $row->total_packing_piece;
                        $totalOperationSummaries[$operationType] += $fieldValue;
                        $operationSummaries[$operationType][] = ['title' => $row->linenProduct->name, 'value' => $fieldValue];
                        break;
                    case OperationType::Collect():
                        $fieldValue = $row->total_collect_weight;
                        $totalOperationSummaries[$operationType] += $fieldValue;
                        $operationSummaries[$operationType][] = ['title' => $row->linenProduct->name, 'value' => $fieldValue];

                        $fieldValue = $row->total_collect_pack;
                        $totalOperationSummaries['collect_pack'] += $fieldValue;
                        $operationSummaries['collect_pack'][] = ['title' => $row->linenProduct->name, 'value' => $fieldValue];
                        break;

                    default:
                        break;
                }
            }

            $summaryReports = [];
            foreach ($totalOperationSummaries as $key => $totalOperationSummary) {
                if (!isset($summaryReports[$key])) {
                    $summaryReports[$key] = [];
                }
                $summaryReports[$key][] = ['title' => 'จำนวนที่ทำแล้ว', 'value' => $totalOperationSummary];
                $summaryReports[$key] = array_merge($summaryReports[$key], $operationSummaries[$key]);
            }
            return $summaryReports;
        })();

        $workingDuration = $employee->getTotalTimeDurationOfWorkingTime();

        return view(
            'worker.employees.employee-summary',
            [
                'summaryReports' => $summaryReports,
                'workingDuration' => $workingDuration,
                'employee' => $employee->toArray()
            ]
        );
    }
}
