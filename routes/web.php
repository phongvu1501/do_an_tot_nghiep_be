<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->group(function(){
    Route::get('/',[DashboardController::class,'index'])->name('admin.dashboard');
});
