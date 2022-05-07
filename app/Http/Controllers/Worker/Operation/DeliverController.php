<?php

namespace App\Http\Controllers\Worker\Operation;

use App\Http\Controllers\Controller;

class DeliverController extends Controller
{
    public function index()
    {
        return redirect(route('worker.operation.deliver.select-employee'));
    }
}