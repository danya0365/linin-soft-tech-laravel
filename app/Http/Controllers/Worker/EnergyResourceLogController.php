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

    public function setSelectEnergyResource($energyResourceId)
    {
        $energyResourceLog = new EnergyResourceLog();
        $energyResourceLog->energy_resource_id = $energyResourceId;
        $energyResourceLog->save();

        return redirect(route('worker.energy-resource.select-employee', ['energyResourceLogId' => $energyResourceLog->id]));
    }

    public function selectEmployee($energyResourceLogId)
    {
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
        return view('worker.energy-resources.select-employee', ['departments' => $departments, 'energyResourceLog' => $energyResourceLog]);
    }

    public function setSelectEmployee($energyResourceLogId, $employeeId)
    {
        $energyResourceLog = EnergyResourceLog::find($energyResourceLogId);
        $energyResourceLog->employee_id = $employeeId;
        $energyResourceLog->save();

        switch ($energyResourceLog->energy_resource_id) {
            case EnergyResourceNameId::Water()->value:
                return redirect(route('worker.energy-resource.log.submit-water', ['energyResourceLogId' => $energyResourceLog->id]));

            case EnergyResourceNameId::Electricity()->value:
                return redirect(route('worker.energy-resource.log.submit-electricity', ['energyResourceLogId' => $energyResourceLog->id]));

            case EnergyResourceNameId::Gas()->value:
                return redirect(route('worker.energy-resource.log.submit-gas', ['energyResourceLogId' => $energyResourceLog->id]));

            case EnergyResourceNameId::Biomass()->value:
                return redirect(route('worker.energy-resource.log.submit-biomass', ['energyResourceLogId' => $energyResourceLog->id]));

            case EnergyResourceNameId::FuelOil()->value:
                return redirect(route('worker.energy-resource.log.submit-fuel-oil', ['energyResourceLogId' => $energyResourceLog->id]));
        }
        return redirect(route('worker.energy-resource'));
    }

    public function submitWaterLog($energyResourceLogId)
    {
        $energyResourceLog = EnergyResourceLog::with('employee')->find($energyResourceLogId);
        $dateStartAt = request()->get('date_start_at');
        $dateEndAt = request()->get('date_end_at');

        if (request()->isMethod('post')) {

            request()->validate(['value' => 'required', 'log_date' => 'required']);

            $energyResourceLog->log_date = request()->get('log_date');
            $energyResourceLog->value = request()->get('value');
            $energyResourceLog->unit = "ลิตร/Litre";
            $energyResourceLog->save();

            return redirect(route('worker.energy-resource.logs'));
        }

        return view(
            'worker.energy-resources.submit-water',
            [
                'energyResourceLog' => $energyResourceLog,
                'dateStartAt' => $dateStartAt,
                'dateEndAt' => $dateEndAt
            ]
        );
    }

    public function submitElectricityLog($energyResourceLogId)
    {
        $energyResourceLog = EnergyResourceLog::with('employee')->find($energyResourceLogId);
        $dateStartAt = request()->get('date_start_at');
        $dateEndAt = request()->get('date_end_at');

        if (request()->isMethod('post')) {

            request()->validate(['value' => 'required', 'log_date' => 'required']);

            $energyResourceLog->log_date = request()->get('log_date');
            $energyResourceLog->value = request()->get('value');
            $energyResourceLog->unit = "kw/hour";
            $energyResourceLog->save();

            return redirect(route('worker.energy-resource.logs'));
        }

        return view(
            'worker.energy-resources.submit-electricity',
            [
                'energyResourceLog' => $energyResourceLog,
                'dateStartAt' => $dateStartAt,
                'dateEndAt' => $dateEndAt
            ]
        );
    }

    public function submitGasLog($energyResourceLogId)
    {
        $energyResourceLog = EnergyResourceLog::with('employee')->find($energyResourceLogId);
        $dateStartAt = request()->get('date_start_at');
        $dateEndAt = request()->get('date_end_at');

        if (request()->isMethod('post')) {

            request()->validate(['value' => 'required', 'log_date' => 'required']);

            $energyResourceLog->log_date = request()->get('log_date');
            $energyResourceLog->value = request()->get('value');
            $energyResourceLog->unit = "kg/gas";
            $energyResourceLog->save();

            return redirect(route('worker.energy-resource.logs'));
        }

        return view(
            'worker.energy-resources.submit-gas',
            [
                'energyResourceLog' => $energyResourceLog,
                'dateStartAt' => $dateStartAt,
                'dateEndAt' => $dateEndAt
            ]
        );
    }

    public function submitBiomassLog($energyResourceLogId)
    {
        $energyResourceLog = EnergyResourceLog::with('employee')->find($energyResourceLogId);
        $dateStartAt = request()->get('date_start_at');
        $dateEndAt = request()->get('date_end_at');

        if (request()->isMethod('post')) {

            request()->validate(['value' => 'required', 'log_date' => 'required']);

            $energyResourceLog->log_date = request()->get('log_date');
            $energyResourceLog->value = request()->get('value');
            $energyResourceLog->unit = "kg";
            $energyResourceLog->save();

            return redirect(route('worker.energy-resource.logs'));
        }

        return view(
            'worker.energy-resources.submit-biomass',
            [
                'energyResourceLog' => $energyResourceLog,
                'dateStartAt' => $dateStartAt,
                'dateEndAt' => $dateEndAt
            ]
        );
    }

    public function submitFuelOilLog($energyResourceLogId)
    {
        $energyResourceLog = EnergyResourceLog::with('employee')->find($energyResourceLogId);
        $dateStartAt = request()->get('date_start_at');
        $dateEndAt = request()->get('date_end_at');

        if (request()->isMethod('post')) {

            request()->validate(['value' => 'required', 'log_date' => 'required']);

            $energyResourceLog->log_date = request()->get('log_date');
            $energyResourceLog->value = request()->get('value');
            $energyResourceLog->unit = "kg";
            $energyResourceLog->save();

            return redirect(route('worker.energy-resource.logs'));
        }

        return view(
            'worker.energy-resources.submit-fuel-oil',
            [
                'energyResourceLog' => $energyResourceLog,
                'dateStartAt' => $dateStartAt,
                'dateEndAt' => $dateEndAt
            ]
        );
    }
}