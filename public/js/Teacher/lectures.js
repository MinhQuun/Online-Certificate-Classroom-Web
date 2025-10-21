document.addEventListener("DOMContentLoaded", () => {
    // ---------------- 0) Flash messages ----------------
    (function showFlash() {
        const el = document.getElementById("flash-data");
        if (!el || typeof Swal === "undefined") return;
        const { success, error } = el.dataset;
        if (error) {
            Swal.fire({
                icon: "error",
                title: "Thất bại",
                text: error,
                confirmButtonText: "OK",
            });
        } else if (success) {
            Swal.fire({
                icon: "success",
                title: "Thành công",
                text: success,
                timer: 2000,
                showConfirmButton: false,
            });
        }
    })();

    // ---------------- 1) Course Selector ----------------
    const courseSelector = document.getElementById("courseSelector");
    if (courseSelector) {
        courseSelector.addEventListener("change", () => {
            const base = courseSelector.dataset.baseUrl || window.location.pathname;
            const url = new URL(base, window.location.origin);
            if (courseSelector.value) {
                url.searchParams.set("course", courseSelector.value);
            } else {
                url.searchParams.delete("course");
            }
            window.location.href = url.toString();
        });
    }

    // ---------------- 2) Modal Create: Load course_id and chapter_id ----------------
    const modalCreate = document.getElementById("createLessonModal");
    const createCourseId = document.getElementById("createLessonCourse");
    const createChapterId = document.getElementById("createLessonChapter");
    if (modalCreate && createCourseId && createChapterId) {
        modalCreate.addEventListener("show.bs.modal", (event) => {
            const trigger = event.relatedTarget;
            const courseId = trigger?.getAttribute("data-course") || "";
            const chapterId = trigger?.getAttribute("data-chapter") || "";
            createCourseId.value = courseId;
            createChapterId.value = chapterId;

            const form = modalCreate.querySelector("form");
            if (form) {
                form.reset();
                createCourseId.value = courseId;
                createChapterId.value = chapterId;
            }
        });
    }

    // ---------------- 3) Modal Edit: Load data ----------------
    const modalEdit = document.getElementById("editLessonModal");
    if (modalEdit) {
        modalEdit.addEventListener("show.bs.modal", (evt) => {
            const btn = evt.relatedTarget;
            const form = modalEdit.querySelector("#editLessonForm");
            const lessonData = JSON.parse(btn?.getAttribute("data-lesson") || "{}");
            form.action = lessonData.id ? `/teacher/lectures/${lessonData.id}` : "";

            const editCourseId = document.getElementById("editLessonCourse");
            const editChapterId = document.getElementById("editLessonChapter");
            editCourseId.value = lessonData.course || "";
            editChapterId.value = lessonData.chapter || "";

            const fields = {
                editLessonTitle: "title",
                editLessonOrder: "order",
                editLessonDescription: "description",
                editLessonType: "type",
            };

            Object.keys(fields).forEach((fid) => {
                const val = lessonData[fields[fid]] || "";
                const el = modalEdit.querySelector(`#${fid}`);
                if (el) el.value = val;
            });

            // Reset lỗi cũ
            modalEdit.querySelectorAll(".is-invalid").forEach((el) => el.classList.remove("is-invalid"));
        });
    }

    // ---------------- 4) Modal Create Material: Load lesson_id ----------------
    const modalMaterial = document.getElementById("createMaterialModal");
    if (modalMaterial) {
        modalMaterial.addEventListener("show.bs.modal", (evt) => {
            const btn = evt.relatedTarget;
            const form = modalMaterial.querySelector("#createMaterialForm");
            const lessonId = btn?.getAttribute("data-lesson") || "";
            form.action = lessonId ? `/teacher/lectures/${lessonId}/materials` : "";

            // Reset form
            const formElements = form.querySelectorAll("input, select, textarea");
            formElements.forEach((el) => {
                el.value = "";
                el.classList.remove("is-invalid");
            });
        });
    }

    // ---------------- 5) Form validation & submission ----------------
    function validateLessonForm(form) {
        const requiredFields = form.querySelectorAll("[required]");
        let isValid = true;

        requiredFields.forEach((field) => {
            if (!field.value || !String(field.value).trim()) {
                isValid = false;
                field.classList.add("is-invalid");
            } else field.classList.remove("is-invalid");
        });

        return isValid;
    }

    [document.getElementById("createLessonModal"), document.getElementById("editLessonModal"), document.getElementById("createMaterialModal")]
        .forEach((modal) => {
            if (!modal) return;
            const form = modal.querySelector("form");
            if (!form) return;

            form.addEventListener("submit", function (e) {
                if (!validateLessonForm(form)) {
                    e.preventDefault();
                    if (typeof Swal !== "undefined") {
                        Swal.fire({
                            icon: "error",
                            title: "Vui lòng điền đầy đủ thông tin!",
                            text: "Kiểm tra các trường bắt buộc.",
                            timer: 2200,
                            showConfirmButton: false,
                        });
                    }
                    return false;
                }
            });
        });

    // ---------------- 6) Delete confirmation ----------------
    const isDeleteForm = (form) => {
        const hiddenMethod = form.querySelector("input[name='_method']");
        return hiddenMethod && String(hiddenMethod.value).toLowerCase() === "delete";
    };
    document.querySelectorAll("form").forEach((form) => {
        if (!isDeleteForm(form)) return;
        form.addEventListener("submit", function (event) {
            if (form.dataset.confirmed === "true") return;
            event.preventDefault();

            const submitBtn = form.querySelector("button[type='submit'], .btn-danger, .btn-danger-soft");
            if (submitBtn?.disabled) return;

            if (typeof Swal === "undefined") {
                if (confirm("Xoá bài giảng/tài liệu này?")) {
                    form.dataset.confirmed = "true";
                    form.submit();
                }
                return;
            }
            Swal.fire({
                title: "Bạn chắc chắn?",
                text: "Thao tác này sẽ xoá và không thể hoàn tác.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Xoá",
                cancelButtonText: "Huỷ",
                confirmButtonColor: "#d33",
            }).then((res) => {
                if (res.isConfirmed) {
                    form.dataset.confirmed = "true";
                    form.submit();
                }
            });
        });
    });
});