<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;

class PasswordResetController extends Controller
{
    // 1️⃣ Gửi email quên mật khẩu
    public function forgot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required','email','exists:users,email'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'=>false,
                'message'=>'Validation error',
                'errors'=>$validator->errors()
            ], 422);
        }

        $email = $request->email;
        $token = Str::random(64);

        // Lưu token hashed vào DB
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]
        );

        // Gửi email (hoặc log nếu MAIL_MAILER=log)
        Mail::to($email)->send(new ResetPasswordMail($token, $email));

        return response()->json([
            'success'=>true,
            'message'=>'Reset password token sent to your email.'
        ]);
    }

    // 2️⃣ Reset password
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required','email','exists:users,email'],
            'token' => ['required'],
            'password' => ['required','confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'=>false,
                'message'=>'Validation error',
                'errors'=>$validator->errors()
            ], 422);
        }

        $record = DB::table('password_resets')->where('email', $request->email)->first();

     if (!$record || !Hash::check($request->token, $record->token) || Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
    return response()->json([
        'success'=>false,
        'message'=>'Invalid or expired token.'
    ], 400);
}


        // Cập nhật password mới
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Xóa token trong password_resets
        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json([
            'success'=>true,
            'message'=>'Password reset successfully.'
        ]);
    }
}
