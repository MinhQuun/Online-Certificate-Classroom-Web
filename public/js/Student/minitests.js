document.addEventListener("DOMContentLoaded", () => {
    const config = document.getElementById("studentMiniTestConfig");
    if (!config) {
        return;
    }

    initMiniTestAttempt(config);
});

function initMiniTestAttempt(configEl) {
    const resultId = configEl.dataset.resultId;
    const autosaveTemplate = configEl.dataset.autosaveTemplate;
    const uploadTemplate = configEl.dataset.uploadTemplate;
    const countdownValue = Number.parseInt(configEl.dataset.countdown ?? "", 10);

    const form = document.getElementById("attemptForm");
    const submitBtn = document.getElementById("submitAttemptBtn");
    const questionCards = Array.from(document.querySelectorAll(".question-card"));
    const csrfToken =
        document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") ?? "";

    if (!csrfToken) {
        console.error("Missing CSRF token.");
        return;
    }

    if (!resultId || !autosaveTemplate) {
        return;
    }

    const autosaveTimeouts = new Map();
    const progressLinks = new Map();

    document.querySelectorAll("[data-question-link]").forEach((link) => {
        const questionId = link.dataset.questionLink;
        if (!questionId) {
            return;
        }
        progressLinks.set(questionId, link);
        link.addEventListener("click", (event) => {
            event.preventDefault();
            const target = document.getElementById(`question-${questionId}`);
            target?.scrollIntoView({ behavior: "smooth", block: "start" });
        });
    });

    const buildUrl = (template, questionId) =>
        template.replace("__QUESTION__", questionId);

    const updateProgressIndicator = (questionId) => {
        const card = questionCards.find(
            (item) => item.dataset.questionId === questionId
        );
        const link = progressLinks.get(questionId);
        if (!card || !link) {
            return;
        }

        if (isCardAnswered(card)) {
            link.classList.add("is-answered");
        } else {
            link.classList.remove("is-answered");
        }
    };

    const handleAutosave = (questionId, payload, statusEl) => {
        if (!questionId) {
            return;
        }

        if (autosaveTimeouts.has(questionId)) {
            clearTimeout(autosaveTimeouts.get(questionId));
        }

        autosaveTimeouts.set(
            questionId,
            window.setTimeout(() => {
                sendAutosave(questionId, payload, statusEl);
            }, 500)
        );
    };

    const sendAutosave = async (questionId, payload, statusEl) => {
        const url = buildUrl(autosaveTemplate, questionId);

        try {
            setStatus(statusEl, "Đang lưu...", "saving");
            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ answer: payload }),
            });

            if (!response.ok) {
                throw new Error(await response.text());
            }

            setStatus(statusEl, "Đã lưu", "saved");
        } catch (error) {
            console.error(error);
            setStatus(statusEl, "Lưu thất bại", "error");
        }
    };

    const uploadSpeaking = async (questionId, file, statusEl) => {
        const url = buildUrl(uploadTemplate, questionId);
        const formData = new FormData();
        formData.append("audio", file);

        try {
            setStatus(statusEl, "Đang tải lên...", "saving");
            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
                body: formData,
            });

            if (!response.ok) {
                throw new Error(await response.text());
            }

            setStatus(statusEl, "Tải lên thành công", "saved");
            updateProgressIndicator(questionId);
        } catch (error) {
            console.error(error);
            setStatus(statusEl, "Tải lên thất bại", "error");
        }
    };

    questionCards.forEach((card) => {
        const questionId = card.dataset.questionId;
        const type = card.dataset.questionType;
        const statusEl = card.querySelector("[data-status]");

        if (!questionId) {
            return;
        }

        if (type === "multiple_choice" || type === "single_choice" || type === "true_false") {
            const inputs = card.querySelectorAll(".answer-input");
            inputs.forEach((input) => {
                input.addEventListener("change", () => {
                    const selected = Array.from(
                        card.querySelectorAll(".answer-input:checked")
                    ).map((el) => el.value);
                    const payload =
                        type === "multiple_choice" ? selected : selected[0] ?? null;
                    handleAutosave(questionId, payload, statusEl);
                    updateProgressIndicator(questionId);
                });
            });
        } else {
            const textarea = card.querySelector(".answer-text");
            if (textarea) {
                textarea.addEventListener("input", () => {
                    handleAutosave(questionId, textarea.value, statusEl);
                    updateProgressIndicator(questionId);
                });
            }

            if (card.dataset.speaking === "1") {
                const fileInput = card.querySelector(".speaking-file-input");
                fileInput?.addEventListener("change", () => {
                    const file = fileInput.files?.[0];
                    if (file) {
                        uploadSpeaking(questionId, file, statusEl);
                    }
                });
            }
        }

        updateProgressIndicator(questionId);
    });

    if ("IntersectionObserver" in window && progressLinks.size > 0) {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    const id = entry.target.dataset.questionId;
                    const link = id ? progressLinks.get(id) : undefined;
                    if (!link) {
                        return;
                    }

                    if (entry.isIntersecting) {
                        link.classList.add("is-active");
                    } else {
                        link.classList.remove("is-active");
                    }
                });
            },
            {
                rootMargin: "-45% 0px -45% 0px",
                threshold: 0.15,
            }
        );

        questionCards.forEach((card) => observer.observe(card));
    }

    form?.addEventListener("submit", (event) => {
        event.preventDefault();
        if (!confirm("Bạn chắc chắn muốn nộp bài? Sau khi nộp, bạn sẽ không thể thay đổi câu trả lời.")) {
            return;
        }

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Đang nộp...';
        }

        form.submit();
    });

    if (!Number.isNaN(countdownValue) && countdownValue > 0) {
        startCountdown(countdownValue, document.getElementById("countdown"), () => {
            setTimeout(() => form?.submit(), 400);
        });
    }
}

function isCardAnswered(card) {
    const type = card.dataset.questionType;

    if (type === "multiple_choice" || type === "single_choice" || type === "true_false") {
        return card.querySelectorAll(".answer-input:checked").length > 0;
    }

    if (card.dataset.speaking === "1") {
        const hasLocalFile =
            card.querySelector(".speaking-file-input")?.files?.length ?? 0;
        const hasUploadedAudio = Boolean(card.querySelector(".current-audio audio"));
        return hasLocalFile > 0 || hasUploadedAudio;
    }

    const textareaValue = card.querySelector(".answer-text")?.value ?? "";
    return textareaValue.trim().length > 0;
}

function startCountdown(seconds, displayEl, onExpire) {
    let remaining = seconds;

    const updateDisplay = () => {
        if (!displayEl) {
            return;
        }
        const minutes = Math.floor(remaining / 60)
            .toString()
            .padStart(2, "0");
        const secs = (remaining % 60).toString().padStart(2, "0");
        displayEl.textContent = `${minutes}:${secs}`;
    };

    updateDisplay();

    const interval = window.setInterval(() => {
        remaining -= 1;
        if (remaining <= 0) {
            window.clearInterval(interval);
            updateDisplay();
            onExpire?.();
        } else {
            updateDisplay();
        }
    }, 1000);
}

function setStatus(el, message, state) {
    if (!el) {
        return;
    }
    el.textContent = message;
    el.dataset.state = state;
}
