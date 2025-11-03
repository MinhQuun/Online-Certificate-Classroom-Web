document.addEventListener("DOMContentLoaded", () => {
    const hasSweetAlert = typeof Swal !== "undefined";

    (function showFlash() {
        const el = document.getElementById("flash-data");
        if (!el || !hasSweetAlert) return;

        const { success, error, info, warning } = el.dataset;

        if (error) {
            Swal.fire({
                icon: "error",
                title: "Thất bại",
                text: error,
                confirmButtonText: "Đóng",
            });
            return;
        }

        if (warning) {
            Swal.fire({
                icon: "warning",
                title: "Cảnh báo",
                text: warning,
                confirmButtonText: "Đã hiểu",
            });
            return;
        }

        if (info) {
            Swal.fire({
                icon: "info",
                title: "Thông tin",
                text: info,
                confirmButtonText: "Đóng",
            });
            return;
        }

        if (success) {
            Swal.fire({
                icon: "success",
                title: "Thành công",
                text: success,
                timer: 2000,
                showConfirmButton: false,
            });
        }
    })();

    if (globalThis.AdminSlug && typeof globalThis.AdminSlug.init === "function") {
        globalThis.AdminSlug.init();
    }

    const editModal = document.getElementById("modalEdit");
    if (editModal) {
        editModal.addEventListener("show.bs.modal", (evt) => {
            const trigger = evt.relatedTarget;
            if (!trigger) return;

            const { id, name, slug, icon, action } = trigger.dataset;
            const form = editModal.querySelector("#formEdit");
            const nameField = editModal.querySelector("#e_name");
            const slugField = editModal.querySelector("#e_slug");
            const iconField = editModal.querySelector("#e_icon");

            if (!form || !nameField || !slugField || !iconField) {
                return;
            }

            form.action = action || `/admin/categories/${id}`;
            nameField.value = name || "";
            slugField.value = slug || "";
            iconField.value = icon || "";

            slugField.dataset.manual =
                slugField.value.trim() !== "" ? "true" : "false";

            if (form.__slugAutoUpdate && slugField.dataset.manual === "false") {
                form.__slugAutoUpdate();
            }
        });
    }

    document.querySelectorAll("form[data-confirm='delete']").forEach((form) => {
        form.addEventListener("submit", (event) => {
            if (form.dataset.confirmed === "true") {
                return;
            }

            event.preventDefault();

            const hasCourses = form.dataset.hasCourses === "true";
            const courseCount = Number(form.dataset.courseCount || 0);

            if (!hasSweetAlert) {
                if (hasCourses) {
                    alert(
                        "Không thể xóa danh mục khi vẫn còn khóa học gắn kèm."
                    );
                    return;
                }

                if (confirm("Bạn có chắc chắn muốn xóa danh mục này?")) {
                    form.dataset.confirmed = "true";
                    form.submit();
                }

                return;
            }

            if (hasCourses) {
                const message =
                    courseCount > 0
                        ? `Danh mục này đang có ${courseCount} khóa học. Xóa hoặc chuyển khóa học trước khi xóa danh mục.`
                        : "Danh mục này đang có khóa học gắn kèm.";

                Swal.fire({
                    icon: "error",
                    title: "Không thể xóa",
                    text: message,
                    confirmButtonText: "Đóng",
                });
                return;
            }

            Swal.fire({
                title: "Xác nhận xóa?",
                text: "Hành động này sẽ xóa danh mục khỏi hệ thống.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Xóa",
                cancelButtonText: "Hủy",
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
