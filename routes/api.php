<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\API\DatBanAnController;
use App\Http\Controllers\API\MenuApiController;
use App\Http\Controllers\API\MenuCategoryApiController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Middleware\RoleMiddleware;

// ======================================================
// 🔓 PUBLIC ROUTES (Không cần token)
// ======================================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [PasswordResetController::class, 'forgot']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);

// Menu và danh mục (cho tất cả)
Route::get('/menu-categories', [MenuCategoryApiController::class, 'index']);
Route::get('/menus', [MenuApiController::class, 'index']);

// Xác nhận thanh toán (nếu public)
Route::get('/payment/confirm/{token}', [DatBanAnController::class, 'confirmPayment']);

// ======================================================
// 🔐 PROTECTED ROUTES (Cần token Sanctum)
// ======================================================
Route::middleware('auth:sanctum')->group(function () {

    // Thông tin người dùng hiện tại
    Route::get('/user', [AuthController::class, 'user']);
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // ==================================================
    // 👥 USER + ADMIN ROUTES (role: user, admin)
    // ==================================================
    Route::middleware([RoleMiddleware::class . ':user,admin'])->group(function () {
        // Hồ sơ người dùng
        Route::get('/profile', function () {
            return response()->json(['message' => 'Xin chào, đây là trang hồ sơ của bạn.']);
        });

        // Đặt bàn ăn
        Route::post('/dat-ban-an', [DatBanAnController::class, 'store']);

        // Lịch sử đặt bàn
        Route::get('/dat-ban-an/history', [DatBanAnController::class, 'history']);

        // Chi tiết đơn đặt bàn
        Route::get('/dat-ban-an/{id}', [DatBanAnController::class, 'show']);

        // Hủy đơn đặt bàn
        Route::put('/dat-ban-an/{id}/cancel', [DatBanAnController::class, 'cancel']);
    });

    // ==================================================
    // 🛡️ ADMIN-ONLY ROUTES (role: admin)
    // ==================================================
    Route::middleware([RoleMiddleware::class . ':admin'])->group(function () {
        Route::get('/admin-only', function () {
            return response()->json(['message' => 'Chào mừng bạn đến khu vực Admin!']);
        });
    });
});
