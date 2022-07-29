<?php

namespace App\Http\Controllers;

use App\Models\EmployeeOperationLog;
use Illuminate\Http\Request;

/**
 * Class EmployeeOperationLogController
 * @package App\Http\Controllers
 */
class EmployeeOperationLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employeeOperationLogs = EmployeeOperationLog::paginate();

        return view('employee-operation-log.index', compact('employeeOperationLogs'))
            ->with('i', (request()->input('page', 1) - 1) * $employeeOperationLogs->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employeeOperationLog = new EmployeeOperationLog();
        return view('employee-operation-log.create', compact('employeeOperationLog'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(EmployeeOperationLog::$rules);

        $employeeOperationLog = EmployeeOperationLog::create($request->all());

        return redirect()->route('employee-operation-logs.index')
            ->with('success', 'EmployeeOperationLog created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $employeeOperationLog = EmployeeOperationLog::find($id);

        return view('employee-operation-log.show', compact('employeeOperationLog'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employeeOperationLog = EmployeeOperationLog::find($id);

        return view('employee-operation-log.edit', compact('employeeOperationLog'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  EmployeeOperationLog $employeeOperationLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeOperationLog $employeeOperationLog)
    {
        request()->validate(EmployeeOperationLog::$rules);

        $employeeOperationLog->update($request->all());

        return redirect()->route('employee-operation-logs.index')
            ->with('success', 'EmployeeOperationLog updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $employeeOperationLog = EmployeeOperationLog::find($id)->delete();

        return redirect()->route('employee-operation-logs.index')
            ->with('success', 'EmployeeOperationLog deleted successfully');
    }
}
