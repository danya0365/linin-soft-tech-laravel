<?php

namespace App\Http\Controllers\Supervisor;

use App\Enums\ExpenseType;
use App\Enums\IncomeType;
use App\Enums\OperationStatus;
use App\Enums\OperationType;
use App\Http\Controllers\Controller;
use App\Managers\ExpenseManager;
use App\Managers\IncomeManager;
use App\Managers\OperationManager;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Department;
use App\Models\DepartmentDailyCostLog;
use App\Models\Operation;

class CustomerController extends Controller
{
    public function index()
    {
        return view('supervisor.customers.index');
    }

    public function getNewBilling()
    {
        if (request()->isMethod('post')) {

            request()->validate(
                [
                    'customer_id' => 'required',
                    'total_billing_weight' => 'required',
                    'total_billing_payment' => 'required',
                    'billing_payment_date' => 'required'
                ]
            );

            $operation = new Operation();
            $operation->operation_type = OperationType::Payment();
            $operation->status = OperationStatus::Close();
            $operation->customer_id = request()->get('customer_id');
            $operation->total_billing_weight = request()->get('total_billing_weight');
            $operation->total_billing_payment = request()->get('total_billing_payment');
            $operation->billing_payment_date = request()->get('billing_payment_date');
            $operation->save();

            OperationManager::createCustomerOperationDailySummary($operation);
            IncomeManager::create(IncomeType::CustomerBilling(), $operation, $operation->total_billing_payment, $operation->billing_payment_date);
            return redirect(route('supervisor.customer.billing-logs'));
        }
        $customerGroups = CustomerGroup::with('customers')->get();
        return view('supervisor.customers.new-billing', ['customerGroups' => $customerGroups]);
    }

    public function getBillingLog()
    {
        return view('supervisor.customers.index');
    }
}
