<?php

namespace App\Http\Controllers;

use App\Models\DepartmentDailyCostLog;
use Illuminate\Http\Request;

/**
 * Class DepartmentDailyCostLogController
 * @package App\Http\Controllers
 */
class DepartmentDailyCostLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departmentDailyCostLogs = DepartmentDailyCostLog::paginate();

        return view('department-daily-cost-log.index', compact('departmentDailyCostLogs'))
            ->with('i', (request()->input('page', 1) - 1) * $departmentDailyCostLogs->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $departmentDailyCostLog = new DepartmentDailyCostLog();
        return view('department-daily-cost-log.create', compact('departmentDailyCostLog'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(DepartmentDailyCostLog::$rules);

        $departmentDailyCostLog = DepartmentDailyCostLog::create($request->all());

        return redirect()->route('department-daily-cost-logs.index')
            ->with('success', 'DepartmentDailyCostLog created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $departmentDailyCostLog = DepartmentDailyCostLog::find($id);

        return view('department-daily-cost-log.show', compact('departmentDailyCostLog'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $departmentDailyCostLog = DepartmentDailyCostLog::find($id);

        return view('department-daily-cost-log.edit', compact('departmentDailyCostLog'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  DepartmentDailyCostLog $departmentDailyCostLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DepartmentDailyCostLog $departmentDailyCostLog)
    {
        request()->validate(DepartmentDailyCostLog::$rules);

        $departmentDailyCostLog->update($request->all());

        return redirect()->route('department-daily-cost-logs.index')
            ->with('success', 'DepartmentDailyCostLog updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $departmentDailyCostLog = DepartmentDailyCostLog::find($id)->delete();

        return redirect()->route('department-daily-cost-logs.index')
            ->with('success', 'DepartmentDailyCostLog deleted successfully');
    }
}
