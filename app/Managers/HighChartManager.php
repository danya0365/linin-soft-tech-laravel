<?php

namespace App\Managers;

use App\Enums\ExpenseType;
use App\Managers\Manager;
use App\Models\EnergyResource;
use App\Models\EnergyResourceLog;
use App\Models\Expense;
use App\Models\Income;
use Illuminate\Support\Facades\DB;

class HighChartManager extends Manager
{
    public static function getEnergyDaysSummary()
    {
        $currentDate = \Carbon\Carbon::now();
        $totalDays = 7;
        $agoDate = $currentDate->subDays($totalDays);

        $energyResourceData = (function ($energyResourceId) use ($agoDate) {
            $query = EnergyResourceLog::query();
            $query->select(
                DB::raw('SUM(cost) as total_cost'),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as date")
            );

            $query->where('energy_resource_id', $energyResourceId)->where('created_at', '>=', $agoDate);
            $query->groupBy('date');

            $rows = $query->get();

            $weekDayReports = [];
            foreach ($rows as $key => $row) {
                $date = \Carbon\Carbon::parse($row->date)->format('Y-m-d');
                if (!isset($weekDayReports[$date])) {
                    $weekDayReports[$date] = [];
                }
                $row = $row->toArray();
                $weekDayReports[$date] =  $row['total_cost'];
            }

            return $weekDayReports;
        });

        $titles = [];
        $_date = \Carbon\Carbon::now();
        for ($i = 0; $i < $totalDays; $i++) {
            $date = $i > 0 ? $_date->subDays(1) : $_date;
            $titles[] = $date->format('Y-m-d');
        }

        $energyWeekSummary = [];
        $energyResources = EnergyResource::get();
        foreach ($energyResources as $key => $energyResource) {

            $data = $energyResourceData($energyResource->id);
            $_date = \Carbon\Carbon::now();
            $_data = [];
            for ($i = 0; $i < $totalDays; $i++) {
                $date = $i > 0 ? $_date->subDays(1) : $_date;
                $dateString = $date->format('Y-m-d');
                $_data[] = isset($data[$dateString]) ? $data[$dateString] : 0;
            }
            $energyWeekSummary[] = ['name' => $energyResource->name, 'data' => $_data];
        }

        return ['titles' => $titles, 'data' => $energyWeekSummary];
    }

    public static function getExpenseDaysSummary()
    {
        $currentDate = \Carbon\Carbon::now();
        $totalDays = 7;
        $agoDate = $currentDate->subDays($totalDays);

        $expenseData = (function ($typeName) use ($agoDate) {
            $query = Expense::query();
            $query->select(
                DB::raw('SUM(amount) as total_cost'),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as date")
            );

            $query->where('type_name', $typeName)->where('created_at', '>=', $agoDate);
            $query->groupBy('date');

            $rows = $query->get();

            $returnData = [];
            foreach ($rows as $key => $row) {
                $date = \Carbon\Carbon::parse($row->date)->format('Y-m-d');
                if (!isset($returnData[$date])) {
                    $returnData[$date] = [];
                }
                $row = $row->toArray();
                $returnData[$date] =  $row['total_cost'];
            }

            return $returnData;
        });

        $titles = [];
        $_date = \Carbon\Carbon::now();
        for ($i = 0; $i < $totalDays; $i++) {
            $date = $i > 0 ? $_date->subDays(1) : $_date;
            $titles[] = $date->format('Y-m-d');
        }

        $returnData = [];
        $expenseTypes = ExpenseType::asSelectArray();
        foreach ($expenseTypes as $key => $expenseType) {

            $data = $expenseData($key);
            $_date = \Carbon\Carbon::now();
            $_data = [];
            for ($i = 0; $i < $totalDays; $i++) {
                $date = $i > 0 ? $_date->subDays(1) : $_date;
                $dateString = $date->format('Y-m-d');
                $_data[] = isset($data[$dateString]) ? $data[$dateString] : 0;
            }
            $returnData[] = ['name' => $expenseType, 'data' => $_data];
        }

        return ['titles' => $titles, 'data' => $returnData];
    }

    public static function getSalesLatestDaysSummary(): array
    {
        $salesLatestDaysSummary = new SalesLatestDaysSummary;
        return $salesLatestDaysSummary->getSalesLatestDaysSummary();
    }
}

class SalesLatestDaysSummary
{
    public function getSalesLatestDaysSummary(): array
    {
        $currentDate = \Carbon\Carbon::now();
        $totalDays = 7;
        $agoDate = $currentDate->subDays($totalDays);

        $incomeYearSummary = $this->incomeLatestDaysSummary($totalDays, $agoDate);
        $expenseYearSummary = $this->expenseLatestDaysSummary($totalDays, $agoDate);
        $profitYearSummary = $this->profitLatestDaysSummary($totalDays, $incomeYearSummary, $expenseYearSummary);

        $result = $this->generateLatestDaysSummaryStructure($totalDays);
        $titles = $result['titles'];

        return [
            'titles' => array_values($titles),
            'data' => [
                ['name' => 'Income', 'data' => array_values($incomeYearSummary)],
                ['name' => 'Expense', 'data' => array_values($expenseYearSummary)],
                ['name' => 'Profit', 'data' => array_values($profitYearSummary)],
            ],
        ];
    }

    private function incomeLatestDaysSummary($totalDays, $agoDate): array
    {
        $incomeRows = DB::table('incomes')
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as ymd"), DB::raw('SUM(amount) as total_cost'))
            ->where('created_at', '>=', $agoDate)
            ->groupBy('ymd')->get();
        $result = $this->generateLatestDaysSummaryStructure($totalDays);
        $data = $result['data'];

        foreach ($incomeRows as $key => $incomeRow) {
            $ymd = $incomeRow->ymd;
            $data[$ymd] = $incomeRow->total_cost;
        }
        return $data;
    }

    private function expenseLatestDaysSummary($totalDays, $agoDate): array
    {
        $incomeRows = DB::table('expenses')
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as ymd"), DB::raw('SUM(amount) as total_cost'))
            ->where('created_at', '>=', $agoDate)
            ->groupBy('ymd')->get();
        $result = $this->generateLatestDaysSummaryStructure($totalDays);
        $data = $result['data'];

        foreach ($incomeRows as $key => $incomeRow) {
            $ymd = $incomeRow->ymd;
            $data[$ymd] = $incomeRow->total_cost;
        }

        return $data;
    }

    private function profitLatestDaysSummary($totalDays, $incomeYearSummary, $expenseYearSummary): array
    {
        $result = $this->generateLatestDaysSummaryStructure($totalDays);
        $data = $result['data'];

        foreach ($data as $key => $profitRow) {
            $data[$key] = $incomeYearSummary[$key] - $expenseYearSummary[$key];
        }

        return $data;
    }

    private function generateLatestDaysSummaryStructure($totalDays): array
    {
        $titles = $data = [];
        $_date = \Carbon\Carbon::now();
        for ($i = 0; $i < $totalDays; $i++) {
            $date = $i > 0 ? $_date->subDays(1) : $_date;
            $dateYmd = $date->format('Y-m-d');
            $titles[$dateYmd] = $date->format('Y F j');
            $data[$dateYmd] = 0;
        }

        return ['titles' => $titles, 'data' => $data];
    }
}
