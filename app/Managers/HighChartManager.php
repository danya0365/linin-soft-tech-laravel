<?php

namespace App\Managers;

use App\Managers\Manager;
use App\Models\EnergyResource;
use App\Models\EnergyResourceLog;
use Illuminate\Support\Facades\DB;

class HighChartManager extends Manager
{
    public static function getEnergyWeekSummary()
    {
        $currentDate = \Carbon\Carbon::now();
        $totalDays = 10;
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
}
