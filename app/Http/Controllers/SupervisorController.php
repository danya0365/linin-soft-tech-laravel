<?php

namespace App\Http\Controllers;

class SupervisorController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('supervisor.index');
    }
}
