document.addEventListener('DOMContentLoaded', () => {
    setupProgressMeters();
    setupChapterAccordions();
});

function setupProgressMeters() {
    const meters = document.querySelectorAll('.progress-card__meter[data-progress-target]');

    meters.forEach((meter) => {
        const target = Number.parseFloat(meter.dataset.progressTarget || '0');

        if (Number.isNaN(target)) {
            return;
        }

        meter.style.setProperty('--progress', target);

        const valueNode = meter.querySelector('.progress-card__meter-value');
        if (valueNode) {
            valueNode.textContent = `${Math.round(target)}%`;
        }
    });
}

function setupChapterAccordions() {
    const accordions = document.querySelectorAll('[data-progress-accordion]');

    accordions.forEach((accordion) => {
        const chapters = accordion.querySelectorAll('[data-chapter]');

        chapters.forEach((chapter, index) => {
            const toggle = chapter.querySelector('[data-chapter-toggle]');
            const body = chapter.querySelector('[data-chapter-body]');

            if (!toggle || !body) {
                return;
            }

            toggle.addEventListener('click', () => {
                const expanded = toggle.getAttribute('aria-expanded') === 'true';
                setChapterState(chapter, toggle, body, !expanded);
            });

            if (index === 0) {
                setChapterState(chapter, toggle, body, true);
            }
        });
    });
}

function setChapterState(chapter, toggle, body, expanded) {
    toggle.setAttribute('aria-expanded', String(expanded));
    body.hidden = !expanded;
    chapter.classList.toggle('is-open', expanded);
}
