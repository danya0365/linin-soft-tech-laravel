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
        $customerGroup = CustomerGroup::with('customers')->get();
        return view('worker.operations.wash.select-customer', ['customerGroups' => $customerGroup->toArray(), 'jobId' => $jobId]);
    }

    public function setSelectCustomer($jobId, $customerId)
    {
        $todayJobs = JobGroup::where('customer_id', $customerId)->whereDate('created_at', \Carbon\Carbon::today())->get();

        $job = Job::find($jobId);
        $job->customer_id = $customerId;
        $job->save();
        return redirect(route('worker.operation.wash.select-job-group', ['jobId' => $job->id]));
    }

    public function selectJobGroup($jobId)
    {
        $job = Job::find($jobId);
        $todayJobGroups = JobGroup::where('customer_id', $job->customerId)->whereDate('created_at', \Carbon\Carbon::today())->get();
        return view('worker.operations.wash.select-job-group', ['todayJobGroups' => $todayJobGroups->toArray(), 'jobId' => $jobId]);
    }
}
