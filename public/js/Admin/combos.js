"use strict";

(function () {
    const datasetEl = document.getElementById("combo-form-dataset");
    if (!datasetEl) {
        return;
    }

    const PROMOTION_TYPE_PERCENT = "PERCENT_DISCOUNT";
    const PROMOTION_TYPE_FIXED = "FIXED_DISCOUNT";
    const PROMOTION_TYPE_GIFT = "GIFT";

    const dataset = safeJsonParse(datasetEl.textContent);
    const courseMap = new Map(
        Array.isArray(dataset.courses)
            ? dataset.courses.map((course) => [Number(course.id), course])
            : []
    );
    const updateUrlTemplate = dataset.updateUrlTemplate || "";
    const promotionMap = new Map(
        Array.isArray(dataset.promotions)
            ? dataset.promotions.map((promotion) => [
                  Number(promotion.id),
                  {
                      ...promotion,
                      type: normalizePromotionType(promotion.type),
                  },
              ])
            : []
    );

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

    function normalizePromotionType(type) {
        const value = String(type || "").toUpperCase();

        if (value === PROMOTION_TYPE_PERCENT || value === "PERCENT") {
            return PROMOTION_TYPE_PERCENT;
        }

        if (value === PROMOTION_TYPE_FIXED || value === "FIXED") {
            return PROMOTION_TYPE_FIXED;
        }

        return PROMOTION_TYPE_GIFT;
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
        const promotionHelp =
            promotionWrapper?.querySelector("[data-promotion-help]") || null;
        const defaultPromotionHelp = promotionHelp?.textContent || "";

        const selectedCourses = new Map();

        const calculateTotals = () => {
            const basePrice = Array.from(selectedCourses.values()).reduce(
                (sum, course) => sum + (Number(course.price) || 0),
                0
            );

            const hasPromotion = !!promotionSelect?.value;
            let finalPrice = basePrice;
            let message = defaultPromotionHelp;
            let promotionType = null;

            if (hasPromotion && basePrice > 0) {
                const promotionId = Number(promotionSelect.value);
                const promotion = promotionMap.get(promotionId);

                if (promotion) {
                    const rawValue = Number(promotion.value) || 0;
                    const type = normalizePromotionType(promotion.type);
                    promotionType = type;

                    if (type === PROMOTION_TYPE_PERCENT) {
                        const percent = Math.min(Math.max(rawValue, 0), 100);
                        const discount = Math.round(basePrice * (percent / 100));
                        finalPrice = Math.max(0, basePrice - discount);
                        message = `Giảm ${percent}% · Giá sau ưu đãi: ${formatCurrency(
                            finalPrice
                        )}.`;
                    } else if (type === PROMOTION_TYPE_FIXED) {
                        const discount = Math.max(0, rawValue);
                        finalPrice = Math.max(0, basePrice - discount);
                        message = `Giảm ${formatCurrency(
                            discount
                        )} · Giá sau ưu đãi: ${formatCurrency(finalPrice)}.`;
                    } else {
                        finalPrice = Math.max(0, basePrice);
                        message = "Khuyến mãi quà tặng · Giá combo giữ nguyên.";
                    }
                }
            }

            return {
                basePrice,
                finalPrice,
                saving: Math.max(0, basePrice - finalPrice),
                hasPromotion,
                message,
                promotionType,
            };
        };

        const applyTotals = (totals) => {
            if (priceInput) {
                priceInput.value = totals.basePrice > 0 ? totals.basePrice : 0;
            }

            if (promotionWrapper) {
                promotionWrapper.classList.toggle("show", totals.hasPromotion);
            }

            if (promotionPriceInput) {
                const isGiftPromotion =
                    totals.promotionType === PROMOTION_TYPE_GIFT;
                const shouldDisable =
                    isGiftPromotion || !totals.hasPromotion;

                if (shouldDisable) {
                    promotionPriceInput.value = "";
                    promotionPriceInput.setAttribute("disabled", "disabled");
                } else {
                    promotionPriceInput.removeAttribute("disabled");
                    promotionPriceInput.value = totals.finalPrice;
                }
            }

            if (promotionHelp) {
                promotionHelp.textContent = totals.message;
            }

            if (originalEl) originalEl.textContent = formatCurrency(totals.basePrice);
            if (saleEl) saleEl.textContent = formatCurrency(totals.finalPrice);
            if (savingEl) savingEl.textContent = formatCurrency(totals.saving);
        };

        const refreshFinancials = () => {
            const totals = calculateTotals();
            applyTotals(totals);
        };


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

            refreshFinancials();
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

        promotionSelect?.addEventListener("change", refreshFinancials);

        refreshState();

        return {
            setCourses,
            refresh: refreshState,
            refreshPromotion: refreshFinancials,
        };
    }

    const formControllers = new Map();

    document.querySelectorAll("[data-combo-form]").forEach((form) => {
        formControllers.set(form, createFormController(form));

        const actionField = form.querySelector("[data-action-field]");
        if (actionField) {
            const defaultValue =
                actionField.value && actionField.value.trim() !== ""
                    ? actionField.value
                    : "save_close";

            form.querySelectorAll("[data-form-action]").forEach((button) => {
                button.addEventListener("click", () => {
                    actionField.value =
                        button.dataset.formAction || defaultValue;
                });
            });
        }
    });

    const createModal = document.getElementById("comboCreateModal");
    if (createModal) {
        createModal.addEventListener("show.bs.modal", () => {
            const form = createModal.querySelector(
                'form[data-combo-form="create"]'
            );
            if (!form) {
                return;
            }

            const actionField = form.querySelector("[data-action-field]");
            if (actionField) {
                actionField.value = "save_close";
            }

            if (typeof form.__slugAutoUpdate === "function") {
                const slugTarget = form.__slugTarget;
                if (slugTarget && slugTarget.value.trim() === "") {
                    slugTarget.dataset.manual = "false";
                    form.__slugAutoUpdate();
                }
            }
        });
    }

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

        const slugField = form.querySelector('input[name="slug"]');
        if (slugField) {
            slugField.value = payload.slug || "";
            slugField.dataset.manual =
                slugField.value.trim() !== "" ? "true" : "false";

            if (form.__slugAutoUpdate && slugField.dataset.manual === "false") {
                form.__slugAutoUpdate();
            }
        }

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

        const certificateEnabledField = form.querySelector(
            "[data-certificate-enabled]"
        );
        if (certificateEnabledField) {
            certificateEnabledField.value = Number(
                payload.certificate_enabled ? 1 : 0
            );
        }

        const certificateProgressField = form.querySelector(
            "[data-certificate-progress]"
        );
        if (certificateProgressField) {
            const progressValue =
                payload.certificate_progress_required === null ||
                payload.certificate_progress_required === undefined
                    ? 100
                    : payload.certificate_progress_required;
            certificateProgressField.value = progressValue;
        }

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
        if (controller.refreshPromotion) {
            controller.refreshPromotion();
        }

        const actionField = form.querySelector("[data-action-field]");
        if (actionField) {
            actionField.value = "save_close";
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
