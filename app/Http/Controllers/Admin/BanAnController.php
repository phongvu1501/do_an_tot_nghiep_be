<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BanAn;
use Illuminate\Http\Request;

class BanAnController extends Controller
{
    public function index(Request $request)
    {
        $currentHour = now()->hour;
        if ($currentHour >= 6 && $currentHour < 10) {
            $currentShift = 'morning';
        } elseif ($currentHour >= 10 && $currentHour < 14) {
            $currentShift = 'afternoon';
        } elseif ($currentHour >= 14 && $currentHour < 18) {
            $currentShift = 'evening';
        } else {
            $currentShift = 'night';
        }

        $filterDate = $request->filled('date') ? $request->date : now()->toDateString();
        $filterShift = $request->filled('shift') ? $request->shift : $currentShift;

        $tables = BanAn::orderBy('id', 'asc')->paginate(20);

        $banAn = "Quản lý bàn ăn";

        return view('admin.banAn.index', compact('banAn', 'tables', 'filterDate', 'filterShift'));
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
        ], [
            'name.required' => 'Tên bàn không được bỏ trống!',
            'name.unique' => 'Tên bàn này đã tồn tại!',
            'limit_number.required' => 'Số lượng người không được bỏ trống!',
            'limit_number.integer' => 'Số lượng người phải là số nguyên!',
            'limit_number.min' => 'Số lượng người phải lớn hơn 0!',
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
        ], [
            'name.required' => 'Tên bàn không được bỏ trống!',
            'name.unique' => 'Tên bàn này đã tồn tại!',
            'limit_number.required' => 'Số lượng người không được bỏ trống!',
            'limit_number.integer' => 'Số lượng người phải là số nguyên!',
            'limit_number.min' => 'Số lượng người phải lớn hơn 0!',
        ]);

        try {
            $banAn->update([
                'name' => $request->name,
                'limit_number' => $request->limit_number,
            ]);

            return redirect()->route('admin.banAn.index')
                ->with('success', 'Cập nhật bàn ăn ' . $banAn->name . ' thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Cập nhật thất bại. Vui lòng thử lại.');
        }
    }

    public function destroy(BanAn $banAn)
    {
        try {
            $banAn->delete();

            return redirect()->route('admin.banAn.index')
                ->with('success', 'Đã xóa bàn ăn ' . $banAn->name . ' thành công.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Không thể xóa bàn ăn. Vui lòng thử lại.');
        }
    }
}