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
            $operation->total_edit_weight = request()->get('total_edit_weight');
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
            $query->whereBetween('billing_payment_date', [$dateStartAt, $dateEndAt]);
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
                DB::raw('sum(total_edit_weight) as total_edit_weight'),
                DB::raw('sum(total_billing_payment) as total_billing_payment'),
                'customer_id'
            )
                ->whereNotNull('customer_id')
                ->groupBy('customer_id');

            $dateStartAt = request()->get('date_start_at');
            $dateEndAt = request()->get('date_end_at');
            if ($dateStartAt && $dateEndAt) {
                $query->whereBetween('billing_payment_date', [$dateStartAt, $dateEndAt]);
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

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function deleteBillingLog($id)
    {
        $billingLog = Operation::find($id);

        // เก็บข้อมูลก่อนลบ เพื่อใช้ recalculate
        $customerId = $billingLog->customer_id;
        $operationDate = $billingLog->created_at->format('Y-m-d');

        $billingLog->delete();

        IncomeManager::delete($billingLog);

        // Recalculate CustomerOperationDailySummary สำหรับวันนั้น
        if ($customerId && $operationDate) {
            OperationManager::recalculateCustomerOperationDailySummary($customerId, $operationDate);
        }

        return redirect()->route('supervisor.customer.billing-logs')
            ->with('success', 'BillingLog deleted successfully');
    }

    public function editBillingLog($id)
    {
        $operation = Operation::with('customer')->find($id);
        
        if (!$operation) {
            return redirect()->route('supervisor.customer.billing-logs')
                ->with('error', 'Billing not found');
        }

        if (request()->isMethod('post')) {
            // ตรวจสอบเฉพาะฟิลด์ที่อนุญาตให้แก้ไข
            request()->validate([
                'total_billing_weight' => 'required',
            ]);

            // ⚠️ ห้ามแก้ไข total_billing_payment และ billing_payment_date 
            // เพราะผูกกับ Income และ Daily Summary
            // ⚠️ ห้ามใส่ total_wet_weight, total_dry_weight เพราะจะซ้ำกับ non-payment operations
            $operation->total_billing_weight = request()->get('total_billing_weight');
            $operation->total_edit_weight = request()->get('total_edit_weight');
            $operation->save();

            // Recalculate CustomerOperationDailySummary
            OperationManager::createCustomerOperationDailySummary($operation);

            return redirect()->route('supervisor.customer.billing-logs')
                ->with('success', 'อัพเดตน้ำหนักเรียบร้อยแล้ว');
        }

        $customerGroups = CustomerGroup::with('customers')->get();
        return view('supervisor.customers.edit-billing', [
            'operation' => $operation,
            'customerGroups' => $customerGroups
        ]);
    }
}
