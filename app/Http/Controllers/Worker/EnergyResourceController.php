<?php

namespace App\Http\Controllers\Worker;

use App\Enums\EnergyResourceNameId;
use App\Http\Controllers\Controller;
use App\Models\EnergyResource;
use App\Models\EnergyResourceLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EnergyResourceController extends Controller
{
    public function index()
    {
        return view('worker.energy-resources.index');
    }

    public function getSummary($energyResourceVarName)
    {
        $energyResource = EnergyResource::where('var_name', $energyResourceVarName)->orWhere('id', $energyResourceVarName)->first();

        $dateStartAt = request()->get('date_start_at');
        $dateEndAt = request()->get('date_end_at');

        switch ($energyResource->id) {
            case EnergyResourceNameId::Water()->value:

                $weekDayReports = (function () use ($dateStartAt, $dateEndAt) {
                    $query = EnergyResourceLog::with('energyResource')->select(
                        DB::raw('SUM(cost) as total_cost'),
                        DB::raw('DATE(created_at) as date'),
                        'energy_resource_id'
                    )
                        ->groupBy('date', 'energy_resource_id');

                    if ($dateStartAt && $dateEndAt) {
                        $query->whereBetween('created_at', [$dateStartAt . ' 00:00:00', $dateEndAt . ' 23:59:59']);
                    }
                    $rows = $query->get();

                    $weekDayReports = [];
                    foreach ($rows as $row) {
                        $date = $row->date;
                        if (!isset($weekDayReports[$date])) {
                            $weekDayReports[$date] = [];
                        }
                        $row = $row->toArray();
                        $energyResourceId = $row['energy_resource_id'];
                        $weekDayReports[$date][$energyResourceId] = $row;
                    }
                    return $weekDayReports;
                })();

                return view(
                    'worker.energy-resources.summaries.water',
                    [
                        'energyResource' => $energyResource,
                        'dateStartAt' => $dateStartAt,
                        'dateEndAt' => $dateEndAt,
                        'weekDayReports' => $weekDayReports,
                    ]
                );

            case EnergyResourceNameId::Electricity()->value:
                return view(
                    'worker.energy-resources.summaries.electricity',
                    [
                        'energyResource' => $energyResource,
                        'dateStartAt' => $dateStartAt,
                        'dateEndAt' => $dateEndAt,
                    ]
                );

            case EnergyResourceNameId::Gas()->value:
                return view(
                    'worker.energy-resources.summaries.gas',
                    [
                        'energyResource' => $energyResource,
                        'dateStartAt' => $dateStartAt,
                        'dateEndAt' => $dateEndAt,
                    ]
                );

            case EnergyResourceNameId::Biomass()->value:
                return view(
                    'worker.energy-resources.summaries.biomass',
                    [
                        'energyResource' => $energyResource,
                        'dateStartAt' => $dateStartAt,
                        'dateEndAt' => $dateEndAt,
                    ]
                );

            case EnergyResourceNameId::FuelOil()->value:
                return view(
                    'worker.energy-resources.summaries.fuel-oil',
                    [
                        'energyResource' => $energyResource,
                        'dateStartAt' => $dateStartAt,
                        'dateEndAt' => $dateEndAt,
                    ]
                );

            case EnergyResourceNameId::Petrol()->value:
                return view(
                    'worker.energy-resources.summaries.petrol',
                    [
                        'energyResource' => $energyResource,
                        'dateStartAt' => $dateStartAt,
                        'dateEndAt' => $dateEndAt,
                    ]
                );
        }
        return redirect(route('worker.energy-resource'));
    }
}
