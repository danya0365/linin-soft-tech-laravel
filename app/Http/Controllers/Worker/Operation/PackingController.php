<?php

namespace App\Http\Controllers\Worker\Operation;

use App\Enums\WorkerOperationStatus;
use App\Http\Controllers\Controller;
use App\Managers\EmployeeManager;
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
        $jobGroups = JobGroup::find($jobId);
        $jobGroups->status = WorkerOperationStatus::Packing();
        $jobGroups->save();

        return redirect(route('worker.operation.packing.select-employee', ['jobGroupId' => $jobGroups->id]));
    }
}
