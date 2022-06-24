<?php

namespace App\Managers;

use App\Enums\ExpenseType;
use App\Enums\IncomeType;
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


    public static function getIncomeDaysSummary()
    {
        $currentDate = \Carbon\Carbon::now();
        $totalDays = 7;
        $agoDate = $currentDate->subDays($totalDays);

        $incomeData = (function ($typeName) use ($agoDate) {
            $query = Income::query();
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
        $incomeTypes = IncomeType::asSelectArray();
        foreach ($incomeTypes as $key => $incomeType) {

            $data = $incomeData($key);
            $_date = \Carbon\Carbon::now();
            $_data = [];
            for ($i = 0; $i < $totalDays; $i++) {
                $date = $i > 0 ? $_date->subDays(1) : $_date;
                $dateString = $date->format('Y-m-d');
                $_data[] = isset($data[$dateString]) ? $data[$dateString] : 0;
            }
            $returnData[] = ['name' => $incomeType, 'data' => $_data];
        }

        return ['titles' => $titles, 'data' => $returnData];
    }

    public static function getSalesLatestDaysSummary(): array
    {
        $salesLatestDaysSummary = new SalesLatestDaysSummary;
        return $salesLatestDaysSummary->getSalesLatestDaysSummary();
    }

    public static function getOverallSummary($queryOverall): array
    {
        return OverallSummary::getOverallSummary($queryOverall);
    }

    public static function getEnergySummary($queryOverall): array
    {
        return EnergySummary::getEnergySummary($queryOverall);
    }

    public static function getSalesYearSummary(): array
    {
        return SalesYearSummary::getInstance()->getSalesYearSummary();
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
        $incomeRows = Income::query()
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as ymd"), DB::raw('SUM(amount) as total_cost'))
            ->where('created_at', '>=', $agoDate)
            ->groupBy('ymd')->get();
        $result = $this->generateLatestDaysSummaryStructure($totalDays);
        $data = $result['data'];

        foreach ($incomeRows as $key => $incomeRow) {
            $ymd = $incomeRow->ymd;
            if (isset($data[$ymd])) {
                $data[$ymd] = $incomeRow->total_cost;
            }
        }
        return $data;
    }

    private function expenseLatestDaysSummary($totalDays, $agoDate): array
    {
        $expenseRows = Expense::query()
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as ymd"), DB::raw('SUM(amount) as total_cost'))
            ->where('created_at', '>=', $agoDate)
            ->groupBy('ymd')->get();
        $result = $this->generateLatestDaysSummaryStructure($totalDays);
        $data = $result['data'];

        foreach ($expenseRows as $key => $expenseRow) {
            $ymd = $expenseRow->ymd;
            if (isset($data[$ymd])) {
                $data[$ymd] = $expenseRow->total_cost;
            }
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


class OverallSummary
{
    public static function getOverallSummary($queryOverall): array
    {
        $queryParam['dateStartAt'] = isset($queryOverall['date_start_at']) ? $queryOverall['date_start_at'] : '';
        $queryParam['dateEndAt'] = isset($queryOverall['date_end_at']) ? $queryOverall['date_end_at'] : '';

        $overallSummary = [];

        $expense = (function () use ($queryParam) {

            $query = Expense::query()
                ->select(DB::raw('SUM(amount) as total_amount'));

            if ($queryParam['dateStartAt'] && $queryParam['dateEndAt']) {
                $query->whereBetween('created_at', [$queryParam['dateStartAt'] . ' 00:00:00', $queryParam['dateEndAt'] . ' 23:59:59']);
            }

            $totalAmount = $query->value('total_amount');
            return $totalAmount;
        })();


        $income = (function () use ($queryParam) {

            $query = Income::query()
                ->select(DB::raw('SUM(amount) as total_amount'));

            if ($queryParam['dateStartAt'] && $queryParam['dateEndAt']) {
                $query->whereBetween('created_at', [$queryParam['dateStartAt'] . ' 00:00:00', $queryParam['dateEndAt'] . ' 23:59:59']);
            }

            $totalAmount = $query->value('total_amount');
            return $totalAmount;
        })();

        $overallSummary['expense'] = $expense;
        $overallSummary['income'] = $income;
        $overallSummary['profit'] = $income - $expense;

        return ['data' => $overallSummary, 'queryParam' => $queryParam];
    }
}

class EnergySummary
{
    public static function getEnergySummary($queryEnergy): array
    {
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
}

class SalesYearSummary
{
    private static $sharedInstance;
    public static function getInstance()
    {
        if (!self::$sharedInstance) {
            self::$sharedInstance = new SalesYearSummary();
        }
        return self::$sharedInstance;
    }

    public function getSalesYearSummary(): array
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
        $incomeRows = Income::query()
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_year"), DB::raw('SUM(amount) as total_amount'))
            ->whereYear('created_at', date('Y'))
            ->groupBy('month_year')
            ->get();

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
        $incomeRows = Expense::query()
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_year"), DB::raw('SUM(amount) as total_amount'))
            ->whereYear('created_at', date('Y'))
            ->groupBy('month_year')
            ->get();
        $result = $this->generateYearSummaryStructure();

        foreach ($incomeRows as $key => $incomeRow) {
            $monthYear = $incomeRow->month_year;
            $row = $result[$monthYear];
            $row['total_amount'] = $incomeRow->total_amount;
            $result[$monthYear] = $row;
        }
        return $result;
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
