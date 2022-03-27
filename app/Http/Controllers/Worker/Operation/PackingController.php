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
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
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

    public function setSelectJobGroup($jobId)
    {
        $jobGroup = JobGroup::find($jobId);
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

        $departments = Department::with('employees')->where('id', DepartmentNameId::PickUp())->get();
        return view('worker.operations.packing.select-employee', ['departments' => $departments->toArray(), 'jobGroup' => $jobGroup->toArray()]);
    }

    public function setSelectEmployee($jobGroupId, $employeeId)
    {
        $jobGroup = JobGroup::find($jobGroupId);
        $jobGroup->employee_id = $employeeId;
        $jobGroup->packing_employee_id = $employeeId;
        $jobGroup->operation_status = WorkerOperationStatus::Packing();
        $jobGroup->save();

        EmployeeManager::createEmployeeOperationLog($employeeId, WorkerOperationStatus::Packing(), EmployeeOperationActionType::Start());

        return redirect(route('worker.operation.packing.submit', ['jobGroupId' => $jobGroup->id]));
    }
}
