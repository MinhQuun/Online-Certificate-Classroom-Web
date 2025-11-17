document.addEventListener("DOMContentLoaded", () => {
    const pendingCartButtons = new Map();

    const submitCartForm = (form) => {
        if (typeof form.requestSubmit === "function") {
            form.requestSubmit();
            return true;
        }

        const event = new Event("submit", { bubbles: true, cancelable: true });
        return form.dispatchEvent(event);
    };

    const resetCartButton = (button) => {
        if (!button) {
            return;
        }

        const original =
            button.dataset.originalLabel || button.textContent.trim();
        button.textContent = original;
        button.disabled = false;
        button.classList.remove("is-busy");
        button.classList.remove("course-card__cta--in-cart");
        button.setAttribute("aria-label", original);
    };

    const finalizeCartButton = (button) => {
        if (!button) {
            return;
        }

        const addedLabel =
            button.dataset.cartAddedLabel || "Đã trong giỏ hàng";
        button.textContent = addedLabel;
        button.disabled = true;
        button.classList.remove("is-busy");
        button.classList.add("course-card__cta--in-cart");
        button.setAttribute("aria-label", addedLabel);
    };

    const initAddToCartButtons = () => {
        const buttons = document.querySelectorAll("[data-add-to-cart]");
        if (!buttons.length) {
            return;
        }

        buttons.forEach((button) => {
            button.addEventListener("click", (event) => {
                event.preventDefault();
                event.stopPropagation();

                if (button.disabled) {
                    return;
                }

                const courseId = button.getAttribute("data-add-to-cart");
                if (!courseId) {
                    return;
                }

                const form = document.querySelector(
                    `.cart-form[data-course-id="${courseId}"]`
                );
                if (!form) {
                    console.warn(
                        "[Course Show] Không tìm thấy form giỏ hàng cho",
                        courseId
                    );
                    return;
                }

                const originalLabel =
                    button.dataset.originalLabel || button.textContent.trim();
                button.dataset.originalLabel = originalLabel;

                const addingLabel =
                    button.dataset.cartAddingLabel || "Đang thêm...";
                button.textContent = addingLabel;
                button.disabled = true;
                button.classList.add("is-busy");

                pendingCartButtons.set(form, button);

                const submitted = submitCartForm(form);
                if (!submitted) {
                    pendingCartButtons.delete(form);
                    resetCartButton(button);
                }
            });
        });

        document.addEventListener("cart:request:finished", (event) => {
            const detail = event.detail || {};
            const { form, success } = detail;
            if (!form || !pendingCartButtons.has(form)) {
                return;
            }

            const button = pendingCartButtons.get(form);
            pendingCartButtons.delete(form);

            if (success) {
                finalizeCartButton(button);
            } else {
                resetCartButton(button);
            }
        });
    };

    initAddToCartButtons();

    const flags = document.getElementById("courseAccessFlags");
    const isAuthenticated = flags?.dataset.authenticated === "1";
    const isEnrolled = flags?.dataset.enrolled === "1";
    const freeLessonId = flags?.dataset.freeLesson || "";
    const freeMiniTestId = flags?.dataset.freeMinitest || "";
    const lockedPrompt = flags?.dataset.lockedPrompt || "";
    const lockedTarget = flags?.dataset.lockedTarget || "";
    const lockedLessonId = flags?.dataset.lockedLesson || "";

    const lockedNotice = document.getElementById("lockedNotice");
    let lockedTimer;

    const openAuth = () => {
        const el = document.getElementById("authModal");
        if (el) new bootstrap.Modal(el).show();
    };

    const openEnroll = () => {
        const el = document.getElementById("enrollPromptModal");
        if (el) new bootstrap.Modal(el).show();
    };

    const showLockedNotice = () => {
        if (!lockedNotice) return;
        lockedNotice.hidden = false;
        lockedNotice.classList.add("is-visible");
        if (lockedTimer) clearTimeout(lockedTimer);
        lockedTimer = window.setTimeout(() => {
            lockedNotice?.classList.remove("is-visible");
        }, 5000);
    };

    lockedNotice
        ?.querySelector(".course-locked-notice__close")
        ?.addEventListener("click", () => {
            lockedNotice.hidden = true;
            lockedNotice.classList.remove("is-visible");
        });

    const handleLockedClick = (event) => {
        event?.preventDefault();
        showLockedNotice();
        openEnroll();
    };

    if (!isEnrolled) {
        // Xử lý lock cho lessons
        document.querySelectorAll("a[data-lesson-id]").forEach((anchor) => {
            const lessonId = anchor.dataset.lessonId || "";
            const isFree = freeLessonId && lessonId === freeLessonId;
            if (isFree) {
                anchor.classList.add("lesson-link--free");
                return;
            }

            anchor.classList.add("lesson-link--locked");
            anchor.addEventListener("click", handleLockedClick, {
                passive: false,
            });
        });

        // Xử lý lock cho MiniTests
        document.querySelectorAll(".mini-test-item").forEach((item) => {
            const miniId = item.dataset.miniTestId || "";
            const isFreeMini = freeMiniTestId && miniId === freeMiniTestId;
            if (isEnrolled || isFreeMini) return;

            // Lock link chính
            const mainLink = item.querySelector(".mini-test-link");
            if (mainLink) {
                mainLink.classList.add("lesson-link--locked");
                mainLink.addEventListener("click", handleLockedClick, {
                    passive: false,
                });
            }

            // Lock resources
            item.querySelectorAll(".resource-list a").forEach((link) => {
                link.classList.add("locked-resource");
                link.addEventListener("click", handleLockedClick, {
                    passive: false,
                });
            });
        });

        // Lock final test resources
        document
            .querySelectorAll(".final-tests__grid .resource-list a")
            .forEach((link) => {
                link.classList.add("locked-resource");
                link.addEventListener("click", handleLockedClick, {
                    passive: false,
                });
            });

        // Highlight locked lesson nếu có
        if (lockedLessonId) {
            const target = document.querySelector(
                `a[data-lesson-id="${lockedLessonId}"]`
            );
            if (target) {
                target.classList.add("lesson-link--locked-active");
                target.scrollIntoView({ behavior: "smooth", block: "center" });
                window.setTimeout(
                    () => target.classList.remove("lesson-link--locked-active"),
                    4000
                );
            }
        }
    }

    // Xử lý prompt từ URL
    const resolvePrompt = () => {
        if (lockedPrompt) return lockedPrompt;

        try {
            const params = new URLSearchParams(window.location.search);
            return params.get("prompt");
        } catch (error) {
            return null;
        }
    };

    const prompt = resolvePrompt();
    if (prompt === "auth") {
        openAuth();
    } else if (prompt === "enroll") {
        openEnroll();
    } else if (prompt === "locked") {
        showLockedNotice();
        openEnroll();
    }

    if (lockedTarget === "lesson" && !isEnrolled) {
        showLockedNotice();
    }

    const initRatingForm = () => {
        const wrapper = document.querySelector("[data-rating-input]");
        if (!wrapper) return;

        const form = wrapper.closest("form");
        const hiddenInput = form?.querySelector('input[name="diemSo"]');
        if (!form || !hiddenInput) return;

        const stars = Array.from(wrapper.querySelectorAll(".review-form__star"));
        let committed = parseInt(hiddenInput.value || wrapper.dataset.initial || "0", 10) || 0;

        const applyState = (value) => {
            stars.forEach((star) => {
                const starValue = parseInt(star.dataset.value || "0", 10);
                const isActive = value >= starValue;
                star.classList.toggle("is-active", isActive);
                star.setAttribute("aria-pressed", isActive ? "true" : "false");
            });
        };

        const commitValue = (value) => {
            committed = value;
            hiddenInput.value = value > 0 ? String(value) : "";
            applyState(committed);
        };

        stars.forEach((star) => {
            const starValue = parseInt(star.dataset.value || "0", 10);
            star.addEventListener("mouseenter", () => applyState(starValue));
            star.addEventListener("focus", () => applyState(starValue));
            star.addEventListener("click", (event) => {
                event.preventDefault();
                commitValue(starValue);
                wrapper.classList.remove("review-form__rating--error");
            });
            star.addEventListener("keydown", (event) => {
                if (event.key === "Enter" || event.key === " ") {
                    event.preventDefault();
                    commitValue(starValue);
                    wrapper.classList.remove("review-form__rating--error");
                }
                if (event.key === "ArrowLeft" || event.key === "ArrowDown") {
                    event.preventDefault();
                    const next = Math.max(1, committed - 1);
                    stars[Math.max(next - 1, 0)]?.focus();
                    commitValue(next);
                }
                if (event.key === "ArrowRight" || event.key === "ArrowUp") {
                    event.preventDefault();
                    const next = Math.min(5, committed + 1);
                    stars[Math.min(next - 1, stars.length - 1)]?.focus();
                    commitValue(next);
                }
            });
        });

        wrapper.addEventListener("mouseleave", () => applyState(committed));
        wrapper.addEventListener("focusout", (event) => {
            if (!wrapper.contains(event.relatedTarget)) {
                applyState(committed);
            }
        });

        form.addEventListener("submit", (event) => {
            const currentValue = parseInt(hiddenInput.value || "0", 10);
            if (!currentValue) {
                event.preventDefault();
                wrapper.classList.add("review-form__rating--error");
                stars[0]?.focus();
            }
        });

        applyState(committed);
    };

    initRatingForm();

    const reviewSection = document.getElementById("course-reviews");
    if (reviewSection && reviewSection.querySelector(".alert")) {
        window.setTimeout(() => {
            reviewSection.scrollIntoView({ behavior: "smooth", block: "start" });
        }, 150);
    }

    // ==================== SCROLL ANIMATIONS ====================
    // Add animation class to body
    const courseDetail = document.querySelector("[data-course-detail]");
    if (courseDetail) {
        document.body.classList.add("course-animate-ready");
        window.requestAnimationFrame(() => {
            courseDetail.classList.add("is-animated");
        });
    }

    // Setup Intersection Observer for scroll animations
    const revealTargets = document.querySelectorAll(
        "[data-reveal-on-scroll], [data-reveal-from-left], [data-reveal-from-right], [data-reveal-scale]"
    );

    if (!revealTargets.length) {
        return;
    }

    if ("IntersectionObserver" in window) {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("is-visible");
                    } else {
                        // Remove để reset animation khi scroll lên
                        entry.target.classList.remove("is-visible");
                    }
                });
            },
            { 
                threshold: 0.15, // Trigger khi 15% element vào view
                rootMargin: "0px 0px -50px 0px" // Trigger sớm hơn một chút
            }
        );

        revealTargets.forEach((el) => observer.observe(el));
    } else {
        // Fallback for browsers without IntersectionObserver
        revealTargets.forEach((el) => el.classList.add("is-visible"));
    }
});
