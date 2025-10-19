<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\MenuCategoryController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DatBanController;



Route::get('/', function () {
    return ['Laravel' => app()->version()];
});


Route::prefix('admin')->group(function(){
    Route::get('/',[DashboardController::class,'index'])->name('admin.dashboard');
    Route::resource('/ban-an', BanAnController::class)->names('admin.banAn');

    Route::resource('/menu-categories', MenuCategoryController::class)->names('admin.menu_categories');
    Route::resource('/menus', MenuController::class)->names('admin.menus');
    Route::get('/menus-trash', [MenuController::class, 'trash'])->name('admin.menus.trash');
    Route::post('/menus/{id}/restore', [MenuController::class, 'restore'])->name('admin.menus.restore');
    Route::delete('/menus/{id}/force-delete', [MenuController::class, 'forceDelete'])->name('admin.menus.forceDelete');


    Route::put('/ban-an/disable/{banAn}', [BanAnController::class, 'disable'])->name('admin.banAn.disable');

    Route::resource('dat-ban', DatBanController::class)->names('admin.datBan');
    Route::post('dat-ban/update-status', [DatBanController::class, 'updateStatus'])->name('admin.datBan.updateStatus');
});

Route::get('/register', [RegisterController::class, 'showForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register');

require __DIR__.'/auth.php';
