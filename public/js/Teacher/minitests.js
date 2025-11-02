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

function getSelectedOption(select) {
    if (!select) {
        return null;
    }

    if (typeof select.selectedIndex === "number" && select.selectedIndex >= 0) {
        return select.options[select.selectedIndex] ?? null;
    }

    return null;
}

function resolveSkillMeta(option) {
    if (!option) {
        return { value: "", label: "" };
    }

    const value = option.dataset.skillDefault || "";
    const label = option.dataset.skillLabel || option.textContent?.trim() || "";

    return { value, label };
}

function syncSkillField(skillSelect, skillStatic, meta) {
    if (!skillSelect || !skillStatic) {
        return;
    }

    const isLocked = Boolean(meta.value);

    if (isLocked) {
        const match = Array.from(skillSelect.options).find((opt) => opt.value === meta.value);
        if (match) {
            match.selected = true;
        } else if (skillSelect.options.length > 0) {
            skillSelect.options[0].selected = true;
        }

        const currentOption = skillSelect.options[skillSelect.selectedIndex] ?? null;
        const displayLabel = meta.label || currentOption?.textContent?.trim() || meta.value;
        skillStatic.textContent = displayLabel;
    } else {
        skillStatic.textContent = "";
    }

    skillSelect.classList.toggle("d-none", isLocked);
    skillStatic.classList.toggle("d-none", !isLocked);
    if (isLocked) {
        skillSelect.dataset.skillLocked = "1";
    } else {
        delete skillSelect.dataset.skillLocked;
    }
}

function applyCourseSkill(courseSelect, skillSelect, skillStatic) {
    if (!courseSelect || !skillSelect || !skillStatic) {
        return false;
    }

    const option = getSelectedOption(courseSelect);
    const meta = resolveSkillMeta(option);
    syncSkillField(skillSelect, skillStatic, meta);

    return Boolean(meta.value);
}

function initCreateMiniTestModal() {
    const modal = document.getElementById("createMiniTestModal");
    if (!modal) {
        return;
    }

    const courseSelect = modal.querySelector("select[name='course_id']");
    const chapterSelect = modal.querySelector("select[name='chapter_id']");
    const skillField = modal.querySelector("[data-skill-field]");
    const skillSelect = skillField?.querySelector("[data-skill-select]");
    const skillStatic = skillField?.querySelector("[data-skill-static]");

    const filterChapters = (courseId) => {
        if (!chapterSelect) {
            return;
        }

        Array.from(chapterSelect.options).forEach((option) => {
            const belongs = option.dataset.course === courseId;
            option.hidden = !belongs && option.value !== "";
            if (!belongs && option.selected) {
                option.selected = false;
            }
        });
    };

    const handleCourseChange = () => {
        if (courseSelect) {
            filterChapters(courseSelect.value);
        }
        applyCourseSkill(courseSelect, skillSelect, skillStatic);
    };

    courseSelect?.addEventListener("change", handleCourseChange);

    if (courseSelect?.value) {
        filterChapters(courseSelect.value);
    }

    applyCourseSkill(courseSelect, skillSelect, skillStatic);
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
    const skillSelect = modal.querySelector("#edit_skill_type");
    const skillStatic = skillSelect?.closest("[data-skill-field]")?.querySelector("[data-skill-static]");

    const filterChapters = (courseId) => {
        if (!chapterSelect) {
            return;
        }

        Array.from(chapterSelect.options).forEach((option) => {
            const belongs = option.dataset.course === courseId;
            option.hidden = !belongs && option.value !== "";
            if (!belongs && option.selected) {
                option.selected = false;
            }
        });
    };

    const refreshSkill = () => applyCourseSkill(courseSelect, skillSelect, skillStatic);

    courseSelect?.addEventListener("change", () => {
        if (courseSelect) {
            filterChapters(courseSelect.value);
        }
        refreshSkill();
    });

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
            const locked = refreshSkill();
            if (!locked) {
                setSelectValue(skillSelect, button.dataset.skillType);
            }
            refreshSkill();
            modal.querySelector("#edit_order").value = button.dataset.order || "";
            modal.querySelector("#edit_weight").value = button.dataset.weight || "";
            modal.querySelector("#edit_time_limit").value = button.dataset.timeLimit || "";
            modal.querySelector("#edit_attempts").value = button.dataset.attempts || "";
            modal.querySelector("#edit_is_active").checked = button.dataset.isActive === "1";
        });
    });

    refreshSkill();
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
        const newCard = container.lastElementChild;
        bindCardEvents(newCard);
        renumberQuestions();
        newCard.scrollIntoView({ behavior: "smooth", block: "start" });
    });

    // ===== AJAX SUBMIT - ĐÃ SỬA HOÀN CHỈNH =====
    form.addEventListener("submit", async (event) => {
        event.preventDefault();

        const cards = container.querySelectorAll(".question-card");
        if (cards.length === 0) {
            alert("Vui lòng thêm ít nhất một câu hỏi trước khi lưu.");
            return;
        }

        const formData = new FormData(form);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        if (!csrfToken) {
            alert("Lỗi: Không tìm thấy CSRF token. Vui lòng tải lại trang.");
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Đang lưu...';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                alert(data.message || 'Đã lưu câu hỏi thành công!');
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể lưu câu hỏi. Vui lòng kiểm tra dữ liệu.'));
            }
        } catch (error) {
            console.error('Lỗi AJAX:', error);
            alert('Lỗi kết nối. Vui lòng kiểm tra mạng và thử lại.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
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
