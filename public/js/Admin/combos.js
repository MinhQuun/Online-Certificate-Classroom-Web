"use strict";

(function () {
    const datasetEl = document.getElementById("combo-form-dataset");
    if (!datasetEl) {
        return;
    }

    const dataset = safeJsonParse(datasetEl.textContent);
    const courseMap = new Map(
        Array.isArray(dataset.courses)
            ? dataset.courses.map((course) => [Number(course.id), course])
            : []
    );
    const updateUrlTemplate = dataset.updateUrlTemplate || "";

    function safeJsonParse(value) {
        try {
            return JSON.parse(value || "{}");
        } catch (error) {
            console.error("Unable to parse combo dataset", error);
            return {};
        }
    }

    function formatCurrency(value) {
        return (
            new Intl.NumberFormat("vi-VN", { maximumFractionDigits: 0 }).format(
                Number(value) || 0
            ) + " VND"
        );
    }

    function createFormController(form) {
        const picker = form.querySelector("[data-course-picker]");
        const addButton = form.querySelector("[data-course-add]");
        const list = form.querySelector("[data-course-list]");
        const emptyState = form.querySelector("[data-course-empty]");
        const countBadge = form.querySelector("[data-course-count]");
        const priceInput = form.querySelector("[data-combo-price-input]");
        const summary = form.querySelector("[data-combo-summary]");
        const originalEl =
            summary?.querySelector("[data-combo-original]") || null;
        const saleEl = summary?.querySelector("[data-combo-sale]") || null;
        const savingEl =
            summary?.querySelector("[data-combo-saving]") || null;
        const promotionSelect = form.querySelector("[data-promotion-select]");
        const promotionWrapper = form.querySelector(
            "[data-promotion-price-wrapper]"
        );
        const promotionPriceInput = form.querySelector(
            "[data-promotion-price-input]"
        );

        const selectedCourses = new Map();

        function refreshSummary() {
            const totalOriginal = Array.from(selectedCourses.values()).reduce(
                (sum, course) => sum + (Number(course.price) || 0),
                0
            );
            const saleValue = Number(priceInput?.value || 0);
            const saving = Math.max(0, totalOriginal - saleValue);

            if (originalEl) originalEl.textContent = formatCurrency(totalOriginal);
            if (saleEl) saleEl.textContent = formatCurrency(saleValue);
            if (savingEl) savingEl.textContent = formatCurrency(saving);
        }

        function refreshState() {
            const hasItems = selectedCourses.size > 0;

            emptyState?.classList.toggle("d-none", hasItems);
            list?.classList.toggle("d-none", !hasItems);

            if (countBadge) {
                const count = selectedCourses.size;
                countBadge.textContent =
                    count === 1 ? "1 khóa học" : `${count} khóa học`;
            }

            if (picker) {
                Array.from(picker.options).forEach((option) => {
                    option.disabled = false;
                });

                Array.from(selectedCourses.keys()).forEach((id) => {
                    const option = picker.querySelector(`option[value="${id}"]`);
                    if (option) {
                        option.disabled = true;
                    }
                });

                picker.value = "";
            }

            refreshSummary();
        }

        function createCourseItem(course, order) {
            const li = document.createElement("li");
            li.dataset.courseId = String(course.id);

            const info = document.createElement("div");
            info.className = "d-flex flex-column gap-1";

            const title = document.createElement("div");
            title.className = "name";
            title.textContent = course.name || `#${course.id}`;

            const meta = document.createElement("div");
            meta.className = "meta";
            meta.textContent = formatCurrency(course.price);

            info.appendChild(title);
            info.appendChild(meta);

            const orderWrapper = document.createElement("div");
            orderWrapper.className = "d-flex align-items-center gap-2";

            const orderLabel = document.createElement("span");
            orderLabel.className = "text-muted small";
            orderLabel.textContent = "Thứ tự";

            const orderInput = document.createElement("input");
            orderInput.type = "number";
            orderInput.name = `courses[${course.id}]`;
            orderInput.value = order;
            orderInput.min = "1";
            orderInput.max = "99";
            orderInput.className = "form-control form-control-sm order-input";

            orderWrapper.appendChild(orderLabel);
            orderWrapper.appendChild(orderInput);

            const removeButton = document.createElement("button");
            removeButton.type = "button";
            removeButton.className = "btn btn-outline-danger btn-sm";
            removeButton.innerHTML = '<i class="bi bi-x-lg"></i>';
            removeButton.addEventListener("click", () => {
                selectedCourses.delete(course.id);
                li.remove();
                refreshState();
            });

            li.appendChild(info);
            li.appendChild(orderWrapper);
            li.appendChild(removeButton);

            list?.appendChild(li);
        }

        function addCourse(courseId) {
            const id = Number(courseId);
            if (!id || selectedCourses.has(id)) {
                return;
            }

            const course = courseMap.get(id);
            if (!course) {
                return;
            }

            const order = selectedCourses.size + 1;
            selectedCourses.set(id, course);
            createCourseItem(course, order);
            refreshState();
        }

        function setCourses(initialCourses) {
            selectedCourses.clear();
            list && (list.innerHTML = "");

            (initialCourses || []).forEach((course) => {
                const detailedCourse =
                    courseMap.get(Number(course.id)) || course;
                selectedCourses.set(Number(course.id), detailedCourse);
                createCourseItem(
                    detailedCourse,
                    Number(course.order) || selectedCourses.size
                );
            });

            refreshState();
        }

        addButton?.addEventListener("click", () => {
            const value = picker?.value;
            if (!value) {
                return;
            }
            addCourse(value);
        });

        priceInput?.addEventListener("input", refreshSummary);

        promotionSelect?.addEventListener("change", () => {
            const hasPromotion = !!promotionSelect.value;
            if (promotionWrapper) {
                promotionWrapper.classList.toggle("show", hasPromotion);
            }

            if (!hasPromotion && promotionPriceInput) {
                promotionPriceInput.value = "";
            }
        });

        refreshState();

        return {
            setCourses,
            refresh: refreshState,
        };
    }

    const formControllers = new Map();

    document
        .querySelectorAll("[data-combo-form]")
        .forEach((form) => formControllers.set(form, createFormController(form)));

    const editModal = document.getElementById("comboEditModal");
    if (!editModal) {
        return;
    }

    editModal.addEventListener("show.bs.modal", (event) => {
        const trigger = event.relatedTarget;
        if (!trigger) {
            return;
        }

        const payload = safeJsonParse(trigger.getAttribute("data-combo"));
        const form = editModal.querySelector('form[data-combo-form="edit"]');
        if (!form || !payload?.id) {
            return;
        }

        const controller = formControllers.get(form);
        if (!controller) {
            return;
        }

        const action = updateUrlTemplate.replace("__ID__", String(payload.id));
        form.setAttribute("action", action);

        const idField = form.querySelector("[data-combo-id]");
        if (idField) idField.value = payload.id;

        const nameField = form.querySelector('input[name="tenGoi"]');
        if (nameField) nameField.value = payload.name || "";

        const descriptionField = form.querySelector('textarea[name="moTa"]');
        if (descriptionField) descriptionField.value = payload.description || "";

        const priceField = form.querySelector('input[name="gia"]');
        if (priceField) priceField.value = payload.price || 0;

        const statusField = form.querySelector('select[name="trangThai"]');
        if (statusField) statusField.value = payload.status || "PUBLISHED";

        const startField = form.querySelector('input[name="ngayBatDau"]');
        if (startField) startField.value = payload.start_date || "";

        const endField = form.querySelector('input[name="ngayKetThuc"]');
        if (endField) endField.value = payload.end_date || "";

        const promotionField = form.querySelector('select[name="promotion_id"]');
        if (promotionField) promotionField.value = payload.promotion_id || "";

        const promotionPriceField = form.querySelector(
            'input[name="promotion_price"]'
        );
        if (promotionPriceField) {
            promotionPriceField.value =
                payload.promotion_price !== null &&
                payload.promotion_price !== undefined
                    ? payload.promotion_price
                    : "";
        }

        const promotionWrapper = form.querySelector(
            "[data-promotion-price-wrapper]"
        );
        if (promotionWrapper) {
            promotionWrapper.classList.toggle(
                "show",
                !!payload.promotion_id
            );
        }

        const currentImage = form.querySelector("[data-current-image]");
        if (currentImage) {
            const img = currentImage.querySelector("img");
            if (payload.image && img) {
                img.src = payload.image;
                currentImage.classList.remove("d-none");
            } else {
                currentImage.classList.add("d-none");
            }
        }

        controller.setCourses(payload.courses || []);
    });
})();
