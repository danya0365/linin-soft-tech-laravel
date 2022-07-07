<?php

use App\Models\EnergyResourceLog;
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

Route::group(['middleware' => ['manager']], function () {
    Route::get('sales-range-days-chart', [App\Http\Controllers\Manager\ReportController::class, 'getSalesRangeDaysChart'])->name('api.sales-range-days-chart');
    Route::get('energy-range-days-chart', [App\Http\Controllers\Manager\ReportController::class, 'getEnergyRangeDaysChart'])->name('api.energy-range-days-chart');
    Route::get('income-range-days-chart', [App\Http\Controllers\Manager\ReportController::class, 'getIncomeRangeDaysChart'])->name('api.income-range-days-chart');
});

Route::group(['middleware' => ['supervisor', 'manager']], function () {
    Route::get('expense-range-days-chart', [App\Http\Controllers\Manager\ReportController::class, 'getExpenseRangeDaysChart'])->name('api.expense-range-days-chart');
});

Route::get('/test-2', function () {

    $startDateString = ''; //'2022-06-22';
    $endDateString = '2022-07-03';

    $endDate = $endDateString ? \Carbon\Carbon::parse($endDateString) : \Carbon\Carbon::now();
    $startDate = $startDateString ? \Carbon\Carbon::parse($startDateString) : \Carbon\Carbon::parse($endDate->format('Y-m-d'))->subDays(7);

    $dates = [];
    $period = new \DatePeriod(
        new \DateTime($startDate->format('Y-m-d')),
        new \DateInterval('P1D'),
        new \DateTime($endDate->format('Y-m-d'))
    );

    foreach ($period as $key => $value) {
        $dates[] = $value;
    }
    return $dates;
});
