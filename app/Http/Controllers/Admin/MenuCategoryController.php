<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuCategory;

class MenuCategoryController extends Controller
{
    // 1. Hiển thị danh sách danh mục
    public function index()
    {
        $categories = MenuCategory::all(); // Lấy tất cả danh mục
        return view('admin.menu_categories.index', compact('categories'));
    }

    // 2. Hiển thị form thêm mới
    public function create()
    {
        return view('admin.menu_categories.create');
    }

    // 3. Lưu dữ liệu khi thêm mới
    public function store(Request $request)
    {
        // Validate dữ liệu gửi lên
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:menu_categories,name',
            'description' => 'nullable|string',
        ],[
            'name.unique' => 'Tên danh mục này đã tồn tại.',
        ]);

        // Tạo danh mục mới
        MenuCategory::create($validated);

        // Quay lại trang danh sách với thông báo
        return redirect()
            ->route('admin.menu_categories.index')
            ->with('success', 'Thêm danh mục món ăn thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('admin.menu_categories.show', compact('menuCategory')); 
    }

    // 4. Hiển thị form sửa danh mục
    public function edit(MenuCategory $menuCategory)
    {
        return view('admin.menu_categories.edit', compact('menuCategory'));
    }

    //  5. Cập nhật danh mục
    public function update(Request $request, MenuCategory $menuCategory)
    {
        // Validate dữ liệu
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:menu_categories,name,' . $menuCategory->id,
            'description' => 'nullable|string',
        ],[
            'name.unique' => 'Tên danh mục này đã tồn tại.',
        ]);

        // Cập nhật
        $menuCategory->update($validated);

        return redirect()
            ->route('admin.menu_categories.index')
            ->with('success', 'Cập nhật danh mục thành công!');
    }

    // 6. Xoá danh mục
    public function destroy(MenuCategory $menuCategory)
    {
         $menuCategory->delete();

        return redirect()
            ->route('admin.menu_categories.index')
            ->with('success', 'Xóa danh mục thành công!');
    }
}
