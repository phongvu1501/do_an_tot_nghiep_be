<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\BanAnController;
use App\Http\Controllers\Admin\DatBanController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuCategoryController;

// --- Trang chính
Route::get('/', function () {
    return redirect()->route('login');
});
//
Route::get('/login', function () {
    return view('auth.login'); // view login
})->name('login');

//route tài khoản role admin user
Route::middleware(['auth'])->group(function () {
    Route::get('/accounts', [UserController::class, 'showAdmins'])->name('admin.accounts');
    Route::get('/user/accounts', [UserController::class, 'showUsers'])->name('user.accounts');
    Route::get('/admin/profile', [UserController::class, 'profile'])->name('admin.profile');
    Route::get('/admin/profile', [UserController::class, 'profile'])->name('admin.profile');
    Route::put('/admin/profile', [UserController::class, 'updateProfile'])->name('admin.profile.update');
    Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
    Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->middleware('auth')->name('dashboard');
});


// --- Admin routes
//bọc tất cả router admin lại 
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('/ban-an', BanAnController::class)->names('admin.banAn');

    Route::resource('/menu-categories', MenuCategoryController::class)->names('admin.menu_categories');
    Route::resource('/menus', MenuController::class)->names('admin.menus');
    Route::get('/menus-trash', [MenuController::class, 'trash'])->name('admin.menus.trash');
    Route::post('/menus/{id}/restore', [MenuController::class, 'restore'])->name('admin.menus.restore');
    Route::delete('/menus/{id}/force-delete', [MenuController::class, 'forceDelete'])->name('admin.menus.forceDelete');

    Route::put('/ban-an/disable/{banAn}', [BanAnController::class, 'disable'])->name('admin.banAn.disable');

    Route::get('dat-ban/available-tables', [DatBanController::class, 'getAvailableTables'])->name('admin.datBan.availableTables');
    Route::resource('dat-ban', DatBanController::class)->names('admin.datBan');
    Route::post('dat-ban/update-status', [DatBanController::class, 'updateStatus'])->name('admin.datBan.updateStatus');
    Route::put('dat-ban/{id}/update-tables', [DatBanController::class, 'updateTables'])->name('admin.datBan.updateTables');


    Route::get('/accounts/{id}', [UserController::class, 'show'])->name('admin.accounts.show');
});






// --- Auth routes
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', [AuthController::class, 'dashboard'])->middleware('auth')->name('dashboard');


///password reset quên mật khẩu
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

// Route đổi mật khẩu (phải đăng nhập mới được đổi)

Route::get('/change-password', [PasswordResetController::class, 'showChangeForm'])->name('password.change.form');
Route::post('/change-password', [PasswordResetController::class, 'change'])->name('password.change');
