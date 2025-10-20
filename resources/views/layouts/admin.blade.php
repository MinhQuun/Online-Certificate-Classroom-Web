<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>@yield('title','Admin')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- SweetAlert2 --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  {{-- Bootstrap --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  {{-- CSS riêng --}}
  <link rel="stylesheet" href="{{ asset('css/Admin/admin.css') }}">
  @stack('styles')
</head>
<body class="admin-body">

  {{-- TOPBAR --}}
  <nav class="admin-topbar navbar navbar-expand-lg">
    <div class="container-fluid">
      <button class="btn btn-outline-light d-lg-none me-2" id="btnSidebar" aria-label="Mở menu">
        <i class="bi bi-list"></i>
      </button>

      <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">
        <i class="bi bi-speedometer2 me-1"></i> Admin Panel
      </a>

      <div class="ms-auto d-flex align-items-center">
        <div class="dropdown">
          <button class="btn btn-outline-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle me-1"></i>
            {{ Auth::user()->name ?? 'Tài khoản' }}
          </button>
          <div class="dropdown-menu dropdown-menu-end">
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

  {{-- Overlay cho mobile --}}
  <div id="sidebarOverlay"></div>

  <div class="admin-wrapper">
    {{-- SIDEBAR --}}
    @php
      $usersRoute = request()->routeIs('admin.users.*');
      $rawRole    = (string) request('role');
      $normalizedRole = \App\Support\RoleResolver::map(strtoupper($rawRole), $rawRole)
        ?? \Illuminate\Support\Str::slug($rawRole);
      $roleSlug  = strtolower($normalizedRole);

      $usersBaseActive = $usersRoute && !in_array($roleSlug, ['teacher', 'student'], true);
      $teacherActive   = $usersRoute && $roleSlug === 'teacher';
      $studentActive   = $usersRoute && $roleSlug === 'student';
    @endphp

    <aside id="adminSidebar" class="admin-sidebar" aria-label="Điều hướng quản trị">
      <div class="px-3 py-3">
        <div class="text-muted small mb-2">Điều hướng</div>
        <ul class="nav flex-column gap-1">

          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                href="{{ route('admin.dashboard') }}">
              <i class="bi bi-grid me-2"></i> Tổng quan
            </a>
          </li>

          {{-- Học vụ --}}
          <li class="nav-item mt-3 text-muted small">Học vụ</li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
              href="{{ route('admin.categories.index') }}"
              title="Quản lý danh mục">
              <i class="bi bi-folder2-open me-2"></i> Danh mục
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}"
              href="{{ route('admin.courses.index') }}"
              title="Quản lý khóa học">
              <i class="bi bi-journal-text me-2"></i> Khóa học
            </a>
          </li>

          <li class="nav-item mt-3 text-muted small">Quản trị</li>
          <li class="nav-item">
            <a class="nav-link {{ $usersBaseActive ? 'active' : '' }}"
                href="{{ route('admin.users.index') }}"
                title="Quản lý người dùng và phân quyền">
              <i class="bi bi-people me-2"></i> Người dùng 
              {{-- <i class="bi bi-people me-2"></i> Người dùng &amp; Phân quyền --}}
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ $teacherActive ? 'active' : '' }}"
                href="{{ route('admin.users.index', ['role' => 'teacher']) }}"
                title="Danh sách giảng viên">
              <i class="bi bi-person-badge me-2"></i> Giảng viên
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ $studentActive ? 'active' : '' }}"
                href="{{ route('admin.users.index', ['role' => 'student']) }}"
                title="Danh sách học viên">
              <i class="bi bi-person-lines-fill me-2"></i> Học viên
            </a>
          </li>

          {{-- Chứng chỉ --}}
          <li class="nav-item mt-3 text-muted small">Chứng chỉ</li>
          <li class="nav-item">
            {{-- TODO: tạo route admin.certificates.index --}}
            <a class="nav-link {{ request()->routeIs('admin.certificates.*') ? 'active' : '' }}"
                href="#"
                title="Cấp & tra cứu chứng chỉ">
              <i class="bi bi-award me-2"></i> Chứng chỉ
            </a>
          </li>

        </ul>
      </div>
    </aside>

    {{-- MAIN --}}
    <main class="admin-main">
      @yield('content')
    </main>
  </div>

  {{-- Bootstrap bundle --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const btnSidebar = document.getElementById('btnSidebar');
    const sidebar    = document.getElementById('adminSidebar');
    const overlay    = document.getElementById('sidebarOverlay');

    function openSidebar() {
      sidebar?.classList.add('open');
      overlay?.classList.add('show');
      document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
      sidebar?.classList.remove('open');
      overlay?.classList.remove('show');
      document.body.style.overflow = '';
    }

    btnSidebar?.addEventListener('click', openSidebar);
    overlay?.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeSidebar();
    });
  </script>

  @stack('scripts')
</body>
</html>
