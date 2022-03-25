<?php

namespace App\Http\Controllers\Worker\Operation;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\CustomerGroup;
use App\Models\Department;
use App\Models\JobGroup;

class PickUpController extends Controller
{
    public function selectEmployee()
    {
        $departments = Department::with('employees')->where('id', 1)->get();
        return view('worker.operations.pickups.select-employee', ['departments' => $departments->toArray()]);
    }

    public function setSelectEmployee($employeeId)
    {
        $jobGroup = new JobGroup;
        $jobGroup->employee_id = $employeeId;
        $jobGroup->operation_status = 'progress';
        $jobGroup->save();
        return redirect(route('worker.operation.pick-up.select-customer', ['jobGroupId' => $jobGroup->id]));
    }

    public function selectCustomer($jobGroupId)
    {
        $jobGroup = JobGroup::with('employee')->where('id', $jobGroupId)->first();
        $customerGroup = CustomerGroup::with('customers')->get();
        return view('worker.operations.pickups.select-customer', ['customerGroups' => $customerGroup->toArray(), 'jobGroup' => $jobGroup->toArray()]);
    }

    public function setSelectCustomer($jobGroupId, $customerId)
    {
        $todayJob = JobGroup::where('customer_id', $customerId)->whereDate('created_at', \Carbon\Carbon::today())->get();
        if ($todayJob->count() > 1) {
            // TODO: Notify to user here by alert box
            $jobGroup = JobGroup::find($jobGroupId);
            $jobGroup->customer_id = $customerId;
            $jobGroup->save();
        } else {
            $jobGroup = JobGroup::find($jobGroupId);
            $jobGroup->customer_id = $customerId;
            $jobGroup->save();
        }
        return redirect(route('worker.operation.pick-up.submit', ['jobGroupId' => $jobGroup->id]));
    }

    public function getSubmit(Request $request, $jobGroupId)
    {
        $jobGroup = JobGroup::with('employee')->with('customer')->where('id', $jobGroupId)->first();
        return view('worker.operations.pickups.submit', ['jobGroup' => $jobGroup]);
    }

    public function postSubmit(Request $request, $jobGroupId)
    {
        request()->validate(['wet_weight' => 'required']);
        $jobGroup = JobGroup::find($jobGroupId);
        $jobGroup->wet_weight = $request->get('wet_weight');
        $jobGroup->save();

        // TODO: update customer total_wet_weight
        $totalWetWeight = JobGroup::where('customer_id', $jobGroup->customer_id)->sum('wet_weight');
        Customer::where('id', $jobGroup->customer_id)->update(['total_wet_weight' => $totalWetWeight]);
        return view('worker.operations.pickups.submit', ['jobGroup' => $jobGroup]);
    }
}
