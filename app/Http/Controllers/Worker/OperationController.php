<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Enums\OperationStatus;
use App\Enums\OperationType;
use App\Models\LinenType;
use App\Models\Operation;
use App\Models\OperationLinenCase;
use App\Models\OperationLinenProduct;
use Illuminate\Support\Facades\DB;

class OperationController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('worker.operations.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getOperationsInProgress()
    {
        $sortOrders = [
            ['var' => 'id-desc', 'name' => 'ใหม่ที่สุด - Newest'],
            ['var' => 'id-asc', 'name' => 'เก่าที่สุด - Oldest'],
        ];

        $sortOrderSelected = request()->get('sort_order', 'id-desc');
        $operationTypeSelected = request()->get('operation_type');
        $linenTypeSelected = request()->get('linenType');

        $query = OperationLinenProduct::with(['operation' => function ($query) {
            $query->with('employee')->with('customer');
        }])->with('linenProduct');

        $query->where(function ($query) {
            $query->whereHas('operation', function ($query) {
                $query->where('status', OperationStatus::InProgress());
            });
        });

        if ($linenTypeSelected || $operationTypeSelected) {
            $query->where(function ($query) use ($linenTypeSelected, $operationTypeSelected) {
                $query->whereHas('linenProduct', function ($query) use ($linenTypeSelected) {
                    if ($linenTypeSelected) {
                        $query->where('linen_type_id', $linenTypeSelected);
                    }
                });
                $query->whereHas('operation', function ($query) use ($operationTypeSelected) {
                    if ($operationTypeSelected) {
                        $query->where('operation_type', $operationTypeSelected);
                    }
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

        $operationTypes = OperationType::asSelectArray();

        return view(
            'worker.operations.get-operations-in-progress',
            [
                'operations' => $operations,
                'linenTypes' => $linenTypes,
                'linenTypeSelected' => $linenTypeSelected,
                'operationTypes' => $operationTypes,
                'operationTypeSelected' => $operationTypeSelected,
                'dateStartAt' => $dateStartAt,
                'dateEndAt' => $dateEndAt,
                'sortOrders' => $sortOrders,
                'sortOrderSelected' => $sortOrderSelected
            ]
        )
            ->with('i', (request()->input('page', 1) - 1) * $operations->perPage());
    }

    public function checkOutOperationInProgress($operationId)
    {
        $operation = Operation::find($operationId);
        if (!$operation) {
            return null;
        }
        switch ($operation->operation_type) {
            case OperationType::Wash()->value:
                return redirect(route('worker.operation.wash.employee-summary', ['operationId' => $operation->id]));

            case OperationType::Dry()->value:
                return redirect(route('worker.operation.dry.employee-summary', ['operationId' => $operation->id]));

            case OperationType::Iron()->value:
                return redirect(route('worker.operation.iron.employee-summary', ['operationId' => $operation->id]));

            case OperationType::Packing()->value:
                return redirect(route('worker.operation.packing.employee-summary', ['operationId' => $operation->id]));

            case OperationType::Collect()->value:
                return redirect(route('worker.operation.collect.employee-summary', ['operationId' => $operation->id]));

            case OperationType::Deliver()->value:
                return redirect(route('worker.operation.deliver.employee-summary', ['operationId' => $operation->id]));
            default:
                return null;
        }
        return view('worker.operations.index');
    }
}
