document.addEventListener("DOMContentLoaded", () => {
    const configElement = document.getElementById("teacherExamsConfig");
    const config = {
        updateRoute: configElement?.dataset.updateRoute || "",
        materialRoute: configElement?.dataset.materialRoute || "",
    };

    const courseSelector = document.getElementById("examCourseSelector");
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

    const createExamModal = document.getElementById("createExamModal");
    if (createExamModal) {
        createExamModal.addEventListener("show.bs.modal", (event) => {
            const trigger = event.relatedTarget;
            const courseInput = document.getElementById("createExamCourse");
            const defaultCourse = courseSelector ? courseSelector.value : null;
            if (courseInput) {
                courseInput.value =
                    trigger?.dataset.course || defaultCourse || "";
            }
            const form = createExamModal.querySelector("form");
            form?.reset();
            if (courseInput) {
                courseInput.value =
                    trigger?.dataset.course || defaultCourse || "";
            }
        });
    }

    const editExamModal = document.getElementById("editExamModal");
    if (editExamModal) {
        editExamModal.addEventListener("show.bs.modal", (event) => {
            const trigger = event.relatedTarget;
            const payload = trigger?.getAttribute("data-exam");
            if (!payload) {
                return;
            }

            let data;
            try {
                data = JSON.parse(payload);
            } catch (_) {
                data = {};
            }

            const form = document.getElementById("editExamForm");
            if (form && data.id) {
                form.action = (config.updateRoute || "").replace(
                    "__ID__",
                    data.id
                );
            }

            document.getElementById("editExamTitle").value = data.title || "";
            document.getElementById("editExamDot").value = data.dotTest || "";
            document.getElementById("editExamTime").value =
                data.time_limit_min ?? "";
            document.getElementById("editExamQuestions").value =
                data.total_questions ?? "";
        });
    }

    const materialModal = document.getElementById("createExamMaterialModal");
    if (materialModal) {
        materialModal.addEventListener("show.bs.modal", (event) => {
            const trigger = event.relatedTarget;
            const examId = trigger?.getAttribute("data-exam");
            const form = document.getElementById("createExamMaterialForm");
            if (form && examId) {
                form.action = (config.materialRoute || "").replace(
                    "__ID__",
                    examId
                );
                form.reset();
            }
        });
    }
});
