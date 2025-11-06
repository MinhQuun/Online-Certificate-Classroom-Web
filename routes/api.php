<?php

use App\Http\Controllers\API\Student\ActivationController;
use App\Http\Controllers\API\Student\AuthController;
use App\Http\Controllers\API\Student\ComboController;
use App\Http\Controllers\API\Student\ContactController;
use App\Http\Controllers\API\Student\CourseController;
use App\Http\Controllers\API\Student\CourseReviewController;
use App\Http\Controllers\API\Student\LessonController;
use App\Http\Controllers\API\Student\LessonProgressController;
use App\Http\Controllers\API\Student\OrderController;
use App\Http\Controllers\API\Student\ProfileController;
use App\Http\Controllers\API\Student\ProgressController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('student')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('login/google', [AuthController::class, 'loginWithGoogle']);
        Route::post('contact', [ContactController::class, 'store']);
        Route::get('combos', [ComboController::class, 'index']);
        Route::get('combos/{combo}', [ComboController::class, 'show']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('activations', [ActivationController::class, 'store']);

            Route::get('profile', [ProfileController::class, 'me']);
            Route::put('profile', [ProfileController::class, 'update']);

            Route::get('courses/enrolled', [CourseController::class, 'myCourses']);
            Route::post('courses/{course}/reviews', [CourseReviewController::class, 'store']);
            Route::get('orders', [OrderController::class, 'index']);
            Route::get('progress/overview', [ProgressController::class, 'overview']);
        });
    });

    Route::get('courses', [CourseController::class, 'index']);
    Route::get('courses/{course}', [CourseController::class, 'show']);
    Route::get('courses/{course}/reviews', [CourseReviewController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('lessons/{lesson}', [LessonController::class, 'show']);
        Route::put('lessons/{lesson}/progress', [LessonProgressController::class, 'update']);
    });
});
