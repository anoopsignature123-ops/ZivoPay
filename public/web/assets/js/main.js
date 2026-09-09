/* =============================================
   NEXT GEN FOREX — MAIN JAVASCRIPT
   Smart Digital Income & Growth Platform 2026
   ============================================= */

'use strict';

/* ---------- NAVBAR SCROLL ---------- */
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
  navbar.classList.toggle('scrolled', window.scrollY > 50);
}, { passive: true });

/* ---------- MOBILE NAV TOGGLE ---------- */
const navToggle = document.getElementById('navToggle');
const navLinks  = document.getElementById('navLinks');

navToggle.addEventListener('click', () => {
  const isOpen = navLinks.classList.toggle('open');
  navToggle.classList.toggle('active', isOpen);
  navToggle.setAttribute('aria-expanded', String(isOpen));
});

/* Close nav on link click */
navLinks.querySelectorAll('a').forEach(a => {
  a.addEventListener('click', () => {
    navLinks.classList.remove('open');
    navToggle.classList.remove('active');
    navToggle.setAttribute('aria-expanded', 'false');
  });
});

/* Close on outside click */
document.addEventListener('click', e => {
  if (!navbar.contains(e.target)) {
    navLinks.classList.remove('open');
    navToggle.classList.remove('active');
    navToggle.setAttribute('aria-expanded', 'false');
  }
});

/* ---------- SCROLL REVEAL ---------- */
const revealEls = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');
const revealObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      const delay = parseFloat(
        entry.target.style.getPropertyValue('--delay') || '0'
      ) * 1000;
      setTimeout(() => entry.target.classList.add('visible'), delay);
      revealObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

revealEls.forEach(el => revealObserver.observe(el));

/* ---------- ACTIVE NAV LINK (SCROLL SPY) ---------- */
const sections = document.querySelectorAll('section[id]');
const navAs    = document.querySelectorAll('.nav-links a');

const spyObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      navAs.forEach(a => a.classList.remove('active'));
      const active = document.querySelector(`.nav-links a[href="#${entry.target.id}"]`);
      if (active) active.classList.add('active');
    }
  });
}, { rootMargin: '-40% 0px -55% 0px' });

sections.forEach(s => spyObserver.observe(s));

/* ---------- SMOOTH SCROLL (OFFSET FOR FIXED NAV) ---------- */
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    const target = document.querySelector(this.getAttribute('href'));
    if (!target) return;
    e.preventDefault();
    const offset = navbar.offsetHeight + 10;
    window.scrollTo({ top: target.offsetTop - offset, behavior: 'smooth' });
  });
});

/* ---------- FLOATING PARTICLES ---------- */
(function generateParticles() {
  const container = document.getElementById('particles');
  if (!container) return;
  const isMobile = window.innerWidth < 768;
  const count = isMobile ? 12 : 30;
  for (let i = 0; i < count; i++) {
    const p = document.createElement('div');
    p.className = 'particle';
    const size   = Math.random() * 4 + 1.5;
    const left   = Math.random() * 100;
    const dur    = Math.random() * 14 + 10;
    const delay  = Math.random() * 12;
    const colour = i % 5 === 0
      ? 'radial-gradient(circle,rgba(0,200,180,0.9),transparent)'
      : i % 5 === 1
        ? 'radial-gradient(circle,rgba(224,123,0,0.85),transparent)'
        : i % 5 === 2
          ? 'radial-gradient(circle,rgba(29,233,212,0.75),transparent)'
          : i % 5 === 3
            ? 'radial-gradient(circle,rgba(245,158,43,0.8),transparent)'
            : 'radial-gradient(circle,rgba(0,125,114,0.7),transparent)';
    p.style.cssText = `
      width:${size}px; height:${size}px;
      left:${left}%; bottom:0;
      background:${colour};
      animation-duration:${dur}s;
      animation-delay:${delay}s;
    `;
    container.appendChild(p);
  }
})();

/* ---------- COIN CARD TILT EFFECT (desktop only) ---------- */
const isTouch = window.matchMedia('(hover: none) and (pointer: coarse)').matches;
if (!isTouch) {
  document.querySelectorAll('.coin-card, .vision-card, .mission-card, .benefit-card').forEach(card => {
    card.addEventListener('mousemove', e => {
      const rect = card.getBoundingClientRect();
      const x = (e.clientX - rect.left) / rect.width  - 0.5;
      const y = (e.clientY - rect.top)  / rect.height - 0.5;
      card.style.transform = `translateY(-8px) rotateX(${y * -6}deg) rotateY(${x * 6}deg)`;
    });
    card.addEventListener('mouseleave', () => {
      card.style.transform = '';
    });
  });
}

/* ---------- STAT COUNTER ANIMATION (hero stats v2) ---------- */
(function initCounters() {
  const counters = document.querySelectorAll('.hstat-v2-num[data-target]');
  if (!counters.length) return;

  const ease = (t) => t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;

  const animateCounter = (el) => {
    const target  = parseInt(el.dataset.target, 10);
    const suffix  = el.dataset.suffix || '';
    const dur     = target > 1000 ? 2200 : 1400;
    const start   = performance.now();

    const step = (now) => {
      const elapsed  = now - start;
      const progress = Math.min(elapsed / dur, 1);
      const value    = Math.floor(ease(progress) * target);
      el.textContent = value.toLocaleString() + suffix;
      if (progress < 1) requestAnimationFrame(step);
      else el.textContent = target.toLocaleString() + suffix;
    };
    requestAnimationFrame(step);
  };

  const obs = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animateCounter(entry.target);
        obs.unobserve(entry.target);
      }
    });
  }, { threshold: 0.4 });

  counters.forEach(c => obs.observe(c));
})();
document.querySelectorAll('.faq-item').forEach(item => {
  const btn    = item.querySelector('.faq-question');
  const answer = item.querySelector('.faq-answer');

  btn.addEventListener('click', () => {
    const isOpen = item.classList.contains('open');

    /* Close all other open items */
    document.querySelectorAll('.faq-item.open').forEach(openItem => {
      if (openItem !== item) {
        openItem.classList.remove('open');
        openItem.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
      }
    });

    /* Toggle this item */
    item.classList.toggle('open', !isOpen);
    btn.setAttribute('aria-expanded', String(!isOpen));

    /* Scroll into view smoothly if opening */
    if (!isOpen) {
      setTimeout(() => {
        const rect     = item.getBoundingClientRect();
        const navH     = document.getElementById('navbar').offsetHeight + 16;
        const scrollY  = window.scrollY + rect.top - navH;
        if (rect.top < navH || rect.bottom > window.innerHeight) {
          window.scrollTo({ top: scrollY, behavior: 'smooth' });
        }
      }, 50);
    }
  });
});
