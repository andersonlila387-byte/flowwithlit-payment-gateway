<?php
/**
 * Flowwithlit Registration Page
 * Clean 2-column split design with top back-arrow icon on Step 2.
 */
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
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Smooch+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />

  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { 
            sans: ['"Smooch Sans"', 'sans-serif'],
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
      font-size: 17px;
    }
    body { 
      font-family: 'Smooch Sans', sans-serif; 
      font-weight: 600;
      letter-spacing: 0.025em;
      color: #0b2545; 
      background-color: #ffffff;
      -webkit-font-smoothing: antialiased;
    }
    .text-xs {
      font-size: 0.875rem !important;
      font-weight: 600;
      letter-spacing: 0.02em;
    }
    .text-\[10px\], .text-\[11px\] {
      font-size: 0.8125rem !important;
      font-weight: 600;
      letter-spacing: 0.02em;
    }
    .text-sm {
      font-size: 1.025rem !important;
    }
    .hero-bg {
      background-image: linear-gradient(180deg, rgba(10, 18, 30, 0.75) 0%, rgba(10, 18, 30, 0.9) 100%), url('images/75nmeaVY0yWUShlVFysSmJb9Q.png');
      background-size: cover; 
      background-position: center;
    }
    .step-transition {
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
  </style>
</head>
<body class="bg-white text-ink antialiased min-h-screen">

  <div class="min-h-screen grid lg:grid-cols-12">
    
    <!-- COLUMN 1: VISUAL COLUMN (Desktop Only - Motivational Assurance) -->
    <div class="hidden lg:flex lg:col-span-5 hero-bg relative flex-col justify-center p-12 text-white overflow-hidden">
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
      
      <!-- Brand mark: stays above the form without adding a tall header on mobile. -->
      <a href="index" class="flex items-center justify-center lg:justify-end pt-2 sm:pt-3 pb-2" aria-label="Flowwithlit home">
        <img src="logo/flowwithlit-wordmark.png" alt="Flowwithlit" class="h-8 sm:h-9 w-auto object-contain" />
      </a>

      <!-- Center Form Content Container -->
      <div class="max-w-md w-full mx-auto my-auto py-4">
        
        <!-- Header with Back Arrow Icon for Step 2 -->
        <div class="mb-6 flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-[#0b2545]">Create Account</h1>
            <p class="text-xs text-slate-500 mt-1">Enter your details to get started with Flowwithlit</p>
          </div>

          <!-- Top Back Arrow Icon Button (Hidden on Step 1, Visible on Step 2) -->
          <button type="button" id="btn-back-to-step-1" class="hidden p-2 rounded-full bg-slate-100 hover:bg-slate-200 text-[#0b2545] transition" title="Go back to Step 1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
          </button>
        </div>

        <!-- Alert Message Box -->
        <div id="alert-box" class="hidden mb-5 p-3.5 rounded-xl text-xs font-medium border border-red-200 bg-red-50 text-red-700"></div>

        <!-- GOOGLE SIGN-UP OPTION AT THE TOP -->
        <button id="google-signup-btn" type="button" class="w-full flex items-center justify-center gap-3 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold py-3 px-4 rounded-xl transition shadow-sm mb-5">
          <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
          </svg>
          <span>Sign up with Google</span>
        </button>

        <!-- Or Divider -->
        <div class="relative flex items-center justify-center mb-6">
          <div class="border-t border-slate-200 w-full"></div>
          <span class="bg-white px-3 text-[10px] uppercase tracking-wider text-slate-400 font-bold absolute">OR REGISTER WITH EMAIL</span>
        </div>

        <!-- Registration Multi-Step Form (Max 3 inputs per view) -->
        <form id="register-form" autocomplete="on">
          
          <!-- STEP 1 PANEL (Max 3 Inputs: Name, Email, Phone) -->
          <div id="step-1-panel" class="space-y-4 step-transition">
            <div>
              <label for="full_name" class="block text-xs font-bold text-[#0b2545] mb-1.5">Full Legal Name</label>
              <input type="text" id="full_name" required placeholder="Pamakinwa Olayegundoye" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-[#0b2545] focus:bg-white transition" />
            </div>

            <div>
              <label for="email" class="block text-xs font-bold text-[#0b2545] mb-1.5">Email Address</label>
              <input type="email" id="email" required placeholder="name@company.com" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-[#0b2545] focus:bg-white transition" />
            </div>

            <div>
              <label for="phone" class="block text-xs font-bold text-[#0b2545] mb-1.5">Phone Number (Optional)</label>
              <input type="tel" id="phone" placeholder="+234 801 234 5678" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-[#0b2545] focus:bg-white transition" />
            </div>

            <button type="button" id="btn-to-step-2" class="w-full group inline-flex items-center justify-between bg-[#0b2545] hover:bg-[#071930] text-white text-xs font-semibold py-3.5 pl-6 pr-2 rounded-xl shadow-md transition-all duration-300 mt-2">
              <span>Continue to Password</span>
              <span class="w-7 h-7 rounded-full bg-white text-[#0b2545] flex items-center justify-center font-bold shrink-0 transition-transform duration-300 group-hover:scale-105">
                <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M7 17L17 7M17 7H7M17 7V17"/></svg>
              </span>
            </button>
          </div>

          <!-- STEP 2 PANEL (Max 3 Controls: Password, Confirm Password, Terms Checkbox) -->
          <div id="step-2-panel" class="hidden space-y-4 step-transition">
            <div>
              <label for="password" class="block text-xs font-bold text-[#0b2545] mb-1.5">Create Password (Min 8 Characters)</label>
              <div class="relative">
                <input type="password" id="password" minlength="8" placeholder="••••••••" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-[#0b2545] focus:bg-white transition pr-10" />
                <button type="button" id="toggle-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
              </div>
            </div>

            <div>
              <label for="confirm_password" class="block text-xs font-bold text-[#0b2545] mb-1.5">Confirm Password</label>
              <div class="relative">
                <input type="password" id="confirm_password" minlength="8" placeholder="••••••••" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-[#0b2545] focus:bg-white transition pr-10" />
                <button type="button" id="toggle-confirm-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
              </div>
            </div>

            <!-- Terms Agreement -->
            <div class="flex items-start gap-2.5 pt-1">
              <input type="checkbox" id="terms" required class="mt-0.5 rounded border-slate-300 text-[#0b2545] focus:ring-[#0b2545] shrink-0" />
              <label for="terms" class="text-[11px] text-slate-600 leading-relaxed">
                I agree to Flowwithlit's <a href="terms" class="text-blue-600 hover:underline font-semibold" target="_blank">Terms of Service</a>, <a href="privacy" class="text-blue-600 hover:underline font-semibold" target="_blank">Privacy Policy</a>, and <a href="aml-kyc-policy" class="text-blue-600 hover:underline font-semibold" target="_blank">AML/KYC Policy</a>.
              </label>
            </div>

            <!-- Full-Width Submit Button -->
            <div class="pt-2">
              <button type="submit" id="submit-btn" class="w-full group inline-flex items-center justify-between bg-[#0b2545] hover:bg-[#071930] text-white text-xs font-semibold py-3.5 pl-6 pr-2 rounded-xl shadow-md transition-all duration-300">
                <span id="btn-text">Create Account</span>
                <span class="w-7 h-7 rounded-full bg-white text-[#0b2545] flex items-center justify-center font-bold shrink-0 transition-transform duration-300 group-hover:scale-105">
                  <svg id="btn-arrow" class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M7 17L17 7M17 7H7M17 7V17"/></svg>
                  <svg id="btn-spinner" class="w-3.5 h-3.5 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </span>
              </button>
            </div>
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
      const step1Panel = document.getElementById('step-1-panel');
      const step2Panel = document.getElementById('step-2-panel');
      const btnToStep2 = document.getElementById('btn-to-step-2');
      const btnBackToStep1 = document.getElementById('btn-back-to-step-1');

      const fullNameInput = document.getElementById('full_name');
      const emailInput = document.getElementById('email');
      const phoneInput = document.getElementById('phone');
      const passwordInput = document.getElementById('password');
      const confirmPasswordInput = document.getElementById('confirm_password');

      function showAlert(msg) {
        alertBox.classList.remove('hidden');
        alertBox.innerHTML = msg;
      }

      function hideAlert() {
        alertBox.classList.add('hidden');
        alertBox.innerHTML = '';
      }

      // Step 1 -> Step 2 Validation & Navigation
      btnToStep2.addEventListener('click', () => {
        hideAlert();

        const nameVal = fullNameInput.value.trim();
        const emailVal = emailInput.value.trim();

        if (!nameVal) {
          showAlert('Please enter your full legal name.');
          fullNameInput.focus();
          return;
        }

        if (!emailVal || !emailVal.includes('@') || !emailVal.includes('.')) {
          showAlert('Please enter a valid email address.');
          emailInput.focus();
          return;
        }

        // Advance to Step 2 & Show Top Back Arrow Icon
        step1Panel.classList.add('hidden');
        step2Panel.classList.remove('hidden');
        btnBackToStep1.classList.remove('hidden');
        passwordInput.focus();
      });

      // Step 2 -> Step 1 Back Navigation via Top Back Arrow Icon
      btnBackToStep1.addEventListener('click', () => {
        hideAlert();
        step2Panel.classList.add('hidden');
        btnBackToStep1.classList.add('hidden');
        step1Panel.classList.remove('hidden');
      });

      // Toggle Passwords Visibility
      const togglePasswordBtn = document.getElementById('toggle-password');
      if (togglePasswordBtn) {
        togglePasswordBtn.addEventListener('click', () => {
          const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
          passwordInput.setAttribute('type', type);
        });
      }

      const toggleConfirmPasswordBtn = document.getElementById('toggle-confirm-password');
      if (toggleConfirmPasswordBtn) {
        toggleConfirmPasswordBtn.addEventListener('click', () => {
          const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
          confirmPasswordInput.setAttribute('type', type);
        });
      }

      // Form Submission Handler (AJAX)
      const registerForm = document.getElementById('register-form');
      const submitBtn = document.getElementById('submit-btn');
      const btnText = document.getElementById('btn-text');
      const btnArrow = document.getElementById('btn-arrow');
      const btnSpinner = document.getElementById('btn-spinner');

      registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        hideAlert();

        const full_name = fullNameInput.value.trim();
        const email = emailInput.value.trim();
        const phone = phoneInput.value.trim();
        const password = passwordInput.value;
        const confirm_password = confirmPasswordInput.value;
        const terms = document.getElementById('terms').checked;

        if (!password || password.length < 8) {
          showAlert('Password must be at least 8 characters long.');
          passwordInput.focus();
          return;
        }

        if (password !== confirm_password) {
          showAlert('Passwords do not match. Please verify your password entry.');
          confirmPasswordInput.focus();
          return;
        }

        if (!terms) {
          showAlert('You must agree to the Terms of Service and Privacy Policy to continue.');
          return;
        }

        submitBtn.disabled = true;
        btnText.innerText = 'Creating Account...';
        btnArrow.classList.add('hidden');
        btnSpinner.classList.remove('hidden');

        try {
          const res = await fetch('api/auth/register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
              full_name, 
              email, 
              phone, 
              password, 
              account_type: 'individual'
            })
          });

          const data = await res.json();

          if (res.ok && data.success) {
            localStorage.setItem('flow_token', data.token);
            localStorage.setItem('flow_user', JSON.stringify(data.user));

            window.location.href = `verify-otp?type=email_verification&email=${encodeURIComponent(email)}`;
          } else {
            showAlert(data.error || 'Registration failed. Please check your details and try again.');
            submitBtn.disabled = false;
            btnText.innerText = 'Create Account';
            btnArrow.classList.remove('hidden');
            btnSpinner.classList.add('hidden');
          }
        } catch (err) {
          showAlert('Network error connecting to registration server.');
          submitBtn.disabled = false;
          btnText.innerText = 'Create Account';
          btnArrow.classList.remove('hidden');
          btnSpinner.classList.add('hidden');
        }
      });

      // Google Sign Up Handler (Simulated Integration)
      document.getElementById('google-signup-btn').addEventListener('click', async () => {
        const mockGoogleUser = {
          google_id: 'google_user_' + Math.floor(Math.random() * 1000000),
          email: 'user_' + Math.floor(Math.random() * 900) + '@gmail.com',
          full_name: 'Google User',
          avatar_url: 'https://lh3.googleusercontent.com/a/default-user'
        };

        submitBtn.disabled = true;
        btnText.innerText = 'Connecting with Google...';
        btnArrow.classList.add('hidden');
        btnSpinner.classList.remove('hidden');

        try {
          const res = await fetch('api/auth/google.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(mockGoogleUser)
          });

          const data = await res.json();
          if (res.ok && data.success) {
            localStorage.setItem('flow_token', data.token);
            localStorage.setItem('flow_user', JSON.stringify(data.user));
            window.location.href = 'dashboard';
          } else {
            showAlert(data.error || 'Google Registration failed.');
            submitBtn.disabled = false;
            btnText.innerText = 'Create Account';
            btnArrow.classList.remove('hidden');
            btnSpinner.classList.add('hidden');
          }
        } catch (err) {
          showAlert('Network error connecting to Google Authentication server.');
          submitBtn.disabled = false;
          btnText.innerText = 'Create Account';
          btnArrow.classList.remove('hidden');
          btnSpinner.classList.add('hidden');
        }
      });
    });
  </script>
</body>
</html>
