/* ============================================
   NEET LMS - Authentication JavaScript
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {
  initializeAuthForms();
});

/**
 * Initialize authentication forms
 */
function initializeAuthForms() {
  const loginForm = document.getElementById('loginForm');
  const registerForm = document.getElementById('registerForm');

  if (loginForm) {
    loginForm.addEventListener('submit', handleLogin);
  }

  if (registerForm) {
    registerForm.addEventListener('submit', handleRegister);
  }

  // Password visibility toggle
  const passwordToggles = document.querySelectorAll('.password-toggle');
  passwordToggles.forEach(toggle => {
    toggle.addEventListener('click', togglePasswordVisibility);
  });
}

/**
 * Handle login form submission
 */
function handleLogin(e) {
  e.preventDefault();

  const form = e.target;
  const email = document.getElementById('email');
  const password = document.getElementById('password');

  // Validate
  if (!email.value || !password.value) {
    showAuthError('Please fill in all fields');
    return;
  }

  if (!isValidEmail(email.value)) {
    showAuthError('Please enter a valid email');
    return;
  }

  // Show loading state
  const submitBtn = form.querySelector('.btn');
  const originalText = submitBtn.textContent;
  submitBtn.disabled = true;
  submitBtn.textContent = 'Logging in...';

  // Submit the form to the server
  form.submit();
}

/**
 * Handle register form submission
 */
function handleRegister(e) {
  e.preventDefault();

  const fullName = document.getElementById('fullName');
  const email = document.getElementById('email');
  const password = document.getElementById('password');
  const confirmPassword = document.getElementById('confirmPassword');
  const agreeTerms = document.getElementById('agreeTerms');

  // Validate
  if (!fullName.value || !email.value || !password.value || !confirmPassword.value) {
    showAuthError('Please fill in all fields');
    return;
  }

  if (!isValidEmail(email.value)) {
    showAuthError('Please enter a valid email');
    return;
  }

  if (password.value.length < 8) {
    showAuthError('Password must be at least 8 characters');
    return;
  }

  if (password.value !== confirmPassword.value) {
    showAuthError('Passwords do not match');
    return;
  }

  if (!agreeTerms.checked) {
    showAuthError('Please agree to terms and conditions');
    return;
  }

  // Show loading state
  const submitBtn = e.target.querySelector('.btn');
  const originalText = submitBtn.textContent;
  submitBtn.disabled = true;
  submitBtn.textContent = 'Creating account...';

  // Simulate API call
  setTimeout(() => {
    // In production, this would be a real API call
    console.log({
      fullName: fullName.value,
      email: email.value,
      password: password.value
    });

    // Redirect to login or dashboard
    showAuthSuccess('Account created successfully! Redirecting...');

    setTimeout(() => {
      window.location.href = '/login';
    }, 2000);

    submitBtn.disabled = false;
    submitBtn.textContent = originalText;
  }, 1500);
}

/**
 * Toggle password visibility
 */
function togglePasswordVisibility(e) {
  const button = e.currentTarget;
  const input = button.previousElementSibling;

  if (input.type === 'password') {
    input.type = 'text';
    button.innerHTML = '👁️'; // Eye open icon
  } else {
    input.type = 'password';
    button.innerHTML = '👁️‍🗨️'; // Eye closed icon
  }
}

/**
 * Show auth error
 */
function showAuthError(message) {
  const errorContainer = document.querySelector('.auth-error');

  if (!errorContainer) {
    const form = document.querySelector('.auth-form');
    const newError = document.createElement('div');
    newError.className = 'auth-error';
    newError.innerHTML = `<span>⚠️</span> ${message}`;
    form.insertBefore(newError, form.firstChild);
  } else {
    errorContainer.innerHTML = `<span>⚠️</span> ${message}`;
    errorContainer.style.display = 'flex';
  }

  setTimeout(() => {
    const error = document.querySelector('.auth-error');
    if (error) error.style.display = 'none';
  }, 5000);
}

/**
 * Show auth success
 */
function showAuthSuccess(message) {
  const successContainer = document.querySelector('.auth-success');

  if (!successContainer) {
    const form = document.querySelector('.auth-form');
    const newSuccess = document.createElement('div');
    newSuccess.className = 'auth-success';
    newSuccess.innerHTML = `<span>✓</span> ${message}`;
    form.insertBefore(newSuccess, form.firstChild);
  } else {
    successContainer.innerHTML = `<span>✓</span> ${message}`;
    successContainer.style.display = 'flex';
  }
}

/**
 * Validate email
 */
function isValidEmail(email) {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
}

/**
 * Toggle password strength indicator
 */
function updatePasswordStrength(passwordInput) {
  const password = passwordInput.value;
  const strengthContainer = document.querySelector('.password-strength');

  if (!strengthContainer) return;

  let strength = 0;

  if (password.length >= 8) strength++;
  if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
  if (/\d/.test(password)) strength++;
  if (/[^a-zA-Z\d]/.test(password)) strength++;

  const strengthTexts = ['Very Weak', 'Weak', 'Good', 'Strong'];
  const strengthColors = ['#EF4444', '#F59E0B', '#FBBF24', '#10B981'];

  strengthContainer.textContent = strengthTexts[strength - 1] || 'Very Weak';
  strengthContainer.style.color = strengthColors[strength - 1];
}

// Password strength tracking
const passwordInputs = document.querySelectorAll('input[type="password"]');
passwordInputs.forEach(input => {
  if (input.id === 'password') {
    input.addEventListener('input', function() {
      updatePasswordStrength(this);
    });
  }
});
