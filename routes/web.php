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
use App\Http\Controllers\Admin\InvoiceController;

/*
|--------------------------------------------------------------------------
| Controllers (Teacher)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\LectureController;
use App\Http\Controllers\Teacher\ProgressController;
use App\Http\Controllers\Teacher\ChapterController;
use App\Http\Controllers\Teacher\MiniTestController;
use App\Http\Controllers\Teacher\GradingController;
use App\Http\Controllers\Teacher\ResultController;

/*
|--------------------------------------------------------------------------
| Controllers (Student)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\ForgotPasswordController;
use App\Http\Controllers\Student\LessonController as StudentLessonController;
use App\Http\Controllers\Student\LessonProgressController as StudentLessonProgressController;
use App\Http\Controllers\Student\CartController as StudentCartController;
use App\Http\Controllers\Student\CheckoutController;
use App\Http\Controllers\Student\ActivationController;
use App\Http\Controllers\Student\ProgressController as StudentProgressController;
use App\Http\Controllers\Student\MiniTestController as StudentMiniTestController;
use App\Http\Controllers\Student\CourseReviewController as StudentCourseReviewController;

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

// Giỏ hàng & thanh toán
Route::prefix('cart')
    ->name('student.cart.')
    ->group(function () {
        Route::get('/', [StudentCartController::class, 'index'])->name('index');
        Route::post('/', [StudentCartController::class, 'store'])->name('store');
        Route::delete('/{course}', [StudentCartController::class, 'destroy'])->name('destroy');
    });

Route::post('/cart/checkout', [CheckoutController::class, 'start'])->name('student.checkout.start');
Route::get('/checkout', [CheckoutController::class, 'index'])
    ->middleware('auth')
    ->name('student.checkout.index');
Route::post('/checkout/complete', [CheckoutController::class, 'complete'])
    ->middleware('auth')
    ->name('student.checkout.complete');

Route::middleware('auth')
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/activation-codes', [ActivationController::class, 'showForm'])->name('activations.form');
        Route::post('/activation-codes', [ActivationController::class, 'redeem'])->name('activations.redeem');

        Route::get('/progress', [StudentProgressController::class, 'index'])->name('progress.index');
        Route::post('/lessons/{lesson}/progress', [StudentLessonProgressController::class, 'store'])
            ->name('lessons.progress.store');

        // Mini-tests cho học viên
        Route::get('/chapters/{chapter}/minitests', [StudentMiniTestController::class, 'index'])->name('minitests.index');
        Route::get('/minitests/{miniTest}', [StudentMiniTestController::class, 'show'])->name('minitests.show');
        Route::post('/minitests/{miniTest}/submit', [StudentMiniTestController::class, 'submit'])->name('minitests.submit');
        Route::get('/minitests/{result}/result', [StudentMiniTestController::class, 'result'])->name('minitests.result');

        Route::post('/courses/{course:slug}/reviews', [StudentCourseReviewController::class, 'store'])->name('courses.reviews.store');
    });


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

        // Quản lý hóa đơn
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/export', [InvoiceController::class, 'exportExcel'])->name('invoices.export');
        Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'exportPdf'])->name('invoices.pdf');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
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

        Route::get('/chapters', [ChapterController::class, 'index'])->name('chapters.index');
        Route::post('/chapters', [ChapterController::class, 'store'])->name('chapters.store');
        Route::put('/chapters/{chapter}', [ChapterController::class, 'update'])->name('chapters.update');
        Route::delete('/chapters/{chapter}', [ChapterController::class, 'destroy'])->name('chapters.destroy');

        Route::get('/lectures', [LectureController::class, 'index'])->name('lectures.index');
        Route::post('/lectures', [LectureController::class, 'store'])->name('lectures.store');
        Route::patch('/lectures/{lesson}', [LectureController::class, 'update'])->name('lectures.update');
        Route::delete('/lectures/{lesson}', [LectureController::class, 'destroy'])->name('lectures.destroy');
        Route::post('/lectures/{lesson}/materials', [LectureController::class, 'storeMaterial'])->name('lectures.materials.store');
        Route::delete('/materials/{material}', [LectureController::class, 'destroyMaterial'])->name('lectures.materials.destroy');

        Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');
        Route::patch('/progress/{course}/{student}', [ProgressController::class, 'update'])->name('progress.update');

        Route::get('/minitests', [MiniTestController::class, 'index'])->name('minitests.index');
        Route::post('/minitests', [MiniTestController::class, 'store'])->name('minitests.store');
        Route::patch('/minitests/{miniTest}', [MiniTestController::class, 'update'])->name('minitests.update');
        Route::delete('/minitests/{miniTest}', [MiniTestController::class, 'destroy'])->name('minitests.destroy');
        Route::get('/minitests/{miniTest}/questions', [MiniTestController::class, 'showQuestionForm'])->name('minitests.questions.form');
        Route::post('/minitests/{miniTest}/questions', [MiniTestController::class, 'storeQuestions'])->name('minitests.questions.store');
        Route::post('/minitests/{miniTest}/materials', [MiniTestController::class, 'storeMaterial'])->name('minitests.materials.store');
        Route::delete('/minitests/materials/{material}', [MiniTestController::class, 'destroyMaterial'])->name('minitests.materials.destroy');
        Route::post('/minitests/{miniTest}/publish', [MiniTestController::class, 'publish'])->name('minitests.publish');
        Route::post('/minitests/{miniTest}/unpublish', [MiniTestController::class, 'unpublish'])->name('minitests.unpublish');

        // Chấm điểm
        Route::get('/grading', [GradingController::class, 'index'])->name('grading.index');
        Route::get('/grading/{result}', [GradingController::class, 'show'])->name('grading.show');
        Route::post('/grading/{result}', [GradingController::class, 'grade'])->name('grading.grade');
        Route::post('/grading/bulk', [GradingController::class, 'bulkGrade'])->name('grading.bulk');

        // Xem điểm học viên
        Route::get('/results', [ResultController::class, 'index'])->name('results.index');
        Route::get('/results/{result}', [ResultController::class, 'show'])->name('results.show');
    });
