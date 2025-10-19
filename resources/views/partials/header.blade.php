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
                <li class="nav-item nav-item--mega" data-dropdown>
                    <button class="nav-link" type="button" data-dropdown-trigger aria-expanded="false">
                        <span>Danh mục khóa học</span>
                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.24l3.7-3.01a.75.75 0 0 1 .95 1.17l-4.2 3.4a.75.75 0 0 1-.95 0l-4.2-3.4a.75.75 0 0 1 .02-1.19z"/></svg>
                    </button>
                    <div class="dropdown-panel" data-dropdown-panel>
                        @if ($categories->isEmpty())
                            <div class="dropdown-empty-state">Chưa có danh mục nào.</div>
                        @else
                            <div class="dropdown-panel__cols" data-category-container>
                                <div class="dropdown-col dropdown-col--cats">
                                    <ul>
                                        @foreach ($categories as $category)
                                            <li>
                                                <button type="button" class="dropdown-cat" data-category-trigger="{{ $category->maDanhMuc }}">
                                                    {{ $category->tenDanhMuc }}
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="dropdown-col dropdown-col--courses">
                                    <div class="dropdown-placeholder" data-category-placeholder>Chọn danh mục để xem khóa học</div>
                                    @foreach ($categories as $category)
                                        <div class="dropdown-courses" data-category-panel="{{ $category->maDanhMuc }}">
                                            <h4>{{ $category->tenDanhMuc }}</h4>
                                            <ul>
                                                @forelse ($category->courses as $course)
                                                    <li>
                                                        <a href="{{ route('student.courses.show', $course->slug) }}">{{ $course->tenKH }}</a>
                                                    </li>
                                                @empty
                                                    <li class="dropdown-empty">Đang cập nhật khóa học</li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </li>
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

            <a href="#cart" class="header-icon" aria-label="Giỏ hàng">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 4a1 1 0 0 0-1 1v1H3.5a1 1 0 1 0 0 2H4l1.4 8.4A2.5 2.5 0 0 0 7.86 19H18.5a1 1 0 0 0 0-2H7.86a.5.5 0 0 1-.49-.41L7.24 16h9.63a2 2 0 0 0 1.97-1.64l1-5A2 2 0 0 0 17.89 7H6V5a1 1 0 0 0-1-1Zm3 16a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Zm8 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z"/></svg>
            </a>

            <button class="header-toggle" type="button" data-header-toggle aria-expanded="false" aria-label="Mở menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</header>