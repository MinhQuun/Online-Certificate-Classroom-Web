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
    const totalEl = document.querySelector("[data-cart-total]");
    const submitButton = document.querySelector("[data-cart-submit]");
    const clearForm = cartScope.querySelector("[data-cart-clear-form]");
    const removeForm = cartScope.querySelector("[data-cart-remove-form]");
    const removeInputsContainer = removeForm?.querySelector("[data-cart-remove-inputs]") || null;
    const removeButton = removeForm?.querySelector("[data-cart-remove-selected]") || null;
    const removeLabel = removeButton?.querySelector("[data-cart-remove-label]") || null;

    const formatCurrency = (value) =>
        new Intl.NumberFormat("vi-VN", { maximumFractionDigits: 0 }).format(value) + " VND";

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
            selectedCountEl.textContent = `${selected} mục`;
        }

        if (totalEl) {
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

            const selectedItems = itemCheckboxes
                .filter((checkbox) => checkbox.checked)
                .map((checkbox) => checkbox.value);

            if (selectedItems.length === 0) {
                event.preventDefault();
                return;
            }

            while (removeInputsContainer.firstChild) {
                removeInputsContainer.firstChild.remove();
            }

            selectedItems.forEach((value) => {
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = "items[]";
                input.value = value;
                removeInputsContainer.appendChild(input);
            });

            const message =
                removeForm.getAttribute("data-confirm") ||
                "Bạn có chắc chắn muốn xoá các mục đã chọn?";

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    }
})();
