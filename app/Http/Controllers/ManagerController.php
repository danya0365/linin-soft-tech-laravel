<?php

namespace App\Http\Controllers;

class ManagerController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('manager.index');
    }
}
