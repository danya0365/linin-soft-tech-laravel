<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\Request;

/**
 * Class IncomeController
 * @package App\Http\Controllers
 */
class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $incomes = Income::paginate();

        return view('income.index', compact('incomes'))
            ->with('i', (request()->input('page', 1) - 1) * $incomes->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $income = new Income();
        return view('income.create', compact('income'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(Income::$rules);

        $income = Income::create($request->all());

        return redirect()->route('incomes.index')
            ->with('success', 'Income created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $income = Income::find($id);

        return view('income.show', compact('income'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $income = Income::find($id);

        return view('income.edit', compact('income'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Income $income
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Income $income)
    {
        request()->validate(Income::$rules);

        $income->update($request->all());

        return redirect()->route('incomes.index')
            ->with('success', 'Income updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $income = Income::find($id)->delete();

        return redirect()->route('incomes.index')
            ->with('success', 'Income deleted successfully');
    }
}
