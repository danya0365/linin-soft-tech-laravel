<?php

namespace App\Http\Controllers\Worker\Operation;

use App\Enums\EmployeeOperationActionType;
use App\Enums\WorkerOperationStatus;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\CustomerGroup;
use App\Models\Department;
use App\Models\JobGroup;
use App\Models\Job;
use App\Models\JobCase;
use App\Models\WashingMachine;
use App\Models\EmployeeOperationLog;
use App\Models\LinenProduct;
use App\Models\LinenType;
use Carbon\CarbonInterval;

class DryController extends Controller
{
    public function selectJob()
    {
        $jobs = Job::with('employee')
            ->with('customer')
            ->with('jobGroup')
            ->with('washingMachine')
            ->with('linenType')
            ->with('washEmployee')
            ->where('status', WorkerOperationStatus::Wash())
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
        return view('worker.operations.dry.select-job', ['customers' => $customers]);
    }

    public function setSelectJob($jobId)
    {
        $job = Job::find($jobId);
        $job->status = WorkerOperationStatus::Dry();
        $job->save();

        $employeeOperationLog = new EmployeeOperationLog;
        $employeeOperationLog->employee_id = $job->wash_employee_id;
        $employeeOperationLog->operation_type = WorkerOperationStatus::Dry();
        $employeeOperationLog->action_type = EmployeeOperationActionType::Start();
        $employeeOperationLog->save();

        return redirect(route('worker.operation.dry.select-employee', ['jobId' => $job->id]));
    }
}
