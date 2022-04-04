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
use App\Models\WashingMachine;

class IronController extends Controller
{
    public function index()
    {
        return redirect(route('worker.operation'));
    }
}
