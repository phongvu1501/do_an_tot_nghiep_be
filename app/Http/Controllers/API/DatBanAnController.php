<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\VnPayController;
use App\Http\Controllers\Controller;
use App\Models\DepositRequiredDate;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Voucher;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DatBanAnController extends Controller
{
    const DEPOSIT_PER_TABLE = 300000; // 300 nghìn đồng
    const BANK_CODE = 'NCB';

    public function history(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = Reservation::where('user_id', $user->id)
            ->with(['tables', 'reservationItems.menu', 'voucher.tier']);

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

            $subtotal = $reservation->reservationItems->sum(
                fn($item) =>
                $item->price * $item->quantity
            );

            $vat = $subtotal * 0.1;
            $total_price = $subtotal + $vat;

            // Ưu tiên dùng voucher_discount đã lưu, nếu không có thì tính lại
            $voucher = $reservation->voucher;
            $discount_value = $reservation->voucher_discount ?? 0;

            // Nếu không có voucher_discount đã lưu nhưng có voucher_id, tính lại
            if ($reservation->voucher_id && !$reservation->voucher_discount && $voucher) {
                if ($voucher->status === 'active') {
                    if (!$voucher->min_order_value || $total_price >= $voucher->min_order_value) {
                        if ($voucher->discount_type === 'percent') {
                            $discount_value = ($total_price * $voucher->discount_value) / 100;
                            // Áp dụng giới hạn giá trị giảm tối đa (chỉ cho giảm theo %)
                            if ($voucher->order_value_allowed && $discount_value > $voucher->order_value_allowed) {
                                $discount_value = $voucher->order_value_allowed;
                            }
                        } else {
                            $discount_value = floatval($voucher->discount_value);
                        }
                    }
                }
            }

            $final_amount = $total_price - $discount_value;
            if ($final_amount < 0) $final_amount = 0;

            return [
                'id' => $reservation->id,
                'reservation_date' => $reservation->reservation_date,
                'shift' => $reservation->shift,
                'shift_info' => $this->getShiftInfo($reservation->shift),
                'num_people' => $reservation->num_people,
                'depsection' => $reservation->depsection,
                'status' => $reservation->status,
                'status_text' => $this->getStatusText($reservation->status),

                'tables' => $reservation->tables->map(fn($table) => [
                    'id' => $table->id,
                    'name' => $table->name,
                ]),
                'tables_count' => $reservation->tables->count(),

                'menus' => $reservation->reservationItems->map(fn($item) => [
                    'id' => $item->menu->id,
                    'name' => $item->menu->name,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'total' => $item->price * $item->quantity,
                ]),

                'subtotal' => $subtotal,
                'vat' => $vat,
                'total_price' => $total_price,

                'voucher' => $voucher ? [
                    'id' => $voucher->id,
                    'code' => $voucher->code,
                    'discount_type' => $voucher->discount_type,
                    'discount_value' => $voucher->discount_value,
                    'min_order_value' => $voucher->min_order_value,
                    'order_value_allowed' => $voucher->order_value_allowed,
                    'max_discount_value' => $voucher->tier ? $voucher->tier->max_discount_value : null,
                    'status' => $voucher->status,
                ] : null,

                'voucher_discount' => $discount_value,
                'final_amount' => $final_amount,

                'deposit' => $reservation->deposit,
                'table_deposit' => $reservation->getTableDeposit(),
                'food_deposit' => $reservation->getFoodDeposit(),
                'payment_url' => $reservation->payment_url,
                'reservation_code' => $reservation->reservation_code,
                'cancellation_reason' => $reservation->cancellation_reason,
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

        $reservation = Reservation::with(['tables', 'reservationItems.menu', 'user', 'voucher.tier'])
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
                'menus' => $reservation->reservationItems->map(function ($item) {
                    return [
                        'id' => $item->menu->id,
                        'name' => $item->menu->name,
                        'price' => $item->price,
                        'quantity' => $item->quantity,
                        'total' => $item->price * $item->quantity,
                    ];
                }),
                'subtotal' => $reservation->reservationItems->sum(function ($item) {
                    return $item->price * $item->quantity;
                }),
                'vat' => $reservation->reservationItems->sum(function ($item) {
                    return $item->price * $item->quantity;
                }) * 0.1,
                'total_price' => $reservation->reservationItems->sum(function ($item) {
                    return $item->price * $item->quantity;
                }) * 1.1,
                'voucher' => $reservation->voucher ? [
                    'id' => $reservation->voucher->id,
                    'code' => $reservation->voucher->code,
                    'discount_type' => $reservation->voucher->discount_type,
                    'discount_value' => $reservation->voucher->discount_value,
                    'min_order_value' => $reservation->voucher->min_order_value,
                    'order_value_allowed' => $reservation->voucher->order_value_allowed,
                    'max_discount_value' => $reservation->voucher->tier ? $reservation->voucher->tier->max_discount_value : null,
                    'status' => $reservation->voucher->status,
                ] : null,
                'voucher_discount' => $reservation->voucher_discount ?? 0,
                'final_amount' => ($reservation->reservationItems->sum(function ($item) {
                    return $item->price * $item->quantity;
                }) * 1.1) - ($reservation->voucher_discount ?? 0),
                'deposit' => $reservation->deposit,
                'table_deposit' => $reservation->getTableDeposit(),
                'food_deposit' => $reservation->getFoodDeposit(),
                'payment_url' => $reservation->payment_url,
                'reservation_code' => $reservation->reservation_code,
                'cancellation_reason' => $reservation->cancellation_reason,
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

        if (!in_array($reservation->status, ['pending', 'deposit_pending', 'deposit_paid'])) {
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
            'shift' => 'required|in:morning,afternoon,evening',
            'num_people' => 'required|integer|min:1',
            'depsection' => 'nullable|string|max:255',
            'voucher_id' => 'nullable|string',
            'prefer_vip' => 'nullable|boolean',
            'menus' => 'nullable|array',
            'menus.*.menu_id' => 'required_with:menus|exists:menus,id',
            'menus.*.quantity' => 'required_with:menus|integer|min:1',
        ], [
            'reservation_date.required' => 'Ngày đặt bàn là bắt buộc.',
            'reservation_date.date' => 'Ngày đặt bàn không hợp lệ.',
            'reservation_date.after_or_equal' => 'Ngày đặt bàn không thể là ngày quá khứ.',
            'shift.required' => 'Ca đặt bàn là bắt buộc.',
            'shift.in' => 'Ca đặt bàn phải là: morning (sáng 8h-13h), afternoon (trưa 13h-18h), evening (tối 18h-23h).',
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

        $voucherId = null;
        if (!empty($request->voucher_id)) {
            $now = Carbon::now();
            $voucher = Voucher::where('code', $request->voucher_id)
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhereNull('user_id');
                })
                ->where('status', 'active')
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->first();

            if (!$voucher) {
                return response()->json([
                    'error' => 'Mã voucher không hợp lệ hoặc đã hết hạn.'
                ], 400);
            }

            $voucherId = $voucher->id;
        }

        try {
            DB::beginTransaction();
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

            // Lấy danh sách tất cả bàn trống trong ca này
            $allAvailableTables = \App\Models\BanAn::whereDoesntHave('reservations', function ($query) use ($request) {
                $query->where('reservation_date', $request->reservation_date)
                    ->where('shift', $request->shift)
                    ->where('status', '!=', 'cancelled');
            })
                ->orderBy('limit_number', 'asc') // Sắp xếp theo sức chứa tăng dần
                ->get();

            if ($allAvailableTables->isEmpty()) {
                return response()->json([
                    'error' => 'Hết bàn trống',
                    'message' => 'Không còn đủ bàn trống, vui lòng chọn ca khác hoặc liên hệ với quán',
                    'shift_info' => $this->getShiftInfo($request->shift),
                ], 400);
            }

            // Sắp xếp bàn tự động dựa vào số người
            $numPeople = $request->num_people;
            $preferVip = $request->boolean('prefer_vip', false);
            $selectedTables = collect();
            $totalCapacity = 0;

            // Nếu chọn phòng VIP, tự động chia phòng VIP (không quan tâm số người)
            if ($preferVip) {
                $availableVipRooms = \App\Models\BanAn::where('type', 'vip')
                    ->whereDoesntHave('reservations', function ($query) use ($request) {
                        $query->where('reservation_date', $request->reservation_date)
                            ->where('shift', $request->shift)
                            ->where('status', '!=', 'cancelled');
                    })
                    ->get();

                if ($availableVipRooms->isNotEmpty()) {
                    $selectedTables->push($availableVipRooms->first());
                    $totalCapacity = $availableVipRooms->first()->limit_number; // Thường là 30
                } else {
                    return response()->json([
                        'error' => 'Hết phòng VIP',
                        'message' => 'Không còn phòng VIP trống trong ca này. Vui lòng liên hệ nhân viên để được hỗ trợ.',
                        'shift_info' => $this->getShiftInfo($request->shift),
                    ], 400);
                }
            } else {
                // Nếu không chọn VIP, chia bàn thường
                // Lấy danh sách tất cả bàn thường trống trong ca này
                $allAvailableTables = \App\Models\BanAn::where('type', 'normal')
                    ->whereDoesntHave('reservations', function ($query) use ($request) {
                        $query->where('reservation_date', $request->reservation_date)
                            ->where('shift', $request->shift)
                            ->where('status', '!=', 'cancelled');
                    })
                    ->orderBy('limit_number', 'asc') // Sắp xếp theo sức chứa tăng dần
                    ->get();

                if ($allAvailableTables->isEmpty()) {
                    return response()->json([
                        'error' => 'Hết bàn trống',
                        'message' => 'Không còn đủ bàn trống, vui lòng chọn ca khác hoặc liên hệ với quán',
                        'shift_info' => $this->getShiftInfo($request->shift),
                    ], 400);
                }

                // Chiến lược: Chọn bàn sao cho tổng sức chứa vừa đủ hoặc hơn một chút số người
                foreach ($allAvailableTables as $table) {
                    if ($totalCapacity >= $numPeople) {
                        break; // Đã đủ chỗ
                    }
                    $selectedTables->push($table);
                    $totalCapacity += $table->limit_number;
                }
            }

            // Kiểm tra xem có đủ chỗ không
            if ($totalCapacity < $numPeople) {
                return response()->json([
                    'error' => 'Hết bàn trống',
                    'message' => "Cần chỗ cho {$numPeople} người nhưng chỉ còn chỗ cho {$totalCapacity} người trong ca này. Vui lòng chọn ca khác hoặc giảm số người.",
                    'shift_info' => $this->getShiftInfo($request->shift),
                    'people_needed' => $numPeople,
                    'capacity_available' => $totalCapacity,
                ], 400);
            }

            $availableTables = $selectedTables;
            $tablesNeeded = $availableTables->count();

            // Kiểm tra loại bàn được chọn (có phòng VIP hay không)
            $hasVipRoom = $availableTables->where('type', 'vip')->isNotEmpty();
            $hasNormalTables = $availableTables->where('type', 'normal')->isNotEmpty();

            // Tính tổng total_price = tổng tiền món ăn (chưa có VAT)
            $subtotal = 0;
            if ($request->has('menus')) {
                foreach ($request->menus as $menuItem) {
                    $menu = \App\Models\Menu::find($menuItem['menu_id']);
                    if ($menu) {
                        $subtotal += $menu->price * $menuItem['quantity'];
                    }
                }
            }

            // Tính VAT 10% và tổng tiền cuối cùng
            $vat = $subtotal * 0.1;
            $totalPrice = $subtotal + $vat;

            // Kiểm tra ngày có yêu cầu đặt cọc hay không (ngày lễ)
            // Nếu có nhiều bản ghi cùng ngày, lấy bản ghi mới nhất (created_at mới nhất)
            $holidayDate = DepositRequiredDate::where('is_active', true)
                ->whereDate('date', $request->reservation_date)
                ->orderBy('created_at', 'desc')
                ->first();

            $isHoliday = $holidayDate !== null;

            $totalDeposit = 0;
            $tableDeposit = 0;
            $menuDeposit = 0;
            $initialStatus = 'pending';
            $paymentUrl = null;

            // Phòng VIP: Luôn cọc theo cấu hình (1,000,000 VND), không phân biệt ngày thường hay ngày lễ
            if ($hasVipRoom) {
                // Luôn lấy từ database (settings table), không kiểm tra ngày lễ
                $tableDeposit = max(1, (int)Setting::getValue('deposit_vip_rooms', 1000000)); // Đảm bảo >= 1
            } elseif ($isHoliday && $hasNormalTables) {
                // Bàn thường: Chỉ tính cọc nếu là ngày lễ, đảm bảo > 0
                $normalDeposit = $holidayDate->deposit_normal_tables ?? Setting::getValue('deposit_normal_tables', 500000);
                $tableDeposit = max(1, (int)$normalDeposit); // Đảm bảo >= 1
            }
            // Ngày thường + bàn thường: không cần cọc bàn (chỉ cọc món ăn nếu có)

            // Cọc món ăn (luôn cọc toàn bộ tiền món ăn nếu có)
            if ($totalPrice > 0) {
                $menuDeposit = $totalPrice;
            }

            $totalDeposit = $tableDeposit + $menuDeposit;

            // Nếu cần cọc → chờ đặt cọc, nếu không cần cọc → đặt thành công (deposit_paid)
            if ($totalDeposit > 0) {
                $initialStatus = 'deposit_pending';
            } else {
                // Không cần cọc → đặt thành công luôn, admin có thể chuyển sang serving
                $initialStatus = 'deposit_paid';
            }

            $reservation = Reservation::create([
                'user_id' => $user->id,
                'reservation_date' => $request->reservation_date,
                'shift' => $request->shift,
                'num_people' => $request->num_people,
                'depsection' => $request->depsection,
                'voucher_id' => $voucherId,
                'status' => $initialStatus,
                'deposit' => $totalDeposit,
                'total_amount' => $totalPrice,
                'reservation_code' => 'RES-' . strtoupper(Str::random(10)),
            ]);

            // Gán bàn ăn vào reservation
            $reservation->tables()->attach($availableTables->pluck('id'));

            // Lưu thông tin đặt món vào bảng Reservation_items
            if ($request->has('menus')) {
                foreach ($request->menus as $menuItem) {
                    $reservation->reservationItems()->create([
                        'menu_id' => $menuItem['menu_id'],
                        'quantity' => $menuItem['quantity'],
                        'price' => \App\Models\Menu::find($menuItem['menu_id'])->price,
                    ]);
                }
            }

            if ($totalDeposit > 0) {
                $depositDetails = [];

                if ($tableDeposit > 0) {
                    $depositDetails[] = 'Cọc bàn: ' . number_format($tableDeposit, 0, ',', '.') . ' VND';
                }

                if ($menuDeposit > 0) {
                    $depositDetails[] = 'Cọc món ăn: ' . number_format($menuDeposit, 0, ',', '.') . ' VND';
                }

                $depositInfo = !empty($depositDetails)
                    ? implode(' | ', $depositDetails)
                    : 'Đặt cọc đơn hàng';

                $depositInfo .= ' - Mã đơn: ' . $reservation->reservation_code;

                $orderData = [
                    'code' => $reservation->reservation_code,
                    'total' => $totalDeposit,
                    'bankCode' => self::BANK_CODE,
                    'type' => 'billpayment',
                    'info' => $depositInfo,
                ];

                $vnpayController = new VnPayController();
                $paymentUrl = $vnpayController->createPayment($orderData, $request);

                $reservation->update(['payment_url' => $paymentUrl]);
            }

            DB::commit();

            $message = $totalDeposit > 0
                ? 'Đặt bàn thành công! Vui lòng thanh toán tiền cọc trong 15 phút.'
                : 'Đặt bàn thành công! Đơn đặt bàn của bạn đang chờ xác nhận.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'requires_deposit' => $totalDeposit > 0,
                'is_holiday' => $isHoliday,
                'deposit_amount' => $totalDeposit,
                'payment_url' => $paymentUrl,
                'shift_info' => $this->getShiftInfo($request->shift),
                'tables_assigned' => $availableTables->map(function ($table) {
                    return [
                        'id' => $table->id,
                        'name' => $table->name,
                        'capacity' => $table->limit_number,
                    ];
                }),
                'tables_count' => $tablesNeeded,
                'total_capacity' => $totalCapacity,
                'num_people' => $numPeople,
                'reservation' => $reservation->load(['reservationItems.menu', 'tables']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Đặt bàn thất bại',
                'message' => 'Đã có lỗi xảy ra trong quá trình đặt bàn. Vui lòng thử lại.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    // Không còn sử dụng - VNPay callback được xử lý bởi VnPayController::vnpayReturn()
    // public function confirmPayment($token)
    // {
    //     $reservation = Reservation::where('payment_token', $token)->first();

    //     if (!$reservation) {
    //         return response()->json([
    //             'error' => 'Link thanh toán không hợp lệ'
    //         ], 404);
    //     }

    //     if (Carbon::now()->greaterThan($reservation->payment_expires_at)) {
    //         $reservation->update(['status' => 'cancelled']);

    //         return response()->json([
    //             'error' => 'Link thanh toán đã hết hạn (quá 10 phút)',
    //             'message' => 'Đơn đặt bàn đã bị hủy. Vui lòng đặt lại.'
    //         ], 400);
    //     }

    //     if ($reservation->status == 'deposit_paid') {
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Đơn đặt bàn này đã được thanh toán rồi.'
    //         ], 200);
    //     }

    //     $reservation->update([
    //         'status' => 'deposit_paid',
    //     ]);

    //     $reservation->refresh();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Thanh toán thành công! Đơn đặt bàn đã được xác nhận.',
    //         'reservation' => $reservation->load(['tables', 'user']),
    //     ], 200);
    // }


    public function getServingReservations(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Chỉ admin mới được xem
        if ($user->role !== 'admin') {
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'Bạn không có quyền truy cập chức năng này.'
            ], 403);
        }

        $reservations = Reservation::where('status', 'serving')
            ->with(['tables', 'reservationItems.menu', 'user'])
            ->orderBy('reservation_date', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        $data = $reservations->map(function ($reservation) {
            return [
                'id' => $reservation->id,
                'reservation_code' => $reservation->reservation_code,
                'tables' => $reservation->tables->map(function ($table) {
                    return [
                        'id' => $table->id,
                        'name' => $table->name,
                    ];
                }),
                'menus' => $reservation->reservationItems->map(function ($item) {
                    return [
                        'id' => $item->menu->id,
                        'name' => $item->menu->name,
                        'price' => $item->price,
                        'quantity' => $item->quantity,
                        'total' => $item->price * $item->quantity,
                    ];
                }),
                'subtotal' => $reservation->reservationItems->sum(function ($item) {
                    return $item->price * $item->quantity;
                }),
                'vat' => $reservation->reservationItems->sum(function ($item) {
                    return $item->price * $item->quantity;
                }) * 0.1,
                'total_price' => $reservation->reservationItems->sum(function ($item) {
                    return $item->price * $item->quantity;
                }) * 1.1,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'count' => $data->count(),
        ], 200);
    }

    /**
     * Lấy thông tin ca
     */
    private function getShiftInfo($shift)
    {
        $shifts = [
            'morning' => ['name' => 'Ca sáng', 'time' => '8:00 - 13:00'],
            'afternoon' => ['name' => 'Ca trưa', 'time' => '13:00 - 18:00'],
            'evening' => ['name' => 'Ca tối', 'time' => '18:00 - 23:00'],
        ];

        return $shifts[$shift] ?? ['name' => 'Không xác định', 'time' => ''];
    }


    private function getStatusText($status)
    {
        $statuses = [
            'pending' => 'Chờ xác nhận',
            'deposit_pending' => 'Chờ đặt cọc',
            'deposit_paid' => 'Đặt thành công',
            'serving' => 'Đang phục vụ',
            'completed' => 'Hoàn tất',
            'cancelled' => 'Đã hủy',
        ];

        return $statuses[$status] ?? 'Không xác định';
    }
}
