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
            ->whereIn('operation_status', [WorkerOperationStatus::Packing(), WorkerOperationStatus::Collect()])
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

    public function setSelectJobGroup($jobId)
    {
        $jobGroup = JobGroup::find($jobId);
        $jobGroup->operation_status = WorkerOperationStatus::Packing();
        $jobGroup->save();

        return redirect(route('worker.operation.collect.select-employee', ['jobGroupId' => $jobGroup->id]));
    }
}
