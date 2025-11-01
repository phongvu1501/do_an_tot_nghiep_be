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
            if ($request->status == 'waiting_for_payment') {
                $query->whereIn('status', ['waiting_for_payment', 'pending']);
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
            'status' => 'required|in:waiting_for_payment,confirmed,completed,cancelled,pending',
        ]);

        $reservation = Reservation::findOrFail($request->reservation_id);
        $reservation->status = $request->status;
        $reservation->save();

        return redirect()->route('admin.datBan.index')
                         ->with('success', 'Cập nhật trạng thái đặt bàn thành công!');
    }
}
