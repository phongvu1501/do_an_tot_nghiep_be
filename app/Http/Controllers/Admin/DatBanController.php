<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BanAn;
use App\Models\Reservation;
use App\Models\Voucher;
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
        $query = Reservation::with(['reservationItems.menu', 'tables', 'user']);

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
            'num_people'        => 'required|integer|min:1',
            'reservation_date'  => 'required|date|after_or_equal:today',
            'shift'             => 'required|in:morning,afternoon,evening,night',
            'table_ids'         => 'required|array|min:1',
            'table_ids.*'       => 'exists:tables,id',
            'note'              => 'nullable|string|max:500',
            'user_id'           => 'nullable|exists:users,id',
        ], [
            'customer_name.required'     => 'Vui lòng nhập tên khách hàng!',
            'customer_phone.required'    => 'Vui lòng nhập số điện thoại!',
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

            $isBusy = $ban->reservations()
                ->where('reservation_date', $request->reservation_date)
                ->where('shift', $request->shift)
                ->whereIn('status', ['deposit_paid', 'serving'])
                ->exists();

            if ($isBusy) {
                return back()->withInput()
                    ->with('error', "Bàn {$ban->name} đang bận trong ca này! Vui lòng chọn bàn khác.");
            }
        }

        // Tạo đơn đặt bàn (do admin tạo)
        // Nếu có user_id thì dùng user_id đó, nếu không thì tạo mới hoặc dùng user mặc định
        $userId = $request->filled('user_id') ? $request->user_id : (auth()->id() ?? 1);

        $reservation = Reservation::create([
            'user_id'          => $userId,
            'num_people'       => $request->num_people,
            'reservation_date' => $request->reservation_date,
            'shift'            => $request->shift,
            'depsection'       => $request->note,
            'status'           => 'deposit_paid',
        ]);

        // Gán bàn cho đơn đặt
        $reservation->tables()->attach($request->table_ids);

        return redirect()->route('admin.datBan.index')
            ->with('success', "Tạo đơn đặt bàn thành công! Mã đơn: #{$reservation->id}");
    }

    /**
     * Hiển thị chi tiết đặt bàn
     */
    public function show(string $id)
    {
        $reservation = Reservation::with(['reservationItems.menu', 'tables'])->findOrFail($id);
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

        $oldStatus = $reservation->status;

        if ($request->status === 'serving') {
            // Cho phép chuyển sang serving từ deposit_paid trở lên (đặt thành công)
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
                        'conflicting_reservation_id' => $item['conflicting_reservation']->id,
                    ];
                })->toArray();

                return redirect()->route('admin.datBan.index')
                    ->with('error', "Không thể bắt đầu phục vụ! Các bàn sau đang được sử dụng bởi đơn khác (đang phục vụ): {$tableNames}. Vui lòng chỉnh sửa bàn trước.")
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
                        $query->where('reservations.reservation_date', $reservation->reservation_date)
                            ->where('reservations.shift', $reservation->shift)
                            ->whereIn('reservations.status', ['confirmed', 'deposit_paid', 'serving'])
                            ->where('reservations.shift', $reservation->shift)
                            ->whereIn('reservations.status', ['deposit_paid', 'serving']);
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
                    'conflicting_reservation_id' => $item['conflicting_reservation']->id,
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

        $busyTableIds = BanAn::whereHas('reservations', function ($q) use ($date, $shift) {
            $q->where('reservation_date', $date)
                ->where('shift', $shift)
                ->whereIn('status', ['confirmed', 'deposit_paid', 'serving'])
                ->where('shift', $shift)
                ->whereIn('status', ['deposit_paid', 'serving']);
        })->pluck('id');

        return response()->json([
            'tables' => $allTables,
            'busyTableIds' => $busyTableIds,
        ]);
    }
    // Xử lý voucher
    // public function calculateVoucherDiscount($reservation)
    // {
    //     if (!$reservation->voucher_id) {
    //         return 0;
    //     }

    //     $voucher = Voucher::find($reservation->voucher_id);
    //     if (!$voucher || $voucher->status !== 'active') {
    //         return 0;
    //     }

    //     // Tính tổng
    //     $subtotal = $reservation->reservationItems->sum(fn($item) => $item->price * $item->quantity);
    //     $vat = $subtotal * 0.1;
    //     $total = $subtotal + $vat;

    //     // Điều kiện min/max
    //     if ($voucher->min_order_value && $total < $voucher->min_order_value) {
    //         return 0;
    //     }

    //     if ($voucher->order_value_allowed && $total > $voucher->order_value_allowed) {
    //         return 0;
    //     }

    //     // Tính giảm
    //     $discount = $voucher->discount_type == 'percent'
    //         ? ($total * $voucher->discount_value) / 100
    //         : $voucher->discount_value;

    //     if ($voucher->max_discount_value) {
    //         $discount = min($discount, $voucher->max_discount_value);
    //     }

    //     return $discount;
    // }


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
}
