@php
    if (!function_exists('student_asset_version')) {
        function student_asset_version(string $relativePath): int
        {
            $fullPath = public_path($relativePath);
            return file_exists($fullPath) ? filemtime($fullPath) : time();
        }
    }

    $studentCoreStyles = [
        'css/Student/base.css',
        'css/Student/layout.css',
        'css/Student/components.css',
    ];

    $studentCoreScripts = [
        'js/Student/main.js',
        'js/Student/dropdown.js',
        'js/Student/accordion.js',
    ];
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Online Certificate Classroom')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    @foreach ($studentCoreStyles as $style)
        <link rel="stylesheet" href="{{ asset($style) }}?v={{ student_asset_version($style) }}">
    @endforeach
    @stack('styles')
</head>
<body>
    <header class="site-header" data-site-header>
        <div class="site-header__inner oc-container">
            <a href="{{ route('student.courses.index') }}" class="brand">
                <img src="{{ asset('Assets/logo.png') }}" alt="Online Certificate Classroom Logo">
                <div class="brand__text">
                    <span class="brand__name">Online Certificate Classroom</span>
                    <span class="brand__tagline">Học chứng chỉ hiệu quả</span>
                </div>
            </a>

            <nav class="main-nav" aria-label="Điều hướng chính">
                <div class="nav-item nav-item--dropdown" data-dropdown>
                    <button class="nav-link" type="button" data-dropdown-trigger aria-expanded="false">
                        <span>Danh mục khóa học</span>
                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.238l3.71-3.007a.75.75 0 1 1 .94 1.172l-4.2 3.4a.75.75 0 0 1-.94 0l-4.2-3.4a.75.75 0 0 1 .02-1.193z"/></svg>
                    </button>
                    <div class="dropdown-panel" data-dropdown-panel>
                        <div class="dropdown-grid">
                            @forelse ($studentNavCategories ?? [] as $category)
                                <div class="dropdown-column">
                                    <span class="dropdown-heading">
                                        {{ $category->tenDanhMuc }}
                                    </span>
                                    <ul class="dropdown-list">
                                        @forelse ($category->courses as $course)
                                            <li>
                                                <a href="{{ route('student.courses.show', $course->slug) }}">{{ $course->tenKH }}</a>
                                            </li>
                                        @empty
                                            <li class="dropdown-empty">Sắp ra mắt</li>
                                        @endforelse
                                    </ul>
                                </div>
                            @empty
                                <div class="dropdown-empty-state">Chưa có danh mục nào.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <a class="nav-link" href="#about">Về chúng tôi</a>
                <a class="nav-link" href="#contact">Liên hệ</a>
            </nav>

            <div class="header-actions">
                <form action="{{ route('student.courses.index') }}" method="get" class="header-search" role="search">
                    <label for="header-search" class="sr-only">Tìm khóa học</label>
                    <input id="header-search" type="text" name="q" placeholder="Tìm khóa học..." value="{{ request('q') }}">
                    <button type="submit" aria-label="Tìm kiếm">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16.94 15.12a8 8 0 1 0-1.82 1.82l4.65 4.65a1.28 1.28 0 0 0 1.81-1.81ZM10.5 16a5.5 5.5 0 1 1 5.5-5.5A5.5 5.5 0 0 1 10.5 16Z"/></svg>
                    </button>
                </form>
                <a href="#auth" class="header-link">Đăng nhập</a>
                <a href="#register" class="header-link header-link--accent">Đăng ký</a>
                <a href="#cart" class="header-icon" aria-label="Giỏ hàng">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 4a1 1 0 0 0-1 1v1H3.5a1 1 0 0 0 0 2H4l1.4 8.4A2.5 2.5 0 0 0 7.86 19H18.5a1 1 0 0 0 0-2H7.86a.5.5 0 0 1-.49-.41L7.24 16h9.63a2 2 0 0 0 1.97-1.64l1-5A2 2 0 0 0 17.89 7H6V5a1 1 0 0 0-1-1Zm3 16a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Zm8 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z"/></svg>
                </a>
                <button class="header-toggle" type="button" data-header-toggle aria-expanded="false" aria-label="Mở menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>

    <div class="site-body">
        @yield('content')
    </div>

    <footer class="site-footer" id="contact">
        <div class="site-footer__top oc-container">
            <div class="footer-column">
                <h3>Online Certificate Classroom</h3>
                <p>Nền tảng học chứng chỉ trực tuyến với lộ trình rõ ràng, tài nguyên đa định dạng và theo dõi tiến độ thông minh.</p>
                <div class="footer-contact">
                    <a href="mailto:support@occ.edu.vn">support@occ.edu.vn</a>
                    <a href="tel:+84901234567">+84 901 234 567</a>
                </div>
            </div>
            <div class="footer-column">
                <h4>Dịch vụ</h4>
                <ul>
                    <li><a href="#">Lộ trình chứng chỉ TOEIC</a></li>
                    <li><a href="#">Lộ trình chứng chỉ IELTS</a></li>
                    <li><a href="#">Tư vấn cá nhân</a></li>
                    <li><a href="#">Chứng chỉ doanh nghiệp</a></li>
                </ul>
            </div>
            <div class="footer-column" id="about">
                <h4>Về OCC</h4>
                <ul>
                    <li><a href="#">Giới thiệu</a></li>
                    <li><a href="#">Đội ngũ giảng viên</a></li>
                    <li><a href="#">Hỏi đáp</a></li>
                    <li><a href="#">Điều khoản &amp; Bảo mật</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Kết nối</h4>
                <p>Nhận tin tức và tài nguyên mới nhất mỗi tuần.</p>
                <form class="footer-subscribe" action="#" method="post">
                    <label for="subscribe-email" class="sr-only">Email</label>
                    <input type="email" id="subscribe-email" name="email" placeholder="Nhập email của bạn">
                    <button type="submit">Đăng ký</button>
                </form>
            </div>
        </div>
        <div class="site-footer__bottom">
            <div class="oc-container">
                <span>© {{ date('Y') }} Online Certificate Classroom. All rights reserved.</span>
                <div class="footer-links">
                    <a href="#">Chính sách bảo mật</a>
                    <a href="#">Điều khoản sử dụng</a>
                </div>
            </div>
        </div>
    </footer>

    @foreach ($studentCoreScripts as $script)
        <script src="{{ asset($script) }}?v={{ student_asset_version($script) }}" defer></script>
    @endforeach
    @stack('scripts')
</body>
</html>

