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

    card.querySelector('.toast-close')?.addEventListener('click', () => {
        clearTimeout(timer);
        remove();
    });

    card.style.animation = "toast-fade-in .22s ease-out both";
}
// ===================== Clear form errors =====================
function clearFormErrors() {
    // Clear all error messages
    qsa('.auth-error').forEach(errorDiv => {
        errorDiv.textContent = '';
    });
    
    // Remove invalid class from inputs
    qsa('.auth-input').forEach(input => {
        input.classList.remove('is-invalid');
        input.classList.remove('is-valid');
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

// ===================== Redirect handling =====================
let _redirectValue = "";
function setRedirectInputs(val) {
    if (!val) return;
    _redirectValue = val;
    qsa("#authModal form.auth-form").forEach((form) => {
        let input = form.querySelector('input[name="redirect"]');
        if (!input) {
            input = document.createElement("input");
            input.type = "hidden";
            input.name = "redirect";
            form.appendChild(input);
        }
        input.value = val;
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