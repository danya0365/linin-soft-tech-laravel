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
    private static function getDates($startDateString = '', $endDateString = '')
    {
        $endDate = $endDateString ? \Carbon\Carbon::parse($endDateString) : \Carbon\Carbon::now();
        $endDate->addDays(1);
        $startDate = $startDateString ? \Carbon\Carbon::parse($startDateString) : \Carbon\Carbon::parse($endDate->format('Y-m-d'))->subDays(7);

        $dates = [];
        $period = new \DatePeriod(
            new \DateTime($startDate->format('Y-m-d')),
            new \DateInterval('P1D'),
            new \DateTime($endDate->format('Y-m-d'))
        );

        foreach ($period as $key => $value) {
            $dates[] = $value;
        }
        return $dates;
    }

    public static function getEnergyDaysSummary($startDateString = '', $endDateString = '')
    {
        $totalDays = 7;
        $endDate = $endDateString ? \Carbon\Carbon::parse($endDateString) : \Carbon\Carbon::now();
        $startDate = $startDateString ? \Carbon\Carbon::parse($startDateString) : \Carbon\Carbon::parse($endDate->format('Y-m-d'))->subDays($totalDays);

        $energyResourceData = (function ($energyResourceId) use ($startDate, $endDate) {
            $query = EnergyResourceLog::query();
            $query->select(
                DB::raw('SUM(cost) as total_cost'),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as date")
            );

            $query->where('energy_resource_id', $energyResourceId)
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
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

        $dates = self::getDates($startDateString, $endDateString);

        $titles = [];
        foreach ($dates as $key => $date) {
            $titles[] = $date->format('F j, Y');
        }

        $energyWeekSummary = [];
        $energyResources = EnergyResource::get();
        foreach ($energyResources as $key => $energyResource) {

            $data = $energyResourceData($energyResource->id);
            $_data = [];
            foreach ($dates as $key => $date) {
                $dateString = $date->format('Y-m-d');
                $_data[] = isset($data[$dateString]) ? $data[$dateString] : 0;
            }
            $energyWeekSummary[] = ['name' => $energyResource->name, 'data' => $_data];
        }

        return ['titles' => $titles, 'data' => $energyWeekSummary];
    }

    public static function getExpenseDaysSummary($startDateString = '', $endDateString = '')
    {
        $totalDays = 7;
        $endDate = $endDateString ? \Carbon\Carbon::parse($endDateString) : \Carbon\Carbon::now();
        $startDate = $startDateString ? \Carbon\Carbon::parse($startDateString) : \Carbon\Carbon::parse($endDate->format('Y-m-d'))->subDays($totalDays);

        $expenseData = (function ($typeName) use ($startDate, $endDate) {
            $query = Expense::query();
            $query->select(
                DB::raw('SUM(amount) as total_cost'),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as date")
            );

            $query->where('type_name', $typeName)
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);

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

        $dates = self::getDates($startDateString, $endDateString);

        $titles = [];
        foreach ($dates as $key => $date) {
            $titles[] = $date->format('F j, Y');
        }

        $returnData = [];
        $expenseTypes = ExpenseType::asSelectArray();
        foreach ($expenseTypes as $key => $expenseType) {

            $data = $expenseData($key);
            $_data = [];
            foreach ($dates as $key => $date) {
                $dateString = $date->format('Y-m-d');
                $_data[] = isset($data[$dateString]) ? $data[$dateString] : 0;
            }
            $returnData[] = ['name' => $expenseType, 'data' => $_data];
        }

        return ['titles' => $titles, 'data' => $returnData];
    }


    public static function getIncomeDaysSummary($startDateString = '', $endDateString = '')
    {
        $totalDays = 7;
        $endDate = $endDateString ? \Carbon\Carbon::parse($endDateString) : \Carbon\Carbon::now();
        $startDate = $startDateString ? \Carbon\Carbon::parse($startDateString) : \Carbon\Carbon::parse($endDate->format('Y-m-d'))->subDays($totalDays);

        $incomeData = (function ($typeName) use ($startDate, $endDate) {
            $query = Income::query();
            $query->select(
                DB::raw('SUM(amount) as total_cost'),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as date")
            );

            $query->where('type_name', $typeName)
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
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

        $dates = self::getDates($startDateString, $endDateString);

        $titles = [];
        foreach ($dates as $key => $date) {
            $titles[] = $date->format('F j, Y');
        }

        $returnData = [];
        $incomeTypes = IncomeType::asSelectArray();
        foreach ($incomeTypes as $key => $incomeType) {

            $data = $incomeData($key);
            $_data = [];
            foreach ($dates as $key => $date) {
                $dateString = $date->format('Y-m-d');
                $_data[] = isset($data[$dateString]) ? $data[$dateString] : 0;
            }
            $returnData[] = ['name' => $incomeType, 'data' => $_data];
        }

        return ['titles' => $titles, 'data' => $returnData];
    }

    public static function getSalesLatestDaysSummary($startDateString = '', $endDateString = ''): array
    {
        return SalesLatestDaysSummary::getInstance()->getSalesLatestDaysSummary($startDateString, $endDateString);
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

    /**
     * กราฟ 1: ภาพรวมการเงิน (รายได้, ต้นทุน, กำไร, ค่าพลังงาน) หน่วย: บาท
     */
    public static function getFinancialComparisonSummary($startDateString = '', $endDateString = ''): array
    {
        $totalDays = 7;
        $endDate = $endDateString ? \Carbon\Carbon::parse($endDateString) : \Carbon\Carbon::now();
        $startDate = $startDateString ? \Carbon\Carbon::parse($startDateString) : \Carbon\Carbon::parse($endDate->format('Y-m-d'))->subDays($totalDays);

        $dates = self::getDates($startDateString, $endDateString);
        $titles = [];
        $dataStructure = [];
        foreach ($dates as $date) {
            $dateYmd = $date->format('Y-m-d');
            $titles[] = $date->format('M j');
            $dataStructure[$dateYmd] = 0;
        }

        // รายได้
        $incomeData = $dataStructure;
        $incomeRows = Income::query()
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as ymd"), DB::raw('SUM(amount) as total'))
            ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->groupBy('ymd')->get();
        foreach ($incomeRows as $row) {
            if (isset($incomeData[$row->ymd])) {
                $incomeData[$row->ymd] = floatval($row->total);
            }
        }

        // ต้นทุน
        $expenseData = $dataStructure;
        $expenseRows = Expense::query()
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as ymd"), DB::raw('SUM(amount) as total'))
            ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->groupBy('ymd')->get();
        foreach ($expenseRows as $row) {
            if (isset($expenseData[$row->ymd])) {
                $expenseData[$row->ymd] = floatval($row->total);
            }
        }

        // กำไร = รายได้ - ต้นทุน
        $profitData = $dataStructure;
        foreach ($dataStructure as $ymd => $val) {
            $profitData[$ymd] = $incomeData[$ymd] - $expenseData[$ymd];
        }

        // ค่าพลังงานรวม
        $energyData = $dataStructure;
        $energyRows = EnergyResourceLog::query()
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as ymd"), DB::raw('SUM(cost) as total'))
            ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->groupBy('ymd')->get();
        foreach ($energyRows as $row) {
            if (isset($energyData[$row->ymd])) {
                $energyData[$row->ymd] = floatval($row->total);
            }
        }

        return [
            'titles' => array_values($titles),
            'data' => [
                ['name' => 'รายได้', 'data' => array_values($incomeData)],
                ['name' => 'ต้นทุน', 'data' => array_values($expenseData)],
                ['name' => 'กำไร', 'data' => array_values($profitData)],
                ['name' => 'ค่าพลังงาน', 'data' => array_values($energyData)],
            ]
        ];
    }

    /**
     * กราฟ 2: ปริมาณงาน (น้ำหนักผ้าเปียก, น้ำหนักผ้าแห้ง) หน่วย: กก.
     */
    public static function getOperationComparisonSummary($startDateString = '', $endDateString = ''): array
    {
        $totalDays = 7;
        $endDate = $endDateString ? \Carbon\Carbon::parse($endDateString) : \Carbon\Carbon::now();
        $startDate = $startDateString ? \Carbon\Carbon::parse($startDateString) : \Carbon\Carbon::parse($endDate->format('Y-m-d'))->subDays($totalDays);

        $dates = self::getDates($startDateString, $endDateString);
        $titles = [];
        $dataStructure = [];
        foreach ($dates as $date) {
            $dateYmd = $date->format('Y-m-d');
            $titles[] = $date->format('M j');
            $dataStructure[$dateYmd] = 0;
        }

        // น้ำหนักผ้าเปียก
        $wetWeightData = $dataStructure;
        $wetRows = \App\Models\CustomerOperationDailySummary::query()
            ->select(DB::raw("operation_date as ymd"), DB::raw('SUM(total_wet_weight) as total'))
            ->whereBetween('operation_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->groupBy('ymd')->get();
        foreach ($wetRows as $row) {
            if (isset($wetWeightData[$row->ymd])) {
                $wetWeightData[$row->ymd] = floatval($row->total);
            }
        }

        // น้ำหนักผ้าแห้ง
        $dryWeightData = $dataStructure;
        $dryRows = \App\Models\CustomerOperationDailySummary::query()
            ->select(DB::raw("operation_date as ymd"), DB::raw('SUM(total_dry_weight) as total'))
            ->whereBetween('operation_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->groupBy('ymd')->get();
        foreach ($dryRows as $row) {
            if (isset($dryWeightData[$row->ymd])) {
                $dryWeightData[$row->ymd] = floatval($row->total);
            }
        }

        return [
            'titles' => array_values($titles),
            'data' => [
                ['name' => 'น้ำหนักผ้าเปียก (กก.)', 'data' => array_values($wetWeightData)],
                ['name' => 'น้ำหนักผ้าแห้ง (กก.)', 'data' => array_values($dryWeightData)],
            ]
        ];
    }

    /**
     * กราฟ 3: เปรียบเทียบแนวโน้ม (Normalized %) - รวมทุกข้อมูลในกราฟเดียว
     * แปลงค่าเป็น % ของค่าสูงสุดในช่วงเวลา
     */
    public static function getTrendComparisonSummary($startDateString = '', $endDateString = ''): array
    {
        // ดึงข้อมูลจากทั้ง 2 กราฟ
        $financial = self::getFinancialComparisonSummary($startDateString, $endDateString);
        $operation = self::getOperationComparisonSummary($startDateString, $endDateString);

        $allSeries = [];

        // Normalize ข้อมูลเป็น % ของค่าสูงสุด
        $normalizeData = function($data) {
            $max = max($data);
            if ($max == 0) return array_fill(0, count($data), 0);
            return array_map(function($val) use ($max) {
                return round(($val / $max) * 100, 1);
            }, $data);
        };

        // เพิ่มข้อมูลการเงิน (เลือกเฉพาะบางตัว)
        foreach ($financial['data'] as $series) {
            if (in_array($series['name'], ['รายได้', 'ต้นทุน', 'ค่าพลังงาน'])) {
                $allSeries[] = [
                    'name' => $series['name'],
                    'data' => $normalizeData($series['data'])
                ];
            }
        }

        // เพิ่มข้อมูลปริมาณงาน (รวมเป็น 1 เส้น: ผ้าเปียก)
        foreach ($operation['data'] as $series) {
            if ($series['name'] === 'น้ำหนักผ้าเปียก (กก.)') {
                $allSeries[] = [
                    'name' => 'น้ำหนักผ้า',
                    'data' => $normalizeData($series['data'])
                ];
            }
        }

        return [
            'titles' => $financial['titles'],
            'data' => $allSeries
        ];
    }
}

class SalesLatestDaysSummary
{
    private static $sharedInstance;
    public static function getInstance()
    {
        if (!self::$sharedInstance) {
            self::$sharedInstance = new SalesLatestDaysSummary();
        }
        return self::$sharedInstance;
    }

    public function getSalesLatestDaysSummary($startDateString = '', $endDateString = ''): array
    {
        $totalDays = 7;
        $endDate = $endDateString ? \Carbon\Carbon::parse($endDateString) : \Carbon\Carbon::now();
        $startDate = $startDateString ? \Carbon\Carbon::parse($startDateString) : \Carbon\Carbon::parse($endDate->format('Y-m-d'))->subDays($totalDays);

        $result = $this->generateLatestDaysSummaryStructure($startDate, $endDate);
        $titles = $result['titles'];
        $dataStructure = $result['data'];

        $incomeYearSummary = $this->incomeLatestDaysSummary($dataStructure, $startDate, $endDate);
        $expenseYearSummary = $this->expenseLatestDaysSummary($dataStructure, $startDate, $endDate);

        $profitYearSummary = $this->profitLatestDaysSummary($dataStructure, $incomeYearSummary, $expenseYearSummary);

        return [
            'titles' => array_values($titles),
            'data' => [
                ['name' => 'Income', 'data' => array_values($incomeYearSummary)],
                ['name' => 'Expense', 'data' => array_values($expenseYearSummary)],
                ['name' => 'Profit', 'data' => array_values($profitYearSummary)],
            ],
        ];
    }

    private function incomeLatestDaysSummary($dataStructure, $startDate, $endDate): array
    {
        $incomeRows = Income::query()
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as ymd"), DB::raw('SUM(amount) as total_cost'))
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->groupBy('ymd')->get();

        foreach ($incomeRows as $key => $incomeRow) {
            $ymd = $incomeRow->ymd;
            if (isset($dataStructure[$ymd])) {
                $dataStructure[$ymd] = $incomeRow->total_cost;
            }
        }
        return $dataStructure;
    }

    private function expenseLatestDaysSummary($dataStructure, $startDate, $endDate): array
    {
        $expenseRows = Expense::query()
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as ymd"), DB::raw('SUM(amount) as total_cost'))
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->groupBy('ymd')->get();

        foreach ($expenseRows as $key => $expenseRow) {
            $ymd = $expenseRow->ymd;
            if (isset($dataStructure[$ymd])) {
                $dataStructure[$ymd] = $expenseRow->total_cost;
            }
        }

        return $dataStructure;
    }

    private function profitLatestDaysSummary($dataStructure, $incomeYearSummary, $expenseYearSummary): array
    {
        foreach ($dataStructure as $key => $profitRow) {
            $dataStructure[$key] = $incomeYearSummary[$key] - $expenseYearSummary[$key];
        }

        return $dataStructure;
    }

    private function generateLatestDaysSummaryStructure($startDate, $endDate): array
    {
        $titles = $dataStructure = [];

        $endDate->addDays(1);

        $period = new \DatePeriod(
            new \DateTime($startDate->format('Y-m-d')),
            new \DateInterval('P1D'),
            new \DateTime($endDate->format('Y-m-d'))
        );

        foreach ($period as $key => $value) {

            $dateYmd = $value->format('Y-m-d');
            $titles[$dateYmd] = $value->format('F j, Y');
            $dataStructure[$dateYmd] = 0;
        }

        return ['titles' => $titles, 'data' => $dataStructure];
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