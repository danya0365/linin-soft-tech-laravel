<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/query', function () {
    $incomeRows = DB::table('incomes')->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_year"), DB::raw('SUM(amount) as total_amount'))->groupBy('month_year')->get();
    $result = [];
    $currentYear = date('Y');
    for ($m = 1; $m <= 12; $m++) {
        $time = mktime(0, 0, 0, $m, 1, $currentYear);
        $monthYear = date('Y-m', $time);
        $result[$monthYear] = ['month_year' => $monthYear, 'total_amount' => 0, 'month_year_title' => date('F y', $time)];
    }

    foreach ($incomeRows as $key => $incomeRow) {
        $monthYear = $incomeRow->month_year;
        $row = $result[$monthYear];
        $row['total_amount'] = $incomeRow->total_amount;
        $result[$monthYear] = $row;
    }
    return $result;
});

Route::get('/test', [App\Http\Controllers\Supervisor\ReportController::class, 'salesYearSummary']);
