<?php

namespace App\Http\Controllers\Worker\Operation;

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

class WashController extends Controller
{
    public function selectEmployee()
    {
        $departments = Department::with('employees')->where('id', 2)->get();
        return view('worker.operations.wash.select-employee', ['departments' => $departments->toArray()]);
    }

    public function setSelectEmployee($employeeId)
    {
        $job = new Job;
        $job->employee_id = $employeeId;
        $job->wash_employee_id = $employeeId;
        $job->status = 'wash';
        $job->save();

        $employeeOperationLog = new EmployeeOperationLog;
        $employeeOperationLog->employee_id = $employeeId;
        $employeeOperationLog->operation_type = 'wash';
        $employeeOperationLog->action_type = 'start';
        $employeeOperationLog->save();
        return redirect(route('worker.operation.wash.select-customer', ['jobId' => $job->id]));
    }

    public function selectCustomer($jobId)
    {
        $job = Job::with('employee')->where('id', $jobId)->first();
        $customerGroup = CustomerGroup::with('customers')->get();
        return view('worker.operations.wash.select-customer', ['customerGroups' => $customerGroup->toArray(), 'job' => $job->toArray()]);
    }

    public function setSelectCustomer($jobId, $customerId)
    {
        $job = Job::find($jobId);
        $job->customer_id = $customerId;
        $job->save();
        return redirect(route('worker.operation.wash.select-job-group', ['jobId' => $job->id]));
    }

    public function selectJobGroup($jobId)
    {
        $job = Job::with('employee')->with('customer')->where('id', $jobId)->first();
        $todayJobGroups = JobGroup::where('customer_id', $job->customer_id)->whereDate('created_at', \Carbon\Carbon::today())->get();
        return view('worker.operations.wash.select-job-group', ['todayJobGroups' => $todayJobGroups->toArray(), 'job' => $job->toArray()]);
    }

    public function setSelectJobGroup($jobId, $jobGroupId)
    {
        $job = Job::find($jobId);
        $job->job_group_id = $jobGroupId;
        $job->save();

        $jobGroup = JobGroup::find($jobGroupId);
        $jobGroup->operation_status = 'progress';
        $jobGroup->save();

        $employeeOperationLog = new EmployeeOperationLog;
        $employeeOperationLog->employee_id = $jobGroup->pickup_employee_id;
        $employeeOperationLog->operation_type = 'pickup';
        $employeeOperationLog->action_type = 'stop';
        $employeeOperationLog->save();

        return redirect(route('worker.operation.wash.select-job-case', ['jobId' => $job->id]));
    }

    public function selectJobCase($jobId)
    {
        $job = Job::with('employee')->with('customer')->with('jobGroup')->where('id', $jobId)->first();
        $jobCases = JobCase::$list;
        return view('worker.operations.wash.select-job-case', ['job' => $job->toArray(), 'jobCases' => $jobCases]);
    }

    public function setSelectJobCase($jobId, $jobCase)
    {
        $job = Job::find($jobId);
        $job->job_case = $jobCase;
        $job->save();
        return redirect(route('worker.operation.wash.select-washing-machine', ['jobId' => $job->id]));
    }

    public function selectWashingMachine($jobId)
    {
        $job = Job::with('employee')->with('customer')->with('jobGroup')->where('id', $jobId)->first();
        $washingMachines = WashingMachine::with('job')->get();
        return view('worker.operations.wash.select-washing-machine', ['job' => $job->toArray(), 'washingMachines' => $washingMachines->toArray()]);
    }

    public function setSelectWashingMachine($jobId, $washingMachineId)
    {
        $job = Job::find($jobId);
        $prevWashingMachineId = $job->washing_machine_id;
        $job->washing_machine_id = $washingMachineId;
        $job->save();

        if ($prevWashingMachineId) WashingMachine::where('id', $prevWashingMachineId)->update(['job_id' => null]);
        WashingMachine::where('id', $washingMachineId)->update(['job_id' => $job->id]);
        return redirect(route('worker.operation.wash.select-linen-type', ['jobId' => $job->id]));
    }
}
