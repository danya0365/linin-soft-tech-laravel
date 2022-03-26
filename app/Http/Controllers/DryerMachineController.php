<?php

namespace App\Http\Controllers;

use App\Models\DryerMachine;
use Illuminate\Http\Request;

/**
 * Class DryerMachineController
 * @package App\Http\Controllers
 */
class DryerMachineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dryerMachines = DryerMachine::paginate();

        return view('dryer-machine.index', compact('dryerMachines'))
            ->with('i', (request()->input('page', 1) - 1) * $dryerMachines->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $dryerMachine = new DryerMachine();
        return view('dryer-machine.create', compact('dryerMachine'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(DryerMachine::$rules);

        $dryerMachine = DryerMachine::create($request->all());

        return redirect()->route('dryer-machines.index')
            ->with('success', 'DryerMachine created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $dryerMachine = DryerMachine::find($id);

        return view('dryer-machine.show', compact('dryerMachine'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $dryerMachine = DryerMachine::find($id);

        return view('dryer-machine.edit', compact('dryerMachine'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  DryerMachine $dryerMachine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DryerMachine $dryerMachine)
    {
        request()->validate(DryerMachine::$rules);

        $dryerMachine->update($request->all());

        return redirect()->route('dryer-machines.index')
            ->with('success', 'DryerMachine updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $dryerMachine = DryerMachine::find($id)->delete();

        return redirect()->route('dryer-machines.index')
            ->with('success', 'DryerMachine deleted successfully');
    }
}
