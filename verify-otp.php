<?php
/**
 * Flowwithlit Email & Security OTP Verification Page
 * 2-column split layout matching register.php & login.php
 */
$pageTitle = 'Security Verification — Flowwithlit';
$pageDesc  = 'Verify your email address or enter your 6-digit security OTP to complete setup on Flowwithlit.';
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
    .otp-field {
      width: calc((100% - 2.5rem) / 6);
      flex: 1 1 0;
      height: 3.25rem;
      text-align: center;
      font-size: 1.25rem;
      font-weight: 700;
      border-radius: 0.75rem;
      border: 1px solid #cbd5e1;
      background-color: #f8fafc;
      color: #0b2545;
      transition: all 0.15s ease;
    }
    .otp-field:focus {
      outline: none;
      border-color: #0b2545;
      background-color: #ffffff;
      box-shadow: 0 0 0 3px rgba(11, 37, 69, 0.15);
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
          Security first.
        </h1>
        <p class="text-white/85 text-sm sm:text-base leading-relaxed font-normal">
          Verify your account to activate seamless cross-border transfers, virtual cards, and instant crypto payouts.
        </p>
        <div class="pt-2">
          <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-full text-xs text-white/90 border border-white/20 font-medium">
            <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span>Bank-Grade Security &amp; End-to-End Encryption</span>
          </div>
        </div>
      </div>
    </div>

    <!-- COLUMN 2: VERIFICATION CONTAINER (Right Side) -->
    <div class="lg:col-span-7 flex flex-col justify-between p-6 sm:p-10 md:p-14 bg-white min-h-screen">
      
      <!-- Top Spacer -->
      <!-- Brand mark: stays above the form without adding a tall header on mobile. -->
      <a href="index" class="flex items-center justify-center lg:justify-end pt-2 sm:pt-3 pb-2" aria-label="Flowwithlit home">
        <img src="logo/flowwithlit-wordmark.png" alt="Flowwithlit" class="h-8 sm:h-9 w-auto object-contain" />
      </a>

      <!-- Center Content Container -->
      <div class="max-w-md w-full mx-auto my-auto py-4">
        
        <!-- Header -->
        <div class="mb-6 text-center sm:text-left">
          <div class="w-12 h-12 rounded-2xl bg-[#eef4ff] text-[#0b2545] border border-blue-200 flex items-center justify-center sm:mx-0 mx-auto mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
          </div>
          <h1 id="verify-title" class="text-2xl font-bold text-[#0b2545]">Verify Security Code</h1>
          <p id="verify-desc" class="text-xs text-slate-500 mt-1">We sent a 6-digit verification code to <span id="display-email" class="font-bold text-slate-800">your email</span></p>
        </div>

        <!-- Alert Message Box -->
        <div id="alert-box" class="hidden mb-5 p-3.5 rounded-xl text-xs font-medium border border-red-200 bg-red-50 text-red-700"></div>

        <!-- OTP Input Form -->
        <form id="otp-form" class="space-y-6">
          <div class="flex w-full items-center gap-2">
            <input type="text" maxlength="1" class="otp-field" data-index="0" autofocus autocomplete="off" inputmode="numeric" />
            <input type="text" maxlength="1" class="otp-field" data-index="1" autocomplete="off" inputmode="numeric" />
            <input type="text" maxlength="1" class="otp-field" data-index="2" autocomplete="off" inputmode="numeric" />
            <input type="text" maxlength="1" class="otp-field" data-index="3" autocomplete="off" inputmode="numeric" />
            <input type="text" maxlength="1" class="otp-field" data-index="4" autocomplete="off" inputmode="numeric" />
            <input type="text" maxlength="1" class="otp-field" data-index="5" autocomplete="off" inputmode="numeric" />
          </div>

          <button type="submit" id="submit-btn" class="w-full flex items-center justify-between bg-[#0b2545] hover:bg-[#071930] text-white text-xs font-semibold py-3.5 px-5 rounded-xl shadow-md transition group">
            <span id="btn-text">Verify &amp; Continue</span>
            <div id="btn-icon-box" class="w-7 h-7 rounded-full bg-white text-[#0b2545] flex items-center justify-center transition-transform group-hover:translate-x-0.5">
              <svg id="btn-arrow" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
              <svg id="btn-spinner" class="w-3.5 h-3.5 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            </div>
          </button>
        </form>

        <!-- Resend Code section -->
        <div class="mt-6 text-center sm:text-left text-xs text-slate-500">
          Didn't receive the code? 
          <button id="resend-btn" type="button" class="font-bold text-[#0b2545] hover:underline disabled:opacity-50 disabled:no-underline">
            Resend Code <span id="timer-text">(30s)</span>
          </button>
        </div>

      </div>

      <!-- Bottom Sign In / Wrong Email Link -->
      <div class="pt-6 border-t border-slate-100 text-center sm:text-left text-xs text-slate-500">
        Entered the wrong email? 
        <a href="register" class="font-semibold text-[#0b2545] hover:underline">Change Email / Register again</a>
      </div>

    </div>

  </div>

  <script>
    const urlParams = new URLSearchParams(window.location.search);
    const email = urlParams.get('email') || '';
    const type  = urlParams.get('type') || 'email_verification';

    const displayEmail = document.getElementById('display-email');
    displayEmail.innerText = email || 'your email';

    if (type === '2fa') {
      document.getElementById('verify-title').innerText = '2FA Authentication Code';
      document.getElementById('verify-desc').innerHTML = `Enter the 2FA code generated for <span class="font-bold text-slate-800">${email || 'your account'}</span>`;
    }

    // OTP Input Field Movement Logic
    const otpInputs = document.querySelectorAll('.otp-field');
    
    otpInputs.forEach((input, index) => {
      input.addEventListener('input', (e) => {
        const val = e.target.value.replace(/[^0-9]/g, '');
        e.target.value = val;
        if (val && index < otpInputs.length - 1) {
          otpInputs[index + 1].focus();
        }
      });

      input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !input.value && index > 0) {
          otpInputs[index - 1].focus();
        } else if (e.key === 'ArrowLeft' && index > 0) {
          otpInputs[index - 1].focus();
        } else if (e.key === 'ArrowRight' && index < otpInputs.length - 1) {
          otpInputs[index + 1].focus();
        }
      });

      input.addEventListener('paste', (e) => {
        e.preventDefault();
        const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim().replace(/[^0-9]/g, '');
        if (pasteData) {
          for (let i = 0; i < otpInputs.length; i++) {
            otpInputs[i].value = pasteData[i] || '';
          }
          const nextFocus = Math.min(pasteData.length, otpInputs.length - 1);
          otpInputs[nextFocus].focus();
        }
      });
    });

    // Alert helper
    function showAlert(msg, isError = true) {
      const alertBox = document.getElementById('alert-box');
      alertBox.className = `mb-5 p-3.5 rounded-xl text-xs font-medium border ${
        isError 
          ? 'border-red-200 bg-red-50 text-red-700' 
          : 'border-blue-200 bg-blue-50 text-blue-900'
      }`;
      alertBox.innerText = msg;
      alertBox.classList.remove('hidden');
    }

    function hideAlert() {
      document.getElementById('alert-box').classList.add('hidden');
    }

    // Resend Timer logic
    let timerCount = 30;
    let timerInterval = null;
    const resendBtn = document.getElementById('resend-btn');
    const timerText = document.getElementById('timer-text');

    function startResendTimer() {
      resendBtn.disabled = true;
      timerCount = 30;
      timerText.innerText = `(${timerCount}s)`;
      
      if (timerInterval) clearInterval(timerInterval);
      
      timerInterval = setInterval(() => {
        timerCount--;
        if (timerCount <= 0) {
          clearInterval(timerInterval);
          resendBtn.disabled = false;
          timerText.innerText = '';
        } else {
          timerText.innerText = `(${timerCount}s)`;
        }
      }, 1000);
    }

    startResendTimer();

    resendBtn.addEventListener('click', async () => {
      if (!email) {
        showAlert('Email is missing. Please restart verification.');
        return;
      }

      try {
        resendBtn.disabled = true;
        timerText.innerText = '(sending...)';

        const res = await fetch('api/auth/resend-otp.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email, type })
        });
        const data = await res.json();

        if (data.success) {
          showAlert(data.message || 'A new verification code has been sent to your email.', false);
          startResendTimer();
        } else {
          showAlert(data.error || 'Failed to resend code. Please try again.');
          resendBtn.disabled = false;
          timerText.innerText = '';
        }
      } catch (err) {
        showAlert('Network error while resending code.');
        resendBtn.disabled = false;
        timerText.innerText = '';
      }
    });

    // Form Submit Handler
    document.getElementById('otp-form').addEventListener('submit', async (e) => {
      e.preventDefault();
      hideAlert();

      let code = '';
      otpInputs.forEach(i => code += i.value.trim());

      if (code.length !== 6) {
        showAlert('Please enter all 6 digits of your verification code.');
        return;
      }

      if (!email) {
        showAlert('Verification context missing. Please enter your email or re-register.');
        return;
      }

      const submitBtn = document.getElementById('submit-btn');
      const btnText   = document.getElementById('btn-text');
      const btnArrow  = document.getElementById('btn-arrow');
      const btnSpinner= document.getElementById('btn-spinner');

      submitBtn.disabled = true;
      btnText.innerText  = 'Verifying...';
      btnArrow.classList.add('hidden');
      btnSpinner.classList.remove('hidden');

      try {
        const token = localStorage.getItem('flow_token');
        const endpoint = type === '2fa' ? 'api/auth/totp-login.php' : 'api/auth/verify-otp.php';
        const res = await fetch(endpoint, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', ...(type === '2fa' && token ? { Authorization: `Bearer ${token}` } : {}) },
          body: JSON.stringify(type === '2fa' ? { code, token } : { email, code, type })
        });
        const data = await res.json();

        if (data.success) {
          showAlert(data.message || 'Verification successful! Redirecting...', false);
          setTimeout(() => {
            const savedUser = JSON.parse(localStorage.getItem('flow_user') || '{}');
            window.location.href = type === '2fa' && savedUser.onboarding_complete ? 'app/index.php' : 'app/onboarding.php';
          }, 1200);
        } else {
          showAlert(data.error || 'Invalid or expired verification code.');
          submitBtn.disabled = false;
          btnText.innerText  = 'Verify & Continue';
          btnArrow.classList.remove('hidden');
          btnSpinner.classList.add('hidden');
        }
      } catch (err) {
        showAlert('Network error verifying code. Please try again.');
        submitBtn.disabled = false;
        btnText.innerText  = 'Verify & Continue';
        btnArrow.classList.remove('hidden');
        btnSpinner.classList.add('hidden');
      }
    });
  </script>
</body>
</html>
