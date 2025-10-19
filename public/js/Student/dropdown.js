// dropdown.js — Dropdown & Subdropdown with stable hover

document.addEventListener("DOMContentLoaded", () => {
    const isDesktop = () => window.matchMedia("(min-width: 861px)").matches;

    // Hai timer tách biệt
    let dropdownTimer = null; // cấp 1
    let subTimer = null; // cấp 2

    let openDropdown = null;
    let openSubDropdown = null;

    // ====== helpers cấp 1 ======
    const open = (dropdown) => {
        const trigger = dropdown.querySelector("[data-dropdown-trigger]");
        const panel = dropdown.querySelector("[data-dropdown-panel]");
        if (!panel || !trigger) return;

        if (openDropdown && openDropdown !== dropdown)
            closeDropdown(openDropdown, 0);

        panel.classList.add("is-open");
        trigger.setAttribute("aria-expanded", "true");
        openDropdown = dropdown;
    };

    const closeDropdown = (dropdown, delay = 200) => {
        const trigger = dropdown.querySelector("[data-dropdown-trigger]");
        const panel = dropdown.querySelector("[data-dropdown-panel]");
        if (!panel || !trigger) return;

        if (dropdownTimer) clearTimeout(dropdownTimer);
        dropdownTimer = setTimeout(() => {
            panel.classList.remove("is-open");
            trigger.setAttribute("aria-expanded", "false");
            if (openDropdown === dropdown) openDropdown = null;

            // đóng mọi subdropdown con
            dropdown
                .querySelectorAll("[data-subdropdown]")
                .forEach((sub) => closeSubDropdown(sub, 0));
        }, Math.max(250, delay));
    };

    // ====== helpers cấp 2 ======
    const openSub = (subDropdown) => {
        const trigger = subDropdown.querySelector("[data-subdropdown-trigger]");
        const panel = subDropdown.querySelector("[data-subdropdown-panel]");
        if (!panel || !trigger) return;

        if (openSubDropdown && openSubDropdown !== subDropdown)
            closeSubDropdown(openSubDropdown, 0);

        panel.classList.add("is-open");
        trigger.setAttribute("aria-expanded", "true");
        openSubDropdown = subDropdown;
    };

    const closeSubDropdown = (subDropdown, delay = 200) => {
        const trigger = subDropdown.querySelector("[data-subdropdown-trigger]");
        const panel = subDropdown.querySelector("[data-subdropdown-panel]");
        if (!panel || !trigger) return;

        if (subTimer) clearTimeout(subTimer);
        subTimer = setTimeout(() => {
            panel.classList.remove("is-open");
            trigger.setAttribute("aria-expanded", "false");
            if (openSubDropdown === subDropdown) openSubDropdown = null;
        }, Math.max(250, delay));
    };

    // ====== bind cấp 1 ======
    document.querySelectorAll("[data-dropdown]").forEach((dropdown) => {
        const trigger = dropdown.querySelector("[data-dropdown-trigger]");
        const panel = dropdown.querySelector("[data-dropdown-panel]");
        if (!trigger || !panel) return;

        // Desktop: hover
        dropdown.addEventListener("mouseenter", () => {
            if (isDesktop()) {
                if (dropdownTimer) clearTimeout(dropdownTimer);
                open(dropdown);
            }
        });
        dropdown.addEventListener("mouseleave", () => {
            if (isDesktop()) closeDropdown(dropdown);
        });

        // Mobile: click toggle
        trigger.addEventListener("click", (e) => {
            if (!isDesktop()) {
                e.preventDefault();
                const expanded =
                    trigger.getAttribute("aria-expanded") === "true";
                if (expanded) closeDropdown(dropdown, 0);
                else open(dropdown);
            }
        });
    });

    // ====== bind cấp 2 ======
    document.querySelectorAll("[data-subdropdown]").forEach((subDropdown) => {
        const trigger = subDropdown.querySelector("[data-subdropdown-trigger]");
        const panel = subDropdown.querySelector("[data-subdropdown-panel]");
        if (!trigger || !panel) return;

        // Desktop: hover
        subDropdown.addEventListener("mouseenter", () => {
            if (isDesktop()) {
                if (subTimer) clearTimeout(subTimer);
                openSub(subDropdown);
            }
        });
        subDropdown.addEventListener("mouseleave", () => {
            if (isDesktop()) closeSubDropdown(subDropdown);
        });

        // Mobile: click toggle
        trigger.addEventListener("click", (e) => {
            if (!isDesktop()) {
                e.preventDefault();
                const expanded =
                    trigger.getAttribute("aria-expanded") === "true";
                if (expanded) closeSubDropdown(subDropdown, 0);
                else openSub(subDropdown);
            }
        });
    });

    // Đổi breakpoint: đóng hết để tránh trạng thái kẹt
    window.addEventListener("resize", () => {
        document
            .querySelectorAll("[data-dropdown]")
            .forEach((dd) => closeDropdown(dd, 0));
        document
            .querySelectorAll("[data-subdropdown]")
            .forEach((sd) => closeSubDropdown(sd, 0));
    });
});