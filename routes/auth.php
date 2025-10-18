<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// -------------------------------------------------------------------
// ROUTE ĐĂNG NHẬP (CẦN ĐẶT TÊN LÀ 'login' để Laravel biết chuyển hướng)
// -------------------------------------------------------------------

// [GET] Hiển thị Form Đăng nhập
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login'); 

// [POST] Xử lý Đăng nhập
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');
    
// -------------------------------------------------------------------
// CÁC ROUTE XÁC THỰC KHÁC
// -------------------------------------------------------------------

// [GET] Hiển thị Form Đăng ký
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');

// [POST] Xử lý Đăng ký
Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');

// Quên mật khẩu
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

// Đặt lại mật khẩu
Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');

// Xác minh Email
Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

// Gửi lại thông báo xác minh Email
Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

// Đăng xuất
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');