<?php

namespace App\Http\Controllers;

use App\Models\InventoryGroup;
use Illuminate\Http\Request;

/**
 * Class InventoryGroupController
 * @package App\Http\Controllers
 */
class InventoryGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $inventoryGroups = InventoryGroup::paginate();

        return view('inventory-group.index', compact('inventoryGroups'))
            ->with('i', (request()->input('page', 1) - 1) * $inventoryGroups->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $inventoryGroup = new InventoryGroup();
        return view('inventory-group.create', compact('inventoryGroup'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(InventoryGroup::$rules);

        $inventoryGroup = InventoryGroup::create($request->all());

        return redirect()->route('inventory-groups.index')
            ->with('success', 'InventoryGroup created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $inventoryGroup = InventoryGroup::find($id);

        return view('inventory-group.show', compact('inventoryGroup'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $inventoryGroup = InventoryGroup::find($id);

        return view('inventory-group.edit', compact('inventoryGroup'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  InventoryGroup $inventoryGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InventoryGroup $inventoryGroup)
    {
        request()->validate(InventoryGroup::$rules);

        $inventoryGroup->update($request->all());

        return redirect()->route('inventory-groups.index')
            ->with('success', 'InventoryGroup updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $inventoryGroup = InventoryGroup::find($id)->delete();

        return redirect()->route('inventory-groups.index')
            ->with('success', 'InventoryGroup deleted successfully');
    }
}
