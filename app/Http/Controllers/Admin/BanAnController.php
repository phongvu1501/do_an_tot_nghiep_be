<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BanAn;
use Illuminate\Http\Request;

class BanAnController extends Controller
{
    public function index(Request $request)
    {
        $tables = BanAn::orderBy('id', 'desc')->paginate(10);

        $banAn = "Danh sách bàn ăn";

        return view('admin.banAn.index', compact('banAn', 'tables'));
    }

    public function show(string $id)
    {
        $table = BanAn::findOrFail($id);

        $tenBan = $table->name;

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
            'name' => 'required|string|max:255|unique:tables,name',
            'limit_number' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'Tên bàn không được bỏ trống!',
            'name.unique' => 'Tên bàn này đã tồn tại!',
            'limit_number.required' => 'Số lượng người không được bỏ trống!',
            'limit_number.integer' => 'Số lượng người phải là số nguyên!',
            'limit_number.min' => 'Số lượng người phải lớn hơn 0!',
            'status.in' => 'Trạng thái không hợp lệ!',
        ]);

        BanAn::create($validateData);

        return redirect()->route('admin.banAn.index')->with('success', 'Thêm mới bàn ăn thành công!');
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
            'name' => 'required|string|max:255|unique:tables,name,' . $banAn->id,
            'limit_number' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'Tên bàn không được bỏ trống!',
            'name.unique' => 'Tên bàn này đã tồn tại!',
            'limit_number.required' => 'Số lượng người không được bỏ trống!',
            'limit_number.integer' => 'Số lượng người phải là số nguyên!',
            'limit_number.min' => 'Số lượng người phải lớn hơn 0!',
            'status.in' => 'Trạng thái không hợp lệ!',
        ]);

        try {
            $banAn->update([
                'name' => $request->name,
                'limit_number' => $request->limit_number,
                'status' => $request->status,
            ]);

            return redirect()->route('admin.banAn.index')
                ->with('success', 'Cập nhật bàn ăn ' . $banAn->name . ' thành công!');
        } catch (\Exception $e) {
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
                ->with('success', 'Đã chuyển bàn ăn ' . $banAn->name . ' sang trạng thái Tạm dừng.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Không thể chuyển trạng thái bàn ăn. Vui lòng thử lại.');
        }
    }
}
