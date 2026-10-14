<?php
/**
 * Application Header Component
 * Reusable header for all Flowwithlit dashboard pages
 * 
 * Available variables to set before including:
 * - $pageTitle (string): Custom title for the page
 * - $activeNavTab (string): Active pill in center nav (Overview, Introduction, Features, etc.)
 */
$pageTitle = $pageTitle ?? 'Flowwithlit - Financial Dashboard';
$pageDesc = $pageDesc ?? 'Flowwithlit wallet, transfer, withdrawal and payment tools.';
$activeNavTab = $activeNavTab ?? 'Overview';
$appBodyLayout = $appBodyLayout ?? 'h-screen w-screen overflow-hidden';
$appWorkspaceLayout = $appWorkspaceLayout ?? 'h-[calc(100vh-5rem)] overflow-hidden';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <meta name="description" content="<?= htmlspecialchars($pageDesc) ?>" />
  <meta name="robots" content="noindex,nofollow,noarchive" />
  <meta name="theme-color" content="#0b2545" />
    <link rel="icon" type="image/png" sizes="32x32" href="../logo/flowwithlit-icon-32.png" />
    <link rel="apple-touch-icon" sizes="192x192" href="../logo/flowwithlit-icon-192.png" />
    <link rel="manifest" href="../site.webmanifest" />
  <!-- Google Fonts: Smooch Sans (Primary UI) & IBM Plex Mono (Financial Data & References) -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Smooch+Sans:wght@300;400;500;600;700;800;900&display=swap"
    rel="stylesheet"
  />
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Smooch Sans"', 'sans-serif'],
            mono: ['"IBM Plex Mono"', 'ui-monospace', 'SFMono-Regular', 'monospace'],
          },
          colors: {
            brand: {
              50: '#eef4ff',
              100: '#d9e6ff',
              500: '#0b2545',
              600: '#071930',
              green: '#10d07a',
              'green-light': '#e6fbf2',
              dark: '#0b2545',
            },
            ink: '#0b2545',
          },
        },
      },
    };
  </script>
  <!-- Early Theme Initializer (prevents theme flash) -->
  <script>
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  </script>
  <style>
    .dark #sidebarMenu .sidebar-item:hover,
    .dark #sidebarMenu .sidebar-item:focus-visible,
    .dark #profileDropdownMenu a:hover,
    .dark #profileDropdownMenu a:focus-visible {
      background-color:#13304f !important;
      color:#f8fafc !important;
    }
    .dark #profileDropdownMenu a > div:first-child {
      background-color:#193651 !important;
      color:#cbd5e1 !important;
    }
    .dark #profileDropdownMenu #logoutBtn:hover,
    .dark #profileDropdownMenu #logoutBtn:focus-visible {
      background-color:#3b1d2b !important;
      color:#fda4af !important;
    }
    .dark #logoutBtn > div:first-child { background-color:#3b1d2b; color:#fda4af; }
    #profileDropdownMenu { width:260px; max-width:calc(100vw - 24px); max-height:calc(100dvh - 104px); overflow-y:auto; }
    #profileDropdownMenu > div:first-child { padding:12px; gap:8px; }
    #profileDropdownMenu a, #profileDropdownMenu #logoutBtn { padding-block:7px; }
    #profileDropdownMenu:not(.hidden), #notificationDropdownMenu:not(.hidden) { animation:header-menu-in .16s ease-out; }
    @keyframes header-menu-in { from { opacity:0; transform:translateY(-5px); } to { opacity:1; transform:translateY(0); } }
    @media (prefers-reduced-motion:reduce) {
      #profileDropdownMenu:not(.hidden), #notificationDropdownMenu:not(.hidden) { animation:none; }
    }
    @media (max-width:767px) {
      #notificationDropdownMenu { position:fixed; top:calc(5rem + 8px); left:12px; right:12px; width:auto; margin-top:0; max-height:calc(100dvh - 5rem - 24px); overflow-y:auto; }
      #notificationFeed { max-height:calc(100dvh - 5rem - 160px); }
      #profileDropdownMenu { position:fixed; top:calc(5rem + 8px); right:12px; margin-top:0; }
    }
    [data-user-avatar] { display:grid; place-items:center; width:100%; height:100%; overflow:hidden; border-radius:50%; color:white; font-family:Arial,sans-serif; font-size:14px; font-weight:600; letter-spacing:0; }
    [data-user-avatar] .user-avatar-photo { width:100%; height:100%; object-fit:cover; }
    [data-user-avatar] [hidden] { display:none; }
    @media (max-width: 767px) {
      #appHeader { padding-inline:12px; gap:12px; }
      #appHeader > a { gap:6px; flex-shrink:0; }
      #appHeader > a > div { width:32px; height:32px; }
      #appHeader > a > span { font-size:20px; }
      #appHeader > div { gap:2px; margin-left:auto; flex-shrink:0; }
      #themeToggleBtn, #openCommandSearch, #notificationDropdownBtn { width:36px; height:40px; background:transparent; border-color:transparent; box-shadow:none; }
      #themeToggleBtn svg, #openCommandSearch svg, #notificationDropdownBtn svg { width:17px; height:17px; }
      #profileDropdownBtn { min-width:36px; min-height:40px; padding:2px; }
      #profileDropdownBtn > div { width:30px; height:30px; }
      #profileChevron { display:none; }
    }
    html {
      font-size: 17px;
    }
    body {
      font-family: 'Smooch Sans', sans-serif;
      font-weight: 600;
      letter-spacing: 0.025em;
      background-color: #ffffff;
      margin: 0;
      padding: 0;
      color: #0b2545;
      -webkit-font-smoothing: antialiased;
    }
    
    /* Boost legibility for compact text classes in Smooch Sans */
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
    
    /* ================= COMPREHENSIVE DARK MODE (ALL DASHBOARDS) ================= */
    .dark {
      color-scheme: dark;
    }
    .dark body {
      background-color: #071322 !important;
      color: #e2e8f0 !important;
    }
    .dark main {
      background-color: #071322 !important;
    }
    .dark .bg-white {
      background-color: #0c1f36 !important;
      color: #f1f5f9 !important;
    }
    .dark .bg-gray-50,
    .dark .bg-gray-50\/40,
    .dark .bg-gray-50\/50,
    .dark .bg-gray-50\/60,
    .dark .bg-gray-50\/70,
    .dark .bg-gray-50\/80,
    .dark .bg-gray-50\/90,
    .dark .bg-gray-100 {
      background-color: #09182b !important;
      color: #e2e8f0 !important;
    }
    .dark .border-gray-100,
    .dark .border-gray-200,
    .dark .border-gray-200\/60,
    .dark .border-gray-200\/70,
    .dark .border-gray-200\/80,
    .dark .border-gray-300 {
      border-color: #1a324d !important;
    }
    .dark .divide-gray-100 > * + *,
    .dark .divide-gray-200 > * + * {
      border-color: #1a324d !important;
    }
    .dark .text-gray-900,
    .dark .text-gray-800 {
      color: #f8fafc !important;
    }
    .dark .text-gray-700,
    .dark .text-gray-600 {
      color: #cbd5e1 !important;
    }
    .dark .text-gray-500,
    .dark .text-gray-400 {
      color: #94a3b8 !important;
    }
    .dark tr.hover\:bg-blue-50\/20:hover,
    .dark tr:hover {
      background-color: #102641 !important;
    }
    .dark select,
    .dark input[type="text"],
    .dark input[type="number"],
    .dark input[type="password"] {
      background-color: #09182b !important;
      color: #f8fafc !important;
      border-color: #1a324d !important;
    }
    .dark select option {
      background-color: #0c1f36 !important;
      color: #f8fafc !important;
    }
    .dark .shadow-xs,
    .dark .shadow-sm,
    .dark .shadow-2xs {
      box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.4) !important;
    }

    /* Custom subtle scrollbar */
    ::-webkit-scrollbar {
      width: 6px;
      height: 6px;
    }
    ::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: #94a3b8;
    }

    /* A compact loader with a simple zoom-out exit. */
    #flowPreloader {
      opacity: 1;
      visibility: visible;
      transition: opacity .45s ease, visibility 0s linear .45s;
      background: #0b2545;
    }
    #flowPreloader.is-complete {
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
    }
    .flow-preloader-mark { transform:scale(1); transition:transform .45s ease-out; }
    #flowPreloader.is-complete .flow-preloader-mark { transform:scale(.6); }
    #flowPreloader.is-complete .flow-preloader-orb { animation-play-state:paused; }
    .flow-preloader-orb {
      width: 50px;
      height: 50px;
      border-radius: 9999px;
      overflow: hidden;
      background: #ffffff;
      border: 1px solid rgba(255,255,255,.2);
      box-shadow: 0 4px 12px rgba(0, 8, 27, .2);
      animation: flow-pulse 1.1s ease-in-out infinite;
    }
    .flow-preloader-orb img { width: 40px; height: 40px; border-radius:50%; }
    .flow-preloader-orbit { position:absolute; inset:-5px; border:1.5px solid rgba(255,255,255,.16); border-top-color:#7dd3fc; border-right-color:#fff; border-radius:50%; animation:flow-orbit 1.4s linear infinite; }
    @keyframes flow-orbit { to { transform:rotate(360deg); } }
    @keyframes flow-pulse {
      0%, 100% { transform: scale(.88); }
      50% { transform: scale(1); }
    }
    @media (prefers-reduced-motion: reduce) {
      .flow-preloader-orb, #flowPreloader.is-complete .flow-preloader-orb { animation: none; }
      #flowPreloader { transition: opacity .15s ease, visibility 0s linear .15s; }
      .flow-preloader-orbit { animation:none; }
      .flow-preloader-mark, #flowPreloader.is-complete .flow-preloader-mark { transition:none; transform:none; }
    }
  </style>
</head>
<body class="bg-white dark:bg-[#071322] text-[#0b2545] dark:text-gray-100 <?= $appBodyLayout ?> flex flex-col transition-colors duration-200">

  <!-- Compact pulsing brand preloader -->
  <div id="flowPreloader" class="fixed inset-0 z-[200] overflow-hidden text-white" role="status" aria-live="polite" aria-label="Loading Flowwithlit">
    <div class="relative z-10 grid min-h-full place-items-center px-6">
      <div class="flex flex-col items-center text-center">
        <div class="flow-preloader-mark relative grid h-[50px] w-[50px] place-items-center">
          <div class="flow-preloader-orbit" aria-hidden="true"></div>
          <div class="flow-preloader-orb relative z-10 grid place-items-center">
            <img src="../logo/flowwithlit-icon-192.png" alt="" class="rounded-full" />
          </div>
        </div>
        <span class="sr-only">Loading Flowwithlit</span>
      </div>
    </div>
  </div>

  <!-- Full-screen command search -->
  <div id="commandSearchModal" class="hidden fixed inset-0 z-[100] bg-white dark:bg-[#071322] overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="commandSearchTitle">
    <div class="min-h-full">
      <div class="max-w-4xl mx-auto px-5 sm:px-8 pt-6 sm:pt-10">
        <div class="flex items-center justify-between gap-5 pb-5">
          <a href="index.php" class="flex items-center gap-2.5 text-gray-900 dark:text-white">
            <img src="../logo/flowwithlit-icon-192.png" alt="" class="w-9 h-9 rounded-lg" />
            <span class="text-xl font-bold tracking-tight">Flowwithlit</span>
          </a>
          <button type="button" id="closeCommandSearch" aria-label="Close search" class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-bold text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
            <span>Close</span><kbd class="hidden sm:inline-flex rounded border border-gray-300 dark:border-gray-600 px-1.5 py-0.5 text-[10px] font-mono">Esc</kbd>
            <i data-lucide="x" class="w-5 h-5"></i>
          </button>
        </div>

        <div class="border-y border-gray-200 dark:border-gray-700">
          <div class="flex items-center gap-4 py-5">
            <i data-lucide="search" class="w-6 h-6 flex-shrink-0 text-sky-500"></i>
            <input id="commandSearchInput" type="search" autocomplete="off" placeholder="Search pages, wallets, transactions, or actions…" class="w-full border-0 bg-transparent p-0 text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-none focus:ring-0" />
            <kbd class="hidden sm:inline-flex flex-shrink-0 rounded border border-gray-300 dark:border-gray-600 px-2 py-1 text-xs font-mono text-gray-400">⌘ K</kbd>
          </div>
        </div>

        <div class="py-7 sm:py-10">
          <div class="flex items-center justify-between mb-4">
            <h2 id="commandSearchTitle" class="text-xs font-bold uppercase tracking-[0.16em] text-gray-400">Quick access</h2>
            <span id="commandSearchCount" class="text-xs text-gray-400">11 destinations</span>
          </div>
          <div id="commandSearchResults" class="grid sm:grid-cols-2 gap-3">
            <a href="index.php" data-command-result data-search-terms="overview dashboard home analytics balance activity" class="command-search-result group flex items-start gap-3 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 hover:border-sky-300 dark:hover:border-sky-700 hover:bg-sky-50/60 dark:hover:bg-sky-950/30 transition">
              <span class="w-10 h-10 rounded-xl bg-sky-100 dark:bg-sky-950/60 text-sky-600 dark:text-sky-300 grid place-items-center"><i data-lucide="layout-dashboard" class="w-5 h-5"></i></span><span><strong class="block text-base text-gray-900 dark:text-white">Overview</strong><small class="block mt-0.5 text-xs text-gray-500 dark:text-gray-400">Your account activity and balances</small></span>
            </a>
            <a href="transactions.php" data-command-result data-search-terms="transaction transactions history transfer payment receipt details" class="command-search-result group flex items-start gap-3 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 hover:border-sky-300 dark:hover:border-sky-700 hover:bg-sky-50/60 dark:hover:bg-sky-950/30 transition">
              <span class="w-10 h-10 rounded-xl bg-violet-100 dark:bg-violet-950/60 text-violet-600 dark:text-violet-300 grid place-items-center"><i data-lucide="arrow-left-right" class="w-5 h-5"></i></span><span><strong class="block text-base text-gray-900 dark:text-white">Transactions</strong><small class="block mt-0.5 text-xs text-gray-500 dark:text-gray-400">View payments, transfers and receipts</small></span>
            </a>
            <a href="wallets.php" data-command-result data-search-terms="wallet wallets fiat crypto currencies balance account" class="command-search-result group flex items-start gap-3 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 hover:border-sky-300 dark:hover:border-sky-700 hover:bg-sky-50/60 dark:hover:bg-sky-950/30 transition">
              <span class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-300 grid place-items-center"><i data-lucide="wallet-cards" class="w-5 h-5"></i></span><span><strong class="block text-base text-gray-900 dark:text-white">Wallets</strong><small class="block mt-0.5 text-xs text-gray-500 dark:text-gray-400">Manage fiat and crypto balances</small></span>
            </a>
            <a href="add-money.php" data-command-result data-search-terms="add money fund funding deposit top up card bank transfer" class="command-search-result group flex items-start gap-3 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 hover:border-sky-300 dark:hover:border-sky-700 hover:bg-sky-50/60 dark:hover:bg-sky-950/30 transition">
              <span class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-300 grid place-items-center"><i data-lucide="circle-plus" class="w-5 h-5"></i></span><span><strong class="block text-base text-gray-900 dark:text-white">Add money</strong><small class="block mt-0.5 text-xs text-gray-500 dark:text-gray-400">Fund a wallet by bank, card or crypto</small></span>
            </a>
            <a href="send-money.php" data-command-result data-search-terms="send money transfer withdraw payout recipient bank" class="command-search-result group flex items-start gap-3 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 hover:border-sky-300 dark:hover:border-sky-700 hover:bg-sky-50/60 dark:hover:bg-sky-950/30 transition">
              <span class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-300 grid place-items-center"><i data-lucide="send" class="w-5 h-5"></i></span><span><strong class="block text-base text-gray-900 dark:text-white">Send money</strong><small class="block mt-0.5 text-xs text-gray-500 dark:text-gray-400">Make a secure transfer to a recipient</small></span>
            </a>
            <a href="request-money.php" data-command-result data-search-terms="request money payment link invoice collect receive customer" class="command-search-result group flex items-start gap-3 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 hover:border-sky-300 dark:hover:border-sky-700 hover:bg-sky-50/60 dark:hover:bg-sky-950/30 transition">
              <span class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-300 grid place-items-center"><i data-lucide="link" class="w-5 h-5"></i></span><span><strong class="block text-base text-gray-900 dark:text-white">Request money</strong><small class="block mt-0.5 text-xs text-gray-500 dark:text-gray-400">Create a payment request link</small></span>
            </a>
            <a href="cards.php" data-command-result data-search-terms="card cards virtual physical debit spend" class="command-search-result group flex items-start gap-3 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 hover:border-sky-300 dark:hover:border-sky-700 hover:bg-sky-50/60 dark:hover:bg-sky-950/30 transition">
              <span class="w-10 h-10 rounded-xl bg-pink-100 dark:bg-pink-950/60 text-pink-600 dark:text-pink-300 grid place-items-center"><i data-lucide="credit-card" class="w-5 h-5"></i></span><span><strong class="block text-base text-gray-900 dark:text-white">Cards</strong><small class="block mt-0.5 text-xs text-gray-500 dark:text-gray-400">Control your Flowwithlit cards</small></span>
            </a>
            <a href="payments.php" data-command-result data-search-terms="payments payment checkout invoices merchant collect" class="command-search-result group flex items-start gap-3 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 hover:border-sky-300 dark:hover:border-sky-700 hover:bg-sky-50/60 dark:hover:bg-sky-950/30 transition">
              <span class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-300 grid place-items-center"><i data-lucide="receipt-text" class="w-5 h-5"></i></span><span><strong class="block text-base text-gray-900 dark:text-white">Payments</strong><small class="block mt-0.5 text-xs text-gray-500 dark:text-gray-400">Manage payment collections</small></span>
            </a>
            <a href="developer.php" data-command-result data-search-terms="developer api api key integration webhook code live key" class="command-search-result group flex items-start gap-3 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 hover:border-sky-300 dark:hover:border-sky-700 hover:bg-sky-50/60 dark:hover:bg-sky-950/30 transition">
              <span class="w-10 h-10 rounded-xl bg-cyan-100 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-300 grid place-items-center"><i data-lucide="code-2" class="w-5 h-5"></i></span><span><strong class="block text-base text-gray-900 dark:text-white">Developer API</strong><small class="block mt-0.5 text-xs text-gray-500 dark:text-gray-400">Keys, webhooks and integrations</small></span>
            </a>
            <a href="settings.php" data-command-result data-search-terms="settings security pin withdrawal password profile preferences two factor" class="command-search-result group flex items-start gap-3 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 hover:border-sky-300 dark:hover:border-sky-700 hover:bg-sky-50/60 dark:hover:bg-sky-950/30 transition">
              <span class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 grid place-items-center"><i data-lucide="settings" class="w-5 h-5"></i></span><span><strong class="block text-base text-gray-900 dark:text-white">Settings</strong><small class="block mt-0.5 text-xs text-gray-500 dark:text-gray-400">Security, PIN and account preferences</small></span>
            </a>
            <a href="onboarding.php?mode=business" data-command-result data-search-terms="business verification verify onboarding company corporate personal documents compliance kyc" class="command-search-result group flex items-start gap-3 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 hover:border-sky-300 dark:hover:border-sky-700 hover:bg-sky-50/60 dark:hover:bg-sky-950/30 transition">
              <span class="w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-950/60 text-teal-600 dark:text-teal-300 grid place-items-center"><i data-lucide="badge-check" class="w-5 h-5"></i></span><span><strong class="block text-base text-gray-900 dark:text-white">Business verification</strong><small class="block mt-0.5 text-xs text-gray-500 dark:text-gray-400">Submit your business for review</small></span>
            </a>
          </div>
          <p id="commandSearchEmpty" class="hidden py-12 text-center text-gray-500 dark:text-gray-400">No destination found. Try “transactions”, “wallets”, or “send money”.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Fixed Top Header Navigation Bar (Never scrolls) -->
  <header id="appHeader" class="w-full h-20 flex-shrink-0 bg-white dark:bg-[#09182b] border-b border-gray-100 dark:border-gray-800/80 px-6 lg:px-8 flex items-center justify-between z-40 transition-colors duration-200">
    
    <!-- Brand Logo -->
    <a href="index.php" class="flex items-center gap-3 cursor-pointer">
      <div class="w-11 h-11 rounded-xl bg-[#0b2545] flex items-center justify-center shadow-sm relative overflow-hidden group">
        <img src="../logo/flowwithlit-icon-192.png" alt="" class="w-full h-full object-cover" />
        <svg class="hidden w-6 h-6 text-sky-400" viewBox="0 0 24 24" fill="currentColor">
          <path d="M4 6.5C4 5.11929 5.11929 4 6.5 4H18C18.5523 4 19 4.44772 19 5C19 5.55228 18.5523 6 18 6H8C7.44772 6 7 6.44772 7 7C7 7.55228 7.44772 8 8 8H16C16.5523 8 17 8.44772 17 9C17 9.55228 16.5523 10 16 10H8C7.44772 10 7 10.4477 7 11C7 11.5523 7.44772 12 8 12H13C13.5523 12 14 12.4477 14 13C14 13.5523 13.5523 14 13 14H6.5C5.11929 14 4 12.8807 4 11.5V6.5Z"/>
          <path d="M5 16C5 15.4477 5.44772 15 6 15H11C11.5523 15 12 15.4477 12 16C12 16.5523 11.5523 17 11 17H7C6.44772 17 6 17.4477 6 18C6 18.5523 6.44772 19 7 19H10C10.5523 19 11 19.4477 11 20C11 20.5523 10.5523 21 10 21H6C5.44772 21 5 20.5523 5 20V16Z" opacity="0.8"/>
        </svg>
      </div>
      <span class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Flowwithlit</span>
    </a>

    <!-- Center Dashboard Context Bar -->
    <nav aria-label="Reporting period" class="hidden md:flex items-center bg-gray-50/90 dark:bg-gray-800/80 border border-gray-200/70 dark:border-gray-700/60 rounded-full p-1.5 shadow-sm text-sm font-medium">
      <button type="button" data-reporting-period="today" class="reporting-period-btn bg-[#0b2545] text-white px-3.5 py-2 rounded-full shadow-sm transition">Today</button>
      <button type="button" data-reporting-period="7-days" class="reporting-period-btn text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-full transition">7 Days</button>
      <button type="button" data-reporting-period="30-days" class="reporting-period-btn text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-full transition">30 Days</button>
      <button type="button" data-reporting-period="this-month" class="reporting-period-btn text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-full transition">This Month</button>
      <button type="button" data-reporting-period="all-time" class="reporting-period-btn text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-full transition">All Time</button>
      <span class="w-px h-5 bg-gray-200 dark:bg-gray-700 mx-1"></span>
      <span title="All Flowwithlit systems operational" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/50 rounded-full">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Online
      </span>
    </nav>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const periodButtons = document.querySelectorAll('.reporting-period-btn');
        const savedPeriod = localStorage.getItem('flowwithlitReportingPeriod') || 'today';

        const setReportingPeriod = (period) => {
          localStorage.setItem('flowwithlitReportingPeriod', period);
          periodButtons.forEach((button) => {
            const active = button.dataset.reportingPeriod === period;
            button.classList.toggle('bg-[#0b2545]', active);
            button.classList.toggle('text-white', active);
            button.classList.toggle('shadow-sm', active);
            button.classList.toggle('text-gray-600', !active);
            button.classList.toggle('dark:text-gray-300', !active);
          });
          window.dispatchEvent(new CustomEvent('flowwithlit:reporting-period-change', { detail: { period } }));
        };

        periodButtons.forEach((button) => button.addEventListener('click', () => setReportingPeriod(button.dataset.reportingPeriod)));
        setReportingPeriod(savedPeriod);
      });
    </script>

    <!-- Right Profile & Controls -->
    <div class="flex items-center gap-2.5 relative">
      
      <!-- Light / Dark Mode Toggle Button -->
      <button type="button" id="themeToggleBtn" onclick="toggleTheme()" aria-label="Toggle Theme" class="w-11 h-11 rounded-full bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200/80 dark:border-gray-700 flex items-center justify-center text-gray-700 dark:text-gray-200 shadow-sm transition hover:scale-105 active:scale-95 cursor-pointer" title="Switch Light/Dark Mode">
        <i data-lucide="moon" class="w-5 h-5 hidden dark:block text-amber-300"></i>
        <i data-lucide="sun" class="w-5 h-5 block dark:hidden text-amber-500"></i>
      </button>

      <!-- Command Search -->
      <button type="button" id="openCommandSearch" aria-label="Search Flowwithlit" class="w-11 h-11 rounded-full bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200/80 dark:border-gray-700 flex items-center justify-center text-gray-700 dark:text-gray-200 shadow-sm transition hover:scale-105 active:scale-95 cursor-pointer" title="Search (Ctrl/⌘ K)">
        <i data-lucide="search" class="w-5 h-5"></i>
      </button>

      <!-- Notification Bell & Recent Notifications -->
      <div class="relative" id="notificationDropdownWrapper">
      <button id="notificationDropdownBtn" type="button" aria-label="Notifications" aria-expanded="false" aria-haspopup="true" class="relative w-11 h-11 rounded-full bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200/80 dark:border-gray-700 flex items-center justify-center text-gray-700 dark:text-gray-200 shadow-sm transition hover:scale-105 active:scale-95">
        <i data-lucide="bell" class="w-5 h-5"></i>
        <span id="notificationBadge" class="hidden absolute right-0.5 top-0.5 min-w-[17px] h-[17px] rounded-full border-2 border-white dark:border-[#09182b] bg-rose-500 px-1 text-[10px] font-bold leading-[13px] text-white">0</span>
      </button>
      <div id="notificationDropdownMenu" class="hidden absolute right-0 mt-3 w-[min(24rem,calc(100vw-2rem))] overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-[#0c1f36] shadow-2xl z-50">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 px-4 py-3.5"><div><h2 class="text-base font-bold text-gray-900 dark:text-white">Notifications</h2><p id="notificationUnreadText" class="text-xs text-gray-400">Loading updates…</p></div><button id="markNotificationsRead" type="button" class="rounded-lg px-2 py-1 text-xs font-bold text-sky-600 hover:bg-sky-50 dark:hover:bg-sky-950/30">Mark all read</button></div>
        <div id="notificationFeed" class="max-h-[22rem] overflow-y-auto p-2">
          <div class="py-8 text-center text-xs text-gray-400">Loading notifications…</div>
          <a href="developer.php" class="notification-item group mt-1 flex gap-3 rounded-xl bg-sky-50/70 dark:bg-sky-950/25 p-3 transition hover:bg-sky-50 dark:hover:bg-sky-950/45"><span class="mt-0.5 grid h-9 w-9 shrink-0 place-items-center rounded-full bg-violet-100 dark:bg-violet-950/60 text-violet-600 dark:text-violet-300"><i data-lucide="webhook" class="w-4 h-4"></i></span><span class="min-w-0 flex-1"><span class="flex items-start justify-between gap-3"><strong class="text-sm text-gray-900 dark:text-white">Webhook needs attention</strong><time class="whitespace-nowrap text-[10px] text-gray-400">18m</time></span><small class="mt-0.5 block text-xs text-gray-500 dark:text-gray-300">Your payment webhook returned an error twice.</small></span><span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-sky-500"></span></a>
          <a href="settings.php" class="notification-item group mt-1 flex gap-3 rounded-xl bg-sky-50/70 dark:bg-sky-950/25 p-3 transition hover:bg-sky-50 dark:hover:bg-sky-950/45"><span class="mt-0.5 grid h-9 w-9 shrink-0 place-items-center rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-300"><i data-lucide="shield-check" class="w-4 h-4"></i></span><span class="min-w-0 flex-1"><span class="flex items-start justify-between gap-3"><strong class="text-sm text-gray-900 dark:text-white">Security reminder</strong><time class="whitespace-nowrap text-[10px] text-gray-400">1h</time></span><small class="mt-0.5 block text-xs text-gray-500 dark:text-gray-300">Set a Withdrawal PIN to protect outgoing transfers.</small></span><span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-sky-500"></span></a>
          <a href="wallets.php" class="notification-item group mt-1 flex gap-3 rounded-xl p-3 transition hover:bg-gray-50 dark:hover:bg-gray-800/70"><span class="mt-0.5 grid h-9 w-9 shrink-0 place-items-center rounded-full bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-300"><i data-lucide="arrow-left-right" class="w-4 h-4"></i></span><span class="min-w-0 flex-1"><span class="flex items-start justify-between gap-3"><strong class="text-sm text-gray-900 dark:text-white">Swap complete</strong><time class="whitespace-nowrap text-[10px] text-gray-400">Yesterday</time></span><small class="mt-0.5 block text-xs text-gray-500 dark:text-gray-300">0.5 BTC was converted to USD at your confirmed rate.</small></span></a>
        </div>
        <div class="border-t border-gray-100 dark:border-gray-800 p-2"><a href="notifications.php" class="flex items-center justify-center gap-2 rounded-xl py-2.5 text-sm font-bold text-[#0b2545] dark:text-sky-300 hover:bg-gray-50 dark:hover:bg-gray-800">View all notifications <i data-lucide="arrow-right" class="w-4 h-4"></i></a></div>
      </div>
      </div>

      <!-- User Profile Dropdown Container -->
      <div class="relative" id="profileDropdownWrapper">
        <button id="profileDropdownBtn" class="flex items-center gap-1.5 focus:outline-none p-0.5 rounded-full hover:ring-2 hover:ring-gray-200 dark:hover:ring-gray-700 transition" aria-expanded="false" aria-haspopup="true">
          <!-- Avatar Image with status ring -->
          <div class="w-11 h-11 rounded-full overflow-hidden border-2 border-white dark:border-gray-700 shadow-sm ring-1 ring-gray-200/80 dark:ring-gray-700 hover:ring-[#10d07a] transition relative">
            <span id="currentUserAvatar" data-user-avatar></span>
          </div>
          <!-- Dropdown Chevron -->
          <i data-lucide="chevron-down" id="profileChevron" class="w-3.5 h-3.5 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition transform duration-200"></i>
        </button>

        <!-- Dropdown Menu Box -->
        <div id="profileDropdownMenu" class="hidden absolute right-0 mt-3 w-72 bg-white dark:bg-[#0c1f36] rounded-2xl shadow-xl border border-gray-100 dark:border-gray-800 py-2 z-50 transform origin-top-right transition-all duration-150 animate-in fade-in zoom-in-95">
          
          <!-- User info header -->
          <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full overflow-hidden border border-gray-200 dark:border-gray-700 flex-shrink-0">
              <span id="currentUserMenuAvatar" data-user-avatar></span>
            </div>
            <div class="flex flex-1 flex-col min-w-0">
                <span id="currentUserName" class="text-sm font-bold text-gray-900 dark:text-white truncate">Loading account…</span>
              <span id="currentUserEmail" class="text-xs text-gray-400 truncate">Loading…</span>
              <span id="currentUserTier" class="hidden mt-1 w-fit items-center text-slate-600 dark:text-slate-300" role="img"></span>
            </div>
          </div>

          <!-- Section 1: Account Management -->
          <div class="py-1.5 px-1.5">
            <a href="settings.php" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition">
              <div class="w-7 h-7 rounded-lg bg-gray-100/80 flex items-center justify-center text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <div class="flex flex-col">
                <span>My Profile</span>
                
              </div>
            </a>

            <a href="settings.php" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition">
              <div class="w-7 h-7 rounded-lg bg-gray-100/80 flex items-center justify-center text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                  <circle cx="12" cy="12" r="3" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </div>
              <div class="flex flex-col">
                <span>Account Settings</span>
                
              </div>
            </a>

            <a href="billing.php" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition">
              <div class="w-7 h-7 rounded-lg bg-gray-100/80 flex items-center justify-center text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                  <rect x="3" y="5" width="18" height="14" rx="3" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M3 10h18M7 15h3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <div class="flex flex-col">
                <span>Billing & Plans</span>
                
              </div>
            </a>
          </div>

          <div class="h-px bg-gray-100 mx-3 my-1"></div>

          <!-- Section 3: Logout -->
          <div class="py-1 px-1.5">
            <button id="logoutBtn" class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-red-600 hover:bg-red-50 hover:text-red-700 transition">
              <div class="w-7 h-7 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
              </div>
              <div class="flex flex-col text-left">
                <span>Log Out</span>
                
              </div>
            </button>
          </div>

        </div>
      </div>
    </div>
  </header>

  <!-- Workspace Container: Non-scrolling Sidebar + Scrollable Content Area -->
  <div class="flex flex-1 w-full <?= $appWorkspaceLayout ?>">
