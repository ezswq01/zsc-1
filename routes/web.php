<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceLogController;
use App\Http\Controllers\DeviceStatusController;
use App\Http\Controllers\DeviceTypeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StatusTypeController;
use App\Http\Controllers\UserController;
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

Route::get('/', function () {
    return redirect()->route('admin.dashboard.index');
});

Route::get('/login', [AuthController::class, 'loginGet'])->name('login');
Route::post('/login', [AuthController::class, 'loginPost'])->name('login.post');

Route::name('admin.')->prefix('admin')->middleware('auth')->group(function () {
    Route::resource('settings', SettingController::class)->only('index', 'update');
    Route::resource('devices', DeviceController::class);
    Route::resource('device_types', DeviceTypeController::class);
    Route::resource('status_types', StatusTypeController::class);
    Route::resource('device_logs', DeviceLogController::class);
    Route::resource('dashboard', DashboardController::class)->only(['index']);
    Route::resource('users', UserController::class);

    // Logout
    Route::get('/logout', [AuthController::class, 'logoutPost'])->name('logout');

    // Ajax routes
    Route::post('/devices/publish', [DeviceController::class, 'publish'])->name('devices.publish');
    Route::post('/device_status/notes', [DeviceStatusController::class, 'notes'])->name('device_status.notes');
});
