"use strict";

document.addEventListener("DOMContentLoaded", () => {
    const detailRoot = document.querySelector("[data-combo-detail]");

    if (detailRoot) {
        document.body.classList.add("combo-animate-ready");
        window.requestAnimationFrame(() => {
            detailRoot.classList.add("is-animated");
        });
    }

    const revealTargets = document.querySelectorAll("[data-reveal-on-scroll]");

    if (!revealTargets.length) {
        return;
    }

    if ("IntersectionObserver" in window) {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) {
                        return;
                    }
                    entry.target.classList.add("is-visible");
                    observer.unobserve(entry.target);
                });
            },
            { threshold: 0.2 }
        );

        revealTargets.forEach((el) => observer.observe(el));
    } else {
        revealTargets.forEach((el) => el.classList.add("is-visible"));
    }
});
