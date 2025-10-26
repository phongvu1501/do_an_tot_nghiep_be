<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DatBanAnController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'reservation_date' => 'required|date|after_or_equal:' . Carbon::today()->toDateString(),
            'shift' => 'required|in:morning,afternoon,evening',
            'num_people' => 'required|integer|min:1',
            'depsection' => 'nullable|string|max:255',
            'voucher_id' => 'nullable|exists:vouchers,id',
            'menus' => 'nullable|array',
            'menus.*.menu_id' => 'required_with:menus|exists:menus,id',
            'menus.*.quantity' => 'required_with:menus|integer|min:1',
        ], [
            'reservation_date.required' => 'Ngày đặt bàn là bắt buộc.',
            'reservation_date.date' => 'Ngày đặt bàn không hợp lệ.',
            'reservation_date.after_or_equal' => 'Ngày đặt bàn không thể là ngày quá khứ.',
            'shift.required' => 'Ca đặt bàn là bắt buộc.',
            'shift.in' => 'Ca đặt bàn phải là: morning (sáng), afternoon (trưa), evening (tối).',
            'num_people.required' => 'Số lượng người là bắt buộc.',
            'num_people.integer' => 'Số lượng người phải là số nguyên.',
            'num_people.min' => 'Số lượng người phải tối thiểu 1.',
            'depsection.string' => 'Mã khu vực phải là một chuỗi ký tự.',
            'depsection.max' => 'Mã khu vực không được vượt quá 255 ký tự.',
            'voucher_id.exists' => 'Mã voucher không hợp lệ.',
            'menus.array' => 'Danh sách món ăn không hợp lệ.',
            'menus.*.menu_id.required_with' => 'Món ăn không thể thiếu khi chọn thực đơn.',
            'menus.*.menu_id.exists' => 'Món ăn không tồn tại.',
            'menus.*.quantity.required_with' => 'Số lượng món ăn không thể thiếu khi chọn thực đơn.',
            'menus.*.quantity.integer' => 'Số lượng món ăn phải là một số nguyên.',
            'menus.*.quantity.min' => 'Số lượng món ăn phải tối thiểu 1.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Tính số bàn cần thiết (lấy limit_number từ bàn đầu tiên, mặc định 8)
        $firstTable = \App\Models\BanAn::first();
        $peoplePerTable = $firstTable ? $firstTable->limit_number : 8;
        $tablesNeeded = (int) ceil($request->num_people / $peoplePerTable);

        // Tìm bàn trống theo ngày và ca đặt
        $availableTables = \App\Models\BanAn::where('status', 'active')
            ->whereDoesntHave('reservations', function ($query) use ($request) {
                $query->where('reservation_date', $request->reservation_date)
                      ->where('shift', $request->shift)
                      ->where('status', '!=', 'cancelled');
            })
            ->limit($tablesNeeded)
            ->get();

        // Kiểm tra có đủ bàn không
        if ($availableTables->count() < $tablesNeeded) {
            return response()->json([
                'error' => 'Không đủ bàn trống',
                'message' => "Cần {$tablesNeeded} bàn nhưng chỉ còn {$availableTables->count()} bàn trống vào ca này.",
                'shift_info' => $this->getShiftInfo($request->shift),
            ], 400);
        }

        // Tạo payment token (link thanh toán giả)
        $paymentToken = Str::random(32);
        $paymentExpiresAt = Carbon::now()->addMinutes(10); // Hết hạn sau 10 phút

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'reservation_date' => $request->reservation_date,
            'shift' => $request->shift,
            'num_people' => $request->num_people,
            'depsection' => $request->depsection,
            'voucher_id' => $request->voucher_id,
            'status' => 'waiting_payment', // Trạng thái chờ thanh toán
            'payment_token' => $paymentToken,
            'payment_expires_at' => $paymentExpiresAt,
        ]);

        // Gán bàn cho reservation
        foreach ($availableTables as $table) {
            $reservation->tables()->attach($table->id, [
                'user_id' => $user->id,
            ]);
        }

        if ($request->has('menus')) {
            foreach ($request->menus as $menuItem) {
                $reservation->menus()->attach($menuItem['menu_id'], [
                    'quantity' => $menuItem['quantity'],
                ]);
            }
        }

        // tao limk thanh toan
        $paymentUrl = url("/api/payment/confirm/{$paymentToken}");

        return response()->json([
            'success' => true,
            'message' => 'Đặt bàn thành công! Vui lòng thanh toán trong 10 phút.',
            'payment_url' => $paymentUrl,
            'payment_expires_at' => $paymentExpiresAt->toDateTimeString(),
            'shift_info' => $this->getShiftInfo($request->shift),
            'tables_assigned' => $availableTables->map(function($table) {
                return [
                    'id' => $table->id,
                    'name' => $table->name,
                ];
            }),
            'tables_count' => $tablesNeeded,
            'reservation' => $reservation->load(['menus', 'tables']),
        ], 201);
    }

  
    public function confirmPayment($token)
    {
        $reservation = Reservation::where('payment_token', $token)->first();

        if (!$reservation) {
            return response()->json([
                'error' => 'Link thanh toán không hợp lệ'
            ], 404);
        }

        if (Carbon::now()->greaterThan($reservation->payment_expires_at)) {
            $reservation->update(['status' => 'cancelled']);
            
            return response()->json([
                'error' => 'Link thanh toán đã hết hạn (quá 10 phút)',
                'message' => 'Đơn đặt bàn đã bị hủy. Vui lòng đặt lại.'
            ], 400);
        }

        if ($reservation->status == 'confirmed') {
            return response()->json([
                'success' => true,
                'message' => 'Đơn đặt bàn này đã được thanh toán rồi.'
            ], 200);
        }

        $reservation->update([
            'status' => 'confirmed',
        ]);

        $reservation->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Thanh toán thành công! Đơn đặt bàn đã được xác nhận.',
            'reservation' => $reservation->load(['tables', 'user']),
        ], 200);
    }

    /**
     * Lấy thông tin ca
     */
    private function getShiftInfo($shift)
    {
        $shifts = [
            'morning' => ['name' => 'Ca sáng', 'time' => '6:00 - 11:00'],
            'afternoon' => ['name' => 'Ca trưa', 'time' => '11:00 - 14:00'],
            'evening' => ['name' => 'Ca tối', 'time' => '17:00 - 22:00'],
        ];

        return $shifts[$shift] ?? ['name' => 'Không xác định', 'time' => ''];
    }
}
