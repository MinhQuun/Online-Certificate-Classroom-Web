"use strict";

const PROMOTION_TYPE_PERCENT = "PERCENT_DISCOUNT";
const PROMOTION_TYPE_FIXED = "FIXED_DISCOUNT";
const PROMOTION_TYPE_GIFT = "GIFT";

document.addEventListener("DOMContentLoaded", () => {
    initFlashMessage();
    initSlugHelper();

    const { map: promotionMap } = loadPromotionDataset();

    const createModal = document.getElementById("modalCreate");
    const createForm = createModal ? createModal.querySelector("form") : null;
    if (createForm) {
        bindCurrencyInputs(createForm);
        bindValidation(createForm);
        bindFileInputs(createForm);
        const refreshCreatePromotion = bindPromotionControls(createForm, promotionMap);
        refreshCreatePromotion();
        wireDateDuration({
            startEl: createForm.querySelector("#c_start"),
            endEl: createForm.querySelector("#c_end"),
            durationEl: createForm.querySelector("#c_duration"),
        });
    }

    const editModal = document.getElementById("modalEdit");
    const editForm = editModal ? editModal.querySelector("#formEdit") : null;
    if (editModal && editForm) {
        bindCurrencyInputs(editForm);
        bindValidation(editForm);
        bindFileInputs(editForm);
        const refreshEditPromotion = bindPromotionControls(editForm, promotionMap);

        editModal.addEventListener("show.bs.modal", (event) => {
            const trigger = event.relatedTarget;
            if (!trigger) {
                editForm.reset();
                return;
            }

            const action = trigger.getAttribute("data-action");
            if (action) {
                editForm.setAttribute("action", action);
            }

            const fieldMap = {
                e_name: "data-name",
                e_slug: "data-slug",
                e_category: "data-category",
                e_teacher: "data-teacher",
                e_fee: "data-fee",
                e_duration: "data-duration",
                e_start: "data-start",
                e_end: "data-end",
                e_desc: "data-desc",
                e_status: "data-status",
                e_promotion: "data-promotion-id",
                e_promotion_price: "data-promotion-price",
            };

            Object.entries(fieldMap).forEach(([id, attr]) => {
                const el = editForm.querySelector(`#${id}`);
                if (!el) return;

                let value = trigger.getAttribute(attr) ?? "";
                if (value === "null" || value === "undefined") {
                    value = "";
                }

                if (el.tagName === "SELECT") {
                    if (id === "e_status") {
                        el.value = value || "DRAFT";
                    } else if (value !== "") {
                        el.value = value;
                    } else {
                        el.value = "";
                    }
                } else if (el.tagName === "TEXTAREA") {
                    el.value = value || "";
                } else if (id === "e_fee") {
                    const raw = sanitizeNumber(value);
                    el.dataset.rawValue = raw;
                    el.value = raw ? formatCurrency(raw) : "";
                } else if (id === "e_duration") {
                    el.value = value ? Number(value) : "";
                } else if (id === "e_promotion_price") {
                    el.value = value ? Number(value) : "";
                } else {
                    el.value = value || "";
                }
            });

            editForm
                .querySelectorAll(".is-invalid")
                .forEach((input) => input.classList.remove("is-invalid"));

            const previewImg = editForm.querySelector("[data-current-image]");
            const previewEmpty = editForm.querySelector("[data-current-image-empty]");
            if (previewImg && previewEmpty) {
                const imageUrl =
                    trigger.getAttribute("data-image-url") ||
                    trigger.getAttribute("data-image") ||
                    "";
                if (imageUrl) {
                    previewImg.src = imageUrl;
                    previewImg.alt = trigger.getAttribute("data-name") || "Hình ảnh khóa học";
                    previewImg.classList.remove("d-none");
                    previewEmpty.classList.add("d-none");
                } else {
                    previewImg.src = "";
                    previewImg.classList.add("d-none");
                    previewEmpty.classList.remove("d-none");
                }
            }

            refreshEditPromotion();

            wireDateDuration({
                startEl: editForm.querySelector("#e_start"),
                endEl: editForm.querySelector("#e_end"),
                durationEl: editForm.querySelector("#e_duration"),
            });
        });
    }

    initDeleteConfirmation();
});

function initFlashMessage() {
    const el = document.getElementById("flash-data");
    if (!el || typeof Swal === "undefined") {
        return;
    }

    const { success = "", error = "" } = el.dataset;
    if (error) {
        Swal.fire({
            icon: "error",
            title: "Thất bại",
            text: error,
            confirmButtonText: "Đóng",
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
}

function initSlugHelper() {
    if (globalThis.AdminSlug && typeof globalThis.AdminSlug.init === "function") {
        globalThis.AdminSlug.init();
    }
}

const numberFormatter = new Intl.NumberFormat("vi-VN", {
    maximumFractionDigits: 0,
});

function formatCurrency(value) {
    const sanitized = sanitizeNumber(value);
    if (!sanitized) return "";
    return numberFormatter.format(Number(sanitized));
}

function sanitizeNumber(value) {
    return String(value ?? "").replace(/\D/g, "");
}

function normalizePromotionType(type) {
    const value = String(type || "").toUpperCase();

    if (value.includes("PERCENT")) {
        return PROMOTION_TYPE_PERCENT;
    }

    if (value.includes("FIXED")) {
        return PROMOTION_TYPE_FIXED;
    }

    return PROMOTION_TYPE_GIFT;
}

function loadPromotionDataset() {
    const datasetEl = document.getElementById("course-promotion-dataset");
    if (!datasetEl) {
        return { list: [], map: new Map() };
    }

    try {
        const parsed = JSON.parse(datasetEl.textContent || "{}");
        const list = Array.isArray(parsed.promotions) ? parsed.promotions : [];
        const map = new Map(
            list
                .filter((item) => item && item.id !== undefined && item.id !== null)
                .map((item) => [
                    Number(item.id),
                    {
                        ...item,
                        value:
                            Number(
                                item.value !== undefined ? item.value : item.giaTriUuDai
                            ) || 0,
                        type: normalizePromotionType(item.type),
                    },
                ])
        );
        return { list, map };
    } catch (error) {
        console.error("Không thể phân tích dữ liệu khuyến mãi của khóa học:", error);
        return { list: [], map: new Map() };
    }
}

function bindPromotionControls(form, promotionMap) {
    const select = form.querySelector("[data-promotion-select]");
    const wrapper = form.querySelector("[data-promotion-price-wrapper]");
    const priceInput = form.querySelector("[data-promotion-price-input]");
    const help = wrapper ? wrapper.querySelector("[data-promotion-help]") : null;
    const tuitionInput = form.querySelector('input[name="hocPhi"]');

    if (!select || !wrapper || !priceInput) {
        return () => {};
    }

    const defaultHelp = help ? help.textContent : "";

    const parseBaseFee = () => {
        if (!tuitionInput) {
            return 0;
        }

        const rawValue =
            tuitionInput.dataset.rawValue ?? tuitionInput.value ?? "";

        return Number(sanitizeNumber(rawValue));
    };

    const resetUi = () => {
        priceInput.removeAttribute("disabled");
        priceInput.value = "";
        priceInput.placeholder = "Giá sau ưu đãi";
        if (help) {
            help.textContent = defaultHelp;
        }
    };

    const update = () => {
        const hasPromotion = select.value !== "";
        wrapper.classList.toggle("show", hasPromotion);

        if (!hasPromotion) {
            resetUi();
            return;
        }

        const promotion = promotionMap.get(Number(select.value));
        if (!promotion) {
            resetUi();
            return;
        }

        const baseFee = parseBaseFee();
        const type = normalizePromotionType(promotion.type);
        const value = Number(promotion.value) || 0;
        let suggested = null;
        let message = defaultHelp;

        if ((type === PROMOTION_TYPE_PERCENT || type === PROMOTION_TYPE_FIXED) && baseFee <= 0) {
            message = "Nhập học phí gốc trước khi áp dụng khuyến mãi.";
        } else if (type === PROMOTION_TYPE_PERCENT) {
            const percent = Math.min(Math.max(value, 0), 100);
            const discount = Math.round(baseFee * (percent / 100));
            suggested = Math.max(0, baseFee - discount);
            message = `Giảm ${percent}% - Giá sau ưu đãi: ${formatCurrency(suggested)} đ.`;
        } else if (type === PROMOTION_TYPE_FIXED) {
            const discount = Math.max(0, value);
            suggested = Math.max(0, baseFee - discount);
            message = `Giảm ${formatCurrency(discount)} đ - Giá sau ưu đãi: ${formatCurrency(suggested)} đ.`;
        } else {
            message = "Khuyến mãi quà tặng - Giá ưu đãi giữ nguyên.";
        }

        if (type === PROMOTION_TYPE_GIFT) {
            priceInput.value = "";
            priceInput.setAttribute("disabled", "disabled");
            priceInput.placeholder = "Áp dụng quà tặng";
        } else {
            priceInput.removeAttribute("disabled");
            if (suggested !== null) {
                priceInput.value = suggested;
                priceInput.placeholder = `Đề xuất: ${formatCurrency(suggested)} đ`;
            } else {
                priceInput.value = "";
                priceInput.placeholder = "Giá sau ưu đãi";
            }
        }

        if (help) {
            help.textContent = message;
        }
    };

    select.addEventListener("change", update);
    if (tuitionInput) {
        tuitionInput.addEventListener("input", () => {
            setTimeout(update, 0);
        });
        tuitionInput.addEventListener("blur", () => {
            setTimeout(update, 0);
        });
    }

    return update;
}

function bindCurrencyInputs(root) {
    root.querySelectorAll('input[name="hocPhi"]').forEach((input) => {
        input.dataset.rawValue = sanitizeNumber(input.value);

        input.addEventListener("input", () => {
            const sanitized = sanitizeNumber(input.value);
            input.dataset.rawValue = sanitized;
            input.value = sanitized;
        });

        input.addEventListener("focus", () => {
            input.value = input.dataset.rawValue || "";
        });

        input.addEventListener("blur", () => {
            const raw = input.dataset.rawValue || "";
            input.value = raw ? formatCurrency(raw) : "";
        });

        const form = input.closest("form");
        if (form) {
            form.addEventListener("submit", () => {
                input.value = input.dataset.rawValue || "";
            });
        }
    });
}

function bindValidation(form) {
    form.querySelectorAll("[required]").forEach((field) => {
        field.addEventListener("input", () => {
            field.classList.remove("is-invalid");
        });
        field.addEventListener("change", () => {
            field.classList.remove("is-invalid");
        });
    });

    form.addEventListener("submit", (event) => {
        if (validateCourseForm(form)) {
            return;
        }

        event.preventDefault();

        if (typeof Swal !== "undefined") {
            Swal.fire({
                icon: "error",
                title: "Vui lòng kiểm tra thông tin!",
                text: "Kiểm tra các trường bắt buộc, ngày tháng và trạng thái hợp lệ.",
                timer: 2200,
                showConfirmButton: false,
            });
        }
    });
}

function validateCourseForm(form) {
    let isValid = true;

    form.querySelectorAll("[required]").forEach((field) => {
        const value =
            field.type === "checkbox" || field.type === "radio"
                ? field.checked
                : String(field.value ?? "").trim();

        if (!value) {
            field.classList.add("is-invalid");
            isValid = false;
        } else {
            field.classList.remove("is-invalid");
        }
    });

    const start = form.querySelector('input[name="ngayBatDau"]');
    const end = form.querySelector('input[name="ngayKetThuc"]');
    if (start && end && start.value && end.value) {
        const startDate = new Date(`${start.value}T00:00:00`);
        const endDate = new Date(`${end.value}T00:00:00`);
        if (endDate < startDate) {
            end.classList.add("is-invalid");
            isValid = false;
        }
    }

    return isValid;
}

function bindFileInputs(root) {
    root.querySelectorAll('input[type="file"]').forEach((input) => {
        const wrapper =
            input.closest(".col-12, .col-md-6, .mb-3, .form-group") || input.parentElement;
        const label = wrapper ? wrapper.querySelector("label.form-label") : null;
        if (!label) return;

        const original = label.textContent;

        input.addEventListener("change", () => {
            const fileName = input.files && input.files[0] ? input.files[0].name : "";
            label.textContent = fileName ? `${original} (Đã chọn: ${fileName})` : original;
        });
    });
}

function initDeleteConfirmation() {
    document.querySelectorAll("form.form-delete").forEach((form) => {
        form.addEventListener("submit", (event) => {
            if (form.dataset.confirmed === "true") {
                return;
            }

            event.preventDefault();

            const proceed = () => {
                form.dataset.confirmed = "true";
                form.submit();
            };

            if (typeof Swal === "undefined") {
                if (confirm("Xóa khóa học này?")) {
                    proceed();
                }
                return;
            }

            Swal.fire({
                title: "Bạn chắc chắn?",
                text: "Thao tác này sẽ xóa khóa học và không thể hoàn tác.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Xóa",
                cancelButtonText: "Huỷ",
                confirmButtonColor: "#d33",
            }).then((result) => {
                if (result.isConfirmed) {
                    proceed();
                }
            });
        });
    });
}

function wireDateDuration({ startEl, endEl, durationEl }) {
    if (!startEl || !endEl || !durationEl) {
        return;
    }

    const DAY_MS = 24 * 60 * 60 * 1000;

    const parseDate = (value) => {
        if (!value) return null;
        const date = new Date(`${value}T00:00:00`);
        return Number.isNaN(date.getTime()) ? null : date;
    };

    const formatDate = (date) => {
        if (!date || Number.isNaN(date.getTime())) return "";
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const day = String(date.getDate()).padStart(2, "0");
        return `${date.getFullYear()}-${month}-${day}`;
    };

    const clearValidity = () => {
        startEl.setCustomValidity("");
        endEl.setCustomValidity("");
        durationEl.setCustomValidity("");
    };

    const updateDuration = () => {
        const startDate = parseDate(startEl.value);
        const endDate = parseDate(endEl.value);
        if (!startDate || !endDate) return;

        if (endDate < startDate) {
            endEl.setCustomValidity("Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.");
            durationEl.value = "";
            return;
        }

        clearValidity();
        const diff = Math.round((endDate - startDate) / DAY_MS) + 1;
        durationEl.value = diff;
    };

    const updateEndDate = () => {
        const startDate = parseDate(startEl.value);
        const duration = parseInt(durationEl.value, 10);
        if (!startDate || !Number.isInteger(duration) || duration < 1) return;

        clearValidity();
        const endDate = new Date(startDate.getTime() + (duration - 1) * DAY_MS);
        endEl.value = formatDate(endDate);
    };

    startEl.addEventListener("change", () => {
        if (durationEl.value && !endEl.value) {
            updateEndDate();
        } else {
            updateDuration();
        }
    });

    endEl.addEventListener("change", updateDuration);
    durationEl.addEventListener("input", updateEndDate);

    if (startEl.value && endEl.value) {
        updateDuration();
    } else if (startEl.value && durationEl.value) {
        updateEndDate();
    }
}
