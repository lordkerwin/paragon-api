<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeePayRateController;
use App\Http\Controllers\PayRateController;
use App\Models\Employee;
use Illuminate\Http\Request;
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

Route::post('login', [AuthController::class, 'login'])->name('auth.login');

Route::group(['middleware' => ['auth:api', 'admin']], function () {
    // Authenticated Auth Routes
    Route::get('logout', [AuthController::class, 'logout'])->name('auth.logout');

    // Employee
    Route::resource('employees', EmployeeController::class);

    // Employee Pay Rate
    Route::resource('employee-pay-rates', EmployeePayRateController::class);



    // PayRate
    Route::resource('pay-rates', PayRateController::class);
});
