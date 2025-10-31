document.addEventListener("DOMContentLoaded", () => {
    initCourseSelector();
    initCreateMiniTestModal();
    initEditMiniTestModal();
    initMaterialModal();
    initQuestionBuilder();
});

function initCourseSelector() {
    const selector = document.getElementById("courseSelector");
    if (!selector) {
        return;
    }

    selector.addEventListener("change", () => {
        const baseUrl = selector.dataset.baseUrl;
        const courseId = selector.value;
        if (baseUrl && courseId) {
            window.location.href = `${baseUrl}?course=${courseId}`;
        }
    });
}

function initCreateMiniTestModal() {
    const modal = document.getElementById("createMiniTestModal");
    if (!modal) {
        return;
    }

    const courseSelect = modal.querySelector("select[name='course_id']");
    const chapterSelect = modal.querySelector("select[name='chapter_id']");

    const filterChapters = (courseId) => {
        Array.from(chapterSelect.options).forEach((option) => {
            const belongs = option.dataset.course === courseId;
            option.hidden = !belongs && option.value !== "";
            if (!belongs && option.selected) {
                option.selected = false;
            }
        });
    };

    courseSelect?.addEventListener("change", () => filterChapters(courseSelect.value));

    if (courseSelect?.value) {
        filterChapters(courseSelect.value);
    }
}

function initEditMiniTestModal() {
    const modal = document.getElementById("editMiniTestModal");
    if (!modal) {
        return;
    }

    const configEl = document.getElementById("teacherMinitestsConfig");
    if (!configEl) {
        return;
    }

    const updateTemplate = configEl.dataset.updateRoute || "";
    const courseSelect = modal.querySelector("#edit_course_id");
    const chapterSelect = modal.querySelector("#edit_chapter_id");

    const filterChapters = (courseId) => {
        Array.from(chapterSelect.options).forEach((option) => {
            const belongs = option.dataset.course === courseId;
            option.hidden = !belongs && option.value !== "";
            if (!belongs && option.selected) {
                option.selected = false;
            }
        });
    };

    courseSelect?.addEventListener("change", () => filterChapters(courseSelect.value));

    document.querySelectorAll(".edit-minitest-btn").forEach((button) => {
        button.addEventListener("click", () => {
            const miniTestId = button.dataset.minitestId;
            const form = document.getElementById("editMiniTestForm");
            if (!miniTestId || !form) {
                return;
            }

            form.action = updateTemplate.replace("__ID__", miniTestId);

            setSelectValue(courseSelect, button.dataset.courseId);
            filterChapters(button.dataset.courseId || "");
            setSelectValue(chapterSelect, button.dataset.chapterId);

            modal.querySelector("#edit_title").value = button.dataset.title || "";
            setSelectValue(modal.querySelector("#edit_skill_type"), button.dataset.skillType);
            modal.querySelector("#edit_order").value = button.dataset.order || "";
            modal.querySelector("#edit_weight").value = button.dataset.weight || "";
            modal.querySelector("#edit_time_limit").value = button.dataset.timeLimit || "";
            modal.querySelector("#edit_attempts").value = button.dataset.attempts || "";
            modal.querySelector("#edit_is_active").checked = button.dataset.isActive === "1";
        });
    });
}

function initMaterialModal() {
    const modal = document.getElementById("addMaterialModal");
    if (!modal) {
        return;
    }

    const configEl = document.getElementById("teacherMinitestsConfig");
    if (!configEl) {
        return;
    }

    const materialTemplate = configEl.dataset.materialRoute || "";
    const form = document.getElementById("addMaterialForm");
    const fileSection = modal.querySelector("#file_upload_section");
    const urlSection = modal.querySelector("#url_input_section");
    const sourceRadios = modal.querySelectorAll("input[name='source_type']");

    const toggleSource = (value) => {
        if (value === "file") {
            fileSection.classList.remove("d-none");
            urlSection.classList.add("d-none");
        } else {
            urlSection.classList.remove("d-none");
            fileSection.classList.add("d-none");
        }
    };

    sourceRadios.forEach((radio) => {
        radio.addEventListener("change", () => toggleSource(radio.value));
    });

    toggleSource(modal.querySelector("input[name='source_type']:checked")?.value || "file");

    document.querySelectorAll(".add-material-btn").forEach((button) => {
        button.addEventListener("click", () => {
            const miniTestId = button.dataset.minitestId;
            const titleTarget = modal.querySelector("#addMaterialModalLabel");
            if (titleTarget) {
                titleTarget.textContent = `Thêm tài liệu cho: ${button.dataset.minitestTitle || ""}`;
            }
            if (miniTestId && form) {
                form.action = materialTemplate.replace("__ID__", miniTestId);
                form.reset();
                toggleSource("file");
            }
        });
    });
}

function initQuestionBuilder() {
    const form = document.getElementById("questionsForm");
    if (!form) {
        return;
    }

    const template = document.getElementById("questionTemplate");
    const addBtn = document.getElementById("addQuestionBtn");
    const container = form.querySelector(".question-collection");
    const countEl = document.getElementById("questionCount");
    const totalPointsEl = document.getElementById("questionTotalPoints");

    const renumberQuestions = () => {
        const cards = container.querySelectorAll(".question-card");
        let totalPoints = 0;

        cards.forEach((card, idx) => {
            card.dataset.questionIndex = idx;
            card.querySelector(".question-number").textContent = `Câu ${idx + 1}`;

            card.querySelectorAll("textarea, input, select").forEach((input) => {
                const name = input.getAttribute("name");
                if (name) {
                    input.setAttribute("name", name.replace(/\[\d+\]/, `[${idx}]`));
                }
            });

            const pointsInput = card.querySelector("input[name$='[points]']");
            const points = parseFloat(pointsInput?.value || "0");
            if (!isNaN(points)) {
                totalPoints += points;
            }
        });

        if (countEl) {
            countEl.textContent = cards.length;
        }
        if (totalPointsEl) {
            totalPointsEl.textContent = totalPoints.toFixed(1);
        }
    };

    const bindCardEvents = (card) => {
        const typeSelect = card.querySelector(".question-type");
        const choiceSection = card.querySelector("[data-choice]");
        const trueFalseSection = card.querySelector("[data-true-false]");
        const essaySection = card.querySelector("[data-essay]");

        const handleTypeChange = () => {
            const value = typeSelect.value;

            choiceSection.style.display = ["single_choice", "multiple_choice"].includes(value) ? "" : "none";
            trueFalseSection.style.display = value === "true_false" ? "" : "none";
            essaySection.style.display = value === "essay" ? "" : "none";

            if (value === "single_choice") {
                card.querySelectorAll("input.correct-answer").forEach((checkbox) => {
                    checkbox.addEventListener("change", () => {
                        if (checkbox.checked) {
                            card.querySelectorAll("input.correct-answer").forEach((other) => {
                                if (other !== checkbox) {
                                    other.checked = false;
                                }
                            });
                        }
                    });
                });
            }
        };

        typeSelect.addEventListener("change", handleTypeChange);
        handleTypeChange();

        const pointsInput = card.querySelector("input[name$='[points]']");
        pointsInput?.addEventListener("input", renumberQuestions);

        const removeBtn = card.querySelector(".remove-question");
        removeBtn?.addEventListener("click", () => {
            card.remove();
            renumberQuestions();
        });
    };

    container.querySelectorAll(".question-card").forEach(bindCardEvents);

    addBtn?.addEventListener("click", () => {
        if (!template?.content) {
            return;
        }
        const index = container.querySelectorAll(".question-card").length;
        const clone = template.content.cloneNode(true);
        clone.querySelector(".question-card").dataset.questionIndex = index;
        clone.querySelector(".question-number").textContent = `Câu ${index + 1}`;

        clone.querySelectorAll("textarea, input, select").forEach((input) => {
            const name = input.getAttribute("name");
            if (name) {
                input.setAttribute("name", name.replace(/__INDEX__/g, index));
            }
        });

        container.appendChild(clone);
        const newCard = container.querySelectorAll(".question-card")[container.querySelectorAll(".question-card").length - 1];
        bindCardEvents(newCard);
        renumberQuestions();
        newCard.scrollIntoView({ behavior: "smooth", block: "start" });
    });

    form.addEventListener("submit", (event) => {
        const cards = container.querySelectorAll(".question-card");
        if (cards.length === 0) {
            event.preventDefault();
            alert("Vui lòng thêm ít nhất một câu hỏi trước khi lưu.");
            return;
        }
    });

    renumberQuestions();
}

function setSelectValue(select, value) {
    if (!select) {
        return;
    }
    const option = Array.from(select.options).find((opt) => opt.value === value);
    if (option) {
        option.selected = true;
    }
}
