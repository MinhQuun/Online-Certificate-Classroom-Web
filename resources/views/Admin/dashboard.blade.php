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

<div class="row g-3 mb-4 stat-row">
    {{-- Tổng người dùng --}}
    <div class="col-md-6 col-lg-3">
        <a class="stat-card" href="{{ route('admin.users.index') }}">
        <div class="icon"><i class="bi bi-people"></i></div>
        <div>
            <div class="n">{{ $counts['total'] ?? 0 }}</div>
            <div class="t">Người dùng</div>
        </div>
        </a>
    </div>

    {{-- Admin (Giáo vụ) --}}
    <div class="col-md-6 col-lg-3">
        <a class="stat-card" href="{{ route('admin.users.index', ['role' => $filters['admin'] ?? 'admin']) }}">
        <div class="icon"><i class="bi bi-shield-lock"></i></div>
        <div>
            <div class="n">{{ $counts['admin'] ?? 0 }}</div>
            <div class="t">Admin </div>
        </div>
        </a>
    </div>

    {{-- Giảng viên --}}
    <div class="col-md-6 col-lg-3">
        <a class="stat-card" href="{{ route('admin.users.index', ['role' => $filters['teacher'] ?? 'teacher']) }}">
        <div class="icon"><i class="bi bi-person-badge"></i></div>
        <div>
            <div class="n">{{ $counts['teacher'] ?? 0 }}</div>
            <div class="t">Giảng viên</div>
        </div>
        </a>
    </div>

    {{-- Học viên --}}
    <div class="col-md-6 col-lg-3">
        <a class="stat-card" href="{{ route('admin.users.index', ['role' => $filters['student'] ?? 'student']) }}">
        <div class="icon"><i class="bi bi-person"></i></div>
        <div>
            <div class="n">{{ $counts['student'] ?? 0 }}</div>
            <div class="t">Học viên</div>
        </div>
        </a>
    </div>
</div>

<div class="card quick-links">
    <div class="card-body">
        <h5 class="mb-3">Tác vụ nhanh</h5>
        <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.users.index') }}" class="chip"><i class="bi bi-person-plus me-1"></i>Thêm người dùng</a>
        <a href="{{ route('admin.users.index') }}" class="chip"><i class="bi bi-shield-check me-1"></i>Phân quyền</a>
        <a href="{{ route('home') }}" class="chip"><i class="bi bi-window-sidebar me-1"></i>Trang khách</a>
        </div>
    </div>
</div>
@endsection

