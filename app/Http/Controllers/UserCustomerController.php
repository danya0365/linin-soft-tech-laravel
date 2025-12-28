<?php

namespace App\Http\Controllers;

class UserCustomerController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('user-customer.index');
    }
}