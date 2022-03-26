<?php

namespace App\Http\Controllers\Worker\Operation;

use App\Enums\DepartmentNameId;
use App\Enums\EmployeeOperationActionType;
use App\Enums\WorkerOperationStatus;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\CustomerGroup;
use App\Models\Department;
use App\Models\DryerMachine;
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
            ->whereIn('status', [WorkerOperationStatus::Wash(), WorkerOperationStatus::Dry()])
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
        $employeeOperationLog->operation_type = WorkerOperationStatus::Wash();
        $employeeOperationLog->action_type = EmployeeOperationActionType::Stop();
        $employeeOperationLog->save();

        return redirect(route('worker.operation.dry.select-employee', ['jobId' => $job->id]));
    }

    public function selectEmployee($jobId)
    {
        $departments = Department::with('employees')->where('id', DepartmentNameId::Dry())->get();
        $job = Job::with('employee')->with('customer')->with('jobGroup')->with('washingMachine')->with('linenType')->with('washEmployee')->where('id', $jobId)->first();
        return view('worker.operations.dry.select-employee', ['departments' => $departments->toArray(), 'job' => $job->toArray()]);
    }

    public function setSelectEmployee($jobId, $employeeId)
    {
        $job = Job::find($jobId);
        $job->employee_id = $employeeId;
        $job->dry_employee_id = $employeeId;
        $job->status = WorkerOperationStatus::Dry();
        $job->save();

        $employeeOperationLog = new EmployeeOperationLog;
        $employeeOperationLog->employee_id = $employeeId;
        $employeeOperationLog->operation_type = WorkerOperationStatus::Dry();
        $employeeOperationLog->action_type = EmployeeOperationActionType::Start();
        $employeeOperationLog->save();


        return redirect(route('worker.operation.dry.select-dryer-machine', ['jobId' => $job->id]));
    }

    public function selectDryerMachine($jobId)
    {
        $job = Job::with('employee')->with('customer')->with('jobGroup')->with('washingMachine')->with('linenType')->with('washEmployee')->where('id', $jobId)->first();
        $dryerMachines = DryerMachine::with('job')->get();
        return view('worker.operations.dry.select-dryer-machine', ['job' => $job->toArray(), 'dryerMachines' => $dryerMachines->toArray()]);
    }

    public function setSelectDryerMachine($jobId, $dryerMachineId)
    {
        $job = Job::find($jobId);
        $prevDryerMachineId = $job->dryer_machine_id;
        $job->dryer_machine_id = $dryerMachineId;
        $job->save();

        if ($prevDryerMachineId) DryerMachine::where('id', $prevDryerMachineId)->update(['job_id' => null]);
        DryerMachine::where('id', $dryerMachineId)->update(['job_id' => $job->id]);
        return redirect(route('worker.operation.dry.submit', ['jobId' => $job->id]));
    }
}
