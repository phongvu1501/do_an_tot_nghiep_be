<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Đăng ký tài khoản
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users|regex:/^[0-9]{9,15}$/',
            'password' => 'required|string|min:6|confirmed',
        ], [
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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Tạo mã OTP
        $otp = rand(100000, 999999);
        // Tạo tài khoản
         $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
            'is_verified' => false,
        ]);


        // Gửi email chứa mã OTP
        try {
            Mail::raw("Xin chào {$user->name}, chào mừng bạn đã đến với website DATBAN, xin mời bạn chọn bàn và món ăn",
             function ($m) use ($user) {
                $m->to($user->email)->subject('Xác nhận đăng ký tài khoản thành công');
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => true,
                'message' => 'Đăng ký thành công, nhưng không thể gửi email OTP.',
                'user' => $user,
            ], 201);
        }

        return response()->json([
            'status' => true,
            'message' => 'Đăng ký thành công! Chào mừng bạn đến với website DATBAN.',
            'user' => $user,
        ], 201);
    }

    //  API xác nhận OTP
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy người dùng.'], 404);
        }

        if ($user->is_verified) {
            return response()->json(['status' => true, 'message' => 'Tài khoản đã được xác minh.'], 200);
        }

        if ($user->otp_code !== $request->otp) {
            return response()->json(['status' => false, 'message' => 'Mã OTP không chính xác.'], 400);
        }

        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['status' => false, 'message' => 'Mã OTP đã hết hạn.'], 400);
        }

        // Xác thực thành công
        $user->update([
            'is_verified' => true,
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Xác thực tài khoản thành công!',
            'user' => $user,
        ], 200);
    }

    // API gửi lại OTP (tùy chọn)
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy người dùng.'], 404);
        }

        if ($user->is_verified) {
            return response()->json(['status' => true, 'message' => 'Tài khoản đã được xác minh.'], 200);
        }

        // Tạo mã OTP mới
        $otp = rand(100000, 999999);
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        try {
            Mail::raw("Mã OTP mới của bạn là: {$otp} (hết hạn sau 10 phút)", function ($m) use ($user) {
                $m->to($user->email)->subject('Gửi lại mã OTP');
            });
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Không thể gửi lại email OTP.'], 500);
        }

        return response()->json(['status' => true, 'message' => 'Đã gửi lại mã OTP. Vui lòng kiểm tra email.'], 200);
    }

    
    /**
     * Đăng nhập
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'phone' => 'required|string|regex:/^[0-9]{9,11}$/',
            'password' => 'required|string',
        ], [
            'phone.required' => 'Trường số điện thoại là bắt buộc.',
            'phone.regex' => 'Số điện thoại không hợp lệ (chỉ chứa 9-11 chữ số).',
            'password.required' => 'Trường mật khẩu là bắt buộc.',
        ]);

        $user = User::where('phone', $credentials['phone'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Số điện thoại hoặc mật khẩu không đúng.'
            ], 401);
        }

        // Tạo token Sanctum
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công!',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 200);
    }

    /**
     * Thông tin user hiện tại
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    }

    /**
     * Logout (Xóa token hiện tại)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công!'
        ]);
    }

   
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id . '|regex:/^[0-9]{9,15}$/',
        ], [
            'name.required' => 'Vui lòng nhập tên.',
            'name.string' => 'Tên phải là chuỗi ký tự.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã được sử dụng bởi tài khoản khác.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.unique' => 'Số điện thoại đã được sử dụng bởi tài khoản khác.',
            'phone.regex' => 'Số điện thoại không hợp lệ (chỉ chứa 9-15 chữ số).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin thành công!',
            'user' => $user
        ]);
    }

  
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu hiện tại không đúng.'
            ], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công!'
        ]);
    }

}


