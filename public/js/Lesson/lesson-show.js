document.addEventListener('DOMContentLoaded', function() {
    const accordions = document.querySelectorAll('.accordion');
    
    accordions.forEach(accordion => {
        const toggle = accordion.querySelector('.module__toggle');
        const panel = accordion.querySelector('.module__panel');
        
        if (!toggle || !panel) return;
        
        // Set initial state
        accordion.setAttribute('aria-expanded', 'false');
        panel.style.maxHeight = '0';
        
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            const isExpanded = accordion.getAttribute('aria-expanded') === 'true';
            
            // Close all other accordions (optional - bỏ comment nếu muốn cho phép mở nhiều)
            accordions.forEach(otherAccordion => {
                if (otherAccordion !== accordion) {
                    const otherPanel = otherAccordion.querySelector('.module__panel');
                    otherAccordion.setAttribute('aria-expanded', 'false');
                    if (otherPanel) {
                        otherPanel.style.maxHeight = '0';
                    }
                }
            });
            
            // Toggle current accordion
            accordion.setAttribute('aria-expanded', !isExpanded);
            
            if (!isExpanded) {
                panel.style.maxHeight = panel.scrollHeight + 'px';
            } else {
                panel.style.maxHeight = '0';
            }
        });
        
        // Auto-expand if contains active lesson
        const isActive = accordion.querySelector('.lesson-list li.is-active');
        if (isActive) {
            accordion.setAttribute('aria-expanded', 'true');
            // Đợi một chút để đảm bảo DOM đã render
            setTimeout(() => {
                panel.style.maxHeight = panel.scrollHeight + 'px';
            }, 100);
        }
    });
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            accordions.forEach(accordion => {
                const isExpanded = accordion.getAttribute('aria-expanded') === 'true';
                if (isExpanded) {
                    const panel = accordion.querySelector('.module__panel');
                    if (panel) {
                        panel.style.maxHeight = panel.scrollHeight + 'px';
                    }
                }
            });
        }, 250);
    });
});