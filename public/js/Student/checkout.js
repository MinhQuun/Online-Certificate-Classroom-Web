"use strict";

(function () {
    const root = document.querySelector("[data-checkout]");

    if (!root) {
        return;
    }

    const stageElements = Array.from(root.querySelectorAll("[data-stage]"));
    const stepElements = Array.from(root.querySelectorAll("[data-checkout-step]"));
    const nextButtons = root.querySelectorAll("[data-checkout-next]");
    const prevButtons = root.querySelectorAll("[data-checkout-prev]");
    const isLocked = root.dataset.locked === "true";
    const methodLabelTarget = root.querySelector("[data-checkout-method-label]");
    const methodRadios = root.querySelectorAll('input[name="payment_method"]');
    const methodCards = root.querySelectorAll("[data-checkout-method-card]");
    const methodPanels = root.querySelectorAll("[data-checkout-method-panel]");

    const activateMethodPanel = (key) => {
        methodPanels.forEach((panel) => {
            const panelKey = panel.dataset.checkoutMethodPanel;
            panel.classList.toggle("is-active", panelKey === key);
        });
    };

    const activateMethodCard = (key) => {
        methodCards.forEach((card) => {
            const input = card.querySelector('input[name="payment_method"]');
            card.classList.toggle("is-active", input?.value === key);
        });
    };

    const updateMethodLabel = (input) => {
        if (!methodLabelTarget || !input) {
            return;
        }
        const card = input.closest("[data-checkout-method-card]");
        const heading = card?.querySelector("[data-payment-title]");
        const label = input.dataset.methodLabel || heading?.textContent || methodLabelTarget.textContent;
        methodLabelTarget.textContent = label.trim();
    };

    const clampStage = (stage) => Math.min(3, Math.max(1, stage));

    let currentStage = clampStage(parseInt(root.dataset.currentStage || "1", 10));

    const applyStage = (stage) => {
        currentStage = clampStage(stage);

        stageElements.forEach((stageEl) => {
            const stageIndex = Number(stageEl.dataset.stage);
            const isActive = stageIndex === currentStage;
            stageEl.classList.toggle("is-active", isActive);
            stageEl.setAttribute("aria-hidden", isActive ? "false" : "true");
        });

        stepElements.forEach((step) => {
            const stepIndex = Number(step.dataset.checkoutStep);
            step.classList.toggle("is-active", stepIndex === currentStage);
            step.classList.toggle("is-completed", stepIndex < currentStage);
        });
    };

    nextButtons.forEach((button) => {
        button.addEventListener("click", () => {
            if (isLocked) {
                return;
            }
            applyStage(currentStage + 1);
        });
    });

    prevButtons.forEach((button) => {
        button.addEventListener("click", () => {
            if (isLocked) {
                return;
            }
            applyStage(currentStage - 1);
        });
    });

    if (methodRadios.length) {
        const checkedRadio = root.querySelector('input[name="payment_method"]:checked');
        if (checkedRadio) {
            activateMethodPanel(checkedRadio.value);
            activateMethodCard(checkedRadio.value);
            updateMethodLabel(checkedRadio);
        }

        methodRadios.forEach((radio) => {
            radio.addEventListener("change", () => {
                if (!radio.checked) {
                    return;
                }
                updateMethodLabel(radio);
                activateMethodPanel(radio.value);
                activateMethodCard(radio.value);
            });
        });
    }

    applyStage(currentStage);
})();
