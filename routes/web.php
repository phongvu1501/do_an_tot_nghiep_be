<?php

use App\Http\Controllers\Admin\BanAnController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MenuCategoryController;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->group(function(){
    Route::get('/',[DashboardController::class,'index'])->name('admin.dashboard');
    Route::resource('/ban-an', BanAnController::class)->names('admin.banAn');
    
    Route::resource('/menu-categories', MenuCategoryController::class)->names('admin.menu_categories');

    Route::put('/ban-an/disable/{banAn}', [BanAnController::class, 'disable'])->name('admin.banAn.disable');
});

Route::get('/register', [RegisterController::class, 'showForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register');