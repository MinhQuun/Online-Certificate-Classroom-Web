(function (global) {
    const slugify = (value) =>
        String(value || "")
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/[^a-z0-9\s-]/g, "")
            .trim()
            .replace(/\s+/g, "-")
            .replace(/-+/g, "-");

    const attach = (form) => {
        if (!form || form.__slugAttached) {
            return;
        }

        const source = form.querySelector("[data-slug-source]");
        const target = form.querySelector("[data-slug-target]");

        if (!source || !target) {
            return;
        }

        const syncManualFlag = () => {
            target.dataset.manual =
                target.value.trim() !== "" ? "true" : "false";
        };

        const updateSlug = () => {
            if (target.dataset.manual === "true") {
                return;
            }
            target.value = slugify(source.value);
        };

        syncManualFlag();

        if (target.value.trim() === "") {
            target.dataset.manual = "false";
            target.value = slugify(source.value);
        }

        source.addEventListener("input", updateSlug);
        target.addEventListener("input", syncManualFlag);
        target.addEventListener("blur", () => {
            if (target.value.trim() === "") {
                target.dataset.manual = "false";
                target.value = slugify(source.value);
            }
        });

        form.__slugAutoUpdate = updateSlug;
        form.__slugTarget = target;
        form.__slugAttached = true;
    };

    const init = (root = document) => {
        root.querySelectorAll("[data-slug-form]").forEach(attach);
    };

    global.AdminSlug = {
        init,
        attach,
        slugify,
    };

    document.addEventListener("DOMContentLoaded", () => init());
})(window);
