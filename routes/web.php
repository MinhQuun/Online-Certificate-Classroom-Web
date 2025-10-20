<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// =====================
// Controllers
// =====================
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\ForgotPasswordController;
use App\Http\Controllers\Student\LessonController as StudentLessonController;
use App\Http\Controllers\UserController;

// =====================
// Public (Student-facing)
// =====================

// Trang chủ & điều hướng cơ bản
Route::get('/', [StudentCourseController::class, 'index'])->name('student.courses.index');
Route::get('/home', fn () => redirect()->route('student.courses.index'))->name('home');

// Khóa học & bài học (public)
Route::get('/courses/{slug}',   [StudentCourseController::class, 'show'])->name('student.courses.show');
Route::get('/lessons/{maBH}',   [StudentLessonController::class,  'show'])->name('student.lessons.show');

// Đường dẫn cũ -> điều hướng mới (legacy redirects)
Route::redirect('/student/courses', '/');
Route::get('/student/courses/{slug}', fn ($slug)   => redirect()->route('student.courses.show', $slug));
Route::get('/student/lessons/{maBH}', fn ($maBH)   => redirect()->route('student.lessons.show', $maBH));

// =====================
// Auth (Login/Register/Logout) + OTP Password Reset
// =====================

// Mở modal đăng nhập/đăng ký trên trang chủ (giữ nguyên name & hành vi)
Route::get('/login', function (Request $request) {
    $redir = $request->query('redirect', url()->previous());
    return redirect()->to(route('student.courses.index') . '?open=login&redirect=' . urlencode($redir));
})->name('login');

Route::get('/register', function (Request $request) {
    $redir = $request->query('redirect', url()->previous());
    return redirect()->to(route('student.courses.index') . '?open=register&redirect=' . urlencode($redir));
})->name('register');

// Xử lý form đăng nhập/đăng ký/đăng xuất
Route::post('/login',    [UserController::class, 'login'])->name('users.login');
Route::post('/register', [UserController::class, 'store'])->name('users.store');
Route::post('/logout',   [UserController::class, 'logout'])->name('logout');

// Quên mật khẩu qua OTP
Route::name('password.')->group(function () {
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetCode'])->name('send');    // password.send
    Route::post('/verify-otp',      [ForgotPasswordController::class, 'verifyOtp'])->name('verify');      // password.verify
    Route::post('/reset-password',  [ForgotPasswordController::class, 'resetPassword'])->name('update');  // password.update
});

// =====================
// Admin Area
// =====================

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::get('/users',                 [UserAdminController::class, 'index'])->name('users.index');
        Route::post('/users',                [UserAdminController::class, 'store'])->name('users.store');
        Route::put('/users/{user}',          [UserAdminController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/role',    [UserAdminController::class, 'updateRole'])->name('users.updateRole');
        Route::delete('/users/{user}',       [UserAdminController::class, 'destroy'])->name('users.destroy');

        // Categories
        Route::get('/categories',                [CategoryAdminController::class, 'index'])->name('categories.index');
        Route::post('/categories',               [CategoryAdminController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}',     [CategoryAdminController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}',  [CategoryAdminController::class, 'destroy'])->name('categories.destroy');
    });