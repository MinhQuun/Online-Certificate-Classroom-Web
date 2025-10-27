document.addEventListener("DOMContentLoaded", () => {
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
});
