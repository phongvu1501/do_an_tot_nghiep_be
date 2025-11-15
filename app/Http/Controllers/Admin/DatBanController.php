<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BanAn;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
            if ($request->status === 'deposit_pending') {
                $query->whereIn('status', ['deposit_pending', 'pending']);
            } else {
                $query->where('status', $request->status);
            }
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
        $reservation = Reservation::create([
            'user_id'          => auth()->id() ?? 1,
            // 'user_id' => optional(auth()->user())->id ?? 1,
            'customer_name'    => $request->customer_name,
            'customer_phone'   => $request->customer_phone,
            'num_people'       => $request->num_people,
            'reservation_date' => $request->reservation_date,
            'shift'            => $request->shift,
            'note'             => $request->note,
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
        $reservation = Reservation::with(['menus', 'tables'])->findOrFail($id);
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

        if ($request->status === 'serving') {
            $shiftStartTimes = [
                'morning'   => '06:00',
                'afternoon' => '10:00',
                'evening'   => '14:00',
                'night'     => '18:00',
            ];

            $shiftStart = $shiftStartTimes[$reservation->shift] ?? null;

            if ($shiftStart) {
                $reservationStart = Carbon::parse($reservation->reservation_date, config('app.timezone'))
                    ->setTimeFromTimeString($shiftStart);

                if (now()->lt($reservationStart)) {
                    return back()->with('error', 'Chưa đến khung giờ phục vụ của đơn đặt bàn này nên chưa thể bắt đầu phục vụ.');
                }
            }
        }

        $reservation->status = $request->status;

        if ($request->status === 'cancelled' && $request->filled('cancellation_reason')) {
            $reservation->cancellation_reason = $request->cancellation_reason;
        }

        $reservation->save();

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
        foreach ($request->table_ids as $tableId) {
            $ban = BanAn::findOrFail($tableId);

            $isBusy = $ban->reservations()
                ->where('reservation_date', $reservation->reservation_date)
                ->where('shift', $reservation->shift)
                ->whereIn('status', ['deposit_paid', 'serving'])
                ->where('reservations.id', '!=', $reservation->id)
                ->exists();

            if ($isBusy) {
                return back()->with('error', "Bàn {$ban->name} đang bận trong ca này!");
            }
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
              ->whereIn('status', ['deposit_paid', 'serving']);
        })->pluck('id');

        return response()->json([
            'tables' => $allTables,
            'busyTableIds' => $busyTableIds,
        ]);
    }
}