/**
 * Home Page Scroll Animations
 * Adds smooth reveal animations when scrolling through the page
 */

document.addEventListener('DOMContentLoaded', function() {
    // Add ready class to body to enable animations
    document.body.classList.add('home-animate-ready');

    // Create intersection observer for scroll animations
    const observerOptions = {
        threshold: 0.15,
        rootMargin: '0px 0px -50px 0px' // Trigger slightly before element enters viewport
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const shouldRepeat = entry.target.hasAttribute('data-reveal-repeat');

            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');

                if (!shouldRepeat) {
                    observer.unobserve(entry.target);
                }
            } else if (shouldRepeat) {
                entry.target.classList.remove('is-visible');
            }
        });
    }, observerOptions);

    // Observe all elements with animation attributes
    const animatedElements = document.querySelectorAll(
        '[data-reveal-on-scroll], [data-reveal-from-left], [data-reveal-from-right], [data-reveal-scale]'
    );

    animatedElements.forEach(element => {
        observer.observe(element);
    });
});
