<?php

namespace App\Http\Controllers\Worker\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IronController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return ['hello world'];
    }
}
