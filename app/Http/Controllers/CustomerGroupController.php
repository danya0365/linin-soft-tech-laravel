<?php

namespace App\Http\Controllers;

use App\Models\CustomerGroup;
use Illuminate\Http\Request;

/**
 * Class CustomerGroupController
 * @package App\Http\Controllers
 */
class CustomerGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customerGroups = CustomerGroup::paginate();

        return view('customer-group.index', compact('customerGroups'))
            ->with('i', (request()->input('page', 1) - 1) * $customerGroups->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customerGroup = new CustomerGroup();
        return view('customer-group.create', compact('customerGroup'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(CustomerGroup::$rules);

        $customerGroup = CustomerGroup::create($request->all());

        return redirect()->route('customer-groups.index')
            ->with('success', 'CustomerGroup created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customerGroup = CustomerGroup::find($id);

        return view('customer-group.show', compact('customerGroup'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customerGroup = CustomerGroup::find($id);

        return view('customer-group.edit', compact('customerGroup'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  CustomerGroup $customerGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerGroup $customerGroup)
    {
        request()->validate(CustomerGroup::$rules);

        $customerGroup->update($request->all());

        return redirect()->route('customer-groups.index')
            ->with('success', 'CustomerGroup updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $customerGroup = CustomerGroup::find($id)->delete();

        return redirect()->route('customer-groups.index')
            ->with('success', 'CustomerGroup deleted successfully');
    }
}
