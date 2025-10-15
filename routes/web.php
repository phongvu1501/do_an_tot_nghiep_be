<?php

use App\Http\Controllers\Admin\BanAnController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MenuCategoryController;

Route::get('/', function () {
    return view('welcome');
});

 <<<<<<<develop
Route::prefix('admin')->group(function(){
    Route::get('/',[DashboardController::class,'index'])->name('admin.dashboard');
    Route::resource('/ban-an', BanAnController::class)->names('admin.banAn');
});
Route::prefix('admin')->group(function () {
    Route::resource('menu-categories', MenuCategoryController::class);
});

 >>>>>>>ductai
