<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    private const OTP_TTL_MINUTES = 10;
    private const OTP_RESEND_COOLDOWN_SECONDS = 60;

    public function showRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:nguoidung,email',
        ]);

        $now = Carbon::now();
        $existingRequest = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if ($existingRequest) {
            $availableAt = Carbon::parse($existingRequest->created_at)
                ->addSeconds(self::OTP_RESEND_COOLDOWN_SECONDS);

            if ($availableAt->isFuture()) {
                $secondsLeft = $now->diffInSeconds($availableAt);

                return response()->json([
                    'status' => false,
                    'message' => "Vui lòng chờ {$secondsLeft}s trước khi yêu cầu mã OTP mới.",
                ], 429);
            }
        }

        $token = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = (clone $now)->addMinutes(self::OTP_TTL_MINUTES);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => $now,
            ]
        );

        Mail::send('emails.ResetPassMail', [
            'otp' => $token,
            'email' => $request->email,
            'expiresAt' => $expiresAt,
            'sentAt' => $now,
            'appName' => config('app.name', 'Online Certificate Classroom'),
            'supportEmail' => config('mail.from.address'),
        ], function ($message) use ($request) {
            $message->to($request->email)->subject('Mã OTP đặt lại mật khẩu');
        });

        return response()->json([
            'status' => true,
            'message' => 'Mã OTP đã được gửi tới email của bạn.',
            'expires_at' => $expiresAt->toIso8601String(),
            'cooldown' => self::OTP_RESEND_COOLDOWN_SECONDS,
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

        if (!$record || Carbon::parse($record->created_at)->lt(now()->subMinutes(self::OTP_TTL_MINUTES))) {
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
            Carbon::parse($record->created_at)->lt(now()->subMinutes(self::OTP_TTL_MINUTES)) ||
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
