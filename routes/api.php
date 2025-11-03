<?php

use App\Http\Controllers\Api\PointController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VoucherController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/vouchers/apply', [VoucherController::class, 'applyVoucher']);
Route::post('/points/add', [PointController::class, 'addPoints']);
Route::post('/points/use', [PointController::class, 'usePoints']);
Route::get('/points/{userId}', [PointController::class, 'getPoints']);