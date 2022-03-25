<?php

namespace App\Http\Controllers;

use App\Models\WashingMachine;
use Illuminate\Http\Request;

/**
 * Class WashingMachineController
 * @package App\Http\Controllers
 */
class WashingMachineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $washingMachines = WashingMachine::paginate();

        return view('washing-machine.index', compact('washingMachines'))
            ->with('i', (request()->input('page', 1) - 1) * $washingMachines->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $washingMachine = new WashingMachine();
        return view('washing-machine.create', compact('washingMachine'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(WashingMachine::$rules);

        $washingMachine = WashingMachine::create($request->all());

        return redirect()->route('washing-machines.index')
            ->with('success', 'WashingMachine created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $washingMachine = WashingMachine::find($id);

        return view('washing-machine.show', compact('washingMachine'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $washingMachine = WashingMachine::find($id);

        return view('washing-machine.edit', compact('washingMachine'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  WashingMachine $washingMachine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WashingMachine $washingMachine)
    {
        request()->validate(WashingMachine::$rules);

        $washingMachine->update($request->all());

        return redirect()->route('washing-machines.index')
            ->with('success', 'WashingMachine updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $washingMachine = WashingMachine::find($id)->delete();

        return redirect()->route('washing-machines.index')
            ->with('success', 'WashingMachine deleted successfully');
    }
}
