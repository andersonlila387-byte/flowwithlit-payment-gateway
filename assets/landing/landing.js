/* Flowwithlit landing page interactions. No dependencies. */
(() => {
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const finePointer = window.matchMedia('(pointer: fine)').matches;
  const root = document.documentElement;

  /* Split headline words so each one slides up in sequence. */
  document.querySelectorAll('.split').forEach((el) => {
    let i = 0;
    const walk = (node) => {
      [...node.childNodes].forEach((child) => {
        if (child.nodeType === Node.TEXT_NODE) {
          const frag = document.createDocumentFragment();
          child.textContent.split(/(\s+)/).forEach((part) => {
            if (!part) return;
            if (/^\s+$/.test(part)) { frag.append(' '); return; }
            const w = document.createElement('span');
            w.className = 'w';
            const inner = document.createElement('span');
            inner.style.setProperty('--i', i++);
            inner.textContent = part;
            w.append(inner);
            frag.append(w);
          });
          child.replaceWith(frag);
        } else if (child.nodeType === Node.ELEMENT_NODE && child.tagName !== 'BR') {
          walk(child);
        }
      });
    };
    walk(el);
  });
  requestAnimationFrame(() => requestAnimationFrame(() => root.classList.add('is-ready')));

  /* Count-up numbers when they come into view. */
  const countObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      countObserver.unobserve(entry.target);
      const el = entry.target;
      const target = parseFloat(el.dataset.count);
      const decimals = (el.dataset.count.split('.')[1] || '').length;
      const suffix = el.dataset.suffix || '';
      if (reduceMotion) { el.textContent = target.toFixed(decimals) + suffix; return; }
      const start = performance.now();
      const duration = 1600;
      const tick = (now) => {
        const t = Math.min(1, (now - start) / duration);
        const eased = 1 - Math.pow(1 - t, 4);
        el.textContent = (target * eased).toFixed(decimals) + suffix;
        if (t < 1) requestAnimationFrame(tick);
      };
      requestAnimationFrame(tick);
    });
  }, { threshold: 0.6 });
  document.querySelectorAll('[data-count]').forEach((el) => countObserver.observe(el));

  /* Statement: words light up as the section scrolls through the viewport. */
  const statement = document.querySelector('.statement');
  let statementWords = [];
  if (statement) {
    const highlight = new Set((statement.dataset.highlight || '').toLowerCase().split(','));
    const words = statement.textContent.trim().split(/\s+/);
    statement.textContent = '';
    words.forEach((word, i) => {
      const span = document.createElement('span');
      span.className = 'w' + (highlight.has(word.toLowerCase().replace(/[^a-z0-9]/g, '')) ? ' hl' : '');
      span.textContent = word;
      if (i) statement.append(' ');
      statement.append(span);
    });
    statementWords = [...statement.querySelectorAll('.w')];
  }

  /* Elements driven by scroll position. */
  const steps = document.querySelector('.steps');
  const stepItems = steps ? [...steps.querySelectorAll('.step')] : [];
  const heroBg = document.querySelector('.hero__bg');

  let ticking = false;

  const onScroll = () => {
    const y = window.scrollY;
    const vh = window.innerHeight;


    if (statementWords.length) {
      const r = statement.getBoundingClientRect();
      const progress = Math.min(1, Math.max(0, (vh * 0.85 - r.top) / (r.height + vh * 0.35)));
      const lit = Math.round(progress * statementWords.length);
      statementWords.forEach((w, i) => w.classList.toggle('on', i < lit));
    }

    if (steps) {
      const r = steps.getBoundingClientRect();
      const p = Math.min(1, Math.max(0, (vh * 0.75 - r.top) / (r.height * 0.6)));
      steps.style.setProperty('--p', p.toFixed(3));
      stepItems.forEach((s, i) => s.classList.toggle('on', s.getBoundingClientRect().top < vh * 0.75 && (window.innerWidth < 860 || p >= i / Math.max(1, stepItems.length - 1) - 0.01)));
    }

    if (heroBg && !reduceMotion && y < vh * 1.2) {
      heroBg.style.translate = `0 ${(y * 0.25).toFixed(1)}px`;
    }


    ticking = false;
  };
  window.addEventListener('scroll', () => {
    if (!ticking) { ticking = true; requestAnimationFrame(onScroll); }
  }, { passive: true });
  onScroll();

  /* Cursor spotlight on feature cards. */
  if (finePointer) {
    document.querySelectorAll('.feature').forEach((card) => {
      card.addEventListener('pointermove', (e) => {
        const r = card.getBoundingClientRect();
        card.style.setProperty('--x', `${e.clientX - r.left}px`);
        card.style.setProperty('--y', `${e.clientY - r.top}px`);
      });
    });
  }

  /* FAQ accordion: one open at a time, accessible buttons. */
  document.querySelectorAll('.faq__q').forEach((button) => {
    button.addEventListener('click', () => {
      const item = button.closest('.faq__item');
      const willOpen = !item.classList.contains('open');
      item.parentElement.querySelectorAll('.faq__item.open').forEach((open) => {
        open.classList.remove('open');
        open.querySelector('.faq__q').setAttribute('aria-expanded', 'false');
      });
      item.classList.toggle('open', willOpen);
      button.setAttribute('aria-expanded', String(willOpen));
    });
  });

})();
