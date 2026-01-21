<?php

namespace App\Http\Controllers\UserCustomer;

use App\Enums\OperationStatus;
use App\Enums\OperationType;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerOperationDailySummary;
use App\Models\LinenType;
use App\Models\OperationLinenCase;
use App\Models\OperationLinenProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    private function getAuthCustomer()
    {
        $customerAccount = Auth::user()->customer_account;
        return Customer::find($customerAccount);
    }

    public function index()
    {
        return redirect(route('user-customer.customer.operation-summary'));
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

        $customer = $this->getAuthCustomer();
        if (!$customer) {
            abort(404);
            exit;
        }

        $query = CustomerOperationDailySummary::with('customer')->select(
            DB::raw('sum(total_wet_weight) as total_wet_weight'),
            DB::raw('sum(total_edit_collect_weight) as total_edit_collect_weight'),
            DB::raw('sum(total_edit_weight) as total_edit_weight'),
            DB::raw('sum(total_collect_weight) as total_collect_weight'),
            DB::raw('sum(total_billing_weight) as total_billing_weight'),
            'customer_id'
        );
        $query->groupBy('customer_id');
        $query->where('customer_id', $customer->id);

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
                DB::raw('sum(total_edit_weight) as total_edit_weight'),
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
            'user-customer.customers.operation-summary',
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
        $customer = $this->getAuthCustomer();
        if (!$customer || $customer->id != $customerId) {
            abort(404);
            exit;
        }

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
            'user-customer.customers.get-operations-by-customer',
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
}