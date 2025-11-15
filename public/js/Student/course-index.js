(function () {
    'use strict';

    const pendingCartButtons = new Map();

    function onReady(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback, { once: true });
        } else {
            callback();
        }
    }

    function initStickyHeading() {
        const headingBar = document.querySelector('[data-sticky-heading]');
        if (!headingBar || !headingBar.parentElement) {
            return;
        }

        let sentinel = headingBar.previousElementSibling;
        if (!sentinel || !sentinel.classList.contains('courses-heading-bar__sentinel')) {
            sentinel = document.createElement('span');
            sentinel.className = 'courses-heading-bar__sentinel';
            sentinel.setAttribute('aria-hidden', 'true');
            headingBar.parentElement.insertBefore(sentinel, headingBar);
        }

        const observer = new IntersectionObserver(
            (entries) => {
                const entry = entries[0];
                if (!entry) {
                    return;
                }
                headingBar.classList.toggle('is-stuck', !entry.isIntersecting);
            },
            { threshold: [0] }
        );

        observer.observe(sentinel);
    }

    function initDynamicHeadingIndicator() {
        const headingBar = document.querySelector('[data-sticky-heading]');
        const indicator = document.querySelector('[data-heading-indicator]');
        const indicatorValue = document.querySelector('[data-heading-indicator-text]');

        if (!headingBar || !indicator || !indicatorValue) {
            return;
        }

        const bands = Array.from(document.querySelectorAll('.course-band[data-band-title]'));
        const defaultTitle =
            headingBar.getAttribute('data-heading-default-title') ||
            indicatorValue.textContent.trim() ||
            'Tất cả khóa học';

        indicatorValue.textContent = defaultTitle;

        if (!bands.length) {
            indicator.classList.add('is-hidden');
            return;
        }

        const updateIndicator = (target) => {
            const nextTitle = target?.getAttribute('data-band-title') || defaultTitle;
            indicatorValue.textContent = nextTitle;

            bands.forEach((band) => {
                band.classList.toggle('is-current-band', band === target);
            });
        };

        const pickCurrentBand = () => {
            let candidate = null;
            let smallestDistance = Number.POSITIVE_INFINITY;
            const anchor = window.innerHeight * 0.28;

            bands.forEach((band) => {
                const rect = band.getBoundingClientRect();
                const distance = Math.abs(rect.top - anchor);

                if (distance < smallestDistance) {
                    smallestDistance = distance;
                    candidate = band;
                }
            });

            if (!candidate) {
                candidate = bands[bands.length - 1];
            }

            updateIndicator(candidate);
        };

        let ticking = false;
        const requestPick = () => {
            if (ticking) {
                return;
            }

            ticking = true;
            requestAnimationFrame(() => {
                pickCurrentBand();
                ticking = false;
            });
        };

        window.addEventListener('scroll', requestPick, { passive: true });
        window.addEventListener('resize', requestPick);
        pickCurrentBand();
    }

    function initCourseCardNavigation() {
        const cards = document.querySelectorAll('.course-card[data-course-slug]');
        if (!cards.length) {
            return;
        }

        cards.forEach((card) => {
            card.classList.add('course-card--clickable');

            card.addEventListener('click', (event) => {
                if (
                    event.defaultPrevented ||
                    event.target.closest('button') ||
                    event.target.closest('a')
                ) {
                    return;
                }

                const slug = card.getAttribute('data-course-slug');
                if (slug) {
                    window.location.href = `/student/courses/${slug}`;
                }
            });
        });
    }

    function initAddToCartButtons() {
        const buttons = document.querySelectorAll('[data-add-to-cart]');
        if (!buttons.length) {
            return;
        }

        buttons.forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();

                if (button.disabled) {
                    return;
                }

                const courseId = button.getAttribute('data-add-to-cart');
                if (!courseId) {
                    return;
                }

                const form = document.querySelector(`.cart-form[data-course-id="${courseId}"]`);
                if (!form) {
                    console.warn('[Course Index] Không tìm thấy form giỏ hàng cho', courseId);
                    return;
                }

                const originalLabel = button.dataset.originalLabel || button.textContent.trim();
                button.dataset.originalLabel = originalLabel;

                const addingLabel = button.dataset.cartAddingLabel || 'Đang thêm...';
                button.textContent = addingLabel;
                button.disabled = true;
                button.classList.add('is-busy');

                pendingCartButtons.set(form, button);

                const submitted = submitCartForm(form);
                if (!submitted) {
                    pendingCartButtons.delete(form);
                    resetCartButton(button);
                }
            });
        });

        document.addEventListener('cart:request:finished', (event) => {
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
    }

    function submitCartForm(form) {
        if (typeof form.requestSubmit === 'function') {
            form.requestSubmit();
            return true;
        }

        const event = new Event('submit', { bubbles: true, cancelable: true });
        return form.dispatchEvent(event);
    }

    function resetCartButton(button) {
        const original = button.dataset.originalLabel || 'Thêm vào giỏ hàng';
        button.textContent = original;
        button.disabled = false;
        button.classList.remove('is-busy');
        button.classList.remove('course-card__cta--in-cart');
        button.setAttribute('aria-label', original);
    }

    function finalizeCartButton(button) {
        const addedLabel = button.dataset.cartAddedLabel || 'Đã trong giỏ hàng';
        button.textContent = addedLabel;
        button.disabled = true;
        button.classList.remove('is-busy');
        button.classList.add('course-card__cta--in-cart');
        button.setAttribute('aria-label', addedLabel);
    }

    onReady(() => {
        initStickyHeading();
        initDynamicHeadingIndicator();
        initCourseCardNavigation();
        initAddToCartButtons();
    });
})();
