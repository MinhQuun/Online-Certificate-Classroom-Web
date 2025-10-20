document.addEventListener("DOMContentLoaded", () => {
    // --- 0) Flash messages (đồng bộ admin-users) ---
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

    // --- 1) Modal Edit: Load data ---
    const modalEdit = document.getElementById("modalEdit");
    if (modalEdit) {
        modalEdit.addEventListener("show.bs.modal", (evt) => {
            const btn = evt.relatedTarget;
            const id = btn?.getAttribute("data-id");
            
            // Fill form data
            modalEdit.querySelector("#e_name").value = btn?.getAttribute("data-name") || "";
            modalEdit.querySelector("#e_category").value = btn?.getAttribute("data-category") || "";
            modalEdit.querySelector("#e_teacher").value = btn?.getAttribute("data-teacher") || "";
            modalEdit.querySelector("#e_fee").value = btn?.getAttribute("data-fee") || "";
            modalEdit.querySelector("#e_duration").value = btn?.getAttribute("data-duration") || "";
            modalEdit.querySelector("#e_start").value = btn?.getAttribute("data-start") || "";
            modalEdit.querySelector("#e_end").value = btn?.getAttribute("data-end") || "";
            modalEdit.querySelector("#e_desc").value = btn?.getAttribute("data-desc") || "";
            modalEdit.querySelector("#e_status").value = btn?.getAttribute("data-status") || "DRAFT";

            // Set form action
            const form = modalEdit.querySelector("#formEdit");
            form.action = `/admin/courses/${id}`;
        });
    }

    // --- 2) Form validation & submission ---
    function validateCourseForm(form) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });

        return isValid;
    }

    // Apply to both modals
    [document.getElementById("modalCreate"), document.getElementById("modalEdit")].forEach(modal => {
        if (modal) {
            const form = modal.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (!validateCourseForm(form)) {
                        e.preventDefault();
                        if (typeof Swal !== "undefined") {
                            Swal.fire({
                                icon: "error",
                                title: "Vui lòng điền đầy đủ thông tin!",
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                        return false;
                    }
                });
            }
        }
    });

    // --- 3) Delete confirmation (đồng bộ admin-users) ---
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
                if (confirm("Xoá khóa học này?")) {
                    form.dataset.confirmed = "true";
                    form.submit();
                }
                return;
            }

            Swal.fire({
                title: "Bạn chắc chắn?",
                text: "Thao tác này sẽ xoá khóa học và không thể hoàn tác.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Xoá",
                cancelButtonText: "Huỷ",
                confirmButtonColor: "#d33",
            }).then((result) => {
                if (result.isConfirmed) {
                    form.dataset.confirmed = "true";
                    form.submit();
                }
            });
        });
    });

    // --- 4) Auto-format currency ---
    document.querySelectorAll('input[name="hocPhi"]').forEach(input => {
        input.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value) {
                this.value = parseInt(value).toLocaleString('vi-VN');
            }
        });
    });

    // --- 5) File preview ---
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function() {
            const fileName = this.files[0]?.name || '';
            const label = this.parentElement.querySelector('label');
            if (fileName) {
                label.textContent += ` - ${fileName}`;
            }
        });
    });
});