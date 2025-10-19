document.addEventListener('DOMContentLoaded', () => {
  const dropdowns = document.querySelectorAll('[data-dropdown]');
  let openDropdown = null;

  const isDesktop = () => window.matchMedia('(min-width: 861px)').matches;

  const setActiveCategory = (dropdown, categoryId) => {
    const triggers = dropdown.querySelectorAll('[data-category-trigger]');
    const panels = dropdown.querySelectorAll('[data-category-panel]');
    const placeholder = dropdown.querySelector('[data-category-placeholder]');
    if (!triggers.length) return;

    let found = false;

    triggers.forEach(trigger => {
      const matches = trigger.getAttribute('data-category-trigger') === categoryId;
      trigger.classList.toggle('is-active', matches);
      if (matches) found = true;
    });

    const activeId = found ? categoryId : null;

    triggers.forEach(trigger => {
      const matches = activeId && trigger.getAttribute('data-category-trigger') === activeId;
      trigger.classList.toggle('is-active', matches);
    });

    panels.forEach(panel => {
      const matches = activeId && panel.getAttribute('data-category-panel') === activeId;
      panel.classList.toggle('is-active', matches);
    });

    if (placeholder) {
      placeholder.classList.toggle('is-visible', !activeId);
    }
  };

  const initCategoryNavigation = dropdown => {
    const container = dropdown.querySelector('[data-category-container]');
    if (!container) return;
    const triggers = container.querySelectorAll('[data-category-trigger]');
    if (!triggers.length) return;

    setActiveCategory(dropdown, null);

    triggers.forEach(trigger => {
      const id = trigger.getAttribute('data-category-trigger');

      trigger.addEventListener('mouseenter', () => {
        if (isDesktop()) {
          setActiveCategory(dropdown, id);
        }
      });

      trigger.addEventListener('focus', () => setActiveCategory(dropdown, id));

      trigger.addEventListener('click', event => {
        if (!isDesktop()) {
          event.preventDefault();
          setActiveCategory(dropdown, id);
        }
      });
    });
  };

  const closeDropdown = dropdown => {
    if (!dropdown) return;
    const trigger = dropdown.querySelector('[data-dropdown-trigger]');
    const panel = dropdown.querySelector('[data-dropdown-panel]');
    panel?.classList.remove('is-open');
    trigger?.setAttribute('aria-expanded', 'false');
    setActiveCategory(dropdown, null);
    if (openDropdown === dropdown) {
      openDropdown = null;
    }
  };

  const open = dropdown => {
    const trigger = dropdown.querySelector('[data-dropdown-trigger]');
    const panel = dropdown.querySelector('[data-dropdown-panel]');
    if (!panel || !trigger) return;
    if (openDropdown && openDropdown !== dropdown) {
      closeDropdown(openDropdown);
    }
    panel.classList.add('is-open');
    trigger.setAttribute('aria-expanded', 'true');
    openDropdown = dropdown;
  };

  dropdowns.forEach(dropdown => {
    const trigger = dropdown.querySelector('[data-dropdown-trigger]');
    const panel = dropdown.querySelector('[data-dropdown-panel]');
    if (!trigger || !panel) return;

    initCategoryNavigation(dropdown);

    trigger.addEventListener('click', event => {
      event.preventDefault();
      const isOpen = panel.classList.contains('is-open');
      if (isOpen) {
        closeDropdown(dropdown);
      } else {
        open(dropdown);
      }
    });

    dropdown.addEventListener('mouseenter', () => {
      if (isDesktop()) {
        open(dropdown);
      }
    });

    dropdown.addEventListener('mouseleave', () => {
      if (isDesktop()) {
        closeDropdown(dropdown);
      }
    });
  });

  document.addEventListener('click', event => {
    if (!openDropdown) return;
    if (!openDropdown.contains(event.target)) {
      closeDropdown(openDropdown);
    }
  });

  window.addEventListener('resize', () => {
    if (!isDesktop() && openDropdown) {
      // Collapse dropdown when switching to mobile to avoid layout issues
      closeDropdown(openDropdown);
    }
  });
});
