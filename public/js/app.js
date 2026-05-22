/* ============================================
   NEET LMS - App JavaScript
   ============================================ */

// Initialize app on page load
document.addEventListener('DOMContentLoaded', function() {
  initializeNavigation();
  initializeForms();
  initializeModals();
});

/**
 * Navigation
 */
function initializeNavigation() {
  const navToggle = document.querySelector('.nav-toggle');
  const navMenu = document.querySelector('.nav-menu');

  if (navToggle) {
    navToggle.addEventListener('click', function() {
      navMenu.classList.toggle('active');
    });
  }

  // Close menu when clicking on a link
  const navLinks = document.querySelectorAll('.nav-item a');
  navLinks.forEach(link => {
    link.addEventListener('click', function() {
      navMenu.classList.remove('active');
    });
  });

  // Mark active link
  const currentPath = window.location.pathname;
  navLinks.forEach(link => {
    if (link.getAttribute('href') === currentPath) {
      link.classList.add('active');
    }
  });
}

/**
 * Form Validation
 */
function initializeForms() {
  const forms = document.querySelectorAll('.auth-form, form');

  forms.forEach(form => {
    form.addEventListener('submit', function(e) {
      if (!validateForm(this)) {
        e.preventDefault();
      }
    });
  });
}

function validateForm(form) {
  const inputs = form.querySelectorAll('[required]');
  let isValid = true;

  inputs.forEach(input => {
    const error = input.parentElement.querySelector('.form-error');

    if (!input.value.trim()) {
      if (error) error.textContent = 'This field is required';
      input.classList.add('is-invalid');
      isValid = false;
    } else {
      if (error) error.textContent = '';
      input.classList.remove('is-invalid');

      // Additional validation
      if (input.type === 'email') {
        if (!isValidEmail(input.value)) {
          if (error) error.textContent = 'Please enter a valid email';
          input.classList.add('is-invalid');
          isValid = false;
        }
      }
    }
  });

  return isValid;
}

function isValidEmail(email) {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
}

/**
 * Modal functionality
 */
function initializeModals() {
  const modals = document.querySelectorAll('.modal');

  modals.forEach(modal => {
    const closeBtn = modal.querySelector('.modal-close');
    const backdrop = modal.querySelector('.modal-backdrop');

    if (closeBtn) {
      closeBtn.addEventListener('click', () => closeModal(modal));
    }

    if (backdrop) {
      backdrop.addEventListener('click', () => closeModal(modal));
    }
  });
}

function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }
}

function closeModal(modal) {
  modal.classList.remove('active');
  document.body.style.overflow = 'auto';
}

/**
 * Utility: Show notification
 */
function showNotification(message, type = 'info') {
  const notification = document.createElement('div');
  notification.className = `alert alert-${type}`;
  notification.textContent = message;
  notification.style.position = 'fixed';
  notification.style.top = '20px';
  notification.style.right = '20px';
  notification.style.zIndex = '9999';

  document.body.appendChild(notification);

  setTimeout(() => {
    notification.remove();
  }, 5000);
}

/**
 * Utility: Copy to clipboard
 */
function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(() => {
    showNotification('Copied to clipboard!', 'success');
  });
}

/**
 * Utility: Scroll to element
 */
function scrollToElement(selector) {
  const element = document.querySelector(selector);
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' });
  }
}
