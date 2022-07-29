<?php

namespace App\Http\Controllers;

use App\Models\InventoryStockLog;
use Illuminate\Http\Request;

/**
 * Class InventoryStockLogController
 * @package App\Http\Controllers
 */
class InventoryStockLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $inventoryStockLogs = InventoryStockLog::paginate();

        return view('inventory-stock-log.index', compact('inventoryStockLogs'))
            ->with('i', (request()->input('page', 1) - 1) * $inventoryStockLogs->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $inventoryStockLog = new InventoryStockLog();
        return view('inventory-stock-log.create', compact('inventoryStockLog'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(InventoryStockLog::$rules);

        $inventoryStockLog = InventoryStockLog::create($request->all());

        return redirect()->route('inventory-stock-logs.index')
            ->with('success', 'InventoryStockLog created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $inventoryStockLog = InventoryStockLog::find($id);

        return view('inventory-stock-log.show', compact('inventoryStockLog'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $inventoryStockLog = InventoryStockLog::find($id);

        return view('inventory-stock-log.edit', compact('inventoryStockLog'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  InventoryStockLog $inventoryStockLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InventoryStockLog $inventoryStockLog)
    {
        request()->validate(InventoryStockLog::$rules);

        $inventoryStockLog->update($request->all());

        return redirect()->route('inventory-stock-logs.index')
            ->with('success', 'InventoryStockLog updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $inventoryStockLog = InventoryStockLog::find($id)->delete();

        return redirect()->route('inventory-stock-logs.index')
            ->with('success', 'InventoryStockLog deleted successfully');
    }
}
