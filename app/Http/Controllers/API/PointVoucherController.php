<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PointVoucherTier;
use App\Models\PointVoucherLog;
use App\Models\User;
use App\Models\Voucher;

class PointVoucherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    // Xem tất cả mức điểm có thể đổi voucher
    public function tiers()
    {
        $tiers = PointVoucherTier::where('is_active', true)->orderBy('points_required')->get();
        return response()->json([
            'status' => true,
            'message' => 'Danh sách mức điểm đổi voucher',
            'data' => $tiers
        ]);
    }

    // Đổi điểm ra voucher
    public function redeem(Request $request)
    {
        // 1. Validate tier_id
        $request->validate([
            'tier_id' => 'required|exists:point_voucher_tiers,id'
        ]);

        $user = Auth::user();
        $tier = PointVoucherTier::findOrFail($request->tier_id);

        // 2. Kiểm tra tier có active không
        if (!$tier->is_active) {
            return response()->json(['message' => 'Tier này hiện không hoạt động'], 400);
        }

        // 3. Kiểm tra user đủ điểm không
        if ($user->points < $tier->points_required) {
            return response()->json(['message' => 'Bạn không đủ điểm để đổi tier này'], 400);
        }

        // 4. Lấy voucher có sẵn của admin (chưa được gán)
        $voucher = Voucher::whereNull('user_id')
            ->where('status', 'active')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->where('discount_value', $tier->discount_percent) // Lọc theo tier nếu muốn
            ->first();

        if (!$voucher) {
            return response()->json(['message' => 'Hiện không còn voucher nào phù hợp để đổi'], 404);
        }

        // 5. Trừ điểm của user
        $user->points -= $tier->points_required;
        $user->save();

        // 6. Gán voucher cho user
        $voucher->user_id = $user->id;
        $voucher->save();

        // 7. Ghi log
        PointVoucherLog::create([
            'user_id' => $user->id,
            'voucher_id' => $voucher->id,
            'points_spent' => $tier->points_required
        ]);

        // 8. Phản hồi
        return response()->json([
            'status' => 'success',
            'message' => 'Đổi điểm thành công!',
            'data' => [
                'voucher' => $voucher,
                'remaining_points' => $user->points
            ]
        ]);
    }


    // Xem lịch sử tiêu điểm
    public function history()
    {
        $user = Auth::user();
        $logs = PointVoucherLog::with('voucher')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Lịch sử tiêu điểm',
            'data' => $logs
        ]);
    }
}
