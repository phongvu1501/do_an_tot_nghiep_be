<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    // 1. Hiển thị danh sách món ăn
    public function index()
    {
        $menus = Menu::with('category')->get();

        // Đếm số món đã bị xóa mềm
        $trashedCount = Menu::onlyTrashed()->count();

        return view('admin.menus.index', compact('menus', 'trashedCount'));
    }

    // 2. Form thêm mới
    public function create()
    {
        $categories = MenuCategory::all();
        return view('admin.menus.create', compact('categories'));
    }

    // 3. Lưu món ăn mới
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:menus,name',
            'category_id' => 'required|exists:menu_categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|boolean',
        ], [
            'name.required' => 'Vui lòng nhập tên món ăn.',
            'name.unique' => 'Tên món ăn này đã tồn tại.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'category_id.exists' => 'Danh mục được chọn không hợp lệ.',
            'price.required' => 'Vui lòng nhập giá món ăn.',
            'price.numeric' => 'Giá phải là số.',
            'image.image' => 'File tải lên phải là hình ảnh.',
            'image.mimes' => 'Chỉ chấp nhận các định dạng: jpeg, png, jpg, webp.',
            'image.max' => 'Kích thước ảnh tối đa là 2MB.',
        ]);

        // Upload ảnh nếu có
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/menus', 'public');
            $validated['image'] = $path;
        }

        Menu::create($validated);

        return redirect()->route('admin.menus.index')->with('success', 'Thêm món ăn thành công!');
    }

    // 4. Xem chi tiết món ăn
    public function show(Menu $menu)
    {
        return view('admin.menus.show', compact('menu'));
    }

    // 5. Form chỉnh sửa
    public function edit(Menu $menu)
    {
        $categories = MenuCategory::all();
        return view('admin.menus.edit', compact('menu', 'categories'));
    }

    // 6. Cập nhật món ăn
    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:menus,name,' . $menu->id,
            'category_id' => 'required|exists:menu_categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|boolean',
        ]);

        // Nếu có upload ảnh mới → xóa ảnh cũ rồi lưu ảnh mới
        if ($request->hasFile('image')) {
            if ($menu->image && Storage::disk('public')->exists($menu->image)) {
                Storage::disk('public')->delete($menu->image);
            }
            $path = $request->file('image')->store('uploads/menus', 'public');
            $validated['image'] = $path;
        }

        $menu->update($validated);

        return redirect()->route('admin.menus.index')->with('success', 'Cập nhật món ăn thành công!');
    }

    // 7. Xóa món ăn
    public function destroy(Menu $menu)
    {
        

        $menu->delete();

        return redirect()->route('admin.menus.index')->with('success', 'Xóa món ăn thành công!');
    }

    // Hiển thị danh sách các món đã xóa mềm
    public function trash()
    {
        $trashedMenus = Menu::onlyTrashed()->with('category')->get();
        // foreach ($trashedMenus as $menu) {
        //     dd($menu->image); // xem có dữ liệu đường dẫn ảnh không
        // }
        return view('admin.menus.trash', compact('trashedMenus'));
    }

    // Khôi phục món ăn
    public function restore($id)
    {
        $menu = Menu::onlyTrashed()->findOrFail($id);
        $menu->restore();

        return redirect()->route('admin.menus.trash')->with('success', 'Khôi phục món ăn thành công!');
    }

    // Xóa vĩnh viễn món ăn (và xóa ảnh luôn)
    public function forceDelete($id)
{
    $menu = Menu::onlyTrashed()->findOrFail($id);

    if ($menu->image && Storage::disk('public')->exists($menu->image)) {
        Storage::disk('public')->delete($menu->image); // Xóa file thật
    }

    $menu->forceDelete();

    return redirect()->route('admin.menus.trash')->with('success', 'Đã xóa vĩnh viễn món ăn!');
}

}