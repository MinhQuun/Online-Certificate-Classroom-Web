/**
 * My Courses Page JavaScript
 * Handle course card interactions and filtering
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Animate course cards on load
    animateCourseCards();
    
    // Handle tab switching with smooth scroll
    setupTabNavigation();
    
    // Add loading state for course actions
    setupCourseActions();
    
    // Lazy load course images
    setupLazyLoading();
    
    // Add ripple effect to buttons
    setupRippleEffect();
    
    // Add tooltips
    setupTooltips();
    
    // Add keyboard navigation
    setupKeyboardNavigation();
    
    // Track user interactions
    trackUserInteractions();
});

/**
 * Animate course cards when they enter viewport
 */
function animateCourseCards() {
    const cards = document.querySelectorAll('.course-card');
    
    if (!cards.length) return;
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 50);
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });
}

/**
 * Setup smooth tab navigation
 */
function setupTabNavigation() {
    const tabs = document.querySelectorAll('.tab');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            // Add loading state
            if (!this.classList.contains('active')) {
                const label = this.querySelector('.tab-label');
                const originalText = label.textContent;
                label.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
                
                // Let the navigation happen
                setTimeout(() => {
                    label.textContent = originalText;
                }, 500);
            }
        });
    });
}

/**
 * Setup course action buttons
 */
function setupCourseActions() {
    const actionButtons = document.querySelectorAll('.course-actions .btn');
    
    actionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Add loading state
            if (!this.classList.contains('loading')) {
                const originalHTML = this.innerHTML;
                this.classList.add('loading');
                this.style.pointerEvents = 'none';
                
                const icon = this.querySelector('i');
                if (icon) {
                    icon.className = 'fa-solid fa-spinner fa-spin';
                }
                
                // Reset after navigation (if user comes back)
                setTimeout(() => {
                    this.innerHTML = originalHTML;
                    this.classList.remove('loading');
                    this.style.pointerEvents = '';
                }, 3000);
            }
        });
    });
}

/**
 * Lazy load course images
 */
function setupLazyLoading() {
    const images = document.querySelectorAll('.course-card__image img');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.src; // Trigger load
                    img.style.opacity = '0';
                    img.style.transition = 'opacity 0.3s ease';
                    
                    img.onload = function() {
                        this.style.opacity = '1';
                    };
                    
                    imageObserver.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    }
}

/**
 * Handle progress bar animation
 */
function animateProgressBars() {
    const progressBars = document.querySelectorAll('.progress-fill');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const width = entry.target.style.width;
                entry.target.style.width = '0%';
                
                setTimeout(() => {
                    entry.target.style.width = width;
                }, 100);
                
                observer.unobserve(entry.target);
            }
        });
    });
    
    progressBars.forEach(bar => observer.observe(bar));
}

// Animate progress bars
animateProgressBars();

/**
 * Update URL without page reload when switching tabs
 */
function updateTabURL(url) {
    if (history.pushState) {
        history.pushState(null, null, url);
    }
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <i class="fa-solid fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/**
 * Filter courses by search (if search feature added later)
 */
function filterCourses(searchTerm) {
    const cards = document.querySelectorAll('.course-card');
    let visibleCount = 0;
    
    cards.forEach(card => {
        const title = card.querySelector('.course-title a').textContent.toLowerCase();
        const category = card.querySelector('.course-category')?.textContent.toLowerCase() || '';
        
        if (title.includes(searchTerm.toLowerCase()) || category.includes(searchTerm.toLowerCase())) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show empty state if no results
    const emptyState = document.querySelector('.empty-state');
    const coursesGrid = document.querySelector('.courses-grid');
    
    if (visibleCount === 0 && coursesGrid) {
        if (!emptyState) {
            const empty = document.createElement('div');
            empty.className = 'empty-state search-empty';
            empty.innerHTML = `
                <div class="empty-icon">
                    <i class="fa-solid fa-search"></i>
                </div>
                <h3>Không tìm thấy kết quả</h3>
                <p>Không có khóa học nào phù hợp với từ khóa "<strong>${searchTerm}</strong>"</p>
            `;
            coursesGrid.after(empty);
        }
    } else {
        const searchEmpty = document.querySelector('.search-empty');
        if (searchEmpty) searchEmpty.remove();
    }
}

/**
 * Setup ripple effect for buttons
 */
function setupRippleEffect() {
    const buttons = document.querySelectorAll('.btn');
    
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });
}

/**
 * Setup tooltips for course status badges
 */
function setupTooltips() {
    const statusBadges = document.querySelectorAll('.course-status');
    
    statusBadges.forEach(badge => {
        const tooltipText = badge.classList.contains('active') ? 'Khóa học đang hoạt động' :
                          badge.classList.contains('pending') ? 'Đang chờ kích hoạt mã' :
                          badge.classList.contains('expired') ? 'Khóa học đã hết hạn' : '';
        
        if (tooltipText) {
            badge.setAttribute('title', tooltipText);
            badge.style.cursor = 'help';
        }
    });
    
    // Add tooltip for progress bars
    const progressBars = document.querySelectorAll('.progress-section');
    progressBars.forEach(progress => {
        const percent = progress.querySelector('.progress-percent')?.textContent || '0%';
        progress.setAttribute('title', `Bạn đã hoàn thành ${percent} khóa học này`);
    });
}

/**
 * Setup keyboard navigation
 */
function setupKeyboardNavigation() {
    const tabs = document.querySelectorAll('.tab');
    
    tabs.forEach((tab, index) => {
        tab.setAttribute('tabindex', '0');
        
        tab.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
            
            // Arrow key navigation
            if (e.key === 'ArrowRight') {
                e.preventDefault();
                const nextTab = tabs[index + 1] || tabs[0];
                nextTab.focus();
            }
            
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                const prevTab = tabs[index - 1] || tabs[tabs.length - 1];
                prevTab.focus();
            }
        });
    });
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K to focus search (if added later)
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.querySelector('input[type="search"]');
            if (searchInput) searchInput.focus();
        }
    });
}

/**
 * Track user interactions for analytics
 */
function trackUserInteractions() {
    // Track tab clicks
    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.querySelector('.tab-label')?.textContent || 'Unknown';
            console.log(`Tab clicked: ${tabName}`);
            // Send to analytics service here
        });
    });
    
    // Track course card clicks
    const courseCards = document.querySelectorAll('.course-card');
    courseCards.forEach(card => {
        card.addEventListener('click', function(e) {
            if (!e.target.closest('.btn')) {
                const courseTitle = this.querySelector('.course-title a')?.textContent || 'Unknown';
                console.log(`Course viewed: ${courseTitle}`);
                // Send to analytics service here
            }
        });
    });
    
    // Track button clicks
    const buttons = document.querySelectorAll('.course-actions .btn');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const action = this.textContent.trim();
            console.log(`Button clicked: ${action}`);
            // Send to analytics service here
        });
    });
}

/**
 * Add visual feedback when hovering over course cards
 */
function enhanceCourseCardInteraction() {
    const cards = document.querySelectorAll('.course-card');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const img = this.querySelector('.course-card__image img');
            if (img) {
                img.style.filter = 'brightness(1.1)';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const img = this.querySelector('.course-card__image img');
            if (img) {
                img.style.filter = 'brightness(1)';
            }
        });
    });
}

// Enhance course card interaction
enhanceCourseCardInteraction();

/**
 * Add smooth scrolling to top button
 */


/**
 * Preload next page images
 */
function preloadNextPageImages() {
    const nextPageLink = document.querySelector('.pagination a[rel="next"]');
    if (nextPageLink) {
        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = nextPageLink.href;
        document.head.appendChild(link);
    }
}

// Preload next page
preloadNextPageImages();
