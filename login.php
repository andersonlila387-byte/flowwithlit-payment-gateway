<?php
/**
 * Flowwithlit Login Page
 * Simplified 2-column split design matching register.php
 */
$pageTitle = 'Sign In — Flowwithlit';
$pageDesc  = 'Sign in to your Flowwithlit account to manage payments, crypto-naira swaps, and business API keys.';
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
  </style>
</head>
<body class="bg-white text-ink antialiased min-h-screen">

  <div class="min-h-screen grid lg:grid-cols-12">
    
    <!-- COLUMN 1: VISUAL COLUMN (Desktop Only - Welcome Back Message) -->
    <div class="hidden lg:flex lg:col-span-5 hero-bg relative flex-col justify-center p-12 text-white overflow-hidden">
      <div class="space-y-5 max-w-md">
        <span class="inline-block w-8 h-1 bg-blue-400 rounded-full mb-1"></span>
        <h1 class="text-4xl font-bold tracking-tight leading-tight text-white">
          Welcome back.
        </h1>
        <p class="text-white/85 text-sm sm:text-base leading-relaxed font-normal">
          Sign in to access your digital wallets, manage virtual bank accounts, and process seamless global payments.
        </p>
        <div class="pt-2">
          <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-full text-xs text-white/90 border border-white/20 font-medium">
            <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span>Bank-Grade Encryption &amp; Instant Access</span>
          </div>
        </div>
      </div>
    </div>

    <!-- COLUMN 2: LOGIN FORM CONTAINER (Right Side) -->
    <div class="lg:col-span-7 flex flex-col justify-between p-6 sm:p-10 md:p-14 bg-white min-h-screen">
      
      <!-- Brand mark: stays above the form without adding a tall header on mobile. -->
      <a href="index" class="flex items-center justify-center lg:justify-end pt-2 sm:pt-3 pb-2" aria-label="Flowwithlit home">
        <img src="logo/flowwithlit-wordmark.png" alt="Flowwithlit" class="h-8 sm:h-9 w-auto object-contain" />
      </a>

      <!-- Center Form Content Container -->
      <div class="max-w-md w-full mx-auto my-auto py-4">
        
        <!-- Header -->
        <div class="mb-6 text-center sm:text-left">
          <h1 class="text-2xl font-bold text-[#0b2545]">Sign In</h1>
          <p class="text-xs text-slate-500 mt-1">Enter your credentials to access your account</p>
        </div>

        <!-- Alert Message Box -->
        <div id="alert-box" class="hidden mb-5 p-3.5 rounded-xl text-xs font-medium border"></div>

        <!-- GOOGLE SIGN-IN BUTTON AT TOP -->
        <button id="google-login-btn" type="button" class="w-full flex items-center justify-center gap-3 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold py-3 px-4 rounded-xl transition shadow-sm mb-5">
          <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
          </svg>
          <span>Continue with Google</span>
        </button>

        <!-- Or Divider -->
        <div class="relative flex items-center justify-center mb-6">
          <div class="border-t border-slate-200 w-full"></div>
          <span class="bg-white px-3 text-[10px] uppercase tracking-wider text-slate-400 font-bold absolute">OR SIGN IN WITH EMAIL</span>
        </div>

        <!-- Login Form -->
        <form id="login-form" class="space-y-4" autocomplete="on">
          <div>
            <label for="email" class="block text-xs font-bold text-[#0b2545] mb-1.5">Email Address</label>
            <input type="email" id="email" required placeholder="name@company.com" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-[#0b2545] focus:bg-white transition" />
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label for="password" class="block text-xs font-bold text-[#0b2545]">Password</label>
              <a href="forgot-password" class="text-[11px] font-semibold text-blue-600 hover:text-[#0b2545] transition">Forgot password?</a>
            </div>
            <div class="relative">
              <input type="password" id="password" required placeholder="••••••••" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-[#0b2545] focus:bg-white transition pr-10" />
              <button type="button" id="toggle-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>

          <button type="submit" id="submit-btn" class="w-full group inline-flex items-center justify-between bg-[#0b2545] hover:bg-[#071930] text-white text-xs font-semibold py-3.5 pl-6 pr-2 rounded-xl shadow-md transition-all duration-300 mt-4">
            <span id="btn-text">Sign In to Dashboard</span>
            <span class="w-7 h-7 rounded-full bg-white text-[#0b2545] flex items-center justify-center font-bold shrink-0 transition-transform duration-300 group-hover:scale-105">
              <svg id="btn-arrow" class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M7 17L17 7M17 7H7M17 7V17"/></svg>
              <svg id="btn-spinner" class="w-3.5 h-3.5 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            </span>
          </button>
        </form>

        <!-- CREATE NEW ACCOUNT LINK AT THE BOTTOM OF THE FORM CARD -->
        <div class="mt-8 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
          Don't have an account yet? <a href="register" class="font-bold text-[#0b2545] hover:underline">Create a new account</a>
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
      const urlParams = new URLSearchParams(window.location.search);
      const alertBox = document.getElementById('alert-box');

      if (urlParams.get('reason') === 'inactivity') {
        showAlert('Session expired due to 15 minutes of inactivity for your security. Please sign in again.', 'warning');
      } else if (urlParams.get('registered') === '1') {
        showAlert('Account created! Please check your email for the verification code.', 'info');
      }

      function showAlert(msg, type = 'error') {
        alertBox.classList.remove('hidden', 'bg-red-50', 'text-red-700', 'border-red-200', 'bg-amber-50', 'text-amber-800', 'border-amber-200', 'bg-blue-50', 'text-blue-800', 'border-blue-200', 'bg-emerald-50', 'text-emerald-800', 'border-emerald-200');
        if (type === 'error') {
          alertBox.classList.add('bg-red-50', 'text-red-700', 'border-red-200');
        } else if (type === 'warning') {
          alertBox.classList.add('bg-amber-50', 'text-amber-800', 'border-amber-200');
        } else if (type === 'info') {
          alertBox.classList.add('bg-blue-50', 'text-blue-800', 'border-blue-200');
        } else {
          alertBox.classList.add('bg-blue-50', 'text-blue-800', 'border-blue-200');
        }
        alertBox.innerHTML = msg;
      }

      // Toggle Password Visibility
      const togglePasswordBtn = document.getElementById('toggle-password');
      const passwordInput = document.getElementById('password');
      togglePasswordBtn.addEventListener('click', () => {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
      });

      // Form Submission (AJAX)
      const loginForm = document.getElementById('login-form');
      const submitBtn = document.getElementById('submit-btn');
      const btnText = document.getElementById('btn-text');
      const btnArrow = document.getElementById('btn-arrow');
      const btnSpinner = document.getElementById('btn-spinner');

      loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const email = document.getElementById('email').value.trim();
        const password = passwordInput.value;

        if (!email || !password) {
          showAlert('Please fill in both email and password fields.');
          return;
        }

        submitBtn.disabled = true;
        btnText.innerText = 'Verifying Credentials...';
        btnArrow.classList.add('hidden');
        btnSpinner.classList.remove('hidden');
        alertBox.classList.add('hidden');

        try {
          const res = await fetch('api/auth/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
          });

          const data = await res.json();

          if (res.ok && data.success) {
            localStorage.setItem('flow_token', data.token);
            localStorage.setItem('flow_user', JSON.stringify(data.user));

            if (data.requires_2fa) {
              window.location.href = `verify-otp?type=2fa&email=${encodeURIComponent(email)}`;
            } else {
              window.location.href = data.user.onboarding_complete ? 'app/index.php' : 'app/onboarding.php';
            }
          } else {
            showAlert(data.error || 'Login failed. Please check your credentials.');
            submitBtn.disabled = false;
            btnText.innerText = 'Sign In to Dashboard';
            btnArrow.classList.remove('hidden');
            btnSpinner.classList.add('hidden');
          }
        } catch (err) {
          showAlert('Network error connecting to authentication server.');
          submitBtn.disabled = false;
          btnText.innerText = 'Sign In to Dashboard';
          btnArrow.classList.remove('hidden');
          btnSpinner.classList.add('hidden');
        }
      });

      // Google Sign-In Simulation
      document.getElementById('google-login-btn').addEventListener('click', async () => {
        const mockGoogleUser = {
          google_id: 'google_user_' + Math.floor(Math.random() * 1000000),
          email: 'user_' + Math.floor(Math.random() * 900) + '@gmail.com',
          full_name: 'Google Merchant',
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
            window.location.href = data.user.onboarding_complete ? 'app/index.php' : 'app/onboarding.php';
          } else {
            showAlert(data.error || 'Google Authentication failed.');
            submitBtn.disabled = false;
            btnText.innerText = 'Sign In to Dashboard';
            btnArrow.classList.remove('hidden');
            btnSpinner.classList.add('hidden');
          }
        } catch (err) {
          showAlert('Network error connecting to Google Authentication server.');
          submitBtn.disabled = false;
          btnText.innerText = 'Sign In to Dashboard';
          btnArrow.classList.remove('hidden');
          btnSpinner.classList.add('hidden');
        }
      });
    });
  </script>
</body>
</html>
