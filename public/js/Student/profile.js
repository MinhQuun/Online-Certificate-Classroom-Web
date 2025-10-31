/**
 * Profile Page JavaScript
 * Xử lý modal đổi mật khẩu
 */

document.addEventListener('DOMContentLoaded', function() {
    // Modal elements
    const modal = document.getElementById('changePasswordModal');
    const modalTriggers = document.querySelectorAll('[data-modal-trigger="changePasswordModal"]');
    const modalCloses = document.querySelectorAll('[data-modal-close]');

    // Open modal
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            openModal();
        });
    });

    // Close modal
    modalCloses.forEach(close => {
        close.addEventListener('click', function() {
            closeModal();
        });
    });

    // Close modal when clicking overlay
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }

    // Close modal with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('is-active')) {
            closeModal();
        }
    });

    // Functions
    function openModal() {
        if (modal) {
            modal.classList.add('is-active');
            document.body.style.overflow = 'hidden';
            
            // Focus first input
            const firstInput = modal.querySelector('input');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 100);
            }
        }
    }

    function closeModal() {
        if (modal) {
            modal.classList.remove('is-active');
            document.body.style.overflow = '';
            
            // Clear form
            const form = modal.querySelector('form');
            if (form) {
                form.reset();
                // Remove validation errors
                const invalidInputs = form.querySelectorAll('.is-invalid');
                invalidInputs.forEach(input => input.classList.remove('is-invalid'));
                const feedbacks = form.querySelectorAll('.invalid-feedback');
                feedbacks.forEach(feedback => feedback.remove());
            }
        }
    }

    // Auto-show modal if there are password change errors
    const hasPasswordErrors = document.querySelector('#changePasswordModal .is-invalid');
    if (hasPasswordErrors) {
        openModal();
    }

    // Auto-hide success alert after 5 seconds
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        setTimeout(() => {
            successAlert.style.transition = 'opacity 0.5s ease';
            successAlert.style.opacity = '0';
            setTimeout(() => successAlert.remove(), 500);
        }, 5000);
    }

    // Form validation enhancement
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input[required]');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateInput(this);
            });

            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    validateInput(this);
                }
            });
        });
    });

    function validateInput(input) {
        const value = input.value.trim();
        
        if (input.hasAttribute('required') && !value) {
            input.classList.add('is-invalid');
            return false;
        }

        if (input.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                input.classList.add('is-invalid');
                return false;
            }
        }

        input.classList.remove('is-invalid');
        return true;
    }

    // Password confirmation validation
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('new_password_confirmation');

    if (newPassword && confirmPassword) {
        confirmPassword.addEventListener('input', function() {
            if (this.value && newPassword.value !== this.value) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    }
});
