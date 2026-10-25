(() => {
  const root = document.documentElement;
  const savedTheme = localStorage.getItem('studenthub-theme');
  if (savedTheme === 'dark' || savedTheme === 'light') root.dataset.theme = savedTheme;
  const themeButton = document.getElementById('themeToggle');
  if (themeButton) themeButton.addEventListener('click', () => {
    const next = root.dataset.theme === 'dark' ? 'light' : 'dark';
    root.dataset.theme = next;
    localStorage.setItem('studenthub-theme', next);
    themeButton.setAttribute('aria-label', `Switch to ${next === 'dark' ? 'dark' : 'light'} theme`);
  });

  const menuToggle = document.getElementById('menuToggle');
  const nav = document.getElementById('primaryNav');
  if (menuToggle && nav) menuToggle.addEventListener('click', () => {
    const open = nav.classList.toggle('open');
    menuToggle.setAttribute('aria-expanded', String(open));
    menuToggle.setAttribute('aria-label', open ? 'Close navigation menu' : 'Open navigation menu');
  });

  const announcement = document.getElementById('announcement');
  const dismiss = document.getElementById('dismissAnnouncement');
  if (dismiss && announcement) dismiss.addEventListener('click', () => {
    announcement.hidden = true;
    localStorage.setItem('studenthub-announcement-dismissed', '1');
  });
  if (announcement && localStorage.getItem('studenthub-announcement-dismissed') === '1') announcement.hidden = true;

  const modal = document.getElementById('enquiryModal');
  const openModal = document.getElementById('openEnquiryModal');
  const closeModal = document.getElementById('closeModal');
  let lastFocus = null;
  function showModal() { if (!modal) return; lastFocus = document.activeElement; modal.hidden = false; closeModal?.focus(); }
  function hideModal() { if (!modal) return; modal.hidden = true; lastFocus?.focus?.(); }
  openModal?.addEventListener('click', showModal);
  closeModal?.addEventListener('click', hideModal);
  modal?.addEventListener('click', e => { if (e.target === modal) hideModal(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && modal && !modal.hidden) hideModal(); });

  const slides = [...document.querySelectorAll('.slide')];
  const dots = document.getElementById('sliderDots');
  let slideIndex = 0;
  function showSlide(index) {
    if (!slides.length) return;
    slideIndex = (index + slides.length) % slides.length;
    slides.forEach((slide, i) => { slide.classList.toggle('active', i === slideIndex); slide.setAttribute('aria-hidden', String(i !== slideIndex)); });
    if (dots) [...dots.children].forEach((dot, i) => dot.setAttribute('aria-current', String(i === slideIndex)));
  }
  if (dots && slides.length) slides.forEach((_, i) => {
    const dot = document.createElement('button'); dot.type = 'button'; dot.setAttribute('aria-label', `Show slide ${i + 1}`); dot.addEventListener('click', () => showSlide(i)); dots.appendChild(dot);
  });
  document.getElementById('prevSlide')?.addEventListener('click', () => showSlide(slideIndex - 1));
  document.getElementById('nextSlide')?.addEventListener('click', () => showSlide(slideIndex + 1));
  showSlide(0);

  document.querySelectorAll('[data-faq-question]').forEach(button => button.addEventListener('click', () => {
    const answer = document.getElementById(button.getAttribute('aria-controls'));
    const expanded = button.getAttribute('aria-expanded') === 'true';
    button.setAttribute('aria-expanded', String(!expanded));
    if (answer) answer.hidden = expanded;
    const icon = button.querySelector('[data-faq-icon]'); if (icon) icon.textContent = expanded ? '+' : '−';
  }));

  // Client-side Regex validation for Practical 5. Server-side validation is also required.
  const registrationForm = document.getElementById('registrationForm');
  if (registrationForm) {
    const rules = {
      full_name: [/^[\p{L}][\p{L} .'-]{1,79}$/u, 'Enter a name using letters, spaces, apostrophes, dots or hyphens.'],
      username: [/^[A-Za-z][A-Za-z0-9_]{3,23}$/, 'Use 4–24 characters: letters, numbers or underscores; start with a letter.'],
      email: [/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/, 'Enter a valid email address.'],
      mobile: [/^[6-9]\d{9}$/, 'Enter a valid 10-digit Indian mobile number.'],
      password: [/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,64}$/, 'Use 8–64 characters with uppercase, lowercase, a number and a symbol.']
    };
    const validate = (field) => {
      const input = registrationForm.elements[field]; if (!input) return true;
      const error = registrationForm.querySelector(`[data-error-for="${field}"]`);
      let message = '';
      if (rules[field] && !rules[field][0].test(input.value.trim())) message = rules[field][1];
      if (field === 'confirm_password' && input.value !== registrationForm.elements.password.value) message = 'Passwords do not match.';
      if (field === 'course' && !input.value) message = 'Choose a course.';
      if (field === 'year' && !input.value) message = 'Choose your current year.';
      if (field === 'gender') {
        if (!registrationForm.querySelector('input[name="gender"]:checked')) message = 'Choose a gender option.';
        if (error) error.textContent = message;
        registrationForm.querySelectorAll('input[name="gender"]').forEach(radio => radio.setAttribute('aria-invalid', String(Boolean(message))));
        return !message;
      }
      if (field === 'terms' && !input.checked) message = 'You must accept the terms to register.';
      if (error) error.textContent = message;
      input.classList.toggle('input-invalid', Boolean(message));
      input.setAttribute('aria-invalid', String(Boolean(message)));
      return !message;
    };
    Object.keys(rules).concat(['confirm_password', 'course', 'year', 'gender', 'terms']).forEach(field => {
      const input = registrationForm.elements[field];
      if (field === 'gender') { registrationForm.querySelectorAll('input[name="gender"]').forEach(radio => radio.addEventListener('change', () => validate(field))); return; }
      if (input && typeof input.addEventListener === 'function') input.addEventListener(input.type === 'checkbox' || input.type === 'radio' || input.tagName === 'SELECT' ? 'change' : 'input', () => validate(field));
    });
    registrationForm.elements.password?.addEventListener('input', () => validate('confirm_password'));
    registrationForm.addEventListener('submit', event => {
      const fields = Object.keys(rules).concat(['confirm_password', 'course', 'year', 'gender', 'terms']);
      const valid = fields.map(validate).every(Boolean);
      if (!valid) { event.preventDefault(); registrationForm.querySelector('[aria-invalid="true"]')?.focus(); }
    });
  }

  const contactForm = document.getElementById('contactForm');
  if (contactForm) contactForm.addEventListener('submit', e => {
    const email = contactForm.elements.email.value.trim();
    const phone = contactForm.elements.mobile.value.trim();
    const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email);
    const phoneOk = !phone || /^[6-9]\d{9}$/.test(phone);
    if (!emailOk || !phoneOk) { e.preventDefault(); const box = document.getElementById('contactClientError'); if (box) box.textContent = !emailOk ? 'Enter a valid email address.' : 'Enter a valid 10-digit mobile number.'; }
  });
})();
