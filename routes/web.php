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

Route::get('/landing', function () {
    return view('landing');
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
    Route::resource('linen-types', App\Http\Controllers\LinenTypeController::class);
    Route::resource('linen-products', App\Http\Controllers\LinenProductControllers::class);
    Route::resource('washing-machines', App\Http\Controllers\WashingMachineController::class);
    Route::resource('dryer-machines', App\Http\Controllers\DryerMachineController::class);
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
            Route::get('/', [App\Http\Controllers\Worker\Operation\PickupController::class, 'index'])->name('worker.operation.pick-up');
            Route::get('/employee', [App\Http\Controllers\Worker\Operation\PickupController::class, 'selectEmployee'])->name('worker.operation.pickup.select-employee');
            Route::get('/employee/{employeeId}', [App\Http\Controllers\Worker\Operation\PickupController::class, 'setSelectEmployee'])->name('worker.operation.pickup.set-select-employee');
            Route::get('/{jobGroupId}/customer', [App\Http\Controllers\Worker\Operation\PickupController::class, 'selectCustomer'])->name('worker.operation.pickup.select-customer');
            Route::get('/{jobGroupId}/customer/{customerId}', [App\Http\Controllers\Worker\Operation\PickupController::class, 'setSelectCustomer'])->name('worker.operation.pickup.set-select-customer');
            Route::get('/{jobGroupId}/submit', [App\Http\Controllers\Worker\Operation\PickupController::class, 'getSubmit'])->name('worker.operation.pickup.submit');
            Route::post('/{jobGroupId}/submit', [App\Http\Controllers\Worker\Operation\PickupController::class, 'postSubmit'])->name('worker.operation.pickup.submit');
        });

        Route::group(['prefix' => 'wash'], function () {
            Route::get('/', [App\Http\Controllers\Worker\Operation\WashController::class, 'index'])->name('worker.operation.wash');
            Route::get('/employee', [App\Http\Controllers\Worker\Operation\WashController::class, 'selectEmployee'])->name('worker.operation.wash.select-employee');
            Route::get('/employee/{employeeId}', [App\Http\Controllers\Worker\Operation\WashController::class, 'setSelectEmployee'])->name('worker.operation.wash.set-select-employee');
            Route::get('/{jobId}/customer', [App\Http\Controllers\Worker\Operation\WashController::class, 'selectCustomer'])->name('worker.operation.wash.select-customer');
            Route::get('/{jobId}/customer/{customerId}', [App\Http\Controllers\Worker\Operation\WashController::class, 'setSelectCustomer'])->name('worker.operation.wash.set-select-customer');
            Route::get('/{jobId}/job-group', [App\Http\Controllers\Worker\Operation\WashController::class, 'selectJobGroup'])->name('worker.operation.wash.select-job-group');
            Route::get('/{jobId}/job-group/{jobGroupId}', [App\Http\Controllers\Worker\Operation\WashController::class, 'setSelectJobGroup'])->name('worker.operation.wash.set-select-job-group');
            Route::get('/{jobId}/job-case', [App\Http\Controllers\Worker\Operation\WashController::class, 'selectJobCase'])->name('worker.operation.wash.select-job-case');
            Route::get('/{jobId}/job-case/{jobCase}', [App\Http\Controllers\Worker\Operation\WashController::class, 'setSelectJobCase'])->name('worker.operation.wash.set-select-job-case');
            Route::get('/{jobId}/washing-machine', [App\Http\Controllers\Worker\Operation\WashController::class, 'selectWashingMachine'])->name('worker.operation.wash.select-washing-machine');
            Route::get('/{jobId}/washing-machine/{washingMachineId}', [App\Http\Controllers\Worker\Operation\WashController::class, 'setSelectWashingMachine'])->name('worker.operation.wash.set-select-washing-machine');
            Route::get('/{jobId}/linen-type', [App\Http\Controllers\Worker\Operation\WashController::class, 'selectLinenType'])->name('worker.operation.wash.select-linen-type');
            Route::get('/{jobId}/linen-type/{tags}', [App\Http\Controllers\Worker\Operation\WashController::class, 'setSelectLinenType'])->name('worker.operation.wash.set-select-linen-type');
            Route::get('/{jobId}/submit', [App\Http\Controllers\Worker\Operation\WashController::class, 'getSubmit'])->name('worker.operation.wash.submit');
            Route::post('/{jobId}/submit', [App\Http\Controllers\Worker\Operation\WashController::class, 'postSubmit'])->name('worker.operation.wash.submit');
            Route::get('/{jobId}/employee-result', [App\Http\Controllers\Worker\Operation\WashController::class, 'getEmployeeResult'])->name('worker.operation.wash.employee-result');
        });

        Route::group(['prefix' => 'dry'], function () {
            Route::get('/', [App\Http\Controllers\Worker\Operation\DryController::class, 'index'])->name('worker.operation.dry');
            Route::get('/job', [App\Http\Controllers\Worker\Operation\DryController::class, 'selectJob'])->name('worker.operation.dry.select-job');
            Route::get('/job/{jobId}', [App\Http\Controllers\Worker\Operation\DryController::class, 'setSelectJob'])->name('worker.operation.dry.set-select-job');
            Route::get('/{jobId}/employee', [App\Http\Controllers\Worker\Operation\DryController::class, 'selectEmployee'])->name('worker.operation.dry.select-employee');
            Route::get('/{jobId}/employee/{employeeId}', [App\Http\Controllers\Worker\Operation\DryController::class, 'setSelectEmployee'])->name('worker.operation.dry.set-select-employee');
            Route::get('/{jobId}/dryer-machine', [App\Http\Controllers\Worker\Operation\DryController::class, 'selectDryerMachine'])->name('worker.operation.dry.select-dryer-machine');
            Route::get('/{jobId}/dryer-machine/{dryerMachineId}', [App\Http\Controllers\Worker\Operation\DryController::class, 'setSelectDryerMachine'])->name('worker.operation.dry.set-select-dryer-machine');
            Route::get('/{jobId}/submit', [App\Http\Controllers\Worker\Operation\DryController::class, 'getSubmit'])->name('worker.operation.dry.submit');
            Route::post('/{jobId}/submit', [App\Http\Controllers\Worker\Operation\DryController::class, 'postSubmit'])->name('worker.operation.dry.submit');
            Route::get('/{jobId}/employee-result', [App\Http\Controllers\Worker\Operation\DryController::class, 'getEmployeeResult'])->name('worker.operation.dry.employee-result');
        });

        Route::group(['prefix' => 'iron'], function () {
            Route::get('/', [App\Http\Controllers\Worker\Operation\IronController::class, 'index'])->name('worker.operation.iron');
            Route::get('/job', [App\Http\Controllers\Worker\Operation\IronController::class, 'selectJob'])->name('worker.operation.iron.select-job');
            Route::get('/job/{jobId}', [App\Http\Controllers\Worker\Operation\IronController::class, 'setSelectJob'])->name('worker.operation.iron.set-select-job');
            Route::get('/{jobId}/employee', [App\Http\Controllers\Worker\Operation\IronController::class, 'selectEmployee'])->name('worker.operation.iron.select-employee');
            Route::get('/{jobId}/employee/{employeeId}', [App\Http\Controllers\Worker\Operation\IronController::class, 'setSelectEmployee'])->name('worker.operation.iron.set-select-employee');
            Route::get('/{jobId}/submit', [App\Http\Controllers\Worker\Operation\IronController::class, 'getSubmit'])->name('worker.operation.iron.submit');
            Route::post('/{jobId}/submit', [App\Http\Controllers\Worker\Operation\IronController::class, 'postSubmit'])->name('worker.operation.iron.submit');
            Route::get('/{jobId}/employee-result', [App\Http\Controllers\Worker\Operation\IronController::class, 'getEmployeeResult'])->name('worker.operation.iron.employee-result');
            Route::post('/{jobId}/employee-result', [App\Http\Controllers\Worker\Operation\IronController::class, 'postEmployeeResult'])->name('worker.operation.iron.employee-result');
        });

        Route::group(['prefix' => 'packing'], function () {
            Route::get('/', [App\Http\Controllers\Worker\Operation\PackingController::class, 'index'])->name('worker.operation.packing');
            Route::get('/job-group', [App\Http\Controllers\Worker\Operation\PackingController::class, 'selectJobGroup'])->name('worker.operation.packing.select-job-group');
            Route::get('/job-group/{jobGroupId}', [App\Http\Controllers\Worker\Operation\PackingController::class, 'setSelectJobGroup'])->name('worker.operation.packing.set-select-job-group');
            Route::get('/{jobGroupId}/employee', [App\Http\Controllers\Worker\Operation\PackingController::class, 'selectEmployee'])->name('worker.operation.packing.select-employee');
            Route::get('/{jobGroupId}/employee/{employeeId}', [App\Http\Controllers\Worker\Operation\PackingController::class, 'setSelectEmployee'])->name('worker.operation.packing.set-select-employee');
            Route::get('/{jobGroupId}/submit', [App\Http\Controllers\Worker\Operation\PackingController::class, 'getSubmit'])->name('worker.operation.packing.submit');
            Route::post('/{jobGroupId}/submit', [App\Http\Controllers\Worker\Operation\PackingController::class, 'postSubmit'])->name('worker.operation.packing.submit');
            Route::get('/{jobGroupId}/employee-result', [App\Http\Controllers\Worker\Operation\PackingController::class, 'getEmployeeResult'])->name('worker.operation.packing.employee-result');
        });

        Route::group(['prefix' => 'collect'], function () {
            Route::get('/', [App\Http\Controllers\Worker\Operation\CollectController::class, 'index'])->name('worker.operation.collect');
            Route::get('/job-group', [App\Http\Controllers\Worker\Operation\CollectController::class, 'selectJobGroup'])->name('worker.operation.collect.select-job-group');
            Route::get('/job-group/{jobGroupId}', [App\Http\Controllers\Worker\Operation\CollectController::class, 'setSelectJobGroup'])->name('worker.operation.collect.set-select-job-group');
            Route::get('/{jobGroupId}/employee', [App\Http\Controllers\Worker\Operation\CollectController::class, 'selectEmployee'])->name('worker.operation.collect.select-employee');
            Route::get('/{jobGroupId}/employee/{employeeId}', [App\Http\Controllers\Worker\Operation\CollectController::class, 'setSelectEmployee'])->name('worker.operation.collect.set-select-employee');
            Route::get('/{jobGroupId}/submit', [App\Http\Controllers\Worker\Operation\CollectController::class, 'getSubmit'])->name('worker.operation.collect.submit');
            Route::post('/{jobGroupId}/submit', [App\Http\Controllers\Worker\Operation\CollectController::class, 'postSubmit'])->name('worker.operation.collect.submit');
            Route::get('/{jobGroupId}/employee-result', [App\Http\Controllers\Worker\Operation\CollectController::class, 'getEmployeeResult'])->name('worker.operation.collect.employee-result');
            Route::post('/{jobGroupId}/employee-result', [App\Http\Controllers\Worker\Operation\CollectController::class, 'postEmployeeResult'])->name('worker.operation.collect.employee-result');
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
