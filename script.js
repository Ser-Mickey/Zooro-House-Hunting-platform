/* ================================================================
   ZOORO KENYA — script.js
   JavaScript Lab | Web Application Development
   ------------------------------------------------------------
   * Victor Nyagah
   * Student No: 165080
   * BBIT, Web Application Project
   ================================================================ */

document.addEventListener('DOMContentLoaded', () => {
  initWelcomeMessage();     // Feature 1: personalised welcome (Home)
  initHomeSearchValidation();
  initPostForm();           // Feature 2: form validation (Post a House)
  initHuntForm();           // Feature 2: form validation (House Hunt Request)
  initFormToggle();         // Dynamic content: List a Property / Request a House Hunt tabs
  initFileUploadPreview();  // Responsive file upload indicator (Post a House)
  initContactForm();        // Feature 2: form validation (Contact)
  initGalleryFavorites();   // Feature 3: dynamic content (Gallery)
  initListingsExpand();     // Feature 3: dynamic content (Listings)
  initListingsLiveFilter(); // Feature 3: dynamic content (Listings)
  initThemeToggle();        // Bonus: site-wide dark mode
  initBackToTop();          // Bonus: site-wide back-to-top button
});

/* ================================================================
   SHARED HELPERS
   ================================================================ */

/** Escapes user-typed text before it's inserted as HTML. */
function escapeHTML(str) {
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}

/** Marks a field invalid and prints a short message right under it. */
function showFieldError(field, message) {
  clearFieldError(field);
  field.classList.add('input-error');
  const error = document.createElement('span');
  error.className = 'field-error';
  error.textContent = message;
  field.insertAdjacentElement('afterend', error);
}

/** Removes error state + message for a single field. */
function clearFieldError(field) {
  field.classList.remove('input-error');
  const next = field.nextElementSibling;
  if (next && next.classList.contains('field-error')) {
    next.remove();
  }
}

/** Small floating toast used for validation results & confirmations. */
function showFormToast(message, isSuccess = true) {
  const existing = document.getElementById('form-toast');
  if (existing) existing.remove();

  const toast = document.createElement('div');
  toast.id = 'form-toast';
  toast.className = `form-toast ${isSuccess ? 'success' : 'error'}`;
  toast.textContent = message;
  document.body.appendChild(toast);

  requestAnimationFrame(() => toast.classList.add('show'));

  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 400);
  }, 3500);
}

/* ================================================================
   FEATURE 1 — WELCOME MESSAGE (index.html)
   ================================================================ */
function initWelcomeMessage() {
  const heroContent = document.querySelector('.hero-content');
  const searchBar = document.querySelector('.search-bar');
  if (!heroContent || !searchBar) return; // only exists on the Home page

  const STORAGE_KEY = 'zooro_visitor_name';
  let name = localStorage.getItem(STORAGE_KEY);

  if (!name) {
    const entered = window.prompt("👋 Welcome to Zooro Kenya! What's your name?");
    name = entered && entered.trim() ? entered.trim() : '';
    localStorage.setItem(STORAGE_KEY, name);
  }

  const greeting = name
    ? `Welcome back, <strong>${escapeHTML(name)}</strong>! Ready to find your next home in Nairobi?`
    : `Welcome to Zooro Kenya! Ready to find your next home in Nairobi?`;

  const banner = document.createElement('div');
  banner.className = 'js-welcome-banner';
  banner.innerHTML = `
    <span>👋 ${greeting}</span>
    ${name ? `<button type="button" id="reset-name-btn">Not ${escapeHTML(name)}?</button>` : ''}
  `;
  heroContent.insertBefore(banner, searchBar);

  const resetBtn = document.getElementById('reset-name-btn');
  if (resetBtn) {
    resetBtn.addEventListener('click', () => {
      localStorage.removeItem(STORAGE_KEY);
      location.reload();
    });
  }
}

/* Quick validation for the homepage search bar (empty search is blocked) */
function initHomeSearchValidation() {
  const searchForm = document.querySelector('.search-bar');
  if (!searchForm) return;

  searchForm.addEventListener('submit', (e) => {
    const input = searchForm.querySelector('input[name="area"]');
    if (!input.value.trim()) {
      e.preventDefault();
      input.classList.add('input-error');
      const original = input.placeholder;
      input.placeholder = 'Please type an area first...';
      input.focus();
      setTimeout(() => {
        input.classList.remove('input-error');
        input.placeholder = original;
      }, 1800);
    }
  });
}

/* ================================================================
   FEATURE 2 — FORM VALIDATION & PROCESSORS
   ================================================================ */

/* "Post a House" form (post.html) */
function initPostForm() {
  const nameField = document.getElementById('landlord-name');
  if (!nameField) return; // only exists on post.html
  const form = nameField.closest('form');
  form.noValidate = true; // hand validation fully to JS

  const requiredIds = ['landlord-name', 'phone', 'location', 'house-type', 'rent', 'available-from'];

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    let isValid = true;

    requiredIds.forEach((id) => {
      const field = document.getElementById(id);
      clearFieldError(field);
      if (!field.value.trim()) {
        showFieldError(field, 'This field is required.');
        isValid = false;
      }
    });

    const phone = document.getElementById('phone');
    if (phone.value.trim() && !/^\+?[\d\s-]{7,16}$/.test(phone.value.trim())) {
      showFieldError(phone, 'Enter a valid phone number.');
      isValid = false;
    }

    const rent = document.getElementById('rent');
    if (rent.value && Number(rent.value) < 1000) {
      showFieldError(rent, 'Rent should be at least KSh 1,000.');
      isValid = false;
    }

    const availableFrom = document.getElementById('available-from');
    if (availableFrom.value) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      if (new Date(availableFrom.value) < today) {
        showFieldError(availableFrom, 'Date cannot be in the past.');
        isValid = false;
      }
    }

    if (!isValid) {
      showFormToast('Please fix the errors highlighted below.', false);
      form.querySelector('.input-error')?.focus();
      return;
    }

    // Submit validated data to process_listing.php
    form.action = 'post.php';
    form.method = 'POST';
    form.submit();
  });

  requiredIds.forEach((id) => {
    const field = document.getElementById(id);
    field.addEventListener('input', () => clearFieldError(field));
    field.addEventListener('change', () => clearFieldError(field));
  });
}

/* "Request a House Hunt" form (post.html, tenant side) */
function initHuntForm() {
  const nameField = document.getElementById('hunt-name');
  if (!nameField) return; // only exists on post.html
  const form = nameField.closest('form');
  form.noValidate = true;

  const requiredIds = ['hunt-name', 'hunt-phone', 'hunt-location', 'hunt-house-type', 'hunt-budget', 'hunt-timeframe'];

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    let isValid = true;

    requiredIds.forEach((id) => {
      const field = document.getElementById(id);
      clearFieldError(field);
      if (!field.value.trim()) {
        showFieldError(field, 'This field is required.');
        isValid = false;
      }
    });

    const phone = document.getElementById('hunt-phone');
    if (phone.value.trim() && !/^\+?[\d\s-]{7,16}$/.test(phone.value.trim())) {
      showFieldError(phone, 'Enter a valid phone number.');
      isValid = false;
    }

    if (!isValid) {
      showFormToast('Please fix the errors highlighted below.', false);
      form.querySelector('.input-error')?.focus();
      return;
    }

    // Submit validated data to process_hunt.php
    form.submit();
  });

  requiredIds.forEach((id) => {
    const field = document.getElementById(id);
    field.addEventListener('input', () => clearFieldError(field));
    field.addEventListener('change', () => clearFieldError(field));
  });
}

/* Toggle between "List a Property" and "Request a House Hunt" (post.html) */
function initFormToggle() {
  const tabLandlord = document.getElementById('tab-landlord');
  const tabHunt = document.getElementById('tab-hunt');
  const panelLandlord = document.getElementById('panel-landlord');
  const panelHunt = document.getElementById('panel-hunt');
  if (!tabLandlord || !tabHunt || !panelLandlord || !panelHunt) return; // only exists on post.html

  function showLandlord() {
    panelLandlord.classList.remove('hidden');
    panelHunt.classList.add('hidden');
    tabLandlord.classList.add('active');
    tabHunt.classList.remove('active');
    tabLandlord.setAttribute('aria-selected', 'true');
    tabHunt.setAttribute('aria-selected', 'false');
  }

  function showHunt() {
    panelHunt.classList.remove('hidden');
    panelLandlord.classList.add('hidden');
    tabHunt.classList.add('active');
    tabLandlord.classList.remove('active');
    tabHunt.setAttribute('aria-selected', 'true');
    tabLandlord.setAttribute('aria-selected', 'false');
  }

  tabLandlord.addEventListener('click', showLandlord);
  tabHunt.addEventListener('click', showHunt);
}

/* Displays selected photo file name on post.html */
function initFileUploadPreview() {
  const photoInput = document.getElementById('photo-upload');
  if (!photoInput) return;

  photoInput.addEventListener('change', (e) => {
    const uploadArea = e.target.closest('.file-upload-area');
    if (!uploadArea) return;
    const textElement = uploadArea.querySelector('p');

    if (e.target.files && e.target.files.length > 0) {
      const fileName = e.target.files[0].name;
      if (textElement) textElement.textContent = `Selected File: ${fileName}`;
      uploadArea.style.borderColor = '#10b981';
    } else {
      if (textElement) textElement.textContent = 'Choose file or drag and drop here';
      uploadArea.style.borderColor = '#cbd5e1';
    }
  });
}

/* "Contact Us" form (contact.html) */
function initContactForm() {
  const emailField = document.getElementById('email');
  if (!emailField) return; // only exists on contact.html
  const form = emailField.closest('form');
  form.noValidate = true;

  const requiredIds = ['name', 'email', 'message'];

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    let isValid = true;

    requiredIds.forEach((id) => {
      const field = document.getElementById(id);
      clearFieldError(field);
      if (!field.value.trim()) {
        showFieldError(field, 'This field is required.');
        isValid = false;
      }
    });

    if (emailField.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailField.value.trim())) {
      showFieldError(emailField, 'Enter a valid email address.');
      isValid = false;
    }

    const agree = form.querySelector('input[name="agree"]');
    const agreeLabel = agree.closest('.checkbox-label');
    const oldAgreeError = agreeLabel.parentElement.querySelector('.field-error');
    if (oldAgreeError) oldAgreeError.remove();
    if (!agree.checked) {
      const error = document.createElement('span');
      error.className = 'field-error';
      error.textContent = 'Please confirm you agree to be contacted.';
      agreeLabel.insertAdjacentElement('afterend', error);
      isValid = false;
    }

    if (!isValid) {
      showFormToast('Please fix the errors highlighted below.', false);
      return;
    }

    showFormToast("✅ Message sent! We'll get back to you within 24 hours.", true);
    form.reset();
  });

  requiredIds.forEach((id) => {
    const field = document.getElementById(id);
    field.addEventListener('input', () => clearFieldError(field));
  });
}

/* ================================================================
   FEATURE 3 — DYNAMIC CONTENT
   ================================================================ */

/* Gallery: heart button on each flip-card, saved to localStorage */
function initGalleryFavorites() {
  const galleryGrid = document.querySelector('.gallery-grid');
  if (!galleryGrid) return; // only exists on gallery.html

  const STORAGE_KEY = 'zooro_favorites';
  const saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');

  const galleryHero = document.querySelector('.gallery-hero');
  const counter = document.createElement('p');
  counter.className = 'favorites-counter';
  galleryHero?.appendChild(counter);

  function updateCounter() {
    const count = galleryGrid.querySelectorAll('.favorite-btn.active').length;
    counter.textContent = count > 0
      ? `❤️ You've saved ${count} propert${count === 1 ? 'y' : 'ies'}`
      : '🤍 Tap the heart on a card to save it';
  }

  Array.from(galleryGrid.querySelectorAll('.flip-card')).forEach((card, index) => {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'favorite-btn';
    btn.setAttribute('aria-label', 'Save to favourites');
    btn.textContent = '♡';

    if (saved.includes(index)) {
      btn.classList.add('active');
      btn.textContent = '❤️';
    }

    card.appendChild(btn);

    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      btn.classList.toggle('active');
      btn.textContent = btn.classList.contains('active') ? '❤️' : '♡';

      const current = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
      const pos = current.indexOf(index);
      if (btn.classList.contains('active') && pos === -1) current.push(index);
      if (!btn.classList.contains('active') && pos > -1) current.splice(pos, 1);
      localStorage.setItem(STORAGE_KEY, JSON.stringify(current));

      updateCounter();
    });
  });

  updateCounter();
}

/* Listings: "View Details" expands an extra info panel per card */
function initListingsExpand() {
  const grid = document.querySelector('.listings-grid');
  const isListingsPage = document.querySelector('.filter-bar');
  if (!grid || !isListingsPage) return; // only exists on listings.html

  grid.querySelectorAll('.listing-card').forEach((card) => {
    const link = card.querySelector('.btn-view');
    if (!link) return;

    const panel = document.createElement('div');
    panel.className = 'details-panel';
    panel.innerHTML = `<p>📍 Contact the landlord directly through Zooro to arrange a viewing.
      Deposit and utility terms vary by property — always confirm before paying anything.</p>`;
    card.querySelector('.listing-card-body').appendChild(panel);

    link.addEventListener('click', (e) => {
      e.preventDefault();
      const isOpen = panel.classList.toggle('open');
      link.textContent = isOpen ? 'Hide Details ↑' : 'View Details →';
    });
  });
}

/* Listings: filter bar re-filters the grid live, no page reload */
function initListingsLiveFilter() {
  const filterForm = document.querySelector('.filter-bar');
  const grid = document.querySelector('.listings-grid');
  if (!filterForm || !grid) return; // only exists on listings.html

  const locationSelect = document.getElementById('location');
  const priceSelect = document.getElementById('price');
  const typeSelect = document.getElementById('type');
  const cards = Array.from(grid.querySelectorAll('.listing-card'));

  const countEl = document.createElement('p');
  countEl.className = 'filter-results-count';
  grid.parentNode.insertBefore(countEl, grid);

  function priceInRange(price, rangeLabel) {
    if (!rangeLabel) return true;
    if (rangeLabel.includes('Under')) return price < 10000;
    if (rangeLabel.includes('10,000') && rangeLabel.includes('20,000')) return price >= 10000 && price <= 20000;
    if (rangeLabel.includes('20,000') && rangeLabel.includes('40,000')) return price >= 20000 && price <= 40000;
    if (rangeLabel.includes('40,000+')) return price > 40000;
    return true;
  }

  function applyFilters() {
    const loc = locationSelect.value;
    const priceRange = priceSelect.value;
    const type = typeSelect.value;
    let visibleCount = 0;

    cards.forEach((card) => {
      const heading = card.querySelector('h3')?.textContent || '';
      const tag = card.querySelector('.listing-tag')?.textContent.trim() || '';
      const priceText = card.querySelector('.listing-price')?.textContent || '';
      const priceNum = parseInt(priceText.replace(/[^\d]/g, ''), 10) || 0;

      const matchesLocation = !loc || heading.includes(loc);
      const matchesType = !type || tag === type;
      const matchesPrice = priceInRange(priceNum, priceRange);
      const visible = matchesLocation && matchesType && matchesPrice;

      card.style.display = visible ? '' : 'none';
      if (visible) visibleCount++;
    });

    countEl.textContent = `Showing ${visibleCount} of ${cards.length} listings`;
  }

  filterForm.addEventListener('submit', (e) => e.preventDefault());
  [locationSelect, priceSelect, typeSelect].forEach((select) => {
    select.addEventListener('change', applyFilters);
  });

  applyFilters();
}

/* ================================================================
   BONUS — SITE-WIDE POLISH (not required, adds general functionality)
   ================================================================ */

/* Dark mode toggle, added into the nav on every page */
function initThemeToggle() {
  const navLinks = document.querySelector('.nav-links');
  if (!navLinks) return;

  const li = document.createElement('li');
  const btn = document.createElement('button');
  btn.type = 'button';
  btn.id = 'theme-toggle-btn';
  btn.setAttribute('aria-label', 'Toggle dark mode');
  li.appendChild(btn);
  navLinks.appendChild(li);

  const STORAGE_KEY = 'zooro_theme';

  function applyTheme(isDark) {
    document.body.classList.toggle('dark-theme', isDark);
    btn.textContent = isDark ? '☀️' : '🌙';
  }

  applyTheme(localStorage.getItem(STORAGE_KEY) === 'dark');

  btn.addEventListener('click', () => {
    const isDark = !document.body.classList.contains('dark-theme');
    applyTheme(isDark);
    localStorage.setItem(STORAGE_KEY, isDark ? 'dark' : 'light');
  });
}

/* Floating back-to-top button, added on every page */
function initBackToTop() {
  const btn = document.createElement('button');
  btn.type = 'button';
  btn.id = 'back-to-top';
  btn.setAttribute('aria-label', 'Back to top');
  btn.textContent = '↑';
  document.body.appendChild(btn);

  window.addEventListener('scroll', () => {
    btn.classList.toggle('show', window.scrollY > 400);
  });

  btn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}
