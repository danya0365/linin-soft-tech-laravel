<?php

namespace App\Http\Controllers\Worker\Operation;

use App\Enums\DepartmentNameId;
use App\Enums\EmployeeOperationActionType;
use App\Enums\WorkerOperationStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\DryerMachine;
use App\Models\Job;
use App\Models\EmployeeOperationLog;
use App\Models\LinenType;
use Carbon\CarbonInterval;
use App\Translations\Translator;

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

        $employeeOperationLog = new EmployeeOperationLog;
        $employeeOperationLog->employee_id = $job->dry_employee_id;
        $employeeOperationLog->operation_type = WorkerOperationStatus::Dry();
        $employeeOperationLog->action_type = EmployeeOperationActionType::Progress();
        $employeeOperationLog->save();
        return redirect(route('worker.operation.dry.employee-result', ['jobId' => $job->id]));
    }


    public function getEmployeeResult($jobId)
    {
        $linenTypes = LinenType::with('linenProducts')->get();
        $job = Job::with('employee')->with('customer')->with('jobGroup')->with('washingMachine')->with('dryerMachine')->with('linenType')->with('washEmployee')->with('dryEmployee')->where('id', $jobId)->first();

        $summaryReports = (function () use ($linenTypes, $job) {
            $totalValue = 0;
            $summaryReports = [];
            foreach ($linenTypes as $linenType) {
                $value = Job::where('dry_employee_id', $job->dry_employee_id)->where('linen_type_id', $linenType->id)->sum('wet_weight');
                $totalValue += $value;
                $summaryReports[] = ['title' => $linenType->name, 'value' => $value];
            }
            $summaryReports[] = ['title' => 'จำนวนที่อบแล้ว', 'value' => $totalValue];
            return $summaryReports;
        })();

        $workingDuration = (function () use ($job) {
            // $second = 1;
            // $minute = 60 * $second;
            // $hours  = 60 * $minute;
            // $day    = 24 * $hours;
            // $week   = 7  * $day;
            // $month  = 4  * $week;
            // $year   = 12 * $month;

            // $sum      = $second + $minute + $hours + $day + $week + $month + $year;
            $interval = 0;
            $start = EmployeeOperationLog::where('operation_type', WorkerOperationStatus::Dry())
                ->where('action_type', EmployeeOperationActionType::Start())
                ->where('employee_id', $job->dry_employee_id)
                ->orderBy('id', 'desc')
                ->first();
            if ($start) {
                $end = EmployeeOperationLog::where('operation_type', WorkerOperationStatus::Dry())
                    ->where('employee_id', $job->dry_employee_id)
                    ->orderBy('id', 'desc')
                    ->first();
                $diff = $start->created_at->diff($end->created_at);
                $interval = (function (\DateInterval $interval) {
                    return $interval->days * 86400 + $interval->h * 3600 + $interval->i * 60 + $interval->s;
                })($diff);
            }
            $interval = CarbonInterval::seconds($interval)->cascade();

            $interval->setLocalTranslator(new Translator());
            return $interval->forHumans();
        })();

        return view('worker.operations.wash.employee-result', ['job' => $job->toArray(), 'summaryReports' => $summaryReports, 'workingDuration' => $workingDuration]);
    }
}
