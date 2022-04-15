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
    public function index()
    {
        return redirect(route('worker.energy-resource.select-employee'));
    }

    public function selectEmployee()
    {
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
        return view('worker.energy-resource.select-employee', ['departments' => $departments]);
    }

    public function setSelectEmployee($employeeId)
    {
        $energyResourceLog = new EnergyResourceLog;
        $energyResourceLog->employee_id = $employeeId;
        $energyResourceLog->save();
        return redirect(route('worker.energy-resource.select-energy', ['energyResourceLogId' => $energyResourceLog->id]));
    }

    public function selectEnergyResource($energyResourceLogId)
    {
        $energyResourceLog = EnergyResourceLog::find($energyResourceLogId);
        $energyResources = EnergyResource::get();
        return view('worker.energy-resource.select-energy', ['energyResources' => $energyResources, 'energyResourceLog' => $energyResourceLog]);
    }

    public function setSelectEnergyResource($energyResourceLogId, $energyResourceId)
    {
        $energyResourceLog = EnergyResourceLog::find($energyResourceLogId);
        $energyResourceLog->energy_resource_id = $energyResourceId;
        $energyResourceLog->save();

        switch ($energyResourceId) {
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
    }
}