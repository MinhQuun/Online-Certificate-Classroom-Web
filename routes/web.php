<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Controllers (Common)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Controllers (Admin)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\CourseAdminController as CourseAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserAdminController;

/*
|--------------------------------------------------------------------------
| Controllers (Teacher)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\LectureController;
use App\Http\Controllers\Teacher\ExamController;
use App\Http\Controllers\Teacher\ProgressController;

/*
|--------------------------------------------------------------------------
| Controllers (Student)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\ForgotPasswordController;
use App\Http\Controllers\Student\LessonController as StudentLessonController;

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


/*
|--------------------------------------------------------------------------
| AUTH & USER (đăng nhập/đăng ký/đổi mật khẩu qua OTP)
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| ADMIN (role: admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Quản lý người dùng
        Route::get('/users',                 [UserAdminController::class, 'index'])->name('users.index');
        Route::post('/users',                [UserAdminController::class, 'store'])->name('users.store');
        Route::put('/users/{user}',          [UserAdminController::class, 'update'])->name('users.update'); // Đảm bảo route PUT
        Route::post('/users/{user}/role',    [UserAdminController::class, 'updateRole'])->name('users.updateRole');
        Route::delete('/users/{user}',       [UserAdminController::class, 'destroy'])->name('users.destroy');

        // Quản lý danh mục
        Route::get('/categories',                [CategoryAdminController::class, 'index'])->name('categories.index');
        Route::post('/categories',               [CategoryAdminController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}',     [CategoryAdminController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}',  [CategoryAdminController::class, 'destroy'])->name('categories.destroy');

        // Quản lý khóa học
        Route::get('/courses', [CourseAdminController::class, 'index'])->name('courses.index');
        Route::post('/courses', [CourseAdminController::class, 'store'])->name('courses.store');
        Route::put('/courses/{course}', [CourseAdminController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{course}', [CourseAdminController::class, 'destroy'])->name('courses.destroy');
    });

/*
|--------------------------------------------------------------------------
| TEACHER (role: teacher)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {
        Route::get('/', [TeacherDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', fn () => redirect()->route('teacher.dashboard'));

        Route::get('/lectures', [LectureController::class, 'index'])->name('lectures.index');
        Route::post('/lectures', [LectureController::class, 'store'])->name('lectures.store');
        Route::patch('/lectures/{lesson}', [LectureController::class, 'update'])->name('lectures.update');
        Route::delete('/lectures/{lesson}', [LectureController::class, 'destroy'])->name('lectures.destroy');
        Route::post('/lectures/{lesson}/materials', [LectureController::class, 'storeMaterial'])->name('lectures.materials.store');
        Route::delete('/materials/{material}', [LectureController::class, 'destroyMaterial'])->name('lectures.materials.destroy');

        Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');
        Route::patch('/progress/{course}/{student}', [ProgressController::class, 'update'])->name('progress.update');

        Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');
        Route::post('/exams', [ExamController::class, 'store'])->name('exams.store');
        Route::patch('/exams/{exam}', [ExamController::class, 'update'])->name('exams.update');
        Route::delete('/exams/{exam}', [ExamController::class, 'destroy'])->name('exams.destroy');
        Route::post('/exams/{exam}/materials', [ExamController::class, 'storeMaterial'])->name('exams.materials.store');
        Route::delete('/exams/materials/{material}', [ExamController::class, 'destroyMaterial'])->name('exams.materials.destroy');
    });
