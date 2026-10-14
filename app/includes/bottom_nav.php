<?php
/**
 * Application Mobile Bottom Navigation & Services Drawer Component
 * Reusable for all Flowwithlit mobile dashboard views with Lucide Icons & Dark Mode
 * 
 * Available variables to set before including:
 * - $activePage (string): 'dashboard', 'wallets', 'payments', 'cards', etc.
 */
$activePage = $activePage ?? 'dashboard';
?>

<!-- Mobile Bottom Navigation Bar (md:hidden) -->
<nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 dark:bg-[#09182b]/95 backdrop-blur-md border-t border-gray-200/80 dark:border-gray-800 px-3 py-2 flex items-center justify-around shadow-lg select-none transition-colors duration-200">
  
  <!-- 1. Dashboard -->
  <a href="index.php" class="mobile-nav-btn flex flex-col items-center gap-1 active:scale-95 transition" data-title="Dashboard">
    <div class="nav-circle w-10 h-10 rounded-full <?= ($activePage === 'dashboard') ? 'bg-[#0b2545] dark:bg-[#134074] text-white shadow-xs' : 'bg-gray-50 dark:bg-[#0c1f36] text-gray-600 dark:text-gray-300' ?> flex items-center justify-center transition">
      <i data-lucide="layout-grid" class="w-4 h-4"></i>
    </div>
    <span class="nav-label text-[10px] <?= ($activePage === 'dashboard') ? 'font-bold text-[#0b2545] dark:text-blue-400' : 'font-semibold text-gray-500 dark:text-gray-400' ?>">Home</span>
  </a>

  <!-- 2. Wallets -->
  <a href="wallets.php" class="mobile-nav-btn flex flex-col items-center gap-1 active:scale-95 transition" data-title="Wallets">
    <div class="nav-circle w-10 h-10 rounded-full <?= ($activePage === 'wallets') ? 'bg-[#0b2545] dark:bg-[#134074] text-white shadow-xs' : 'bg-gray-50 dark:bg-[#0c1f36] text-gray-600 dark:text-gray-300' ?> flex items-center justify-center transition">
      <i data-lucide="wallet" class="w-4 h-4"></i>
    </div>
    <span class="nav-label text-[10px] <?= ($activePage === 'wallets') ? 'font-bold text-[#0b2545] dark:text-blue-400' : 'font-semibold text-gray-500 dark:text-gray-400' ?>">Wallets</span>
  </a>

  <!-- 3. Payments -->
  <a href="payments.php" class="mobile-nav-btn flex flex-col items-center gap-1 active:scale-95 transition" data-title="Payments">
    <div class="nav-circle w-10 h-10 rounded-full <?= ($activePage === 'payments') ? 'bg-[#0b2545] dark:bg-[#134074] text-white shadow-xs' : 'bg-gray-50 dark:bg-[#0c1f36] text-gray-600 dark:text-gray-300' ?> flex items-center justify-center transition">
      <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
    </div>
    <span class="nav-label text-[10px] <?= ($activePage === 'payments') ? 'font-bold text-[#0b2545] dark:text-blue-400' : 'font-semibold text-gray-500 dark:text-gray-400' ?>">Payments</span>
  </a>

  <!-- 4. Cards -->
  <a href="cards.php" class="mobile-nav-btn flex flex-col items-center gap-1 active:scale-95 transition" data-title="Cards">
    <div class="nav-circle w-10 h-10 rounded-full <?= ($activePage === 'cards') ? 'bg-[#0b2545] dark:bg-[#134074] text-white shadow-xs' : 'bg-gray-50 dark:bg-[#0c1f36] text-gray-600 dark:text-gray-300' ?> flex items-center justify-center transition">
      <i data-lucide="credit-card" class="w-4 h-4"></i>
    </div>
    <span class="nav-label text-[10px] <?= ($activePage === 'cards') ? 'font-bold text-[#0b2545] dark:text-blue-400' : 'font-semibold text-gray-500 dark:text-gray-400' ?>">Cards</span>
  </a>

  <!-- 5. More (Opens Full Menu Drawer) -->
  <button id="mobileMoreBtn" class="mobile-nav-btn flex flex-col items-center gap-1 active:scale-95 transition" data-title="More" type="button">
    <div class="nav-circle w-10 h-10 rounded-full bg-gray-50 dark:bg-[#0c1f36] text-gray-600 dark:text-gray-300 flex items-center justify-center hover:bg-[#eef4ff] dark:hover:bg-[#13304f] transition">
      <i data-lucide="more-horizontal" class="w-4 h-4"></i>
    </div>
    <span class="nav-label text-[10px] font-semibold text-gray-500 dark:text-gray-400">More</span>
  </button>

</nav>

<!-- Mobile "More Services" Drawer (Slide-up Bottom Sheet) -->
<div id="mobileDrawerBackdrop" class="hidden md:hidden fixed inset-0 bg-black/50 backdrop-blur-xs z-50 transition-opacity duration-200 opacity-0 flex flex-col justify-end">
  <div id="mobileDrawerPanel" class="w-full bg-white dark:bg-[#09182b] text-[#0b2545] dark:text-gray-100 rounded-t-[32px] p-6 shadow-2xl border-t border-gray-100 dark:border-gray-800 transform translate-y-full transition-transform duration-300 max-h-[85vh] overflow-y-auto">
    
    <!-- Drag Handle -->
    <div class="w-12 h-1.5 rounded-full bg-gray-300 dark:bg-gray-700 mx-auto mb-4"></div>
    
    <!-- Drawer Header -->
    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800">
      <div>
        <h3 class="text-base font-bold text-[#0b2545] dark:text-white">All Services</h3>
        <p class="text-xs text-gray-400">Access all Flowwithlit tools &amp; configurations</p>
      </div>
      <div class="flex items-center gap-2">
        <!-- Mobile Dark/Light Theme Switcher -->
        <button type="button" onclick="toggleTheme()" class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-600 dark:text-gray-200 transition" title="Toggle Theme">
          <i data-lucide="moon" class="w-4 h-4 hidden dark:block text-amber-300"></i>
          <i data-lucide="sun" class="w-4 h-4 block dark:hidden text-amber-500"></i>
        </button>
        <!-- Close Drawer -->
        <button id="closeMobileDrawerBtn" class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-500 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 active:scale-95 transition" type="button">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>
    </div>

    <!-- Complete Menu Grid inside Drawer -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 py-4">
      
      <!-- Transactions Ledger -->
      <a href="transactions.php" class="flex flex-col items-center text-center p-3 rounded-2xl bg-gray-50 dark:bg-[#0c1f36] hover:bg-[#eef4ff] dark:hover:bg-[#13304f] border border-gray-100 dark:border-gray-800/80 transition group active:scale-95">
        <div class="w-11 h-11 rounded-full bg-white dark:bg-[#13304f] text-gray-700 dark:text-gray-200 group-hover:bg-[#0b2545] group-hover:text-white flex items-center justify-center shadow-xs mb-2 transition">
          <i data-lucide="receipt-text" class="w-5 h-5"></i>
        </div>
        <span class="text-xs font-bold text-[#0b2545] dark:text-white">Transactions</span>
        <span class="text-[10px] text-gray-400">Ledger &amp; Receipts</span>
      </a>

      <!-- Payment Links & Invoices -->
      <a href="payment-links.php" class="flex flex-col items-center text-center p-3 rounded-2xl bg-gray-50 dark:bg-[#0c1f36] hover:bg-[#eef4ff] dark:hover:bg-[#13304f] border border-gray-100 dark:border-gray-800/80 transition group active:scale-95">
        <div class="w-11 h-11 rounded-full bg-white dark:bg-[#13304f] text-gray-700 dark:text-gray-200 group-hover:bg-[#0b2545] group-hover:text-white flex items-center justify-center shadow-xs mb-2 transition">
          <i data-lucide="link" class="w-5 h-5"></i>
        </div>
        <span class="text-xs font-bold text-[#0b2545] dark:text-white">Payment Links</span>
        <span class="text-[10px] text-gray-400">Invoices &amp; QR</span>
      </a>

      <!-- Customers & Payees -->
      <a href="customers.php" class="flex flex-col items-center text-center p-3 rounded-2xl bg-gray-50 dark:bg-[#0c1f36] hover:bg-[#eef4ff] dark:hover:bg-[#13304f] border border-gray-100 dark:border-gray-800/80 transition group active:scale-95">
        <div class="w-11 h-11 rounded-full bg-white dark:bg-[#13304f] text-gray-700 dark:text-gray-200 group-hover:bg-[#0b2545] group-hover:text-white flex items-center justify-center shadow-xs mb-2 transition">
          <i data-lucide="users" class="w-5 h-5"></i>
        </div>
        <span class="text-xs font-bold text-[#0b2545] dark:text-white">Customers</span>
        <span class="text-[10px] text-gray-400">Beneficiaries</span>
      </a>

      <!-- Developer API -->
      <a href="developer.php" class="flex flex-col items-center text-center p-3 rounded-2xl bg-gray-50 dark:bg-[#0c1f36] hover:bg-[#eef4ff] dark:hover:bg-[#13304f] border border-gray-100 dark:border-gray-800/80 transition group relative active:scale-95">
        <div class="w-11 h-11 rounded-full bg-white dark:bg-[#13304f] text-gray-700 dark:text-gray-200 group-hover:bg-[#0b2545] group-hover:text-white flex items-center justify-center shadow-xs mb-2 transition relative">
          <i data-lucide="code-2" class="w-5 h-5"></i>
          <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-emerald-500"></span>
        </div>
        <span class="text-xs font-bold text-[#0b2545] dark:text-white">Developer API</span>
        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">Live / Sandbox</span>
      </a>

      <!-- Global Accounts -->
      <a href="accounts.php" class="flex flex-col items-center text-center p-3 rounded-2xl bg-gray-50 dark:bg-[#0c1f36] hover:bg-[#eef4ff] dark:hover:bg-[#13304f] border border-gray-100 dark:border-gray-800/80 transition group active:scale-95">
        <div class="w-11 h-11 rounded-full bg-white dark:bg-[#13304f] text-gray-700 dark:text-gray-200 group-hover:bg-[#0b2545] group-hover:text-white flex items-center justify-center shadow-xs mb-2 transition">
          <i data-lucide="globe" class="w-5 h-5"></i>
        </div>
        <span class="text-xs font-bold text-[#0b2545] dark:text-white">Global Accounts</span>
        <span class="text-[10px] text-gray-400">IBAN &amp; Routing</span>
      </a>

      <!-- Settings -->
      <a href="settings.php" class="flex flex-col items-center text-center p-3 rounded-2xl bg-gray-50 dark:bg-[#0c1f36] hover:bg-[#eef4ff] dark:hover:bg-[#13304f] border border-gray-100 dark:border-gray-800/80 transition group active:scale-95">
        <div class="w-11 h-11 rounded-full bg-white dark:bg-[#13304f] text-gray-700 dark:text-gray-200 group-hover:bg-[#0b2545] group-hover:text-white flex items-center justify-center shadow-xs mb-2 transition">
          <i data-lucide="settings" class="w-5 h-5"></i>
        </div>
        <span class="text-xs font-bold text-[#0b2545] dark:text-white">Settings</span>
        <span class="text-[10px] text-gray-400">Profile &amp; Security</span>
      </a>

    </div>

    <!-- Quick Help & Legal -->
    <div class="pt-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between text-[11px] text-gray-400 font-medium">
      <div class="flex items-center gap-1">
        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
        <span>Systems Operational</span>
      </div>
      <a href="#" class="hover:text-[#0b2545] dark:hover:text-white transition">Documentation &rarr;</a>
    </div>

  </div>
</div>
