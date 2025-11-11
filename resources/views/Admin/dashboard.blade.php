@extends('layouts.admin')
@section('title','Bảng điều khiển')

@section('content')
@php
    $filters = $roleFilters ?? ['admin' => 'admin', 'teacher' => 'teacher', 'student' => 'student'];
@endphp
<section class="page-header">
    <span class="kicker">Quản trị hệ thống</span>
    <h1 class="title">Bảng điều khiển</h1>
    <p class="muted">Tổng quan nhanh và lối tắt tới các khu vực chính.</p>
</section>

<section class="stats-grid mb-4">
    {{-- Tổng người dùng --}}
    <a class="stats-card stats-card--link" href="{{ route('admin.users.index') }}">
        <div class="stats-icon"><i class="bi bi-people"></i></div>
        <div class="stats-card__body">
            <span class="stats-label">Tổng người dùng</span>
            <span class="stats-value">{{ number_format($counts['total'] ?? 0) }}</span>
        </div>
    </a>

    {{-- Admin --}}
    <a class="stats-card stats-card--link" href="{{ route('admin.users.index', ['role' => $filters['admin'] ?? 'admin']) }}">
        <div class="stats-icon"><i class="bi bi-shield-lock"></i></div>
        <div class="stats-card__body">
            <span class="stats-label">Tài khoản Admin</span>
            <span class="stats-value">{{ number_format($counts['admin'] ?? 0) }}</span>
        </div>
    </a>

    {{-- Giảng viên --}}
    <a class="stats-card stats-card--link" href="{{ route('admin.users.index', ['role' => $filters['teacher'] ?? 'teacher']) }}">
        <div class="stats-icon"><i class="bi bi-person-badge"></i></div>
        <div class="stats-card__body">
            <span class="stats-label">Giảng viên</span>
            <span class="stats-value">{{ number_format($counts['teacher'] ?? 0) }}</span>
        </div>
    </a>

    {{-- Học viên --}}
    <a class="stats-card stats-card--link" href="{{ route('admin.users.index', ['role' => $filters['student'] ?? 'student']) }}">
        <div class="stats-icon"><i class="bi bi-person"></i></div>
        <div class="stats-card__body">
            <span class="stats-label">Học viên</span>
            <span class="stats-value">{{ number_format($counts['student'] ?? 0) }}</span>
        </div>
    </a>
</section>

<div class="card quick-links">
    <div class="card-body">
        <h5 class="mb-3">Tác vụ nhanh</h5>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.users.index') }}" class="chip"><i class="bi bi-person-plus me-1"></i>Thêm người dùng</a>
            <a href="{{ route('admin.users.index') }}" class="chip"><i class="bi bi-shield-check me-1"></i>Phân quyền</a>
            <a href="{{ route('admin.courses.index') }}" class="chip"><i class="bi bi-journal-bookmark-fill me-1"></i>Quản lý khóa học</a>
            <a href="{{ route('admin.promotions.index') }}" class="chip"><i class="bi bi-tag-fill me-1"></i>Quản lý khuyến mãi</a>
        </div>
    </div>
</div>
@endsection
