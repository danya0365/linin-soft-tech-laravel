<?php

namespace App\Http\Controllers\Worker;

use App\Enums\DepartmentNameId;
use App\Enums\EnergyResourceNameId;
use App\Enums\ExpenseType;
use App\Http\Controllers\Controller;
use App\Managers\ExpenseManager;
use App\Models\Department;
use App\Models\EnergyResource;
use App\Models\EnergyResourceLog;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class EnergyResourceLogController extends Controller
{

    public function setSelectEnergyResource($energyResourceVarName)
    {
        $energyResource = EnergyResource::where('var_name', $energyResourceVarName)->orWhere('id', $energyResourceVarName)->first();

        $energyResourceLog = new EnergyResourceLog();
        $energyResourceLog->energy_resource_id = $energyResource->id;
        $energyResourceLog->save();

        return redirect(route('worker.energy-resource.select-employee', ['energyResourceLogId' => $energyResourceLog->id]));
    }

    public function getLogs()
    {
        $sortOrders = [
            ['var' => 'id-desc', 'name' => 'ใหม่ที่สุด - Newest'],
            ['var' => 'id-asc', 'name' => 'เก่าที่สุด - Oldest'],
        ];

        $sortOrderSelected = request()->get('sort_order', 'id-desc');
        $energyResourceSelected = request()->get('energy_resource');

        $query = EnergyResourceLog::with('energyResource')->with('employee');
        $query->whereNotNull("energy_resource_id")->whereNotNull("value")->whereNotNull("unit");

        if ($energyResourceSelected) {
            $query->where(function ($query) use ($energyResourceSelected) {
                $query->where('energy_resource_id', $energyResourceSelected);
            });
        }

        $dateStartAt = request()->get('date_start_at');
        $dateEndAt = request()->get('date_end_at');
        if ($dateStartAt && $dateEndAt) {
            $query->whereBetween('created_at', [$dateStartAt . ' 00:00:00', $dateEndAt . ' 23:59:59']);
        }
        if ($sortOrderSelected) {
            list($sort, $order) = explode('-', $sortOrderSelected);
            $query->orderBy($sort, $order);
        }

        $energyResourceLogs = $query->paginate();

        $energyResources = EnergyResource::get();

        return view(
            'worker.energy-resources.logs',
            [
                'energyResources' => $energyResources,
                'energyResourceLogs' => $energyResourceLogs,
                'sortOrders' => $sortOrders,
                'sortOrderSelected' => $sortOrderSelected,
                'energyResourceSelected' => $energyResourceSelected,
                'dateStartAt' => $dateStartAt,
                'dateEndAt' => $dateEndAt
            ]
        );
    }

    public function deleteLog($energyResourceLogId)
    {
        $energyResourceLog = EnergyResourceLog::find($energyResourceLogId);
        $energyResourceLog->delete();
        ExpenseManager::delete($energyResourceLog);

        return redirect()->route('worker.energy-resource.logs')
            ->with('success', 'EnergyResourceLog deleted successfully');
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
                DepartmentNameId::Deliver(),
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

            case EnergyResourceNameId::Petrol()->value:
                return redirect(route('worker.energy-resource.log.submit-petrol', ['energyResourceLogId' => $energyResourceLog->id]));
        }
        return redirect(route('worker.energy-resource'));
    }

    public function submitWaterLog($energyResourceLogId)
    {
        $energyResourceLog = EnergyResourceLog::with('employee')->find($energyResourceLogId);
        $dateStartAt = request()->get('date_start_at');
        $dateEndAt = request()->get('date_end_at');

        if (request()->isMethod('post')) {

            request()->validate(['value' => 'required', 'cost' => 'required', 'created_at' => 'required']);

            $energyResourceLog->value = request()->get('value');
            $energyResourceLog->cost = request()->get('cost');
            $energyResourceLog->unit = "litre";
            $energyResourceLog->timestamps = false;
            $energyResourceLog->created_at = \Carbon\Carbon::parse(request()->get('created_at'));
            $energyResourceLog->updated_at = \Carbon\Carbon::now();
            $energyResourceLog->save();

            ExpenseManager::create(ExpenseType::Water(), $energyResourceLog, $energyResourceLog->cost, $energyResourceLog->created_at);

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

            request()->validate(['value' => 'required', 'cost' => 'required', 'created_at' => 'required']);

            $energyResourceLog->value = request()->get('value');
            $energyResourceLog->cost = request()->get('cost');
            $energyResourceLog->unit = "kw/hour";
            $energyResourceLog->timestamps = false;
            $energyResourceLog->created_at = \Carbon\Carbon::parse(request()->get('created_at'));
            $energyResourceLog->updated_at = \Carbon\Carbon::now();
            $energyResourceLog->save();

            ExpenseManager::create(ExpenseType::Electricity(), $energyResourceLog, $energyResourceLog->cost, $energyResourceLog->created_at);

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

            request()->validate(['value' => 'required', 'cost' => 'required', 'created_at' => 'required']);

            $energyResourceLog->value = request()->get('value');
            $energyResourceLog->cost = request()->get('cost');
            $energyResourceLog->unit = "kg/gas";
            $energyResourceLog->timestamps = false;
            $energyResourceLog->created_at = \Carbon\Carbon::parse(request()->get('created_at'));
            $energyResourceLog->updated_at = \Carbon\Carbon::now();
            $energyResourceLog->save();

            ExpenseManager::create(ExpenseType::Gas(), $energyResourceLog, $energyResourceLog->cost, $energyResourceLog->created_at);

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

            request()->validate(['value' => 'required', 'cost' => 'required', 'created_at' => 'required']);

            $energyResourceLog->value = request()->get('value');
            $energyResourceLog->cost = request()->get('cost');
            $energyResourceLog->unit = "kg";
            $energyResourceLog->timestamps = false;
            $energyResourceLog->created_at = \Carbon\Carbon::parse(request()->get('created_at'));
            $energyResourceLog->updated_at = \Carbon\Carbon::now();
            $energyResourceLog->save();

            ExpenseManager::create(ExpenseType::Biomass(), $energyResourceLog, $energyResourceLog->cost, $energyResourceLog->created_at);

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

            request()->validate(['value' => 'required', 'cost' => 'required', 'created_at' => 'required']);

            $energyResourceLog->value = request()->get('value');
            $energyResourceLog->cost = request()->get('cost');
            $energyResourceLog->unit = "litre";
            $energyResourceLog->timestamps = false;
            $energyResourceLog->created_at = \Carbon\Carbon::parse(request()->get('created_at'));
            $energyResourceLog->updated_at = \Carbon\Carbon::now();
            $energyResourceLog->save();

            ExpenseManager::create(ExpenseType::FuelOil(), $energyResourceLog, $energyResourceLog->cost, $energyResourceLog->created_at);

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

    public function submitPetrolLog($energyResourceLogId)
    {
        $energyResourceLog = EnergyResourceLog::with('employee')->find($energyResourceLogId);
        $dateStartAt = request()->get('date_start_at');
        $dateEndAt = request()->get('date_end_at');

        if (request()->isMethod('post')) {

            request()->validate(['value' => 'required', 'cost' => 'required', 'created_at' => 'required']);

            $energyResourceLog->value = request()->get('value');
            $energyResourceLog->cost = request()->get('cost');
            $energyResourceLog->unit = "litre";
            $energyResourceLog->timestamps = false;
            $energyResourceLog->created_at = \Carbon\Carbon::parse(request()->get('created_at'));
            $energyResourceLog->updated_at = \Carbon\Carbon::now();
            $energyResourceLog->save();

            ExpenseManager::create(ExpenseType::Petrol(), $energyResourceLog, $energyResourceLog->cost, $energyResourceLog->created_at);

            return redirect(route('worker.energy-resource.logs'));
        }

        return view(
            'worker.energy-resources.submit-petrol',
            [
                'energyResourceLog' => $energyResourceLog,
                'dateStartAt' => $dateStartAt,
                'dateEndAt' => $dateEndAt
            ]
        );
    }
}
