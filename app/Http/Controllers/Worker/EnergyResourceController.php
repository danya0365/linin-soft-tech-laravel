<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;

class EnergyResourceController extends Controller
{
    public function index()
    {
        return view('worker.energy-resources.index');
    }
}