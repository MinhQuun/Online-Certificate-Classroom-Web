document.addEventListener("DOMContentLoaded", () => {
    // --- 0) Flash từ server (success/error) ---
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

    // --- 1) Modal sửa: đổ dữ liệu vào form ---
    const modalEdit = document.getElementById("modalEdit");
    if (modalEdit) {
        modalEdit.addEventListener("show.bs.modal", (evt) => {
            const btn = evt.relatedTarget;
            const id = btn?.getAttribute("data-id");
            const name = btn?.getAttribute("data-name");
            const email = btn?.getAttribute("data-email");
            const phone = btn?.getAttribute("data-phone");
            const roleId = btn?.getAttribute("data-role-id");
            const chuyenMon = btn?.getAttribute("data-chuyen-mon");

            modalEdit.querySelector("#e_name").value = name || "";
            modalEdit.querySelector("#e_email").value = email || "";
            modalEdit.querySelector("#e_phone").value = phone || "";
            modalEdit.querySelector("#e_role").value = roleId || "";
            modalEdit.querySelector('input[name="chuyenMon"]').value = chuyenMon || "";

            const form = modalEdit.querySelector("#formEdit");
            form.action = `/admin/users/${id}`;
            toggleChuyenMonField(modalEdit); // Gọi hàm để hiển thị/ẩn cột chuyên môn
        });
    }

    // --- 2) Modal thêm và sửa: Hiển thị/ẩn cột chuyên môn dựa trên quyền ---
    function toggleChuyenMonField(modal) {
        const roleSelect = modal.querySelector(".form-select[name='MAQUYEN']");
        const chuyenMonField = modal.querySelector(".chuyenMonField");

        if (roleSelect && chuyenMonField) {
            roleSelect.addEventListener("change", () => {
                const selectedRole = roleSelect.value;
                const teacherRole = roleSelect.dataset.teacherRole || 'Q002';
                console.log("Selected Role:", selectedRole, "Teacher Role:", teacherRole);

                if (selectedRole === teacherRole) {
                    chuyenMonField.style.display = 'block';
                } else {
                    chuyenMonField.style.display = 'none';
                    modal.querySelector('input[name="chuyenMon"]').value = '';
                }
            });

            // Kích hoạt lần đầu để kiểm tra giá trị mặc định
            roleSelect.dispatchEvent(new Event('change'));
        } else {
            console.warn("Không tìm thấy roleSelect hoặc chuyenMonField.", { roleSelect, chuyenMonField });
        }
    }

    // Áp dụng cho cả modalCreate và modalEdit
    [document.getElementById("modalCreate"), document.getElementById("modalEdit")].forEach(modal => {
        if (modal) {
            modal.addEventListener("show.bs.modal", () => toggleChuyenMonField(modal));
        }
    });

    // --- 3) Đổi quyền với SweetAlert2 (khóa admin) ---
    document.querySelectorAll(".role-cell form").forEach((form) => {
        const wrap = form.querySelector(".role-wrap");
        const select = form.querySelector(".role-select");
        if (!select) return;

        const initial = select.value;
        const lock = select.dataset.lock; // 'admin' hoặc ''

        select.addEventListener("change", () => {
            if (lock === "admin" && select.value !== initial) {
                if (typeof Swal !== "undefined") {
                    Swal.fire({
                        icon: "error",
                        title: "Không được phép",
                        text: "Bạn không thể thay đổi quyền của tài khoản Admin.",
                        confirmButtonText: "OK",
                    });
                }

                select.value = initial;
                wrap?.classList.remove("show-save");
                return;
            }

            if (select.value !== initial) {
                wrap?.classList.add("show-save");
                if (typeof Swal !== "undefined") {
                    Swal.fire({
                        icon: "info",
                        title: "Quyền đã thay đổi",
                        text: "Nhấn 'Lưu' để cập nhật.",
                        confirmButtonText: "OK",
                    });
                }
            } else {
                wrap?.classList.remove("show-save");
            }
        });
    });

    // --- 4) Xác nhận xoá bằng SweetAlert2 ---
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
                if (confirm("Xoá người dùng này?")) {
                    form.dataset.confirmed = "true";
                    form.submit();
                }
                return;
            }

            Swal.fire({
                title: "Bạn chắc chắn?",
                text: "Thao tác này sẽ xoá người dùng và không thể hoàn tác.",
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
});