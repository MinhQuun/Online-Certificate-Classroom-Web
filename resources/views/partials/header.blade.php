@php
    $categories = $studentNavCategories ?? collect();
    $notificationPreview = $studentNotificationPreview ?? collect();
    $notificationUnread = $studentNotificationUnread ?? 0;
    $notificationBadge = $notificationUnread > 9 ? '9+' : $notificationUnread;
@endphp

<header class="site-header" data-site-header>
    <div class="site-header__inner oc-container">
        <div class="header-left">
            <a href="{{ route('student.courses.index') }}" class="brand">
                <img src="{{ asset('Assets/logo.png') }}" alt="Logo Online Certificate Classroom">
                <div class="brand__text">
                    <span class="brand__name">Online Certificate Classroom</span>
                    <span class="brand__tagline">Học chứng chỉ hiệu quả</span>
                </div>
            </a>

            <nav class="main-nav" aria-label="Thanh điều hướng chính">
                <ul class="nav-list">
                    <li class="nav-item nav-item--mobile-search">
                        <form action="{{ route('student.courses.index') }}" method="get" class="header-search" role="search">
                            <label for="header-search-mobile" class="sr-only">Tìm khóa học</label>
                            <input id="header-search-mobile" type="text" name="q" placeholder="Tìm khóa học..." value="{{ request('q') }}">
                            <button type="submit" aria-label="Tìm kiếm">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16.94 15.12a8 8 0 1 0-1.82 1.82l4.65 4.65a1.28 1.28 0 0 0 1.81-1.81Zm-6.44.88a5.5 5.5 0 1 1 5.5-5.5a5.5 5.5 0 0 1-5.5 5.5Z"/></svg>
                            </button>
                        </form>
                    </li>
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
                    {{-- @auth
                        <li class="nav-item"><a class="nav-link" href="{{ route('student.progress.index') }}">Tiến độ học tập</a></li>
                    @endauth --}}
                    <li class="nav-item"><a class="nav-link" href="{{ route('student.combos.index') }}">Combo ưu đãi</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('student.services') }}">Dịch vụ</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('student.about') }}">Về chúng tôi</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('student.contact') }}">Liên hệ</a></li>
                </ul>
            </nav>
        </div>

        <div class="header-center">
            <form action="{{ route('student.courses.index') }}" method="get" class="header-search" role="search">
                <label for="header-search" class="sr-only">Tìm khóa học</label>
                <input id="header-search" type="text" name="q" placeholder="Tìm khóa học..." value="{{ request('q') }}">
                <button type="submit" aria-label="Tìm kiếm">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16.94 15.12a8 8 0 1 0-1.82 1.82l4.65 4.65a1.28 1.28 0 0 0 1.81-1.81Zm-6.44.88a5.5 5.5 0 1 1 5.5-5.5a5.5 5.5 0 0 1-5.5 5.5Z"/></svg>
                </button>
            </form>
        </div>

        <div class="header-right">
            <div class="header-actions">
                <button
                    type="button"
                    class="header-icon header-icon--notify"
                    aria-label="Thong bao"
                    data-notification-trigger
                    data-endpoint="{{ route('student.notifications.index') }}"
                    data-read-template="{{ route('student.notifications.read', ['notification' => '__ID__']) }}"
                    data-mark-all-endpoint="{{ route('student.notifications.read-all') }}"
                    data-authenticated="{{ Auth::check() ? '1' : '0' }}"
                    data-unread-count="{{ $notificationUnread }}"
                    data-fallback-image="{{ asset('Assets/logo.png') }}"
                    @guest
                        data-action="open-login"
                        data-open="login"
                        data-redirect="{{ request()->fullUrl() }}"
                    @endguest
                >
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 3a5.5 5.5 0 0 0-5.5 5.5V13l-1.22 2.72A1 1 0 0 0 6.18 17h11.64a1 1 0 0 0 .9-1.4L17.5 13V8.5A5.5 5.5 0 0 0 12 3Zm0 18a2.5 2.5 0 0 1-2.45-2h4.9A2.5 2.5 0 0 1 12 21Z"/>
                    </svg>
                    <span class="header-icon__badge {{ $notificationUnread > 0 ? '' : 'is-hidden' }}" data-notification-badge>
                        {{ $notificationBadge }}
                    </span>
                </button>
            @auth
                @php
                    $fullName = Auth::user()->name ?? 'User';
                    $nameParts = explode(' ', trim($fullName));
                    $lastName = end($nameParts);
                    $initial = !empty($lastName) ? mb_substr($lastName, 0, 1) : mb_substr($fullName, 0, 1);

                    if (empty($initial)) { $initial = 'U'; }
                @endphp

                <div class="header-profile" data-profile>
                    <button type="button" class="header-profile__trigger" data-profile-trigger aria-expanded="false" aria-label="Tài khoản">
                        <span class="header-profile__avatar">{{ $initial }}</span>
                    </button>
                    <div class="header-profile__menu" data-profile-menu>
                        @php
                            $email = Auth::user()->email;
                            $emailParts = explode('@', $email);
                            $localPart = $emailParts[0] ?? '';
                            $domainPart = '@' . ($emailParts[1] ?? '');
                        @endphp
                        <div class="header-profile__meta">
                            <strong>{{ Auth::user()->name }}</strong>
                            <div class="header-profile__email">
                                <span class="email-local">{{ $localPart }}</span>
                                @if(!empty($domainPart))
                                    <span class="email-domain">{{ $domainPart }}</span>
                                @endif
                            </div>
                        </div>

                        <nav class="header-profile__nav">
                            <a href="{{ route('student.profile.show') }}">
                                <i class="fa-solid fa-user"></i>
                                <span> Trang cá nhân</span>
                            </a>
                            <a href="{{ route('student.my-courses') }}">
                                <i class="fa-solid fa-book-tanakh"></i>
                                <span> Khóa học của tôi</span>
                            </a>
                            <a href="{{ route('student.progress.index') }}">
                                <i class="fa-solid fa-chart-line"></i>
                                <span> Tiến độ học tập</span>
                            </a>
                            <a href="{{ route('student.order-history') }}">
                                <i class="fa-solid fa-cart-shopping"></i>
                                <span> Lịch sử đơn hàng</span>
                            </a>
                            @if (Auth::user()?->student)
                                <a href="{{ route('student.certificates.index') }}">
                                    <i class="fa-solid fa-award"></i>
                                    <span> Chứng chỉ của tôi</span>
                                </a>
                            @endif
                        </nav>

                        <form action="{{ route('logout') }}" method="post" class="header-profile__logout">
                            @csrf
                            <button type="submit">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                <span>Đăng xuất</span>
                            </button>
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
            </div>

            <button class="header-toggle" type="button" data-header-toggle aria-expanded="false" aria-label="Mở menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>

<div class="notification-overlay" data-notification-overlay aria-hidden="true">
    <div class="notification-modal" role="dialog" aria-modal="true" aria-labelledby="notificationCenterTitle">
        <div class="notification-modal__header">
            <div>
                <p class="notification-modal__eyebrow">Thông báo</p>
                <h3 id="notificationCenterTitle">Trung tâm thông báo</h3>
                <p class="notification-modal__subtitle">
                    Cập nhật nhanh điểm số, lịch học và ưu đãi đang diễn ra cho tài khoản của bạn.
                </p>
            </div>
            <div class="notification-modal__header-actions">
                <button type="button" class="notification-icon-btn" data-notification-refresh aria-label="Làm mới thông báo">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
                <button type="button" class="notification-icon-btn" data-notification-close aria-label="Đóng thông báo">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <div class="notification-modal__actions">
            <div class="notification-modal__status">
                <span class="notification-dot"></span>
                <span class="notification-modal__status-count" data-notification-unread>{{ $notificationUnread }}</span>
                <span> Thông báo chưa đọc</span>
            </div>
            <button
                type="button"
                class="notification-mark-all"
                data-notification-mark-all
                {{ $notificationUnread ? '' : 'disabled' }}
            >
                Đánh dấu đã đọc
            </button>
        </div>

        <div class="notification-modal__body">
            <div class="notification-error is-hidden" data-notification-error>
                <p>Không thể tải thông báo lúc này. Thử lại sau vào giây.</p>
            </div>

            <div class="notification-empty {{ $notificationPreview->isEmpty() ? '' : 'is-hidden' }}" data-notification-empty>
                <div class="notification-empty__icon"><i class="fa-regular fa-bell"></i></div>
                <p>Chưa có thông báo mới. Khi có thông báo về khóa học và ưu đãi chúng tôi sẽ cập nhật ở đây</p>
            </div>

            <div class="notification-empty {{ Auth::check() ? 'is-hidden' : '' }}" data-notification-guest>
                <div class="notification-empty__icon"><i class="fa-regular fa-circle-user"></i></div>
                <p>Đăng nhập để xem thông báo cá nhân của bạn.</p>
                <button
                    type="button"
                    class="notification-card__cta"
                    data-action="open-login"
                    data-open="login"
                    data-redirect="{{ request()->fullUrl() }}"
                >
                    Đăng nhập
                </button>
            </div>

            <div class="notification-loading is-hidden" data-notification-loading>
                @for($i = 0; $i < 3; $i++)
                    <div class="notification-skeleton-card">
                        <div class="notification-skeleton__media"></div>
                        <div class="notification-skeleton__lines">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                @endfor
            </div>

            <div class="notification-list" data-notification-list>
                @foreach($notificationPreview as $notification)
                    <article
                        class="notification-card {{ $notification->is_read ? 'is-read' : 'is-unread' }}"
                        data-notification-id="{{ $notification->maTB }}"
                    >
                        <div class="notification-card__media">
                            <img src="{{ $notification->thumbnail_url }}" alt="Minh hoạ thông báo" loading="lazy">
                        </div>
                        <div class="notification-card__content">
                            <div class="notification-card__top">
                                <span class="notification-pill notification-pill--{{ $notification->badge_tone }}">
                                    {{ $notification->type_label }}
                                </span>
                                <span class="notification-card__time">{{ $notification->time_label }}</span>
                            </div>
                            <h4 class="notification-card__title">{{ $notification->tieuDe }}</h4>
                            <p class="notification-card__desc">{{ \Illuminate\Support\Str::limit($notification->noiDung, 150) }}</p>
                            <div class="notification-card__actions">
                                @if($notification->resolved_action_url)
                                    <a href="{{ $notification->resolved_action_url }}" class="notification-card__cta">Xem chi tiết</a>
                                @endif
                                @if(!$notification->is_read)
                                    <button type="button" class="notification-card__mark" data-action="mark-read">
                                        Đánh dấu đã đọc
                                    </button>
                                @else
                                    <span class="notification-card__status">Đã đọc</span>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('css/Student/notifications.css') }}?v={{ student_asset_version('css/Student/notifications.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/Student/dropdown.js') }}" defer></script>
    <script src="{{ asset('js/Student/notifications.js') }}" defer></script>
@endpush


