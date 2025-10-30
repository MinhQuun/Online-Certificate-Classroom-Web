document.addEventListener("DOMContentLoaded", function () {
    // Get config from hidden element
    const config = document.getElementById("teacherMinitestsConfig");
    if (config) {
        const csrf = config.dataset.csrf;
        const updateRouteTemplate = config.dataset.updateRoute;
        const materialRouteTemplate = config.dataset.materialRoute;
        const courseId = config.dataset.courseId;

        // Course selector
        const courseSelector = document.getElementById("courseSelector");
        if (courseSelector) {
            courseSelector.addEventListener("change", function () {
                const selectedCourseId = this.value;
                const baseUrl = this.dataset.baseUrl;
                if (selectedCourseId) {
                    window.location.href = `${baseUrl}?course=${selectedCourseId}`;
                }
            });
        }

        // Edit Mini-Test Button
        document.querySelectorAll(".edit-minitest-btn").forEach((button) => {
            button.addEventListener("click", function (e) {
                e.preventDefault();

                const miniTestId = this.dataset.minitestId;
                const courseId = this.dataset.courseId;
                const chapterId = this.dataset.chapterId;
                const title = this.dataset.title;
                const skillType = this.dataset.skillType;
                const order = this.dataset.order;
                const maxScore = this.dataset.maxScore;
                const weight = this.dataset.weight;
                const timeLimit = this.dataset.timeLimit;
                const attempts = this.dataset.attempts;
                const isActive = this.dataset.isActive === "1";

                // Set form action
                const form = document.getElementById("editMiniTestForm");
                form.action = updateRouteTemplate.replace("__ID__", miniTestId);

                // Fill form fields
                document.getElementById("edit_course_id").value = courseId;
                document.getElementById("edit_chapter_id").value = chapterId;
                document.getElementById("edit_title").value = title;
                document.getElementById("edit_skill_type").value =
                    skillType || "";
                document.getElementById("edit_order").value = order;
                document.getElementById("edit_max_score").value = maxScore;
                document.getElementById("edit_weight").value = weight;
                document.getElementById("edit_time_limit").value = timeLimit;
                document.getElementById("edit_attempts").value = attempts;
                document.getElementById("edit_is_active").checked = isActive;

                // Show modal
                const modal = new bootstrap.Modal(
                    document.getElementById("editMiniTestModal")
                );
                modal.show();
            });
        });

        // Add Material Button
        document.querySelectorAll(".add-material-btn").forEach((button) => {
            button.addEventListener("click", function (e) {
                e.preventDefault();

                const miniTestId = this.dataset.minitestId;
                const miniTestTitle = this.dataset.minitestTitle;

                // Set mini-test info
                document.getElementById("materialMiniTestTitle").textContent =
                    miniTestTitle;

                // Set form action
                const form = document.getElementById("addMaterialForm");
                form.action = materialRouteTemplate.replace(
                    "__ID__",
                    miniTestId
                );

                // Reset form
                form.reset();
                document.getElementById("source_file").checked = true;
                document.getElementById("url_input_section").style.display =
                    "none";
                document.getElementById("file_upload_section").style.display =
                    "block";

                // Show modal
                const modal = new bootstrap.Modal(
                    document.getElementById("addMaterialModal")
                );
                modal.show();
            });
        });

        // Handle material source type switch
        const addMaterialForm = document.getElementById("addMaterialForm");
        if (addMaterialForm) {
            addMaterialForm
                .querySelectorAll('input[name="source_type"]')
                .forEach((radio) => {
                    radio.addEventListener("change", function () {
                        document.getElementById(
                            "file_upload_section"
                        ).style.display =
                            this.value === "file" ? "block" : "none";
                        document.getElementById(
                            "url_input_section"
                        ).style.display =
                            this.value === "url" ? "block" : "none";
                    });
                });

            addMaterialForm.addEventListener("submit", async function (e) {
                e.preventDefault();

                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Đang thêm...';

                const formData = new FormData(this);

                try {
                    const response = await fetch(this.action, {
                        method: "POST",
                        body: formData,
                        headers: {
                            "X-CSRF-TOKEN": csrf,
                        },
                    });

                    const result = await response.json();

                    if (result.success) {
                        showToast("Thành công", result.message, "success");
                        location.reload(); // Reload to update materials list
                    } else {
                        showToast(
                            "Lỗi",
                            result.error || "Không thể thêm tài liệu",
                            "error"
                        );
                    }
                } catch (error) {
                    console.error("Error:", error);
                    showToast(
                        "Lỗi",
                        "Có lỗi xảy ra khi thêm tài liệu",
                        "error"
                    );
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML =
                        '<i class="bi bi-check-circle me-2"></i> Thêm tài liệu';
                }
            });
        }

        // Handle create modal chapter selection
        document
            .querySelectorAll('[data-bs-target="#createMiniTestModal"]')
            .forEach((button) => {
                button.addEventListener("click", function () {
                    const chapterId = this.dataset.chapterId;
                    if (chapterId) {
                        setTimeout(() => {
                            document.getElementById("create_chapter_id").value =
                                chapterId;
                        }, 100);
                    }
                });
            });

        // Show toast notification
        function showToast(title, message, type = "info") {
            const toastContainer = getOrCreateToastContainer();

            const toastId = "toast-" + Date.now();
            const bgClass =
                type === "success"
                    ? "bg-success"
                    : type === "error"
                    ? "bg-danger"
                    : "bg-info";

            const toastHtml = `
                <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <strong>${title}</strong><br>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;

            toastContainer.insertAdjacentHTML("beforeend", toastHtml);

            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
            toast.show();

            toastElement.addEventListener("hidden.bs.toast", function () {
                this.remove();
            });
        }

        // Get or create toast container
        function getOrCreateToastContainer() {
            let container = document.querySelector(".toast-container");
            if (!container) {
                container = document.createElement("div");
                container.className =
                    "toast-container position-fixed top-0 end-0 p-3";
                container.style.zIndex = "9999";
                document.body.appendChild(container);
            }
            return container;
        }

        // Update file input label when file is selected
        const fileInput = document.getElementById("material_file");
        if (fileInput) {
            fileInput.addEventListener("change", function () {
                const fileName = this.files[0]?.name;
                if (fileName) {
                    const label = this.nextElementSibling;
                    if (label && label.tagName === "SMALL") {
                        label.textContent = `Đã chọn: ${fileName}`;
                    }
                }
            });
        }

        // Smooth scroll to mini-test after page load (if fragment exists)
        if (window.location.hash) {
            setTimeout(() => {
                const element = document.querySelector(window.location.hash);
                if (element) {
                    element.scrollIntoView({
                        behavior: "smooth",
                        block: "center",
                    });
                    element.style.animation = "highlight 2s ease-in-out";
                }
            }, 500);
        }

        // Add highlight animation
        const style = document.createElement("style");
        style.textContent = `
            @keyframes highlight {
                0%, 100% { background-color: transparent; }
                50% { background-color: rgba(103, 58, 183, 0.1); }
            }
        `;
        document.head.appendChild(style);
    }

    // Questions Form Logic
    const questionsForm = document.getElementById("questionsForm");
    if (questionsForm) {
        const questionsContainer =
            document.getElementById("questionsContainer");
        const addQuestionBtn = document.getElementById("addQuestionBtn");
        const questionCountSpan = document.getElementById("questionCount");
        const submitBtn = document.getElementById("submitBtn");

        let questionIndex =
            questionsContainer.querySelectorAll(".question-card").length;

        // Add new question
        addQuestionBtn.addEventListener("click", function () {
            const newQuestion = createQuestionCard(questionIndex);
            questionsContainer.appendChild(newQuestion);
            questionIndex++;
            updateQuestionNumbers();
            updateQuestionCount();

            // Smooth scroll to new question
            newQuestion.scrollIntoView({ behavior: "smooth", block: "center" });
            newQuestion.style.animation = "slideIn 0.3s ease-out";
        });

        // Delete question
        questionsContainer.addEventListener("click", function (e) {
            if (e.target.closest(".delete-question-btn")) {
                const questionCard = e.target.closest(".question-card");
                const questionCount =
                    questionsContainer.querySelectorAll(
                        ".question-card"
                    ).length;

                if (questionCount <= 1) {
                    showToast(
                        "Cảnh báo",
                        "Phải có ít nhất 1 câu hỏi",
                        "warning"
                    );
                    return;
                }

                if (confirm("Bạn có chắc muốn xóa câu hỏi này?")) {
                    questionCard.style.animation = "slideOut 0.3s ease-out";
                    setTimeout(() => {
                        questionCard.remove();
                        reindexQuestions();
                        updateQuestionNumbers();
                        updateQuestionCount();
                    }, 300);
                }
            }
        });

        // Handle image/audio upload preview
        questionsContainer.addEventListener("change", function (e) {
            if (e.target.classList.contains("image-input")) {
                handleImagePreview(e.target);
            } else if (e.target.classList.contains("audio-input")) {
                handleAudioPreview(e.target);
            }
        });

        // Form submission
        questionsForm.addEventListener("submit", async function (e) {
            e.preventDefault();

            // Validate
            if (!validateForm()) {
                return;
            }

            const formData = new FormData(this);

            // Show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';

            try {
                const response = await fetch(
                    this.action || window.location.href,
                    {
                        method: "POST",
                        body: formData,
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                        },
                    }
                );

                const result = await response.json();

                if (result.success) {
                    showToast("Thành công", result.message, "success");

                    // Redirect after 1 second
                    setTimeout(() => {
                        const courseId =
                            new URLSearchParams(window.location.search).get(
                                "course"
                            ) || "{{ $miniTest->maKH }}";
                        window.location.href = `/teacher/minitests?course=${courseId}`;
                    }, 1000);
                } else {
                    showToast(
                        "Lỗi",
                        result.error || "Không thể lưu câu hỏi",
                        "error"
                    );
                    submitBtn.disabled = false;
                    submitBtn.innerHTML =
                        '<i class="bi bi-check-circle me-2"></i>Lưu tất cả';
                }
            } catch (error) {
                console.error("Error:", error);
                showToast(
                    "Lỗi",
                    "Có lỗi xảy ra khi lưu câu hỏi. Vui lòng thử lại.",
                    "error"
                );
                submitBtn.disabled = false;
                submitBtn.innerHTML =
                    '<i class="bi bi-check-circle me-2"></i>Lưu tất cả';
            }
        });
    }

    // Create new question card
    function createQuestionCard(index) {
        const template = document
            .getElementById("questionTemplate")
            .content.cloneNode(true);

        // Replace placeholders
        const html = template.innerHTML.replace(/__INDEX__/g, index);
        template.innerHTML = html;

        const card = template.firstElementChild;

        // Add event listeners
        card.querySelector(".delete-question-btn").addEventListener(
            "click",
            deleteQuestion
        );
        card.querySelector(".question-type-select").addEventListener(
            "change",
            toggleQuestionType
        );
        card.querySelector(".image-input").addEventListener(
            "change",
            function (e) {
                handleImagePreview(this);
            }
        );
        card.querySelector(".audio-input").addEventListener(
            "change",
            function (e) {
                handleAudioPreview(this);
            }
        );

        return card;
    }

    // Delete question handler
    function deleteQuestion(e) {
        const card = e.target.closest(".question-card");
        card.remove();
        reindexQuestions();
        updateQuestionNumbers();
        updateQuestionCount();
    }

    // Toggle question type
    function toggleQuestionType(e) {
        const card = e.target.closest(".question-card");
        const mcSection = card.querySelector(".multiple-choice-section");
        const essaySection = card.querySelector(".essay-section");

        if (e.target.value === "multiple_choice") {
            mcSection.style.display = "block";
            essaySection.style.display = "none";
        } else {
            mcSection.style.display = "none";
            essaySection.style.display = "block";
        }
    }

    // Handle image preview
    function handleImagePreview(input) {
        const file = input.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            let preview = input
                .closest(".question-card")
                .querySelector(".image-preview");
            if (!preview) {
                preview = document.createElement("img");
                preview.className = "image-preview mt-2 rounded img-fluid";
                preview.style.maxHeight = "200px";
                input.closest(".form-group").appendChild(preview);
            }
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    // Handle audio preview
    function handleAudioPreview(input) {
        const file = input.files[0];
        if (!file) return;

        let preview = input
            .closest(".question-card")
            .querySelector(".audio-preview");
        if (!preview) {
            preview = document.createElement("audio");
            preview.className = "audio-preview mt-2 w-100";
            preview.controls = true;
            input.closest(".form-group").appendChild(preview);
        }
        preview.src = URL.createObjectURL(file);
    }

    // Reindex questions
    function reindexQuestions() {
        const cards = questionsContainer.querySelectorAll(".question-card");
        cards.forEach((card, idx) => {
            card.dataset.index = idx;

            // Update names
            const inputs = card.querySelectorAll("[name]");
            inputs.forEach((input) => {
                input.name = input.name.replace(/\[\d+\]/, `[${idx}]`);
            });
        });
    }

    // Update question numbers
    function updateQuestionNumbers() {
        const numbers = questionsContainer.querySelectorAll(".question-number");
        numbers.forEach((num, idx) => {
            num.textContent = `Câu ${idx + 1}`;
        });
    }

    // Update question count
    function updateQuestionCount() {
        const count =
            questionsContainer.querySelectorAll(".question-card").length;
        questionCountSpan.textContent = count;
    }

    // Validate form
    function validateForm() {
        let isValid = true;
        const questions = questionsContainer.querySelectorAll(".question-card");

        if (questions.length === 0) {
            showToast("Lỗi", "Phải có ít nhất 1 câu hỏi", "error");
            return false;
        }

        questions.forEach((question, index) => {
            const content = question.querySelector(
                'textarea[name*="[content]"]'
            );
            if (!content.value.trim()) {
                showToast(
                    "Lỗi",
                    `Câu ${index + 1}: Nội dung câu hỏi chưa được nhập`,
                    "error"
                );
                content.focus();
                isValid = false;
                return;
            }

            const points = question.querySelector('input[name*="[points]"]');
            if (!points.value || parseFloat(points.value) <= 0) {
                showToast(
                    "Lỗi",
                    `Câu ${index + 1}: Điểm phải lớn hơn 0`,
                    "error"
                );
                points.focus();
                isValid = false;
                return;
            }

            const type = question.querySelector('select[name*="[type]"]').value;

            if (type === "multiple_choice") {
                const answers = question.querySelectorAll(
                    'input[name*="[answers]"][name*="[text]"]'
                );
                let hasEmptyAnswer = false;
                answers.forEach((answer, answerIndex) => {
                    if (!answer.value.trim()) {
                        showToast(
                            "Lỗi",
                            `Câu ${index + 1}: Đáp án ${String.fromCharCode(
                                65 + answerIndex
                            )} chưa được nhập`,
                            "error"
                        );
                        answer.focus();
                        hasEmptyAnswer = true;
                        isValid = false;
                        return;
                    }
                });

                if (hasEmptyAnswer) return;

                const correctAnswer = question.querySelector(
                    'input[name*="[correct_answer]"]:checked'
                );
                if (!correctAnswer) {
                    showToast(
                        "Lỗi",
                        `Câu ${index + 1}: Chưa chọn đáp án đúng`,
                        "error"
                    );
                    isValid = false;
                    return;
                }
            }
        });

        return isValid;
    }

    // Show toast notification
    function showToast(title, message, type = "info") {
        const toastContainer = getOrCreateToastContainer();

        const toastId = "toast-" + Date.now();
        const bgClass =
            type === "success"
                ? "bg-success"
                : type === "error"
                ? "bg-danger"
                : type === "warning"
                ? "bg-warning"
                : "bg-info";

        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${title}</strong><br>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML("beforeend", toastHtml);

        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
        toast.show();

        toastElement.addEventListener("hidden.bs.toast", function () {
            this.remove();
        });
    }

    // Get or create toast container
    function getOrCreateToastContainer() {
        let container = document.querySelector(".toast-container");
        if (!container) {
            container = document.createElement("div");
            container.className =
                "toast-container position-fixed top-0 end-0 p-3";
            container.style.zIndex = "9999";
            document.body.appendChild(container);
        }
        return container;
    }

    // Add animations
    const style = document.createElement("style");
    style.textContent = `
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(-20px);
            }
        }
    `;
    document.head.appendChild(style);
});
