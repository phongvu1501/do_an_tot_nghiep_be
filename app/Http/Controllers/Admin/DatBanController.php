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
                ->where('status', 'confirmed')
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
}
