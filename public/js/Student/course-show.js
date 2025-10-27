
document.addEventListener('DOMContentLoaded', function () {
    const flags = document.getElementById('courseAccessFlags');
    const isAuthenticated = flags?.dataset.authenticated === '1';
    const isEnrolled = flags?.dataset.enrolled === '1';

    const openAuth = () => {
        const el = document.getElementById('authModal');
        if (el) new bootstrap.Modal(el).show();
    };

    const openEnroll = () => {
        const el = document.getElementById('enrollPromptModal');
        if (el) new bootstrap.Modal(el).show();
    };

    if (!isEnrolled) {
        // Lessons: allow only first lesson in the first module
        const modules = document.querySelectorAll('.course-layout__main .module');
        if (modules.length) {
            const firstModule = modules[0];
            const firstAllowedLesson = firstModule.querySelector('ul.lesson-list a');

            document.querySelectorAll('ul.lesson-list a').forEach(function (a) {
                const allow = (a === firstAllowedLesson);
                if (!allow) {
                    a.classList.add('lesson-link--locked');
                    a.addEventListener('click', function (e) {
                        e.preventDefault();
                        isAuthenticated ? openEnroll() : openAuth();
                    }, { passive: false });
                }
            });

            // Mini tests: allow only first mini test resources in first module
            const firstMiniCard = firstModule.querySelector('.mini-tests__grid .mini-test-card');
            const allowedMiniRes = firstMiniCard ? firstMiniCard.querySelectorAll('.resource-list a') : [];
            const allowedSet = new Set(Array.from(allowedMiniRes));
            document.querySelectorAll('.mini-tests .resource-list a').forEach(function (a) {
                const allow = allowedSet.has(a);
                if (!allow) {
                    a.classList.add('locked-resource');
                    a.addEventListener('click', function (e) {
                        e.preventDefault();
                        isAuthenticated ? openEnroll() : openAuth();
                    }, { passive: false });
                }
            });
        }

        // Final tests resources: lock all if not enrolled
        document.querySelectorAll('.final-tests__grid .resource-list a').forEach(function (a) {
            a.classList.add('locked-resource');
            a.addEventListener('click', function (e) {
                e.preventDefault();
                isAuthenticated ? openEnroll() : openAuth();
            }, { passive: false });
        });
    }

    // Auto-open based on prompt query
    try {
        const params = new URLSearchParams(window.location.search);
        const prompt = params.get('prompt');
        if (prompt === 'auth') openAuth();
        else if (prompt === 'enroll') openEnroll();
    } catch (_) {}
});

