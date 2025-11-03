<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BanAn;
use App\Models\Reservation;
use Illuminate\Http\Request;

class DatBanController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['menus', 'tables', 'user']);

        if ($request->filled('date')) {
            $query->whereDate('reservation_date', $request->date);
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        if ($request->filled('status')) {
            if ($request->status == 'deposit_pending') {
                $query->whereIn('status', ['deposit_pending', 'pending']);
            } else {
                $query->where('status', $request->status);
            }
        }

        $tables = $query->orderBy('id', 'desc')->paginate(10)->appends($request->except('page'));

        $title = "Trang quản lý đặt bàn";

        $availableTables = BanAn::all();

        return view('admin.datBan.index', compact('title', 'tables', 'availableTables'));
    }

    public function create()
    {
        $title = "Tạo đơn đặt bàn mới";
        $allTables = BanAn::all();
        return view('admin.datBan.create', compact('title', 'allTables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'num_people' => 'required|integer|min:1',
            'reservation_date' => 'required|date|after_or_equal:today',
            'shift' => 'required|in:morning,afternoon,evening,night',
            'table_ids' => 'required|array|min:1',
            'table_ids.*' => 'exists:tables,id',
            'note' => 'nullable|string|max:500',
        ], [
            'customer_name.required' => 'Vui lòng nhập tên khách hàng!',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại!',
            'num_people.required' => 'Vui lòng nhập số lượng người!',
            'num_people.min' => 'Số người phải lớn hơn 0!',
            'reservation_date.required' => 'Vui lòng chọn ngày đặt bàn!',
            'reservation_date.after_or_equal' => 'Ngày đặt bàn phải từ hôm nay trở đi!',
            'shift.required' => 'Vui lòng chọn ca!',
            'table_ids.required' => 'Vui lòng chọn ít nhất 1 bàn!',
            'table_ids.min' => 'Vui lòng chọn ít nhất 1 bàn!',
        ]);

        foreach ($request->table_ids as $tableId) {
            $isBusy = \App\Models\BanAn::find($tableId)
                ->reservations()
                ->where('reservation_date', $request->reservation_date)
                ->where('shift', $request->shift)
                ->whereIn('status', ['deposit_paid', 'serving'])
                ->exists();

            if ($isBusy) {
                $tableName = \App\Models\BanAn::find($tableId)->name;
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Bàn {$tableName} đang bận trong ca này! Vui lòng chọn bàn khác.");
            }
        }

        // Tạo reservation (admin tạo thì deposit_paid luôn, không cần payment)
        $reservation = Reservation::create([
            'user_id' => auth()->id() ?? 1, // Nếu admin chưa login thì dùng user_id = 1
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'num_people' => $request->num_people,
            'reservation_date' => $request->reservation_date,
            'shift' => $request->shift,
            'note' => $request->note,
            'status' => 'deposit_paid', // Admin tạo thì đã đặt cọc luôn
        ]);

        // Gán bàn
        $reservation->tables()->attach($request->table_ids);

        return redirect()->route('admin.datBan.index')
            ->with('success', "Tạo đơn đặt bàn thành công! Mã đơn: #{$reservation->id}");
    }

    public function show(string $id)
    {
        $reservation = Reservation::with(['menus', 'tables'])->findOrFail($id);

        return view('admin.datBan.show', compact('reservation'));
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $reservationId)
    {
        // Lấy reservation theo ID
        $reservation = Reservation::findOrFail($reservationId);

        // Cập nhật bảng trung gian để thay thế bàn cũ với bàn mới
        $newTableId = $request->table_id;
        $reservation->tables()->sync([$newTableId]);

        return redirect()->route('admin.datBan.index')
                         ->with('success', 'Chọn bàn thành công!');
    }

    public function destroy(string $id)
    {
        //
    }

    // Cập nhật trạng thái đặt bàn
    public function updateStatus(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'status' => 'required|in:pending,deposit_pending,deposit_paid,serving,completed,cancelled',
        ]);

        $reservation = Reservation::findOrFail($request->reservation_id);
        $reservation->status = $request->status;
        $reservation->save();

        return redirect()->route('admin.datBan.index')
                         ->with('success', 'Cập nhật trạng thái đặt bàn thành công!');
    }

    public function updateTables(Request $request, $id)
    {
        $request->validate([
            'table_ids' => 'required|array|min:1',
            'table_ids.*' => 'exists:tables,id',
        ], [
            'table_ids.required' => 'Vui lòng chọn ít nhất 1 bàn!',
            'table_ids.min' => 'Vui lòng chọn ít nhất 1 bàn!',
        ]);

        $reservation = Reservation::findOrFail($id);

        if (in_array($reservation->status, ['completed', 'cancelled'])) {
            return redirect()->back()
                ->with('error', 'Không thể chỉnh sửa bàn cho đơn đã hoàn tất hoặc đã hủy!');
        }

        foreach ($request->table_ids as $tableId) {
            $isBusy = \App\Models\BanAn::find($tableId)
                ->reservations()
                ->where('reservation_date', $reservation->reservation_date)
                ->where('shift', $reservation->shift)
                ->whereIn('status', ['deposit_paid', 'serving'])
                ->where('reservations.id', '!=', $reservation->id)
                ->exists();

            if ($isBusy) {
                $tableName = \App\Models\BanAn::find($tableId)->name;
                return redirect()->back()
                    ->with('error', "Bàn {$tableName} đang bận trong ca này!");
            }
        }

        $reservation->tables()->sync($request->table_ids);

        return redirect()->route('admin.datBan.index')
            ->with('success', "Đã cập nhật bàn cho đơn #{$reservation->id} thành công! (Số bàn: " . count($request->table_ids) . ")");
    }

    public function getAvailableTables(Request $request)
    {
        $date = $request->query('date');
        $shift = $request->query('shift');
        
        $allTables = BanAn::all();
        
        $busyTableIds = BanAn::whereHas('reservations', function ($query) use ($date, $shift) {
            $query->where('reservation_date', $date)
                  ->where('shift', $shift)
                  ->whereIn('status', ['deposit_paid', 'serving']);
        })->pluck('id')->toArray();
        
        return response()->json([
            'tables' => $allTables,
            'busyTableIds' => $busyTableIds,
        ]);
    }
}
