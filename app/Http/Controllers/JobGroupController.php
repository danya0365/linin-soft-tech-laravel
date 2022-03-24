<?php

namespace App\Http\Controllers;

use App\Models\JobGroup;
use Illuminate\Http\Request;

/**
 * Class JobGroupController
 * @package App\Http\Controllers
 */
class JobGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jobGroups = JobGroup::paginate();

        return view('job-group.index', compact('jobGroups'))
            ->with('i', (request()->input('page', 1) - 1) * $jobGroups->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jobGroup = new JobGroup();
        return view('job-group.create', compact('jobGroup'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(JobGroup::$rules);

        $jobGroup = JobGroup::create($request->all());

        return redirect()->route('job-groups.index')
            ->with('success', 'JobGroup created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $jobGroup = JobGroup::find($id);

        return view('job-group.show', compact('jobGroup'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $jobGroup = JobGroup::find($id);

        return view('job-group.edit', compact('jobGroup'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  JobGroup $jobGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, JobGroup $jobGroup)
    {
        request()->validate(JobGroup::$rules);

        $jobGroup->update($request->all());

        return redirect()->route('job-groups.index')
            ->with('success', 'JobGroup updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $jobGroup = JobGroup::find($id)->delete();

        return redirect()->route('job-groups.index')
            ->with('success', 'JobGroup deleted successfully');
    }
}
