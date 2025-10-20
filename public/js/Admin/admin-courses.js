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
            const form = modalEdit.querySelector("#formEdit");
            const id = btn?.getAttribute("data-id");

            // Đặt action cho form
            if (id) {
                form.action = `/admin/courses/${id}`;
            } else {
                form.action = ""; // Đặt mặc định nếu không có id
            }

            // Điền dữ liệu vào form
            const fields = {
                "e_name": "data-name",
                "e_category": "data-category",
                "e_teacher": "data-teacher",
                "e_fee": "data-fee",
                "e_duration": "data-duration",
                "e_start": "data-start",
                "e_end": "data-end",
                "e_desc": "data-desc",
                "e_status": "data-status"
            };

            Object.keys(fields).forEach(id => {
                const value = btn?.getAttribute(fields[id]) || "";
                const element = modalEdit.querySelector(`#${id}`);
                if (element) {
                    if (element.tagName === "SELECT") {
                        element.value = value || "DRAFT"; // Đặt mặc định là DRAFT nếu không có giá trị
                    } else {
                        element.value = value;
                    }
                }
            });

            // Xóa thông báo lỗi cũ (nếu có)
            modalEdit.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
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

        // Kiểm tra trạng thái hợp lệ
        const statusField = form.querySelector('#e_status');
        if (statusField && !["DRAFT", "PUBLISHED", "ARCHIVED"].includes(statusField.value)) {
            isValid = false;
            statusField.classList.add('is-invalid');
        }

        return isValid;
    }

    // Áp dụng cho cả hai modal
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
                                text: "Kiểm tra các trường bắt buộc và trạng thái hợp lệ.",
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

    // --- 4) Format hocPhi ---
    document.querySelectorAll('input[name="hocPhi"]').forEach(input => {
        // Lưu giá trị gốc (không định dạng)
        input.dataset.rawValue = input.value || '';

        // Xử lý khi người dùng nhập
        input.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            if (value) {
                this.dataset.rawValue = value;
                this.value = parseInt(value).toLocaleString('vi-VN');
            } else {
                this.dataset.rawValue = '';
                this.value = '';
            }
        });

        // Định dạng lại khi mất focus
        input.addEventListener('blur', function() {
            let value = this.dataset.rawValue;
            if (value) {
                this.value = parseInt(value).toLocaleString('vi-VN');
            } else {
                this.value = '';
            }
        });

        // Đảm bảo gửi giá trị gốc khi submit
        input.closest('form').addEventListener('submit', function(e) {
            let inputField = input;
            let rawValue = inputField.dataset.rawValue || '';
            if (rawValue) {
                inputField.value = rawValue; // Gửi giá trị gốc (không dấu phẩy)
            } else {
                inputField.value = ''; // Nếu không có giá trị, để trống
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