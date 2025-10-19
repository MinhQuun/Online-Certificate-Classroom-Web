<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\UserController;

use App\Http\Controllers\Student\ForgotPasswordController;

// Student-facing public pages
Route::get('/', [\App\Http\Controllers\Student\CourseController::class, 'index'])->name('student.courses.index');
Route::get('/courses/{slug}', [\App\Http\Controllers\Student\CourseController::class, 'show'])->name('student.courses.show');
Route::get('/lessons/{maBH}', [\App\Http\Controllers\Student\LessonController::class, 'show'])->name('student.lessons.show');

// Legacy student paths redirect to new URLs
Route::redirect('/student/courses', '/');
Route::get('/student/courses/{slug}', fn($slug) => redirect()->route('student.courses.show', $slug));
Route::get('/student/lessons/{maBH}', fn($maBH) => redirect()->route('student.lessons.show', $maBH));

// Điều hướng mở modal đăng nhập trên trang chủ (giữ nguyên name & hành vi)
Route::get('/login', function (Request $request) {
    $redir = $request->query('redirect', url()->previous());
    return redirect()->to(route('student.courses.index') . '?open=login&redirect=' . urlencode($redir));
})->name('login');

// Đăng nhập / Đăng ký / Đăng xuất
Route::post('/login',    [UserController::class, 'login'])->name('users.login');
Route::get('/register', function (Request $request) {
    $redir = $request->query('redirect', url()->previous());
    return redirect()->to(route('student.courses.index') . '?open=register&redirect=' . urlencode($redir));
})->name('register');
Route::post('/register', [UserController::class, 'store'])->name('users.store');
Route::post('/logout',   [UserController::class, 'logout'])->name('logout');

// Quên mật khẩu qua OTP
Route::name('password.')->group(function () {
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetCode'])->name('send');    // POST -> password.send
    Route::post('/verify-otp',      [ForgotPasswordController::class, 'verifyOtp'])->name('verify');      // POST -> password.verify
    Route::post('/reset-password',  [ForgotPasswordController::class, 'resetPassword'])->name('update');  // POST -> password.update
});