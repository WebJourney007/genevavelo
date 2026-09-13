// Scripts du site, partagés par toutes les langues (chargé en defer)

// --- Navbar : masquée au scroll vers le bas, réaffichée au scroll vers le haut ---
function initNavbar() {
  const navbar = document.querySelector('.nav--component');
  if (!navbar) return;
  let lastScroll = window.scrollY;
  window.addEventListener('scroll', () => {
    const current = window.scrollY;
    if (current > lastScroll && current > 100) navbar.classList.add('is-hidden');
    else if (current < lastScroll) navbar.classList.remove('is-hidden');
    lastScroll = current;
  }, { passive: true });
}

// --- Menu mobile ---
function initMobileMenu() {
  const menuButton = document.querySelector('.menu-button');
  const navMenu = document.querySelector('.nav-menu');
  if (!menuButton || !navMenu) return;
  const setOpen = (open) => {
    menuButton.classList.toggle('is-open', open);
    navMenu.classList.toggle('is-open', open);
  };
  menuButton.addEventListener('click', () => setOpen(!navMenu.classList.contains('is-open')));
  navMenu.querySelectorAll('.nav-link, .button').forEach((link) => link.addEventListener('click', () => setOpen(false)));
}

// --- Lien de nav actif selon la section visible ---
function initActiveNavLink() {
  const navLinks = document.querySelectorAll('.nav-link');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      navLinks.forEach((link) => link.classList.toggle('is-active', link.getAttribute('href') === `#${entry.target.id}`));
    });
  }, { threshold: 0.4 });
  document.querySelectorAll('section[id]').forEach((section) => observer.observe(section));
}

// --- Sélecteur de langue ---
function initLangSwitcher() {
  const switcher = document.querySelector('.lang-switcher');
  if (!switcher) return;
  const toggle = switcher.querySelector('.lang-switcher__toggle');
  const menu = switcher.querySelector('.lang-switcher__menu');
  const setOpen = (open) => {
    menu.classList.toggle('is-open', open);
    toggle.setAttribute('aria-expanded', String(open));
  };
  toggle.addEventListener('click', () => setOpen(!menu.classList.contains('is-open')));
  document.addEventListener('click', (e) => { if (!switcher.contains(e.target)) setOpen(false); });
  switcher.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    setOpen(false);
    toggle.focus();
  });
}

// --- Slider des témoignages ---
function initSlider() {
  const slider = document.querySelector('.slider');
  if (!slider) return;
  const slides = slider.querySelectorAll('.slide');
  const dotsWrapper = slider.querySelector('.slide-nav');
  const SWIPE_THRESHOLD = 50;
  let current = 0;
  let autoplayTimer;
  let dragStartX = null;

  const dots = [...slides].map((_, i) => {
    const dot = document.createElement('div');
    dot.className = 'slide-nav__dot';
    dot.addEventListener('click', () => goTo(i));
    dotsWrapper.appendChild(dot);
    return dot;
  });

  function goTo(index) {
    slides[current].classList.remove('is-active');
    dots[current].classList.remove('is-active');
    current = (index + slides.length) % slides.length;
    slides[current].classList.add('is-active');
    dots[current].classList.add('is-active');
    clearInterval(autoplayTimer);
    autoplayTimer = setInterval(() => goTo(current + 1), 7500);
  }

  const endDrag = (x) => {
    if (dragStartX === null) return;
    const diff = dragStartX - x;
    if (Math.abs(diff) > SWIPE_THRESHOLD) goTo(diff > 0 ? current + 1 : current - 1);
    dragStartX = null;
  };
  slider.addEventListener('mousedown', (e) => { dragStartX = e.clientX; });
  slider.addEventListener('mouseup', (e) => endDrag(e.clientX));
  slider.addEventListener('touchstart', (e) => { dragStartX = e.touches[0].clientX; }, { passive: true });
  slider.addEventListener('touchend', (e) => endDrag(e.changedTouches[0].clientX));
  slider.addEventListener('dragstart', (e) => e.preventDefault());
  slider.querySelector('.left-arrow').addEventListener('click', () => goTo(current - 1));
  slider.querySelector('.right-arrow').addEventListener('click', () => goTo(current + 1));

  goTo(0);
}

// --- Bandeau de logos : défilement infini (animation CSS, distance calculée ici) ---
function initLogoTrack() {
  const track = document.querySelector('.logo__track');
  if (!track) return;
  window.addEventListener('load', () => {
    track.style.setProperty('--track-shift', `-${track.scrollWidth / 2}px`);
    track.classList.add('is-scrolling');
  });
}

// --- Formulaire de contact ---
function initContactForm() {
  const form = document.getElementById('contact-form');
  if (!form) return;
  const submitBtn = document.getElementById('submit-btn');
  const successMessage = document.getElementById('success-message');
  const errorMessage = document.getElementById('error-message');
  const idleLabel = submitBtn.value;

  // Timestamp de chargement (anti-bot timing)
  document.getElementById('form_time').value = Math.floor(Date.now() / 1000);

  const flash = (el) => {
    el.classList.add('is-visible');
    setTimeout(() => el.classList.remove('is-visible'), 5000);
  };

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    successMessage.classList.remove('is-visible');
    errorMessage.classList.remove('is-visible');

    // Honeypot : champ caché rempli = bot, on simule un succès
    if (document.getElementById('website').value !== '') {
      form.reset();
      flash(successMessage);
      return;
    }

    submitBtn.disabled = true;
    submitBtn.value = submitBtn.dataset.wait;
    try {
      const response = await fetch(form.action, { method: 'POST', body: new FormData(form) });
      const result = response.ok ? await response.json() : null;
      if (!result?.success) throw new Error('Envoi refusé');
      form.reset();
      flash(successMessage);
    } catch {
      flash(errorMessage);
    } finally {
      submitBtn.disabled = false;
      submitBtn.value = idleLabel;
    }
  });
}

// --- Année du copyright ---
function initCopyrightYear() {
  const year = document.getElementById('copyright-year');
  if (year) year.textContent = new Date().getFullYear();
}

initNavbar();
initMobileMenu();
initActiveNavLink();
initLangSwitcher();
initSlider();
initLogoTrack();
initContactForm();
initCopyrightYear();
