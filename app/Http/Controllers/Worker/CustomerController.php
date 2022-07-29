<?php

namespace App\Http\Controllers\Worker;

use App\Enums\IncomeType;
use App\Enums\OperationStatus;
use App\Enums\OperationType;
use App\Http\Controllers\Controller;
use App\Managers\IncomeManager;
use App\Managers\OperationManager;
use App\Models\Customer;
use App\Models\CustomerOperationDailySummary;
use App\Models\Income;
use App\Models\LinenType;
use App\Models\Operation;
use App\Models\OperationLinenCase;
use App\Models\OperationLinenProduct;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        return redirect(route('worker.customer.operation-summary'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getOperationSummary()
    {
        $sortOrders = [
            ['var' => 'total_wet_weight-desc', 'name' => 'ผ้าเปียกเยอะที่สุด'],
            ['var' => 'total_wet_weight-asc', 'name' => 'ผ้าเปียกน้อยที่สุด'],
        ];
        $sortOrderSelected = request()->get('sort_order', $sortOrders[0]['var']);

        $query = CustomerOperationDailySummary::with('customer')->select(
            DB::raw('sum(total_wet_weight) as total_wet_weight'),
            DB::raw('sum(total_edit_collect_weight) as total_edit_collect_weight'),
            DB::raw('sum(total_collect_weight) as total_collect_weight'),
            DB::raw('sum(total_billing_weight) as total_billing_weight'),
            'customer_id'
        );
        $query->groupBy('customer_id');

        $dateStartAt = request()->get('date_start_at');
        $dateEndAt = request()->get('date_end_at');
        if ($dateStartAt && $dateEndAt) {
            $query->whereBetween('operation_date', [$dateStartAt, $dateEndAt]);
        }
        if ($sortOrderSelected) {
            list($sort, $order) = explode('-', $sortOrderSelected);
            $query->orderBy($sort, $order);
        }

        $operations = $query->paginate();

        $summary = (function () {
            $query = CustomerOperationDailySummary::select(
                DB::raw('sum(total_wet_weight) as total_wet_weight'),
                DB::raw('sum(total_edit_collect_weight) as total_edit_collect_weight'),
                DB::raw('sum(total_collect_weight) as total_collect_weight'),
                DB::raw('sum(total_billing_weight) as total_billing_weight')
            );
            $dateStartAt = request()->get('date_start_at');
            $dateEndAt = request()->get('date_end_at');
            if ($dateStartAt && $dateEndAt) {
                $query->whereBetween('operation_date', [$dateStartAt, $dateEndAt]);
            }
            return $query->first();
        })();

        return view(
            'worker.customers.operation-summary',
            [
                'operations' => $operations,
                'dateStartAt' => $dateStartAt,
                'dateEndAt' => $dateEndAt,
                'sortOrders' => $sortOrders,
                'sortOrderSelected' => $sortOrderSelected,
                'summary' => $summary
            ]
        )
            ->with('i', (request()->input('page', 1) - 1) * $operations->perPage());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getOperationsGroupByCustomer()
    {
        $sortOrders = [
            ['var' => 'total_wet_weight-desc', 'name' => 'ผ้าเปียกเยอะที่สุด'],
            ['var' => 'total_wet_weight-asc', 'name' => 'ผ้าเปียกน้อยที่สุด'],
        ];
        $sortOrderSelected = request()->get('sort_order', $sortOrders[0]['var']);

        $query = OperationLinenProduct::with('operationCustomer')->select(
            DB::raw('sum(wet_weight) as total_wet_weight'),
            DB::raw('(SELECT sum(collect_weight) as total_collect_weight
                        FROM operations_linen_products as op 
                        JOIN operations as o ON o.id = op.operation_id 
                        JOIN customers as c ON c.id = o.customer_id 
                        WHERE o.customer_id = operations.customer_id AND op.linen_case = \'edit\' ) as total_edit_collect_weight'),
            DB::raw('sum(collect_weight) as total_collect_weight'),
            DB::raw('sum(operations.total_billing_weight) as total_billing_weight'),
            'operations.customer_id'
        )
            ->join('operations', 'operations.id', '=', 'operation_id')
            ->join('customers', 'customers.id', '=', 'operations.customer_id')
            ->groupBy('operations.customer_id');

        $query->where(function ($query) {
            $query->whereHas('operation', function ($query) {
                $query->where('status', OperationStatus::Close());
            });
        });

        $dateStartAt = request()->get('date_start_at');
        $dateEndAt = request()->get('date_end_at');
        if ($dateStartAt && $dateEndAt) {
            $query->whereBetween('operations_linen_products.created_at', [$dateStartAt . ' 00:00:00', $dateEndAt . ' 23:59:59']);
        }
        if ($sortOrderSelected) {
            list($sort, $order) = explode('-', $sortOrderSelected);
            $query->orderBy($sort, $order);
        }

        $operations = $query->paginate();
        return view(
            'worker.customers.get-operations-group-by-customer',
            [
                'operations' => $operations,
                'dateStartAt' => $dateStartAt,
                'dateEndAt' => $dateEndAt,
                'sortOrders' => $sortOrders,
                'sortOrderSelected' => $sortOrderSelected
            ]
        )
            ->with('i', (request()->input('page', 1) - 1) * $operations->perPage());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getOperationsByCustomer($customerId)
    {
        $sortOrders = [
            ['var' => 'id-desc', 'name' => 'ใหม่ที่สุด - Newest'],
            ['var' => 'id-asc', 'name' => 'เก่าที่สุด - Oldest'],
        ];
        $sortOrderSelected = request()->get('sort_order', 'id-desc');
        $operationTypeSelected = request()->get('operation_type');
        $linenProductSelected = request()->get('linen_product_id');
        $linenCaseSelected = request()->get('linen_case');

        $query = OperationLinenProduct::with(['operation' => function ($query) {
            $query->with('employee')->with('customer');
        }])->with('linenProduct');

        $query->whereNotNull('linen_product_id');

        $query->where(function ($query) use ($customerId, $operationTypeSelected, $linenProductSelected, $linenCaseSelected) {
            $query->whereHas('operation', function ($query) use ($customerId, $operationTypeSelected) {
                $query->where('customer_id', $customerId);
                if ($operationTypeSelected) {
                    $query->where('operation_type', $operationTypeSelected);
                }
            });
            if ($linenProductSelected) {
                $query->where('linen_product_id', $linenProductSelected);
            }
            if ($linenCaseSelected) {
                $query->where('linen_case', $linenCaseSelected);
            }
        });

        $dateStartAt = request()->get('date_start_at');
        $dateEndAt = request()->get('date_end_at');
        if ($dateStartAt && $dateEndAt) {
            $query->whereBetween('created_at', [$dateStartAt . ' 00:00:00', $dateEndAt . ' 23:59:59']);
        }
        if ($sortOrderSelected) {
            list($sort, $order) = explode('-', $sortOrderSelected);
            $query->orderBy($sort, $order);
        }

        $operations = $query->paginate();
        $customer = Customer::find($customerId);
        $linenTypes = LinenType::with('linenProducts')->get();

        $operationTypes = OperationType::asSelectArray();
        $linenCases = OperationLinenCase::$list;

        return view(
            'worker.customers.get-operations-by-customer',
            [
                'operations' => $operations,
                'customer' => $customer->toArray(),
                'linenTypes' => $linenTypes->toArray(),
                'dateStartAt' => $dateStartAt,
                'dateEndAt' => $dateEndAt,
                'sortOrders' => $sortOrders,
                'sortOrderSelected' => $sortOrderSelected,
                'operationTypes' => $operationTypes,
                'operationTypeSelected' => $operationTypeSelected,
                'linenProductSelected' => $linenProductSelected,
                'linenCases' => $linenCases,
                'linenCaseSelected' => $linenCaseSelected,
            ]
        )
            ->with('i', (request()->input('page', 1) - 1) * $operations->perPage());
    }

    public function getNewBilling($customerId)
    {
        $customer = Customer::find($customerId);
        return view('worker.customers.new-billing', ['customer' => $customer]);
    }

    public function submitBilling($customerId)
    {
        request()->validate(['total_billing_weight' => 'required', 'total_billing_payment' => 'required', 'billing_payment_date' => 'required']);

        $operation = new Operation();
        $operation->operation_type = OperationType::Payment();
        $operation->status = OperationStatus::Close();
        $operation->customer_id = $customerId;
        $operation->total_billing_weight = request()->get('total_billing_weight');
        $operation->total_billing_payment = request()->get('total_billing_payment');
        $operation->billing_payment_date = request()->get('billing_payment_date');
        $operation->save();

        OperationManager::createCustomerOperationDailySummary($operation);
        IncomeManager::create(IncomeType::CustomerBilling(), $operation, $operation->total_billing_payment, $operation->billing_payment_date);
        return redirect(route('worker.customer.operation-summary'));
    }
}
