// ========================================
// Admin Contact Reply Management JS
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    
    // Flash Messages Handler
    handleFlashMessages();
    
    // Modal View Handler
    initModalViewHandlers();
    
    // Reply Counter
    initReplyCounter();
    
    // Delete Confirmation
    initDeleteConfirmation();
    
    // Keyboard Shortcuts
    initKeyboardShortcuts();
});

/**
 * Handle flash messages (success, error, info, warning)
 */
function handleFlashMessages() {
    const flash = document.getElementById('flash');
    if (!flash) return;
    
    const messages = {
        success: flash.dataset.success,
        error: flash.dataset.error,
        info: flash.dataset.info,
        warning: flash.dataset.warning
    };
    
    Object.entries(messages).forEach(([type, message]) => {
        if (message && message.trim()) {
            showToast(message, type);
        }
    });
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    // Check if Bootstrap Toast is available
    if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
        createBootstrapToast(message, type);
    } else {
        // Fallback to simple alert
        createSimpleToast(message, type);
    }
}

/**
 * Create Bootstrap Toast
 */
function createBootstrapToast(message, type) {
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();
    
    const icons = {
        success: 'bi-check-circle-fill',
        error: 'bi-x-circle-fill',
        warning: 'bi-exclamation-triangle-fill',
        info: 'bi-info-circle-fill'
    };
    
    const bgColors = {
        success: 'bg-success',
        error: 'bg-danger',
        warning: 'bg-warning',
        info: 'bg-info'
    };
    
    const toastEl = document.createElement('div');
    toastEl.className = 'toast align-items-center text-white border-0 ' + (bgColors[type] || 'bg-primary');
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');
    
    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi ${icons[type] || 'bi-info-circle-fill'} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toastEl);
    const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
    toast.show();
    
    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
}

/**
 * Create toast container
 */
function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

/**
 * Simple toast fallback
 */
function createSimpleToast(message, type) {
    const colors = {
        success: '#10b981',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6'
    };
    
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${colors[type] || colors.info};
        color: white;
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        z-index: 9999;
        max-width: 400px;
        font-size: 14px;
        font-weight: 600;
        animation: slideIn 0.3s ease-out;
    `;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

/**
 * Initialize modal view handlers
 */
function initModalViewHandlers() {
    const modal = document.getElementById('modalView');
    if (!modal) return;
    
    const viewButtons = document.querySelectorAll('.btn-view');
    
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const email = this.dataset.email;
            const message = this.dataset.message;
            const time = this.dataset.time;
            
            // Populate modal
            document.getElementById('v_name').textContent = name || '—';
            document.getElementById('v_email').textContent = email || '—';
            document.getElementById('v_time').textContent = time || '—';
            document.getElementById('v_message').textContent = message || '—';
            
            // Update form action
            const form = document.getElementById('formReply');
            if (form) {
                const template = form.dataset.actionTemplate;
                form.action = template.replace(':id', id);
            }
            
            // Clear reply textarea
            const textarea = document.getElementById('reply_message');
            if (textarea) {
                textarea.value = '';
                updateReplyCounter();
            }
        });
    });
}

/**
 * Initialize reply counter
 */
function initReplyCounter() {
    const textarea = document.getElementById('reply_message');
    const counter = document.getElementById('replyCounter');
    
    if (!textarea || !counter) return;
    
    textarea.addEventListener('input', updateReplyCounter);
    updateReplyCounter();
}

/**
 * Update reply counter
 */
function updateReplyCounter() {
    const textarea = document.getElementById('reply_message');
    const counter = document.getElementById('replyCounter');
    
    if (!textarea || !counter) return;
    
    const current = textarea.value.length;
    const max = textarea.maxLength || 5000;
    
    counter.textContent = `${current}/${max}`;
    
    // Change color when near limit
    if (current > max * 0.9) {
        counter.style.color = '#ef4444';
        counter.style.fontWeight = '700';
    } else if (current > max * 0.75) {
        counter.style.color = '#f59e0b';
        counter.style.fontWeight = '600';
    } else {
        counter.style.color = '';
        counter.style.fontWeight = '';
    }
}

/**
 * Initialize delete confirmation
 */
function initDeleteConfirmation() {
    const deleteForms = document.querySelectorAll('.form-delete');
    
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (confirm('Bạn có chắc chắn muốn xóa liên hệ này? Hành động này không thể hoàn tác.')) {
                this.submit();
            }
        });
    });
}

/**
 * Initialize keyboard shortcuts
 */
function initKeyboardShortcuts() {
    const textarea = document.getElementById('reply_message');
    const form = document.getElementById('formReply');
    
    if (!textarea || !form) return;
    
    // Ctrl + Enter to submit
    textarea.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            e.preventDefault();
            form.submit();
        }
    });
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
