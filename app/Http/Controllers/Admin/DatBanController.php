<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BanAn;
use App\Models\Reservation;
use Illuminate\Http\Request;

class DatBanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tables = Reservation::with(['menus', 'tables'])->orderBy('id', 'desc')->paginate(10);

        $title = "Trang quản lý đặt bàn";

        // Lấy danh sách các bàn còn trống
        $availableTables = BanAn::where('status', 'active')
            ->whereDate('available_date', '>=', \Carbon\Carbon::today())
            ->get();

        return view('admin.datBan.index', compact('title', 'tables', 'availableTables'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $reservation = Reservation::with(['menus', 'tables'])->findOrFail($id);

        // $tenBan = $table->table_number;

        return view('admin.datBan.show', compact('reservation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $reservationId)
    {
        // Kiểm tra xem có bàn nào được chọn không
        // $request->validate([
        //     'table_id' => 'required|array|min:1', // Người dùng phải chọn ít nhất 1 bàn
        //     'table_id.*' => 'exists:tables,id', // Kiểm tra bảng có tồn tại trong bảng tables
        // ]);

        // Lấy reservation theo ID
        $reservation = Reservation::findOrFail($reservationId);

        // Kiểm tra bàn cũ (nếu có) và cập nhật trạng thái của bàn cũ thành "trống"
        $oldTableId = $reservation->tables->first()->id ?? null;  // Lấy bàn cũ nếu có

        if ($oldTableId) {
            // Cập nhật trạng thái bàn cũ về "trống"
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // Cập nhật trạng thái đặt bàn
    public function updateStatus(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'status' => 'required|in:pending,confirmed,serving,completed,cancelled,suspended',
        ]);

        $reservation = Reservation::findOrFail($request->reservation_id);
        $reservation->status = $request->status;
        $reservation->save();

        return redirect()->route('admin.datBan.index')
                         ->with('success', 'Cập nhật trạng thái đặt bàn thành công!');
    }
}
