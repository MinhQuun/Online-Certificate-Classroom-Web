<!doctype html>
<html lang="vi">
    <head>
        <meta charset="utf-8">
        <title>@yield('title','Teacher')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">

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
        {{-- TOPBAR --}}
        <nav class="teacher-topbar navbar navbar-expand-lg">
            <div class="container-fluid">
            <button class="btn btn-outline-light d-lg-none me-2" id="btnSidebar">
                <i class="bi bi-list"></i>
            </button>

            <a class="navbar-brand fw-bold" href="{{ route('teacher.dashboard') }}">
                <i class="bi bi-person-badge me-1"></i> Giảng viên Panel
            </a>

            <div class="ms-auto d-flex align-items-center">
                <div class="dropdown">
                <button class="btn btn-outline-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i>
                    {{ Auth::user()->name ?? 'Tài khoản' }}
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    @can('view-admin')
                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2 me-1"></i> Admin
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
            {{-- SIDEBAR --}}
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

                {{-- BÀI GIẢNG --}}
                <li class="nav-item mt-2 text-muted small">Bài giảng</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('teacher.lectures.*') ? 'active' : '' }}"
                    href="{{ route('teacher.lectures.index') }}">
                    <i class="bi bi-book me-2"></i> Quản lý bài giảng
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('teacher.videos.*') ? 'active' : '' }}"
                    href="{{ route('teacher.videos.index') }}">
                    <i class="bi bi-camera-video me-2"></i> Video bài giảng
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('teacher.documents.*') ? 'active' : '' }}"
                    href="{{ route('teacher.documents.index') }}">
                    <i class="bi bi-file-earmark-text me-2"></i> Tài liệu
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('teacher.assignments.*') ? 'active' : '' }}"
                    href="{{ route('teacher.assignments.index') }}">
                    <i class="bi bi-pencil-square me-2"></i> Bài tập
                    @if(($badges['assignments_pending'] ?? 0) > 0)
                        <span class="badge text-bg-warning ms-2">{{ $badges['assignments_pending'] }}</span>
                    @endif
                    </a>
                </li>

                {{-- HỌC VIÊN --}}
                <li class="nav-item mt-2 text-muted small">Học viên</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('teacher.students.*') ? 'active' : '' }}"
                    href="{{ route('teacher.students.index') }}">
                    <i class="bi bi-people me-2"></i> Quản lý học viên
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('teacher.progress.*') ? 'active' : '' }}"
                    href="{{ route('teacher.progress.index') }}">
                    <i class="bi bi-graph-up me-2"></i> Tiến độ học tập
                    </a>
                </li>

                {{-- KỲ THI --}}
                <li class="nav-item mt-2 text-muted small">Kỳ thi</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('teacher.exams.*') ? 'active' : '' }}"
                    href="{{ route('teacher.exams.index') }}">
                    <i class="bi bi-clipboard-check me-2"></i> Quản lý kỳ thi cuối khóa
                    @if(($badges['exams_pending'] ?? 0) > 0)
                        <span class="badge text-bg-secondary ms-2">{{ $badges['exams_pending'] }}</span>
                    @endif
                    </a>
                </li>

                {{-- THỐNG KÊ --}}
                <li class="nav-item mt-2 text-muted small">Thống kê & Báo cáo</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('teacher.reports.progress') ? 'active' : '' }}"
                    href="{{ route('teacher.reports.progress') }}">
                    <i class="bi bi-bar-chart me-2"></i> Tiến độ học viên
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('teacher.reports.exams') ? 'active' : '' }}"
                    href="{{ route('teacher.reports.exams') }}">
                    <i class="bi bi-file-earmark-bar-graph me-2"></i> Kết quả kỳ thi
                    </a>
                </li>
                </ul>
            </div>
            </aside>

            {{-- MAIN --}}
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
