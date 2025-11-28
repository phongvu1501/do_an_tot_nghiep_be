<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
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

        \DB::beginTransaction();

        try {
            // 1. Trừ điểm
            $user->points -= $tier->points_required;
            $user->save();

            // 2. Sinh voucher mới từ Tier
            $voucher = Voucher::create([
                'code' => strtoupper('VC-' . uniqid()),
                'discount_type' => 'percent',
                'discount_value' => $tier->discount_percent,
                'max_discount_value' => $tier->max_discount_value,
                'min_order_value' => $tier->min_order_value,
                'order_value_allowed' => $tier->order_value_allowed,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'status' => 'active',
                'user_id' => $user->id,
            ]);

            // 3. Log lịch sử đổi điểm
            PointVoucherLog::create([
                'user_id' => $user->id,
                'voucher_id' => $voucher->id,
                'points_spent' => $tier->points_required
            ]);

            \DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Đổi điểm thành công!',
                'data' => [
                    'voucher' => $voucher,
                    'remaining_points' => $user->points
                ]
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
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
