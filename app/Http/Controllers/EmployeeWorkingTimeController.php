<?php

namespace App\Http\Controllers;

use App\Models\EmployeeWorkingTime;
use Illuminate\Http\Request;

/**
 * Class EmployeeWorkingTimeController
 * @package App\Http\Controllers
 */
class EmployeeWorkingTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employeeWorkingTimes = EmployeeWorkingTime::paginate();

        return view('employee-working-time.index', compact('employeeWorkingTimes'))
            ->with('i', (request()->input('page', 1) - 1) * $employeeWorkingTimes->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employeeWorkingTime = new EmployeeWorkingTime();
        return view('employee-working-time.create', compact('employeeWorkingTime'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(EmployeeWorkingTime::$rules);

        $employeeWorkingTime = EmployeeWorkingTime::create($request->all());

        return redirect()->route('employee-working-times.index')
            ->with('success', 'EmployeeWorkingTime created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $employeeWorkingTime = EmployeeWorkingTime::find($id);

        return view('employee-working-time.show', compact('employeeWorkingTime'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employeeWorkingTime = EmployeeWorkingTime::find($id);

        return view('employee-working-time.edit', compact('employeeWorkingTime'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  EmployeeWorkingTime $employeeWorkingTime
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeWorkingTime $employeeWorkingTime)
    {
        request()->validate(EmployeeWorkingTime::$rules);

        $employeeWorkingTime->update($request->all());

        return redirect()->route('employee-working-times.index')
            ->with('success', 'EmployeeWorkingTime updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $employeeWorkingTime = EmployeeWorkingTime::find($id)->delete();

        return redirect()->route('employee-working-times.index')
            ->with('success', 'EmployeeWorkingTime deleted successfully');
    }
}
