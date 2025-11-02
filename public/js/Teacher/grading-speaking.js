document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("[data-speaking-form]");

    if (!form) {
        return;
    }

    const cards = Array.from(form.querySelectorAll("[data-speaking-card]"));
    const submitButton = form.querySelector("[data-submit-grades]");
    const submitHint = form.querySelector("[data-submit-hint]");

    if (cards.length === 0) {
        if (submitButton) {
            submitButton.disabled = false;
        }
        if (submitHint) {
            submitHint.textContent = "Không có bản ghi để nghe. Bạn có thể lưu điểm.";
        }
        return;
    }

    cards.forEach((card) => initSpeakingCard(card));
    updateSubmitState();

    function initSpeakingCard(card) {
        const audio = card.querySelector("[data-speaking-audio]");
        const listenedField = card.querySelector("[data-listened-field]");
        const dependentInputs = card.querySelectorAll("[data-requires-listened]");
        const statusBadge = card.querySelector("[data-listen-status]");
        const hint = card.querySelector("[data-listen-hint]");
        const warning = card.querySelector("[data-gate-warning]");
        const markButton = card.querySelector("[data-mark-listened]");
        const durationTarget = card.querySelector("[data-audio-duration]");

        if (!listenedField) {
            return;
        }

        const markListened = () => {
            if (card.dataset.listened === "1") {
                return;
            }

            card.dataset.listened = "1";
            listenedField.value = "1";

            dependentInputs.forEach((input) => {
                input.removeAttribute("disabled");
            });

            warning?.classList.add("d-none");
            hint?.classList.add("d-none");

            if (statusBadge) {
                statusBadge.textContent = "Đã nghe";
                statusBadge.classList.remove("text-bg-warning");
                statusBadge.classList.add("text-bg-success");
            }

            if (markButton) {
                markButton.classList.remove("btn-outline-primary");
                markButton.classList.add("btn-outline-success");
                markButton.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Đã mở khóa';
                markButton.disabled = true;
            }

            updateSubmitState();
        };

        dependentInputs.forEach((input) => {
            input.setAttribute("disabled", "disabled");
        });

        markButton?.addEventListener("click", (event) => {
            event.preventDefault();
            markListened();
        });

        if (audio) {
            audio.addEventListener("loadedmetadata", () => {
                if (!durationTarget) {
                    return;
                }

                if (Number.isFinite(audio.duration) && audio.duration > 0) {
                    durationTarget.textContent = formatSeconds(audio.duration);
                }
            });

            audio.addEventListener("ended", markListened);
            audio.addEventListener("timeupdate", () => {
                if (card.dataset.listened === "1") {
                    return;
                }

                if (Number.isFinite(audio.duration) && audio.duration > 0) {
                    const threshold = Math.min(audio.duration * 0.6, audio.duration - 1);
                    if (audio.currentTime >= threshold) {
                        markListened();
                    }
                } else if (audio.currentTime >= 12) {
                    markListened();
                }
            });
        }

        if (listenedField.value === "1") {
            markListened();
        }
    }

    function updateSubmitState() {
        const allListened = cards.every((card) => {
            const field = card.querySelector("[data-listened-field]");
            return !field || field.value === "1";
        });

        if (submitButton) {
            submitButton.disabled = !allListened;
        }

        if (submitHint) {
            submitHint.textContent = allListened
                ? "Tất cả bản ghi đã được nghe. Bạn có thể lưu điểm."
                : "Nghe toàn bộ các bản ghi để kích hoạt nút lưu điểm.";
        }
    }

    function formatSeconds(durationValue) {
        const totalSeconds = Math.max(0, Math.floor(durationValue));
        const minutes = Math.floor(totalSeconds / 60);
        const seconds = totalSeconds % 60;
        return `${minutes.toString().padStart(2, "0")}:${seconds
            .toString()
            .padStart(2, "0")}`;
    }
});
