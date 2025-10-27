<?php

use App\Http\Controllers\Admin\BanAnController;
use App\Http\Controllers\Admin\DanhMucMonAnController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NguoiDungController;
use App\Http\Controllers\admin\VoucherController;
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

        Route::resource('/thuc-don/danh-muc-mon-an', DanhMucMonAnController::class)->names('admin.thucDon.danhMucMonAn');
        Route::put('/thuc-don/danh-muc-mon-an/disable/{menuCategory}', [DanhMucMonAnController::class, 'disable'])->name('admin.thucDon.danhMucMonAn.disable');

        Route::resource('/voucher', VoucherController::class)->names('admin.voucher');
        Route::put('/voucher/disable/{voucher}', [VoucherController::class, 'disable'])->name('admin.voucher.disable');

        Route::resource('/tai-khoan/nguoi-dung', NguoiDungController::class)->names('admin.taiKhoan.nguoiDung');
    });
});

require __DIR__ . '/auth.php';
