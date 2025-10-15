<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BanAn;
use Illuminate\Http\Request;

class BanAnController extends Controller
{
    public function index()
    {
        $tables = BanAn::orderBy('id', 'desc')->paginate(10);

        $banAn = "Trang bàn ăn";

        return view('admin.banAn.index', compact('banAn', 'tables'));
    }

    public function show(string $id)
    {
        $table = BanAn::findOrFail($id);

        $tenBan = $table->table_number;

        return view('admin.banAn.show', compact('table', 'tenBan'));
    }

    public function create()
    {
        $title = "Trang thêm mới bàn ăn";

        return view('admin.banAn.create', compact('title'));
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'table_number' => 'required|string|max:255',
            'capacity' => 'required|integer'
        ], [
            'table_number.required' => 'Tên bàn không được bỏ trống !',
            'capacity.required' => 'Số lượng người không được bỏ trống !'
        ]);
        $existingBanAn = BanAn::where('table_number', $validateData['table_number'])->first();

        if ($existingBanAn) {
            return redirect()->back()->withInput()->with('error_table_number', 'Tên bàn này đã tồn tại.');
        }

        if (!is_array($validateData)) {
            return redirect()->back()->with('error', 'Dữ liệu không hợp lệ!');
        }

        // Nếu tên lớp không bị trùng, tiến hành tạo mới lớp học
        BanAn::create($validateData);

        return redirect()->route('admin.banAn.index')->with('success', 'Thêm mới bàn ăn thành công !');
    }

    public function edit(string $id)
    {
        $title = "Trang chỉnh sửa bàn";

        $banAn = BanAn::findOrFail($id);

        return view('admin.banAn.edit', compact('title', 'banAn'));
    }

    public function update(Request $request, BanAn $banAn)
    {
        $request->validate([
            'table_number' => 'required|string|max:255|unique:tables,table_number,' . $banAn->id,
            'capacity' => 'required|integer|min:1',
            // 'status' => 'required|in:available,reserved,occupied,inactive',
        ]);

        try {
            $banAn->update([
                'table_number' => $request->table_number,
                'capacity' => $request->capacity,
                'status' => $request->status,
            ]);

            return redirect()->route('admin.banAn.index')
                ->with('success', 'Cập nhật bàn ăn ' . $banAn->table_number . ' thành công!');
        } catch (\Exception $e) {
            // \Log::error("Lỗi cập nhật bàn ăn: " . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Cập nhật thất bại. Vui lòng thử lại.');
        }
    }

    public function disable(BanAn $banAn)
    {
        try {
            $banAn->status = 'inactive';

            $banAn->save();

            return redirect()->route('admin.banAn.index')
                ->with('success', 'Đã chuyển bàn ăn ' . $banAn->table_number . ' sang trạng thái Tạm dừng.');
        } catch (\Exception $e) {
            // \Log::error("Lỗi vô hiệu hóa bàn ăn: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Không thể chuyển trạng thái bàn ăn. Vui lòng thử lại.');
        }
    }
}
