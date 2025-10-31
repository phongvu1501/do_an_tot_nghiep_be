<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController; // ✅ AuthController có JWT
use App\Http\Controllers\API\DatBanAnController;
use App\Http\Controllers\Api\MenuApiController;
use App\Http\Controllers\API\MenuCategoryApiController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Middleware\RoleMiddleware;

// ======================================================
// 🔓 PUBLIC ROUTES (Không cần token)
// ======================================================

// // Đăng ký & Đăng nhập
// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);

// Quên / đặt lại mật khẩu
Route::post('/forgot-password', [PasswordResetController::class, 'forgot']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);

// Menu và danh mục (cho tất cả)
Route::get('/menu-categories', [MenuCategoryApiController::class, 'index']);
Route::get('/menus', [MenuApiController::class, 'index']);

// ======================================================
// 🔐 PROTECTED ROUTES (Cần token JWT hợp lệ)
// ======================================================
Route::middleware('auth:api')->group(function () {

    // // 🧑‍💻 Thông tin người dùng đang đăng nhập
    // Route::get('/me', [AuthController::class, 'me']);

    // // 🔁 Làm mới token
    // Route::post('/refresh', [AuthController::class, 'refresh']);

    // // 🚪 Đăng xuất
    // Route::post('/logout', [AuthController::class, 'logout']);

    // 🍽️ Đặt bàn ăn (user hoặc admin đều dùng được)
    Route::post('/dat-ban-an', [DatBanAnController::class, 'store']);

    // ==================================================
    // 👥 USER & ADMIN ROUTES (role: user,admin)
    // ==================================================
    Route::middleware([RoleMiddleware::class . ':user,admin'])->group(function () {
        Route::get('/profile', function () {
            return response()->json(['message' => 'Xin chào, đây là trang hồ sơ của bạn.']);
        });
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
