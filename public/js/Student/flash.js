(() => {
    const TYPE_MAP = {
        success: {
            className: "is-success",
            title: "Thành công",
            icon: '<i class="fa-solid fa-circle-check"></i>',
        },
        error: {
            className: "is-error",
            title: "Lỗi",
            icon: '<i class="fa-solid fa-circle-exclamation"></i>',
        },
        warning: {
            className: "is-warning",
            title: "Chú ý",
            icon: '<i class="fa-solid fa-triangle-exclamation"></i>',
        },
        info: {
            className: "is-info",
            title: "Thông báo",
            icon: '<i class="fa-solid fa-circle-info"></i>',
        },
    };

    const STORAGE_KEY = "occStudentQueuedToasts";

    const ensureStack = () => {
        let stack = document.querySelector(".toast-stack");
        if (stack) return stack;
        stack = document.createElement("div");
        stack.className = "toast-stack";
        document.body.appendChild(stack);
        return stack;
    };

    const scheduleRemoval = (card, ms) => {
        const duration = Number(ms) || Number(card.dataset.autohide) || 4200;
        card.style.setProperty("--ms", `${duration}ms`);
        card.style.animation = "toast-fade-in .22s ease-out both";

        const remove = () => {
            card.style.animation = "toast-fade-out .22s ease-in both";
            setTimeout(() => card.remove(), 220);
        };

        const timer = setTimeout(remove, duration);
        const closeBtn = card.querySelector(".toast-close");
        if (closeBtn) {
            closeBtn.addEventListener("click", () => {
                clearTimeout(timer);
                remove();
            });
        }
    };

    const createToast = (type, message, title, duration) => {
        const stack = ensureStack();
        const key = TYPE_MAP[type] ? type : "info";
        const meta = TYPE_MAP[key];
        const card = document.createElement("div");
        card.className = `toast-card ${meta.className}`;
        card.dataset.autohide = String(duration || "");

        const iconWrap = document.createElement("div");
        iconWrap.className = "toast-icon";
        iconWrap.innerHTML = meta.icon;

        const contentWrap = document.createElement("div");
        contentWrap.className = "toast-content";

        const titleEl = document.createElement("strong");
        titleEl.textContent = title || meta.title;
        contentWrap.appendChild(titleEl);

        if (
            message &&
            message.trim() &&
            message.trim() !== titleEl.textContent
        ) {
            const bodyEl = document.createElement("div");
            bodyEl.className = "toast-text";
            bodyEl.textContent = message;
            contentWrap.appendChild(bodyEl);
        }

        const closeBtn = document.createElement("button");
        closeBtn.className = "toast-close";
        closeBtn.setAttribute("aria-label", "Đóng");
        closeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';

        card.append(iconWrap, contentWrap, closeBtn);
        stack.appendChild(card);

        scheduleRemoval(card, duration);
        return card;
    };

    const hydrateExisting = () => {
        document
            .querySelectorAll(".toast-stack .toast-card")
            .forEach((card) => scheduleRemoval(card));
    };

    const fromDataset = (el) => {
        if (!el) return;
        ["success", "error", "warning", "info"].forEach((type) => {
            const message = el.dataset[type];
            if (!message) return;
            const titleKey = `${type}Title`;
            createToast(
                type,
                message,
                el.dataset[titleKey] || undefined,
                el.dataset[`${type}Autohide`] || undefined
            );
        });
    };

    const readQueuedToasts = () => {
        try {
            const raw = sessionStorage.getItem(STORAGE_KEY);
            if (!raw) return [];
            const parsed = JSON.parse(raw);
            sessionStorage.removeItem(STORAGE_KEY);
            return Array.isArray(parsed) ? parsed : [];
        } catch (error) {
            try {
                sessionStorage.removeItem(STORAGE_KEY);
            } catch (_) {
                /* ignore */
            }
            return [];
        }
    };

    const enqueueToast = (payload) => {
        try {
            const raw = sessionStorage.getItem(STORAGE_KEY);
            const parsed = raw ? JSON.parse(raw) : [];
            const queue = Array.isArray(parsed) ? parsed : [];
            queue.push(payload);
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify(queue));
            return true;
        } catch (error) {
            return false;
        }
    };

    const flushQueuedToasts = () => {
        readQueuedToasts().forEach((toast) => {
            if (!toast) return;
            if (typeof toast === "string") {
                createToast("info", toast);
                return;
            }
            createToast(
                toast.type,
                toast.message,
                toast.title,
                toast.autohide ?? toast.duration
            );
        });
    };

    const api = {
        push: (message, type = "info", title, duration) =>
            createToast(type, message, title, duration),
        queue: (message, type = "info", title, duration, options = {}) => {
            const payload = {
                type,
                message,
                title,
                autohide: duration ?? options.autohide ?? undefined,
            };
            if (!enqueueToast(payload)) {
                return createToast(type, message, title, duration);
            }
            return payload;
        },
        fromDataset,
    };

    if (!window.flashToast) {
        window.flashToast = api;
    } else {
        Object.assign(window.flashToast, api);
    }

    const run = () => {
        hydrateExisting();
        document
            .querySelectorAll("[data-flash-dataset]")
            .forEach((el) => fromDataset(el));
        flushQueuedToasts();
    };

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", run, { once: true });
    } else {
        run();
    }
})();
