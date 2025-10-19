document.addEventListener('DOMContentLoaded', () => {
    const banner = document.querySelector('[data-hero-banner]');
    if (!banner) return;

    const slides = banner.querySelectorAll('.hero-banner__slide');
    const dots = banner.querySelectorAll('[data-hero-banner-dot]');

    if (slides.length <= 1) {
        dots.forEach((dot) => dot.remove());
        return;
    }

    let index = 0;
    let timer = null;
    const intervalMs = 6000;

    const setActive = (nextIndex) => {
        slides.forEach((slide, idx) => {
            slide.classList.toggle('is-active', idx === nextIndex);
        });
        dots.forEach((dot, idx) => {
            const isActive = idx === nextIndex;
            dot.classList.toggle('is-active', isActive);
            dot.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
        index = nextIndex;
    };

    const go = (nextIndex) => {
        const normalized = (nextIndex + slides.length) % slides.length;
        setActive(normalized);
    };

    const startTimer = () => {
        timer = window.setInterval(() => go(index + 1), intervalMs);
    };

    const stopTimer = () => {
        if (timer) {
            window.clearInterval(timer);
            timer = null;
        }
    };

    dots.forEach((dot, dotIndex) => {
        dot.addEventListener('click', () => {
            go(dotIndex);
            stopTimer();
            startTimer();
        });
    });

    banner.addEventListener('mouseenter', stopTimer);
    banner.addEventListener('mouseleave', () => {
        if (!timer) startTimer();
    });

    startTimer();
});
