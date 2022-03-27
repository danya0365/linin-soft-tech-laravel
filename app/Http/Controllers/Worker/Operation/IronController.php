<?php

namespace App\Http\Controllers\Worker\Operation;

use App\Enums\DepartmentNameId;
use App\Enums\EmployeeOperationActionType;
use App\Enums\WorkerOperationStatus;
use App\Http\Controllers\Controller;
use App\Managers\EmployeeManager;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\DryerMachine;
use App\Models\Job;
use App\Models\LinenType;
use App\Models\WashingMachine;

class IronController extends Controller
{
    public function index()
    {
        return redirect(route('worker.operation.iron.select-job'));
    }

    public function selectJob()
    {
        $jobs = Job::with('employee')
            ->with('customer')
            ->with('jobGroup')
            ->with('washingMachine')
            ->with('linenType')
            ->with('washEmployee')
            ->whereIn('status', [WorkerOperationStatus::Dry(), WorkerOperationStatus::Iron()])
            ->get();

        $customers = [];
        foreach ($jobs as $job) {
            $customer = $job->customer->toArray();
            if (!isset($customers[$customer['id']])) {
                $customer['jobs'] = [];
                $customers[$customer['id']] = $customer;
            }
            $customer = $customers[$customer['id']];
            $customer['jobs'][] = $job->toArray();
            $customers[$customer['id']] = $customer;
        }
        return view('worker.operations.iron.select-job', ['customers' => $customers]);
    }

    public function setSelectJob($jobId)
    {
        $job = Job::find($jobId);
        $job->status = WorkerOperationStatus::Iron();
        $job->save();

        if ($job->washing_machine_id) WashingMachine::where('id', $job->washing_machine_id)->update(['job_id' => null]);
        if ($job->dryer_machine_id) DryerMachine::where('id', $job->dryer_machine_id)->update(['job_id' => null]);

        EmployeeManager::createEmployeeOperationLog($job->dry_employee_id, WorkerOperationStatus::Dry(), EmployeeOperationActionType::Stop());

        return redirect(route('worker.operation.iron.select-employee', ['jobId' => $job->id]));
    }

    public function selectEmployee($jobId)
    {
        $departments = Department::with('employees')->where('id', DepartmentNameId::Iron())->get();
        $job = Job::with('employee')
            ->with('customer')
            ->with('jobGroup')
            ->with('washingMachine')
            ->with('dryerMachine')
            ->with('linenType')
            ->with('washEmployee')
            ->with('dryEmployee')
            ->where('id', $jobId)->first();
        return view('worker.operations.iron.select-employee', ['departments' => $departments->toArray(), 'job' => $job->toArray()]);
    }

    public function setSelectEmployee($jobId, $employeeId)
    {
        $job = Job::find($jobId);
        $job->employee_id = $employeeId;
        $job->iron_employee_id = $employeeId;
        $job->status = WorkerOperationStatus::Dry();
        $job->save();

        EmployeeManager::createEmployeeOperationLog($employeeId, WorkerOperationStatus::Iron(), EmployeeOperationActionType::Start());

        return redirect(route('worker.operation.iron.submit', ['jobId' => $job->id]));
    }
}
