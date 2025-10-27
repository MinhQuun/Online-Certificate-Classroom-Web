// dropdown.js — Dropdown & Subdropdown with stable hover (improved)

document.addEventListener("DOMContentLoaded", () => {
    // Ngăn trường hợp file bị load 2 lần (layout + @push)
    if (window.__dropdownInit) return;
    window.__dropdownInit = true;

    const isDesktop = () => window.matchMedia("(min-width: 861px)").matches;

    // Timer cấp 1 và cấp 2
    let dropdownTimer = null;
    let subTimer = null;

    // Ai đang mở hiện tại
    let openDropdown = null;
    let openSubDropdown = null;

    // =========== Helpers chung ===========

    // Đóng subdropdown NGAY LẬP TỨC, không delay
    const forceCloseSub = (subDropdown) => {
        if (!subDropdown) return;
        const trigger = subDropdown.querySelector("[data-subdropdown-trigger]");
        const panel = subDropdown.querySelector("[data-subdropdown-panel]");
        if (!trigger || !panel) return;

        panel.classList.remove("is-open");
        trigger.setAttribute("aria-expanded", "false");

        if (openSubDropdown === subDropdown) {
            openSubDropdown = null;
        }
    };

    // Đóng dropdown cấp 1 NGAY LẬP TỨC, đồng thời đóng hết subdropdown con
    const forceCloseDropdown = (dropdown) => {
        if (!dropdown) return;
        const trigger = dropdown.querySelector("[data-dropdown-trigger]");
        const panel = dropdown.querySelector("[data-dropdown-panel]");
        if (!trigger || !panel) return;

        panel.classList.remove("is-open");
        trigger.setAttribute("aria-expanded", "false");

        // đóng tất cả subdropdown con ngay lập tức
        dropdown
            .querySelectorAll("[data-subdropdown]")
            .forEach((sub) => forceCloseSub(sub));

        if (openDropdown === dropdown) {
            openDropdown = null;
        }
    };

    // delay helper:
    // - nếu delay === 0 => đóng ngay
    // - nếu có delay khác => max(250ms, delay)
    const getDelay = (delay) => {
        if (delay === 0) return 0;
        return Math.max(250, delay || 0);
    };

    // =========== Dropdown cấp 1 (Danh mục khóa học) ===========

    const openDropdownFn = (dropdown) => {
        const trigger = dropdown.querySelector("[data-dropdown-trigger]");
        const panel = dropdown.querySelector("[data-dropdown-panel]");
        if (!panel || !trigger) return;

        // Nếu đang mở dropdown khác -> đóng ngay dropdown cũ
        if (openDropdown && openDropdown !== dropdown) {
            forceCloseDropdown(openDropdown);
        }

        panel.classList.add("is-open");
        trigger.setAttribute("aria-expanded", "true");
        openDropdown = dropdown;
    };

    const closeDropdownFn = (dropdown, delay = 200) => {
        if (!dropdown) return;
        if (dropdownTimer) clearTimeout(dropdownTimer);

        const wait = getDelay(delay);

        dropdownTimer = setTimeout(() => {
            forceCloseDropdown(dropdown);
        }, wait);
    };

    // =========== Subdropdown cấp 2 (panel bên phải) ===========

    const openSubFn = (subDropdown) => {
        const trigger = subDropdown.querySelector("[data-subdropdown-trigger]");
        const panel = subDropdown.querySelector("[data-subdropdown-panel]");
        if (!panel || !trigger) return;

        // Nếu đang mở sub khác -> đóng NGAY sub cũ
        if (openSubDropdown && openSubDropdown !== subDropdown) {
            forceCloseSub(openSubDropdown);
        }

        panel.classList.add("is-open");
        trigger.setAttribute("aria-expanded", "true");
        openSubDropdown = subDropdown;
    };

    const closeSubDropdownFn = (subDropdown, delay = 200) => {
        if (!subDropdown) return;
        if (subTimer) clearTimeout(subTimer);

        const wait = getDelay(delay);

        subTimer = setTimeout(() => {
            forceCloseSub(subDropdown);
        }, wait);
    };

    // =========== Gán event cho dropdown cấp 1 ===========

    document.querySelectorAll("[data-dropdown]").forEach((dropdown) => {
        const trigger = dropdown.querySelector("[data-dropdown-trigger]");
        const panel = dropdown.querySelector("[data-dropdown-panel]");
        if (!trigger || !panel) return;

        // Desktop: hover mở / rời chuột đóng (có delay nhẹ)
        dropdown.addEventListener("mouseenter", () => {
            if (isDesktop()) {
                if (dropdownTimer) clearTimeout(dropdownTimer);
                openDropdownFn(dropdown);
            }
        });

        dropdown.addEventListener("mouseleave", () => {
            if (isDesktop()) {
                closeDropdownFn(dropdown); // dùng delay chuẩn (250ms)
            }
        });

        // Mobile: click toggle
        trigger.addEventListener("click", (e) => {
            if (!isDesktop()) {
                e.preventDefault();
                const expanded =
                    trigger.getAttribute("aria-expanded") === "true";
                if (expanded) {
                    // đóng ngay
                    closeDropdownFn(dropdown, 0);
                } else {
                    openDropdownFn(dropdown);
                }
            }
        });
    });

    // =========== Gán event cho subdropdown cấp 2 ===========

    document.querySelectorAll("[data-subdropdown]").forEach((subDropdown) => {
        const trigger = subDropdown.querySelector("[data-subdropdown-trigger]");
        const panel = subDropdown.querySelector("[data-subdropdown-panel]");
        if (!trigger || !panel) return;

        // Desktop: hover từng item bên trái -> panel bên phải
        subDropdown.addEventListener("mouseenter", () => {
            if (isDesktop()) {
                if (subTimer) clearTimeout(subTimer);
                // mở sub này, đóng ngay sub cũ (nếu khác)
                openSubFn(subDropdown);
            }
        });

        subDropdown.addEventListener("mouseleave", () => {
            if (isDesktop()) {
                // khi rời item + panel, bắt đầu đếm ngược đóng
                closeSubDropdownFn(subDropdown); // delay ~250ms
            }
        });

        // Mobile: bấm để expand/collapse
        trigger.addEventListener("click", (e) => {
            if (!isDesktop()) {
                e.preventDefault();
                const expanded =
                    trigger.getAttribute("aria-expanded") === "true";
                if (expanded) {
                    closeSubDropdownFn(subDropdown, 0); // đóng ngay
                } else {
                    openSubFn(subDropdown);
                }
            }
        });
    });

    // Khi đổi breakpoint (resize), reset trạng thái để tránh kẹt
    window.addEventListener("resize", () => {
        document
            .querySelectorAll("[data-dropdown]")
            .forEach((dd) => forceCloseDropdown(dd));
        document
            .querySelectorAll("[data-subdropdown]")
            .forEach((sd) => forceCloseSub(sd));
    });
});
