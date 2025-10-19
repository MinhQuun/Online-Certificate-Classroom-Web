document.addEventListener('DOMContentLoaded', () => {
  const header = document.querySelector('[data-site-header]');
  const nav = document.querySelector('.main-nav');
  const toggle = document.querySelector('[data-header-toggle]');

  const setHeaderState = () => {
    if (!header) return;
    if (window.scrollY > 12) {
      header.classList.add('is-condensed');
    } else {
      header.classList.remove('is-condensed');
    }
  };

  window.addEventListener('scroll', setHeaderState, { passive: true });
  setHeaderState();

  if (toggle && nav) {
    toggle.addEventListener('click', () => {
      const expanded = toggle.getAttribute('aria-expanded') === 'true';
      toggle.setAttribute('aria-expanded', String(!expanded));
      nav.classList.toggle('is-open', !expanded);
      if (!expanded) {
        document.body.style.overflow = 'hidden';
      } else {
        document.body.style.overflow = '';
      }
    });

    const closeNav = () => {
      toggle.setAttribute('aria-expanded', 'false');
      nav.classList.remove('is-open');
      document.body.style.overflow = '';
    };

    nav.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        if (window.matchMedia('(max-width: 860px)').matches) {
          closeNav();
        }
      });
    });

    window.addEventListener('resize', () => {
      if (window.matchMedia('(min-width: 861px)').matches) {
        closeNav();
      }
    });
  }

  const profile = document.querySelector('[data-profile]');
  if (profile) {
    const trigger = profile.querySelector('[data-profile-trigger]');
    const menu = profile.querySelector('[data-profile-menu]');

    const closeProfile = () => {
      profile.classList.remove('is-open');
      trigger?.setAttribute('aria-expanded', 'false');
    };

    trigger?.addEventListener('click', event => {
      event.preventDefault();
      const willOpen = !profile.classList.contains('is-open');
      if (willOpen) {
        profile.classList.add('is-open');
        trigger.setAttribute('aria-expanded', 'true');
      } else {
        closeProfile();
      }
    });

    document.addEventListener('click', event => {
      if (!profile.contains(event.target)) {
        closeProfile();
      }
    });

    window.addEventListener('resize', () => {
      if (window.matchMedia('(max-width: 860px)').matches) {
        closeProfile();
      }
    });
  }
});
