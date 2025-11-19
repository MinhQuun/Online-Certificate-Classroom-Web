document.addEventListener("DOMContentLoaded", () => {
    const accordions = document.querySelectorAll("[data-accordion]");

    const updateAria = (wrapper, button) => {
        if (!wrapper || !button) return;
        button.setAttribute(
            "aria-expanded",
            wrapper.classList.contains("is-open") ? "true" : "false"
        );
    };

    accordions.forEach((wrapper) => {
        const button = wrapper.querySelector(
            ".module__toggle, .accordion__header"
        );
        const group = Array.from(
            wrapper.parentElement?.querySelectorAll("[data-accordion]") || []
        );
        const hasActive = wrapper.querySelector(".is-active");
        const presetOpen = wrapper.classList.contains("is-open");
        const skipAutoOpen = wrapper.dataset.accordionAutopen === "false";

        const shouldAutoOpen =
            !skipAutoOpen &&
            (hasActive || (group.length && group[0] === wrapper));

        if (shouldAutoOpen && !presetOpen) {
            wrapper.classList.add("is-open");
        }

        button?.addEventListener("click", () => {
            const willOpen = !wrapper.classList.contains("is-open");
            group.forEach((item) => {
                if (item !== wrapper) {
                    item.classList.remove("is-open");
                    updateAria(
                        item,
                        item.querySelector(
                            ".module__toggle, .accordion__header"
                        )
                    );
                }
            });
            wrapper.classList.toggle("is-open", willOpen);
            updateAria(wrapper, button);
        });

        updateAria(wrapper, button);
    });
});
