<?php

use App\Http\Controllers\API\DatBanAnController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Also expose the same endpoints under /api/auth/* for clients that expect
// an "auth" prefix (keeps backwards compatibility with plain /api/* routes).
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });
});

// Keep top-level protected routes too (optional)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // đặt bàn ăn
    Route::post('/dat-ban-an', [DatBanAnController::class, 'store']);
});
