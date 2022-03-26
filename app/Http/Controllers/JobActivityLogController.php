<?php

namespace App\Http\Controllers;

use App\Models\JobActivityLog;
use Illuminate\Http\Request;

/**
 * Class JobActivityLogController
 * @package App\Http\Controllers
 */
class JobActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jobActivityLogs = JobActivityLog::paginate();

        return view('job-activity-log.index', compact('jobActivityLogs'))
            ->with('i', (request()->input('page', 1) - 1) * $jobActivityLogs->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jobActivityLog = new JobActivityLog();
        return view('job-activity-log.create', compact('jobActivityLog'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(JobActivityLog::$rules);

        $jobActivityLog = JobActivityLog::create($request->all());

        return redirect()->route('job-activity-logs.index')
            ->with('success', 'JobActivityLog created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $jobActivityLog = JobActivityLog::find($id);

        return view('job-activity-log.show', compact('jobActivityLog'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $jobActivityLog = JobActivityLog::find($id);

        return view('job-activity-log.edit', compact('jobActivityLog'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  JobActivityLog $jobActivityLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, JobActivityLog $jobActivityLog)
    {
        request()->validate(JobActivityLog::$rules);

        $jobActivityLog->update($request->all());

        return redirect()->route('job-activity-logs.index')
            ->with('success', 'JobActivityLog updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $jobActivityLog = JobActivityLog::find($id)->delete();

        return redirect()->route('job-activity-logs.index')
            ->with('success', 'JobActivityLog deleted successfully');
    }
}
