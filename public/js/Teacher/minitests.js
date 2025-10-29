/**
 * Teacher Mini-Test Management JavaScript
 * Handles interactions for creating, editing, and managing mini-tests
 */

document.addEventListener('DOMContentLoaded', function () {
    // Get config from hidden element
    const config = document.getElementById('teacherMinitestsConfig');
    if (!config) return;

    const csrf = config.dataset.csrf;
    const updateRouteTemplate = config.dataset.updateRoute;
    const materialRouteTemplate = config.dataset.materialRoute;
    const courseId = config.dataset.courseId;

    // Course selector
    const courseSelector = document.getElementById('courseSelector');
    if (courseSelector) {
        courseSelector.addEventListener('change', function () {
            const selectedCourseId = this.value;
            const baseUrl = this.dataset.baseUrl;
            if (selectedCourseId) {
                window.location.href = `${baseUrl}?course=${selectedCourseId}`;
            }
        });
    }

    // Edit Mini-Test Button
    document.querySelectorAll('.edit-minitest-btn').forEach(button => {
        button.addEventListener('click', function (e) {
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
            const isActive = this.dataset.isActive === '1';

            // Set form action
            const form = document.getElementById('editMiniTestForm');
            form.action = updateRouteTemplate.replace('__ID__', miniTestId);

            // Fill form fields
            document.getElementById('edit_course_id').value = courseId;
            document.getElementById('edit_chapter_id').value = chapterId;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_skill_type').value = skillType || '';
            document.getElementById('edit_order').value = order;
            document.getElementById('edit_max_score').value = maxScore;
            document.getElementById('edit_weight').value = weight;
            document.getElementById('edit_time_limit').value = timeLimit;
            document.getElementById('edit_attempts').value = attempts;
            document.getElementById('edit_is_active').checked = isActive;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('editMiniTestModal'));
            modal.show();
        });
    });

    // Add Material Button
    document.querySelectorAll('.add-material-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            
            const miniTestId = this.dataset.minitestId;
            const miniTestTitle = this.dataset.minitestTitle;

            // Set mini-test info
            document.getElementById('materialMiniTestTitle').textContent = miniTestTitle;

            // Set form data attribute
            document.getElementById('addMaterialForm').dataset.minitestId = miniTestId;

            // Reset form
            document.getElementById('addMaterialForm').reset();
            document.getElementById('file_upload_section').style.display = 'block';
            document.getElementById('url_input_section').style.display = 'none';

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('addMaterialModal'));
            modal.show();
        });
    });

    // Toggle between file upload and URL input
    const sourceFileRadio = document.getElementById('source_file');
    const sourceUrlRadio = document.getElementById('source_url');
    const fileUploadSection = document.getElementById('file_upload_section');
    const urlInputSection = document.getElementById('url_input_section');

    if (sourceFileRadio && sourceUrlRadio) {
        sourceFileRadio.addEventListener('change', function () {
            if (this.checked) {
                fileUploadSection.style.display = 'block';
                urlInputSection.style.display = 'none';
                document.getElementById('material_file').required = true;
                document.getElementById('material_url').required = false;
            }
        });

        sourceUrlRadio.addEventListener('change', function () {
            if (this.checked) {
                fileUploadSection.style.display = 'none';
                urlInputSection.style.display = 'block';
                document.getElementById('material_file').required = false;
                document.getElementById('material_url').required = true;
            }
        });
    }

    // Handle Add Material Form Submission
    const addMaterialForm = document.getElementById('addMaterialForm');
    if (addMaterialForm) {
        addMaterialForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const miniTestId = this.dataset.minitestId;
            const url = materialRouteTemplate.replace('__ID__', miniTestId);

            const formData = new FormData(this);
            
            // Check which source type is selected
            const sourceType = document.querySelector('input[name="source_type"]:checked').value;
            if (sourceType === 'file') {
                formData.delete('url');
            } else {
                formData.delete('file');
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang tải lên...';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Show success message
                    showToast('Thành công', result.message, 'success');
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addMaterialModal'));
                    modal.hide();

                    // Reload page after a short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showToast('Lỗi', result.error || 'Không thể thêm tài liệu', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Lỗi', 'Có lỗi xảy ra khi thêm tài liệu', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                submitBtn.innerHTML = originalText;
            }
        });
    }

    // Pre-fill chapter when clicking "Create First Mini-Test" button
    document.querySelectorAll('[data-bs-target="#createMiniTestModal"]').forEach(button => {
        button.addEventListener('click', function () {
            const chapterId = this.dataset.chapterId;
            if (chapterId) {
                setTimeout(() => {
                    document.getElementById('create_chapter_id').value = chapterId;
                }, 100);
            }
        });
    });

    // Show toast notification
    function showToast(title, message, type = 'info') {
        const toastContainer = getOrCreateToastContainer();
        
        const toastId = 'toast-' + Date.now();
        const bgClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-info';
        
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

    // Update file input label when file is selected
    const fileInput = document.getElementById('material_file');
    if (fileInput) {
        fileInput.addEventListener('change', function () {
            const fileName = this.files[0]?.name;
            if (fileName) {
                const label = this.nextElementSibling;
                if (label && label.tagName === 'SMALL') {
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
                element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                element.style.animation = 'highlight 2s ease-in-out';
            }
        }, 500);
    }

    // Add highlight animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes highlight {
            0%, 100% { background-color: transparent; }
            50% { background-color: rgba(103, 58, 183, 0.1); }
        }
    `;
    document.head.appendChild(style);
});
