<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('home');
});

Route::get('/test', function () {
    return ['hello world'];
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::group(['prefix' => 'admin', 'middleware' => ['admin']], function () {
    Route::get('/', [App\Http\Controllers\AdminController::class, 'index'])->name('admin');
});

Route::group(['middleware' => ['admin']], function () {
    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::resource('customer-groups', App\Http\Controllers\CustomerGroupController::class);
    Route::resource('customers', App\Http\Controllers\CustomerController::class);
    Route::resource('departments', App\Http\Controllers\DepartmentController::class);
    Route::resource('employees', App\Http\Controllers\EmployeeController::class);
});

Route::group(['prefix' => 'worker', 'middleware' => ['auth']], function () {

    Route::get('/', [App\Http\Controllers\WorkerController::class, 'index'])->name('worker');

    Route::get('/product', function () {
        return ['hello world'];
    })->name('worker.product');

    Route::get('/customer', function () {
        return ['hello world'];
    })->name('worker.customer');

    Route::group(['prefix' => 'operation'], function () {
        Route::get('/', [App\Http\Controllers\Worker\OperationController::class, 'index'])->name('worker.operation');

        Route::group(['prefix' => 'pickup'], function () {
            Route::get('/employee', [App\Http\Controllers\Worker\Operation\PickupController::class, 'selectEmployee'])->name('worker.operation.pick-up.select-employee');
            Route::get('/employee/{employeeId}', [App\Http\Controllers\Worker\Operation\PickupController::class, 'setSelectEmployee'])->name('worker.operation.pick-up.set-select-employee');
            Route::get('/{jobGroupId}/customer', [App\Http\Controllers\Worker\Operation\PickupController::class, 'selectCustomer'])->name('worker.operation.pick-up.select-customer');
            Route::get('/{jobGroupId}/customer/{customerId}', [App\Http\Controllers\Worker\Operation\PickupController::class, 'setSelectCustomer'])->name('worker.operation.pick-up.set-select-customer');
            Route::get('/{jobGroupId}/submit', [App\Http\Controllers\Worker\Operation\PickupController::class, 'getSubmit'])->name('worker.operation.pick-up.submit');
            Route::post('/{jobGroupId}/submit', [App\Http\Controllers\Worker\Operation\PickupController::class, 'postSubmit'])->name('worker.operation.pick-up.submit');
        });

        Route::group(['prefix' => 'wash'], function () {
            Route::get('/employee', [App\Http\Controllers\Worker\Operation\WashController::class, 'selectEmployee'])->name('worker.operation.wash.select-employee');
            Route::get('/employee/{employeeId}', [App\Http\Controllers\Worker\Operation\WashController::class, 'setSelectEmployee'])->name('worker.operation.wash.set-select-employee');
            Route::get('/{jobId}/customer', [App\Http\Controllers\Worker\Operation\WashController::class, 'selectCustomer'])->name('worker.operation.wash.select-customer');
        });

        Route::group(['prefix' => 'dry'], function () {
            Route::get('/', [App\Http\Controllers\Worker\Operation\DryController::class, 'index'])->name('worker.operation.dry');
        });

        Route::group(['prefix' => 'iron'], function () {
            Route::get('/', [App\Http\Controllers\Worker\Operation\IronController::class, 'index'])->name('worker.operation.iron');
        });

        Route::group(['prefix' => 'packing'], function () {
            Route::get('/', [App\Http\Controllers\Worker\Operation\PackingController::class, 'index'])->name('worker.operation.packing');
        });

        Route::group(['prefix' => 'collect'], function () {
            Route::get('/', [App\Http\Controllers\Worker\Operation\CollectController::class, 'index'])->name('worker.operation.collect');
        });
    });

    Route::get('/energy', function () {
        return ['hello world'];
    })->name('worker.energy');

    Route::get('/employee', function () {
        return ['hello world'];
    })->name('worker.employee');

    Route::get('/report', function () {
        return ['hello world'];
    })->name('worker.report');

    Route::get('/stock', function () {
        return ['hello world'];
    })->name('worker.stock');
});
