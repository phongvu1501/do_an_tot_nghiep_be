<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $discountAmount = 0;
        if ($voucher->discount_type === 'percent') {
            $discountAmount = ($voucher->discount_value / 100) * $request->order_total;
            if ($voucher->order_value_allowed && $discountAmount > $voucher->order_value_allowed) {
                $discountAmount = $voucher->order_value_allowed;
            }
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

    public function getAllVouchers(Request $request)
    {
        $query = Voucher::query();

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $query->orderBy('created_at', 'desc');

        $vouchers = $query->paginate($request->get('limit', 10));

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy danh sách voucher thành công.',
            'data' => $vouchers
        ]);
    }
    public function getUserVouchers(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Người dùng không tồn tại.',
            ], 404);
        }

        $vouchers = $user->vouchers()
            ->where('vouchers.status', 'active') 
            ->orderBy('vouchers.created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách voucher của người dùng thành công.',
            'data' => $vouchers
        ]);
    }


    /**
     * Lấy danh sách voucher có thể áp dụng cho reservation khi thanh toán
     * Kiểm tra đầy đủ các điều kiện: user_id, min_order_value, order_value_allowed, 
     * start_date, end_date, status, used_count, max_uses, max_discount_value
     */
    public function getApplicableVouchersForReservation(Request $request, $reservationId)
    {
        $reservation = Reservation::with(['reservationItems.menu', 'user', 'voucher'])->findOrFail($reservationId);

        // Tính tổng tiền của reservation (sau VAT)
        $subtotal = $reservation->reservationItems->sum(fn($item) => $item->price * $item->quantity);
        $vat = $subtotal * 0.1;
        $totalPrice = $subtotal + $vat;

        $userId = $reservation->user_id;
        $now = Carbon::now();

        // Lấy tất cả voucher của user hoặc voucher chung (user_id = null)
        $vouchers = Voucher::with('tier')
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhereNull('user_id');
            })
            ->where('status', 'active')
            ->get();

        $applicableVouchers = [];

        foreach ($vouchers as $voucher) {
            $errors = [];
            $isApplicable = true;

            // 1. Kiểm tra ngày hiệu lực
            if ($voucher->start_date && $now->lt($voucher->start_date)) {
                $errors[] = 'Voucher chưa đến thời gian sử dụng';
                $isApplicable = false;
            }

            if ($voucher->end_date && $now->gt($voucher->end_date)) {
                $errors[] = 'Voucher đã hết hạn';
                $isApplicable = false;
            }

            // 2. Kiểm tra số lần sử dụng
            if ($voucher->used_count >= $voucher->max_uses) {
                $errors[] = 'Voucher đã đạt giới hạn sử dụng';
                $isApplicable = false;
            }

            // 3. Kiểm tra min_order_value
            if ($voucher->min_order_value && $totalPrice < $voucher->min_order_value) {
                $errors[] = 'Đơn hàng chưa đạt giá trị tối thiểu: ' . number_format($voucher->min_order_value, 0, ',', '.') . ' VNĐ';
                $isApplicable = false;
            }

            // 4. Kiểm tra xem voucher đã được áp dụng vào reservation này chưa
            if ($reservation->voucher_id == $voucher->id) {
                $errors[] = 'Voucher đã được áp dụng vào đơn này';
                $isApplicable = false;
            }

            // Tính toán discount (chỉ tính khi có thể áp dụng)
            $discountAmount = 0;
            $maxDiscountValue = null;

            if ($isApplicable) {
                if ($voucher->discount_type === 'percent') {
                    $discountAmount = ($voucher->discount_value / 100) * $totalPrice;
                    // Áp dụng giới hạn giá trị giảm tối đa (chỉ cho giảm theo %)
                    if ($voucher->order_value_allowed && $discountAmount > $voucher->order_value_allowed) {
                        $discountAmount = $voucher->order_value_allowed;
                    }
                } else {
                    $discountAmount = $voucher->discount_value;
                }

                // Kiểm tra max_discount_value từ tier
                if ($voucher->tier && $voucher->tier->max_discount_value) {
                    $maxDiscountValue = $voucher->tier->max_discount_value;
                    if ($discountAmount > $maxDiscountValue) {
                        $discountAmount = $maxDiscountValue;
                    }
                }

                // Đảm bảo discount không vượt quá total
                if ($discountAmount > $totalPrice) {
                    $discountAmount = $totalPrice;
                }
            }

            $finalAmount = max(0, $totalPrice - $discountAmount);

            $applicableVouchers[] = [
                'id' => $voucher->id,
                'code' => $voucher->code,
                'discount_type' => $voucher->discount_type,
                'discount_value' => $voucher->discount_value,
                'min_order_value' => $voucher->min_order_value,
                'order_value_allowed' => $voucher->order_value_allowed,
                'start_date' => $voucher->start_date,
                'end_date' => $voucher->end_date,
                'max_uses' => $voucher->max_uses,
                'used_count' => $voucher->used_count,
                'max_discount_value' => $maxDiscountValue,
                'is_applicable' => $isApplicable,
                'errors' => $errors,
                'discount_amount' => round($discountAmount, 0),
                'total_price' => round($totalPrice, 0),
                'final_amount' => round($finalAmount, 0),
            ];
        }

        // Tách thành 2 nhóm: có thể áp dụng và không thể áp dụng
        $canApply = collect($applicableVouchers)->where('is_applicable', true)->values();
        $cannotApply = collect($applicableVouchers)->where('is_applicable', false)->values();

        return response()->json([
            'success' => true,
            'data' => [
                'can_apply' => $canApply,
                'cannot_apply' => $cannotApply,
                'total_price' => round($totalPrice, 0),
                'current_voucher' => $reservation->voucher ? [
                    'id' => $reservation->voucher->id,
                    'code' => $reservation->voucher->code,
                    'discount' => $reservation->voucher_discount ?? 0,
                ] : null,
            ],
        ], 200);
    }

    /**
     * Áp dụng voucher vào reservation khi thanh toán
     */
    public function applyVoucherToReservation(Request $request, $reservationId)
    {
        $request->validate([
            'voucher_id' => 'required|exists:vouchers,id',
        ]);

        $reservation = Reservation::with(['reservationItems.menu', 'user'])->findOrFail($reservationId);

        // Tính tổng tiền của reservation (sau VAT)
        $subtotal = $reservation->reservationItems->sum(fn($item) => $item->price * $item->quantity);
        $vat = $subtotal * 0.1;
        $totalPrice = $subtotal + $vat;

        $voucher = Voucher::with('tier')->findOrFail($request->voucher_id);
        $now = Carbon::now();

        // Kiểm tra tất cả điều kiện
        // 1. User phải match
        if ($voucher->user_id && $voucher->user_id != $reservation->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher không thuộc về khách hàng này',
            ], 400);
        }

        // 2. Kiểm tra status
        if ($voucher->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Voucher không còn hiệu lực',
            ], 400);
        }

        // 3. Kiểm tra ngày hiệu lực
        if ($voucher->start_date && $now->lt($voucher->start_date)) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher chưa đến thời gian sử dụng',
            ], 400);
        }

        if ($voucher->end_date && $now->gt($voucher->end_date)) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher đã hết hạn',
            ], 400);
        }

        // 4. Kiểm tra số lần sử dụng
        if ($voucher->used_count >= $voucher->max_uses) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher đã đạt giới hạn sử dụng',
            ], 400);
        }

        // 5. Kiểm tra min_order_value
        if ($voucher->min_order_value && $totalPrice < $voucher->min_order_value) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng chưa đạt giá trị tối thiểu: ' . number_format($voucher->min_order_value, 0, ',', '.') . ' VNĐ',
            ], 400);
        }

        // Tính toán discount
        $discountAmount = 0;
        if ($voucher->discount_type === 'percent') {
            $discountAmount = ($voucher->discount_value / 100) * $totalPrice;
            // Áp dụng giới hạn giá trị giảm tối đa (chỉ cho giảm theo %)
            if ($voucher->order_value_allowed && $discountAmount > $voucher->order_value_allowed) {
                $discountAmount = $voucher->order_value_allowed;
            }
        } else {
            $discountAmount = $voucher->discount_value;
        }

        // Áp dụng max_discount_value từ tier nếu có
        if ($voucher->tier && $voucher->tier->max_discount_value) {
            if ($discountAmount > $voucher->tier->max_discount_value) {
                $discountAmount = $voucher->tier->max_discount_value;
            }
        }

        // Đảm bảo discount không vượt quá total
        if ($discountAmount > $totalPrice) {
            $discountAmount = $totalPrice;
        }

        $finalAmount = max(0, $totalPrice - $discountAmount);

        try {
            DB::beginTransaction();

            // Cập nhật reservation với voucher
            $reservation->voucher_id = $voucher->id;
            $reservation->voucher_discount = round($discountAmount, 0);
            $reservation->total_amount = round($finalAmount, 0);
            $reservation->save();

            // Tăng số lần sử dụng trong bảng vouchers
            $voucher->increment('used_count');

            // Cập nhật số lần sử dụng trong bảng pivot user_voucher
            $pivot = DB::table('user_voucher')
                ->where('user_id', $reservation->user_id)
                ->where('voucher_id', $voucher->id)
                ->first();

            if ($pivot) {
                $newUsedCount = ($pivot->used_count ?? 0) + 1;
                DB::table('user_voucher')
                    ->where('user_id', $reservation->user_id)
                    ->where('voucher_id', $voucher->id)
                    ->update([
                        'used_count' => $newUsedCount,
                        'status' => ($newUsedCount >= $voucher->max_uses) ? 'used' : 'unused',
                        'used_at' => now(),
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Áp dụng voucher thành công',
                'data' => [
                    'voucher' => [
                        'id' => $voucher->id,
                        'code' => $voucher->code,
                        'discount_type' => $voucher->discount_type,
                        'discount_value' => $voucher->discount_value,
                    ],
                    'discount_amount' => round($discountAmount, 0),
                    'total_price' => round($totalPrice, 0),
                    'final_amount' => round($finalAmount, 0),
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi áp dụng voucher: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hủy áp dụng voucher khỏi reservation
     */
    public function removeVoucherFromReservation(Request $request, $reservationId)
    {
        $reservation = Reservation::with(['reservationItems.menu'])->findOrFail($reservationId);

        if (!$reservation->voucher_id) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation không có voucher được áp dụng',
            ], 400);
        }

        $voucher = Voucher::find($reservation->voucher_id);

        try {
            DB::beginTransaction();

            // Tính lại total_amount không có voucher
            $subtotal = $reservation->reservationItems->sum(fn($item) => $item->price * $item->quantity);
            $vat = $subtotal * 0.1;
            $totalPrice = $subtotal + $vat;

            // Cập nhật reservation
            $reservation->voucher_id = null;
            $reservation->voucher_discount = 0;
            $reservation->total_amount = round($totalPrice, 0);
            $reservation->save();

            // Giảm số lần sử dụng trong bảng vouchers
            if ($voucher && $voucher->used_count > 0) {
                $voucher->decrement('used_count');
            }

            // Giảm số lần sử dụng trong bảng pivot user_voucher
            $pivot = DB::table('user_voucher')
                ->where('user_id', $reservation->user_id)
                ->where('voucher_id', $voucher->id)
                ->first();

            if ($pivot && $pivot->used_count > 0) {
                $newUsedCount = $pivot->used_count - 1;
                DB::table('user_voucher')
                    ->where('user_id', $reservation->user_id)
                    ->where('voucher_id', $voucher->id)
                    ->update([
                        'used_count' => $newUsedCount,
                        'status' => ($newUsedCount > 0 && $newUsedCount >= $voucher->max_uses) ? 'used' : 'unused',
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã hủy áp dụng voucher',
                'data' => [
                    'total_price' => round($totalPrice, 0),
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi hủy voucher: ' . $e->getMessage(),
            ], 500);
        }
    }
}
