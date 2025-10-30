/**
 * Khởi tạo trang làm bài mini-test.
 * Hàm này được gọi từ file Blade để truyền các biến động từ server.
 * @param {number} timeLimitMin - Thời gian làm bài (phút).
 * @param {number} totalQuestions - Tổng số câu hỏi.
 */
function initMiniTest(timeLimitMin, totalQuestions) {
    // 1. Timer functionality
    let timeRemaining = timeLimitMin * 60; // seconds
    const timerDisplay = document.getElementById("timer");

    function updateTimer() {
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        timerDisplay.textContent = `${minutes}:${seconds
            .toString()
            .padStart(2, "0")}`;

        // Color warnings
        timerDisplay.classList.remove("timer-warning", "timer-danger");
        if (timeRemaining <= 60) {
            timerDisplay.classList.add("timer-danger");
        } else if (timeRemaining <= 300) {
            timerDisplay.classList.add("timer-warning");
        }

        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            alert("Hết giờ! Bài làm sẽ được tự động nộp.");
            document.getElementById("testForm").submit();
        }

        timeRemaining--;
    }

    const timerInterval = setInterval(updateTimer, 1000);
    updateTimer(); // Gọi ngay lần đầu để hiển thị

    // 2. Progress tracking
    const questionInputs = document.querySelectorAll(".question-input");
    const answeredCount = document.getElementById("answeredCount");
    const progressBar = document.getElementById("progressBar");
    const questionNavItems = document.querySelectorAll(".question-nav-item");

    function updateProgress() {
        let answered = 0;
        const answeredQuestions = new Set();

        questionInputs.forEach((input) => {
            if (input.type === "radio" && input.checked) {
                answeredQuestions.add(input.dataset.question);
            } else if (input.type === "textarea" && input.value.trim() !== "") {
                answeredQuestions.add(input.dataset.question);
            }
        });

        answered = answeredQuestions.size;

        if (answeredCount) {
            answeredCount.textContent = answered;
        }

        const percentage = Math.round((answered / totalQuestions) * 100);

        if (progressBar) {
            progressBar.style.width = percentage + "%";
        }

        // Update progress percent text
        const progressPercentEl = document.getElementById("progressPercent");
        if (progressPercentEl) {
            progressPercentEl.textContent = percentage;
        }

        // Update nav items
        questionNavItems.forEach((item) => {
            const questionId = item.dataset.question;
            if (answeredQuestions.has(questionId)) {
                item.classList.add("answered");
            } else {
                item.classList.remove("answered");
            }
        });
    }

    questionInputs.forEach((input) => {
        input.addEventListener("change", updateProgress);
        if (input.type === "textarea") {
            input.addEventListener("input", updateProgress);
        }
    });

    // 3. Submit confirmation
    const submitBtn = document.getElementById("submitBtn");
    if (submitBtn) {
        submitBtn.addEventListener("click", function () {
            const answeredSet = new Set();
            questionInputs.forEach((input) => {
                if (input.type === "radio" && input.checked) {
                    answeredSet.add(input.dataset.question);
                } else if (
                    input.type === "textarea" &&
                    input.value.trim() !== ""
                ) {
                    answeredSet.add(input.dataset.question);
                }
            });

            const answered = answeredSet.size;
            const unanswered = totalQuestions - answered;

            let message = "Bạn có chắc chắn muốn nộp bài?";
            if (unanswered > 0) {
                message += `\n\nBạn còn ${unanswered} câu chưa trả lời.`;
            }

            if (confirm(message)) {
                this.disabled = true;
                this.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Đang nộp bài...';
                document.getElementById("testForm").submit();
            }
        });
    }

    // 4. Smooth scroll for question navigation
    document.querySelectorAll(".question-nav-item").forEach((item) => {
        item.addEventListener("click", function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute("href"));
            if (target) {
                const offset = 120; // Account for sticky header
                const targetPosition = target.offsetTop - offset;
                window.scrollTo({
                    top: targetPosition,
                    behavior: "smooth",
                });
            }
        });
    });

    // 5. Prevent accidental page leave
    window.addEventListener("beforeunload", function (e) {
        // Chỉ chặn khi thời gian vẫn còn
        if (timeRemaining > 0 && totalQuestions > 0) {
            e.preventDefault();
            e.returnValue = ""; // Bắt buộc cho một số trình duyệt
        }
    });
}
