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
        $request->validate([
            'tier_id' => 'required|exists:point_voucher_tiers,id'
        ]);

        $user = Auth::user();
        $tier = PointVoucherTier::findOrFail($request->tier_id);

        if (!$tier->is_active) {
            return response()->json(['message' => 'Tier này hiện không hoạt động'], 400);
        }

        if ($user->points < $tier->points_required) {
            return response()->json(['message' => 'Bạn không đủ điểm để đổi tier này'], 400);
        }

        // Trừ điểm
        $user->points -= $tier->points_required;
        $user->save();

        // Tạo voucher mới cho user
        $voucher = Voucher::create([
            'code' => 'POINT-' . strtoupper(uniqid()),
            'discount_type' => 'percent',
            'discount_value' => $tier->discount_percent,
            'min_order_value' => $tier->min_order_value,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addMonth(), // ví dụ hết hạn sau 1 tháng
        ]);

        // Ghi log
        PointVoucherLog::create([
            'user_id' => $user->id,
            'voucher_id' => $voucher->id,
            'points_spent' => $tier->points_required
        ]);

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
            'status' => true,
            'message' => 'Lịch sử tiêu điểm',
            'data' => $logs
        ]);
    }
}
