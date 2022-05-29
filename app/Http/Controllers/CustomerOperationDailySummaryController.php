<?php

namespace App\Http\Controllers;

use App\Models\CustomerOperationDailySummary;
use Illuminate\Http\Request;

/**
 * Class CustomerOperationDailySummaryController
 * @package App\Http\Controllers
 */
class CustomerOperationDailySummaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customerOperationDailySummaries = CustomerOperationDailySummary::paginate();

        return view('customer-operation-daily-summary.index', compact('customerOperationDailySummaries'))
            ->with('i', (request()->input('page', 1) - 1) * $customerOperationDailySummaries->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customerOperationDailySummary = new CustomerOperationDailySummary();
        return view('customer-operation-daily-summary.create', compact('customerOperationDailySummary'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(CustomerOperationDailySummary::$rules);

        $customerOperationDailySummary = CustomerOperationDailySummary::create($request->all());

        return redirect()->route('customer-operation-daily-summaries.index')
            ->with('success', 'CustomerOperationDailySummary created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customerOperationDailySummary = CustomerOperationDailySummary::find($id);

        return view('customer-operation-daily-summary.show', compact('customerOperationDailySummary'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customerOperationDailySummary = CustomerOperationDailySummary::find($id);

        return view('customer-operation-daily-summary.edit', compact('customerOperationDailySummary'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  CustomerOperationDailySummary $customerOperationDailySummary
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerOperationDailySummary $customerOperationDailySummary)
    {
        request()->validate(CustomerOperationDailySummary::$rules);

        $customerOperationDailySummary->update($request->all());

        return redirect()->route('customer-operation-daily-summaries.index')
            ->with('success', 'CustomerOperationDailySummary updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $customerOperationDailySummary = CustomerOperationDailySummary::find($id)->delete();

        return redirect()->route('customer-operation-daily-summaries.index')
            ->with('success', 'CustomerOperationDailySummary deleted successfully');
    }
}
