document.addEventListener("DOMContentLoaded", () => {
    const configElement = document.getElementById("teacherProgressConfig");
    const updateRouteTemplate = configElement?.dataset.updateRoute || "";

    const courseSelector = document.getElementById("progressCourseSelector");
    if (courseSelector) {
        courseSelector.addEventListener("change", () => {
            const base =
                courseSelector.dataset.baseUrl || window.location.pathname;
            const url = new URL(base, window.location.origin);
            if (courseSelector.value) {
                url.searchParams.set("course", courseSelector.value);
            } else {
                url.searchParams.delete("course");
            }
            window.location.href = url.toString();
        });
    }

    const statusFilter = document.getElementById("progressStatusFilter");
    const filterForm = document.getElementById("progressFilterForm");
    if (statusFilter && filterForm) {
        statusFilter.addEventListener("change", () => filterForm.submit());
    }

    const updateModalEl = document.getElementById("updateProgressModal");
    if (updateModalEl) {
        updateModalEl.addEventListener("show.bs.modal", (event) => {
            const trigger = event.relatedTarget;
            if (!trigger) {
                return;
            }

            const payload = trigger.getAttribute("data-enrollment");
            if (!payload) {
                return;
            }

            let data;
            try {
                data = JSON.parse(payload);
            } catch (_) {
                data = {};
            }

            const form = document.getElementById("updateProgressForm");
            if (form && data.course_id && data.student_id) {
                form.action = updateRouteTemplate
                    .replace("__COURSE__", data.course_id)
                    .replace("__STUDENT__", data.student_id);
            }

            const studentName = document.getElementById("progressStudentName");
            const progressInput = document.getElementById(
                "progressPercentInput"
            );
            const statusInput = document.getElementById("progressStatusInput");
            const lessonInput = document.getElementById("progressLessonInput");

            if (studentName) {
                studentName.textContent = data.student_name || "Hoc vien";
            }

            if (progressInput) {
                progressInput.value = data.progress ?? 0;
            }

            if (statusInput) {
                statusInput.value = data.status || "ACTIVE";
            }

            if (lessonInput) {
                lessonInput.value = "";
            }
        });
    }
});
