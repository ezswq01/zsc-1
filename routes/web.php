<?php

use App\Http\Controllers\DeviceController;
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
});
