<?php

namespace App\Http\Controllers\Worker\Operation;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\CustomerGroup;
use App\Models\Department;
use App\Models\JobGroup;
use App\Models\Job;

class WashController extends Controller
{
    public function selectEmployee()
    {
        $departments = Department::with('employees')->where('id', 1)->get();
        return view('worker.operations.wash.select-employee', ['departments' => $departments->toArray()]);
    }

    public function setSelectEmployee($employeeId)
    {
        $job = new Job;
        $job->employee_id = $employeeId;
        $job->status = 'wash';
        $job->save();
        return redirect(route('worker.operation.wash.select-customer', ['jobId' => $job->id]));
    }

    public function selectCustomer($jobId)
    {
        $job = Job::with('employee')->where('id', $jobId)->first();
        $customerGroup = CustomerGroup::with('customers')->get();
        return view('worker.operations.wash.select-customer', ['customerGroups' => $customerGroup->toArray(), 'job' => $job]);
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
        return view('worker.operations.wash.select-job-group', ['todayJobGroups' => $todayJobGroups->toArray(), 'job' => $job]);
    }

    public function setSelectJobGroup($jobId, $jobGroupId)
    {
        $job = Job::find($jobId);
        $job->job_group_id = $jobGroupId;
        $job->save();
        return redirect(route('worker.operation.wash.select-linen-type', ['jobId' => $job->id]));
    }
}
