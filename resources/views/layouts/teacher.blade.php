<!doctype html>
<html lang="vi">
    <head>
        <meta charset="utf-8">
        <title>@yield('title','Teacher')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- SweetAlert2 + Bootstrap --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

        <script src="https://kit.fontawesome.com/cdbcf8b89b.js" crossorigin="anonymous"></script>

        {{-- CSS riêng cho giảng viên --}}
        <link rel="stylesheet" href="{{ asset('css/Teacher/teacher.css') }}">
        @stack('styles')
    </head>
    <body class="teacher-body">
        {{-- THANH TRÊN (TOPBAR) --}}
        <nav class="teacher-topbar navbar navbar-expand-lg">
        <div class="container-fluid">
            <button class="btn btn-outline-light d-lg-none me-2" id="btnSidebar" aria-label="Mở/đóng menu">
            <i class="bi bi-list"></i>
            </button>

            <a class="navbar-brand fw-bold" href="{{ route('teacher.dashboard') }}">
            <i class="bi bi-person-badge me-1"></i> Bảng điều khiển giảng viên
            </a>

            <div class="ms-auto d-flex align-items-center">
            <div class="dropdown">
                <button class="btn btn-outline-light btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle me-1"></i>
                {{ Auth::user()->name ?? 'Tài khoản' }}
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                @can('view-admin')
                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2 me-1"></i> Quản trị (Admin)
                    </a>
                    <div class="dropdown-divider"></div>
                @endcan
                <form action="{{ route('logout') }}" method="post" class="px-3 py-1">
                    @csrf
                    <button class="btn btn-danger w-100">
                    <i class="bi bi-box-arrow-right me-1"></i> Đăng xuất
                    </button>
                </form>
                </div>
            </div>
            </div>
        </div>
        </nav>

        <div class="teacher-wrapper">
        {{-- THANH BÊN (SIDEBAR) --}}
        <aside id="teacherSidebar" class="teacher-sidebar">
            <div class="px-3 py-3">
            <div class="text-muted small mb-2">Điều hướng</div>
            <ul class="nav flex-column gap-1">
                {{-- TỔNG QUAN --}}
                <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}"
                    href="{{ route('teacher.dashboard') }}">
                    <i class="bi bi-grid me-2"></i> Tổng quan
                </a>
                </li>

                {{-- NỘI DUNG & LỚP HỌC --}}
                <li class="nav-item mt-2 text-muted small">Nội dung &amp; lớp học</li>
                <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('teacher.chapters.*') ? 'active' : '' }}"
                    href="{{ route('teacher.chapters.index') }}">
                    <i class="bi bi-book me-2"></i> Quản lý chương
                    @if(($badges['assignments_pending'] ?? 0) > 0)
                    <span class="badge text-bg-warning ms-2">{{ $badges['assignments_pending'] }}</span>
                    @endif
                </a>
                </li>
                <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('teacher.lectures.*') ? 'active' : '' }}"
                    href="{{ route('teacher.lectures.index') }}">
                    <i class="bi bi-book me-2"></i> Quản lý bài giảng
                    @if(($badges['assignments_pending'] ?? 0) > 0)
                    <span class="badge text-bg-warning ms-2">{{ $badges['assignments_pending'] }}</span>
                    @endif
                </a>
                </li>
                <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('teacher.progress.*') ? 'active' : '' }}"
                    href="{{ route('teacher.progress.index') }}">
                    <i class="bi bi-graph-up me-2"></i> Tiến độ học tập
                    @if(($badges['low_progress'] ?? 0) > 0)
                    <span class="badge text-bg-danger ms-2">{{ $badges['low_progress'] }}</span>
                    @endif
                </a>
                </li>

                {{-- ĐÁNH GIÁ CUỐI KHÓA --}}
                <li class="nav-item mt-2 text-muted small">Đánh giá cuối khóa</li>
                <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('teacher.exams.*') ? 'active' : '' }}"
                    href="{{ route('teacher.exams.index') }}">
                    <i class="bi bi-clipboard-check me-2"></i> Kỳ thi cuối khóa
                    @if(($badges['exams_pending'] ?? 0) > 0)
                    <span class="badge text-bg-secondary ms-2">{{ $badges['exams_pending'] }}</span>
                    @endif
                </a>
                </li>
            </ul>
            </div>
        </aside>

        {{-- KHU VỰC CHÍNH --}}
        <main class="teacher-main">
            {{-- Flash cho SweetAlert2 --}}
            <div id="flash-data"
                data-success="{{ session('success') }}"
                data-error="{{ session('error') }}"></div>

            @yield('content')
        </main>
        </div>

        <div id="sidebarOverlay" aria-hidden="true"></div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        document.getElementById('btnSidebar')?.addEventListener('click', () => {
            document.getElementById('teacherSidebar')?.classList.toggle('open');
        });
        </script>
        <script src="{{ asset('js/Teacher/teacher.js') }}"></script>
        @stack('scripts')
    </body>
</html>
