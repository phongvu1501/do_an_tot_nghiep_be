<?php

use App\Http\Controllers\Admin\BanAnController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NguoiDungController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {


    Route::prefix('admin')->group(function () {
        Route::get('/thong-ke', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('/ban-an', BanAnController::class)->names('admin.banAn');
        Route::put('/ban-an/disable/{banAn}', [BanAnController::class, 'disable'])->name('admin.banAn.disable');

        Route::resource('/quan-ly-ban-an', BanAnController::class)->names('admin.quanLyBanAn');

        // Route::resource('/tai-khoan', BanAnController::class)->names('admin.taiKhoan');

        Route::resource('/tai-khoan/nguoi-dung', NguoiDungController::class)->names('admin.taiKhoan.nguoiDung');
    });
});

require __DIR__ . '/auth.php';
