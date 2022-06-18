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
use App\Models\OperationLinenProduct;
use Illuminate\Support\Facades\DB;

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
        $sortOrders = [
            ['var' => 'id-desc', 'name' => 'ใหม่ที่สุด - Newest'],
            ['var' => 'id-asc', 'name' => 'เก่าที่สุด - Oldest'],
        ];
        $sortOrderSelected = request()->get('sort_order', 'id-desc');
        $customerIdSelected = request()->get('customer_id');

        $query = Operation::with('customer');

        $query->where('operation_type', OperationType::Payment());
        if ($customerIdSelected) {
            $query->where('customer_id', $customerIdSelected);
        }

        $dateStartAt = request()->get('date_start_at');
        $dateEndAt = request()->get('date_end_at');
        if ($dateStartAt && $dateEndAt) {
            $query->whereBetween('created_at', [$dateStartAt . ' 00:00:00', $dateEndAt . ' 23:59:59']);
        }
        if ($sortOrderSelected) {
            list($sort, $order) = explode('-', $sortOrderSelected);
            $query->orderBy($sort, $order);
        }

        $billingLogs = $query->paginate();
        $customerGroups = CustomerGroup::with('customers')->get();

        $billingSums = (function () {
            $query = Operation::with('customer')->select(
                DB::raw('sum(total_billing_weight) as total_billing_weight'),
                DB::raw('sum(total_billing_payment) as total_billing_payment'),
                'customer_id'
            )
                ->whereNotNull('customer_id')
                ->groupBy('customer_id');

            $dateStartAt = request()->get('date_start_at');
            $dateEndAt = request()->get('date_end_at');
            if ($dateStartAt && $dateEndAt) {
                $query->whereBetween('created_at', [$dateStartAt . ' 00:00:00', $dateEndAt . ' 23:59:59']);
            }
            return $query->get();
        })();

        return view(
            'supervisor.customers.billing-logs',
            [
                'billingLogs' => $billingLogs,
                'billingSums' => $billingSums,
                'customerGroups' => $customerGroups,
                'customerIdSelected' => $customerIdSelected,
                'dateStartAt' => $dateStartAt,
                'dateEndAt' => $dateEndAt,
                'sortOrders' => $sortOrders,
                'sortOrderSelected' => $sortOrderSelected,
            ]
        )
            ->with('i', (request()->input('page', 1) - 1) * $billingLogs->perPage());
    }
}
