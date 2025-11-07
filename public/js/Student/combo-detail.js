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
                    if (entry.isIntersecting) {
                        entry.target.classList.add("is-visible");
                    } else {
                        entry.target.classList.remove("is-visible"); // Remove để reset animation khi scroll lên
                    }
                });
            },
            { threshold: 0.2 } // Trigger khi 20% element vào view
        );

        revealTargets.forEach((el) => observer.observe(el));
    } else {
        revealTargets.forEach((el) => el.classList.add("is-visible")); // Fallback
    }
});
