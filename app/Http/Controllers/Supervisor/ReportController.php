<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\EnergyResource;
use App\Models\EnergyResourceLog;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view(
            'supervisor.reports.index',
            [
                'salesYearSummary' => $this->salesYearSummary(),
                'energySummary' => $this->energySummary()
            ]
        );
    }

    public function energySummary(): array
    {

        $queryEnergy = request()->get('energy');

        $queryParam['dateStartAt'] = isset($queryEnergy['date_start_at']) ? $queryEnergy['date_start_at'] : '';
        $queryParam['dateEndAt'] = isset($queryEnergy['date_end_at']) ? $queryEnergy['date_end_at'] : '';

        $energySummary = [];

        $query = EnergyResourceLog::select(
            DB::raw('SUM(cost) as total_cost'),
            DB::raw('energy_resource_id'),
        )->with('energyResource');

        $query->groupBy('energy_resource_id');

        if ($queryParam['dateStartAt'] && $queryParam['dateEndAt']) {
            $query->whereBetween('created_at', [$queryParam['dateStartAt'] . ' 00:00:00', $queryParam['dateEndAt'] . ' 23:59:59']);
        }

        $energyResourceLogs = $query->get();

        foreach ($energyResourceLogs as $key => $energyResourceLog) {
            $energySummary[$energyResourceLog->energyResource->id] = [
                'name' => $energyResourceLog->energyResource->name,
                'value' => $energyResourceLog->total_cost,
            ];
        }

        return ['data' => $energySummary, 'queryParam' => $queryParam];
    }

    private function profitYearSummary($incomeYearSummary, $expenseYearSummary): array
    {
        $result = $this->generateYearSummaryStructure();
        $profitYearSummary = [];

        foreach ($result as $monthYear => $profitRow) {
            $row = $profitRow;
            $row['total_amount'] = $incomeYearSummary[$monthYear]['total_amount'] - $expenseYearSummary[$monthYear]['total_amount'];
            $profitYearSummary[$monthYear] = $row;
        }

        return $profitYearSummary;
    }

    public function salesYearSummary(): array
    {
        $incomeYearSummary = $this->incomeYearSummary();
        $expenseYearSummary = $this->expenseYearSummary();
        $profitYearSummary = $this->profitYearSummary($incomeYearSummary, $expenseYearSummary);

        $result = $this->generateYearSummaryStructure();
        $monthYearTitles = [];

        foreach ($result as $key => $row) {
            $monthYearTitles[] = $row['month_year_title'];
        }

        $salesYearSummary = [
            'incomeYearSummary' => $incomeYearSummary,
            'expenseYearSummary' => $expenseYearSummary,
            'profitYearSummary' => $profitYearSummary,
            'monthYearTitles' => $monthYearTitles
        ];

        return $salesYearSummary;
    }

    private function incomeYearSummary(): array
    {
        $incomeRows = DB::table('incomes')->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_year"), DB::raw('SUM(amount) as total_amount'))->whereYear('created_at', date('Y'))->groupBy('month_year')->get();
        $result = $this->generateYearSummaryStructure();

        foreach ($incomeRows as $key => $incomeRow) {
            $monthYear = $incomeRow->month_year;
            $row = $result[$monthYear];
            $row['total_amount'] = $incomeRow->total_amount;
            $result[$monthYear] = $row;
        }
        return $result;
    }

    private function expenseYearSummary(): array
    {
        $incomeRows = DB::table('expenses')->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_year"), DB::raw('SUM(amount) as total_amount'))->whereYear('created_at', date('Y'))->groupBy('month_year')->get();
        $result = $this->generateYearSummaryStructure();

        foreach ($incomeRows as $key => $incomeRow) {
            $monthYear = $incomeRow->month_year;
            $row = $result[$monthYear];
            $row['total_amount'] = $incomeRow->total_amount;
            $result[$monthYear] = $row;
        }
        return $result;
    }

    private function generateYearSummaryStructure(): array
    {
        $result = [];
        $currentYear = date('Y');
        for ($m = 1; $m <= 12; $m++) {
            $time = mktime(0, 0, 0, $m, 1, $currentYear);
            $monthYear = date('Y-m', $time);
            $result[$monthYear] = ['month_year' => $monthYear, 'total_amount' => 0, 'month_year_title' => date('F y', $time)];
        }

        return $result;
    }
}
