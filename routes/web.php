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
use App\Http\Controllers\Admin\CourseAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CertificateAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ContactReplyController;
use App\Http\Controllers\Admin\ComboAdminController;
use App\Http\Controllers\Admin\PromotionAdminController;

/*
|--------------------------------------------------------------------------
| Controllers (Teacher)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\LectureController;
use App\Http\Controllers\Teacher\ProgressController;
use App\Http\Controllers\Teacher\ChapterController;
use App\Http\Controllers\Teacher\MiniTestController as TeacherMiniTestController;
use App\Http\Controllers\Teacher\GradingController;
use App\Http\Controllers\Teacher\ResultController;
use App\Http\Controllers\Teacher\LessonDiscussionController as TeacherLessonDiscussionController;
use App\Http\Controllers\Teacher\CertificateController as TeacherCertificateController;

/*
|--------------------------------------------------------------------------
| Controllers (Student)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\ComboController as StudentComboController;
use App\Http\Controllers\Student\ForgotPasswordController;
use App\Http\Controllers\Student\LessonController as StudentLessonController;
use App\Http\Controllers\Student\LessonProgressController as StudentLessonProgressController;
use App\Http\Controllers\Student\CartController as StudentCartController;
use App\Http\Controllers\Student\CheckoutController;
use App\Http\Controllers\Student\ProgressController as StudentProgressController;
use App\Http\Controllers\Student\MiniTestController as StudentMiniTestController;
use App\Http\Controllers\Student\CourseReviewController as StudentCourseReviewController;
use App\Http\Controllers\Student\OrderHistoryController;
use App\Http\Controllers\Student\LessonDiscussionController as StudentLessonDiscussionController;
use App\Http\Controllers\Student\ContactController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Student\MyCoursesController;
use App\Http\Controllers\Student\GoogleAuthController;
use App\Http\Controllers\Student\CertificateController as StudentCertificateController;
use App\Http\Controllers\Student\NotificationController;
use App\Http\Controllers\Payment\VNPayController;

/*
|--------------------------------------------------------------------------
| =====================
| Public (trang công khai cho học viên)
| =====================
|--------------------------------------------------------------------------
*/

// Trang chủ & điều hướng cơ bản
Route::get('/', [StudentCourseController::class, 'index'])->name('student.courses.index');
Route::get('/home', fn () => redirect()->route('student.courses.index'))->name('home');

// Khóa học & bài học (public)
Route::get('/courses/{slug}', [StudentCourseController::class, 'show'])->name('student.courses.show');
Route::get('/combos', [StudentComboController::class, 'index'])->name('student.combos.index');
Route::get('/combos/{slug}', [StudentComboController::class, 'show'])->name('student.combos.show');
Route::get('/lessons/{maBH}', [StudentLessonController::class, 'show'])->name('student.lessons.show');

// Xem danh sách thảo luận bài học (public, không yêu cầu đăng nhập)
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/lessons/{lesson}/discussions', [StudentLessonDiscussionController::class, 'index'])
        ->name('lessons.discussions.index');
});

// Điều hướng cũ → mới (legacy redirects)
Route::redirect('/student/courses', '/');
Route::get('/student/courses/{slug}', fn ($slug) => redirect()->route('student.courses.show', $slug));
Route::get('/student/lessons/{maBH}', fn ($maBH) => redirect()->route('student.lessons.show', $maBH));

// Giỏ hàng & thanh toán
Route::prefix('cart')->name('student.cart.')->group(function () {
    Route::get('/', [StudentCartController::class, 'index'])->name('index');
    Route::post('/', [StudentCartController::class, 'store'])->name('store');
    Route::post('/combos', [StudentCartController::class, 'storeCombo'])->name('store-combo');
    Route::delete('/items', [StudentCartController::class, 'destroySelected'])->name('destroy-selected');
    Route::delete('/', [StudentCartController::class, 'destroyAll'])->name('destroy-all');
    Route::delete('/{course}', [StudentCartController::class, 'destroy'])->name('destroy');
    Route::delete('/combos/{combo}', [StudentCartController::class, 'destroyCombo'])->name('destroy-combo');
});
Route::post('/cart/checkout', [CheckoutController::class, 'start'])->name('student.checkout.start');
Route::get('/checkout', [CheckoutController::class, 'index'])->middleware('auth')->name('student.checkout.index');
Route::post('/checkout/complete', [CheckoutController::class, 'complete'])->middleware('auth')->name('student.checkout.complete');

Route::prefix('payment/vnpay')->name('payment.vnpay.')->group(function () {
    Route::get('/return', [VNPayController::class, 'return'])->name('return');
    Route::post('/ipn', [VNPayController::class, 'ipn'])->name('ipn');
});

// Trang dịch vụ / giới thiệu / liên hệ
Route::get('/services', fn () => view('Student.services'))->name('student.services');
Route::get('/about-us', fn () => view('Student.about-us'))->name('student.about');
Route::get('/contact', fn () => view('Student.contact'))->name('student.contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

/*
|--------------------------------------------------------------------------
| =====================
| Auth & Tài khoản (đăng nhập/đăng ký/đổi mật khẩu qua OTP)
| =====================
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
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('student.auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('student.auth.google.callback');
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
| =====================
| Student (đã đăng nhập)
| =====================
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('student')->name('student.')->group(function () {
    // Thông báo cho học viên
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Tiến độ học tập & tiến độ bài học
    Route::get('/progress', [StudentProgressController::class, 'index'])->name('progress.index');
    Route::post('/lessons/{lesson}/progress', [StudentLessonProgressController::class, 'store'])
        ->name('lessons.progress.store');

    // Thảo luận bài học (hành động cần đăng nhập)
    Route::post('/lessons/{lesson}/discussions', [StudentLessonDiscussionController::class, 'store'])
        ->name('lessons.discussions.store');
    Route::post('/lessons/{lesson}/discussions/{discussion}/replies', [StudentLessonDiscussionController::class, 'storeReply'])
        ->name('lessons.discussions.replies.store');
    Route::delete('/lessons/{lesson}/discussions/{discussion}', [StudentLessonDiscussionController::class, 'destroy'])
        ->name('lessons.discussions.destroy');
    Route::delete('/lessons/{lesson}/discussions/{discussion}/replies/{reply}', [StudentLessonDiscussionController::class, 'destroyReply'])
        ->name('lessons.discussions.replies.destroy');

    // Mini-tests cho học viên
    Route::get('/chapters/{chapter}/minitests', [StudentMiniTestController::class, 'index'])->name('minitests.index');
    Route::get('/minitests/{miniTest}', [StudentMiniTestController::class, 'show'])->name('minitests.show'); // (giữ 1 route, bỏ lặp)
    Route::post('/minitests/{miniTest}/start', [StudentMiniTestController::class, 'start'])->name('minitests.start');
    Route::get('/minitests/attempts/{result}', [StudentMiniTestController::class, 'attempt'])->name('minitests.attempt');
    Route::post('/minitests/attempts/{result}/answers/{question}', [StudentMiniTestController::class, 'saveAnswer'])->name('minitests.answers.save');
    Route::post('/minitests/attempts/{result}/answers/{question}/upload', [StudentMiniTestController::class, 'uploadSpeakingAnswer'])->name('minitests.answers.upload');
    Route::post('/minitests/attempts/{result}/submit', [StudentMiniTestController::class, 'submit'])->name('minitests.submit');
    Route::get('/minitests/results/{result}', [StudentMiniTestController::class, 'result'])->name('minitests.result');

    // Đánh giá khóa học
    Route::post('/courses/{course:slug}/reviews', [StudentCourseReviewController::class, 'store'])->name('courses.reviews.store');

    // Hồ sơ cá nhân
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.changePassword');

    // Khóa học của tôi & Lịch sử đơn hàng
    Route::get('/my-courses', [MyCoursesController::class, 'index'])->name('my-courses');
    Route::get('/order-history', [OrderHistoryController::class, 'index'])->name('order-history');
    Route::get('/certificates', [StudentCertificateController::class, 'index'])->name('certificates.index');
    Route::get('/certificates/{certificate}/download', [StudentCertificateController::class, 'download'])->name('certificates.download');
    Route::get('/certificates/{certificate}', [StudentCertificateController::class, 'show'])->name('certificates.show');

    // Dictionary lookup
    Route::post('/dictionary/lookup', [App\Http\Controllers\Student\DictionaryController::class, 'lookup'])->name('dictionary.lookup');
});

/*
|--------------------------------------------------------------------------
| =====================
| ADMIN (role: admin)
| =====================
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Quản lý người dùng
    Route::get('/users',               [UserAdminController::class, 'index'])->name('users.index');
    Route::post('/users',              [UserAdminController::class, 'store'])->name('users.store');
    Route::put('/users/{user}',        [UserAdminController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/role',  [UserAdminController::class, 'updateRole'])->name('users.updateRole');
    Route::delete('/users/{user}',     [UserAdminController::class, 'destroy'])->name('users.destroy');

    // Quản lý danh mục
    Route::get('/categories',               [CategoryAdminController::class, 'index'])->name('categories.index');
    Route::post('/categories',              [CategoryAdminController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}',    [CategoryAdminController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryAdminController::class, 'destroy'])->name('categories.destroy');

    // Quản lý khóa học
    Route::get('/courses',               [CourseAdminController::class, 'index'])->name('courses.index');
    Route::post('/courses',              [CourseAdminController::class, 'store'])->name('courses.store');
    Route::put('/courses/{course}',      [CourseAdminController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}',   [CourseAdminController::class, 'destroy'])->name('courses.destroy');

    // Quản lý combo khóa học
    Route::get('/combos',               [ComboAdminController::class, 'index'])->name('combos.index');
    Route::post('/combos',              [ComboAdminController::class, 'store'])->name('combos.store');
    Route::put('/combos/{combo}',       [ComboAdminController::class, 'update'])->name('combos.update');
    Route::delete('/combos/{combo}',    [ComboAdminController::class, 'destroy'])->name('combos.destroy');

    // Quản lý chứng chỉ & mẫu
    Route::get('/certificates', [CertificateAdminController::class, 'index'])->name('certificates.index');
    Route::post('/certificates/manual', [CertificateAdminController::class, 'storeManual'])->name('certificates.manual');
    Route::post('/certificates/{certificate}/revoke', [CertificateAdminController::class, 'revoke'])->name('certificates.revoke');
    Route::put('/certificates/policy/courses/{course}', [CertificateAdminController::class, 'updateCoursePolicy'])->name('certificates.courses.policy');
    Route::post('/certificates/templates', [CertificateAdminController::class, 'storeTemplate'])->name('certificates.templates.store');
    Route::put('/certificates/templates/{template}', [CertificateAdminController::class, 'updateTemplate'])->name('certificates.templates.update');
    Route::get('/certificates/search/students', [CertificateAdminController::class, 'searchStudents'])->name('certificates.search.students');
    Route::get('/certificates/search/courses', [CertificateAdminController::class, 'searchCourses'])->name('certificates.search.courses');

    // Quản lý khuyến mãi
    Route::get('/promotions',             [PromotionAdminController::class, 'index'])->name('promotions.index');
    Route::post('/promotions',            [PromotionAdminController::class, 'store'])->name('promotions.store');
    Route::put('/promotions/{promotion}', [PromotionAdminController::class, 'update'])->name('promotions.update');
    Route::delete('/promotions/{promotion}', [PromotionAdminController::class, 'destroy'])->name('promotions.destroy');

    // Quản lý hoá đơn
    Route::get('/invoices',                 [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/export',          [InvoiceController::class, 'exportExcel'])->name('invoices.export');
    Route::get('/invoices/{invoice}/pdf',   [InvoiceController::class, 'exportPdf'])->name('invoices.pdf');
    Route::get('/invoices/{invoice}',       [InvoiceController::class, 'show'])->name('invoices.show');

    // Quản lý liên hệ & phản hồi
    Route::get('/contact-replies',          [ContactReplyController::class, 'index'])->name('contact-replies.index');
    Route::put('/contact-replies/{id}',     [ContactReplyController::class, 'update'])->name('contact-replies.update');
    Route::delete('/contact-replies/{id}',  [ContactReplyController::class, 'destroy'])->name('contact-replies.destroy');
});

/*
|--------------------------------------------------------------------------
| =====================
| TEACHER (role: teacher)
| =====================
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    // Dashboard
    Route::get('/', [TeacherDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', fn () => redirect()->route('teacher.dashboard'));

    // Quản lý thảo luận bài học
    Route::patch('/discussions/{discussion}/pin',   [TeacherLessonDiscussionController::class, 'togglePin'])->name('discussions.pin');
    Route::patch('/discussions/{discussion}/lock',  [TeacherLessonDiscussionController::class, 'toggleLock'])->name('discussions.lock');
    Route::patch('/discussions/{discussion}/status',[TeacherLessonDiscussionController::class, 'updateStatus'])->name('discussions.status');

    // Chương & bài giảng
    Route::get('/chapters',             [ChapterController::class, 'index'])->name('chapters.index');
    Route::post('/chapters',            [ChapterController::class, 'store'])->name('chapters.store');
    Route::put('/chapters/{chapter}',   [ChapterController::class, 'update'])->name('chapters.update');
    Route::delete('/chapters/{chapter}',[ChapterController::class, 'destroy'])->name('chapters.destroy');

    Route::get('/lectures',             [LectureController::class, 'index'])->name('lectures.index');
    Route::post('/lectures',            [LectureController::class, 'store'])->name('lectures.store');
    Route::patch('/lectures/{lesson}',  [LectureController::class, 'update'])->name('lectures.update');
    Route::delete('/lectures/{lesson}', [LectureController::class, 'destroy'])->name('lectures.destroy');
    Route::post('/lectures/{lesson}/materials', [LectureController::class, 'storeMaterial'])->name('lectures.materials.store');
    Route::delete('/materials/{material}',      [LectureController::class, 'destroyMaterial'])->name('lectures.materials.destroy');

    // Theo dõi tiến độ (read-only)
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');
    Route::get('/progress/{course}', [ProgressController::class, 'show'])->name('progress.show');
    Route::get('/certificates', [TeacherCertificateController::class, 'index'])->name('certificates.index');

    // Mini-tests (giáo viên)
    Route::get('/minitests',                         [TeacherMiniTestController::class, 'index'])->name('minitests.index');
    Route::post('/minitests',                        [TeacherMiniTestController::class, 'store'])->name('minitests.store');

    Route::post('/minitests/{miniTest}/questions',   [TeacherMiniTestController::class, 'storeQuestions'])->name('minitests.questions.store');

    Route::patch('/minitests/{miniTest}',            [TeacherMiniTestController::class, 'update'])->name('minitests.update');
    Route::delete('/minitests/{miniTest}',           [TeacherMiniTestController::class, 'destroy'])->name('minitests.destroy');
    Route::get('/minitests/{miniTest}/questions',    [TeacherMiniTestController::class, 'showQuestionForm'])->name('minitests.questions.form');

    Route::post('/minitests/{miniTest}/materials',   [TeacherMiniTestController::class, 'storeMaterial'])->name('minitests.materials.store');
    Route::delete('/minitests/materials/{material}', [TeacherMiniTestController::class, 'destroyMaterial'])->name('minitests.materials.destroy');
    Route::post('/minitests/{miniTest}/publish',     [TeacherMiniTestController::class, 'publish'])->name('minitests.publish');
    Route::post('/minitests/{miniTest}/unpublish',   [TeacherMiniTestController::class, 'unpublish'])->name('minitests.unpublish');

    // Chấm điểm & xem điểm
    Route::get('/grading', fn () => redirect()->route('teacher.grading.writing.index'))->name('grading.index');

    Route::prefix('grading')->name('grading.')->group(function () {
        Route::get('/writing', [GradingController::class, 'writingIndex'])->name('writing.index');
        Route::get('/writing/{result}', [GradingController::class, 'writingShow'])->name('writing.show');
        Route::post('/writing/{result}', [GradingController::class, 'writingGrade'])->name('writing.grade');
        Route::post('/writing/bulk', [GradingController::class, 'writingBulkGrade'])->name('writing.bulk');

        Route::get('/speaking', [GradingController::class, 'speakingIndex'])->name('speaking.index');
        Route::get('/speaking/{result}', [GradingController::class, 'speakingShow'])->name('speaking.show');
        Route::post('/speaking/{result}', [GradingController::class, 'speakingGrade'])->name('speaking.grade');
        Route::post('/speaking/bulk', [GradingController::class, 'speakingBulkGrade'])->name('speaking.bulk');
    });

    Route::get('/results',            [ResultController::class, 'index'])->name('results.index');
    Route::get('/results/{result}',   [ResultController::class, 'show'])->name('results.show');
});
