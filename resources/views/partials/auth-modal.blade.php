<div class="modal fade auth-modal" id="authModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered auth-modal-dialog">
        <div class="modal-content auth-modal-content">
            <div class="modal-body p-0 auth-modal-body">
                <div class="auth-container" id="authContainer">

                <!-- Sign Up Form -->
                <div class="auth-form-container sign-up-container">
                    <form class="auth-form" id="signupForm" action="{{ route('users.store') }}" method="post" novalidate>
                    @csrf
                    <input type="hidden" name="redirect" value="{{ request('redirect', url()->current()) }}">
                    <h1 class="auth-title">Đăng Ký</h1>
                    <!-- Social login buttons -->
                    <div class="auth-social-container">
                        <a class="auth-social" href="#"><i class="fab fa-facebook-f"></i></a>
                        <a class="auth-social" href="#"><i class="fab fa-google"></i></a>
                        <a class="auth-social" href="#"><i class="fab fa-github"></i></a>
                    </div>
                    <span class="auth-subtitle">hoặc sử dụng email của bạn để đăng ký</span>

                    <div class="auth-field-group">
                        <input class="auth-input @error('name') is-invalid @enderror" 
                               type="text" 
                               id="signup-name"
                               name="name" 
                               placeholder="Họ và Tên" 
                               value="{{ old('name') }}" 
                               required 
                               minlength="2" 
                               maxlength="255" 
                               autocomplete="name" />
                        <div class="auth-error" id="name-error">
                            @error('name'){{ $message }}@enderror
                        </div>
                    </div>

                    <div class="auth-field-group">
                        <input class="auth-input @error('email') is-invalid @enderror" 
                               type="email" 
                               id="signup-email"
                               name="email" 
                               placeholder="Email (VD: example@gmail.com)" 
                               value="{{ old('email') }}" 
                               required 
                               maxlength="255" 
                               autocomplete="email" 
                               autocapitalize="off" />
                        <div class="auth-error" id="email-error">
                            @error('email'){{ $message }}@enderror
                        </div>
                    </div>

                    <div class="auth-field-group">
                        <input class="auth-input @error('phone') is-invalid @enderror" 
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
                        <div class="auth-error" id="phone-error">
                            @error('phone'){{ $message }}@enderror
                        </div>
                    </div>

                    <div class="auth-field-group">
                        <div class="auth-input-wrap">
                            <input class="auth-input @error('password') is-invalid @enderror" 
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
                        <div class="auth-error" id="password-error">
                            @error('password'){{ $message }}@enderror
                        </div>
                    </div>

                    <div class="auth-field-group">
                        <div class="auth-input-wrap">
                            <input class="auth-input @error('password_confirmation') is-invalid @enderror" 
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
                        <div class="auth-error" id="password-confirm-error">
                            @error('password_confirmation'){{ $message }}@enderror
                        </div>
                    </div>

                    <button type="submit" class="auth-btn">Đăng Ký</button>
                    </form>
                </div>

                <!-- Sign In Form -->
                <div class="auth-form-container sign-in-container">
                    <form class="auth-form" action="{{ route('users.login') }}" method="post">
                    @csrf
                    <input type="hidden" name="redirect" value="{{ request('redirect', url()->current()) }}">
                    <h1 class="auth-title">Đăng Nhập</h1>
                    <div class="auth-social-container">
                        <a class="auth-social" href="#"><i class="fab fa-facebook-f"></i></a>
                        <a class="auth-social" href="#"><i class="fab fa-google"></i></a>
                        <a class="auth-social" href="#"><i class="fab fa-github"></i></a>
                    </div>
                    <span class="auth-subtitle">hoặc sử dụng tài khoản của bạn</span>

                    <input class="auth-input @error('email') is-invalid @enderror" type="email" name="email" placeholder="Email" value="{{ old('email') }}" required maxlength="255" autofocus autocomplete="email" autocapitalize="off" title="Vui lòng nhập email đúng định dạng" />
                    @error('email') <div class="auth-error">{{ $message }}</div> @enderror

                    <div class="auth-input-wrap">
                        <input class="auth-input @error('password') is-invalid @enderror" type="password" name="password" placeholder="Mật Khẩu" required minlength="6" maxlength="32" autocomplete="current-password" title="Mật khẩu phải có từ 6-32 ký tự" />
                        <button type="button" class="auth-toggle-pass" aria-label="Hiện/Ẩn mật khẩu"><i class="far fa-eye"></i></button>
                    </div>
                    @error('password') <div class="auth-error">{{ $message }}</div> @enderror

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

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const authModal = new bootstrap.Modal(document.getElementById('authModal'));
        const authContainer = document.getElementById('authContainer');
        
        @if($errors->has('name') || $errors->has('email') || $errors->has('phone') || $errors->has('password') || $errors->has('password_confirmation'))
            authContainer.classList.add('right-panel-active');
        @endif
        
        authModal.show();
    });
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation rules
    const validationRules = {
        name: {
            minLength: 2,
            maxLength: 255,
            pattern: /^.{2,255}$/,
            message: 'Họ tên phải có ít nhất 2 ký tự'
        },
        email: {
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            message: 'Email không đúng định dạng (VD: example@gmail.com)'
        },
        phone: {
            pattern: /^0\d{9,10}$/,
            minLength: 10,
            maxLength: 11,
            message: 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0'
        },
        password: {
            minLength: 6,
            maxLength: 32,
            message: 'Mật khẩu phải có từ 6-32 ký tự'
        }
    };

    // Get form elements
    const signupForm = document.getElementById('signupForm');
    const nameInput = document.getElementById('signup-name');
    const emailInput = document.getElementById('signup-email');
    const phoneInput = document.getElementById('signup-phone');
    const passwordInput = document.getElementById('signup-password');
    const passwordConfirmInput = document.getElementById('signup-password-confirm');

    // Validate name
    function validateName(value) {
        if (!value || value.trim().length === 0) {
            return 'Vui lòng nhập họ tên';
        }
        if (value.length < validationRules.name.minLength) {
            return validationRules.name.message;
        }
        if (value.length > validationRules.name.maxLength) {
            return 'Họ tên không được vượt quá 255 ký tự';
        }
        return '';
    }

    // Validate email
    function validateEmail(value) {
        if (!value || value.trim().length === 0) {
            return 'Vui lòng nhập email';
        }
        if (!validationRules.email.pattern.test(value)) {
            return validationRules.email.message;
        }
        if (value.length > 255) {
            return 'Email không được vượt quá 255 ký tự';
        }
        return '';
    }

    // Validate phone
    function validatePhone(value) {
        if (!value || value.trim().length === 0) {
            return 'Vui lòng nhập số điện thoại';
        }
        // Remove non-digit characters
        const cleanValue = value.replace(/[^0-9]/g, '');
        if (cleanValue.length < validationRules.phone.minLength) {
            return 'Số điện thoại phải có ít nhất 10 số';
        }
        if (cleanValue.length > validationRules.phone.maxLength) {
            return 'Số điện thoại không được vượt quá 11 số';
        }
        if (!validationRules.phone.pattern.test(cleanValue)) {
            return validationRules.phone.message;
        }
        return '';
    }

    // Validate password
    function validatePassword(value) {
        if (!value || value.length === 0) {
            return 'Vui lòng nhập mật khẩu';
        }
        if (value.length < validationRules.password.minLength) {
            return 'Mật khẩu phải có ít nhất 6 ký tự';
        }
        if (value.length > validationRules.password.maxLength) {
            return 'Mật khẩu không được vượt quá 32 ký tự';
        }
        return '';
    }

    // Validate password confirmation
    function validatePasswordConfirm(value, passwordValue) {
        if (!value || value.length === 0) {
            return 'Vui lòng xác nhận mật khẩu';
        }
        if (value !== passwordValue) {
            return 'Xác nhận mật khẩu không khớp';
        }
        return '';
    }

    // Show error message
    function showError(input, errorDiv, message) {
        if (message) {
            input.classList.add('is-invalid');
            errorDiv.textContent = message;
        } else {
            input.classList.remove('is-invalid');
            errorDiv.textContent = '';
        }
    }

    // Real-time validation
    if (nameInput) {
        nameInput.addEventListener('input', function() {
            const error = validateName(this.value);
            showError(this, document.getElementById('name-error'), error);
        });

        nameInput.addEventListener('blur', function() {
            const error = validateName(this.value);
            showError(this, document.getElementById('name-error'), error);
        });
    }

    if (emailInput) {
        emailInput.addEventListener('input', function() {
            const error = validateEmail(this.value);
            showError(this, document.getElementById('email-error'), error);
        });

        emailInput.addEventListener('blur', function() {
            const error = validateEmail(this.value);
            showError(this, document.getElementById('email-error'), error);
        });
    }

    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            // Only allow digits
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);
            const error = validatePhone(this.value);
            showError(this, document.getElementById('phone-error'), error);
        });

        phoneInput.addEventListener('blur', function() {
            const error = validatePhone(this.value);
            showError(this, document.getElementById('phone-error'), error);
        });
    }

    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const error = validatePassword(this.value);
            showError(this, document.getElementById('password-error'), error);
            
            // Re-validate password confirmation if it has value
            if (passwordConfirmInput && passwordConfirmInput.value) {
                const confirmError = validatePasswordConfirm(passwordConfirmInput.value, this.value);
                showError(passwordConfirmInput, document.getElementById('password-confirm-error'), confirmError);
            }
        });

        passwordInput.addEventListener('blur', function() {
            const error = validatePassword(this.value);
            showError(this, document.getElementById('password-error'), error);
        });
    }

    if (passwordConfirmInput) {
        passwordConfirmInput.addEventListener('input', function() {
            const error = validatePasswordConfirm(this.value, passwordInput.value);
            showError(this, document.getElementById('password-confirm-error'), error);
        });

        passwordConfirmInput.addEventListener('blur', function() {
            const error = validatePasswordConfirm(this.value, passwordInput.value);
            showError(this, document.getElementById('password-confirm-error'), error);
        });
    }

    // Form submission validation
    if (signupForm) {
        signupForm.addEventListener('submit', function(e) {
            let hasError = false;

            // Validate all fields
            const nameError = validateName(nameInput.value);
            showError(nameInput, document.getElementById('name-error'), nameError);
            if (nameError) hasError = true;

            const emailError = validateEmail(emailInput.value);
            showError(emailInput, document.getElementById('email-error'), emailError);
            if (emailError) hasError = true;

            const phoneError = validatePhone(phoneInput.value);
            showError(phoneInput, document.getElementById('phone-error'), phoneError);
            if (phoneError) hasError = true;

            const passwordError = validatePassword(passwordInput.value);
            showError(passwordInput, document.getElementById('password-error'), passwordError);
            if (passwordError) hasError = true;

            const confirmError = validatePasswordConfirm(passwordConfirmInput.value, passwordInput.value);
            showError(passwordConfirmInput, document.getElementById('password-confirm-error'), confirmError);
            if (confirmError) hasError = true;

            if (hasError) {
                e.preventDefault();
                return false;
            }
        });
    }
});
</script>

