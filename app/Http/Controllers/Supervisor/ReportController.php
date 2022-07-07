<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Managers\HighChartManager;

class ReportController extends Controller
{
    public function index()
    {
        $overallSummary = HighChartManager::getOverallSummary(request()->get('overall'));
        $energySummary = HighChartManager::getEnergySummary(request()->get('energy'));

        return view(
            'supervisor.reports.index',
            [
                'overallSummary' => $overallSummary,
                'energySummary' => $energySummary
            ]
        );
    }
}
