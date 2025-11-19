"use strict";

(function () {
    const cartForm = document.getElementById("cart-form");
    const cartScope = document.querySelector("[data-cart-form-scope]");

    if (!cartForm || !cartScope) {
        return;
    }

    const selectAll = cartScope.querySelector("[data-cart-select-all]");
    let itemCheckboxes = Array.from(
        cartScope.querySelectorAll("[data-cart-item-checkbox]")
    );
    const selectedCountEl = document.querySelector("[data-cart-selected-count]");
    const totalEl = document.querySelector("[data-cart-total]");
    const submitButton = document.querySelector("[data-cart-submit]");
    const comboTotalEl = document.querySelector("[data-cart-combo-total]");
    const courseTotalEl = document.querySelector("[data-cart-course-total]");
    const clearForm = cartScope.querySelector("[data-cart-clear-form]");
    const removeForm = cartScope.querySelector("[data-cart-remove-form]");
    const removeInputsContainer =
        removeForm?.querySelector("[data-cart-remove-inputs]") || null;
    const removeButton =
        removeForm?.querySelector("[data-cart-remove-selected]") || null;
    const removeLabel =
        removeButton?.querySelector("[data-cart-remove-label]") || null;
    const emptyState = document.querySelector("[data-cart-empty-state]");
    const totalCountLabel = cartScope.querySelector("[data-cart-total-count]");
    const metaLabel = cartScope.querySelector("[data-cart-meta]");
    const cartCountBadge = document.querySelector("[data-cart-count-total]");

    const formatCurrency = (value) =>
        new Intl.NumberFormat("vi-VN", { maximumFractionDigits: 0 }).format(value) + " VND";

    function hideElement(element, shouldHide) {
        if (!element) {
            return;
        }
        if (shouldHide) {
            element.setAttribute("hidden", "");
        } else {
            element.removeAttribute("hidden");
        }
    }

    function computeCounts() {
        const items = Array.from(cartScope.querySelectorAll("[data-cart-item]"));
        const combos = items.filter((item) =>
            item.classList.contains("cart-item--combo")
        ).length;
        const courses = items.length - combos;

        return {
            combos,
            courses,
            total: items.length,
        };
    }

    function updateCountsUI() {
        const counts = computeCounts();

        if (cartCountBadge) {
            cartCountBadge.textContent = counts.total;
        }

        if (totalCountLabel) {
            totalCountLabel.textContent = `Chọn tất cả (${counts.total})`;
        }

        if (metaLabel) {
            metaLabel.textContent = `${counts.combos} combo & ${counts.courses} khóa học`;
        }
    }

    function toggleEmptyState() {
        const isEmpty = itemCheckboxes.length === 0;
        hideElement(cartScope, isEmpty);
        hideElement(emptyState, !isEmpty);
    }

    function refreshCartItems() {
        itemCheckboxes = Array.from(
            cartScope.querySelectorAll("[data-cart-item-checkbox]")
        );
        updateCountsUI();
        toggleEmptyState();
    }

    function collectSelectedBuckets() {
        const buckets = { courses: [], combos: [] };

        itemCheckboxes.forEach((checkbox) => {
            if (!checkbox.checked) {
                return;
            }

            const value = checkbox.value || "";
            const [type, rawId] = value.split(":");
            const id = Number(rawId);

            if (!Number.isFinite(id)) {
                return;
            }

            if (type === "combo") {
                buckets.combos.push(id);
            } else {
                buckets.courses.push(id);
            }
        });

        return buckets;
    }

    function appendHiddenInput(container, name, value) {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = name;
        input.value = value;
        container.appendChild(input);
    }

    function updateState() {
        let selected = 0;
        let subtotal = 0;
        let selectedComboTotal = 0; // Biến mới để lưu tổng tiền Combo đã chọn
        let selectedCourseTotal = 0; // Biến mới để lưu tổng tiền Khóa học lẻ đã chọn

        itemCheckboxes.forEach((checkbox) => {
            if (!checkbox.checked) {
                return;
            }
            selected += 1;
            const item = checkbox.closest("[data-cart-item]");

            if (item) {
                const price = Number(item.dataset.price || 0);
                subtotal += price;

                // Phân biệt và tính tổng riêng cho Combo và Khóa học lẻ
                if (item.classList.contains("cart-item--combo")) {
                    selectedComboTotal += price;
                } else {
                    selectedCourseTotal += price;
                }
            }
        });

        // Cập nhật hiển thị tổng tiền Combo đã chọn
        if (comboTotalEl) {
            comboTotalEl.textContent = formatCurrency(selectedComboTotal);
        }

        // Cập nhật hiển thị tổng tiền Khóa học lẻ đã chọn
        if (courseTotalEl) {
            courseTotalEl.textContent = formatCurrency(selectedCourseTotal);
        }

        if (selectedCountEl) {
            if (selected === 0) {
                selectedCountEl.textContent = "Chưa chọn mục nào";
                selectedCountEl.classList.add("is-empty");
            } else {
                selectedCountEl.textContent = `${selected} mục đã chọn`;
                selectedCountEl.classList.remove("is-empty");
            }
        }

        if (totalEl) {
            // Tổng thanh toán (Total) vẫn là tổng của subtotal
            totalEl.textContent = formatCurrency(subtotal);
        }

        if (submitButton) {
            const disabled = selected === 0;
            submitButton.disabled = disabled;
            submitButton.setAttribute("aria-disabled", String(disabled));
        }

        if (removeButton) {
            const disabled = selected === 0;
            removeButton.disabled = disabled;
            removeButton.setAttribute("aria-disabled", String(disabled));

            if (disabled) {
                removeButton.setAttribute("disabled", "");
            } else {
                removeButton.removeAttribute("disabled");
            }

            if (removeLabel) {
                removeLabel.textContent = disabled
                    ? "Xoá đã chọn"
                    : `Xoá (${selected})`;
            }
        }

        if (selectAll) {
            const totalItems = itemCheckboxes.length;
            selectAll.checked = selected > 0 && selected === totalItems;
            selectAll.indeterminate = selected > 0 && selected < totalItems;
        }
    }

    function removeSelectedItemsFromDom() {
        let removed = false;

        itemCheckboxes.forEach((checkbox) => {
            if (!checkbox.checked) {
                return;
            }

            const item = checkbox.closest("[data-cart-item]");
            if (item) {
                item.remove();
                removed = true;
            }
        });

        return removed;
    }

    function clearAllItemsFromDom() {
        const items = cartScope.querySelectorAll("[data-cart-item]");
        if (!items.length) {
            return false;
        }

        items.forEach((item) => item.remove());
        return true;
    }

    refreshCartItems();

    document.addEventListener("cart:request:finished", (event) => {
        const detail = event.detail || {};
        const { form, success } = detail;

        if (!success || !form) {
            return;
        }

        let mutated = false;

        if (form.matches("[data-cart-item-remove]")) {
            const item = form.closest("[data-cart-item]");
            if (item) {
                item.remove();
                mutated = true;
            }
        } else if (form.matches("[data-cart-remove-form]")) {
            mutated = removeSelectedItemsFromDom();
        } else if (form.matches("[data-cart-clear-form]")) {
            mutated = clearAllItemsFromDom();
        }

        if (mutated) {
            refreshCartItems();
            updateState();
        }
    });

    if (selectAll) {
        selectAll.addEventListener("change", () => {
            const checked = selectAll.checked;
            itemCheckboxes.forEach((checkbox) => {
                checkbox.checked = checked;
            });
            updateState();
        });
    }

    itemCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener("change", updateState);
    });

    updateState();

    if (submitButton) {
        submitButton.addEventListener("click", (event) => {
            if (submitButton.disabled) {
                event.preventDefault();
                return;
            }

            if (typeof cartForm.requestSubmit === "function") {
                cartForm.requestSubmit();
                event.preventDefault();
            }
        });
    }

    if (clearForm) {
        clearForm.addEventListener("submit", (event) => {
            const message =
                clearForm.getAttribute("data-confirm") ||
                "Bạn có chắc chắn muốn xoá toàn bộ giỏ hàng?";

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    }

    if (removeForm && removeInputsContainer && removeButton) {
        removeForm.addEventListener("submit", (event) => {
            if (removeButton.disabled) {
                event.preventDefault();
                return;
            }

            const selections = collectSelectedBuckets();
            const totalSelections =
                selections.courses.length + selections.combos.length;

            if (totalSelections === 0) {
                event.preventDefault();
                return;
            }

            while (removeInputsContainer.firstChild) {
                removeInputsContainer.firstChild.remove();
            }

            selections.courses.forEach((id) =>
                appendHiddenInput(removeInputsContainer, "selected_courses[]", id)
            );

            selections.combos.forEach((id) =>
                appendHiddenInput(removeInputsContainer, "selected_combos[]", id)
            );

            const message =
                removeForm.getAttribute("data-confirm") ||
                "Bạn có chắc chắn muốn xoá các mục đã chọn?";

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    }
})();
