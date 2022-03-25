<?php

namespace App\Http\Controllers;

use App\Models\JobGroupActivityLog;
use Illuminate\Http\Request;

/**
 * Class JobGroupActivityLogController
 * @package App\Http\Controllers
 */
class JobGroupActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jobGroupActivityLogs = JobGroupActivityLog::paginate();

        return view('job-group-activity-log.index', compact('jobGroupActivityLogs'))
            ->with('i', (request()->input('page', 1) - 1) * $jobGroupActivityLogs->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jobGroupActivityLog = new JobGroupActivityLog();
        return view('job-group-activity-log.create', compact('jobGroupActivityLog'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(JobGroupActivityLog::$rules);

        $jobGroupActivityLog = JobGroupActivityLog::create($request->all());

        return redirect()->route('job-group-activity-logs.index')
            ->with('success', 'JobGroupActivityLog created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $jobGroupActivityLog = JobGroupActivityLog::find($id);

        return view('job-group-activity-log.show', compact('jobGroupActivityLog'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $jobGroupActivityLog = JobGroupActivityLog::find($id);

        return view('job-group-activity-log.edit', compact('jobGroupActivityLog'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  JobGroupActivityLog $jobGroupActivityLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, JobGroupActivityLog $jobGroupActivityLog)
    {
        request()->validate(JobGroupActivityLog::$rules);

        $jobGroupActivityLog->update($request->all());

        return redirect()->route('job-group-activity-logs.index')
            ->with('success', 'JobGroupActivityLog updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $jobGroupActivityLog = JobGroupActivityLog::find($id)->delete();

        return redirect()->route('job-group-activity-logs.index')
            ->with('success', 'JobGroupActivityLog deleted successfully');
    }
}
