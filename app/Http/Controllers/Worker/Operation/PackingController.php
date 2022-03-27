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

class PackingController extends Controller
{
    public function index()
    {
        return redirect(route('worker.operation.packing.select-job-group'));
    }

    public function selectJobGroup()
    {
        $jobGroups = JobGroup::with('employee')
            ->with('customer')
            ->with('jobs')
            ->with('pickUpEmployee')
            ->with('packingEmployee')
            ->with('collectEmployee')
            ->whereIn('operation_status', [WorkerOperationStatus::PickUp(), WorkerOperationStatus::Packing()])
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
        return view('worker.operations.packing.select-job-group', ['customers' => $customers]);
    }

    public function setSelectJobGroup($jobGroupId)
    {
        $jobGroup = JobGroup::find($jobGroupId);
        $jobGroup->operation_status = WorkerOperationStatus::Packing();
        $jobGroup->save();

        return redirect(route('worker.operation.packing.select-employee', ['jobGroupId' => $jobGroup->id]));
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

        $departments = Department::with('employees')->where('id', DepartmentNameId::Packing())->get();
        return view('worker.operations.packing.select-employee', ['departments' => $departments->toArray(), 'jobGroup' => $jobGroup->toArray()]);
    }

    public function setSelectEmployee($jobGroupId, $employeeId)
    {
        $jobGroup = JobGroup::find($jobGroupId);
        $jobGroup->employee_id = $employeeId;
        $jobGroup->packing_employee_id = $employeeId;
        $jobGroup->save();

        EmployeeManager::createEmployeeOperationLog($employeeId, WorkerOperationStatus::Packing(), EmployeeOperationActionType::Start());

        return redirect(route('worker.operation.packing.submit', ['jobGroupId' => $jobGroup->id]));
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
        return view('worker.operations.packing.submit', ['jobGroup' => $jobGroup->toArray()]);
    }

    public function postSubmit(Request $request, $jobGroupId)
    {
        request()->validate(['total_pieces' => 'required']);
        $jobGroup = JobGroup::find($jobGroupId);
        $jobGroup->total_pieces = $request->get('total_pieces');
        $jobGroup->save();

        EmployeeManager::createEmployeeOperationLog($jobGroup->packing_employee_id, WorkerOperationStatus::Packing(), EmployeeOperationActionType::Progress());

        return redirect(route('worker.operation.packing.employee-result', ['jobGroupId' => $jobGroup->id]));
    }

    public function getEmployeeResult($jobGroupId)
    {
        $jobGroup = JobGroup::with('employee')
            ->with('customer')
            ->with('jobs')
            ->with('pickUpEmployee')
            ->with('packingEmployee')
            ->with('collectEmployee')
            ->where('id', $jobGroupId)->first();

        $summaryReports = $jobGroup->packingSummaryReport();
        $workingDuration = $jobGroup->packingEmployee->getTotalTimeDurationOfWorkingTime();

        return view('worker.operations.packing.employee-result', ['jobGroup' => $jobGroup->toArray(), 'summaryReports' => $summaryReports, 'workingDuration' => $workingDuration]);
    }
}
