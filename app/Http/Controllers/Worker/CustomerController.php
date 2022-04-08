<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
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
        $query = OperationLinenProduct::with('operationCustomer')->select(
            DB::raw('sum(wet_weight) as total_wet_weight'),
            DB::raw('(SELECT sum(collect_weight) as total_collect_weight
                        FROM operations_linen_products as op 
                        JOIN operations as o ON o.id = op.operation_id 
                        JOIN customers as c ON c.id = o.customer_id 
                        WHERE o.customer_id = operations.customer_id AND op.linen_case = \'edit\' ) as total_edit_collect_weight'),
            DB::raw('sum(collect_weight) as total_collect_weight'),
            DB::raw('sum(operations.total_billing_weight) as total_billing_weight'),
            'operations.customer_id',
        )
            ->join('operations', 'operations.id', '=', 'operation_id')
            ->join('customers', 'customers.id', '=', 'operations.customer_id')
            ->groupBy('operations.customer_id');

        $operations = $query->paginate(5);
        return view('worker.customers.get-operations-group-by-customer', ['operations' => $operations])
            ->with('i', (request()->input('page', 1) - 1) * $operations->perPage());
    }
}
