<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Controllers (API - Student)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\API\Student\AuthController;
use App\Http\Controllers\API\Student\CartController as StudentCartController;
use App\Http\Controllers\API\Student\CertificateController as ApiStudentCertificateController;
use App\Http\Controllers\API\Student\CheckoutController as StudentCheckoutController;
use App\Http\Controllers\API\Student\ComboController;
use App\Http\Controllers\API\Student\ContactController;
use App\Http\Controllers\API\Student\CourseController;
use App\Http\Controllers\API\Student\CourseReviewController;
use App\Http\Controllers\API\Student\DictionaryController;
use App\Http\Controllers\API\Student\LessonController;
use App\Http\Controllers\API\Student\LessonDiscussionController as StudentLessonDiscussionController;
use App\Http\Controllers\API\Student\LessonProgressController;
use App\Http\Controllers\API\Student\MiniTestController as StudentMiniTestController;
use App\Http\Controllers\API\Student\OrderController;
use App\Http\Controllers\API\Student\ProfileController;
use App\Http\Controllers\API\Student\ProgressController;
use App\Http\Controllers\Student\ForgotPasswordController as StudentForgotPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Student (Public)
    |--------------------------------------------------------------------------
    */
    Route::prefix('student')->group(function () {
        // Authentication & account recovery
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('login/google', [AuthController::class, 'loginWithGoogle']);
        Route::post('password/otp', [StudentForgotPasswordController::class, 'sendResetCode']);
        Route::post('password/otp/verify', [StudentForgotPasswordController::class, 'verifyOtp']);
        Route::post('password/reset', [StudentForgotPasswordController::class, 'resetPassword']);
        Route::post('contact', [ContactController::class, 'store']);

        // Catalog & discovery
        Route::get('combos', [ComboController::class, 'index']);
        Route::get('combos/{combo}', [ComboController::class, 'show']);

        // Public discussions
        Route::get('lessons/{lesson}/discussions', [StudentLessonDiscussionController::class, 'index']);

        /*
        |--------------------------------------------------------------------------
        | Student (Authenticated)
        |--------------------------------------------------------------------------
        */
        Route::middleware('auth:sanctum')->group(function () {
            // Session & profile management
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('profile', [ProfileController::class, 'me']);
            Route::put('profile', [ProfileController::class, 'update']);

            // Courses, reviews & progress
            Route::get('courses/enrolled', [CourseController::class, 'myCourses']);
            Route::post('courses/{course}/reviews', [CourseReviewController::class, 'store']);
            Route::get('orders', [OrderController::class, 'index']);
            Route::get('progress/overview', [ProgressController::class, 'overview']);
            Route::post('dictionary/lookup', [DictionaryController::class, 'lookup']);

            // Cart & checkout
            Route::get('cart', [StudentCartController::class, 'index']);
            Route::post('cart/courses', [StudentCartController::class, 'storeCourse']);
            Route::post('cart/combos', [StudentCartController::class, 'storeCombo']);
            Route::delete('cart/courses/{course}', [StudentCartController::class, 'destroyCourse']);
            Route::delete('cart/combos/{combo}', [StudentCartController::class, 'destroyCombo']);
            Route::post('cart/remove-selected', [StudentCartController::class, 'destroySelected']);
            Route::delete('cart', [StudentCartController::class, 'clear']);
            Route::post('checkout/preview', [StudentCheckoutController::class, 'preview']);
            Route::post('checkout/complete', [StudentCheckoutController::class, 'complete']);

            // Certificates
            Route::get('certificates', [ApiStudentCertificateController::class, 'index']);
            Route::get('certificates/{certificate}/download', [ApiStudentCertificateController::class, 'download']);
            Route::get('certificates/{certificate}', [ApiStudentCertificateController::class, 'show']);

            // Lesson discussions (authenticated)
            Route::post('lessons/{lesson}/discussions', [StudentLessonDiscussionController::class, 'store']);
            Route::post('lessons/{lesson}/discussions/{discussion}/replies', [StudentLessonDiscussionController::class, 'storeReply']);
            Route::delete('lessons/{lesson}/discussions/{discussion}', [StudentLessonDiscussionController::class, 'destroy']);
            Route::delete('lessons/{lesson}/discussions/{discussion}/replies/{reply}', [StudentLessonDiscussionController::class, 'destroyReply']);

            // Mini tests
            Route::get('chapters/{chapter}/minitests', [StudentMiniTestController::class, 'listByChapter']);
            Route::get('minitests/{miniTest}', [StudentMiniTestController::class, 'showMiniTest']);
            Route::post('minitests/{miniTest}/start', [StudentMiniTestController::class, 'startAttempt']);
            Route::get('minitests/attempts/{result}', [StudentMiniTestController::class, 'showAttempt']);
            Route::post('minitests/attempts/{result}/answers/{question}', [StudentMiniTestController::class, 'saveAnswer']);
            Route::post('minitests/attempts/{result}/answers/{question}/upload', [StudentMiniTestController::class, 'uploadSpeakingAnswer']);
            Route::post('minitests/attempts/{result}/submit', [StudentMiniTestController::class, 'submitAttempt']);
            Route::get('minitests/results/{result}', [StudentMiniTestController::class, 'showResult']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Courses (Public catalog)
    |--------------------------------------------------------------------------
    */
    Route::get('courses', [CourseController::class, 'index']);
    Route::get('courses/{course}', [CourseController::class, 'show']);
    Route::get('courses/{course}/reviews', [CourseReviewController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | Lessons (Authenticated access)
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('lessons/{lesson}', [LessonController::class, 'show']);
        Route::put('lessons/{lesson}/progress', [LessonProgressController::class, 'update']);
    });
});
