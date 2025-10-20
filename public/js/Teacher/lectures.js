document.addEventListener("DOMContentLoaded", () => {
    const configElement = document.getElementById("teacherLecturesConfig");
    const config = {
        courseId: null,
        updateRoute: "",
        materialRoute: "",
        presets: {},
    };

    if (configElement) {
        config.courseId = configElement.dataset.courseId || null;
        config.updateRoute = configElement.dataset.updateRoute || "";
        config.materialRoute = configElement.dataset.materialRoute || "";
        const rawPresets = configElement.dataset.presets || "";
        if (rawPresets) {
            try {
                config.presets = JSON.parse(atob(rawPresets));
            } catch (error) {
                config.presets = {};
            }
        }
    }

    const courseSelector = document.getElementById("courseSelector");
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

    const createLessonModalEl = document.getElementById("createLessonModal");
    const createLessonCourse = document.getElementById("createLessonCourse");
    const createLessonChapter = document.getElementById("createLessonChapter");
    const createResourcePreset = document.getElementById(
        "createResourcePreset"
    );
    const createResourceMime = document.getElementById("createResourceMime");

    if (createLessonModalEl) {
        createLessonModalEl.addEventListener("show.bs.modal", (event) => {
            const trigger = event.relatedTarget;
            const dataset = trigger ? trigger.dataset : {};
            const defaultCourse = config.courseId || "";

            createLessonCourse.value = dataset.course || defaultCourse || "";
            createLessonChapter.value = dataset.chapter || "";

            const form = createLessonModalEl.querySelector("form");
            if (form) {
                form.reset();
                createLessonCourse.value =
                    dataset.course || defaultCourse || "";
                createLessonChapter.value = dataset.chapter || "";
            }
        });
    }

    if (createResourcePreset && createResourceMime) {
        createResourcePreset.addEventListener("change", (event) => {
            const option = event.target.selectedOptions[0];
            const mime = option ? option.getAttribute("data-mime") : "";
            createResourceMime.value = mime || "";
        });
    }

    const editLessonModalEl = document.getElementById("editLessonModal");
    if (editLessonModalEl) {
        editLessonModalEl.addEventListener("show.bs.modal", (event) => {
            const trigger = event.relatedTarget;
            if (!trigger) {
                return;
            }
            const payload = trigger.getAttribute("data-lesson");
            if (!payload) {
                return;
            }

            let data;
            try {
                data = JSON.parse(payload);
            } catch (_) {
                data = {};
            }

            const form = document.getElementById("editLessonForm");
            if (form && data.id) {
                const actionTemplate = config.updateRoute || "";
                form.action = actionTemplate.replace("__ID__", data.id);
            }

            const titleInput = document.getElementById("editLessonTitle");
            const orderInput = document.getElementById("editLessonOrder");
            const descInput = document.getElementById("editLessonDescription");
            const typeSelect = document.getElementById("editLessonType");

            if (titleInput) titleInput.value = data.title || "";
            if (orderInput) orderInput.value = data.order || "";
            if (descInput) descInput.value = data.description || "";
            if (typeSelect && data.type) typeSelect.value = data.type;
        });
    }

    const createMaterialModalEl = document.getElementById(
        "createMaterialModal"
    );
    if (createMaterialModalEl) {
        createMaterialModalEl.addEventListener("show.bs.modal", (event) => {
            const trigger = event.relatedTarget;
            if (!trigger) {
                return;
            }

            const lessonId = trigger.getAttribute("data-lesson");
            const form = document.getElementById("createMaterialForm");

            if (form && lessonId) {
                const template = config.materialRoute || "";
                form.action = template.replace("__ID__", lessonId);
                form.reset();
            }
        });
    }
});
