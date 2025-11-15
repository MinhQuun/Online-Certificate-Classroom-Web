(function () {
    "use strict";

    const FORM_SELECTOR = "[data-combo-add-form]";
    const BUTTON_SELECTOR = "[data-combo-add-btn]";

    const ready = (callback) => {
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", callback, {
                once: true,
            });
        } else {
            callback();
        }
    };

    const getButtonParts = (button) => ({
        text: button.querySelector("[data-combo-add-text]"),
        icon: button.querySelector("[data-combo-add-icon]"),
    });

    const setButtonText = (button, text) => {
        const { text: textNode } = getButtonParts(button);
        if (textNode) {
            textNode.textContent = text;
        } else {
            button.textContent = text;
        }
    };

    const getCurrentIconClass = (button) => {
        const { icon } = getButtonParts(button);
        if (!icon) {
            return "";
        }

        return Array.from(icon.classList)
            .filter((cls) => cls !== "fa-solid")
            .join(" ")
            .trim();
    };

    const setButtonIcon = (button, iconClass) => {
        const { icon } = getButtonParts(button);
        if (!icon) {
            return;
        }

        icon.className = "fa-solid";
        iconClass
            .split(" ")
            .filter(Boolean)
            .forEach((cls) => icon.classList.add(cls));
    };

    const ensureOriginalState = (button) => {
        if (!button.dataset.originalLabel) {
            const { text } = getButtonParts(button);
            button.dataset.originalLabel = text?.textContent?.trim() || "";
        }

        if (!button.dataset.originalIcon) {
            button.dataset.originalIcon = getCurrentIconClass(button);
        }
    };

    const setBusy = (button) => {
        if (!button || button.classList.contains("combo-card__cta--in-cart")) {
            return;
        }

        ensureOriginalState(button);

        button.classList.add("is-busy");
        button.disabled = true;
        button.setAttribute("aria-disabled", "true");

        setButtonText(button, button.dataset.labelAdding || "Đang thêm...");
        setButtonIcon(
            button,
            button.dataset.iconAdding || "fa-spinner fa-spin"
        );
    };

    const setAdded = (button) => {
        if (!button) {
            return;
        }

        button.classList.remove("is-busy");
        button.classList.add("combo-card__cta--in-cart");
        button.disabled = true;
        button.setAttribute("aria-disabled", "true");

        setButtonText(button, button.dataset.labelAdded || "Đã trong giỏ hàng");
        setButtonIcon(button, button.dataset.iconAdded || "fa-check");
    };

    const resetButton = (button) => {
        if (!button) {
            return;
        }

        button.classList.remove("is-busy", "combo-card__cta--in-cart");
        button.disabled = false;
        button.removeAttribute("aria-disabled");

        const label =
            button.dataset.labelDefault ||
            button.dataset.originalLabel ||
            "Thêm vào giỏ hàng";
        setButtonText(button, label);

        const icon =
            button.dataset.iconDefault ||
            button.dataset.originalIcon ||
            "fa-cart-plus";
        setButtonIcon(button, icon);
    };

    const handleCartStarted = (event) => {
        const form = event.detail?.form;
        if (!form || !form.matches(FORM_SELECTOR)) {
            return;
        }

        const button = form.querySelector(BUTTON_SELECTOR);
        setBusy(button);
    };

    const updateButtonsByCombo = (comboId, updater) => {
        if (!comboId) {
            return;
        }

        const forms = document.querySelectorAll(
            `${FORM_SELECTOR}[data-combo-id="${comboId}"]`
        );

        forms.forEach((form) => {
            const button = form.querySelector(BUTTON_SELECTOR);
            updater(button);
        });
    };

    const handleCartFinished = (event) => {
        const { form, success } = event.detail || {};
        if (!form || !form.matches(FORM_SELECTOR)) {
            return;
        }

        const comboId = form.getAttribute("data-combo-id");

        if (success) {
            updateButtonsByCombo(comboId, setAdded);
        } else {
            const button = form.querySelector(BUTTON_SELECTOR);
            resetButton(button);
        }
    };

    const init = () => {
        if (!document.querySelector(FORM_SELECTOR)) {
            return;
        }

        document.addEventListener("cart:request:started", handleCartStarted);
        document.addEventListener("cart:request:finished", handleCartFinished);
    };

    ready(init);
})();
