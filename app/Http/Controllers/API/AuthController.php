<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'user', // mặc định user
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký thành công! Hãy đăng nhập.',
            'user' => $user
        ], 201);
    }

    /**
     * Đăng nhập
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email hoặc mật khẩu không đúng.'
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
}
