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

    $inputName = 'weight';
    $inputValue = 12.45;

    return view('test', ['inputName' => $inputName, 'inputValue' => $inputValue]);
});

Route::get('/landing', function () {
    return view('landing');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::group(['prefix' => 'setting', 'middleware' => ['admin']], function () {
    Route::get('/', [App\Http\Controllers\SettingController::class, 'index'])->name('setting');
});

Route::group(['middleware' => ['admin']], function () {
    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::resource('customer-groups', App\Http\Controllers\CustomerGroupController::class);
    Route::resource('customers', App\Http\Controllers\CustomerController::class);
    Route::resource('departments', App\Http\Controllers\DepartmentController::class);
    Route::resource('employees', App\Http\Controllers\EmployeeController::class);
    Route::resource('linen-types', App\Http\Controllers\LinenTypeController::class);
    Route::resource('linen-products', App\Http\Controllers\LinenProductController::class);
    Route::resource('washing-machines', App\Http\Controllers\WashingMachineController::class);
    Route::resource('dryer-machines', App\Http\Controllers\DryerMachineController::class);
    Route::resource('inventories', App\Http\Controllers\InventoryController::class);

    Route::match(array('GET', 'POST'), 'trucks/{id}/create-note', [App\Http\Controllers\TruckController::class, 'createNote'])->name('trucks.create-note');
    Route::resource('trucks', App\Http\Controllers\TruckController::class);

    Route::resource('notes', App\Http\Controllers\NoteController::class);
});

Route::group(['prefix' => 'supervisor', 'middleware' => ['supervisor']], function () {

    Route::get('/', [App\Http\Controllers\SupervisorController::class, 'index'])->name('supervisor');

    Route::group(['prefix' => 'department'], function () {
        Route::get('/', [App\Http\Controllers\Supervisor\DepartmentController::class, 'index'])->name('supervisor.department');
        Route::match(array('GET', 'POST'), '/submit-daily-expense', [App\Http\Controllers\Supervisor\DepartmentController::class, 'submitDailyExpense'])->name('supervisor.department.submit-daily-expense');
        Route::match(array('GET', 'POST'), '/daily-expense-logs', [App\Http\Controllers\Supervisor\DepartmentController::class, 'dailyExpenseLogs'])->name('supervisor.department.daily-expense-log');
        Route::match(array('GET', 'POST'), '/daily-expense-log/{id}/delete', [App\Http\Controllers\Supervisor\DepartmentController::class, 'deleteDailyExpenseLog'])->name('supervisor.department.delete-daily-expense-log');
    });

    Route::group(['prefix' => 'customer'], function () {
        Route::get('/', [App\Http\Controllers\Supervisor\CustomerController::class, 'index'])->name('supervisor.customer');
        Route::get('/new-billing', [App\Http\Controllers\Supervisor\CustomerController::class, 'getNewBilling'])->name('supervisor.customer.new-billing');
        Route::get('/billing-logs', [App\Http\Controllers\Supervisor\CustomerController::class, 'getBillingLog'])->name('supervisor.customer.billing-logs');
    });


    Route::group(['prefix' => 'report'], function () {
        Route::get('/', [App\Http\Controllers\Supervisor\ReportController::class, 'index'])->name('supervisor.report');
    });
});

Route::group(['prefix' => 'worker', 'middleware' => ['auth']], function () {

    Route::get('/', [App\Http\Controllers\WorkerController::class, 'index'])->name('worker');

    Route::group(['prefix' => 'product'], function () {
        Route::get('/', [App\Http\Controllers\Worker\ProductController::class, 'index'])->name('worker.product');
        Route::get('/linen-case', [App\Http\Controllers\Worker\ProductController::class, 'selectLinenCase'])->name('worker.product.select-linen-case');
        Route::get('/linen-case/{linenCase}/operations', [App\Http\Controllers\Worker\ProductController::class, 'getOperationsByLinenCase'])->name('worker.product.get-operations-by-linen-case');
    });

    Route::group(['prefix' => 'customer'], function () {
        Route::get('/', [App\Http\Controllers\Worker\CustomerController::class, 'index'])->name('worker.customer');
        Route::get('/operation-summary', [App\Http\Controllers\Worker\CustomerController::class, 'getOperationSummary'])->name('worker.customer.operation-summary');
        Route::get('/operations-group-by-customer', [App\Http\Controllers\Worker\CustomerController::class, 'getOperationsGroupByCustomer'])->name('worker.customer.get-operations-group-by-customer');
        Route::get('/operations-by-customer/{customerId}', [App\Http\Controllers\Worker\CustomerController::class, 'getOperationsByCustomer'])->name('worker.customer.get-operations-by-customer');
        Route::get('/new-billing/{customerId}', [App\Http\Controllers\Worker\CustomerController::class, 'getNewBilling'])->name('worker.customer.new-billing');
        Route::post('/submit-billing/{customerId}', [App\Http\Controllers\Worker\CustomerController::class, 'submitBilling'])->name('worker.customer.submit-billing');
    });

    Route::group(['prefix' => 'operation'], function () {
        Route::get('/', [App\Http\Controllers\Worker\OperationController::class, 'index'])->name('worker.operation');
        Route::get('/in-progress', [App\Http\Controllers\Worker\OperationController::class, 'getOperationsInProgress'])->name('worker.operation.in-progress');
        Route::get('/checkout-in-progress/{operationId}', [App\Http\Controllers\Worker\OperationController::class, 'checkOutOperationInProgress'])->name('worker.operation.checkout-in-progress');

        Route::group(['prefix' => 'wash'], function () {
            Route::get('/', [App\Http\Controllers\Worker\Operation\WashController::class, 'index'])->name('worker.operation.wash');
            Route::get('/employee', [App\Http\Controllers\Worker\Operation\WashController::class, 'selectEmployee'])->name('worker.operation.wash.select-employee');
            Route::get('/employee/{employeeId}', [App\Http\Controllers\Worker\Operation\WashController::class, 'setSelectEmployee'])->name('worker.operation.wash.set-select-employee');
            Route::get('/{operationId}/customer', [App\Http\Controllers\Worker\Operation\WashController::class, 'selectCustomer'])->name('worker.operation.wash.select-customer');
            Route::get('/{operationId}/customer/{customerId}', [App\Http\Controllers\Worker\Operation\WashController::class, 'setSelectCustomer'])->name('worker.operation.wash.set-select-customer');
            Route::get('/{operationId}/washing-machine', [App\Http\Controllers\Worker\Operation\WashController::class, 'selectWashingMachine'])->name('worker.operation.wash.select-washing-machine');
            Route::get('/{operationId}/washing-machine/{washingMachineId}', [App\Http\Controllers\Worker\Operation\WashController::class, 'setSelectWashingMachine'])->name('worker.operation.wash.set-select-washing-machine');
            Route::get('/{operationId}/employee-summary', [App\Http\Controllers\Worker\Operation\WashController::class, 'getEmployeeSummary'])->name('worker.operation.wash.employee-summary');
            Route::get('/{operationId}/close', [App\Http\Controllers\Worker\Operation\WashController::class, 'setClose'])->name('worker.operation.wash.set-close');
            Route::get('/{operationId}/in-progress', [App\Http\Controllers\Worker\Operation\WashController::class, 'setInProgress'])->name('worker.operation.wash.set-in-progress');
            Route::get('/{operationId}/operation-linen-product', [App\Http\Controllers\Worker\Operation\WashController::class, 'selectOperationLinenProduct'])->name('worker.operation.wash.select-operation-linen-product');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/linen-case', [App\Http\Controllers\Worker\Operation\WashController::class, 'selectLinenCase'])->name('worker.operation.wash.select-linen-case');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/linen-case/{linenCase}', [App\Http\Controllers\Worker\Operation\WashController::class, 'setSelectLinenCase'])->name('worker.operation.wash.set-select-linen-case');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/linen-product', [App\Http\Controllers\Worker\Operation\WashController::class, 'selectLinenProduct'])->name('worker.operation.wash.select-linen-product');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/linen-product/{linenProductId}', [App\Http\Controllers\Worker\Operation\WashController::class, 'setSelectLinenProduct'])->name('worker.operation.wash.set-select-linen-product');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/weight-and-color', [App\Http\Controllers\Worker\Operation\WashController::class, 'selectWeightAndColor'])->name('worker.operation.wash.select-weight-and-color');
            Route::post('/{operationId}/linen-product/{operationLinenProductId}/set-weight-and-color', [App\Http\Controllers\Worker\Operation\WashController::class, 'setSelectWeightAndColor'])->name('worker.operation.wash.set-select-weight-and-color');
            Route::get('/{operationId}/delete-operation-linen-product/{operationLinenProductId}', [App\Http\Controllers\Worker\Operation\WashController::class, 'deleteOperationLinenProduct'])->name('worker.operation.wash.delete-operation-linen-product');
        });

        Route::group(['prefix' => 'dry'], function () {
            Route::get('/', [App\Http\Controllers\Worker\Operation\DryController::class, 'index'])->name('worker.operation.dry');
            Route::get('/employee', [App\Http\Controllers\Worker\Operation\DryController::class, 'selectEmployee'])->name('worker.operation.dry.select-employee');
            Route::get('/employee/{employeeId}', [App\Http\Controllers\Worker\Operation\DryController::class, 'setSelectEmployee'])->name('worker.operation.dry.set-select-employee');
            Route::get('/{operationId}/customer', [App\Http\Controllers\Worker\Operation\DryController::class, 'selectCustomer'])->name('worker.operation.dry.select-customer');
            Route::get('/{operationId}/customer/{customerId}', [App\Http\Controllers\Worker\Operation\DryController::class, 'setSelectCustomer'])->name('worker.operation.dry.set-select-customer');
            Route::get('/{operationId}/dryer-machine', [App\Http\Controllers\Worker\Operation\DryController::class, 'selectDryerMachine'])->name('worker.operation.dry.select-dryer-machine');
            Route::get('/{operationId}/dryer-machine/{dryerMachineId}', [App\Http\Controllers\Worker\Operation\DryController::class, 'setSelectDryerMachine'])->name('worker.operation.dry.set-select-dryer-machine');
            Route::get('/{operationId}/employee-summary', [App\Http\Controllers\Worker\Operation\DryController::class, 'getEmployeeSummary'])->name('worker.operation.dry.employee-summary');
            Route::get('/{operationId}/close', [App\Http\Controllers\Worker\Operation\DryController::class, 'setClose'])->name('worker.operation.dry.set-close');
            Route::get('/{operationId}/in-progress', [App\Http\Controllers\Worker\Operation\DryController::class, 'setInProgress'])->name('worker.operation.dry.set-in-progress');
            Route::get('/{operationId}/operation-linen-product', [App\Http\Controllers\Worker\Operation\DryController::class, 'selectOperationLinenProduct'])->name('worker.operation.dry.select-operation-linen-product');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/linen-case', [App\Http\Controllers\Worker\Operation\DryController::class, 'selectLinenCase'])->name('worker.operation.dry.select-linen-case');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/linen-case/{linenCase}', [App\Http\Controllers\Worker\Operation\DryController::class, 'setSelectLinenCase'])->name('worker.operation.dry.set-select-linen-case');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/linen-product', [App\Http\Controllers\Worker\Operation\DryController::class, 'selectLinenProduct'])->name('worker.operation.dry.select-linen-product');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/linen-product/{linenProductId}', [App\Http\Controllers\Worker\Operation\DryController::class, 'setSelectLinenProduct'])->name('worker.operation.dry.set-select-linen-product');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/weight-and-color', [App\Http\Controllers\Worker\Operation\DryController::class, 'selectWeightAndColor'])->name('worker.operation.dry.select-weight-and-color');
            Route::post('/{operationId}/linen-product/{operationLinenProductId}/set-weight-and-color', [App\Http\Controllers\Worker\Operation\DryController::class, 'setSelectWeightAndColor'])->name('worker.operation.dry.set-select-weight-and-color');
            Route::get('/{operationId}/delete-operation-linen-product/{operationLinenProductId}', [App\Http\Controllers\Worker\Operation\DryController::class, 'deleteOperationLinenProduct'])->name('worker.operation.dry.delete-operation-linen-product');
        });

        Route::group(['prefix' => 'iron'], function () {
            Route::get('/', [App\Http\Controllers\Worker\Operation\IronController::class, 'index'])->name('worker.operation.iron');
            Route::get('/employee', [App\Http\Controllers\Worker\Operation\IronController::class, 'selectEmployee'])->name('worker.operation.iron.select-employee');
            Route::get('/employee/{employeeId}', [App\Http\Controllers\Worker\Operation\IronController::class, 'setSelectEmployee'])->name('worker.operation.iron.set-select-employee');
            Route::get('/{operationId}/customer', [App\Http\Controllers\Worker\Operation\IronController::class, 'selectCustomer'])->name('worker.operation.iron.select-customer');
            Route::get('/{operationId}/customer/{customerId}', [App\Http\Controllers\Worker\Operation\IronController::class, 'setSelectCustomer'])->name('worker.operation.iron.set-select-customer');
            Route::get('/{operationId}/employee-summary', [App\Http\Controllers\Worker\Operation\IronController::class, 'getEmployeeSummary'])->name('worker.operation.iron.employee-summary');
            Route::get('/{operationId}/close', [App\Http\Controllers\Worker\Operation\IronController::class, 'setClose'])->name('worker.operation.iron.set-close');
            Route::get('/{operationId}/in-progress', [App\Http\Controllers\Worker\Operation\IronController::class, 'setInProgress'])->name('worker.operation.iron.set-in-progress');
            Route::get('/{operationId}/operation-linen-product', [App\Http\Controllers\Worker\Operation\IronController::class, 'selectOperationLinenProduct'])->name('worker.operation.iron.select-operation-linen-product');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/linen-case', [App\Http\Controllers\Worker\Operation\IronController::class, 'selectLinenCase'])->name('worker.operation.iron.select-linen-case');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/linen-case/{linenCase}', [App\Http\Controllers\Worker\Operation\IronController::class, 'setSelectLinenCase'])->name('worker.operation.iron.set-select-linen-case');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/linen-product', [App\Http\Controllers\Worker\Operation\IronController::class, 'selectLinenProduct'])->name('worker.operation.iron.select-linen-product');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/linen-product/{linenProductId}', [App\Http\Controllers\Worker\Operation\IronController::class, 'setSelectLinenProduct'])->name('worker.operation.iron.set-select-linen-product');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/weight-and-color', [App\Http\Controllers\Worker\Operation\IronController::class, 'selectWeightAndColor'])->name('worker.operation.iron.select-weight-and-color');
            Route::post('/{operationId}/linen-product/{operationLinenProductId}/set-weight-and-color', [App\Http\Controllers\Worker\Operation\IronController::class, 'setSelectWeightAndColor'])->name('worker.operation.iron.set-select-weight-and-color');
            Route::get('/{operationId}/delete-operation-linen-product/{operationLinenProductId}', [App\Http\Controllers\Worker\Operation\IronController::class, 'deleteOperationLinenProduct'])->name('worker.operation.iron.delete-operation-linen-product');
        });

        Route::group(['prefix' => 'packing'], function () {
            Route::get('/', [App\Http\Controllers\Worker\Operation\PackingController::class, 'index'])->name('worker.operation.packing');
            Route::get('/employee', [App\Http\Controllers\Worker\Operation\PackingController::class, 'selectEmployee'])->name('worker.operation.packing.select-employee');
            Route::get('/employee/{employeeId}', [App\Http\Controllers\Worker\Operation\PackingController::class, 'setSelectEmployee'])->name('worker.operation.packing.set-select-employee');
            Route::get('/{operationId}/customer', [App\Http\Controllers\Worker\Operation\PackingController::class, 'selectCustomer'])->name('worker.operation.packing.select-customer');
            Route::get('/{operationId}/customer/{customerId}', [App\Http\Controllers\Worker\Operation\PackingController::class, 'setSelectCustomer'])->name('worker.operation.packing.set-select-customer');
            Route::get('/{operationId}/employee-summary', [App\Http\Controllers\Worker\Operation\PackingController::class, 'getEmployeeSummary'])->name('worker.operation.packing.employee-summary');
            Route::get('/{operationId}/close', [App\Http\Controllers\Worker\Operation\PackingController::class, 'setClose'])->name('worker.operation.packing.set-close');
            Route::get('/{operationId}/in-progress', [App\Http\Controllers\Worker\Operation\PackingController::class, 'setInProgress'])->name('worker.operation.packing.set-in-progress');
            Route::get('/{operationId}/operation-linen-product', [App\Http\Controllers\Worker\Operation\PackingController::class, 'selectOperationLinenProduct'])->name('worker.operation.packing.select-operation-linen-product');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/linen-case', [App\Http\Controllers\Worker\Operation\PackingController::class, 'selectLinenCase'])->name('worker.operation.packing.select-linen-case');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/linen-case/{linenCase}', [App\Http\Controllers\Worker\Operation\PackingController::class, 'setSelectLinenCase'])->name('worker.operation.packing.set-select-linen-case');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/linen-product', [App\Http\Controllers\Worker\Operation\PackingController::class, 'selectLinenProduct'])->name('worker.operation.packing.select-linen-product');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/linen-product/{linenProductId}', [App\Http\Controllers\Worker\Operation\PackingController::class, 'setSelectLinenProduct'])->name('worker.operation.packing.set-select-linen-product');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/weight-and-color', [App\Http\Controllers\Worker\Operation\PackingController::class, 'selectWeightAndColor'])->name('worker.operation.packing.select-weight-and-color');
            Route::post('/{operationId}/linen-product/{operationLinenProductId}/set-weight-and-color', [App\Http\Controllers\Worker\Operation\PackingController::class, 'setSelectWeightAndColor'])->name('worker.operation.packing.set-select-weight-and-color');
            Route::get('/{operationId}/delete-operation-linen-product/{operationLinenProductId}', [App\Http\Controllers\Worker\Operation\PackingController::class, 'deleteOperationLinenProduct'])->name('worker.operation.packing.delete-operation-linen-product');
        });

        Route::group(['prefix' => 'collect'], function () {
            Route::get('/', [App\Http\Controllers\Worker\Operation\CollectController::class, 'index'])->name('worker.operation.collect');
            Route::get('/employee', [App\Http\Controllers\Worker\Operation\CollectController::class, 'selectEmployee'])->name('worker.operation.collect.select-employee');
            Route::get('/employee/{employeeId}', [App\Http\Controllers\Worker\Operation\CollectController::class, 'setSelectEmployee'])->name('worker.operation.collect.set-select-employee');
            Route::get('/{operationId}/customer', [App\Http\Controllers\Worker\Operation\CollectController::class, 'selectCustomer'])->name('worker.operation.collect.select-customer');
            Route::get('/{operationId}/customer/{customerId}', [App\Http\Controllers\Worker\Operation\CollectController::class, 'setSelectCustomer'])->name('worker.operation.collect.set-select-customer');
            Route::get('/{operationId}/employee-summary', [App\Http\Controllers\Worker\Operation\CollectController::class, 'getEmployeeSummary'])->name('worker.operation.collect.employee-summary');
            Route::get('/{operationId}/close', [App\Http\Controllers\Worker\Operation\CollectController::class, 'setClose'])->name('worker.operation.collect.set-close');
            Route::get('/{operationId}/in-progress', [App\Http\Controllers\Worker\Operation\CollectController::class, 'setInProgress'])->name('worker.operation.collect.set-in-progress');
            Route::get('/{operationId}/operation-linen-product', [App\Http\Controllers\Worker\Operation\CollectController::class, 'selectOperationLinenProduct'])->name('worker.operation.collect.select-operation-linen-product');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/linen-case', [App\Http\Controllers\Worker\Operation\CollectController::class, 'selectLinenCase'])->name('worker.operation.collect.select-linen-case');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/linen-case/{linenCase}', [App\Http\Controllers\Worker\Operation\CollectController::class, 'setSelectLinenCase'])->name('worker.operation.collect.set-select-linen-case');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/linen-product', [App\Http\Controllers\Worker\Operation\CollectController::class, 'selectLinenProduct'])->name('worker.operation.collect.select-linen-product');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/linen-product/{linenProductId}', [App\Http\Controllers\Worker\Operation\CollectController::class, 'setSelectLinenProduct'])->name('worker.operation.collect.set-select-linen-product');
            Route::get('/{operationId}/linen-product/{operationLinenProductId}/weight-and-color', [App\Http\Controllers\Worker\Operation\CollectController::class, 'selectWeightAndColor'])->name('worker.operation.collect.select-weight-and-color');
            Route::post('/{operationId}/linen-product/{operationLinenProductId}/set-weight-and-color', [App\Http\Controllers\Worker\Operation\CollectController::class, 'setSelectWeightAndColor'])->name('worker.operation.collect.set-select-weight-and-color');
            Route::get('/{operationId}/delete-operation-linen-product/{operationLinenProductId}', [App\Http\Controllers\Worker\Operation\CollectController::class, 'deleteOperationLinenProduct'])->name('worker.operation.collect.delete-operation-linen-product');
        });

        Route::group(['prefix' => 'deliver'], function () {
            Route::get('/', [App\Http\Controllers\Worker\Operation\DeliverController::class, 'index'])->name('worker.operation.deliver');
            Route::get('/employee', [App\Http\Controllers\Worker\Operation\DeliverController::class, 'selectEmployee'])->name('worker.operation.deliver.select-employee');
            Route::get('/employee/{employeeId}', [App\Http\Controllers\Worker\Operation\DeliverController::class, 'setSelectEmployee'])->name('worker.operation.deliver.set-select-employee');
            Route::get('/{operationId}/truck', [App\Http\Controllers\Worker\Operation\DeliverController::class, 'selectTruck'])->name('worker.operation.deliver.select-truck');
            Route::get('/{operationId}/truck/{truckId}', [App\Http\Controllers\Worker\Operation\DeliverController::class, 'setSelectTruck'])->name('worker.operation.deliver.set-select-truck');
            Route::get('/{operationId}/employee-summary', [App\Http\Controllers\Worker\Operation\DeliverController::class, 'getEmployeeSummary'])->name('worker.operation.deliver.employee-summary');
            Route::match(array('GET', 'POST'), '/{operationId}/collect-operation', [App\Http\Controllers\Worker\Operation\DeliverController::class, 'selectCollectOperation'])->name('worker.operation.deliver.select-collect-operation');
            Route::match(array('GET', 'POST'), '/{operationId}/delete-collect-operation', [App\Http\Controllers\Worker\Operation\DeliverController::class, 'deleteCollectOperation'])->name('worker.operation.deliver.delete-collect-operation');
            Route::get('/{operationId}/close', [App\Http\Controllers\Worker\Operation\DeliverController::class, 'setClose'])->name('worker.operation.deliver.set-close');
            Route::get('/{operationId}/in-progress', [App\Http\Controllers\Worker\Operation\DeliverController::class, 'setInProgress'])->name('worker.operation.deliver.set-in-progress');
        });
    });

    Route::group(['prefix' => 'energy-resource'], function () {
        Route::get('/', [App\Http\Controllers\Worker\EnergyResourceController::class, 'index'])->name('worker.energy-resource');
        Route::get('/log/create/{energyResourceVarName}', [App\Http\Controllers\Worker\EnergyResourceLogController::class, 'setSelectEnergyResource'])->name('worker.energy-resource.log.select-energy-resource');
        Route::get('/log/{energyResourceLogId}/employee', [App\Http\Controllers\Worker\EnergyResourceLogController::class, 'selectEmployee'])->name('worker.energy-resource.select-employee');
        Route::get('/log/{energyResourceLogId}/employee/{employeeId}', [App\Http\Controllers\Worker\EnergyResourceLogController::class, 'setSelectEmployee'])->name('worker.energy-resource.set-select-employee');
        Route::match(array('GET', 'POST'), '/log/{energyResourceLogId}/water', [App\Http\Controllers\Worker\EnergyResourceLogController::class, 'submitWaterLog'])->name('worker.energy-resource.log.submit-water');
        Route::match(array('GET', 'POST'), '/log/{energyResourceLogId}/electricity', [App\Http\Controllers\Worker\EnergyResourceLogController::class, 'submitElectricityLog'])->name('worker.energy-resource.log.submit-electricity');
        Route::match(array('GET', 'POST'), '/log/{energyResourceLogId}/gas', [App\Http\Controllers\Worker\EnergyResourceLogController::class, 'submitGasLog'])->name('worker.energy-resource.log.submit-gas');
        Route::match(array('GET', 'POST'), '/log/{energyResourceLogId}/biomass', [App\Http\Controllers\Worker\EnergyResourceLogController::class, 'submitBiomassLog'])->name('worker.energy-resource.log.submit-biomass');
        Route::match(array('GET', 'POST'), '/log/{energyResourceLogId}/fuel-oil', [App\Http\Controllers\Worker\EnergyResourceLogController::class, 'submitFuelOilLog'])->name('worker.energy-resource.log.submit-fuel-oil');
        Route::match(array('GET', 'POST'), '/log/{energyResourceLogId}/petrol', [App\Http\Controllers\Worker\EnergyResourceLogController::class, 'submitPetrolLog'])->name('worker.energy-resource.log.submit-petrol');
        Route::get('/logs', [App\Http\Controllers\Worker\EnergyResourceLogController::class, 'getLogs'])->name('worker.energy-resource.logs');
        Route::post('/logs/{energyResourceLogId}/delete', [App\Http\Controllers\Worker\EnergyResourceLogController::class, 'deleteLog'])->name('worker.energy-resource.logs.delete');
        Route::get('/summary/{energyResourceVarName}', [App\Http\Controllers\Worker\EnergyResourceController::class, 'getSummary'])->name('worker.energy-resource.summary');
    });

    Route::group(['prefix' => 'employee'], function () {
        Route::get('/', [App\Http\Controllers\Worker\EmployeeController::class, 'index'])->name('worker.employee');
        Route::get('/employees', [App\Http\Controllers\Worker\EmployeeController::class, 'selectEmployee'])->name('worker.employee.select-employee');
        Route::get('/employee-summary/{employeeId}', [App\Http\Controllers\Worker\EmployeeController::class, 'getEmployeeSummary'])->name('worker.employee.employee-summary');
    });

    Route::group(['prefix' => 'stock'], function () {
        Route::get('/', [App\Http\Controllers\Worker\StockController::class, 'index'])->name('worker.stock');
        Route::get('/inventory-group', [App\Http\Controllers\Worker\StockController::class, 'selectInventoryGroup'])->name('worker.stock.select-inventory-group');
        Route::get('/inventory-group/{inventoryGroupId}', [App\Http\Controllers\Worker\StockController::class, 'showInventoryByGroup'])->name('worker.stock.show-inventory-by-group');
        Route::match(array('GET', 'POST'), '/inventory-group/{inventoryGroupId}/create', [App\Http\Controllers\Worker\StockController::class, 'createInventoryByGroup'])->name('worker.stock.create-inventory-by-group');
        Route::match(array('GET', 'POST'), '/inventory/{inventoryId}/increase-stock', [App\Http\Controllers\Worker\StockController::class, 'getInventoryIncreaseStock'])->name('worker.stock.inventory.increase-stock');
        Route::match(array('GET', 'POST'), '/inventory/{inventoryId}/decrease-stock', [App\Http\Controllers\Worker\StockController::class, 'getInventoryDecreaseStock'])->name('worker.stock.inventory.decrease-stock');
        Route::get('/inventory/{inventoryId}/logs', [App\Http\Controllers\Worker\StockController::class, 'showInventoryLogs'])->name('worker.stock.inventory.logs');
    });
});
