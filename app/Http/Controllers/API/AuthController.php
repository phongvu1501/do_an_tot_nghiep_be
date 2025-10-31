<?php

namespace App\Http\Controllers\Api;



use App\Models\User;
use Tymon\JWTAuth\JWTGuard;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Đăng ký tài khoản
     */
    public function register(Request $request)
    {
        // ✅ Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // ✅ Tạo tài khoản
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Gán role mặc định
        ]);

        // ✅ Gửi email xác nhận
        try {
            Mail::raw("Xin chào {$user->name}, bạn đã đăng ký thành công tài khoản!", function ($m) use ($user) {
                $m->to($user->email)->subject('Đăng ký tài khoản thành công');
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => true,
                'message' => 'Đăng ký thành công, nhưng không thể gửi email xác nhận.',
                'user' => $user,
            ], 201);
        }

        return response()->json([
            'status' => true,
            'message' => 'Đăng ký thành công! Vui lòng kiểm tra email của bạn.',
            'user' => $user,
        ], 201);
    }

    //-----------------------------------------------------------------------------------------------------------------------
    //PHẦN API JWT 
    /** 
     * @var JWTGuard 
     */
    // protected $auth;

    // public function __construct()
    // {
    //     /** @var JWTGuard $auth */
    //     $this->auth = auth('api');
    // }

    // public function login(Request $request)
    // {
    //     $credentials = $request->only('email', 'password');

    //     if (! $token = $this->auth->attempt($credentials)) {
    //         return response()->json(['error' => 'Email hoặc mật khẩu không đúng!'], 401);
    //     }

    //     return $this->respondWithToken($token);
    // }

    // public function me()
    // {
    //     return response()->json($this->auth->user());
    // }

    // public function logout()
    // {
    //     try {
    //         $this->auth->logout(true);
    //         return response()->json(['message' => 'Đăng xuất thành công!']);
    //     } catch (JWTException $e) {
    //         return response()->json(['error' => 'Token không hợp lệ hoặc đã hết hạn!'], 400);
    //     }
    // }

    // public function refresh()
    // {
    //     try {
    //         $newToken = $this->auth->refresh();
    //         return $this->respondWithToken($newToken);
    //     } catch (JWTException $e) {
    //         return response()->json(['error' => 'Token hết hạn, vui lòng đăng nhập lại!'], 401);
    //     }
    // }

    // protected function respondWithToken($token)
    // {
    //     return response()->json([
    //         'status'       => true,
    //         'access_token' => $token,
    //         'token_type'   => 'bearer',
    //         'expires_in'   => $this->auth->factory()->getTTL() * 60,
    //         'user'         => $this->auth->user(),
    //     ]);
    // }
}

//KHÔNG DÙNG JWT NỮA, DÙNG Session + CSRF + Middleware auth
