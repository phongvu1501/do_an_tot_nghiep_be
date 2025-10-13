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
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable|max:1000',
        ]);

        MenuCategory::create($request->only('name', 'description'));

        return redirect()->route('menu-categories.index')->with('success', 'Thêm danh mục thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    // 4. Hiển thị form sửa danh mục
    public function edit($id)
    {
        $category = MenuCategory::findOrFail($id);
        return view('admin.menu_categories.edit', compact('category'));
    }

    //  5. Cập nhật danh mục
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable|max:1000',
        ]);

        $category = MenuCategory::findOrFail($id);
        $category->update($request->only('name', 'description'));

        return redirect()->route('menu-categories.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    // 6. Xoá danh mục
    public function destroy($id)
    {
        $category = MenuCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('menu-categories.index')->with('success', 'Xoá danh mục thành công!');
    }
}
