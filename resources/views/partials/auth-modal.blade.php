@php($authRedirect = request('redirect', url()->current()))

<div class="modal fade auth-modal" id="authModal" tabindex="-1" aria-hidden="true"
     data-open-on-load="{{ session()->has('error') ? 'true' : 'false' }}"
     data-default-panel="{{ old('name') || old('phone') ? 'register' : 'login' }}">
    <div class="modal-dialog modal-lg modal-dialog-centered auth-modal-dialog">
        <div class="modal-content auth-modal-content">
            <div class="modal-body p-0 auth-modal-body">
                <div class="auth-container" id="authContainer">

                <!-- Sign Up Form -->
                <div class="auth-form-container sign-up-container">
                    <form class="auth-form" id="signupForm" action="{{ route('users.store') }}" method="post" novalidate>
                    @csrf
                    <input type="hidden" name="redirect" value="{{ $authRedirect }}">
                    <h1 class="auth-title">Đăng Ký</h1>
                    <!-- Social login buttons -->
                    <div class="auth-social-container">
                        <a
                            class="auth-social auth-social--google"
                            data-auth-provider-link="google"
                            data-provider-url="{{ route('student.auth.google.redirect') }}"
                            href="{{ route('student.auth.google.redirect', ['redirect' => $authRedirect]) }}"
                            title="Đăng ký nhanh với Google"
                        >
                            <i class="fab fa-google"></i>
                        </a>
                    </div>
                    <span class="auth-subtitle">hoặc sử dụng email của bạn để đăng ký</span>

                    <div class="auth-field-group">
                        <input class="auth-input" 
                               type="text" 
                               id="signup-name"
                               name="name" 
                               placeholder="Họ và Tên" 
                               value="{{ old('name') }}" 
                               required 
                               minlength="2" 
                               maxlength="255" 
                               autocomplete="name" />
                        <div class="auth-error" id="name-error"></div>
                    </div>

                    <div class="auth-field-group">
                        <input class="auth-input" 
                               type="email" 
                               id="signup-email"
                               name="email" 
                               placeholder="Email (VD: example@gmail.com)" 
                               value="{{ old('email') }}" 
                               required 
                               maxlength="255" 
                               autocomplete="email" 
                               autocapitalize="off" />
                        <div class="auth-error" id="email-error"></div>
                    </div>

                    <div class="auth-field-group">
                        <input class="auth-input" 
                               type="tel" 
                               id="signup-phone"
                               name="phone" 
                               placeholder="Số Điện Thoại" 
                               value="{{ old('phone') }}" 
                               required 
                               inputmode="numeric" 
                               minlength="10" 
                               maxlength="11" 
                               pattern="^0\d{9,10}$" 
                               autocomplete="tel" />
                        <div class="auth-error" id="phone-error"></div>
                    </div>

                    <div class="auth-field-group">
                        <div class="auth-input-wrap">
                            <input class="auth-input" 
                                   type="password" 
                                   id="signup-password"
                                   name="password" 
                                   placeholder="Mật Khẩu (6-32 ký tự)" 
                                   required 
                                   minlength="6" 
                                   maxlength="32" 
                                   autocomplete="new-password" />
                            <button type="button" class="auth-toggle-pass" aria-label="Hiện/Ẩn mật khẩu">
                                <i class="far fa-eye"></i>
                            </button>
                        </div>
                        <div class="auth-error" id="password-error"></div>
                    </div>

                    <div class="auth-field-group">
                        <div class="auth-input-wrap">
                            <input class="auth-input" 
                                   type="password" 
                                   id="signup-password-confirm"
                                   name="password_confirmation" 
                                   placeholder="Xác Nhận Mật Khẩu" 
                                   required 
                                   minlength="6" 
                                   maxlength="32" 
                                   autocomplete="new-password" />
                            <button type="button" class="auth-toggle-pass" aria-label="Hiện/Ẩn mật khẩu">
                                <i class="far fa-eye"></i>
                            </button>
                        </div>
                        <div class="auth-error" id="password-confirm-error"></div>
                    </div>

                    <button type="submit" class="auth-btn">Đăng Ký</button>
                    </form>
                </div>

                <!-- Sign In Form -->
                <div class="auth-form-container sign-in-container">
                    <form class="auth-form" id="loginForm" action="{{ route('users.login') }}" method="post" novalidate>
                    @csrf
                    <input type="hidden" name="redirect" value="{{ $authRedirect }}">
                    <h1 class="auth-title">Đăng Nhập</h1>
                    <div class="auth-social-container">
                        <a
                            class="auth-social auth-social--google"
                            data-auth-provider-link="google"
                            data-provider-url="{{ route('student.auth.google.redirect') }}"
                            href="{{ route('student.auth.google.redirect', ['redirect' => $authRedirect]) }}"
                            title="Đăng nhập bằng Google"
                        >
                            <i class="fab fa-google"></i>
                        </a>
                    </div>
                    <span class="auth-subtitle">hoặc sử dụng tài khoản của bạn</span>

                    <div class="auth-field-group">
                        <input class="auth-input" 
                               type="email" 
                               id="login-email"
                               name="email" 
                               placeholder="Email" 
                               value="{{ old('email') }}" 
                               required 
                               maxlength="255" 
                               autofocus 
                               autocomplete="email" 
                               autocapitalize="off" />
                        <div class="auth-error" id="login-email-error"></div>
                    </div>

                    <div class="auth-field-group">
                        <div class="auth-input-wrap">
                            <input class="auth-input" 
                                   type="password" 
                                   id="login-password"
                                   name="password" 
                                   placeholder="Mật Khẩu" 
                                   required 
                                   minlength="6" 
                                   maxlength="32" 
                                   autocomplete="current-password" />
                            <button type="button" class="auth-toggle-pass" aria-label="Hiện/Ẩn mật khẩu">
                                <i class="far fa-eye"></i>
                            </button>
                        </div>
                        <div class="auth-error" id="login-password-error"></div>
                    </div>

                    <a href="#" class="auth-link" id="toForgotPassword">Bạn quên mật khẩu?</a>
                    <button type="submit" class="auth-btn">Đăng Nhập</button>
                    </form>
                </div>

                <!-- Forgot Password / OTP / Reset -->
                <div class="auth-form-container forgot-password-container">
                    <!-- Form nhập email -->
                    <form class="auth-form" id="forgotPasswordForm" action="{{ route('password.send') }}" method="post">
                    @csrf
                    <h1 class="auth-title">Quên Mật Khẩu</h1>
                    <span class="auth-subtitle">Nhập email để nhận OTP</span>
                    <input class="auth-input @error('email') is-invalid @enderror" type="email" name="email" placeholder="Email" value="{{ old('email') }}" required autocomplete="email" autocapitalize="off" />
                    @error('email') <div class="auth-error">{{ $message }}</div> @enderror
                    <button type="submit" class="auth-btn">Gửi OTP</button>
                    <button type="button" class="auth-link mt-2" id="backToLogin">Quay lại Đăng Nhập</button>
                    </form>

                    <!-- Form nhập OTP -->
                    <form class="auth-form d-none" id="verifyOtpForm" action="{{ route('password.verify') }}" method="post">
                    @csrf
                    <input type="hidden" name="email" id="otpEmail" value="">
                    <h1 class="auth-title">Nhập OTP</h1>
                    <span class="auth-subtitle">Nhập mã OTP bạn nhận được qua email</span>
                    <input class="auth-input" type="text" name="token" placeholder="Mã OTP" required />
                    <button type="submit" class="auth-btn">Xác Nhận OTP</button>
                    <button type="button" class="auth-link mt-2" id="backToEmail">Quay lại Nhập Email</button>
                    </form>

                    <!-- Form reset mật khẩu -->
                    <form class="auth-form d-none" id="resetPasswordForm" action="{{ route('password.update') }}" method="post">
                    @csrf
                    <input type="hidden" name="email" id="resetEmail" value="">
                    <h1 class="auth-title">Đặt Lại Mật Khẩu</h1>
                    <span class="auth-subtitle">Nhập mật khẩu mới</span>

                    <div class="auth-input-wrap">
                        <input class="auth-input" type="password" name="password" placeholder="Mật khẩu mới (6-32 ký tự)" required minlength="6" maxlength="32" autocomplete="new-password" title="Mật khẩu phải có từ 6-32 ký tự" />
                        <button type="button" class="auth-toggle-pass" aria-label="Hiện/Ẩn mật khẩu"><i class="far fa-eye"></i></button>
                    </div>

                    <div class="auth-input-wrap">
                        <input class="auth-input" type="password" name="password_confirmation" placeholder="Xác nhận mật khẩu" required minlength="6" maxlength="32" autocomplete="new-password" title="Nhập lại mật khẩu giống ô trên" />
                        <button type="button" class="auth-toggle-pass" aria-label="Hiện/Ẩn mật khẩu"><i class="far fa-eye"></i></button>
                    </div>

                    <button type="submit" class="auth-btn">Đặt lại mật khẩu</button>
                    <button type="button" class="auth-link mt-2" id="backToOtp">Quay lại Nhập OTP</button>
                    </form>
                </div>

                <!-- Overlay -->
                <div class="auth-overlay-container">
                    <div class="auth-overlay">
                    <div class="auth-overlay-panel auth-overlay-left">
                        <h1>Chào mừng trở lại!</h1>
                        <p>Vui lòng đăng nhập để kết nối với chúng tôi</p>
                        <button class="auth-btn ghost" id="signIn">Đăng Nhập</button>
                    </div>
                    <div class="auth-overlay-panel auth-overlay-right">
                        <h1>Chào bạn!</h1>
                        <p>Nhập thông tin để bắt đầu hành trình cùng chúng tôi</p>
                        <button class="auth-btn ghost" id="signUp">Đăng Ký</button>
                    </div>
                    </div>
                </div>

                </div>

            </div>
        </div>
    </div>
</div>



