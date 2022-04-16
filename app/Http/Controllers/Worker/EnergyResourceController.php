<?php

namespace App\Http\Controllers\Worker;

use App\Enums\EnergyResourceNameId;
use App\Http\Controllers\Controller;
use App\Models\EnergyResource;

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

                return view(
                    'worker.energy-resources.summaries.water',
                    [
                        'energyResource' => $energyResource,
                        'dateStartAt' => $dateStartAt,
                        'dateEndAt' => $dateEndAt,
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
        }
        return redirect(route('worker.energy-resource'));
    }
}
