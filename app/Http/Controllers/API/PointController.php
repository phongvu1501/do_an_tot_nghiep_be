<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\PointLog;
use App\Models\Reservation;
use App\Models\Voucher;

class PointController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    // Tích điểm khi thanh toán thành công
    public function addPoints(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $reservation = Reservation::where('user_id', $user->id)
            ->where('status', 'completed')
            ->latest()
            ->first();

        if (!$reservation) {
            return response()->json(['error' => 'No completed reservation found'], 404);
        }

        $existingLog = PointLog::where('user_id', $user->id)
            ->where('reservation_id', $reservation->id)
            ->first();

        if ($existingLog) {
            return response()->json([
                'error' => 'Points already added for this reservation',
                'reservation_id' => $reservation->id
            ], 400);
        }

        $subtotal = $reservation->reservationItems->sum(fn($i) => $i->price * $i->quantity);
        $vat = $subtotal * 0.1;
        $total_price = $subtotal + $vat;

        $discount_value = 0;
        $voucher = null;

        if ($reservation->voucher_id) {
            $voucher = Voucher::find($reservation->voucher_id);

            if ($voucher && $voucher->status === 'active') {

                if ($voucher->min_order_value && $total_price >= $voucher->min_order_value) {

                    if ($voucher->discount_type === 'percent') {
                        $discount_value = ($total_price * $voucher->discount_value) / 100;
                        if ($voucher->order_value_allowed && $discount_value > $voucher->order_value_allowed) {
                            $discount_value = $voucher->order_value_allowed;
                        }
                    } else {
                        $discount_value = $voucher->discount_value;
                    }
                }
            }
        }

        $final_amount = $total_price - $discount_value;
        if ($final_amount < 0) $final_amount = 0;

        if ($reservation->total_amount != $final_amount) {
            $reservation->total_amount = $final_amount;
            $reservation->save();
        }

        $totalAmount = $final_amount + $user->points_balance;

        $pointsToAdd = floor($totalAmount / 10000);

        $user->points_balance = $totalAmount - ($pointsToAdd * 10000);

        if ($pointsToAdd > 0) {
            $user->points += $pointsToAdd;
        }

        $user->save();

        // Ghi log
        PointLog::create([
            'user_id' => $user->id,
            'reservation_id' => $reservation->id,
            'points' => $pointsToAdd,
            'description' => "Tích điểm từ đơn thanh toán {$final_amount}đ"
        ]);

        return response()->json([
            'success' => true,
            'message' => "Đã cộng {$pointsToAdd} điểm cho tài khoản.",
            'points_added' => $pointsToAdd,
            'total_points' => $user->points,
            'points_balance' => $user->points_balance,
            'amount_used_for_points' => $final_amount,
            'voucher_discount' => $discount_value,
            'reservation_id' => $reservation->id
        ]);
    }




    /**
     * API: Admin cộng điểm cho người dùng khác
     */
    public function adminAddPoints(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:1',
            'action' => 'nullable|string',
        ]);

        $user = User::find($validated['user_id']);
        $user->increment('points', $validated['points']);

        PointLog::create([
            'user_id' => $user->id,
            'points' => $validated['points'],
            'action' => $validated['action'] ?? 'Thưởng điểm bởi admin',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Admin đã cộng điểm cho người dùng thành công!',
            'data' => [
                'username' => $user->username ?? $user->email,
                'total_points' => $user->points,
            ],
        ]);
    }

    /**
     * API: Lịch sử tích điểm
     */
    public function history()
    {
        $user = Auth::user();
        $logs = PointLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Lịch sử tích điểm của bạn',
            'data' => $logs,
        ]);
    }
    /**
     * API: Điểm hiện có của người dùng
     */
    public function userPoints(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn chưa đăng nhập',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Điểm hiện có của bạn',
            'data' => [
                'points' => $user->points ?? 0,
                'points_balance' => $user->points_balance ?? 0,
            ],
        ]);
    }
}
