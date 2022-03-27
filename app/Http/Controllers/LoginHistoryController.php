<?php

namespace App\Http\Controllers;

use App\Models\LoginHistory;
use Illuminate\Http\Request;

/**
 * Class LoginHistoryController
 * @package App\Http\Controllers
 */
class LoginHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $loginHistories = LoginHistory::paginate();

        return view('login-history.index', compact('loginHistories'))
            ->with('i', (request()->input('page', 1) - 1) * $loginHistories->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $loginHistory = new LoginHistory();
        return view('login-history.create', compact('loginHistory'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(LoginHistory::$rules);

        $loginHistory = LoginHistory::create($request->all());

        return redirect()->route('login-histories.index')
            ->with('success', 'LoginHistory created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $loginHistory = LoginHistory::find($id);

        return view('login-history.show', compact('loginHistory'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $loginHistory = LoginHistory::find($id);

        return view('login-history.edit', compact('loginHistory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  LoginHistory $loginHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LoginHistory $loginHistory)
    {
        request()->validate(LoginHistory::$rules);

        $loginHistory->update($request->all());

        return redirect()->route('login-histories.index')
            ->with('success', 'LoginHistory updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $loginHistory = LoginHistory::find($id)->delete();

        return redirect()->route('login-histories.index')
            ->with('success', 'LoginHistory deleted successfully');
    }
}
