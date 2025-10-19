<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BanAn;
use Illuminate\Http\Request;

class BanAnController extends Controller
{
    public function index(Request $request)
    {
        // Lấy ngày từ request hoặc sử dụng ngày hiện tại nếu không có giá trị tìm kiếm
        $searchDate = $request->get('search_date', date('Y-m-d'));

        // Lọc các bàn ăn theo ngày
        $tables = BanAn::whereDate('available_date', $searchDate)
                       ->orderBy('id', 'desc')
                       ->paginate(10);

        $banAn = "Trang bàn ăn";

        return view('admin.banAn.index', compact('banAn', 'tables', 'searchDate'));

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
            'capacity' => 'required|integer',
            'status' => 'required|in:active,inactive',
            'available_date' => 'date',
            'available_from' => 'date_format:H:i',
            'available_until' => 'date_format:H:i',
        ], [
            'table_number.required' => 'Tên bàn không được bỏ trống !',
            'capacity.required' => 'Số lượng người không được bỏ trống !',
            'status.in' => 'Trạng thái không hợp lệ !',
            'available_date.date' => 'Ngày có sẵn không hợp lệ !',
            'available_from.date_format' => 'Thời gian có sẵn không hợp lệ !',
            'available_until.date_format' => 'Thời gian hết có sẵn không hợp lệ !',
        ]);

        $existingBanAn = BanAn::where('table_number', $validateData['table_number'])
                        ->where('available_date', $validateData['available_date'])  // Thêm điều kiện ngày
                        ->first();

        if ($existingBanAn) {
            return redirect()->back()->withInput()->with('error_table_number', 'Tên bàn này đã tồn tại.');
        }

        if (!is_array($validateData)) {
            return redirect()->back()->with('error', 'Dữ liệu không hợp lệ!');
        }

        // Nếu tên bàn ăn trong ngày không bị trùng, tiến hành tạo mới bàn ăn
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
