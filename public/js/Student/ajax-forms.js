/**
 * AJAX Form Handler - Prevent page reloads on form submissions
 * Handles: Cart operations, Review submission, Contact form
 */

(function() {
    'use strict';
    
    console.log('[AJAX Handler] Script loaded');
    
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    function init() {
        console.log('[AJAX Handler] Initializing...');
        
        // Setup all handlers
        setupCartHandlers();
        setupReviewHandler();
        setupContactHandler();
        
        console.log('[AJAX Handler] Initialization complete');
    }
    
    // ==================== CART HANDLERS ====================
    function setupCartHandlers() {
        // Use event capturing to catch all cart form submits before other handlers
        document.addEventListener('submit', function(e) {
            const form = e.target;
            
            // Check if this is a cart form
            if (!form.action.includes('/cart') || form.action.includes('/cartridge')) {
                return; // Not a cart form, let it submit normally
            }
            
            // Skip DELETE forms (handle them separately)
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput && methodInput.value === 'DELETE') {
                handleCartDelete(e, form);
                return;
            }
            
            console.log('[AJAX Cart] Intercepting cart form submit');
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            handleCartSubmit(form);
            return false;
            
        }, true); // true = use capture phase
    }
    
    function handleCartSubmit(form) {
        console.log('[AJAX Cart] Sending request to:', form.action);
        
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        
        // Disable button temporarily
        if (submitButton) {
            submitButton.disabled = true;
        }
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('[AJAX Cart] Response:', data);
            window.location.reload();
        })
        .catch(error => {
            console.error('[AJAX Cart] Error:', error);
            if (submitButton) {
                submitButton.disabled = false;
            }
            alert('Đã xảy ra lỗi. Vui lòng thử lại.');
        });
    }
    
    function handleCartDelete(e, form) {
        console.log('[AJAX Cart] Handling delete');
        
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('[AJAX Cart] Delete response:', data);
            window.location.reload();
        })
        .catch(error => {
            console.error('[AJAX Cart] Error:', error);
            alert('Đã xảy ra lỗi. Vui lòng thử lại.');
        });
        
        return false;
    }
    
    // ==================== REVIEW HANDLER ====================
    function setupReviewHandler() {
        const reviewForm = document.getElementById('courseReviewForm');
        if (!reviewForm) {
            return;
        }
        
        console.log('[AJAX Review] Attaching handler');
        
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('[AJAX Review] Form submitted');
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton ? submitButton.innerHTML : '';
            
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = 'Đang gửi...';
            }
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('[AJAX Review] Response:', data);
                
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Có lỗi xảy ra. Vui lòng thử lại.');
                    
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    }
                }
            })
            .catch(error => {
                console.error('[AJAX Review] Error:', error);
                alert('Đã xảy ra lỗi. Vui lòng thử lại.');
                
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }
            });
        });
    }
    
    // ==================== CONTACT HANDLER ====================
    function setupContactHandler() {
        const contactForm = document.querySelector('.contact-form');
        if (!contactForm) {
            return;
        }
        
        console.log('[AJAX Contact] Attaching handler');
        
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('[AJAX Contact] Form submitted');
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton ? submitButton.innerHTML : '';
            
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = 'Đang gửi...';
            }
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('[AJAX Contact] Response:', data);
                
                if (data.success) {
                    window.location.reload();
                } else {
                    if (data.errors) {
                        let errorMsg = 'Vui lòng kiểm tra lại:\n';
                        Object.values(data.errors).forEach(errors => {
                            errors.forEach(error => errorMsg += '- ' + error + '\n');
                        });
                        alert(errorMsg);
                    } else {
                        alert(data.message || 'Có lỗi xảy ra. Vui lòng thử lại.');
                    }
                    
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    }
                }
            })
            .catch(error => {
                console.error('[AJAX Contact] Error:', error);
                alert('Đã xảy ra lỗi. Vui lòng thử lại.');
                
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }
            });
        });
    }
    
})();
