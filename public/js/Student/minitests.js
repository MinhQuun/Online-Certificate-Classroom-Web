document.addEventListener("DOMContentLoaded", () => {
    const config = document.getElementById("studentMiniTestConfig");
    if (config) {
        initAttemptPage(config);
    }
});

function initAttemptPage(configEl) {
    const resultId = configEl.dataset.resultId;
    const countdownValue = parseInt(configEl.dataset.countdown ?? "", 10);
    const autosaveTemplate = configEl.dataset.autosaveTemplate;
    const uploadTemplate = configEl.dataset.uploadTemplate;
    const submitUrl = configEl.dataset.submitUrl;

    const form = document.getElementById("attemptForm");
    const submitBtn = document.getElementById("submitAttemptBtn");
    const questionCards = document.querySelectorAll(".question-card");
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    if (!csrfToken) {
        console.error("Không tìm thấy CSRF token.");
        return;
    }

    if (!resultId || !autosaveTemplate) {
        return;
    }

    const buildUrl = (template, questionId) =>
        template.replace("__QUESTION__", questionId);

    const autosaveTimeouts = {};

    const scheduleAutosave = (questionId, payload, statusEl) => {
        if (autosaveTimeouts[questionId]) {
            clearTimeout(autosaveTimeouts[questionId]);
        }

        autosaveTimeouts[questionId] = setTimeout(() => {
            sendAutosave(questionId, payload, statusEl);
        }, 500);
    };

    const sendAutosave = async (questionId, payload, statusEl) => {
        if (!questionId) return;

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
            setStatus(statusEl, "Lỗi khi lưu", "error");
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
        } catch (error) {
            console.error(error);
            setStatus(statusEl, "Tải lên thất bại", "error");
        }
    };

    questionCards.forEach((card) => {
        const questionId = card.dataset.questionId;
        const type = card.dataset.questionType;
        const statusEl = card.querySelector("[data-status]");

        if (!questionId) return;

        if (type === "multiple_choice" || type === "single_choice") {
            const inputs = card.querySelectorAll(".answer-input");
            inputs.forEach((input) => {
                input.addEventListener("change", () => {
                    const selected = Array.from(
                        card.querySelectorAll(".answer-input:checked")
                    ).map((el) => el.value);
                    const payload =
                        type === "multiple_choice"
                            ? selected
                            : selected[0] ?? null;
                    scheduleAutosave(questionId, payload, statusEl);
                });
            });
        } else if (type === "true_false") {
            const inputs = card.querySelectorAll(".answer-input");
            inputs.forEach((input) =>
                input.addEventListener("change", () => {
                    scheduleAutosave(questionId, input.value, statusEl);
                })
            );
        } else {
            const textarea = card.querySelector(".answer-text");
            if (textarea && textarea.disabled === false) {
                textarea.addEventListener("input", () => {
                    scheduleAutosave(questionId, textarea.value, statusEl);
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
    });

    form?.addEventListener("submit", (event) => {
        event.preventDefault();
        if (!confirm("Bạn chắc chắn muốn nộp bài?")) {
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2"></span>Đang nộp...';
        form.submit();
    });

    if (!isNaN(countdownValue) && countdownValue > 0) {
        startCountdown(countdownValue, document.getElementById("countdown"), () => {
            setTimeout(() => form?.submit(), 500);
        });
    }
}

function startCountdown(seconds, displayEl, onExpire) {
    let remaining = seconds;

    const updateDisplay = () => {
        if (!displayEl) return;
        const minutes = Math.floor(remaining / 60)
            .toString()
            .padStart(2, "0");
        const secs = (remaining % 60).toString().padStart(2, "0");
        displayEl.textContent = `${minutes}:${secs}`;
    };

    updateDisplay();

    const interval = setInterval(() => {
        remaining -= 1;
        if (remaining <= 0) {
            clearInterval(interval);
            updateDisplay();
            onExpire?.();
        } else {
            updateDisplay();
        }
    }, 1000);
}

function setStatus(el, message, state) {
    if (!el) return;
    el.textContent = message;
    el.dataset.state = state;
}
