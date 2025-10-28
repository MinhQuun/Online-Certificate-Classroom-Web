/**
 * Teacher Mini-Test Questions Management JavaScript
 * Google Form style dynamic question builder
 */

document.addEventListener('DOMContentLoaded', function () {
    const questionsContainer = document.getElementById('questionsContainer');
    const addQuestionBtn = document.getElementById('addQuestionBtn');
    const questionsForm = document.getElementById('questionsForm');
    const questionCountSpan = document.getElementById('questionCount');
    const submitBtn = document.getElementById('submitBtn');

    let questionIndex = questionsContainer.querySelectorAll('.question-card').length;

    // Add new question
    addQuestionBtn.addEventListener('click', function () {
        const newQuestion = createQuestionCard(questionIndex);
        questionsContainer.appendChild(newQuestion);
        questionIndex++;
        updateQuestionNumbers();
        updateQuestionCount();
        
        // Smooth scroll to new question
        newQuestion.scrollIntoView({ behavior: 'smooth', block: 'center' });
        newQuestion.style.animation = 'slideIn 0.3s ease-out';
    });

    // Delete question
    questionsContainer.addEventListener('click', function (e) {
        if (e.target.closest('.delete-question-btn')) {
            const questionCard = e.target.closest('.question-card');
            const questionCount = questionsContainer.querySelectorAll('.question-card').length;
            
            if (questionCount <= 1) {
                showToast('Cảnh báo', 'Phải có ít nhất 1 câu hỏi', 'warning');
                return;
            }

            if (confirm('Bạn có chắc muốn xóa câu hỏi này?')) {
                questionCard.style.animation = 'slideOut 0.3s ease-out';
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
    questionsContainer.addEventListener('change', function (e) {
        if (e.target.classList.contains('image-input')) {
            handleImagePreview(e.target);
        } else if (e.target.classList.contains('audio-input')) {
            handleAudioPreview(e.target);
        }
    });

    // Form submission
    questionsForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Validate
        if (!validateForm()) {
            return;
        }

        const formData = new FormData(this);
        
        // Show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';

        try {
            const response = await fetch(this.action || window.location.href, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await response.json();

            if (result.success) {
                showToast('Thành công', result.message, 'success');
                
                // Redirect after 1 second
                setTimeout(() => {
                    const courseId = new URLSearchParams(window.location.search).get('course');
                    window.location.href = '/teacher/minitests' + (courseId ? '?course=' + courseId : '');
                }, 1000);
            } else {
                showToast('Lỗi', result.error || 'Không thể lưu câu hỏi', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Lỗi', 'Có lỗi xảy ra khi lưu câu hỏi', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i> Lưu tất cả câu hỏi';
        }
    });

    // Create question card
    function createQuestionCard(index) {
        const template = document.getElementById('questionTemplate');
        const clone = document.createElement('div');
        
        let answersHtml = '';
        ['A', 'B', 'C', 'D'].forEach((letter, i) => {
            answersHtml += `
                <div class="answer-item">
                    <input type="radio" 
                           name="questions[${index}][correct_answer]" 
                           value="${letter}" 
                           id="q${index}_${letter}"
                           ${i === 0 ? 'checked' : ''} 
                           required>
                    <label for="q${index}_${letter}" class="answer-label">${letter}</label>
                    <input type="hidden" 
                           name="questions[${index}][answers][${i}][order]" 
                           value="${letter}">
                    <input type="text" 
                           name="questions[${index}][answers][${i}][text]" 
                           class="form-control-gform" 
                           placeholder="Nhập đáp án ${letter}" 
                           required>
                </div>
            `;
        });

        clone.innerHTML = template.innerHTML
            .replace(/__INDEX__/g, index)
            .replace(/__NUMBER__/g, index + 1)
            .replace(/__ANSWERS__/g, answersHtml);

        return clone.firstElementChild;
    }

    // Reindex questions after deletion
    function reindexQuestions() {
        const questions = questionsContainer.querySelectorAll('.question-card');
        questions.forEach((question, newIndex) => {
            question.dataset.questionIndex = newIndex;
            
            // Update all name attributes
            question.querySelectorAll('[name]').forEach(input => {
                const name = input.getAttribute('name');
                const newName = name.replace(/questions\[\d+\]/, `questions[${newIndex}]`);
                input.setAttribute('name', newName);
            });

            // Update radio IDs and for attributes
            question.querySelectorAll('input[type="radio"]').forEach(radio => {
                const oldId = radio.getAttribute('id');
                const newId = oldId.replace(/q\d+_/, `q${newIndex}_`);
                radio.setAttribute('id', newId);
                
                const label = question.querySelector(`label[for="${oldId}"]`);
                if (label) {
                    label.setAttribute('for', newId);
                }
            });
        });
        
        questionIndex = questions.length;
    }

    // Update question numbers
    function updateQuestionNumbers() {
        const questions = questionsContainer.querySelectorAll('.question-card');
        questions.forEach((question, index) => {
            const numberSpan = question.querySelector('.question-number');
            if (numberSpan) {
                numberSpan.textContent = `Câu ${index + 1}`;
            }
        });
    }

    // Update question count
    function updateQuestionCount() {
        const count = questionsContainer.querySelectorAll('.question-card').length;
        questionCountSpan.textContent = count;
    }

    // Handle image preview
    function handleImagePreview(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            const questionCard = input.closest('.question-card');
            const mediaSection = questionCard.querySelector('.media-section');
            
            reader.onload = function (e) {
                // Remove existing image preview
                const existingPreview = mediaSection.querySelector('.media-preview img');
                if (existingPreview) {
                    existingPreview.parentElement.remove();
                }

                // Create new preview
                const preview = document.createElement('div');
                preview.className = 'media-preview';
                preview.innerHTML = `
                    <img src="${e.target.result}" alt="Image preview">
                    <button type="button" class="remove-media-btn">
                        <i class="bi bi-x"></i>
                    </button>
                `;
                
                mediaSection.insertBefore(preview, mediaSection.querySelector('.media-buttons'));

                // Add remove handler
                preview.querySelector('.remove-media-btn').addEventListener('click', function () {
                    input.value = '';
                    preview.remove();
                });
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Handle audio preview
    function handleAudioPreview(input) {
        if (input.files && input.files[0]) {
            const questionCard = input.closest('.question-card');
            const mediaSection = questionCard.querySelector('.media-section');
            
            // Remove existing audio preview
            const existingPreview = mediaSection.querySelector('.media-preview audio');
            if (existingPreview) {
                existingPreview.parentElement.remove();
            }

            // Create new preview
            const preview = document.createElement('div');
            preview.className = 'media-preview';
            const audioUrl = URL.createObjectURL(input.files[0]);
            preview.innerHTML = `
                <audio controls src="${audioUrl}"></audio>
                <button type="button" class="remove-media-btn">
                    <i class="bi bi-x"></i>
                </button>
            `;
            
            mediaSection.insertBefore(preview, mediaSection.querySelector('.media-buttons'));

            // Add remove handler
            preview.querySelector('.remove-media-btn').addEventListener('click', function () {
                input.value = '';
                URL.revokeObjectURL(audioUrl);
                preview.remove();
            });
        }
    }

    // Validate form
    function validateForm() {
        const questions = questionsContainer.querySelectorAll('.question-card');
        
        if (questions.length === 0) {
            showToast('Lỗi', 'Phải có ít nhất 1 câu hỏi', 'error');
            return false;
        }

        let isValid = true;

        questions.forEach((question, index) => {
            const questionText = question.querySelector('textarea[name*="[question_text]"]');
            if (!questionText.value.trim()) {
                showToast('Lỗi', `Câu ${index + 1}: Chưa nhập nội dung câu hỏi`, 'error');
                questionText.focus();
                isValid = false;
                return;
            }

            const answers = question.querySelectorAll('input[name*="[answers]"][name*="[text]"]');
            let hasEmptyAnswer = false;
            answers.forEach((answer, answerIndex) => {
                if (!answer.value.trim()) {
                    showToast('Lỗi', `Câu ${index + 1}: Đáp án ${String.fromCharCode(65 + answerIndex)} chưa được nhập`, 'error');
                    answer.focus();
                    hasEmptyAnswer = true;
                    isValid = false;
                    return;
                }
            });

            if (hasEmptyAnswer) return;

            const correctAnswer = question.querySelector('input[name*="[correct_answer]"]:checked');
            if (!correctAnswer) {
                showToast('Lỗi', `Câu ${index + 1}: Chưa chọn đáp án đúng`, 'error');
                isValid = false;
                return;
            }
        });

        return isValid;
    }

    // Show toast notification
    function showToast(title, message, type = 'info') {
        const toastContainer = getOrCreateToastContainer();
        
        const toastId = 'toast-' + Date.now();
        const bgClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : type === 'warning' ? 'bg-warning' : 'bg-info';
        
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
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
        toast.show();
        
        toastElement.addEventListener('hidden.bs.toast', function () {
            this.remove();
        });
    }

    // Get or create toast container
    function getOrCreateToastContainer() {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
        return container;
    }

    // Add animations
    const style = document.createElement('style');
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
