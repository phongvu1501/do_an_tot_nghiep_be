<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    public function showAdmins()
    {
        $admins = User::where('role', 'admin')->get();
        return view('admin.accounts.admins', compact('admins'));
    }

    public function showUsers()
    {
        $users = User::where('role', 'user')->get();
        return view('admin.accounts.users', compact('users'));
    }

    public function profile()
    {
        $user = Auth::user(); // Lấy người dùng hiện tại (admin đang đăng nhập)
        return view('admin.accounts.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ✅ Validate dữ liệu nhập
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:10',
        ], [
            'name.required' => 'Vui lòng nhập tên của bạn.',
            'name.max'      => 'Tên không được vượt quá 255 ký tự.',
            'phone.max'     => 'Số điện thoại không hợp lệ.',
        ]);

        // ✅ Cập nhật dữ liệu
        $user->name  = $validated['name'];
        $user->phone = $validated['phone'] ?? null;
        $user->save(); // dùng save() thay vì update() cho chắc chắn

        return redirect()->back()->with('success', 'Cập nhật thông tin thành công!');
    }

    public function index()
    {
        $users = User::where('role', 'user')->get();
        return view('admin.users.index', compact('users'));
    }

    // Xem chi tiết user (bao gồm bàn và món đã đặt)
  public function show($id)
{
    $user = User::with(['reservations.menus'])->findOrFail($id);
    return view('admin.accounts.show', compact('user'));
}



    public function loc(Request $request)
{
    $query = User::query();

    // Lọc theo tên
    if ($request->name) {
        $query->where('name', 'like', '%' . $request->name . '%');
    }


    // Lọc theo email
    if ($request->email) {
        $query->where('email', 'like', '%' . $request->email . '%');
    }

    // Lọc theo số điện thoại
    if ($request->phone) {
        $query->where('phone', 'like', '%' . $request->phone . '%');
    }

    // Lọc theo ngày tạo
    if ($request->date) {
        $query->whereDate('created_at', $request->date);
    }

    $users = $query->orderBy('created_at', 'desc')->get();

    return view('admin.accounts.users', compact('users'));


}
public function detail($id)
{
    $user = User::with(['reservations.tables', 'reservations.reservationItems.menu'])->findOrFail($id);
    return view('admin.user.detail', compact('user')); // trả về HTML


}


}



