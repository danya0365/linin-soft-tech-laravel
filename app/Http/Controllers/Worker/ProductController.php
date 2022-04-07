<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Operation;
use App\Models\OperationLinenCase;

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
        $linenCase = OperationLinenCase::getByVar($linenCaseVarName);
        $operations = Operation::paginate(5);
        return view('worker.products.get-operations-by-linen-case', ['operations' => $operations, 'linenCase' => $linenCase])
            ->with('i', (request()->input('page', 1) - 1) * $operations->perPage());
    }
}
