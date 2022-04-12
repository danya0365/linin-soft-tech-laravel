<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\LinenType;
use App\Models\Operation;
use App\Models\OperationLinenCase;
use App\Models\OperationLinenProduct;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        return redirect(route('worker.product.select-linen-case'));
    }

    public function selectLinenCase()
    {
        $operationLinenCases = OperationLinenCase::$list;
        return view('worker.products.select-linen-case', ['operationLinenCases' => $operationLinenCases]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getOperationsByLinenCase($linenCaseVarName)
    {
        $sortOrders = [
            ['var' => 'id-desc', 'name' => 'ใหม่ที่สุด - Newest'],
            ['var' => 'id-asc', 'name' => 'เก่าที่สุด - Oldest'],
        ];

        $sortOrderSelected = request()->get('sort_order', 'id-desc');

        $linenTypeSelected = request()->get('linenType');
        $linenCase = OperationLinenCase::getByVar($linenCaseVarName);
        $query = OperationLinenProduct::with(['operation' => function ($query) {
            $query->with('employee')->with('customer');
        }])->with('linenProduct')->where('linen_case', $linenCase['var']);

        if ($linenTypeSelected) {
            $query->where(function ($query) use ($linenTypeSelected) {
                $query->whereHas('linenProduct', function ($query) use ($linenTypeSelected) {
                    $query->where('linen_type_id', $linenTypeSelected);
                });
            });
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

        $operations = $query->paginate();
        $linenTypes = LinenType::get();

        $linenProductSummaries = (function ($linenCase) {
            $query = OperationLinenProduct::with('linenProduct')->select(
                DB::raw('sum(wet_weight) as total_wet_weight'),
                DB::raw('sum(dry_weight) as total_dry_weight'),
                DB::raw('sum(iron_piece) as total_iron_piece'),
                DB::raw('sum(packing_piece) as total_packing_piece'),
                DB::raw('sum(collect_weight) as total_collect_weight'),
                'linen_product_id'
            )
                ->where('linen_case', $linenCase['var'])
                ->groupBy('linen_product_id');

            $dateStartAt = request()->get('date_start_at');
            $dateEndAt = request()->get('date_end_at');
            if ($dateStartAt && $dateEndAt) {
                $query->whereBetween('created_at', [$dateStartAt . ' 00:00:00', $dateEndAt . ' 23:59:59']);
            }
            return $query->get();
        })($linenCase);

        return view(
            'worker.products.get-operations-by-linen-case',
            [
                'operations' => $operations,
                'linenCase' => $linenCase,
                'linenTypes' => $linenTypes,
                'linenTypeSelected' => $linenTypeSelected,
                'dateStartAt' => $dateStartAt,
                'dateEndAt' => $dateEndAt,
                'sortOrders' => $sortOrders,
                'sortOrderSelected' => $sortOrderSelected,
                'linenProductSummaries' => $linenProductSummaries
            ]
        )
            ->with('i', (request()->input('page', 1) - 1) * $operations->perPage());
    }
}
