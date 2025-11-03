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

    if (methodLabelTarget && methodRadios.length) {
        methodRadios.forEach((radio) => {
            radio.addEventListener("change", () => {
                if (!radio.checked) {
                    return;
                }
                const method = radio.closest(".checkout-method");
                const heading = method ? method.querySelector("h3") : null;
                if (heading) {
                    methodLabelTarget.textContent = heading.textContent.trim();
                }
            });
        });
    }

    applyStage(currentStage);
})();
