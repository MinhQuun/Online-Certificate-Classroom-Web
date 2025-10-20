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
    const modal = document.getElementById("modalEdit");
    if (modal) {
        modal.addEventListener("show.bs.modal", (evt) => {
            const btn = evt.relatedTarget;
            const id = btn?.getAttribute("data-id");
            const name = btn?.getAttribute("data-name");
            const email = btn?.getAttribute("data-email");
            const phone = btn?.getAttribute("data-phone");

            modal.querySelector("#e_name").value = name || "";
            modal.querySelector("#e_email").value = email || "";
            modal.querySelector("#e_phone").value = phone || "";

            const form = modal.querySelector("#formEdit");
            form.action = `/admin/users/${id}`;
        });
    }

    // --- 2) Đổi quyền với SweetAlert2 (khóa admin) ---
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

    // --- 3) Xác nhận xoá bằng SweetAlert2 ---
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

