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
        $jobGroup->operation_status = JobGroupStatus::Progress();
        $jobGroup->save();

        EmployeeManager::createEmployeeOperationLog($jobGroup->pickup_employee_id, WorkerOperationStatus::PickUp(), EmployeeOperationActionType::Stop());

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

    public function selectLinenType($jobId)
    {
        $job = Job::with('employee')->with('customer')->with('jobGroup')->with('washingMachine')->where('id', $jobId)->first();
        $linenTypes = LinenType::with('linenProducts')->get();
        $linenProducts = LinenProduct::all();
        return view('worker.operations.wash.select-linen-type', ['job' => $job->toArray(), 'linenTypes' => $linenTypes->toArray(), 'linenProductJson' => $linenProducts->toJson()]);
    }

    public function setSelectLinenType($jobId, $tags)
    {
        $linenProductIds = explode(',', $tags);
        $linenProducts = LinenProduct::whereIn('id', $linenProductIds)->get();

        $linenTypeId = null;
        $tags = [];
        foreach ($linenProducts as $linenProduct) {
            $linenTypeId = $linenProduct->linen_type_id;
            $tags[] = $linenProduct->name;
        }
        $job = Job::find($jobId);
        $job->linen_type_id = $linenTypeId;
        $job->tags = implode(', ', $tags);
        $job->save();

        return redirect(route('worker.operation.wash.submit', ['jobId' => $job->id]));
    }

    public function getSubmit(Request $request, $jobId)
    {
        $job = Job::with('employee')->with('customer')->with('jobGroup')->with('washingMachine')->with('linenType')->where('id', $jobId)->first();
        return view('worker.operations.wash.submit', ['job' => $job->toArray()]);
    }

    public function postSubmit(Request $request, $jobId)
    {
        request()->validate(['wet_weight' => 'required', 'color' => 'required']);
        $job = Job::find($jobId);
        $job->wet_weight = $request->get('wet_weight');
        $job->color = $request->get('color');
        $job->save();

        EmployeeManager::createEmployeeOperationLog($job->wash_employee_id, WorkerOperationStatus::Wash(), EmployeeOperationActionType::Progress());

        return redirect(route('worker.operation.wash.employee-result', ['jobId' => $job->id]));
    }

    public function getEmployeeResult($jobId)
    {
        $job = Job::with('employee')->with('customer')->with('jobGroup')->with('washingMachine')->with('linenType')->with('washEmployee')->where('id', $jobId)->first();

        $summaryReports = $job->washSummaryReport();
        $workingDuration = $job->washEmployee->getTotalTimeDurationOfWorkingTime();

        return view('worker.operations.wash.employee-result', ['job' => $job->toArray(), 'summaryReports' => $summaryReports, 'workingDuration' => $workingDuration]);
    }
}
