<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('student.courses.index');
});

// Student-facing routes
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/courses', [\App\Http\Controllers\Student\CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{slug}', [\App\Http\Controllers\Student\CourseController::class, 'show'])->name('courses.show');
    Route::get('/lessons/{maBH}', [\App\Http\Controllers\Student\LessonController::class, 'show'])->name('lessons.show');
});
