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
use Carbon\Carbon;
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
                DepartmentNameId::Deliver(),
                DepartmentNameId::Maintenance(),
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
        $dateStartAt = request()->get('date_start_at');
        $dateEndAt = request()->get('date_end_at');

        $summaryReports = (function () use ($employeeId, $operationType, $dateStartAt, $dateEndAt) {
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

            if ($dateStartAt && $dateEndAt) {
                $query->whereBetween('operations_linen_products.created_at', [$dateStartAt . ' 00:00:00', $dateEndAt . ' 23:59:59']);
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
                        $operationSummaries[$operationType][] = ['title' => $row->linenProduct->name ?? '-', 'value' => $fieldValue];
                        break;
                    case OperationType::Dry():
                        $fieldValue = $row->total_dry_weight;
                        $totalOperationSummaries[$operationType] += $fieldValue;
                        $operationSummaries[$operationType][] = ['title' => $row->linenProduct->name ?? '-', 'value' => $fieldValue];
                        break;
                    case OperationType::Iron():
                        $fieldValue = $row->total_iron_piece;
                        $totalOperationSummaries[$operationType] += $fieldValue;
                        $operationSummaries[$operationType][] = ['title' => $row->linenProduct->name ?? '-', 'value' => $fieldValue];
                        break;
                    case OperationType::Packing():
                        $fieldValue = $row->total_packing_piece;
                        $totalOperationSummaries[$operationType] += $fieldValue;
                        $operationSummaries[$operationType][] = ['title' => $row->linenProduct->name ?? '-', 'value' => $fieldValue];
                        break;
                    case OperationType::Collect():
                        $fieldValue = $row->total_collect_weight;
                        $totalOperationSummaries[$operationType] += $fieldValue;
                        $operationSummaries[$operationType][] = ['title' => $row->linenProduct->name ?? '-', 'value' => $fieldValue];

                        $fieldValue = $row->total_collect_pack;
                        $totalOperationSummaries['collect_pack'] += $fieldValue;
                        $operationSummaries['collect_pack'][] = ['title' => $row->linenProduct->name ?? '-', 'value' => $fieldValue];
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

        $weekDayReports = (function () use ($employeeId) {
            $query = Operation::select(
                'total_wet_weight',
                'total_dry_weight',
                'total_iron_piece',
                'total_packing_piece',
                'total_collect_weight',
                'total_collect_pack',
                'created_at'
            )->where('employee_id', $employeeId);

            $dateEndAt = Carbon::today();
            $dateStartAt = clone $dateEndAt;
            $dateStartAt->subDays(7);
            if ($dateStartAt && $dateEndAt) {
                $query->whereBetween('created_at', [$dateStartAt->format('Y-m-d') . ' 00:00:00', $dateEndAt->format('Y-m-d') . ' 23:59:59']);
            }
            $rows = $query->orderBy('created_at', 'asc')->get();
            $weekDayReports = [];

            foreach ($rows as $key => $row) {
                $date = Carbon::parse($row->created_at)->format('Y-m-d');
                if (!isset($weekDayReports[$date])) {
                    $weekDayReports[$date] = [];
                }
                $row = $row->toArray();
                $row['value'] = (function () use ($row) {
                    if ($row['total_wet_weight']) {
                        return $row['total_wet_weight'];
                    }
                    if ($row['total_dry_weight']) {
                        return $row['total_dry_weight'];
                    }
                    if ($row['total_iron_piece']) {
                        return $row['total_iron_piece'];
                    }
                    if ($row['total_packing_piece']) {
                        return $row['total_packing_piece'];
                    }
                    if ($row['total_collect_weight']) {
                        return $row['total_collect_weight'];
                    }
                    return 0;
                })();
                $weekDayReports[$date][] =  $row;
            }

            $weekDayDataTemplate = [];
            $_date = clone $dateEndAt;
            for ($i = 0; $i < 7; $i++) {
                $date = $i > 0 ? $_date->subDays(1) : $_date;
                $weekDayDataTemplate[$date->format('Y-m-d')] = [];
            }

            foreach ($weekDayDataTemplate as $key => $value) {
                $weekDayDataTemplate[$key] = isset($weekDayReports[$key]) ? $weekDayReports[$key] : [];
            }

            return $weekDayDataTemplate;
        })();

        $workingDuration = $employee->getTotalTimeDurationOfWorkingTime();

        return view(
            'worker.employees.employee-summary',
            [
                'summaryReports' => $summaryReports,
                'workingDuration' => $workingDuration,
                'employee' => $employee->toArray(),
                'dateStartAt' => $dateStartAt,
                'dateEndAt' => $dateEndAt,
                'weekDayReports' => $weekDayReports
            ]
        );
    }
}
