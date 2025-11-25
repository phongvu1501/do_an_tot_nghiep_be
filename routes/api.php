<?php

use App\Http\Controllers\API\PointController;
use App\Http\Controllers\API\DatBanAnController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\MenuApiController;
use App\Http\Controllers\API\MenuCategoryApiController;
use App\Http\Controllers\Api\PointVoucherController;
use App\Http\Controllers\Api\RedemptionApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\API\VnPayController;
use App\Http\Controllers\API\ReviewApiController;
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\API\DepositRequiredDateController;

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

// Ngày yêu cầu đặt cọc 
Route::get('/deposit-required-dates', [DepositRequiredDateController::class, 'index']);
Route::post('/deposit-required-dates/check', [DepositRequiredDateController::class, 'check']);

// Không còn sử dụng - VNPay callback được xử lý bởi vnpayReturn
// Route::get('/payment/confirm/{token}', [DatBanAnController::class, 'confirmPayment']);

// VNPAY return route
Route::get('/vnpay-return', [VnPayController::class, 'vnpayReturn']);

// ======================================================
// 🔐 PROTECTED ROUTES (Cần token Sanctum)
// ======================================================
Route::middleware('auth:sanctum')->group(function () {

    // Thông tin người dùng hiện tại
    Route::get('/user', [AuthController::class, 'user']);

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin-only route
    Route::middleware([RoleMiddleware::class . ':admin'])->group(function () {
        Route::get('/admin-only', function () {
            return response()->json(['message' => 'Welcome Admin']);
        });
    });

    // User + Admin route
    Route::middleware([RoleMiddleware::class . ':user,admin'])->group(function () {
        Route::get('/profile', function () {
            return response()->json(['message' => 'Hello, this is your profile']);
        });
    });

    // Đặt bàn ăn
    Route::post('/dat-ban-an', [DatBanAnController::class, 'store']);

    //
    // Lịch sử đặt bàn
    Route::get('/dat-ban-an/history', [DatBanAnController::class, 'history']);

    // Danh sách đơn đặt bàn đang phục vụ 
    Route::get('/dat-ban-an/serving', [DatBanAnController::class, 'getServingReservations']);

    // Chi tiết đơn đặt bàn
    Route::get('/dat-ban-an/{id}', [DatBanAnController::class, 'show']);

    // Hủy đơn đặt bàn
    Route::put('/dat-ban-an/{id}/cancel', [DatBanAnController::class, 'cancel']);

    // order thêm món ăn vào đơn đặt bàn
    Route::post('/dat-ban-an/order-items', [OrderController::class, 'store']);

    // Xóa món hoặc giảm số lượng món khỏi đơn đặt bàn
    Route::delete('/dat-ban-an/{reservationId}/order-items', [OrderController::class, 'destroy']);
    // VNPAY Payment Routes
    Route::get('/payment', [VnPayController::class, 'createPayment']);

    // Danh sách đặt bàn có thể đánh giá
    Route::get('/reviewable', [ReviewApiController::class, 'index']);

    // Gửi / cập nhật đánh giá
    Route::post('/reservations/{reservation}/review', [ReviewApiController::class, 'store'])
        ->name('api.review.store');

    // Xem đánh giá
    Route::get('/reservations/{reservation}/review', [ReviewApiController::class, 'show']);

    // Cập nhật / xóa đánh giá
    Route::put('/reviews/{review}', [ReviewApiController::class, 'update']);
    Route::delete('/reviews/{review}', [ReviewApiController::class, 'destroy']);

    // // Áp dụng voucher 
    Route::post('/vouchers/apply', [VoucherController::class, 'applyVoucher']);
    Route::get('/vouchers/getAllVouchers', [VoucherController::class, 'getAllVouchers']);

    // // Tích điểm đổi voucher 
    Route::get('/redeem/tiers', [RedemptionApiController::class, 'getTiers']);
    Route::post('/redeem/exchange', [RedemptionApiController::class, 'exchange']);
    Route::get('/point-voucher/tiers', [PointVoucherController::class, 'tiers']);
    Route::post('/point-voucher/redeem', [PointVoucherController::class, 'redeem']);
    Route::get('/point-voucher/history', [PointVoucherController::class, 'history']);

    // // Tích điểm nội bộ 
    Route::post('/points/add', [PointController::class, 'addPoints']);
    Route::get('/points/history', [PointController::class, 'history']);
});
