"use strict";

(function () {
    function parseJSON(value, fallback = {}) {
        try {
            return JSON.parse(value ?? "");
        } catch (error) {
            console.error("Failed to parse JSON payload:", error);
            return fallback;
        }
    }

    function formatTypeLabel(type) {
        switch (type) {
            case "PERCENT_DISCOUNT":
                return "Giảm giá (%)";
            case "FIXED_DISCOUNT":
                return "Giảm giá (VND)";
            default:
                return "Giảm giá";
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        const datasetEl = document.getElementById("promotion-form-dataset");
        const dataset = datasetEl ? parseJSON(datasetEl.textContent, {}) : {};
        const updateUrlTemplate = dataset.updateUrlTemplate || "";

        function initFormBehaviour(form) {
            if (!form) return;

            const typeSelect = form.querySelector("[data-promotion-type]");
            const valueWrapper = form.querySelector("[data-value-wrapper]");
            const valueInput = form.querySelector('input[name="giaTriUuDai"]');
            const valueLabel = form.querySelector("[data-value-label]");
            const limitInput = form.querySelector('input[name="soLuongGioiHan"]');

            function syncValueField() {
                if (!typeSelect || !valueWrapper || !valueInput) {
                    return;
                }

                const type = String(typeSelect.value || "");
                if (valueLabel) {
                    valueLabel.textContent = formatTypeLabel(type) + ":";
                }

                if (type === "PERCENT_DISCOUNT") {
                    valueWrapper.classList.remove("d-none");
                    valueInput.required = true;
                    valueInput.min = "1";
                    valueInput.max = "100";
                    valueInput.step = "1";
                } else if (type === "FIXED_DISCOUNT") {
                    valueWrapper.classList.remove("d-none");
                    valueInput.required = true;
                    valueInput.min = "0";
                    valueInput.removeAttribute("max");
                    valueInput.step = "0.01";
                } else {
                    valueWrapper.classList.add("d-none");
                    valueInput.required = false;
                    valueInput.value = "0";
                }
            }

            typeSelect?.addEventListener("change", syncValueField);
            syncValueField();

            if (limitInput) {
                form.addEventListener("submit", () => {
                    if (!limitInput.value) {
                        limitInput.value = "";
                    }
                });
            }
        }

        document
            .querySelectorAll("form[data-promotion-form]")
            .forEach((form) => initFormBehaviour(form));

        const editModal = document.getElementById("promotionEditModal");
        if (editModal) {
            editModal.addEventListener("show.bs.modal", (event) => {
                const trigger = event.relatedTarget;
                if (!trigger) {
                    return;
                }

                const payload = parseJSON(
                    trigger.getAttribute("data-promotion"),
                    {}
                );
                const form = editModal.querySelector(
                    'form[data-promotion-form="edit"]'
                );

                if (!form || !payload.id) {
                    return;
                }

                if (updateUrlTemplate.includes("__ID__")) {
                    form.action = updateUrlTemplate.replace(
                        "__ID__",
                        String(payload.id)
                    );
                } else if (payload.update_url) {
                    form.action = payload.update_url;
                }

                const nameInput = form.querySelector('input[name="tenKM"]');
                if (nameInput) {
                    nameInput.value = payload.name || "";
                }

                const descInput = form.querySelector('textarea[name="moTa"]');
                if (descInput) {
                    descInput.value = payload.description || "";
                }

                const targetSelect = form.querySelector(
                    'select[name="apDungCho"]'
                );
                if (targetSelect) {
                    targetSelect.value = payload.target || "";
                }

                const typeSelect = form.querySelector(
                    'select[name="loaiUuDai"]'
                );
                if (typeSelect) {
                    typeSelect.value = payload.type || "";
                    typeSelect.dispatchEvent(new Event("change"));
                }

                const valueInput = form.querySelector(
                    'input[name="giaTriUuDai"]'
                );
                if (valueInput && payload.type !== "GIFT") {
                    valueInput.value =
                        payload.value !== undefined && payload.value !== null
                            ? payload.value
                            : "";
                }

                const startInput = form.querySelector(
                    'input[name="ngayBatDau"]'
                );
                if (startInput) {
                    startInput.value = payload.start || "";
                }

                const endInput = form.querySelector(
                    'input[name="ngayKetThuc"]'
                );
                if (endInput) {
                    endInput.value = payload.end || "";
                }

                const limitInput = form.querySelector(
                    'input[name="soLuongGioiHan"]'
                );
                if (limitInput) {
                    limitInput.value =
                        payload.limit !== undefined && payload.limit !== null
                            ? payload.limit
                            : "";
                }

                const statusSelect = form.querySelector(
                    'select[name="trangThai"]'
                );
                if (statusSelect) {
                    statusSelect.value = payload.status || "ACTIVE";
                }
            });
        }

        document
            .querySelectorAll("form[data-confirm-delete]")
            .forEach((form) => {
                form.addEventListener("submit", (event) => {
                    if (form.dataset.confirmed === "true") {
                        return;
                    }

                    event.preventDefault();

                    const submitAction = () => {
                        form.dataset.confirmed = "true";
                        form.submit();
                    };

                    if (typeof Swal === "undefined") {
                        if (confirm("Xóa khuyến mãi này?")) {
                            submitAction();
                        }

                        return;
                    }

                    Swal.fire({
                        title: "Bạn chắc chắn?",
                        text: "Khuyến mãi sẽ bị xóa vĩnh viễn.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Xóa",
                        cancelButtonText: "Huỷ",
                        confirmButtonColor: "#d64545",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitAction();
                        }
                    });
                });
            });
    });
})();
