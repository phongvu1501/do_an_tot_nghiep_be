<?php

namespace App\Http\Controllers;

use App\Mail\OTPMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

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
                'phone.regex' => 'Số điện thoại không hợp lệ (chỉ chứa 9-15 chữ số).',
                'password.required' => 'Trường mật khẩu là bắt buộc.',
                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
                'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            ]
        );

        $otp = rand(100000, 999999); // tạo OTP 6 số
        $expiresAt = Carbon::now()->addMinutes(5); // hết hạn sau 5 phút

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'otp' => $otp,
            'otp_expires_at' => $expiresAt,
        ]);

        // Gửi email OTP sử dụng view Blade
        Mail::send('emails.otp', ['user' => $user, 'otp' => $otp], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Mã OTP xác thực tài khoản');
        });


        return redirect()->route('verify.otp.form', ['email' => $user->email])
            ->with('success', 'Đăng ký thành công! Vui lòng nhập mã OTP đã được gửi tới email.');
    }

    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Hiển thị form nhập OTP
    public function showOTPForm(Request $request)
    {
        $email = $request->email;
        return view('auth.verify-otp', compact('email'));
    }

    // Xử lý xác thực OTP
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6',
        ], [
            'otp.digits' => 'Mã OTP phải gồm 6 chữ số.',
            'email.exists' => 'Email không tồn tại.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Kiểm tra OTP
        if ($user->otp !== $request->otp) {
            return back()->withErrors(['otp' => 'Mã OTP không đúng.']);
        }

        // Kiểm tra hết hạn
        if (Carbon::now()->gt($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Mã OTP đã hết hạn. Vui lòng đăng ký lại.']);
        }

        // Xác nhận người dùng
        $user->is_verified = true;
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        return redirect()->route('login')->with('success', 'Xác thực thành công! Bạn có thể đăng nhập.');
    }


    // Xử lý đăng nhập
    // public function login(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required|string',
    //     ]);

    //     $user = User::where('email', $credentials['email'])->first();

    //     if (!$user || !Hash::check($credentials['password'], $user->password)) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Email hoặc mật khẩu không đúng.'
    //         ], 401);
    //     }

    //     // Tạo token Sanctum
    //     $token = $user->createToken('api-token')->plainTextToken;

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Đăng nhập thành công!',
    //         'data' => [
    //             'user' => $user,
    //             'token' => $token,
    //             'token_type' => 'Bearer',
    //         ],
    //     ], 200);
    // }



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
