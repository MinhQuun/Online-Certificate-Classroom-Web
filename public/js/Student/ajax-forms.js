/**
 * AJAX Form Handler - Prevent page reloads on form submissions
 * Handles: Cart operations, Review submission, Contact form
 */

(function () {
    "use strict";

    const CART_SUCCESS_FALLBACK = "Giỏ hàng đã được cập nhật.";
    const CART_ERROR_FALLBACK = "Đã xảy ra lỗi với giỏ hàng. Vui lòng thử lại.";
    const REVIEW_ERROR_FALLBACK = "Có lỗi xảy ra. Vui lòng thử lại.";
    const CONTACT_ERROR_FALLBACK = "Đã xảy ra lỗi. Vui lòng thử lại.";

    const toast = {
        push(message, type = "info", title) {
            if (!message) {
                return;
            }
            if (typeof showToast === "function") {
                showToast(message, type, undefined, title);
                return;
            }
            if (window.flashToast?.push) {
                window.flashToast.push(message, type, title);
                return;
            }
            window.alert(message);
        },
        queue(message, type = "info", title) {
            if (!message) {
                return;
            }
            if (window.flashToast?.queue) {
                window.flashToast.queue(message, type, title);
            } else {
                this.push(message, type, title);
            }
        },
    };

    const parseJson = async (response) => {
        try {
            return await response.json();
        } catch (_) {
            return {};
        }
    };

    const extractFirstError = (errors) => {
        if (!errors || typeof errors !== "object") {
            return null;
        }

        for (const key of Object.keys(errors)) {
            const value = errors[key];
            if (Array.isArray(value) && value.length) {
                return value[0];
            }
            if (typeof value === "string" && value) {
                return value;
            }
        }
        return null;
    };

    const updateCartBadge = (count) => {
        if (typeof count !== "number") {
            return;
        }
        const cartLink = document.querySelector(".header-icon--cart");
        if (!cartLink) {
            return;
        }
        let badge = cartLink.querySelector(".header-icon__badge");
        if (count > 0) {
            if (!badge) {
                badge = document.createElement("span");
                badge.className = "header-icon__badge";
                cartLink.appendChild(badge);
            }
            badge.textContent = String(count);
        } else if (badge) {
            badge.remove();
        }
    };

    function setupCartHandlers() {
        document.addEventListener("submit", (event) => {
            const form = event.target;
            if (
                !(form instanceof HTMLFormElement) ||
                !form.action ||
                !form.action.includes("/cart") ||
                form.action.includes("/cartridge")
            ) {
                return;
            }

            const cartAjaxFlag = form.dataset.cartAjax;
            if (
                cartAjaxFlag &&
                ["off", "false", "disabled", "no", "0"].includes(
                    cartAjaxFlag.toLowerCase()
                )
            ) {
                return;
            }

            if (event.defaultPrevented) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            handleCartRequest(form);
        });
    }

    async function handleCartRequest(form) {
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.setAttribute("aria-disabled", "true");
        }

        try {
            const response = await fetch(form.action, {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
                body: new FormData(form),
            });

            const data = await parseJson(response);

            if (typeof data.cartCount === "number") {
                updateCartBadge(data.cartCount);
            }

            const firstError = extractFirstError(data.errors);
            if (firstError) {
                toast.push(firstError, "error");
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.removeAttribute("aria-disabled");
                }
                return;
            }

            if (data.success) {
                toast.queue(data.message || CART_SUCCESS_FALLBACK, "success");
                window.location.reload();
                return;
            }

            toast.push(
                data.message || CART_ERROR_FALLBACK,
                data.success === false ? "info" : "error"
            );
        } catch (error) {
            console.error("[AJAX Cart] Error:", error);
            toast.push(CART_ERROR_FALLBACK, "error");
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.removeAttribute("aria-disabled");
            }
        }
    }

    function setupReviewHandler() {
        const reviewForm = document.getElementById("courseReviewForm");
        if (!reviewForm) {
            return;
        }

        reviewForm.addEventListener("submit", async function (event) {
            event.preventDefault();
            event.stopPropagation();

            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton ? submitButton.innerHTML : "";

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = "Đang gửi...";
            }

            try {
                const response = await fetch(this.action, {
                    method: "POST",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                    body: new FormData(this),
                });

                const data = await parseJson(response);

                if (data.success) {
                    toast.queue(
                        data.message || "Cảm ơn bạn đã đánh giá khóa học.",
                        "success"
                    );
                    window.location.reload();
                    return;
                }

                const firstError =
                    extractFirstError(data.errors) || data.message;
                toast.push(firstError || REVIEW_ERROR_FALLBACK, "error");
            } catch (error) {
                console.error("[AJAX Review] Error:", error);
                toast.push(REVIEW_ERROR_FALLBACK, "error");
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }
            }
        });
    }

    function setupContactHandler() {
        const contactForm = document.querySelector(".contact-form");
        if (!contactForm) {
            return;
        }

        contactForm.addEventListener("submit", async function (event) {
            event.preventDefault();
            event.stopPropagation();

            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton ? submitButton.innerHTML : "";

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = "Đang gửi...";
            }

            try {
                const response = await fetch(this.action, {
                    method: "POST",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                    body: new FormData(this),
                });

                const data = await parseJson(response);

                if (data.success) {
                    toast.push(
                        data.message ||
                            "Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi sớm!",
                        "success"
                    );
                    this.reset();
                } else {
                    const firstError =
                        extractFirstError(data.errors) || data.message;
                    toast.push(firstError || CONTACT_ERROR_FALLBACK, "error");
                }
            } catch (error) {
                console.error("[AJAX Contact] Error:", error);
                toast.push(CONTACT_ERROR_FALLBACK, "error");
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }
            }
        });
    }

    function init() {
        setupCartHandlers();
        setupReviewHandler();
        setupContactHandler();
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }
})();
