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

// External Dashboard API (mobile / third-party) — Sanctum Bearer + abilities
Route::middleware(['auth:sanctum', 'ability:dashboard:read'])
    ->prefix('v1/dashboard')
    ->group(function () {
        Route::get('/', [App\Http\Controllers\Api\V1\DashboardController::class, 'index']);
        Route::get('/operations', [App\Http\Controllers\Api\V1\DashboardController::class, 'operations']);
        Route::get('/sales', [App\Http\Controllers\Api\V1\DashboardController::class, 'sales']);
        Route::get('/energy', [App\Http\Controllers\Api\V1\DashboardController::class, 'energy']);
        Route::get('/income', [App\Http\Controllers\Api\V1\DashboardController::class, 'income']);
        Route::get('/expense', [App\Http\Controllers\Api\V1\DashboardController::class, 'expense']);
    });

// External Resource Read API — plant master/ops tables
Route::middleware(['auth:sanctum', 'ability:resources:read'])
    ->prefix('v1')
    ->group(function () {
        Route::get('/washing-machines', [App\Http\Controllers\Api\V1\WashingMachineController::class, 'index']);
        Route::get('/washing-machines/{id}', [App\Http\Controllers\Api\V1\WashingMachineController::class, 'show'])->whereNumber('id');

        Route::get('/dryer-machines', [App\Http\Controllers\Api\V1\DryerMachineController::class, 'index']);
        Route::get('/dryer-machines/{id}', [App\Http\Controllers\Api\V1\DryerMachineController::class, 'show'])->whereNumber('id');

        Route::get('/trucks', [App\Http\Controllers\Api\V1\TruckController::class, 'index']);
        Route::get('/trucks/{id}', [App\Http\Controllers\Api\V1\TruckController::class, 'show'])->whereNumber('id');

        Route::get('/customers', [App\Http\Controllers\Api\V1\CustomerController::class, 'index']);
        Route::get('/customers/{id}', [App\Http\Controllers\Api\V1\CustomerController::class, 'show'])->whereNumber('id');

        Route::get('/customer-groups', [App\Http\Controllers\Api\V1\CustomerGroupController::class, 'index']);
        Route::get('/customer-groups/{id}', [App\Http\Controllers\Api\V1\CustomerGroupController::class, 'show'])->whereNumber('id');

        Route::get('/employees', [App\Http\Controllers\Api\V1\EmployeeController::class, 'index']);
        Route::get('/employees/{id}', [App\Http\Controllers\Api\V1\EmployeeController::class, 'show'])->whereNumber('id');

        Route::get('/departments', [App\Http\Controllers\Api\V1\DepartmentController::class, 'index']);
        Route::get('/departments/{id}', [App\Http\Controllers\Api\V1\DepartmentController::class, 'show'])->whereNumber('id');

        Route::get('/linen-types', [App\Http\Controllers\Api\V1\LinenTypeController::class, 'index']);
        Route::get('/linen-types/{id}', [App\Http\Controllers\Api\V1\LinenTypeController::class, 'show'])->whereNumber('id');

        Route::get('/linen-products', [App\Http\Controllers\Api\V1\LinenProductController::class, 'index']);
        Route::get('/linen-products/{id}', [App\Http\Controllers\Api\V1\LinenProductController::class, 'show'])->whereNumber('id');

        Route::get('/operations', [App\Http\Controllers\Api\V1\OperationController::class, 'index']);
        Route::get('/operations/{id}', [App\Http\Controllers\Api\V1\OperationController::class, 'show'])->whereNumber('id');

        Route::get('/energy-resources', [App\Http\Controllers\Api\V1\EnergyResourceController::class, 'index']);
        Route::get('/energy-resources/{id}', [App\Http\Controllers\Api\V1\EnergyResourceController::class, 'show'])->whereNumber('id');

        Route::get('/inventories', [App\Http\Controllers\Api\V1\InventoryController::class, 'index']);
        Route::get('/inventories/{id}', [App\Http\Controllers\Api\V1\InventoryController::class, 'show'])->whereNumber('id');
    });

// LINE Messaging API Webhook
// ถอด throttle:api ออก — throttle นับตาม IP แต่ LINE ยิงมาจาก IP ชุดจำกัด
// พอ OA คุยกันเยอะจะได้ 429 ซึ่ง LINE นับเป็น error และปิด webhook ให้อัตโนมัติ
// (gate จริงคือ signature verification ในตัว controller อยู่แล้ว)
Route::post('/line/webhook', [App\Http\Controllers\Api\LineChatbotController::class, 'webhook'])
    ->withoutMiddleware(['throttle:api']);

Route::group(['middleware' => ['logged-in'], 'prefix' => 'web-chat'], function () {
    Route::post('/message', [App\Http\Controllers\Api\WebChatController::class, 'message']);
    Route::post('/action', [App\Http\Controllers\Api\WebChatController::class, 'action']);
    Route::get('/welcome', [App\Http\Controllers\Api\WebChatController::class, 'welcome']);
});

// AI Chat (หน้าแชท AI แบบ jarvis) — sessions ใน DB ต่อ user + streaming tool loop
Route::group(['middleware' => ['staff'], 'prefix' => 'ai-chat'], function () {
    Route::get('/sessions', [App\Http\Controllers\Api\AiChatSessionController::class, 'index']);
    Route::post('/sessions', [App\Http\Controllers\Api\AiChatSessionController::class, 'store']);
    Route::get('/sessions/{id}', [App\Http\Controllers\Api\AiChatSessionController::class, 'show']);
    Route::patch('/sessions/{id}', [App\Http\Controllers\Api\AiChatSessionController::class, 'update']);
    Route::delete('/sessions/{id}', [App\Http\Controllers\Api\AiChatSessionController::class, 'destroy']);
    Route::get('/stats', [App\Http\Controllers\Api\AiChatSessionController::class, 'stats']);
    Route::post('/sessions/{id}/stream', [App\Http\Controllers\AiChatController::class, 'stream'])
        ->middleware('throttle:ai-chat')
        ->name('api.ai-chat.stream');
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
