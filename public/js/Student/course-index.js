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

        const getStickyTop = () => {
            const computedTop = window.getComputedStyle(headingBar).getPropertyValue('top');
            const parsedTop = parseFloat(computedTop);
            return Number.isFinite(parsedTop) ? parsedTop : 0;
        };

        let stickPoint = 0;
        let isStuck = headingBar.classList.contains('is-stuck');
        let ticking = false;
        const STICK_HYSTERESIS = 16;
        const RELEASE_HYSTERESIS = 28;

        const recalcStickPoint = () => {
            const rect = sentinel.getBoundingClientRect();
            const scrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
            stickPoint = scrollY + rect.top - getStickyTop();
        };

        const setStuckState = (nextState) => {
            if (nextState === isStuck) {
                return;
            }

            isStuck = nextState;
            headingBar.classList.toggle('is-stuck', isStuck);
            headingBar.dispatchEvent(
                new CustomEvent('stickychange', {
                    detail: { isStuck },
                })
            );
        };

        const evaluateStickiness = () => {
            const scrollY = window.pageYOffset || document.documentElement.scrollTop || 0;

            if (!isStuck && scrollY >= stickPoint + STICK_HYSTERESIS) {
                setStuckState(true);
            } else if (isStuck && scrollY < stickPoint - RELEASE_HYSTERESIS) {
                setStuckState(false);
            }
        };

        const onScroll = () => {
            if (ticking) {
                return;
            }

            ticking = true;
            requestAnimationFrame(() => {
                evaluateStickiness();
                ticking = false;
            });
        };

        recalcStickPoint();
        window.addEventListener('load', recalcStickPoint, { once: true });
        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', () => {
            recalcStickPoint();
            evaluateStickiness();
        });
        evaluateStickiness();
    }

    function initDynamicHeadingIndicator() {
        const headingBar = document.querySelector('[data-sticky-heading]');
        const indicator = document.querySelector('[data-heading-indicator]');
        const indicatorValue = document.querySelector('[data-heading-indicator-text]');
        const headingTitle = headingBar?.querySelector('[data-heading-title]');

        if (!headingBar || !indicator || !indicatorValue) {
            return;
        }

        const bands = Array.from(document.querySelectorAll('.course-band[data-band-title]'));
        const bandNav = document.querySelector('[data-course-band-nav]');
        const bandNavItems = bandNav
            ? Array.from(bandNav.querySelectorAll('[data-band-target]'))
            : [];
        const bandNavList = bandNav?.querySelector('.course-band-nav__items');
        const bandProgressCurrent = bandNav?.querySelector('[data-course-band-progress-current]');
        const bandProgressTotal = bandNav?.querySelector('[data-course-band-progress-total]');
        const bandTotal = Number(bandNav?.getAttribute('data-band-total')) || bands.length;
        const reduceMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
        const getScrollBehavior = () => (reduceMotionQuery.matches ? 'auto' : 'smooth');
        const defaultTitle =
            headingBar.getAttribute('data-heading-default-title') ||
            indicatorValue.textContent.trim() ||
            'Tat ca khoa hoc';

        indicatorValue.textContent = defaultTitle;
        let currentTitle = defaultTitle;
        let isSticky = headingBar.classList.contains('is-stuck');

        if (!bands.length) {
            indicator.classList.add('is-hidden');
            return;
        }

        if (bandProgressTotal) {
            bandProgressTotal.textContent = bandTotal;
        }

        const syncHeadingTitle = () => {
            if (!headingTitle) {
                return;
            }

            headingTitle.textContent = isSticky ? currentTitle : defaultTitle;
        };

        const updateBandProgress = (target) => {
            if (!bandProgressCurrent) {
                return;
            }

            const bandIndex = Number(target?.getAttribute('data-band-index')) || 1;
            bandProgressCurrent.textContent = bandIndex;
        };

        const syncNavState = (target) => {
            if (!bandNavItems.length) {
                updateBandProgress(target);
                return;
            }

            const targetName =
                target?.getAttribute('data-band') || target?.getAttribute('data-band-title') || '';

            bandNavItems.forEach((item) => {
                const matches = item.getAttribute('data-band-target') === targetName;
                item.classList.toggle('is-active', matches);

                if (matches) {
                    item.setAttribute('aria-current', 'true');
                } else {
                    item.removeAttribute('aria-current');
                }
            });

            updateBandProgress(target);
        };

        const scrollToBand = (band) => {
            if (!band) {
                return;
            }

            const behavior = getScrollBehavior();

            if (typeof band.scrollIntoView === 'function') {
                band.scrollIntoView({
                    behavior,
                    block: 'start',
                    inline: 'nearest',
                });
                return;
            }

            const rect = band.getBoundingClientRect();
            const pageOffset = window.pageYOffset || document.documentElement.scrollTop || 0;
            const fallbackOffset = (headingBar.offsetHeight || 0) + 24;
            const targetTop = rect.top + pageOffset - fallbackOffset;

            window.scrollTo({
                top: Math.max(targetTop, 0),
                behavior,
            });
        };

        const syncIndicatorVisibility = () => {
            const shouldShow = isSticky && bands.length > 0;
            indicator.classList.toggle('is-collapsed', !shouldShow);
        };

        const updateIndicator = (target) => {
            const nextTitle = target?.getAttribute('data-band-title') || defaultTitle;
            currentTitle = nextTitle;
            indicatorValue.textContent = nextTitle;
            syncHeadingTitle();

            bands.forEach((band) => {
                band.classList.toggle('is-current-band', band === target);
            });

            syncNavState(target);
        };

        if (bandNavItems.length) {
            bandNavItems.forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();

                    const targetName = button.getAttribute('data-band-target');
                    if (!targetName) {
                        return;
                    }

                    const targetBand = bands.find((band) => {
                        const name = band.getAttribute('data-band') || band.getAttribute('data-band-title');
                        return name === targetName;
                    });

                    if (!targetBand) {
                        return;
                    }

                    updateIndicator(targetBand);
                    scrollToBand(targetBand);
                });
            });
        }

        const updateNavScrollableState = () => {
            if (!bandNavList) {
                return;
            }

            const isOverflowing = bandNavList.scrollWidth > bandNavList.clientWidth + 4;
            bandNavList.classList.toggle('is-scrollable', isOverflowing);
        };

        if (bandNavList) {
            updateNavScrollableState();
            window.addEventListener('resize', updateNavScrollableState);
        }

        const pickCurrentBand = () => {
            let candidate = null;
            let smallestDistance = Number.POSITIVE_INFINITY;
            const stickyBottom = headingBar.getBoundingClientRect().bottom;
            const anchor = isSticky
                ? stickyBottom + 14
                : Math.max(window.innerHeight * 0.3, stickyBottom + 40);

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

        headingBar.addEventListener('stickychange', (event) => {
            isSticky = Boolean(event.detail?.isStuck);
            syncHeadingTitle();
            syncIndicatorVisibility();
            requestPick();
        });

        window.addEventListener('scroll', requestPick, { passive: true });
        window.addEventListener('resize', requestPick);
        syncHeadingTitle();
        syncIndicatorVisibility();
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
                    console.warn('[Course Index] Khong tim thay form gio hang cho', courseId);
                    return;
                }

                const originalLabel = button.dataset.originalLabel || button.textContent.trim();
                button.dataset.originalLabel = originalLabel;

                const addingLabel = button.dataset.cartAddingLabel || 'Dang them...';
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
        const original = button.dataset.originalLabel || 'Them vao gio hang';
        button.textContent = original;
        button.disabled = false;
        button.classList.remove('is-busy');
        button.classList.remove('course-card__cta--in-cart');
        button.setAttribute('aria-label', original);
    }

    function finalizeCartButton(button) {
        const addedLabel = button.dataset.cartAddedLabel || 'Da trong gio hang';
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
