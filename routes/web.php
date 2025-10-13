<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MenuCategoryController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->group(function () {
    Route::resource('menu-categories', MenuCategoryController::class);
});