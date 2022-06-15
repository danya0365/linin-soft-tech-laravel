<?php

namespace App\Http\Controllers\Supervisor;

use App\Enums\ExpenseType;
use App\Http\Controllers\Controller;
use App\Managers\ExpenseManager;
use App\Models\Department;
use App\Models\DepartmentDailyCostLog;

class CustomerController extends Controller
{
    public function index()
    {
        return view('supervisor.customers.index');
    }

    public function getNewBilling()
    {
        return view('supervisor.customers.index');
    }

    public function getBillingLog()
    {
        return view('supervisor.customers.index');
    }
}
