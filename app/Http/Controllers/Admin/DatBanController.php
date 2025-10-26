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
            if ($request->status == 'waiting_payment') {
                $query->whereIn('status', ['waiting_payment', 'pending']);
            } else {
                $query->where('status', $request->status);
            }
        }

        $tables = $query->orderBy('id', 'desc')->paginate(10)->appends($request->except('page'));

        $title = "Trang quản lý đặt bàn";

        $availableTables = BanAn::where('status', 'active')->get();

        return view('admin.datBan.index', compact('title', 'tables', 'availableTables'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
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

        // Kiểm tra bàn cũ (nếu có) và cập nhật trạng thái của bàn cũ thành "trống"
        $oldTableId = $reservation->tables->first()->id ?? null;

        if ($oldTableId) {
            BanAn::where('id', $oldTableId)->update(['status' => 'active']);
        }

        // Kiểm tra bàn mới và cập nhật trạng thái bàn mới thành "khả dụng"
        $newTableId = $request->table_id;

        // Cập nhật bảng trung gian để thay thế bàn cũ với bàn mới
        $reservation->tables()->sync([$newTableId]);

        BanAn::where('id', $request->table_id)->update(['status' => 'inactive']);

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
            'status' => 'required|in:waiting_payment,confirmed,completed,cancelled',
        ]);

        $reservation = Reservation::findOrFail($request->reservation_id);
        $reservation->status = $request->status;
        $reservation->save();

        return redirect()->route('admin.datBan.index')
                         ->with('success', 'Cập nhật trạng thái đặt bàn thành công!');
    }
}
