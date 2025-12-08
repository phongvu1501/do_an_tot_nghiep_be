<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BanAn;
use App\Models\PointLog;
use App\Models\Reservation;
use App\Models\Voucher;
use App\Models\DepositRequiredDate;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatBanController extends Controller
{
    /**
     * Hiển thị danh sách đơn đặt bàn
     */
    public function index(Request $request)
    {
        $query = Reservation::with(['reservationItems.menu', 'tables', 'user', 'voucher']);

        // Lọc theo ngày
        if ($request->filled('date')) {
            $query->whereDate('reservation_date', $request->date);
        }

        // Lọc theo ca
        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo số điện thoại (tìm chính xác)
        if ($request->filled('phone')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('phone', $request->phone);
            });
        }

        $reservations = $query->orderByDesc('id')
            ->paginate(10)
            ->appends($request->except('page'));

        return view('admin.datBan.index', [
            'title' => 'Trang quản lý đặt bàn',
            'tables' => $reservations,
            'availableTables' => BanAn::all(),
        ]);
    }

    /**
     * Hiển thị form tạo đơn đặt bàn
     */
    public function create()
    {
        return view('admin.datBan.create', [
            'title' => 'Tạo đơn đặt bàn mới',
            'allTables' => BanAn::all(),
            'menus' => \App\Models\Menu::where('status', 1)->with('category')->get(),
            'categories' => \App\Models\MenuCategory::all(),
            'depositVipRooms' => Setting::getValue('deposit_vip_rooms', 1000000),
            'depositNormalTables' => Setting::getValue('deposit_normal_tables', 500000),
            'minTablesForDeposit' => Setting::getValue('min_tables_for_deposit', 2),
        ]);
    }

    /**
     * Xử lý tạo đơn đặt bàn mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name'     => 'required|string|max:255',
            'customer_phone'    => 'required|string|max:20',
            'customer_email'    => 'required|email|max:255',
            'num_people'        => 'required|integer|min:1',
            'reservation_date'  => 'required|date|after_or_equal:today',
            'shift'             => 'required|in:morning,afternoon,evening',
            'table_ids'         => 'required|array|min:1',
            'table_ids.*'       => 'exists:tables,id',
            'note'              => 'nullable|string|max:500',
            'user_id'           => 'nullable|exists:users,id',
            'require_deposit'   => 'nullable|boolean',
            'menus'             => 'nullable|array',
            'menus.*.menu_id'   => 'required_with:menus|exists:menus,id',
            'menus.*.quantity'  => 'required_with:menus|integer|min:1',
        ], [
            'customer_name.required'     => 'Vui lòng nhập tên khách hàng!',
            'customer_phone.required'    => 'Vui lòng nhập số điện thoại!',
            'customer_email.required'    => 'Vui lòng nhập email!',
            'customer_email.email'       => 'Email không hợp lệ!',
            'num_people.required'        => 'Vui lòng nhập số lượng người!',
            'num_people.min'             => 'Số người phải lớn hơn 0!',
            'reservation_date.required'  => 'Vui lòng chọn ngày đặt bàn!',
            'reservation_date.after_or_equal' => 'Ngày đặt bàn phải từ hôm nay trở đi!',
            'shift.required'             => 'Vui lòng chọn ca!',
            'table_ids.required'         => 'Vui lòng chọn ít nhất 1 bàn!',
        ]);

        // Nếu có user_id, kiểm tra xem user đã có đặt bàn trong thời gian đó chưa
        if ($request->filled('user_id')) {
            $existingReservation = Reservation::where('user_id', $request->user_id)
                ->where('reservation_date', $request->reservation_date)
                ->where('shift', $request->shift)
                ->whereNotIn('status', ['cancelled', 'completed'])
                ->first();

            if ($existingReservation) {
                return back()->withInput()
                    ->with('error', 'Khách hàng đã có đặt bàn cho ca này rồi. Vui lòng chọn ca khác hoặc hủy đơn cũ.');
            }
        }

        // Kiểm tra bàn có bị trùng trong cùng ca hay không
        foreach ($request->table_ids as $tableId) {
            $ban = BanAn::findOrFail($tableId);

            // Kiểm tra các trạng thái: confirmed (đã xác nhận), deposit_paid (đã đặt cọc), serving (đang phục vụ)
            $isBusy = $ban->reservations()
                ->where('reservation_date', $request->reservation_date)
                ->where('shift', $request->shift)
                ->whereIn('status', ['confirmed', 'deposit_paid', 'serving'])
                ->exists();

            if ($isBusy) {
                return back()->withInput()
                    ->with('error', "Bàn {$ban->name} đang bận trong ca này! Vui lòng chọn bàn khác.");
            }
        }

        // Tạo đơn đặt bàn (do admin tạo)
        // Nếu có user_id thì dùng user_id đó, nếu không thì tạo user mới
        if ($request->filled('user_id')) {
            $userId = $request->user_id;
        } else {
            $existingUserByPhone = \App\Models\User::where('phone', $request->customer_phone)->first();
            
            if ($existingUserByPhone) {
                $userId = $existingUserByPhone->id;
            } else {
                $existingUserByEmail = \App\Models\User::where('email', $request->customer_email)->first();
                
                if ($existingUserByEmail) {
                    return back()->withInput()
                        ->with('error', 'Email đã tồn tại!');
                }
                
                $user = \App\Models\User::create([
                    'name' => $request->customer_name,
                    'phone' => $request->customer_phone,
                    'email' => $request->customer_email,
                    'password' => bcrypt('123456'),
                    'role' => 'user',
                    'points' => 0,
                ]);
                $userId = $user->id;
            }
        }

        // Tính tổng tiền món ăn
        $subtotal = 0;
        if ($request->has('menus') && is_array($request->menus)) {
            foreach ($request->menus as $menuItem) {
                $menu = \App\Models\Menu::find($menuItem['menu_id']);
                if ($menu) {
                    $subtotal += $menu->price * $menuItem['quantity'];
                }
            }
        }
        $vat = $subtotal * 0.08;
        $totalPrice = $subtotal + $vat;
        
        // Tính tiền cọc (chỉ tính nếu require_deposit = true)
        $requireDeposit = $request->boolean('require_deposit', false);
        $totalDeposit = 0;
        $tableDeposit = 0;
        $menuDeposit = 0;
        
        if ($requireDeposit) {
            // Kiểm tra ngày có yêu cầu đặt cọc hay không (ngày lễ)
            $holidayDate = DepositRequiredDate::where('is_active', true)
                ->whereDate('date', $request->reservation_date)
                ->orderBy('created_at', 'desc')
                ->first();
            
            $isHoliday = $holidayDate !== null;
            
            // Kiểm tra có phòng VIP không
            $selectedTables = BanAn::whereIn('id', $request->table_ids)->get();
            $hasVipRoom = $selectedTables->where('type', 'vip')->isNotEmpty();
            $hasNormalTables = $selectedTables->where('type', 'normal')->isNotEmpty();
            
            // Cọc bàn:
            // - VIP: Luôn cần cọc
            // - Bàn thường: Cọc nếu >= 2 bàn hoặc là ngày lễ
            if ($hasVipRoom) {
                $vipDepositPerTable = max(1, (int)Setting::getValue('deposit_vip_rooms', 1000000));
                $vipTableCount = $selectedTables->where('type', 'vip')->count();
                $tableDeposit = $vipDepositPerTable * $vipTableCount;
            } elseif ($hasNormalTables) {
                $normalTableCount = $selectedTables->where('type', 'normal')->count();
                
                // Ngày lễ: Luôn bắt buộc cọc dù chỉ 1 bàn
                if ($isHoliday) {
                    $normalDepositPerTable = Setting::getValue('deposit_normal_tables', 500000);
                    $normalDepositPerTable = max(1, (int)$normalDepositPerTable);
                    $tableDeposit = $normalDepositPerTable * $normalTableCount;
                } else {
                    // Ngày thường: Chỉ cọc nếu >= số bàn cấu hình
                    $minTablesForDeposit = Setting::getValue('min_tables_for_deposit', 2);
                    if ($normalTableCount >= $minTablesForDeposit) {
                        $normalDepositPerTable = Setting::getValue('deposit_normal_tables', 500000);
                        $normalDepositPerTable = max(1, (int)$normalDepositPerTable);
                        $tableDeposit = $normalDepositPerTable * $normalTableCount;
                    }
                }
            }
            
            // Cọc món ăn: Luôn cọc 100% tiền món nếu có món
            if ($totalPrice > 0) {
                $menuDeposit = $totalPrice;
            }
            
            $totalDeposit = $tableDeposit + $menuDeposit;
        }
        
        // Xác định status ban đầu
        // Admin tạo đơn: Luôn là deposit_paid vì người dùng đã cọc rồi admin mới tạo
        $initialStatus = 'deposit_paid';
        
        // Tạo đơn đặt bàn
        $reservation = Reservation::create([
            'user_id'          => $userId,
            'num_people'       => $request->num_people,
            'reservation_date' => $request->reservation_date,
            'shift'            => $request->shift,
            'depsection'       => $request->note,
            'status'           => $initialStatus,
            'deposit'          => $totalDeposit,
            'total_amount'     => $totalPrice,
            'reservation_code' => 'RES-' . strtoupper(\Illuminate\Support\Str::random(10)),
        ]);

        // Gán bàn cho đơn đặt
        $reservation->tables()->attach($request->table_ids);
        
        // Lưu món ăn vào reservation_items
        if ($request->has('menus') && is_array($request->menus)) {
            foreach ($request->menus as $menuItem) {
                $menu = \App\Models\Menu::find($menuItem['menu_id']);
                if ($menu) {
                    $reservation->reservationItems()->create([
                        'menu_id' => $menu->id,
                        'quantity' => $menuItem['quantity'],
                        'price' => $menu->price,
                    ]);
                }
            }
        }

        $message = "Tạo đơn đặt bàn thành công! Mã đơn: #{$reservation->id}";
        if ($totalDeposit > 0) {
            $message .= " (Cần đặt cọc: " . number_format($totalDeposit, 0, ',', '.') . " VND)";
        }
        
        return redirect()->route('admin.datBan.index')
                         ->with('success', $message);
    }

    /**
     * Hiển thị chi tiết đặt bàn
     */
    public function show(string $id)
    {
        $reservation = Reservation::with(['reservationItems.menu', 'tables', 'user', 'voucher'])->findOrFail($id);
        return view('admin.datBan.show', compact('reservation'));
    }

    /**
     * Cập nhật bàn (thay bàn cũ bằng bàn mới)
     */
    public function update(Request $request, string $reservationId)
    {
        $reservation = Reservation::findOrFail($reservationId);

        $request->validate([
            'table_id' => 'required|exists:tables,id',
        ]);

        $reservation->tables()->sync([$request->table_id]);

        return redirect()->route('admin.datBan.index')
            ->with('success', 'Cập nhật bàn thành công!');
    }


    public function confirm(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể xác nhận đơn đặt bàn đang ở trạng thái "Chờ xác nhận".');
        }

        $reservation->status = 'confirmed';
        $reservation->save();

        return redirect()->route('admin.datBan.index')
            ->with('success', "Đã xác nhận đơn đặt bàn #{$reservation->id} thành công!");
    }


    /**
     * Cập nhật trạng thái đơn đặt bàn
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'reservation_id'       => 'required|exists:reservations,id',
            'status'               => 'required|in:pending,deposit_pending,deposit_paid,serving,completed,cancelled',
            'cancellation_reason'  => 'required_if:status,cancelled',
        ]);

        $reservation = Reservation::findOrFail($request->reservation_id);

        $oldStatus = $reservation->getOriginal('status');

        if ($request->status === 'serving') {
            if (!in_array($reservation->status, ['deposit_paid', 'serving'])) {
                return back()->with('error', 'Đơn đặt bàn phải ở trạng thái "Đặt thành công" trước khi bắt đầu phục vụ.');
            }

            $conflictingTables = [];
            foreach ($reservation->tables as $table) {
                $conflictingReservation = DB::table('reservation_tables')
                    ->join('reservations', 'reservation_tables.reservation_id', '=', 'reservations.id')
                    ->where('reservation_tables.table_id', $table->id)
                    ->where('reservations.status', 'serving')
                    ->where('reservations.id', '!=', $reservation->id)
                    ->first();

                if ($conflictingReservation) {
                    $conflictingReservationModel = Reservation::find($conflictingReservation->reservation_id);
                    $conflictingTables[] = [
                        'table' => $table,
                        'conflicting_reservation' => $conflictingReservationModel,
                    ];
                }
            }

            if (!empty($conflictingTables)) {
                $tableNames = collect($conflictingTables)->pluck('table.name')->implode(', ');
                $conflictingReservationIds = collect($conflictingTables)->pluck('conflicting_reservation.id')->unique()->implode(', ');
                $conflictingTableIds = collect($conflictingTables)->pluck('table.id')->toArray();

                $conflictingInfo = collect($conflictingTables)->map(function ($item) {
                    return [
                        'table_id' => $item['table']->id,
                        'table_name' => $item['table']->name,
                        'conflicting_reservation_code' => $item['conflicting_reservation']->reservation_code ?? '#' . $item['conflicting_reservation']->id,
                    ];
                })->toArray();

                return redirect()->route('admin.datBan.index')
                    ->with('error', "Không thể bắt đầu phục vụ! {$tableNames} đang được sử dụng bởi đơn khác")
                    ->with('open_edit_modal', $reservation->id)
                    ->with('conflicting_tables', $conflictingInfo);
            }
        }

        $reservation->status = $request->status;

        if ($request->status === 'cancelled' && $request->filled('cancellation_reason')) {
            $reservation->cancellation_reason = $request->cancellation_reason;
        }

        $reservation->save();

        if ($request->status === 'completed' && $oldStatus !== 'completed') {

            $user = $reservation->user;

            if ($user) {
                // Tổng tiền sau voucher
                $amount = $reservation->total_amount ?? 0;

                // 1 điểm = 1.000đ
                $points = floor($amount / 1000);

                // Cập nhật điểm
                $user->points = ($user->points ?? 0) + $points;
                $user->save();

                // Ghi log điểm
                PointLog::create([
                    'user_id' => $user->id,
                    'reservation_id' => $reservation->id,
                    'points' => $points,    
                    'action' => 'Hoàn tất đơn hàng #' . ($reservation->reservation_code ?? $reservation->id),
                ]);
            }
        }

        // Điều hướng theo yêu cầu
        if ($request->redirect_to === 'banAn') {
            return redirect()->route('admin.banAn.index', [
                'date'  => $request->filter_date ?? now()->toDateString(),
                'shift' => $request->filter_shift ?? 'morning',
            ])->with('success', 'Cập nhật trạng thái đặt bàn thành công!');
        }

        return redirect()->route('admin.datBan.index')
            ->with('success', 'Cập nhật trạng thái đặt bàn thành công!');
    }

    /**
     * Cập nhật lại danh sách bàn cho 1 đơn
     */
    public function updateTables(Request $request, $id)
    {
        $request->validate([
            'table_ids'   => 'required|array|min:1',
            'table_ids.*' => 'exists:tables,id',
        ], [
            'table_ids.required' => 'Vui lòng chọn ít nhất 1 bàn!',
        ]);

        $reservation = Reservation::findOrFail($id);

        // Không cho chỉnh sửa nếu đã hoàn tất / hủy
        if (in_array($reservation->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Không thể chỉnh sửa bàn cho đơn đã hoàn tất hoặc đã hủy!');
        }

        // Kiểm tra trùng bàn
        $conflictingTables = [];
        foreach ($request->table_ids as $tableId) {
            $ban = BanAn::findOrFail($tableId);

            $conflictingReservation = DB::table('reservation_tables')
                ->join('reservations', 'reservation_tables.reservation_id', '=', 'reservations.id')
                ->where('reservation_tables.table_id', $tableId)
                ->where('reservations.id', '!=', $reservation->id)
                ->where(function ($query) use ($reservation) {
                    if ($reservation->status === 'serving') {
                        $query->where('reservations.status', 'serving');
                    } else {
                        // Kiểm tra các trạng thái: confirmed (đã xác nhận), deposit_paid (đã đặt cọc), serving (đang phục vụ)
                        $query->where('reservations.reservation_date', $reservation->reservation_date)
                            ->where('reservations.shift', $reservation->shift)
                            ->whereIn('reservations.status', ['confirmed', 'deposit_paid', 'serving']);
                    }
                })
                ->first();

            if ($conflictingReservation) {
                $conflictingReservationModel = Reservation::find($conflictingReservation->reservation_id);
                $conflictingTables[] = [
                    'table' => $ban,
                    'conflicting_reservation' => $conflictingReservationModel,
                ];
            }
        }

        if (!empty($conflictingTables) && $reservation->status === 'serving') {
            $tableNames = collect($conflictingTables)->pluck('table.name')->implode(', ');
            $conflictingInfo = collect($conflictingTables)->map(function ($item) {
                return [
                    'table_id' => $item['table']->id,
                    'table_name' => $item['table']->name,
                    'conflicting_reservation_code' => $item['conflicting_reservation']->reservation_code ?? '#' . $item['conflicting_reservation']->id,
                ];
            })->toArray();

            return redirect()->route('admin.datBan.index')
                ->with('error', "Không thể cập nhật bàn! Các bàn sau đang được sử dụng bởi đơn khác (đang phục vụ): {$tableNames}.")
                ->with('open_edit_modal', $reservation->id)
                ->with('conflicting_tables', $conflictingInfo);
        }

        if (!empty($conflictingTables)) {
            $tableNames = collect($conflictingTables)->pluck('table.name')->implode(', ');
            return back()->with('error', "Các bàn sau đang bận trong ca này: {$tableNames}. Vui lòng chọn bàn khác!");
        }

        $reservation->tables()->sync($request->table_ids);

        return redirect()->route('admin.datBan.index')
            ->with('success', "Đã cập nhật bàn cho đơn #{$reservation->id} thành công! (Tổng số bàn: " . count($request->table_ids) . ")");
    }

    /**
     * API lấy danh sách bàn trống theo ngày và ca
     */
    public function getAvailableTables(Request $request)
    {
        $date  = $request->query('date');
        $shift = $request->query('shift');

        $allTables = BanAn::all();

        // Kiểm tra các trạng thái: confirmed (đã xác nhận), deposit_paid (đã đặt cọc), serving (đang phục vụ)
        $busyTableIds = BanAn::whereHas('reservations', function ($q) use ($date, $shift) {
            $q->where('reservation_date', $date)
                ->where('shift', $shift)
                ->whereIn('status', ['confirmed', 'deposit_paid', 'serving']);
        })->pluck('id');

        return response()->json([
            'tables' => $allTables->map(function($table) {
                return [
                    'id' => $table->id,
                    'name' => $table->name,
                    'type' => $table->type,
                    'limit_number' => $table->limit_number,
                ];
            }),
            'busyTableIds' => $busyTableIds,
        ]);
    }
    
    /**
     * Xác nhận đã gọi điện cho khách hàng
     */
    public function confirmPhone($id)
    {
        $reservation = Reservation::findOrFail($id);

        $reservation->update([
            'phone_confirmed' => true,
        ]);

        return redirect()->route('admin.datBan.index')
            ->with('success', 'Đã xác nhận gọi điện cho khách hàng!');
    }

    /**
     * Tìm user theo số điện thoại
     */
    public function checkUserByPhone(Request $request)
    {
        $phone = $request->query('phone');

        if (!$phone) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng nhập số điện thoại',
            ], 400);
        }

        $user = \App\Models\User::where('phone', $phone)->first();

        if ($user) {
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy tài khoản với số điện thoại này',
        ], 404);
    }

    /**
     * Kiểm tra xem user đã có đặt bàn trong thời gian đó chưa
     */
    public function checkExistingReservation(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reservation_date' => 'required|date',
            'shift' => 'required|in:morning,afternoon,evening,night',
        ]);

        $existingReservation = Reservation::where('user_id', $request->user_id)
            ->where('reservation_date', $request->reservation_date)
            ->where('shift', $request->shift)
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->first();

        if ($existingReservation) {
            return response()->json([
                'success' => false,
                'has_reservation' => true,
                'message' => 'Khách hàng đã có đặt bàn cho ca này rồi. Vui lòng chọn ca khác hoặc hủy đơn cũ.',
                'reservation' => [
                    'id' => $existingReservation->id,
                    'date' => $existingReservation->reservation_date,
                    'shift' => $existingReservation->shift,
                    'status' => $existingReservation->status,
                    'status_text' => $this->getStatusText($existingReservation->status),
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'has_reservation' => false,
            'message' => 'Khách hàng chưa có đặt bàn trong thời gian này',
        ]);
    }

    /**
     * Lấy text trạng thái
     */
    private function getStatusText($status)
    {
        $statusTexts = [
            'pending' => 'Chờ xác nhận',
            'deposit_pending' => 'Chờ đặt cọc',
            'deposit_paid' => 'Đặt thành công',
            'serving' => 'Đang phục vụ',
            'completed' => 'Hoàn tất',
            'cancelled' => 'Đã hủy',
        ];

        return $statusTexts[$status] ?? $status;
    }

  
    public function getApplicableVouchers($id)
    {
        $reservation = Reservation::with(['reservationItems.menu', 'user', 'voucher'])->findOrFail($id);
        
        $subtotal = $reservation->reservationItems->sum(fn($item) => $item->price * $item->quantity);
        $vat = $subtotal * 0.08;
        $totalPrice = $subtotal + $vat;

        $userId = $reservation->user_id;
        $now = \Carbon\Carbon::now();
        $totalUsers = \App\Models\User::count();

        $user = \App\Models\User::find($userId);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User không tồn tại',
            ], 404);
        }

        $userVouchers = $user->vouchers()
            ->where('vouchers.status', 'active')
            ->with('tier')
            ->get()
            ->filter(function ($voucher) use ($userId) {
                $pivot = DB::table('user_voucher')
                    ->where('user_id', $userId)
                    ->where('voucher_id', $voucher->id)
                    ->first();
                
                if ($pivot) {
                    $usedCount = $pivot->used_count ?? 0;
                    return $usedCount < $voucher->max_uses;
                }
                return false;
            });

        $allActiveVouchers = Voucher::with(['tier', 'users'])
            ->where('status', 'active')
            ->get();

        $allUsersVouchers = $allActiveVouchers->filter(function ($voucher) use ($totalUsers, $userId) {
            if ($voucher->users->count() === $totalUsers) {
                $pivot = DB::table('user_voucher')
                    ->where('user_id', $userId)
                    ->where('voucher_id', $voucher->id)
                    ->first();
                
                if ($pivot) {
                    $usedCount = $pivot->used_count ?? 0;
                    return $usedCount < $voucher->max_uses;
                }
                return false;
            }
            return false;
        });

        $vouchers = $userVouchers->merge($allUsersVouchers)->unique('id');

        $canApply = [];
        $cannotApply = [];

        foreach ($vouchers as $voucher) {
            $errors = [];
            $isApplicable = true;

            if ($voucher->start_date && $now->lt($voucher->start_date)) {
                $errors[] = 'Voucher chưa đến thời gian sử dụng';
                $isApplicable = false;
            }

            if ($voucher->end_date && $now->gt($voucher->end_date)) {
                $errors[] = 'Voucher đã hết hạn';
                $isApplicable = false;
            }

            if ($voucher->used_count >= $voucher->max_uses) {
                $errors[] = 'Voucher đã đạt giới hạn sử dụng';
                $isApplicable = false;
            }

            if ($voucher->min_order_value && $totalPrice < $voucher->min_order_value) {
                $errors[] = 'Đơn hàng chưa đạt giá trị tối thiểu: ' . number_format($voucher->min_order_value, 0, ',', '.') . ' VNĐ';
                $isApplicable = false;
            }

            if ($reservation->voucher_id == $voucher->id) {
                $errors[] = 'Voucher đã được áp dụng vào đơn này';
                $isApplicable = false;
            }

            $discountAmount = 0;
            $maxDiscountValue = null;
            
            if ($isApplicable) {
                if ($voucher->discount_type === 'percent') {
                    $discountAmount = ($voucher->discount_value / 100) * $totalPrice;
                    if ($voucher->order_value_allowed && $discountAmount > $voucher->order_value_allowed) {
                        $discountAmount = $voucher->order_value_allowed;
                    }
                } else {
                    $discountAmount = $voucher->discount_value;
                }

                if ($voucher->tier && $voucher->tier->max_discount_value) {
                    $maxDiscountValue = $voucher->tier->max_discount_value;
                    if ($discountAmount > $maxDiscountValue) {
                        $discountAmount = $maxDiscountValue;
                    }
                }

                if ($discountAmount > $totalPrice) {
                    $discountAmount = $totalPrice;
                }
            }

            $finalAmount = max(0, $totalPrice - $discountAmount);

            $voucherData = [
                'id' => $voucher->id,
                'code' => $voucher->code,
                'discount_type' => $voucher->discount_type,
                'discount_value' => $voucher->discount_value,
                'min_order_value' => $voucher->min_order_value,
                'order_value_allowed' => $voucher->order_value_allowed,
                'max_discount_value' => $maxDiscountValue,
                'start_date' => $voucher->start_date ? (\Carbon\Carbon::parse($voucher->start_date)->format('Y-m-d')) : null,
                'end_date' => $voucher->end_date ? (\Carbon\Carbon::parse($voucher->end_date)->format('Y-m-d')) : null,
                'max_uses' => (int)($voucher->max_uses ?? 1),
                'used_count' => (int)($voucher->used_count ?? 0),
                'errors' => $errors,
                'discount_amount' => round($discountAmount, 0),
                'final_amount' => round($finalAmount, 0),
            ];

            if ($isApplicable) {
                $canApply[] = $voucherData;
            } else {
                $cannotApply[] = $voucherData;
            }
        }

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

   
    public function applyVoucher(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'voucher_id' => 'required|exists:vouchers,id',
        ]);

        $reservation = Reservation::with(['reservationItems.menu', 'user'])->findOrFail($id);
        
        $subtotal = $reservation->reservationItems->sum(fn($item) => $item->price * $item->quantity);
        $vat = $subtotal * 0.08;
        $totalPrice = $subtotal + $vat;

        $voucher = Voucher::with('tier')->findOrFail($request->voucher_id);
        $now = \Carbon\Carbon::now();

        if ($voucher->user_id && $voucher->user_id != $reservation->user_id) {
            return response()->json(['success' => false, 'message' => 'Voucher không thuộc về khách hàng này'], 400);
        }

        if ($voucher->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'Voucher không còn hiệu lực'], 400);
        }

        if ($voucher->start_date && $now->lt($voucher->start_date)) {
            return response()->json(['success' => false, 'message' => 'Voucher chưa đến thời gian sử dụng'], 400);
        }

        if ($voucher->end_date && $now->gt($voucher->end_date)) {
            return response()->json(['success' => false, 'message' => 'Voucher đã hết hạn'], 400);
        }

        if ($voucher->used_count >= $voucher->max_uses) {
            return response()->json(['success' => false, 'message' => 'Voucher đã đạt giới hạn sử dụng'], 400);
        }

        if ($voucher->min_order_value && $totalPrice < $voucher->min_order_value) {
            return response()->json(['success' => false, 'message' => 'Đơn hàng chưa đạt giá trị tối thiểu: ' . number_format($voucher->min_order_value, 0, ',', '.') . ' VNĐ'], 400);
        }

        $discountAmount = 0;
        if ($voucher->discount_type === 'percent') {
            $discountAmount = ($voucher->discount_value / 100) * $totalPrice;
            if ($voucher->order_value_allowed && $discountAmount > $voucher->order_value_allowed) {
                $discountAmount = $voucher->order_value_allowed;
            }
        } else {
            $discountAmount = $voucher->discount_value;
        }

        if ($voucher->tier && $voucher->tier->max_discount_value) {
            if ($discountAmount > $voucher->tier->max_discount_value) {
                $discountAmount = $voucher->tier->max_discount_value;
            }
        }

        if ($discountAmount > $totalPrice) {
            $discountAmount = $totalPrice;
        }

        $finalAmount = max(0, $totalPrice - $discountAmount);

        try {
            DB::beginTransaction();

            $reservation->voucher_id = $voucher->id;
            $reservation->voucher_discount = round($discountAmount, 0);
            $reservation->total_amount = round($finalAmount, 0);
            $reservation->save();

            $voucher->increment('used_count');

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
                        'discount' => round($discountAmount, 0),
                    ],
                    'discount_amount' => round($discountAmount, 0),
                    'final_amount' => round($finalAmount, 0),
                    'total_price' => round($totalPrice, 0),
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

 
    public function removeVoucher($id)
    {
        $reservation = Reservation::with(['reservationItems.menu'])->findOrFail($id);
        
        if (!$reservation->voucher_id) {
            return response()->json(['success' => false, 'message' => 'Reservation không có voucher được áp dụng'], 400);
        }

        $voucher = Voucher::find($reservation->voucher_id);

        try {
            DB::beginTransaction();

            $subtotal = $reservation->reservationItems->sum(fn($item) => $item->price * $item->quantity);
            $vat = $subtotal * 0.08;
            $totalPrice = $subtotal + $vat;

            $reservation->voucher_id = null;
            $reservation->voucher_discount = 0;
            $reservation->total_amount = round($totalPrice, 0);
            $reservation->save();

            if ($voucher && $voucher->used_count > 0) {
                $voucher->decrement('used_count');
            }

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
                    'discount_amount' => 0,
                    'final_amount' => round($totalPrice, 0),
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }
}
