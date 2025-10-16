<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
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
            
            'reservation_date' => 'required|date',
            'reservation_time' => 'required|date_format:H:i',
            'num_people' => 'required|integer|min:1',
            'depsection' => 'nullable|string|max:255',
            'voucher_id' => 'nullable|exists:vouchers,id',
            'menus' => 'nullable|array',
            'menus.*.menu_id' => 'required_with:menus|exists:menus,id',
            'menus.*.quantity' => 'required_with:menus|integer|min:1',
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
