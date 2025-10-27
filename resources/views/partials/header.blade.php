@php
    $categories = $studentNavCategories ?? collect();
@endphp

<header class="site-header" data-site-header>
    <div class="site-header__inner oc-container">
        <a href="{{ route('student.courses.index') }}" class="brand">
            <img src="{{ asset('Assets/logo.png') }}" alt="Logo Online Certificate Classroom">
            <div class="brand__text">
                <span class="brand__name">Online Certificate Classroom</span>
                <span class="brand__tagline">Học chứng chỉ hiệu quả</span>
            </div>
        </a>

        <nav class="main-nav" aria-label="Thanh điều hướng chính">
            <ul class="nav-list">

                <!-- DROPDOWN: Danh mục khóa học -->
                <li class="nav-item nav-item--mega" data-dropdown>
                    <button class="nav-link" data-dropdown-trigger aria-expanded="false" aria-haspopup="true" aria-controls="menu-categories">
                        <span>Danh mục khóa học</span>
                        <i class="fa-solid fa-angle-down" aria-hidden="true"></i>
                    </button>

                    <div id="menu-categories" class="dropdown-panel" data-dropdown-panel role="menu" aria-label="Danh mục">
                        <ul class="dropdown-categories">
                            <li class="dropdown-category-item">
                                <a href="{{ route('student.courses.index') }}" class="dropdown-category-trigger">
                                    <span>Tất cả khóa học</span>
                                </a>
                            </li>
                            @foreach($categories as $cat)
                                <li class="dropdown-category-item" data-subdropdown>
                                    <a href="{{ route('student.courses.index', ['category' => $cat->slug]) }}"
                                        class="dropdown-category-trigger"
                                        data-subdropdown-trigger
                                        aria-expanded="false"
                                        aria-haspopup="true"
                                        role="button"
                                    >
                                        <span>{{ $cat->tenDanhMuc }}</span>
                                        <i class="fa-solid fa-angle-right" aria-hidden="true"></i>
                                    </a>

                                    <div class="subdropdown-panel" data-subdropdown-panel role="menu" aria-label="Khóa học {{ $cat->tenDanhMuc }}">
                                        <h4 class="subdropdown-title">{{ $cat->tenDanhMuc }}</h4>

                                        @if($cat->courses->isNotEmpty())
                                            <ul class="subdropdown-courses">
                                                @foreach($cat->courses as $course)
                                                    <li>
                                                        <a href="{{ route('student.courses.show', $course->slug) }}" role="menuitem">
                                                            {{ $course->tenKH }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="dropdown-empty-state">Chưa có khóa học</div>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </li>

                <!-- Các mục menu khác -->
                <li class="nav-item"><a class="nav-link" href="#services">Dịch vụ</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">Về chúng tôi</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Liên hệ</a></li>
            </ul>
        </nav>

        <div class="header-actions">
            <form action="{{ route('student.courses.index') }}" method="get" class="header-search" role="search">
                <label for="header-search" class="sr-only">Tìm khóa học</label>
                <input id="header-search" type="text" name="q" placeholder="Tìm khóa học..." value="{{ request('q') }}">
                <button type="submit" aria-label="Tìm kiếm">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16.94 15.12a8 8 0 1 0-1.82 1.82l4.65 4.65a1.28 1.28 0 0 0 1.81-1.81Zm-6.44.88a5.5 5.5 0 1 1 5.5-5.5a5.5 5.5 0 0 1-5.5 5.5Z"/></svg>
                </button>
            </form>

            @auth
                <div class="header-profile" data-profile>
                    <button type="button" class="header-profile__trigger" data-profile-trigger aria-expanded="false" aria-label="Tài khoản">
                        <span class="header-profile__avatar">{{ mb_substr(Auth::user()->name ?? 'U', 0, 1) }}</span>
                    </button>
                    <div class="header-profile__menu" data-profile-menu>
                        <div class="header-profile__meta">
                            <strong>{{ Auth::user()->name }}</strong>
                            <span>{{ Auth::user()->email }}</span>
                        </div>
                        <form action="{{ route('logout') }}" method="post" class="header-profile__logout">
                            @csrf
                            <button type="submit">Đăng xuất</button>
                        </form>
                    </div>
                </div>
            @else
                <div class="header-profile header-profile--guest">
                    <button
                        type="button"
                        class="header-profile__trigger header-profile__trigger--ghost"
                        data-action="open-login"
                        data-open="login"
                        data-redirect="{{ request()->fullUrl() }}"
                        aria-label="Đăng nhập hoặc đăng ký"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12a4.5 4.5 0 1 0-4.5-4.5A4.5 4.5 0 0 0 12 12Zm0 2c-3.35 0-6.5 1.77-6.5 4v1.25a.75.75 0 0 0 1.5 0V18c0-1.13 2.27-2.5 5-2.5s5 1.37 5 2.5v1.25a.75.75 0 0 0 1.5 0V18c0-2.23-3.15-4-6.5-4Z"/></svg>
                    </button>
                </div>
            @endauth

            <a href="{{ route('student.cart.index') }}" class="header-icon header-icon--cart" aria-label="Giỏ hàng">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 4a1 1 0 0 0-1 1v1H3.5a1 1 0 1 0 0 2H4l1.4 8.4A2.5 2.5 0 0 0 7.86 19H18.5a1 1 0 0 0 0-2H7.86a.5.5 0 0 1-.49-.41L7.24 16h9.63a2 2 0 0 0 1.97-1.64l1-5A2 2 0 0 0 17.89 7H6V5a1 1 0 0 0-1-1Zm3 16a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Zm8 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z"/></svg>
                @if(($studentCartCount ?? 0) > 0)
                    <span class="header-icon__badge">{{ $studentCartCount }}</span>
                @endif
            </a>

            <button class="header-toggle" type="button" data-header-toggle aria-expanded="false" aria-label="Mở menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer" />
@endpush

@push('scripts')
    <script src="{{ asset('js/Student/dropdown.js') }}" defer></script>
@endpush
