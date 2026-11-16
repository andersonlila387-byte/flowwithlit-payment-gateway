<?php
/**
 * Flowwithlit Registration Page
 * Clean 2-column split design with top back-arrow icon on Step 2.
 */
require_once __DIR__ . '/config/environment.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/countries.php';
// Google sign-up shows only when a Google OAuth client ID is configured (first entry is the web client).
$googleClientId = trim(explode(',', (string) (getenv('FLOW_GOOGLE_CLIENT_ID') ?: ''))[0]);

// Country list and phone codes are managed in Admin → Countries.
try {
    $signupPdo = Database::getInstance();
    $signupCountries = approvedCountries($signupPdo);
    $signupDialCodes = phoneDialCodes($signupPdo);
} catch (Throwable $e) {
    $signupCountries = FWL_FALLBACK_COUNTRIES;
    $signupDialCodes = ['+234'];
}
$defaultDialCode = $signupCountries[0]['dial_code'] ?? '+234';
$h = static fn ($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');

$pageTitle = 'Create Account — Flowwithlit';
$pageDesc  = 'Register for a Flowwithlit account. Accept payments with Crypto & Naira, transfer funds, and integrate SDKs.';
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($pageDesc); ?>" />
  <meta name="robots" content="noindex, nofollow" />
  <meta name="theme-color" content="#0b2545" />
  <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?>" />
  <meta property="og:description" content="<?php echo htmlspecialchars($pageDesc); ?>" />
  <meta property="og:image" content="logo/flowwithlit-social-preview.png" />
  <link rel="icon" type="image/png" sizes="32x32" href="logo/flowwithlit-icon-32.png" />
  <link rel="apple-touch-icon" href="logo/flowwithlit-icon-192.png" />
  
  <link rel="manifest" href="site.webmanifest" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="stylesheet" href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,700,900&display=swap" />
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet" />

  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { 
            sans: ['Satoshi', 'system-ui', 'sans-serif'],
            mono: ['"IBM Plex Mono"', 'monospace']
          },
          colors: {
            brand: { 
              50: '#eef4ff', 
              100: '#d9e6ff', 
              500: '#0b2545', 
              600: '#071930', 
              700: '#041124' 
            },
            mint: '#eef4ff',
            ink: '#0b2545',
          },
        },
      },
    };
  </script>
  
  <style>
    html {
      font-size: 16px;
    }
    body { 
      font-family: 'Satoshi', system-ui, sans-serif; 
      font-weight: 500;
      color: #0b2545; 
      background-color: #ffffff;
      -webkit-font-smoothing: antialiased;
    }
    .text-xs {
      font-size: 0.8125rem !important;
      font-weight: 600;
    }
    .text-\[10px\], .text-\[11px\] {
      font-size: 0.8125rem !important;
      font-weight: 600;
    }
    .text-sm {
      font-size: 0.9375rem !important;
    }
    .hero-bg {
      background-image: linear-gradient(180deg, rgba(10, 18, 30, 0.75) 0%, rgba(10, 18, 30, 0.9) 100%), url('images/75nmeaVY0yWUShlVFysSmJb9Q.png');
      background-size: cover; 
      background-position: center;
    }
    .field-select {
      appearance: none;
      -webkit-appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath d='M5.5 7.5 10 12l4.5-4.5' stroke='%2364748b' stroke-width='1.8' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 0.9rem center;
      background-size: 1rem;
      padding-right: 2.5rem;
      cursor: pointer;
    }
    .radio-dot {
      flex-shrink: 0;
      width: 1.125rem;
      height: 1.125rem;
      border-radius: 999px;
      border: 2px solid #cbd5e1;
      background: #fff;
      transition: border-color 0.15s, box-shadow 0.15s;
    }
    .peer:checked + span .radio-dot {
      border-color: #0b2545;
      box-shadow: inset 0 0 0 3px #fff, inset 0 0 0 9px #0b2545;
    }
  </style>
</head>
<body class="bg-white text-ink antialiased min-h-screen">

  <div class="min-h-screen grid lg:grid-cols-12">
    
    <!-- COLUMN 1: VISUAL COLUMN (Desktop Only - Motivational Assurance) -->
    <div class="hidden lg:flex lg:col-span-5 hero-bg relative lg:sticky lg:top-0 lg:h-screen lg:self-start flex-col justify-center p-12 text-white overflow-hidden">
      <div class="space-y-5 max-w-md">
        <span class="inline-block w-8 h-1 bg-blue-400 rounded-full mb-1"></span>
        <h1 class="text-4xl font-bold tracking-tight leading-tight text-white">
          Be the best.
        </h1>
        <p class="text-white/85 text-sm sm:text-base leading-relaxed font-normal">
          Join thousands of smart businesses and individuals who trust Flowwithlit for fast, secure, and borderless global financial transactions.
        </p>
        <div class="pt-2">
          <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-full text-xs text-white/90 border border-white/20 font-medium">
            <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span>Bank-Grade Encryption &amp; Instant Settlement</span>
          </div>
        </div>
      </div>
    </div>

    <!-- COLUMN 2: REGISTRATION FORM (Right Side) -->
    <div class="lg:col-span-7 flex flex-col justify-between p-6 sm:p-10 md:p-14 bg-white min-h-screen">
      
      <!-- Center Form Content Container -->
      <div class="max-w-md w-full mx-auto my-auto py-4">

        <!-- Brand mark: centred directly above the form -->
        <a href="./" class="flex items-center justify-center mb-8" aria-label="Flowwithlit home">
          <img src="logo/flowwithlit-wordmark.png" alt="Flowwithlit" class="h-9 sm:h-10 w-auto object-contain" />
        </a>

        <!-- Header -->
        <div class="mb-6 text-center">
          <h1 class="text-2xl font-bold text-[#0b2545]">Create your account</h1>
          <p class="text-xs text-slate-500 mt-1">Start receiving payments and moving money with Flowwithlit</p>
        </div>

        <!-- Alert Message Box -->
        <div id="alert-box" class="hidden mb-5 p-3.5 rounded-xl text-xs font-medium border border-red-200 bg-red-50 text-red-700"></div>

        <?php if ($googleClientId !== ''): ?>
        <!-- Real Google sign-up (verified server-side). Hidden entirely when Google isn't configured. -->
        <div data-google-block>
          <div id="googleSignIn" class="mb-5 flex min-h-[44px] w-full justify-center" data-client-id="<?= htmlspecialchars($googleClientId, ENT_QUOTES, 'UTF-8') ?>" data-context="signup"></div>
          <div class="relative flex items-center justify-center mb-6">
            <div class="border-t border-slate-200 w-full"></div>
            <span class="bg-white px-3 text-[10px] uppercase tracking-wider text-slate-400 font-bold absolute">OR REGISTER WITH EMAIL</span>
          </div>
        </div>
        <?php endif; ?>

        <!-- Registration Form -->
        <form id="register-form" autocomplete="on" novalidate class="space-y-4">

          <!-- Account type -->
          <div role="radiogroup" aria-label="Account type" class="grid grid-cols-2 gap-1 p-1 bg-slate-100 rounded-full">
            <label class="cursor-pointer">
              <input type="radio" name="account_type" value="business" class="sr-only peer" checked />
              <span class="block text-center text-xs py-2.5 rounded-full text-slate-500 transition peer-checked:bg-white peer-checked:text-[#0b2545] peer-checked:shadow-sm peer-focus-visible:ring-2 peer-focus-visible:ring-[#0b2545]">Business</span>
            </label>
            <label class="cursor-pointer">
              <input type="radio" name="account_type" value="individual" class="sr-only peer" />
              <span class="block text-center text-xs py-2.5 rounded-full text-slate-500 transition peer-checked:bg-white peer-checked:text-[#0b2545] peer-checked:shadow-sm peer-focus-visible:ring-2 peer-focus-visible:ring-[#0b2545]">Personal</span>
            </label>
          </div>

          <div>
            <label for="country" class="block text-xs font-bold text-[#0b2545] mb-1.5">Country</label>
            <select id="country" required class="field-select w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-full text-xs text-slate-900 focus:outline-none focus:border-[#0b2545] focus:bg-white transition">
              <?php foreach ($signupCountries as $i => $c): ?>
              <option value="<?= $h($c['code']) ?>" data-dial="<?= $h($c['dial_code']) ?>"<?= $i === 0 ? ' selected' : '' ?>><?= $h($c['name']) ?></option>
              <?php endforeach; ?>
              <option value="OTHER">Other countries</option>
            </select>
          </div>

          <div data-business-only>
            <label for="business_name" class="block text-xs font-bold text-[#0b2545] mb-1.5">Business name</label>
            <input type="text" id="business_name" autocomplete="organization" maxlength="191" placeholder="Doe Ventures" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-full text-xs font-mono text-slate-900 focus:outline-none focus:border-[#0b2545] focus:bg-white transition" />
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label for="first_name" class="block text-xs font-bold text-[#0b2545] mb-1.5">First name</label>
              <input type="text" id="first_name" required autocomplete="given-name" maxlength="75" placeholder="John" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-full text-xs font-mono text-slate-900 focus:outline-none focus:border-[#0b2545] focus:bg-white transition" />
            </div>
            <div>
              <label for="last_name" class="block text-xs font-bold text-[#0b2545] mb-1.5">Last name</label>
              <input type="text" id="last_name" required autocomplete="family-name" maxlength="75" placeholder="Doe" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-full text-xs font-mono text-slate-900 focus:outline-none focus:border-[#0b2545] focus:bg-white transition" />
            </div>
          </div>

          <div>
            <label for="email" class="block text-xs font-bold text-[#0b2545] mb-1.5">Email address</label>
            <input type="email" id="email" required autocomplete="email" placeholder="john@company.com" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-full text-xs font-mono text-slate-900 focus:outline-none focus:border-[#0b2545] focus:bg-white transition" />
          </div>

          <div>
            <label for="phone" class="block text-xs font-bold text-[#0b2545] mb-1.5">Phone number</label>
            <div class="flex gap-2">
              <select id="phone_code" aria-label="Phone country code" class="field-select shrink-0 w-[6.5rem] pl-4 pr-8 py-3 bg-slate-50 border border-slate-200 rounded-full text-xs text-slate-900 focus:outline-none focus:border-[#0b2545] focus:bg-white transition">
                <?php foreach ($signupDialCodes as $dial): ?>
                <option value="<?= $h($dial) ?>"<?= $dial === $defaultDialCode ? ' selected' : '' ?>><?= $h($dial) ?></option>
                <?php endforeach; ?>
              </select>
              <input type="tel" id="phone" required autocomplete="tel-national" inputmode="tel" placeholder="801 234 5678" class="min-w-0 flex-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-full text-xs font-mono text-slate-900 focus:outline-none focus:border-[#0b2545] focus:bg-white transition" />
            </div>
          </div>

          <div>
            <label for="password" class="block text-xs font-bold text-[#0b2545] mb-1.5">Password</label>
            <div class="relative">
              <input type="password" id="password" required minlength="8" autocomplete="new-password" placeholder="At least 8 characters" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-full text-xs font-mono text-slate-900 focus:outline-none focus:border-[#0b2545] focus:bg-white transition pr-10" />
              <button type="button" id="toggle-password" aria-label="Show password" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>

          <!-- Business type -->
          <fieldset data-business-only class="pt-1">
            <legend class="block text-xs font-bold text-[#0b2545] mb-2.5">What type of business do you own?</legend>
            <div class="space-y-3">
              <label class="block cursor-pointer">
                <input type="radio" name="business_stage" value="starter" class="sr-only peer" />
                <span class="flex items-start gap-3 p-4 rounded-2xl border border-slate-200 bg-white transition hover:border-slate-300 peer-checked:border-[#0b2545] peer-checked:bg-[#eef4ff] peer-focus-visible:ring-2 peer-focus-visible:ring-[#0b2545]">
                  <span class="radio-dot mt-0.5"></span>
                  <span>
                    <span class="block text-sm font-bold text-[#0b2545]">Starter Business</span>
                    <span class="block text-xs text-slate-500 mt-1 leading-relaxed">I'm testing my ideas with real customers, and preparing to register my company</span>
                  </span>
                </span>
              </label>
              <label class="block cursor-pointer">
                <input type="radio" name="business_stage" value="registered" class="sr-only peer" />
                <span class="flex items-start gap-3 p-4 rounded-2xl border border-slate-200 bg-white transition hover:border-slate-300 peer-checked:border-[#0b2545] peer-checked:bg-[#eef4ff] peer-focus-visible:ring-2 peer-focus-visible:ring-[#0b2545]">
                  <span class="radio-dot mt-0.5"></span>
                  <span>
                    <span class="block text-sm font-bold text-[#0b2545]">Registered Business</span>
                    <span class="block text-xs text-slate-500 mt-1 leading-relaxed">My business has the approval, documentation, and licences required to operate legally</span>
                  </span>
                </span>
              </label>
            </div>
          </fieldset>

          <!-- Terms Agreement -->
          <div class="flex items-start gap-2.5 pt-1">
            <input type="checkbox" id="terms" required class="mt-0.5 rounded border-slate-300 text-[#0b2545] focus:ring-[#0b2545] shrink-0" />
            <label for="terms" class="text-[11px] text-slate-600 leading-relaxed">
              I agree to Flowwithlit's <a href="terms" class="text-blue-600 hover:underline font-semibold" target="_blank">Terms of Service</a>, <a href="privacy" class="text-blue-600 hover:underline font-semibold" target="_blank">Privacy Policy</a>, and <a href="aml-kyc-policy" class="text-blue-600 hover:underline font-semibold" target="_blank">AML/KYC Policy</a>.
            </label>
          </div>

          <!-- Full-Width Submit Button -->
          <div class="pt-2">
            <button type="submit" id="submit-btn" class="w-full group inline-flex items-center justify-between bg-[#0b2545] hover:bg-[#071930] text-white text-xs font-semibold py-3.5 pl-6 pr-2 rounded-full shadow-md transition-all duration-300">
              <span id="btn-text">Create Account</span>
              <span class="w-7 h-7 rounded-full bg-white text-[#0b2545] flex items-center justify-center font-bold shrink-0 transition-transform duration-300 group-hover:scale-105">
                <svg id="btn-arrow" class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M7 17L17 7M17 7H7M17 7V17"/></svg>
                <svg id="btn-spinner" class="w-3.5 h-3.5 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
              </span>
            </button>
          </div>

        </form>

        <!-- ALREADY HAVE AN ACCOUNT? SIGN IN LINK AT THE BOTTOM OF THE FORM CARD -->
        <div class="mt-8 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
          Already have an account? <a href="login" class="font-bold text-[#0b2545] hover:underline">Sign in to your account</a>
        </div>

      </div>

      <!-- Bottom Footer Links inside Form Column -->
      <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-slate-400">
        <div>Protected by 256-Bit SSL Encryption</div>
        <div class="flex items-center gap-4">
          <a href="terms" class="hover:text-slate-600 transition">Terms</a>
          <a href="privacy" class="hover:text-slate-600 transition">Privacy</a>
          <a href="contact" class="hover:text-slate-600 transition">Support</a>
        </div>
      </div>

    </div>

  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const alertBox = document.getElementById('alert-box');
      const registerForm = document.getElementById('register-form');
      const countrySelect = document.getElementById('country');
      const phoneCodeSelect = document.getElementById('phone_code');
      const businessNameInput = document.getElementById('business_name');
      const firstNameInput = document.getElementById('first_name');
      const lastNameInput = document.getElementById('last_name');
      const emailInput = document.getElementById('email');
      const phoneInput = document.getElementById('phone');
      const passwordInput = document.getElementById('password');
      const businessOnly = document.querySelectorAll('[data-business-only]');

      function showAlert(msg) {
        alertBox.textContent = msg;
        alertBox.classList.remove('hidden');
        alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }

      function hideAlert() {
        alertBox.classList.add('hidden');
        alertBox.textContent = '';
      }

      const accountType = () => registerForm.querySelector('input[name="account_type"]:checked').value;

      // Business-only fields (business name, business type) hide for Personal accounts.
      function syncAccountType() {
        const isBusiness = accountType() === 'business';
        businessOnly.forEach(el => el.classList.toggle('hidden', !isBusiness));
      }
      registerForm.querySelectorAll('input[name="account_type"]').forEach(r => r.addEventListener('change', () => { hideAlert(); syncAccountType(); }));
      syncAccountType();

      // Picking an approved country sets its dialling code (e.g. Nigeria → +234).
      countrySelect.addEventListener('change', () => {
        const dial = countrySelect.selectedOptions[0]?.dataset.dial;
        if (dial) phoneCodeSelect.value = dial;
      });

      // Toggle password visibility
      const togglePasswordBtn = document.getElementById('toggle-password');
      togglePasswordBtn.addEventListener('click', () => {
        const show = passwordInput.type === 'password';
        passwordInput.type = show ? 'text' : 'password';
        togglePasswordBtn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
      });

      // Form Submission Handler (AJAX)
      const submitBtn = document.getElementById('submit-btn');
      const btnText = document.getElementById('btn-text');
      const btnArrow = document.getElementById('btn-arrow');
      const btnSpinner = document.getElementById('btn-spinner');

      function setLoading(loading, label) {
        submitBtn.disabled = loading;
        btnText.innerText = label || 'Create Account';
        btnArrow.classList.toggle('hidden', loading);
        btnSpinner.classList.toggle('hidden', !loading);
      }

      function fail(msg, input) {
        showAlert(msg);
        if (input) input.focus({ preventScroll: true });
      }

      registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        hideAlert();

        const type = accountType();
        const isBusiness = type === 'business';
        const business_name = businessNameInput.value.trim();
        const first_name = firstNameInput.value.trim();
        const last_name = lastNameInput.value.trim();
        const email = emailInput.value.trim();
        const phone = phoneInput.value.trim();
        const password = passwordInput.value;
        const stageInput = registerForm.querySelector('input[name="business_stage"]:checked');
        const terms = document.getElementById('terms').checked;

        if (isBusiness && !business_name) return fail('Please enter your business name.', businessNameInput);
        if (!first_name) return fail('Please enter your first name.', firstNameInput);
        if (!last_name) return fail('Please enter your last name.', lastNameInput);
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return fail('Please enter a valid email address.', emailInput);
        const digits = phone.replace(/\D+/g, '').replace(/^0+/, '');
        if (digits.length < 6 || digits.length > 14) return fail('Please enter a valid phone number.', phoneInput);
        if (password.length < 8) return fail('Password must be at least 8 characters long.', passwordInput);
        if (isBusiness && !stageInput) return fail('Please tell us what type of business you own.');
        if (!terms) return fail('You must agree to the Terms of Service and Privacy Policy to continue.');

        setLoading(true, 'Creating Account...');

        try {
          const res = await fetch('api/auth/register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              account_type: type,
              country: countrySelect.value,
              business_name: isBusiness ? business_name : '',
              business_stage: isBusiness ? stageInput.value : '',
              first_name,
              last_name,
              email,
              phone_code: phoneCodeSelect.value,
              phone,
              password
            })
          });

          const data = await res.json();

          if (res.ok && data.success) {
            localStorage.setItem('flow_token', data.token);
            localStorage.setItem('flow_user', JSON.stringify(data.user));
            window.location.href = `verify-otp?type=email_verification&email=${encodeURIComponent(email)}`;
          } else {
            showAlert(data.error || 'Registration failed. Please check your details and try again.');
            setLoading(false);
          }
        } catch (err) {
          showAlert('Network error connecting to registration server.');
          setLoading(false);
        }
      });

    });
  </script>
  <?php if ($googleClientId !== ''): ?><script src="assets/auth/google-signin.js?v=1" defer></script><?php endif; ?>
</body>
</html>
