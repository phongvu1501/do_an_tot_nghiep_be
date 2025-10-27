<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhMucMonAn;
use Illuminate\Http\Request;

class DanhMucMonAnController extends Controller
{
    public function index()
    {
        $title = 'Danh Mục Món Ăn';

        $menuCategories = DanhMucMonAn::orderBy('id', 'desc')->paginate(10);

        return view('admin.thucDon.danhMucMonAn.index', compact('title', 'menuCategories'));
    }
    public function show(string $id)
    {
        $title = 'Chi tiết danh Mục Món Ăn';

        $menuCategory = DanhMucMonAn::findOrFail($id);

        return view('admin.thucDon.danhMucMonAn.show', compact('title', 'menuCategory'));
    }
    public function create()
    {
        $title = 'Thêm Mới Danh Mục Món Ăn';

        return view('admin.thucDon.danhMucMonAn.create', compact('title'));
    }
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'status' => 'required|in:active,inactive'
        ], [
            'name.required' => 'Tên danh mục món ăn không được bỏ trống !',
            'status.required' => 'Trạng thái không được bỏ trống !'
        ]);
        $existingDanhMucMonAn = DanhMucMonAn::where('name', $validateData['name'])->first();

        if ($existingDanhMucMonAn) {
            return redirect()->back()->withInput()->with('error_name', 'Tên danh mục món ăn này đã tồn tại.');
        }

        if (!is_array($validateData)) {
            return redirect()->back()->with('error', 'Dữ liệu không hợp lệ!');
        }

        DanhMucMonAn::create($validateData);

        return redirect()->route('admin.thucDon.danhMucMonAn.index')->with('success', 'Thêm mới danh mục món ăn thành công !');
    }
    public function edit(string $id)
    {
        $title = 'Chỉnh Sửa Danh Mục Món Ăn';

        $menuCategory = DanhMucMonAn::findOrFail($id);

        return view('admin.thucDon.danhMucMonAn.edit', compact('title', 'menuCategory'));
    }
    public function update(Request $request, DanhMucMonAn $menuCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:menu_categories,name,' . $menuCategory->id,
            'description' => 'nullable|string',
        ], [
            'name.unique' => 'Tên danh mục này đã tồn tại.',
        ]);

        $menuCategory->update($validated);

        return redirect()
            ->route('admin.thucDon.danhMucMonAn.index')
            ->with('success', 'Cập nhật danh mục thành công!');
    }
    public function disable(DanhMucMonAn $menuCategory)
    {
        try {
            $menuCategory->status = 'inactive';

            $menuCategory->save();

            return redirect()->route('admin.thucDon.danhMucMonAn.index')
                ->with('success', 'Đã chuyển danh mục món ăn ' . $menuCategory->name . ' sang trạng thái Tạm dừng.');
        } catch (\Exception $e) {
            // \Log::error("Lỗi vô hiệu hóa danh mục món ăn: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Không thể chuyển trạng thái danh mục món ăn. Vui lòng thử lại.');
        }
    }
}
