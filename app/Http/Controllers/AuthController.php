<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    // Hiển thị form đăng ký
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Xử lý đăng ký
   public function register(Request $request)
{
    $request->validate(
        [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users|regex:/^[0-9]{9,15}$/',
            'password' => 'required|string|min:6|confirmed',
        ],
        [
            'name.required' => 'Trường họ tên là bắt buộc.',
            'email.required' => 'Trường email là bắt buộc.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã tồn tại.',
            'phone.required' => 'Trường số điện thoại là bắt buộc.',
            'phone.unique' => 'Số điện thoại đã được sử dụng.',
            'phone.regex' => 'Số điện thoại không hợp lệ (chỉ chứa 9–15 chữ số).',
            'password.required' => 'Trường mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]
    );

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'password' => Hash::make($request->password),
        'role' => 'user', // mặc định là user
    ]);

    return redirect()->route('login')->with('success', 'Đăng ký thành công! Hãy đăng nhập.');
}


    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Xử lý đăng nhập
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Nếu là admin → vào trang quản lý admin
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            // Nếu là user → vào dashboard bình thường
            return redirect()->route('dashboard');
        }


        return back()->with('error', 'Sai email hoặc mật khẩu!');

        $credentials = $validator->validated();
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $token = $user->createToken('api-token', ['*'], now()->addHours(2))->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công!',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => now()->addHours(2)->toDateTimeString(),
            ],
        ], 200);

    }

    public function logout(Request $request)
    {
        Auth::logout();

        // Hủy session hiện tại và tạo token CSRF mới
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Bạn đã đăng xuất. Vui lòng đăng nhập lại để tiếp tục.');
    }


    public function dashboard(): RedirectResponse
    {
        $user = Auth::user();

        if ($user && $user->role === 'user') {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Tài khoản của bạn không có quyền truy cập trang này!');
        }

        return redirect()->route('admin.dashboard');
    }
}
