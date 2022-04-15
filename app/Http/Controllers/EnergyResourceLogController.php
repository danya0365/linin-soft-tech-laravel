<?php

namespace App\Http\Controllers;

use App\Models\EnergyResourceLog;
use Illuminate\Http\Request;

/**
 * Class EnergyResourceLogController
 * @package App\Http\Controllers
 */
class EnergyResourceLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $energyResourceLogs = EnergyResourceLog::paginate();

        return view('energy-resource-log.index', compact('energyResourceLogs'))
            ->with('i', (request()->input('page', 1) - 1) * $energyResourceLogs->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $energyResourceLog = new EnergyResourceLog();
        return view('energy-resource-log.create', compact('energyResourceLog'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(EnergyResourceLog::$rules);

        $energyResourceLog = EnergyResourceLog::create($request->all());

        return redirect()->route('energy-resource-logs.index')
            ->with('success', 'EnergyResourceLog created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $energyResourceLog = EnergyResourceLog::find($id);

        return view('energy-resource-log.show', compact('energyResourceLog'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $energyResourceLog = EnergyResourceLog::find($id);

        return view('energy-resource-log.edit', compact('energyResourceLog'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  EnergyResourceLog $energyResourceLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EnergyResourceLog $energyResourceLog)
    {
        request()->validate(EnergyResourceLog::$rules);

        $energyResourceLog->update($request->all());

        return redirect()->route('energy-resource-logs.index')
            ->with('success', 'EnergyResourceLog updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $energyResourceLog = EnergyResourceLog::find($id)->delete();

        return redirect()->route('energy-resource-logs.index')
            ->with('success', 'EnergyResourceLog deleted successfully');
    }
}
