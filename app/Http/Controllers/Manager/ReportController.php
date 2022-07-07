<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Managers\HighChartManager;

class ReportController extends Controller
{
    public function index()
    {
        $overallSummary = HighChartManager::getOverallSummary(request()->get('overall'));
        $energySummary = HighChartManager::getEnergySummary(request()->get('energy'));

        return view(
            'manager.reports.index',
            [
                'overallSummary' => $overallSummary,
                'energySummary' => $energySummary
            ]
        );
    }

    function getSalesRangeDaysChart()
    {
        $startAt = request()->get('startAt');
        $endAt = request()->get('endAt');

        $result = HighChartManager::getSalesLatestDaysSummary($startAt, $endAt);
        return $result;
    }

    function getEnergyRangeDaysChart()
    {
        $startAt = request()->get('startAt');
        $endAt = request()->get('endAt');

        $result = HighChartManager::getEnergyDaysSummary($startAt, $endAt);
        return $result;
    }

    function getExpenseRangeDaysChart()
    {
        $startAt = request()->get('startAt');
        $endAt = request()->get('endAt');

        $result = HighChartManager::getExpenseDaysSummary($startAt, $endAt);
        return $result;
    }

    function getIncomeRangeDaysChart()
    {
        $startAt = request()->get('startAt');
        $endAt = request()->get('endAt');

        $result = HighChartManager::getIncomeDaysSummary($startAt, $endAt);
        return $result;
    }
}
