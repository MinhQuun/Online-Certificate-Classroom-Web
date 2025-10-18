// Small JS to handle accordions and UX
document.addEventListener('DOMContentLoaded', () => {
  const updateAria = (wrapper, button) => {
    if (!button) return;
    button.setAttribute('aria-expanded', wrapper.classList.contains('is-open'));
  };

  document.querySelectorAll('[data-accordion]').forEach(acc => {
    const btn = acc.querySelector('.oc-accordion__header');
    const panel = acc.querySelector('.oc-accordion__panel');
    const group = Array.from(acc.parentElement?.querySelectorAll('[data-accordion]') || []);
    const hasActiveChild = acc.querySelector('.is-active');

    if (hasActiveChild) {
      acc.classList.add('is-open');
    } else if (group[0] === acc) {
      acc.classList.add('is-open');
    }

    btn?.addEventListener('click', () => {
      const isOpen = acc.classList.contains('is-open');
      group.forEach(other => {
        if (other !== acc) {
          other.classList.remove('is-open');
          const otherBtn = other.querySelector('.oc-accordion__header');
          updateAria(other, otherBtn);
        }
      });
      acc.classList.toggle('is-open', !isOpen);
      updateAria(acc, btn);
    });

    if (panel) {
      updateAria(acc, btn);
      const observer = new MutationObserver(() => updateAria(acc, btn));
      observer.observe(acc, { attributes: true, attributeFilter: ['class'] });
    }
  });
});
