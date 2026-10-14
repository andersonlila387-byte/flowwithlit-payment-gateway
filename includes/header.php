<?php
/**
 * Flowwithlit Global Header Component
 * Ultra-fast PHP inclusion with zero layout shift & zero extra client HTTP requests.
 */

if (!isset($pageTitle)) $pageTitle = 'Flowwithlit — Pay, Swap & Transfer Money with Crypto &amp; Naira';
if (!isset($pageDesc)) $pageDesc = 'Flowwithlit is your all-in-one payment gateway. Pay with Crypto or Naira, convert currencies instantly, and send money fast.';
if (!isset($activePage)) $activePage = 'home';
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = preg_replace('/[^A-Za-z0-9.:-]/', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost')) ?: 'localhost';
$appUrl = rtrim((string) (getenv('FLOW_APP_URL') ?: $scheme . '://' . $host), '/');
$socialImage = $appUrl . '/logo/flowwithlit-payment-success-social.png';
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($pageDesc); ?>" />
  <meta name="theme-color" content="#0b2545" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?>" />
  <meta property="og:description" content="<?php echo htmlspecialchars($pageDesc); ?>" />
  <meta property="og:image" content="<?php echo htmlspecialchars($socialImage, ENT_QUOTES, 'UTF-8'); ?>" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:image" content="<?php echo htmlspecialchars($socialImage, ENT_QUOTES, 'UTF-8'); ?>" />
    <link rel="icon" type="image/png" sizes="32x32" href="logo/flowwithlit-icon-32.png" />
    <link rel="apple-touch-icon" sizes="192x192" href="logo/flowwithlit-icon-192.png" />
  <link rel="manifest" href="site.webmanifest" />
  <script type="application/ld+json">{"@context":"https://schema.org","@type":"Organization","name":"Flowwithlit","logo":"logo/flowwithlit-icon-512.png","description":"Payments, wallets, transfers and business tools."}</script>
  
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
            mono: ['"IBM Plex Mono"', 'monospace'],
          },
          colors: {
            brand: {
              50: '#eef4ff',
              100: '#d9e6ff',
              500: '#0b2545',
              600: '#071930',
            },
            ink: '#0b2545',
            mint: '#eef4ff',
          }
        }
      }
    }
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
      background-image: linear-gradient(180deg, rgba(10, 18, 30, 0.5) 0%, rgba(10, 18, 30, 0.75) 100%), url('images/75nmeaVY0yWUShlVFysSmJb9Q.png');
      background-size: cover;
      background-position: center 30%;
    }
    .reveal {
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .reveal.in {
      opacity: 1;
      transform: translateY(0);
    }
    .badge-pill {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background-color: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(8px);
      border: 1px solid rgba(11, 37, 69, 0.12);
      color: #0b2545;
      font-size: 0.75rem;
      font-weight: 600;
      padding: 0.375rem 0.875rem;
      border-radius: 9999px;
    }
    .badge-dot {
      width: 0.45rem;
      height: 0.45rem;
      border-radius: 9999px;
      background-color: #0b2545;
    }
    .nature-card-bg {
      background-image: linear-gradient(180deg, rgba(10, 18, 30, 0.55) 0%, rgba(10, 18, 30, 0.8) 100%), url('images/75nmeaVY0yWUShlVFysSmJb9Q.png');
      background-size: cover;
      background-position: center bottom;
    }
    details summary::-webkit-details-marker { display: none; }
  </style>
</head>
<body class="bg-white text-ink antialiased selection:bg-[#0b2545] selection:text-white">

  <!-- STICKY HEADER -->
  <header id="main-header" class="fixed top-0 inset-x-0 z-50 transition-all duration-300 ease-out py-3 px-6">
    <nav class="max-w-7xl mx-auto flex items-center justify-between">
      <!-- Official Flowwithlit wordmark -->
      <a href="index" id="header-logo" class="flex items-center shrink-0 transition-all duration-300" aria-label="Flowwithlit home">
        <img src="logo/flowwithlit-wordmark.png" alt="Flowwithlit" class="h-9 sm:h-10 w-auto object-contain" />
      </a>

      <!-- Desktop Nav Links -->
      <div id="header-nav-links" class="hidden md:flex items-center gap-8 text-sm font-medium text-white/90 transition-colors duration-300">
        <a href="index#features" class="hover:text-blue-400 transition">Features</a>
        <a href="index#how-it-works" class="hover:text-blue-400 transition">How it Works</a>
        <a href="about" class="<?php echo ($activePage === 'about') ? 'text-blue-400 font-bold' : 'hover:text-blue-400'; ?> transition">About Us</a>
        <a href="developers" class="<?php echo ($activePage === 'developers') ? 'text-blue-400 font-bold' : 'hover:text-blue-400'; ?> transition">Developers</a>
        <a href="contact" class="<?php echo ($activePage === 'contact') ? 'text-blue-400 font-bold' : 'hover:text-blue-400'; ?> transition">Contact Us</a>
      </div>

      <!-- Desktop Dual CTAs -->
      <div class="hidden md:flex items-center gap-3">
        <!-- Sign In Button: Navy Blue BG with White Circle Arrow Icon -->
        <a href="login" id="header-login-btn" class="group inline-flex items-center gap-3 bg-[#0b2545] hover:bg-[#071930] transition-all duration-300 text-white text-sm font-semibold pl-5 pr-1.5 py-1.5 rounded-full shadow-md border border-white/10">
          <span>Sign In</span>
          <span class="w-7 h-7 rounded-full bg-white text-[#0b2545] flex items-center justify-center font-bold shadow-sm transition-transform duration-300 group-hover:scale-105">
            <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M7 17L17 7M17 7H7M17 7V17"/>
            </svg>
          </span>
        </a>

        <!-- Sign Up / Create Account Button: White BG with Circle Arrow & Hover Effect -->
        <a href="register" id="header-cta" class="group inline-flex items-center gap-3 bg-white hover:bg-[#eef4ff] transition-all duration-300 text-[#0b2545] text-sm font-semibold pl-5 pr-1.5 py-1.5 rounded-full border border-slate-200 shadow-sm">
          <span>Create Account</span>
          <span class="w-7 h-7 rounded-full bg-[#0b2545] text-white flex items-center justify-center font-bold shadow-sm transition-transform duration-300 group-hover:scale-105">
            <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M7 17L17 7M17 7H7M17 7V17"/>
            </svg>
          </span>
        </a>
      </div>

      <!-- Mobile Hamburger Button -->
      <button id="mobile-menu-btn" aria-label="Toggle menu" class="md:hidden text-white transition-colors duration-300 p-2 focus:outline-none z-50">
        <svg id="menu-icon-open" class="w-7 h-7 block" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        <svg id="menu-icon-close" class="w-7 h-7 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </nav>
  </header>

  <!-- FULL SLIDE-DOWN MOBILE MENU OVERLAY -->
  <div id="mobile-menu-overlay" class="fixed inset-0 z-40 bg-white flex flex-col justify-between p-8 pt-32 transform -translate-y-full transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] md:hidden">
    <div class="flex flex-col space-y-7 text-center pt-8">
      <a href="index#features" class="mobile-nav-link text-2xl font-bold text-[#0b2545] hover:text-blue-600 transition">Features</a>
      <a href="index#how-it-works" class="mobile-nav-link text-2xl font-bold text-[#0b2545] hover:text-blue-600 transition">How it Works</a>
      <a href="about" class="mobile-nav-link text-2xl font-bold <?php echo ($activePage === 'about') ? 'text-blue-600' : 'text-[#0b2545]'; ?> hover:text-blue-600 transition">About Us</a>
      <a href="developers" class="mobile-nav-link text-2xl font-bold <?php echo ($activePage === 'developers') ? 'text-blue-600' : 'text-[#0b2545]'; ?> hover:text-blue-600 transition">Developers</a>
      <a href="contact" class="mobile-nav-link text-2xl font-bold <?php echo ($activePage === 'contact') ? 'text-blue-600' : 'text-[#0b2545]'; ?> hover:text-blue-600 transition">Contact Us</a>
    </div>

    <div class="w-full pb-8 flex flex-col gap-3">
      <a href="login" class="group mobile-nav-link w-full inline-flex items-center justify-between bg-[#0b2545] hover:bg-[#071930] text-white text-base font-semibold pl-6 pr-2 py-2 rounded-full transition shadow-md">
        <span>Sign In</span>
        <span class="w-7 h-7 rounded-full bg-white text-[#0b2545] flex items-center justify-center font-bold">
          <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M7 17L17 7M17 7H7M17 7V17"/>
          </svg>
        </span>
      </a>
      <a href="register" class="group mobile-nav-link w-full inline-flex items-center justify-between bg-slate-100 hover:bg-slate-200 text-[#0b2545] text-base font-semibold pl-6 pr-2 py-2 rounded-full border border-slate-200">
        <span>Create Free Account</span>
        <span class="w-7 h-7 rounded-full bg-[#0b2545] text-white flex items-center justify-center font-bold">
          <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M7 17L17 7M17 7H7M17 7V17"/>
          </svg>
        </span>
      </a>
    </div>
  </div>
