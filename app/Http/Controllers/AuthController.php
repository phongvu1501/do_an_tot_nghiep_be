<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    // POST /api/register
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        if ($validator->fails()) {
                $errors = $validator->errors();

                // If email already exists, return a clearer 409 Conflict with VN message
                if ($errors->has('email')) {
                    $emailFirst = (string) $errors->first('email');
                    if (str_contains($emailFirst, 'taken') || str_contains($emailFirst, 'exists')) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Email đã được đăng ký. Vui lòng đăng nhập hoặc dùng chức năng quên mật khẩu.',
                            'errors' => $errors,
                        ], 409);
                    }
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $errors,
                ], 422);
        }

        $data = $validator->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], // 'hashed' cast on model will hash
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User registered',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }

    // POST /api/login
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = $validator->validated();

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 200);
    }

    // POST /api/logout
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user) {
            // Revoke current access token
            $current = $user->currentAccessToken();
            if ($current) {
                $current->delete();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out',
        ], 200);
    }

    // GET /api/user
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ], 200);
    }
}
