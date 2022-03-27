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

        if ($job->washing_machine_id) WashingMachine::where('id', $job->washing_machine_id)->update(['job_id' => null]);

        EmployeeManager::createEmployeeOperationLog($job->wash_employee_id, WorkerOperationStatus::Wash(), EmployeeOperationActionType::Stop());

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

        EmployeeManager::createEmployeeOperationLog($employeeId, WorkerOperationStatus::Dry(), EmployeeOperationActionType::Start());

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

    public function getSubmit(Request $request, $jobId)
    {
        $job = Job::with('employee')->with('customer')->with('jobGroup')->with('washingMachine')->with('dryerMachine')->with('linenType')->with('washEmployee')->where('id', $jobId)->first();
        return view('worker.operations.dry.submit', ['job' => $job->toArray()]);
    }

    public function postSubmit(Request $request, $jobId)
    {
        request()->validate(['wet_weight' => 'required', 'color' => 'required']);
        $job = Job::find($jobId);
        $job->wet_weight = $request->get('wet_weight');
        $job->color = $request->get('color');
        $job->save();

        EmployeeManager::createEmployeeOperationLog($job->dry_employee_id, WorkerOperationStatus::Dry(), EmployeeOperationActionType::Progress());

        return redirect(route('worker.operation.dry.employee-result', ['jobId' => $job->id]));
    }


    public function getEmployeeResult($jobId)
    {
        $linenTypes = LinenType::with('linenProducts')->get();
        $job = Job::with('employee')->with('customer')->with('jobGroup')->with('washingMachine')->with('dryerMachine')->with('linenType')->with('washEmployee')->with('dryEmployee')->where('id', $jobId)->first();

        $summaryReports = $job->drySummaryReport();
        $workingDuration = $job->dryEmployee->getTotalTimeDurationOfWorkingTime();

        return view('worker.operations.dry.employee-result', ['job' => $job->toArray(), 'summaryReports' => $summaryReports, 'workingDuration' => $workingDuration]);
    }
}
