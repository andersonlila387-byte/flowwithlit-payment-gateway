/* Flowwithlit shared site behaviour: header shadow, mobile menu, scroll reveal, newsletter form. */
(() => {
  const header = document.querySelector('.site-header');
  const onScroll = () => header && header.classList.toggle('is-scrolled', window.scrollY > 8);
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* Mobile menu */
  const toggle = document.querySelector('.site-header__toggle');
  if (toggle) {
    const setMenu = (open) => {
      document.body.classList.toggle('menu-open', open);
      document.body.style.overflow = open ? 'hidden' : '';
      toggle.setAttribute('aria-expanded', String(open));
      toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    };
    toggle.addEventListener('click', () => setMenu(!document.body.classList.contains('menu-open')));
    document.querySelectorAll('.mobile-menu a').forEach((a) => a.addEventListener('click', () => setMenu(false)));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setMenu(false); });
    window.addEventListener('resize', () => { if (window.innerWidth > 900) setMenu(false); });
  }

  /* Scroll reveal with automatic stagger inside [data-stagger] */
  document.querySelectorAll('[data-stagger]').forEach((group) => {
    [...group.querySelectorAll(':scope > [data-reveal]')].forEach((el, i) => el.style.setProperty('--d', `${i * 90}ms`));
  });
  const targets = document.querySelectorAll('[data-reveal], .reveal');
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('in');
        observer.unobserve(entry.target);
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    targets.forEach((el) => observer.observe(el));
  } else {
    targets.forEach((el) => el.classList.add('in'));
  }

  /* Newsletter form: confirmation only (no backend yet) */
  const subscribe = document.querySelector('.subscribe');
  if (subscribe) {
    subscribe.addEventListener('submit', (e) => {
      e.preventDefault();
      const label = subscribe.querySelector('button span');
      if (label) label.textContent = 'Subscribed';
      subscribe.reset();
    });
  }
})();
