<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\VnPayController;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DatBanAnController extends Controller
{
    
    public function history(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = Reservation::where('user_id', $user->id)
            ->with(['tables', 'menus']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('reservation_date', $request->date);
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        $reservations = $query->orderBy('id', 'desc')->paginate(10);

        $data = $reservations->map(function ($reservation) {
            return [
                'id' => $reservation->id,
                'reservation_date' => $reservation->reservation_date,
                'shift' => $reservation->shift,
                'shift_info' => $this->getShiftInfo($reservation->shift),
                'num_people' => $reservation->num_people,
                'depsection' => $reservation->depsection,
                'status' => $reservation->status,
                'status_text' => $this->getStatusText($reservation->status),
                'tables' => $reservation->tables->map(function ($table) {
                    return [
                        'id' => $table->id,
                        'name' => $table->name,
                    ];
                }),
                'tables_count' => $reservation->tables->count(),
                'menus' => $reservation->menus->map(function ($menu) {
                    return [
                        'id' => $menu->id,
                        'name' => $menu->name,
                        'price' => $menu->price,
                        'quantity' => $menu->pivot->quantity,
                        'total' => $menu->price * $menu->pivot->quantity,
                    ];
                }),
                'total_price' => $reservation->menus->sum(function ($menu) {
                    return $menu->price * $menu->pivot->quantity;
                }),
                'payment_token' => $reservation->payment_token,
                'payment_expires_at' => $reservation->payment_expires_at,
                'is_payment_expired' => $reservation->payment_expires_at 
                    ? Carbon::now()->greaterThan($reservation->payment_expires_at) 
                    : false,
                'created_at' => $reservation->created_at->format('d/m/Y H:i'),
                'updated_at' => $reservation->updated_at->format('d/m/Y H:i'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'total' => $reservations->total(),
                'per_page' => $reservations->perPage(),
                'current_page' => $reservations->currentPage(),
                'last_page' => $reservations->lastPage(),
                'from' => $reservations->firstItem(),
                'to' => $reservations->lastItem(),
            ],
        ], 200);
    }

  
    public function show(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $reservation = Reservation::with(['tables', 'menus', 'user'])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$reservation) {
            return response()->json([
                'error' => 'Không tìm thấy đơn đặt bàn',
                'message' => 'Đơn đặt bàn không tồn tại hoặc không thuộc về bạn.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $reservation->id,
                'reservation_date' => $reservation->reservation_date,
                'shift' => $reservation->shift,
                'shift_info' => $this->getShiftInfo($reservation->shift),
                'num_people' => $reservation->num_people,
                'depsection' => $reservation->depsection,
                'status' => $reservation->status,
                'status_text' => $this->getStatusText($reservation->status),
                'tables' => $reservation->tables->map(function ($table) {
                    return [
                        'id' => $table->id,
                        'name' => $table->name,
                    ];
                }),
                'tables_count' => $reservation->tables->count(),
                'menus' => $reservation->menus->map(function ($menu) {
                    return [
                        'id' => $menu->id,
                        'name' => $menu->name,
                        'price' => $menu->price,
                        'quantity' => $menu->pivot->quantity,
                        'total' => $menu->price * $menu->pivot->quantity,
                    ];
                }),
                'total_price' => $reservation->menus->sum(function ($menu) {
                    return $menu->price * $menu->pivot->quantity;
                }),
                'payment_token' => $reservation->payment_token,
                'payment_expires_at' => $reservation->payment_expires_at,
                'is_payment_expired' => $reservation->payment_expires_at 
                    ? Carbon::now()->greaterThan($reservation->payment_expires_at) 
                    : false,
                'payment_url' => $reservation->payment_token && $reservation->status === 'waiting_for_payment'
                    ? url("/api/payment/confirm/{$reservation->payment_token}")
                    : null,
                'created_at' => $reservation->created_at->format('d/m/Y H:i'),
                'updated_at' => $reservation->updated_at->format('d/m/Y H:i'),
            ],
        ], 200);
    }

  
    public function cancel(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $reservation = Reservation::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$reservation) {
            return response()->json([
                'error' => 'Không tìm thấy đơn đặt bàn',
                'message' => 'Đơn đặt bàn không tồn tại hoặc không thuộc về bạn.'
            ], 404);
        }

        // Chỉ cho phép hủy nếu trạng thái là waiting_for_payment hoặc confirmed
        if (!in_array($reservation->status, ['waiting_for_payment', 'confirmed'])) {
            return response()->json([
                'error' => 'Không thể hủy',
                'message' => 'Không thể hủy đơn đặt bàn đã hoàn tất hoặc đã bị hủy trước đó.'
            ], 400);
        }

        $reservation->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Đã hủy đơn đặt bàn thành công.',
            'reservation' => [
                'id' => $reservation->id,
                'status' => $reservation->status,
            ],
        ], 200);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'reservation_date' => 'required|date|after_or_equal:' . Carbon::today()->toDateString(),
            'shift' => 'required|in:morning,afternoon,evening,night',
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
            'shift.in' => 'Ca đặt bàn phải là: morning (sáng), afternoon (trưa), evening (chiều), night (tối).',
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

        $existingReservation = Reservation::where('user_id', $user->id)
            ->where('reservation_date', $request->reservation_date)
            ->where('shift', $request->shift)
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->first();

        if ($existingReservation) {
            return response()->json([
                'error' => 'Đã đặt bàn rồi',
                'message' => 'Bạn đã đặt bàn cho ca này rồi. Vui lòng chọn ca khác hoặc hủy đơn cũ.',
                'existing_reservation' => [
                    'id' => $existingReservation->id,
                    'date' => $existingReservation->reservation_date,
                    'shift' => $existingReservation->shift,
                    'status' => $existingReservation->status,
                ],
            ], 400);
        }

        
        $firstTable = \App\Models\BanAn::first();
        $peoplePerTable = $firstTable ? $firstTable->limit_number : 8;
        $tablesNeeded = (int) ceil($request->num_people / $peoplePerTable);

        // tim ban trong
        $availableTables = \App\Models\BanAn::whereDoesntHave('reservations', function ($query) use ($request) {
                $query->where('reservation_date', $request->reservation_date)
                      ->where('shift', $request->shift)
                      ->where('status', '!=', 'cancelled');
            })
            ->limit($tablesNeeded)
            ->get();

        if ($availableTables->count() < $tablesNeeded) {
            return response()->json([
                'error' => 'Không đủ bàn trống',
                'message' => "Cần {$tablesNeeded} bàn nhưng chỉ còn {$availableTables->count()} bàn trống vào ca này.",
                'shift_info' => $this->getShiftInfo($request->shift),
            ], 400);
        }

        // tao link thanh toan
        $paymentToken = Str::random(32);
        $paymentExpiresAt = Carbon::now()->addMinutes(10); // Hết hạn sau 10 phút

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'reservation_date' => $request->reservation_date,
            'shift' => $request->shift,
            'num_people' => $request->num_people,
            'depsection' => $request->depsection,
            'voucher_id' => $request->voucher_id,
            'status' => 'waiting_for_payment',
            'payment_token' => $paymentToken,
            'payment_expires_at' => $paymentExpiresAt,
        ]);

        // Gán bàn cho reservation
        foreach ($availableTables as $table) {
            $reservation->tables()->attach($table->id);
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
            'morning' => ['name' => 'Ca sáng', 'time' => '6:00 - 10:00'],
            'afternoon' => ['name' => 'Ca trưa', 'time' => '10:00 - 14:00'],
            'evening' => ['name' => 'Ca chiều', 'time' => '14:00 - 18:00'],
            'night' => ['name' => 'Ca tối', 'time' => '18:00 - 22:00'],
        ];

        return $shifts[$shift] ?? ['name' => 'Không xác định', 'time' => ''];
    }

 
    private function getStatusText($status)
    {
        $statuses = [
            'waiting_for_payment' => 'Chờ thanh toán',
            'confirmed' => 'Đã xác nhận',
            'completed' => 'Hoàn tất',
            'cancelled' => 'Đã hủy',
            'pending' => 'Chờ thanh toán',
        ];

        return $statuses[$status] ?? 'Không xác định';
    }
}
