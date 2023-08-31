<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceLogController;
use App\Http\Controllers\DeviceTypeController;
use App\Http\Controllers\StatusTypeController;
use Illuminate\Support\Facades\Route;

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

// Temporary redirect to devices index
Route::get('/', function () {
    return redirect()->route('admin.devices.index');
});

Route::name('admin.')->prefix('admin')->group(function () {
    Route::resource('devices', DeviceController::class);
    Route::resource('device_types', DeviceTypeController::class);
    Route::resource('status_types', StatusTypeController::class);
    Route::resource('device_logs', DeviceLogController::class);
    Route::resource('dashboard', DashboardController::class)->only(['index']);

    // Ajax routes
    Route::post('/devices/publish', [DeviceController::class, 'publish'])->name('devices.publish');
});
