<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HomeSpace — Register</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400;1,600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="stylesheet" href="login.css">
</head>
<body>

<section class="registration" id="register">
  <div class="reg-page">

    <!-- Left panel -->
    <div class="reg-left">
      <a href="#" class="reg-logo">Home<em>Space</em></a>

      <div class="reg-left-body">
        <h2 class="reg-headline">Start your journey<br>to the <em>perfect home</em></h2>
        <p class="reg-sub">Create your free account and unlock access to thousands of verified properties, trusted agents, and powerful search tools.</p>

        <div class="perks">
          <div class="perk">
            <div class="perk-icon"><i class="fa-solid fa-check-double"></i></div>
            <div>
              <div class="perk-title">Save &amp; track favorites</div>
              <div class="perk-desc">Bookmark properties and get notified of price drops</div>
            </div>
          </div>
          <div class="perk">
            <div class="perk-icon"><i class="fa-solid fa-bell"></i></div>
            <div>
              <div class="perk-title">Instant listing alerts</div>
              <div class="perk-desc">Be first to know when new matches go live</div>
            </div>
          </div>
          <div class="perk">
            <div class="perk-icon"><i class="fa-solid fa-handshake"></i></div>
            <div>
              <div class="perk-title">Direct agent access</div>
              <div class="perk-desc">Message verified agents and schedule tours in-app</div>
            </div>
          </div>
          <div class="perk">
            <div class="perk-icon"><i class="fa-solid fa-lock"></i></div>
            <div>
              <div class="perk-title">Privacy protected</div>
              <div class="perk-desc">End-to-end encrypted. Your data is never sold.</div>
            </div>
          </div>
        </div>
      </div>

      <div style="color:var(--muted);font-size:.75rem;position:relative">
        © 2026 HomeSpace. All rights reserved.
      </div>
    </div>

    <!-- Right panel -->
    <div class="reg-right">
      <div class="reg-inner">

        <div class="reg-form-header animate-in">
          <h1 class="reg-form-title">Create your <em>account</em></h1>
          <p class="reg-form-sub">Already have one? <a href="login.php">Sign in instead →</a></p>
        </div>

        <!-- FIX 5: Removed dead .server-error banner div — toasts handle all feedback -->

        <!-- Account type selector -->
        <div class="animate-in anim-d1">
          <p class="type-label">create an account as a&hellip;</p>
          <div class="type-row" id="typeRow">
            <label class="type-card selected" id="card-buyer">
              <div class="type-card-icon"><i class="fa-solid fa-house"></i></div>
              <div class="type-card-text">
                <div class="type-name">Buyer / Renter</div>
                <div class="type-desc">Looking for a property</div>
              </div>
              <div class="type-check"></div>
            </label>
            <label class="type-card" id="card-agent">
              <div class="type-card-icon"><i class="fa-solid fa-briefcase"></i></div>
              <div class="type-card-text">
                <div class="type-name">Agent / Seller</div>
                <div class="type-desc">Listing properties</div>
              </div>
              <div class="type-check"></div>
            </label>
          </div>
        </div>

        <div class="section-sep animate-in anim-d2">
          <div class="sep-line"></div>
          <span class="sep-text">Account details</span>
          <div class="sep-line"></div>
        </div>

        <form id="regForm" method="POST" action="api/reg.php" class="animate-in anim-d2">

          <input type="hidden" name="account_type" id="accountTypeInput" value="buyer">

          <div class="form-row">
            <div class="field" id="field-firstName">
              <label class="field-label" for="firstName">First name</label>
              <div class="field-wrap">
                <span class="field-icon"><i class="fa-solid fa-user"></i></span>
                <input name="firstName" type="text" id="firstName" class="field-input"
                       placeholder="Naomi" autocomplete="given-name" />
              </div>
              <div class="field-error" id="firstName-error">First name is required.</div>
            </div>

            <div class="field" id="field-lastName">
              <label class="field-label" for="lastName">Last name</label>
              <div class="field-wrap">
                <span class="field-icon"><i class="fa-solid fa-user"></i></span>
                <input name="lastName" type="text" id="lastName" class="field-input"
                       placeholder="Kariuki" autocomplete="family-name" />
              </div>
              <div class="field-error" id="lastName-error">Last name is required.</div>
            </div>
          </div>

          <div class="field" id="field-email">
            <label class="field-label" for="regEmail">Email address</label>
            <div class="field-wrap">
              <span class="field-icon"><i class="fa-solid fa-envelope"></i></span>
              <input name="email" type="email" id="regEmail" class="field-input"
                     placeholder="macha@gmail.com" autocomplete="email" />
            </div>
            <div class="field-error" id="regEmail-error">Please enter a valid email address.</div>
          </div>

          <div class="field">
            <label class="field-label" for="phone">Phone number <span class="optional">(optional)</span></label>
            <div class="phone-wrap">
              <input name="phoneNumber" type="tel" id="phone" class="phone-input"
                     placeholder="0712 345 678" autocomplete="tel" />
            </div>
          </div>

          <div class="field" id="field-password">
            <label class="field-label" for="password">Password</label>
            <div class="field-wrap">
              <span class="field-icon"><i class="fa-solid fa-lock"></i></span>
              <input name="password" type="password" id="password" class="field-input"
                     placeholder="Create a strong password" autocomplete="new-password" />
              <button type="button" id="eyeBtn1" class="eye-btn" tabindex="-1" aria-label="Toggle password visibility">
                <i class="fa-solid fa-eye" id="eyeOpen1"></i>
                <i class="fa-solid fa-eye-slash" id="eyeClosed1" style="display:none"></i>
              </button>
            </div>
            <div class="field-error" id="password-error">Password must be at least 8 characters.</div>
          </div>

          <div class="field" id="field-confirm">
            <label class="field-label" for="confirmPassword">Confirm password</label>
            <div class="field-wrap">
              <span class="field-icon"><i class="fa-solid fa-lock"></i></span>
              <input name="confirm_password" type="password" id="confirmPassword" class="field-input"
                     placeholder="Re-enter your password" autocomplete="new-password" />
              <button type="button" id="eyeBtn2" class="eye-btn" tabindex="-1" aria-label="Toggle confirm password visibility">
                <i class="fa-solid fa-eye" id="eyeOpen2"></i>
                <i class="fa-solid fa-eye-slash" id="eyeClosed2" style="display:none"></i>
              </button>
            </div>
            <!-- FIX 7: id kept as "confirm-error" to match setError() call below -->
            <div class="field-error" id="confirm-error">Passwords do not match.</div>
          </div>

          <button type="submit" class="btn-submit" id="submitBtn">
            <span class="btn-text">Create account</span>
          </button>
        </form>

      </div>
    </div>

  </div>
</section>

<script>
  // ── Toast helper ─────────────────────────────────────────────────────────────
  function showToast(message, type = 'error', duration = 4000) {
    document.querySelectorAll('.toast').forEach(t => t.remove());

    const icon = type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation';
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.style.setProperty('--toast-duration', duration + 'ms');
    toast.innerHTML = `
      <i class="fa-solid ${icon}"></i>
      <span class="toast-msg">${message}</span>
      <button class="toast-close" aria-label="Dismiss">
        <i class="fa-solid fa-xmark"></i>
      </button>
      <div class="toast-bar"></div>
    `;
    document.body.appendChild(toast);
    requestAnimationFrame(() => {
      requestAnimationFrame(() => toast.classList.add('show'));
    });
    const timer = setTimeout(() => dismissToast(toast), duration);
    toast.querySelector('.toast-close').addEventListener('click', () => {
      clearTimeout(timer);
      dismissToast(toast);
    });
  }

  function dismissToast(toast) {
    toast.classList.remove('show');
    toast.addEventListener('transitionend', () => toast.remove(), { once: true });
  }

  // FIX 4: Was })(); missing the invocation () — IIFE was defined but never called
  //         so toasts NEVER fired on page load. Fixed closing to })();
  (function readQueryParams() {
    const params = new URLSearchParams(window.location.search);
    const errorMessages = {
      empty_fields:      'Please fill in all required fields.',
      password_mismatch: 'Passwords do not match.',
      email_taken:       'An account with that email already exists. Try signing in.',
      weak_password:     'Password must be at least 8 characters.',
      invalid_email:     'Please enter a valid email address.',
      system_error:      'Something went wrong. Please try again later.'
    };
    const err = params.get('error');
    if (err && errorMessages[err]) showToast(errorMessages[err], 'error');
  })();

  // ── Account type cards ───────────────────────────────────────────────────────
  document.querySelectorAll('.type-card').forEach(card => {
    card.addEventListener('click', () => {
      document.querySelectorAll('.type-card').forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');
      document.getElementById('accountTypeInput').value =
        card.id === 'card-agent' ? 'agent' : 'buyer';
    });
  });

  // ── Eye toggle helper ────────────────────────────────────────────────────────
  function makeEyeToggle(btnId, openId, closedId, inputId) {
    const btn = document.getElementById(btnId);
    if (!btn) return;
    btn.addEventListener('click', () => {
      const inp    = document.getElementById(inputId);
      const isText = inp.type === 'text';
      inp.type = isText ? 'password' : 'text';
      document.getElementById(openId).style.display   = isText ? '' : 'none';
      document.getElementById(closedId).style.display = isText ? 'none' : '';
    });
  }
  makeEyeToggle('eyeBtn1', 'eyeOpen1', 'eyeClosed1', 'password');
  makeEyeToggle('eyeBtn2', 'eyeOpen2', 'eyeClosed2', 'confirmPassword');

  // ── Form validation on submit ────────────────────────────────────────────────
  document.getElementById('regForm').addEventListener('submit', function (e) {
    let valid = true;

    function setError(inputId, errorId, condition) {
      const inp = document.getElementById(inputId);
      const err = document.getElementById(errorId);
      if (condition) {
        inp.classList.add('is-error');
        inp.classList.remove('is-valid');
        if (err) err.style.display = 'block';
        valid = false;
      } else {
        inp.classList.remove('is-error');
        inp.classList.add('is-valid');
        if (err) err.style.display = 'none';
      }
    }

    const fn = document.getElementById('firstName');
    const ln = document.getElementById('lastName');
    const em = document.getElementById('regEmail');
    const pw = document.getElementById('password');
    const cp = document.getElementById('confirmPassword');

    setError('firstName',       'firstName-error', !fn.value.trim());
    setError('lastName',        'lastName-error',  !ln.value.trim());
    setError('regEmail',        'regEmail-error',  !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em.value.trim()));
    setError('password',        'password-error',  pw.value.trim().length < 8);
    setError('confirmPassword', 'confirm-error',   cp.value.trim() !== pw.value.trim() || cp.value.trim() === '');

    if (!valid) {
      e.preventDefault();
      showToast('Please fix the errors below before continuing.', 'error');
    }
  });

  // FIX 6: Replaced the broken string-replace clear-error loop with an explicit
  //         map so every field correctly finds and hides its error element.
  const errorMap = {
    firstName:       'firstName-error',
    lastName:        'lastName-error',
    regEmail:        'regEmail-error',
    password:        'password-error',
    confirmPassword: 'confirm-error'
  };

  Object.keys(errorMap).forEach(inputId => {
    const el = document.getElementById(inputId);
    if (!el) return;
    el.addEventListener('input', () => {
      el.classList.remove('is-error');
      const errEl = document.getElementById(errorMap[inputId]);
      if (errEl) errEl.style.display = 'none';
    });
  });
</script>
</body>
</html>