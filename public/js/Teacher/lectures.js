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

    // ---------------- 4) Modal Create Material: Load lesson_id and handle AJAX submission ----------------
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

            // Populate type select with resourcePresets keys
            const typeSelect = form.querySelector("select[name='type']");
            if (typeSelect) {
                typeSelect.innerHTML = ''; // Clear existing options
                Object.keys({
                    'video/mp4': 0,
                    'application/pdf': 0,
                    'application/vnd.ms-powerpoint': 0,
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation': 0,
                    'application/zip': 0,
                    'audio/mpeg': 0,
                    'text/html': 0
                }).forEach(mime => {
                    const option = document.createElement('option');
                    option.value = mime;
                    option.textContent = mime === 'text/html' ? 'Liên kết' : mime.split('/')[1].toUpperCase();
                    typeSelect.appendChild(option);
                });
            }

            // Xử lý file input
            const fileInput = form.querySelector("input[name='file']");
            const urlInput = form.querySelector("input[name='url']");
            const sizeInput = form.querySelector("input[name='size']");

            fileInput.addEventListener("change", () => {
                const file = fileInput.files[0];
                if (file) {
                    urlInput.disabled = true;
                    sizeInput.value = formatFileSize(file.size);
                    if (typeSelect) typeSelect.value = guessFileType(file.type);
                } else {
                    urlInput.disabled = false;
                    sizeInput.value = "";
                    if (typeSelect) typeSelect.value = "";
                }
            });

            urlInput.addEventListener("input", () => {
                if (urlInput.value) {
                    fileInput.disabled = true;
                    if (typeSelect) typeSelect.value = 'text/html';
                } else {
                    fileInput.disabled = false;
                    if (typeSelect) typeSelect.value = "";
                }
            });
        });

        // AJAX submission for material form
// Thay thế phần AJAX submission trong modalMaterial
        const materialForm = document.getElementById("createMaterialForm");
        if (materialForm) {
            materialForm.addEventListener("submit", function(e) {
                e.preventDefault();

                if (!validateLessonForm(this)) {
                    Swal.fire({
                        icon: "error",
                        title: "Vui lòng điền đầy đủ thông tin!",
                        text: "Kiểm tra các trường bắt buộc hoặc cung cấp file/URL.",
                        timer: 2200,
                        showConfirmButton: false,
                    });
                    return;
                }

                const formData = new FormData(this);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                console.log("Form data sent:", Object.fromEntries(formData));

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => {
                    console.log("Response status:", response.status);
                    return response.text().then(text => {
                        console.log("Raw response text:", text);
                        if (!response.ok) {
                            if (response.status === 422) {
                                try {
                                    const errors = JSON.parse(text);
                                    let errorMessage = 'Có lỗi xảy ra: ';
                                    for (let field in errors.errors) {
                                        errorMessage += errors.errors[field][0] + ' ';
                                    }
                                    throw new Error(errorMessage);
                                } catch (e) {
                                    throw new Error("Không thể parse lỗi validation: " + text.substring(0, 100));
                                }
                            } else if (response.status === 419) {
                                throw new Error("CSRF token không hợp lệ. Vui lòng làm mới trang.");
                            }
                            throw new Error(`Lỗi server: ${response.status} - ${text.substring(0, 100)}...`);
                        }
                        return JSON.parse(text);
                    });
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false,
                        }).then(() => {
                            modalMaterial.querySelector('.btn-close').click();
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: data.error || 'Upload thất bại',
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi kết nối',
                        text: error.message,
                    });
                });
            });
        }
    }

    // ---------------- 5) Form validation & submission (cho các modal khác) ----------------
    function validateLessonForm(form) {
        const requiredFields = form.querySelectorAll("[required]");
        let isValid = true;

        requiredFields.forEach((field) => {
            if (!field.value || !String(field.value).trim()) {
                isValid = false;
                field.classList.add("is-invalid");
            } else {
                field.classList.remove("is-invalid");
            }
        });

        const fileInput = form.querySelector("input[name='file']");
        const urlInput = form.querySelector("input[name='url']");
        if (fileInput && urlInput && !fileInput.files[0] && !urlInput.value) {
            isValid = false;
            urlInput.classList.add("is-invalid");
        }

        return isValid;
    }

    [document.getElementById("createLessonModal"), document.getElementById("editLessonModal")]
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
                            text: "Kiểm tra các trường bắt buộc hoặc cung cấp file/URL.",
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

    // ---------------- 7) Helper functions ----------------
    function formatFileSize(bytes) {
        if (bytes >= 1073741824) {
            return (bytes / 1073741824).toFixed(2) + " GB";
        } else if (bytes >= 1048576) {
            return (bytes / 1048576).toFixed(2) + " MB";
        } else if (bytes >= 1024) {
            return (bytes / 1024).toFixed(2) + " KB";
        }
        return bytes + " B";
    }

    function guessFileType(mimeType) {
        const typeMap = {
            'video/mp4': 'video/mp4',
            'application/pdf': 'application/pdf',
            'application/zip': 'application/zip',
            'application/vnd.ms-powerpoint': 'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation': 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'audio/mpeg': 'audio/mpeg',
            'text/html': 'text/html',
        };
        return typeMap[mimeType] || 'application/octet-stream';
    }
});