<?php

use App\Http\Controllers\Admin\BanAnController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('/ban-an', BanAnController::class)->names('admin.banAn');
    
    Route::put('/ban-an/disable/{banAn}', [BanAnController::class, 'disable'])->name('admin.banAn.disable');
});
