"use strict";

(function () {
    const cartForm = document.getElementById("cart-form");
    const cartScope = document.querySelector("[data-cart-form-scope]");

    if (!cartForm || !cartScope) {
        return;
    }

    const selectAll = cartScope.querySelector("[data-cart-select-all]");
    const itemCheckboxes = Array.from(cartScope.querySelectorAll("[data-cart-item-checkbox]"));
    const selectedCountEl = document.querySelector("[data-cart-selected-count]");
    const subtotalEl = document.querySelector("[data-cart-subtotal]");
    const totalEl = document.querySelector("[data-cart-total]");
    const submitButton = document.querySelector("[data-cart-submit]");

    const formatCurrency = (value) =>
        new Intl.NumberFormat("vi-VN", { maximumFractionDigits: 0 }).format(value) + " VNĐ";

    function updateState() {
        let selected = 0;
        let subtotal = 0;

        itemCheckboxes.forEach((checkbox) => {
            if (!checkbox.checked) {
                return;
            }
            selected += 1;
            const item = checkbox.closest("[data-cart-item]");
            const price = item ? Number(item.dataset.price || 0) : 0;
            subtotal += price;
        });

        if (selectedCountEl) {
            selectedCountEl.textContent = `${selected} khóa học`;
        }

        if (subtotalEl) {
            subtotalEl.textContent = formatCurrency(subtotal);
        }

        if (totalEl) {
            totalEl.textContent = formatCurrency(subtotal);
        }

        if (submitButton) {
            const disabled = selected === 0;
            submitButton.disabled = disabled;
            submitButton.setAttribute("aria-disabled", String(disabled));
        }

        if (selectAll) {
            const totalItems = itemCheckboxes.length;
            selectAll.checked = selected > 0 && selected === totalItems;
            selectAll.indeterminate = selected > 0 && selected < totalItems;
        }
    }

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
})();
