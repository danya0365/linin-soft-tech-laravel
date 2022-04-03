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
        return redirect(route('worker.operation'));
    }
}
