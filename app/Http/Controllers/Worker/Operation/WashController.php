<?php

namespace App\Http\Controllers\Worker\Operation;

use App\Enums\DepartmentNameId;
use App\Enums\EmployeeOperationActionType;
use App\Enums\JobGroupStatus;
use App\Enums\WorkerOperationStatus;
use App\Http\Controllers\Controller;
use App\Managers\EmployeeManager;
use Illuminate\Http\Request;
use App\Models\CustomerGroup;
use App\Models\Department;
use App\Models\JobGroup;
use App\Models\Job;
use App\Models\JobCase;
use App\Models\WashingMachine;
use App\Models\LinenProduct;
use App\Models\LinenType;

class WashController extends Controller
{
    public function index()
    {
        return redirect(route('worker.operation.wash.select-employee'));
    }

    public function selectEmployee()
    {
        $departments = Department::with('employees')->where('id', DepartmentNameId::Wash())->get();
        return view('worker.operations.wash.select-employee', ['departments' => $departments->toArray()]);
    }

    public function setSelectEmployee($employeeId)
    {
        $job = new Job;
        $job->employee_id = $employeeId;
        $job->wash_employee_id = $employeeId;
        $job->status = WorkerOperationStatus::Wash();
        $job->save();

        EmployeeManager::createEmployeeOperationLog($employeeId, WorkerOperationStatus::Wash(), EmployeeOperationActionType::Start());

        return redirect(route('worker.operation.wash.select-customer', ['jobId' => $job->id]));
    }
}
