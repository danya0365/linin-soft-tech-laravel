<?php

namespace App\Http\Controllers;

use App\Models\OperationLog;
use Illuminate\Http\Request;

/**
 * Class OperationLogController
 * @package App\Http\Controllers
 */
class OperationLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $operationLogs = OperationLog::paginate();

        return view('operation-log.index', compact('operationLogs'))
            ->with('i', (request()->input('page', 1) - 1) * $operationLogs->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $operationLog = new OperationLog();
        return view('operation-log.create', compact('operationLog'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(OperationLog::$rules);

        $operationLog = OperationLog::create($request->all());

        return redirect()->route('operation-logs.index')
            ->with('success', 'OperationLog created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $operationLog = OperationLog::find($id);

        return view('operation-log.show', compact('operationLog'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $operationLog = OperationLog::find($id);

        return view('operation-log.edit', compact('operationLog'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  OperationLog $operationLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OperationLog $operationLog)
    {
        request()->validate(OperationLog::$rules);

        $operationLog->update($request->all());

        return redirect()->route('operation-logs.index')
            ->with('success', 'OperationLog updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $operationLog = OperationLog::find($id)->delete();

        return redirect()->route('operation-logs.index')
            ->with('success', 'OperationLog deleted successfully');
    }
}
