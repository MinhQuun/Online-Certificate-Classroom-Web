<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:nguoidung,email',
        ]);

        // Có thể dùng số 6 chữ số: $token = (string) random_int(100000, 999999);
        $token = Str::random(6);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        Mail::raw(
            "Mã OTP đặt lại mật khẩu của bạn là: {$token} (hiệu lực 10 phút).",
            function ($message) use ($request) {
                $message->to($request->email)->subject('Mã OTP đặt lại mật khẩu');
            }
        );

        return response()->json([
            'status' => true,
            'message' => 'Mã OTP đã được gửi tới email của bạn.',
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:nguoidung,email',
            'token' => 'required|string',
        ]);

        $record = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$record || Carbon::parse($record->created_at)->lt(now()->subMinutes(10))) {
            return response()->json([
                'status' => false,
                'message' => 'Mã OTP không hợp lệ hoặc đã hết hạn.',
            ], 422);
        }

        if (!Hash::check($request->token, $record->token)) {
            return response()->json([
                'status' => false,
                'message' => 'Mã OTP không hợp lệ hoặc đã hết hạn.',
            ], 422);
        }

        return response()->json([
            'status' => true,
            'message' => 'OTP hợp lệ, vui lòng đặt mật khẩu mới.',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:nguoidung,email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $record = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (
            !$record ||
            Carbon::parse($record->created_at)->lt(now()->subMinutes(10)) ||
            !Hash::check($request->token, $record->token)
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Mã OTP không hợp lệ hoặc đã hết hạn.',
            ], 422);
        }

        $user = DB::table('nguoidung')
            ->where('email', $request->email)
            ->first();

        // Không cho đặt lại trùng mật khẩu hiện tại
        if ($user && Hash::check($request->password, $user->matKhau)) {
            return response()->json([
                'status' => false,
                'errors' => [
                    'password' => [
                        'Mật khẩu mới không được trùng với mật khẩu hiện tại.',
                    ],
                ],
            ], 422);
        }

        DB::table('nguoidung')
            ->where('email', $request->email)
            ->update(['matKhau' => Hash::make($request->password)]);

        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Mật khẩu đã được đặt lại thành công.',
        ]);
    }
}