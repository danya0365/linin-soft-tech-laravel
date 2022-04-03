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

    Route::group(['prefix' => 'operation/v1'], function () {
        Route::get('/', [App\Http\Controllers\Worker\OperationV1Controller::class, 'index'])->name('worker.operation-v1');

        Route::group(['prefix' => 'pickup'], function () {
            Route::get('/', [App\Http\Controllers\Worker\OperationV1\PickupController::class, 'index'])->name('worker.operation-v1.pick-up');
            Route::get('/employee', [App\Http\Controllers\Worker\OperationV1\PickupController::class, 'selectEmployee'])->name('worker.operation-v1.pickup.select-employee');
            Route::get('/employee/{employeeId}', [App\Http\Controllers\Worker\OperationV1\PickupController::class, 'setSelectEmployee'])->name('worker.operation-v1.pickup.set-select-employee');
            Route::get('/{jobGroupId}/customer', [App\Http\Controllers\Worker\OperationV1\PickupController::class, 'selectCustomer'])->name('worker.operation-v1.pickup.select-customer');
            Route::get('/{jobGroupId}/customer/{customerId}', [App\Http\Controllers\Worker\OperationV1\PickupController::class, 'setSelectCustomer'])->name('worker.operation-v1.pickup.set-select-customer');
            Route::get('/{jobGroupId}/submit', [App\Http\Controllers\Worker\OperationV1\PickupController::class, 'getSubmit'])->name('worker.operation-v1.pickup.submit');
            Route::post('/{jobGroupId}/submit', [App\Http\Controllers\Worker\OperationV1\PickupController::class, 'postSubmit'])->name('worker.operation-v1.pickup.submit');
        });

        Route::group(['prefix' => 'wash'], function () {
            Route::get('/', [App\Http\Controllers\Worker\OperationV1\WashController::class, 'index'])->name('worker.operation-v1.wash');
            Route::get('/employee', [App\Http\Controllers\Worker\OperationV1\WashController::class, 'selectEmployee'])->name('worker.operation-v1.wash.select-employee');
            Route::get('/employee/{employeeId}', [App\Http\Controllers\Worker\OperationV1\WashController::class, 'setSelectEmployee'])->name('worker.operation-v1.wash.set-select-employee');
            Route::get('/{jobId}/customer', [App\Http\Controllers\Worker\OperationV1\WashController::class, 'selectCustomer'])->name('worker.operation-v1.wash.select-customer');
            Route::get('/{jobId}/customer/{customerId}', [App\Http\Controllers\Worker\OperationV1\WashController::class, 'setSelectCustomer'])->name('worker.operation-v1.wash.set-select-customer');
            Route::get('/{jobId}/job-group', [App\Http\Controllers\Worker\OperationV1\WashController::class, 'selectJobGroup'])->name('worker.operation-v1.wash.select-job-group');
            Route::get('/{jobId}/job-group/{jobGroupId}', [App\Http\Controllers\Worker\OperationV1\WashController::class, 'setSelectJobGroup'])->name('worker.operation-v1.wash.set-select-job-group');
            Route::get('/{jobId}/job-case', [App\Http\Controllers\Worker\OperationV1\WashController::class, 'selectJobCase'])->name('worker.operation-v1.wash.select-job-case');
            Route::get('/{jobId}/job-case/{jobCase}', [App\Http\Controllers\Worker\OperationV1\WashController::class, 'setSelectJobCase'])->name('worker.operation-v1.wash.set-select-job-case');
            Route::get('/{jobId}/washing-machine', [App\Http\Controllers\Worker\OperationV1\WashController::class, 'selectWashingMachine'])->name('worker.operation-v1.wash.select-washing-machine');
            Route::get('/{jobId}/washing-machine/{washingMachineId}', [App\Http\Controllers\Worker\OperationV1\WashController::class, 'setSelectWashingMachine'])->name('worker.operation-v1.wash.set-select-washing-machine');
            Route::get('/{jobId}/linen-type', [App\Http\Controllers\Worker\OperationV1\WashController::class, 'selectLinenType'])->name('worker.operation-v1.wash.select-linen-type');
            Route::get('/{jobId}/linen-type/{tags}', [App\Http\Controllers\Worker\OperationV1\WashController::class, 'setSelectLinenType'])->name('worker.operation-v1.wash.set-select-linen-type');
            Route::get('/{jobId}/submit', [App\Http\Controllers\Worker\OperationV1\WashController::class, 'getSubmit'])->name('worker.operation-v1.wash.submit');
            Route::post('/{jobId}/submit', [App\Http\Controllers\Worker\OperationV1\WashController::class, 'postSubmit'])->name('worker.operation-v1.wash.submit');
            Route::get('/{jobId}/employee-result', [App\Http\Controllers\Worker\OperationV1\WashController::class, 'getEmployeeResult'])->name('worker.operation-v1.wash.employee-result');
        });

        Route::group(['prefix' => 'dry'], function () {
            Route::get('/', [App\Http\Controllers\Worker\OperationV1\DryController::class, 'index'])->name('worker.operation-v1.dry');
            Route::get('/job', [App\Http\Controllers\Worker\OperationV1\DryController::class, 'selectJob'])->name('worker.operation-v1.dry.select-job');
            Route::get('/job/{jobId}', [App\Http\Controllers\Worker\OperationV1\DryController::class, 'setSelectJob'])->name('worker.operation-v1.dry.set-select-job');
            Route::get('/{jobId}/employee', [App\Http\Controllers\Worker\OperationV1\DryController::class, 'selectEmployee'])->name('worker.operation-v1.dry.select-employee');
            Route::get('/{jobId}/employee/{employeeId}', [App\Http\Controllers\Worker\OperationV1\DryController::class, 'setSelectEmployee'])->name('worker.operation-v1.dry.set-select-employee');
            Route::get('/{jobId}/dryer-machine', [App\Http\Controllers\Worker\OperationV1\DryController::class, 'selectDryerMachine'])->name('worker.operation-v1.dry.select-dryer-machine');
            Route::get('/{jobId}/dryer-machine/{dryerMachineId}', [App\Http\Controllers\Worker\OperationV1\DryController::class, 'setSelectDryerMachine'])->name('worker.operation-v1.dry.set-select-dryer-machine');
            Route::get('/{jobId}/submit', [App\Http\Controllers\Worker\OperationV1\DryController::class, 'getSubmit'])->name('worker.operation-v1.dry.submit');
            Route::post('/{jobId}/submit', [App\Http\Controllers\Worker\OperationV1\DryController::class, 'postSubmit'])->name('worker.operation-v1.dry.submit');
            Route::get('/{jobId}/employee-result', [App\Http\Controllers\Worker\OperationV1\DryController::class, 'getEmployeeResult'])->name('worker.operation-v1.dry.employee-result');
        });

        Route::group(['prefix' => 'iron'], function () {
            Route::get('/', [App\Http\Controllers\Worker\OperationV1\IronController::class, 'index'])->name('worker.operation-v1.iron');
            Route::get('/job', [App\Http\Controllers\Worker\OperationV1\IronController::class, 'selectJob'])->name('worker.operation-v1.iron.select-job');
            Route::get('/job/{jobId}', [App\Http\Controllers\Worker\OperationV1\IronController::class, 'setSelectJob'])->name('worker.operation-v1.iron.set-select-job');
            Route::get('/{jobId}/employee', [App\Http\Controllers\Worker\OperationV1\IronController::class, 'selectEmployee'])->name('worker.operation-v1.iron.select-employee');
            Route::get('/{jobId}/employee/{employeeId}', [App\Http\Controllers\Worker\OperationV1\IronController::class, 'setSelectEmployee'])->name('worker.operation-v1.iron.set-select-employee');
            Route::get('/{jobId}/submit', [App\Http\Controllers\Worker\OperationV1\IronController::class, 'getSubmit'])->name('worker.operation-v1.iron.submit');
            Route::post('/{jobId}/submit', [App\Http\Controllers\Worker\OperationV1\IronController::class, 'postSubmit'])->name('worker.operation-v1.iron.submit');
            Route::get('/{jobId}/employee-result', [App\Http\Controllers\Worker\OperationV1\IronController::class, 'getEmployeeResult'])->name('worker.operation-v1.iron.employee-result');
            Route::post('/{jobId}/employee-result', [App\Http\Controllers\Worker\OperationV1\IronController::class, 'postEmployeeResult'])->name('worker.operation-v1.iron.employee-result');
        });

        Route::group(['prefix' => 'packing'], function () {
            Route::get('/', [App\Http\Controllers\Worker\OperationV1\PackingController::class, 'index'])->name('worker.operation-v1.packing');
            Route::get('/job-group', [App\Http\Controllers\Worker\OperationV1\PackingController::class, 'selectJobGroup'])->name('worker.operation-v1.packing.select-job-group');
            Route::get('/job-group/{jobGroupId}', [App\Http\Controllers\Worker\OperationV1\PackingController::class, 'setSelectJobGroup'])->name('worker.operation-v1.packing.set-select-job-group');
            Route::get('/{jobGroupId}/employee', [App\Http\Controllers\Worker\OperationV1\PackingController::class, 'selectEmployee'])->name('worker.operation-v1.packing.select-employee');
            Route::get('/{jobGroupId}/employee/{employeeId}', [App\Http\Controllers\Worker\OperationV1\PackingController::class, 'setSelectEmployee'])->name('worker.operation-v1.packing.set-select-employee');
            Route::get('/{jobGroupId}/submit', [App\Http\Controllers\Worker\OperationV1\PackingController::class, 'getSubmit'])->name('worker.operation-v1.packing.submit');
            Route::post('/{jobGroupId}/submit', [App\Http\Controllers\Worker\OperationV1\PackingController::class, 'postSubmit'])->name('worker.operation-v1.packing.submit');
            Route::get('/{jobGroupId}/employee-result', [App\Http\Controllers\Worker\OperationV1\PackingController::class, 'getEmployeeResult'])->name('worker.operation-v1.packing.employee-result');
        });

        Route::group(['prefix' => 'collect'], function () {
            Route::get('/', [App\Http\Controllers\Worker\OperationV1\CollectController::class, 'index'])->name('worker.operation-v1.collect');
            Route::get('/job-group', [App\Http\Controllers\Worker\OperationV1\CollectController::class, 'selectJobGroup'])->name('worker.operation-v1.collect.select-job-group');
            Route::get('/job-group/{jobGroupId}', [App\Http\Controllers\Worker\OperationV1\CollectController::class, 'setSelectJobGroup'])->name('worker.operation-v1.collect.set-select-job-group');
            Route::get('/{jobGroupId}/employee', [App\Http\Controllers\Worker\OperationV1\CollectController::class, 'selectEmployee'])->name('worker.operation-v1.collect.select-employee');
            Route::get('/{jobGroupId}/employee/{employeeId}', [App\Http\Controllers\Worker\OperationV1\CollectController::class, 'setSelectEmployee'])->name('worker.operation-v1.collect.set-select-employee');
            Route::get('/{jobGroupId}/submit', [App\Http\Controllers\Worker\OperationV1\CollectController::class, 'getSubmit'])->name('worker.operation-v1.collect.submit');
            Route::post('/{jobGroupId}/submit', [App\Http\Controllers\Worker\OperationV1\CollectController::class, 'postSubmit'])->name('worker.operation-v1.collect.submit');
            Route::get('/{jobGroupId}/employee-result', [App\Http\Controllers\Worker\OperationV1\CollectController::class, 'getEmployeeResult'])->name('worker.operation-v1.collect.employee-result');
            Route::post('/{jobGroupId}/employee-result', [App\Http\Controllers\Worker\OperationV1\CollectController::class, 'postEmployeeResult'])->name('worker.operation-v1.collect.employee-result');
        });
    });


    Route::group(['prefix' => 'operation'], function () {
        Route::get('/', [App\Http\Controllers\Worker\OperationController::class, 'index'])->name('worker.operation');

        Route::group(['prefix' => 'wash'], function () {
            Route::get('/', [App\Http\Controllers\Worker\Operation\WashController::class, 'index'])->name('worker.operation.wash');
            Route::get('/employee', [App\Http\Controllers\Worker\Operation\WashController::class, 'selectEmployee'])->name('worker.operation.wash.select-employee');
            Route::get('/employee/{employeeId}', [App\Http\Controllers\Worker\Operation\WashController::class, 'setSelectEmployee'])->name('worker.operation.wash.set-select-employee');
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
