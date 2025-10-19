<?php

use Illuminate\Support\Facades\Route;

// Student-facing public pages
Route::get('/', [\App\Http\Controllers\Student\CourseController::class, 'index'])->name('student.courses.index');
Route::get('/courses/{slug}', [\App\Http\Controllers\Student\CourseController::class, 'show'])->name('student.courses.show');
Route::get('/lessons/{maBH}', [\App\Http\Controllers\Student\LessonController::class, 'show'])->name('student.lessons.show');

// Legacy student paths redirect to new URLs
Route::redirect('/student/courses', '/');
Route::get('/student/courses/{slug}', fn($slug) => redirect()->route('student.courses.show', $slug));
Route::get('/student/lessons/{maBH}', fn($maBH) => redirect()->route('student.lessons.show', $maBH));
