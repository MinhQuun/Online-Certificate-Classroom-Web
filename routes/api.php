<?php

use App\Http\Controllers\API\Student\AuthController;
use App\Http\Controllers\API\Student\CourseController;
use App\Http\Controllers\API\Student\LessonController;
use App\Http\Controllers\API\Student\LessonProgressController;
use App\Http\Controllers\API\Student\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('student')->group(function () {
        // Đăng nhập API bằng Sanctum token
        Route::post('login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);

            Route::get('profile', [ProfileController::class, 'me']);
            Route::put('profile', [ProfileController::class, 'update']);

            Route::get('courses/enrolled', [CourseController::class, 'myCourses']);
        });
    });

    // Khóa học công khai cho mobile xem trước
    Route::get('courses', [CourseController::class, 'index']);
    Route::get('courses/{course}', [CourseController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('lessons/{lesson}', [LessonController::class, 'show']);
        Route::put('lessons/{lesson}/progress', [LessonProgressController::class, 'update']);
    });
});
