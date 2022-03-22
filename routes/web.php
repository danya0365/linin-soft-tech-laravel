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
    return view('welcome');
});

Route::get('/test', function () {
    return ['hello world'];
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['admin']], function () {
    Route::resource('users', App\Http\Controllers\UserController::class);
});

Route::group(['prefix' => 'worker'], function () {

    Route::get('/', [App\Http\Controllers\WorkerController::class, 'index'])->name('worker.index');

    Route::get('/product', function () {
        return ['hello world'];
    })->name('worker.product');

    Route::get('/customer', function () {
        return ['hello world'];
    })->name('worker.customer');

    Route::get('/operation', function () {
        return ['hello world'];
    })->name('worker.operation');

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
