<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function applyVoucher(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'order_total' => 'required|numeric|min:0',
        ]);

        $voucher = Voucher::where('code', $request->code)
            ->where('status', 'active')
            ->first();

        if (!$voucher) {
            return response()->json(['message' => 'Voucher không tồn tại hoặc không hoạt động'], 404);
        }

        $today = now();
        if ($today->lt($voucher->start_date) || $today->gt($voucher->end_date)) {
            return response()->json(['message' => 'Voucher đã hết hạn hoặc chưa bắt đầu'], 400);
        }

        if ($voucher->min_order_value && $request->order_total < $voucher->min_order_value) {
            return response()->json(['message' => 'Đơn hàng chưa đạt giá trị tối thiểu để áp dụng voucher'], 400);
        }

        if ($voucher->used_count >= $voucher->max_uses) {
            return response()->json(['message' => 'Voucher đã đạt giới hạn sử dụng'], 400);
        }
        if (!is_null($voucher->order_value_allowed)) {
            if ($request->order_total > $voucher->order_value_allowed) {
                return response()->json([
                    'message' => 'Voucher này chỉ áp dụng cho đơn hàng có giá trị từ '
                        . number_format($voucher->order_value_allowed, 0, ',', '.')
                        . ' VNĐ trở xuống',
                ], 400);
            }
        }

        $discountAmount = 0;
        if ($voucher->discount_type === 'percent') {
            $discountAmount = ($voucher->discount_value / 100) * $request->order_total;
        } else {
            $discountAmount = $voucher->discount_value;
        }

        if ($discountAmount > $request->order_total) {
            $discountAmount = $request->order_total;
        }

        $finalTotal = $request->order_total - $discountAmount;

        $voucher->increment('used_count');

        return response()->json([
            'voucher_code'    => $voucher->code,
            'discount_type'   => $voucher->discount_type,
            'discount_value'  => $voucher->discount_value,
            'order_total'     => $request->order_total,
            'discount_amount' => round($discountAmount, 0),
            'final_total'     => round($finalTotal, 0),
            'message'         => 'Áp dụng voucher thành công',
        ]);
    }
}
