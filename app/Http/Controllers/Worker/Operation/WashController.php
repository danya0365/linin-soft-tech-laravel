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
        return redirect(route('worker.operation.wash.select-customer', ['job' => $job->id]));
    }
}
