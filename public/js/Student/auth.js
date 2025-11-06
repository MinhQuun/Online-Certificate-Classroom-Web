// ===================== Helpers =====================
const qs = (s, r) => (r || document).querySelector(s);
const qsa = (s, r) => Array.from((r || document).querySelectorAll(s));

function showToast(message, type = "success", duration = 4200, title) {
    const normalizedType = type === "warn" ? "warning" : type;
    if (window.flashToast?.push) {
        window.flashToast.push(message, normalizedType, title, duration);
        return;
    }

    let stack = document.querySelector(".toast-stack");
    if (!stack) {
        stack = document.createElement("div");
        stack.className = "toast-stack";
        document.body.appendChild(stack);
    }

    const card = document.createElement("div");
    card.className = `toast-card is-${normalizedType}`;
    card.dataset.autohide = String(duration);

    const iconMap = {
        success: '<i class="fa-solid fa-circle-check"></i>',
        error: '<i class="fa-solid fa-circle-exclamation"></i>',
        warning: '<i class="fa-solid fa-triangle-exclamation"></i>',
        info: '<i class="fa-solid fa-circle-info"></i>',
    };

    const heading =
        title ||
        (normalizedType === "success"
            ? "Thành công"
            : normalizedType === "error"
            ? "Lỗi"
            : normalizedType === "warning"
            ? "Chú ý"
            : "Thông báo");

    card.innerHTML = `
        <div class="toast-icon">${iconMap[normalizedType] || iconMap.info}</div>
        <div class="toast-content">
            <strong>${heading}</strong>
            <div class="toast-text">${message}</div>
        </div>
        <button class="toast-close" aria-label="Đóng"><i class="fa-solid fa-xmark"></i></button>
    `;

    stack.appendChild(card);

    const remove = () => {
        card.style.animation = "toast-fade-out .22s ease-in both";
        setTimeout(() => card.remove(), 220);
    };
    const ms = Number(duration || card.dataset.autohide || 4200);
    const timer = setTimeout(remove, ms);

    card.querySelector(".toast-close")?.addEventListener("click", () => {
        clearTimeout(timer);
        remove();
    });

    card.style.animation = "toast-fade-in .22s ease-out both";
}
// ===================== Clear form errors =====================
function clearFormErrors() {
    // Clear all error messages
    qsa(".auth-error").forEach((errorDiv) => {
        errorDiv.textContent = "";
    });

    // Remove invalid class from inputs
    qsa(".auth-input").forEach((input) => {
        input.classList.remove("is-invalid");
        input.classList.remove("is-valid");
    });
}

// ===================== Toggle đăng nhập / đăng ký =====================
(() => {
    const signUpButton = qs("#signUp");
    const signInButton = qs("#signIn");
    const container = qs("#authContainer");
    if (!signUpButton || !signInButton || !container) return;

    signUpButton.addEventListener("click", () => {
        clearFormErrors();
        container.classList.add("right-panel-active");
    });

    signInButton.addEventListener("click", () => {
        clearFormErrors();
        container.classList.remove("right-panel-active");
    });
})();

function setPanel(panel) {
    const container = qs("#authContainer");
    if (!container) return;
    clearFormErrors();
    container.classList.remove("forgot-password-mode");
    panel === "register"
        ? container.classList.add("right-panel-active")
        : container.classList.remove("right-panel-active");
}

// ===================== Show/hide password =====================
document.addEventListener("click", (e) => {
    const btn = e.target.closest(".auth-toggle-pass");
    if (!btn) return;
    const wrap = btn.closest(".auth-input-wrap");
    const input = wrap?.querySelector(
        'input[type="password"], input[type="text"]'
    );
    if (!input) return;

    input.type = input.type === "password" ? "text" : "password";
    const icon = btn.querySelector("i");
    if (icon) {
        icon.classList.toggle("fa-eye");
        icon.classList.toggle("fa-eye-slash");
        if (
            !icon.classList.contains("far") &&
            !icon.classList.contains("fas")
        ) {
            icon.classList.add("far");
        }
    }
});

// ===================== Modal defaults & form validation =====================
document.addEventListener("DOMContentLoaded", () => {
    const authModalEl = qs("#authModal");
    const authContainer = qs("#authContainer");
    const defaultPanel = (
        authModalEl?.dataset.defaultPanel || "login"
    ).toLowerCase();

    if (authContainer) {
        authContainer.classList.toggle(
            "right-panel-active",
            defaultPanel === "register"
        );
    }

    if (authModalEl?.dataset.openOnLoad === "true") {
        if (typeof openLoginModal === "function") {
            openLoginModal(undefined, defaultPanel);
        } else if (window.bootstrap?.Modal && authModalEl) {
            const ModalCtor = window.bootstrap.Modal;
            const modalInstance =
                typeof ModalCtor.getOrCreateInstance === "function"
                    ? ModalCtor.getOrCreateInstance(authModalEl)
                    : new ModalCtor(authModalEl);
            modalInstance?.show?.();
        }
    }

    const signupForm = qs("#signupForm");
    const nameInput = qs("#signup-name");
    const emailInput = qs("#signup-email");
    const phoneInput = qs("#signup-phone");
    const passwordInput = qs("#signup-password");
    const passwordConfirmInput = qs("#signup-password-confirm");
    const loginForm = qs("#loginForm");
    const loginEmailInput = qs("#login-email");
    const loginPasswordInput = qs("#login-password");

    const nameError = qs("#name-error");
    const emailError = qs("#email-error");
    const phoneError = qs("#phone-error");
    const passwordError = qs("#password-error");
    const passwordConfirmError = qs("#password-confirm-error");
    const loginEmailError = qs("#login-email-error");
    const loginPasswordError = qs("#login-password-error");

    const validationRules = {
        name: { minLength: 2, maxLength: 255 },
        email: { maxLength: 255, pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/ },
        phone: { minLength: 10, maxLength: 11, pattern: /^0\d{9,10}$/ },
        password: { minLength: 6, maxLength: 32 },
    };

    const validateName = (value = "") => {
        const trimmed = value.trim();
        if (!trimmed) return "Vui lòng nhập họ tên";
        if (trimmed.length < validationRules.name.minLength) {
            return "Họ tên phải có ít nhất 2 ký tự";
        }
        if (trimmed.length > validationRules.name.maxLength) {
            return "Họ tên không được vượt quá 255 ký tự";
        }
        return "";
    };

    const validateEmail = (value = "") => {
        const trimmed = value.trim();
        if (!trimmed) return "Vui lòng nhập email";
        if (trimmed.length > validationRules.email.maxLength) {
            return "Email không được vượt quá 255 ký tự";
        }
        if (!validationRules.email.pattern.test(trimmed)) {
            return "Email không đúng định dạng (VD: example@gmail.com)";
        }
        return "";
    };

    const validatePhone = (value = "") => {
        const digits = value.replace(/\D/g, "");
        if (!digits) return "Vui lòng nhập số điện thoại";
        if (digits.length < validationRules.phone.minLength) {
            return "Số điện thoại phải có ít nhất 10 số";
        }
        if (digits.length > validationRules.phone.maxLength) {
            return "Số điện thoại không được vượt quá 11 số";
        }
        if (!validationRules.phone.pattern.test(digits)) {
            return "Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0";
        }
        return "";
    };

    const validatePassword = (value = "") => {
        if (!value) return "Vui lòng nhập mật khẩu";
        if (value.length < validationRules.password.minLength) {
            return "Mật khẩu phải có ít nhất 6 ký tự";
        }
        if (value.length > validationRules.password.maxLength) {
            return "Mật khẩu không được vượt quá 32 ký tự";
        }
        return "";
    };

    const validatePasswordConfirm = (value = "", passwordValue = "") => {
        if (!value) return "Vui lòng xác nhận mật khẩu";
        if (value !== passwordValue) return "Xác nhận mật khẩu không khớp";
        return "";
    };

    const showError = (input, errorEl, message) => {
        if (!input || !errorEl) return;
        if (message) {
            input.classList.add("is-invalid");
            errorEl.textContent = message;
        } else {
            input.classList.remove("is-invalid");
            errorEl.textContent = "";
        }
    };

    if (nameInput && nameError) {
        const handle = () => {
            const error = validateName(nameInput.value);
            showError(nameInput, nameError, error);
        };
        nameInput.addEventListener("input", handle);
        nameInput.addEventListener("blur", handle);
    }

    if (emailInput && emailError) {
        const handle = () => {
            const error = validateEmail(emailInput.value);
            showError(emailInput, emailError, error);
        };
        emailInput.addEventListener("input", handle);
        emailInput.addEventListener("blur", handle);
    }

    if (phoneInput && phoneError) {
        phoneInput.addEventListener("input", () => {
            phoneInput.value = phoneInput.value
                .replace(/\D/g, "")
                .slice(0, validationRules.phone.maxLength);
            const error = validatePhone(phoneInput.value);
            showError(phoneInput, phoneError, error);
        });
        phoneInput.addEventListener("blur", () => {
            const error = validatePhone(phoneInput.value);
            showError(phoneInput, phoneError, error);
        });
    }

    if (passwordInput && passwordError) {
        passwordInput.addEventListener("input", () => {
            const error = validatePassword(passwordInput.value);
            showError(passwordInput, passwordError, error);

            if (
                passwordConfirmInput &&
                passwordConfirmError &&
                passwordConfirmInput.value
            ) {
                const confirmError = validatePasswordConfirm(
                    passwordConfirmInput.value,
                    passwordInput.value
                );
                showError(
                    passwordConfirmInput,
                    passwordConfirmError,
                    confirmError
                );
            }
        });
        passwordInput.addEventListener("blur", () => {
            const error = validatePassword(passwordInput.value);
            showError(passwordInput, passwordError, error);
        });
    }

    if (passwordConfirmInput && passwordConfirmError) {
        const handle = () => {
            const error = validatePasswordConfirm(
                passwordConfirmInput.value,
                passwordInput?.value || ""
            );
            showError(passwordConfirmInput, passwordConfirmError, error);
        };
        passwordConfirmInput.addEventListener("input", handle);
        passwordConfirmInput.addEventListener("blur", handle);
    }

    if (signupForm) {
        signupForm.addEventListener("submit", (e) => {
            let hasError = false;
            const validations = [
                [nameInput, nameError, validateName],
                [emailInput, emailError, validateEmail],
                [phoneInput, phoneError, validatePhone],
                [passwordInput, passwordError, validatePassword],
                [
                    passwordConfirmInput,
                    passwordConfirmError,
                    (value) =>
                        validatePasswordConfirm(
                            value,
                            passwordInput?.value || ""
                        ),
                ],
            ];

            validations.forEach(([input, errorEl, validator]) => {
                if (!input || !errorEl) return;
                const message = validator(input.value);
                showError(input, errorEl, message);
                if (message) hasError = true;
            });

            if (hasError) {
                e.preventDefault();
            }
        });
    }

    if (loginEmailInput && loginEmailError) {
        const handle = () => {
            const error = validateEmail(loginEmailInput.value);
            showError(loginEmailInput, loginEmailError, error);
        };
        loginEmailInput.addEventListener("input", handle);
        loginEmailInput.addEventListener("blur", handle);
    }

    if (loginPasswordInput && loginPasswordError) {
        const handle = () => {
            const error = validatePassword(loginPasswordInput.value);
            showError(loginPasswordInput, loginPasswordError, error);
        };
        loginPasswordInput.addEventListener("input", handle);
        loginPasswordInput.addEventListener("blur", handle);
    }

    if (loginForm) {
        loginForm.addEventListener("submit", (e) => {
            let hasError = false;
            if (loginEmailInput && loginEmailError) {
                const emailMessage = validateEmail(loginEmailInput.value);
                showError(loginEmailInput, loginEmailError, emailMessage);
                if (emailMessage) hasError = true;
            }
            if (loginPasswordInput && loginPasswordError) {
                const passwordMessage = validatePassword(
                    loginPasswordInput.value
                );
                showError(
                    loginPasswordInput,
                    loginPasswordError,
                    passwordMessage
                );
                if (passwordMessage) hasError = true;
            }
            if (hasError) {
                e.preventDefault();
            }
        });
    }
});

// ===================== Redirect handling =====================
let _redirectValue = "";
function setRedirectInputs(val) {
    _redirectValue = val || "";
    qsa("#authModal form.auth-form").forEach((form) => {
        let input = form.querySelector('input[name="redirect"]');
        if (!input) {
            input = document.createElement("input");
            input.type = "hidden";
            input.name = "redirect";
            form.appendChild(input);
        }
        input.value = _redirectValue;
    });

    qsa("[data-auth-provider-link]").forEach((link) => {
        const base = link.dataset.providerUrl || link.href;
        if (!base) return;
        try {
            const url = new URL(base, window.location.origin);
            if (_redirectValue) {
                url.searchParams.set("redirect", _redirectValue);
            } else {
                url.searchParams.delete("redirect");
            }
            link.href = url.toString();
        } catch (error) {
            console.warn("Không thể cập nhật URL đăng nhập Google.", error);
        }
    });
}
function getRedirectFrom(url) {
    try {
        const u = new URL(url, window.location.origin);
        return u.searchParams.get("redirect");
    } catch {
        return null;
    }
}

function getOpenFrom(url) {
    try {
        const u = new URL(url, window.location.origin);
        return (u.searchParams.get("open") || "").toLowerCase();
    } catch {
        return "";
    }
}

// ===================== Open login modal =====================
function openLoginModal(preferredRedirect, mode = "login") {
    if (preferredRedirect) {
        setRedirectInputs(preferredRedirect);
    }
    setPanel(mode === "register" ? "register" : "login");
    const modalEl = qs("#authModal");
    if (modalEl && window.bootstrap?.Modal) {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
        return true;
    }

    if (modalEl) {
        modalEl.classList.add("is-visible");
        modalEl.style.display = "block";
        modalEl.removeAttribute("aria-hidden");
        return true;
    }
    return false;
}
window._openLogin = openLoginModal;

// ===================== Auto open modal from query ?open=... =====================
(() => {
    function applyFromQuery() {
        const params = new URLSearchParams(window.location.search);
        const open = (params.get("open") || "").toLowerCase();
        const redirect = params.get("redirect");
        if (redirect) setRedirectInputs(redirect);
        if (!open) return;
        openLoginModal(redirect || "", open);
    }
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", applyFromQuery);
    } else applyFromQuery();

    const modalEl = qs("#authModal");
    if (modalEl) {
        modalEl.addEventListener("shown.bs.modal", () => {
            if (_redirectValue) setRedirectInputs(_redirectValue);
        });
        modalEl.addEventListener("hidden.bs.modal", () => {
            modalEl.classList.remove("is-visible");
            modalEl.style.display = "";
            modalEl.setAttribute("aria-hidden", "true");
        });
    }
})();

// ===================== Click [data-action="open-login"] =====================
document.addEventListener("click", (e) => {
    const trigger = e.target.closest('[data-action="open-login"]');
    if (!trigger) return;
    const href = trigger.getAttribute("href");
    const fromHref = href ? getRedirectFrom(href) : null;
    const fromData = trigger.getAttribute("data-redirect");
    const modeAttr = trigger.getAttribute("data-open") || "";
    const modeHref = href ? getOpenFrom(href) : "";
    const opened = openLoginModal(
        fromHref || fromData || "",
        (modeAttr || modeHref || "login").toLowerCase()
    );
    if (opened) e.preventDefault();
});

// ===================== Form submit helper =====================
async function handleFormSubmit(form, callback) {
    const submitBtn = form.querySelector('button[type="submit"]');
    if (!submitBtn) return;
    const originalText = submitBtn.innerHTML;

    submitBtn.disabled = true;
    submitBtn.innerHTML =
        '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

    try {
        const formData = new FormData(form);
        const res = await fetch(form.action, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN":
                    document.querySelector('meta[name="csrf-token"]')
                        ?.content || "",
            },
            body: formData,
        });

        let data = {};
        try {
            data = await res.json();
        } catch {}

        await callback(res, data, formData);
    } catch (err) {
        console.error(err);
        showToast("Có lỗi xảy ra, vui lòng thử lại.", "error");
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

// ===================== Forgot password / OTP / Reset =====================
document.addEventListener("DOMContentLoaded", function () {
    const container = qs("#authContainer");
    const forgotForm = qs("#forgotPasswordForm");
    const verifyOtpForm = qs("#verifyOtpForm");
    const resetForm = qs("#resetPasswordForm");
    const otpEmailInput = qs("#otpEmail");
    const resetEmailInput = qs("#resetEmail");

    const backToLogin = qs("#backToLogin");
    const backToEmail = qs("#backToEmail");
    const backToOtp = qs("#backToOtp");
    const toForgotPassword = qs("#toForgotPassword");

    // --- Step 1: send OTP ---
    if (forgotForm) {
        forgotForm.addEventListener("submit", (e) => {
            e.preventDefault();
            handleFormSubmit(forgotForm, async (res, data, formData) => {
                if (!res.ok || !data.status) {
                    showToast(
                        data.message ||
                            data.errors?.email?.[0] ||
                            "Có lỗi xảy ra",
                        "error"
                    );
                    return;
                }
                showToast(data.message || "OTP đã gửi thành công!", "success");
                forgotForm.classList.add("d-none");
                verifyOtpForm.classList.remove("d-none");
                otpEmailInput.value = formData.get("email");
            });
        });
    }

    // --- Step 2: verify OTP ---
    if (verifyOtpForm) {
        verifyOtpForm.addEventListener("submit", (e) => {
            e.preventDefault();
            handleFormSubmit(verifyOtpForm, async (res, data, formData) => {
                if (!res.ok || !data.status) {
                    showToast(data.message || "OTP không hợp lệ", "error");
                    return;
                }
                verifyOtpForm.classList.add("d-none");
                resetForm.classList.remove("d-none");
                resetEmailInput.value = formData.get("email");
                showToast("OTP hợp lệ, nhập mật khẩu mới!", "success");
            });
        });
    }

    // --- Step 3: reset password ---
    if (resetForm) {
        resetForm.addEventListener("submit", (e) => {
            e.preventDefault();
            handleFormSubmit(resetForm, async (res, data) => {
                if (!res.ok || data.errors) {
                    showToast(
                        data.message ||
                            data.errors?.password?.[0] ||
                            "Có lỗi xảy ra",
                        "error"
                    );
                    return;
                }
                showToast(
                    data.status || "Đặt lại mật khẩu thành công!",
                    "success"
                );
                resetForm.classList.add("d-none");
                container.classList.remove("forgot-password-mode");
                resetForm.reset();
                forgotForm.reset();
            });
        });
    }

    // --- Navigation buttons ---
    backToLogin?.addEventListener("click", () => {
        container.classList.remove("forgot-password-mode");
        forgotForm?.reset();
    });
    backToEmail?.addEventListener("click", () => {
        verifyOtpForm?.classList.add("d-none");
        forgotForm?.classList.remove("d-none");
    });
    backToOtp?.addEventListener("click", () => {
        resetForm?.classList.add("d-none");
        verifyOtpForm?.classList.remove("d-none");
    });
    toForgotPassword?.addEventListener("click", (e) => {
        e.preventDefault();
        container.classList.add("forgot-password-mode");
        forgotForm?.classList.remove("d-none");
        verifyOtpForm?.classList.add("d-none");
        resetForm?.classList.add("d-none");
    });
});
