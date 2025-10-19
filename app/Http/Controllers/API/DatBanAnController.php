<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            'reservation_time' => 'required|date_format:H:i',
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
            'reservation_time.required' => 'Giờ đặt bàn là bắt buộc.',
            'reservation_time.date_format' => 'Giờ đặt bàn phải có định dạng H:i (ví dụ: 14:30).',
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

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'reservation_date' => $request->reservation_date,
            'reservation_time' => $request->reservation_time,
            'num_people' => $request->num_people,
            'depsection' => $request->depsection,
            'voucher_id' => $request->voucher_id,
        ]);

        // Nếu người dùng có chọn món -> lưu vào bảng trung gian
        if ($request->has('menus')) {
            foreach ($request->menus as $menuItem) {
                $reservation->menus()->attach($menuItem['menu_id'], [
                    'quantity' => $menuItem['quantity'],
                ]);
            }
        }

        return response()->json([
            'message' => 'Đặt bàn thành công!',
            'reservation' => $reservation->load('menus'),
        ], 201);
    }
}
