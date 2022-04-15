<?php

namespace App\Http\Controllers\Worker;

use App\Enums\DepartmentNameId;
use App\Enums\EnergyResourceNameId;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\EnergyResource;
use App\Models\EnergyResourceLog;

class EnergyResourceLogController extends Controller
{

    public function selectEmployee($energyResourceLogId, $energyResourceId)
    {
        if ($energyResourceLogId == 0) {
            $energyResourceLog = new EnergyResourceLog();
            $energyResourceLog->energy_resource_id = $energyResourceId;
            $energyResourceLog->save();
            $energyResourceLogId = $energyResourceLog->id;
        }
        $energyResourceLog = EnergyResourceLog::find($energyResourceLogId);

        $departments = Department::with('employees')->whereIn(
            'id',
            [
                DepartmentNameId::Wash(),
                DepartmentNameId::Dry(),
                DepartmentNameId::Iron(),
                DepartmentNameId::Packing(),
                DepartmentNameId::Collect(),
            ]
        )->get();
        return view('worker.energy-resource.select-employee', ['departments' => $departments, 'energyResourceLog' => $energyResourceLog]);
    }

    public function setSelectEmployee($energyResourceLogId, $employeeId)
    {
        $energyResourceLog = EnergyResourceLog::find($energyResourceLogId);
        $energyResourceLog->employee_id = $employeeId;
        $energyResourceLog->save();

        switch ($energyResourceLog->energy_resource_id) {
            case EnergyResourceNameId::Water():
                return redirect(route('worker.energy-resource.submit-water-form', ['energyResourceLogId' => $energyResourceLog->id]));

            case EnergyResourceNameId::Electricity():
                return redirect(route('worker.energy-resource.submit-electricity-form', ['energyResourceLogId' => $energyResourceLog->id]));

            case EnergyResourceNameId::Gas():
                return redirect(route('worker.energy-resource.submit-gas-form', ['energyResourceLogId' => $energyResourceLog->id]));

            case EnergyResourceNameId::Biomass():
                return redirect(route('worker.energy-resource.submit-biomass-form', ['energyResourceLogId' => $energyResourceLog->id]));

            case EnergyResourceNameId::FuelOil():
                return redirect(route('worker.energy-resource.submit-fuel-oil-form', ['energyResourceLogId' => $energyResourceLog->id]));
        }
        return redirect(route('worker.energy-resource.index', ['energyResourceLogId' => $energyResourceLog->id]));
    }

    public function setSelectEnergyResource($energyResourceLogId, $energyResourceId)
    {
        if ($energyResourceLogId == 0) {
            $energyResourceLog = new EnergyResourceLog();
            $energyResourceLog->energy_resource_id = $energyResourceId;
            $energyResourceLog->save();
            $energyResourceLogId = $energyResourceLog->id;
        }

        $energyResourceLog = EnergyResourceLog::find($energyResourceLogId);
        $energyResourceLog->energy_resource_id = $energyResourceId;
        $energyResourceLog->save();

        return redirect(route('worker.energy-resource.select-employee', ['energyResourceLogId' => $energyResourceLog->id]));
    }
}
