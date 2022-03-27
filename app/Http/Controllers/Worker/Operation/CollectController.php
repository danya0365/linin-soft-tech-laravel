<?php

namespace App\Http\Controllers\Worker\Operation;

use App\Enums\DepartmentNameId;
use App\Enums\EmployeeOperationActionType;
use App\Enums\JobGroupStatus;
use App\Enums\WorkerOperationStatus;
use App\Http\Controllers\Controller;
use App\Managers\EmployeeManager;
use App\Models\Department;
use App\Models\JobGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CollectController extends Controller
{
    public function index()
    {
        return redirect(route('worker.operation.collect.select-job-group'));
    }

    public function selectJobGroup()
    {
        $jobGroups = JobGroup::with('employee')
            ->with('customer')
            ->with('jobs')
            ->with('pickUpEmployee')
            ->with('packingEmployee')
            ->with('collectEmployee')
            ->whereIn('operation_status', [JobGroupStatus::Packing(), JobGroupStatus::Collect()])
            ->get();

        $customers = [];
        foreach ($jobGroups as $jobGroup) {
            $customer = $jobGroup->customer->toArray();
            if (!isset($customers[$customer['id']])) {
                $customer['job_groups'] = [];
                $customers[$customer['id']] = $customer;
            }
            $customer = $customers[$customer['id']];
            $customer['job_groups'][] = $jobGroup->toArray();
            $customers[$customer['id']] = $customer;
        }
        return view('worker.operations.collect.select-job-group', ['customers' => $customers]);
    }

    public function setSelectJobGroup($jobGroupId)
    {
        $jobGroup = JobGroup::find($jobGroupId);
        $jobGroup->operation_status = WorkerOperationStatus::Collect();
        $jobGroup->save();

        return redirect(route('worker.operation.collect.select-employee', ['jobGroupId' => $jobGroup->id]));
    }

    public function selectEmployee($jobGroupId)
    {
        $jobGroup = JobGroup::with('employee')
            ->with('customer')
            ->with('jobs')
            ->with('pickUpEmployee')
            ->with('packingEmployee')
            ->with('collectEmployee')
            ->where('id', $jobGroupId)->first();

        $departments = Department::with('employees')->where('id', DepartmentNameId::Collect())->get();
        return view('worker.operations.collect.select-employee', ['departments' => $departments->toArray(), 'jobGroup' => $jobGroup->toArray()]);
    }

    public function setSelectEmployee($jobGroupId, $employeeId)
    {
        $jobGroup = JobGroup::find($jobGroupId);
        $jobGroup->employee_id = $employeeId;
        $jobGroup->collect_employee_id = $employeeId;
        $jobGroup->save();

        EmployeeManager::createEmployeeOperationLog($employeeId, WorkerOperationStatus::Collect(), EmployeeOperationActionType::Start());

        return redirect(route('worker.operation.collect.submit', ['jobGroupId' => $jobGroup->id]));
    }

    public function getSubmit(Request $request, $jobGroupId)
    {
        $jobGroup = JobGroup::with('employee')
            ->with('customer')
            ->with('jobs')
            ->with('pickUpEmployee')
            ->with('packingEmployee')
            ->with('collectEmployee')
            ->where('id', $jobGroupId)->first();
        return view('worker.operations.collect.submit', ['jobGroup' => $jobGroup->toArray()]);
    }

    public function postSubmit(Request $request, $jobGroupId)
    {
        request()->validate(['dry_weight' => 'required']);
        $jobGroup = JobGroup::find($jobGroupId);
        $jobGroup->dry_weight = $request->get('dry_weight');
        $jobGroup->save();

        EmployeeManager::createEmployeeOperationLog($jobGroup->collect_employee_id, WorkerOperationStatus::Collect(), EmployeeOperationActionType::Progress());

        return redirect(route('worker.operation.collect.employee-result', ['jobGroupId' => $jobGroup->id]));
    }

    public function getEmployeeResult(Request $request, $jobGroupId)
    {
        $jobGroup = JobGroup::with('employee')
            ->with('customer')
            ->with('jobs')
            ->with('pickUpEmployee')
            ->with('packingEmployee')
            ->with('collectEmployee')
            ->where('id', $jobGroupId)->first();

        $summaryReports = $jobGroup->collectSummaryReport();
        $workingDuration = $jobGroup->collectEmployee->getTotalTimeDurationOfWorkingTime();

        return view('worker.operations.collect.employee-result', ['jobGroup' => $jobGroup->toArray(), 'summaryReports' => $summaryReports, 'workingDuration' => $workingDuration]);
    }

    public function postEmployeeResult(Request $request, $jobGroupId)
    {
        if ($request->get('operation_status') == JobGroupStatus::Close()) {
            $jobGroup = JobGroup::find($jobGroupId);
            $jobGroup->operation_status = $request->get('operation_status');
            $jobGroup->save();

            EmployeeManager::createEmployeeOperationLog($jobGroup->collect_employee_id, WorkerOperationStatus::Collect(), EmployeeOperationActionType::Stop());
        }

        return redirect(route('worker.operation.collect.employee-result', ['jobGroupId' => $jobGroupId]));
    }
}
