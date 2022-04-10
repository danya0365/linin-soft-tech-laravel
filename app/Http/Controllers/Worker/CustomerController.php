<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LinenType;
use App\Models\Operation;
use App\Models\OperationLinenCase;
use App\Models\OperationLinenProduct;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        return redirect(route('worker.customer.get-operations-group-by-customer'));
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

        // $customerIds = [];

        // $query = OperationLinenProduct::query()
        //     ->join('operations o', 'o.id', '=', 'operation_id');

        // $query->whereIn('o.customer_id', $customerIds);

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
            ['var' => 'id-desc', 'name' => 'ใหม่ที่สุด'],
            ['var' => 'id-asc', 'name' => 'เก่าที่สุด'],
        ];
        $sortOrderSelected = request()->get('sort_order', 'id-desc');

        $query = OperationLinenProduct::with(['operation' => function ($query) {
            $query->with('employee')->with('customer');
        }])->with('linenProduct');

        $query->where(function ($query) use ($customerId) {
            $query->whereHas('operation', function ($query) use ($customerId) {
                $query->where('customer_id', $customerId);
            });
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
        $linenTypes = LinenType::get();

        return view(
            'worker.customers.get-operations-by-customer',
            [
                'operations' => $operations,
                'customer' => $customer->toArray(),
                'linenTypes' => $linenTypes,
                'dateStartAt' => $dateStartAt,
                'dateEndAt' => $dateEndAt,
                'sortOrders' => $sortOrders,
                'sortOrderSelected' => $sortOrderSelected
            ]
        )
            ->with('i', (request()->input('page', 1) - 1) * $operations->perPage());
    }
}
