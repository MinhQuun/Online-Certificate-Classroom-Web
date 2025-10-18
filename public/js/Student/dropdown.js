document.addEventListener('DOMContentLoaded', () => {
  const dropdowns = document.querySelectorAll('[data-dropdown]');
  let openDropdown = null;

  const isDesktop = () => window.matchMedia('(min-width: 861px)').matches;

  const closeDropdown = dropdown => {
    if (!dropdown) return;
    const trigger = dropdown.querySelector('[data-dropdown-trigger]');
    const panel = dropdown.querySelector('[data-dropdown-panel]');
    panel?.classList.remove('is-open');
    trigger?.setAttribute('aria-expanded', 'false');
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

