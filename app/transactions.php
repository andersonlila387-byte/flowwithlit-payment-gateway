<?php
/**
 * Flowwithlit Transactions Ledger (Paystack Style)
 * Full-width responsive ledger with 10 items per page, interactive pagination,
 * multi-channel payments, status icons, receipt modal, and transaction drawer.
 */
$pageTitle = 'Transactions — Flowwithlit';
$activePage = 'transactions';
$activeNavTab = 'Features';
$appBodyLayout = 'min-h-screen w-full overflow-x-hidden';
$appWorkspaceLayout = 'min-h-[calc(100vh-5rem)]';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<style>
  #transactionsTableBody { visibility: hidden; }
  @media (min-width: 768px) {
    body > header {
      position: sticky;
      top: 0;
      z-index: 40;
    }

    body > div.flex.flex-1.w-full > aside {
      position: sticky;
      top: 5rem;
      height: calc(100vh - 5rem);
      align-self: flex-start;
    }
  }
</style>

    <!-- Scrollable Main Content Area -->
    <main class="flex-1 min-w-0 px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-6 flex flex-col gap-6">
      
      <!-- Top Title & Quick Action Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 flex-shrink-0">
        <div>
          <div class="flex items-center gap-2">
            <span class="text-xs text-gray-400 font-medium tracking-wide">Payments Ledger</span>
            <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-2 py-0.5 rounded-full flex items-center gap-1.5">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
              Live Gateway Sync
            </span>
          </div>
          <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight mt-0.5">Transactions</h1>
        </div>

        <!-- Action buttons -->
        <div class="flex items-center gap-2.5 flex-wrap">
          <!-- Date Filter Selector -->
          <div class="relative">
            <select id="dateRangeFilter" class="appearance-none bg-white hover:bg-gray-50 border border-gray-200/80 text-gray-700 text-xs font-semibold rounded-full pl-3.5 pr-8 py-2.5 shadow-2xs transition cursor-pointer focus:outline-hidden focus:ring-1 focus:ring-[#0b2545]">
              <option value="today">Today</option>
              <option value="7d">Last 7 Days</option>
              <option value="30d" selected>Last 30 Days</option>
              <option value="90d">Last 3 Months</option>
              <option value="all">All Time</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
          </div>

          <!-- Export Transactions CSV -->
          <button class="px-4 py-2.5 rounded-full bg-white hover:bg-gray-50 border border-gray-200/80 text-gray-700 text-xs font-semibold flex items-center gap-2 shadow-2xs transition hover:scale-105 active:scale-95" onclick="showToast('Exporting full Transactions CSV...')">
            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            <span>Export CSV</span>
          </button>

          <!-- Primary: Create Payment Link -->
          <a href="payment-links.php" class="bg-[#0b2545] hover:bg-[#132847] text-white px-5 py-2.5 rounded-full text-xs font-semibold flex items-center gap-2 shadow-sm transition hover:scale-105 active:scale-95">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Create Payment Link</span>
          </a>
        </div>
      </div>

      <!-- Row 1: KPI Metrics (4 Cards) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 flex-shrink-0">
        
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex flex-col justify-between">
          <div class="flex items-center justify-between text-xs text-gray-500 font-medium">
            <span>Total Gross Volume</span>
            <div class="w-8 h-8 rounded-xl bg-sky-50 text-[#0b2545] flex items-center justify-center font-bold">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
          </div>
          <div class="mt-3">
            <div id="txGrossVolume" class="text-2xl font-black text-gray-900 tracking-tight">₦0.00</div>
            <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1">
              <span>All Currencies</span>
              <span class="text-emerald-600 font-semibold">&uarr; 14.2%</span>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex flex-col justify-between">
          <div class="flex items-center justify-between text-xs text-gray-500 font-medium">
            <span>Successful Transactions</span>
            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
          </div>
          <div class="mt-3">
            <div id="txSuccessfulCount" class="text-2xl font-black text-gray-900 tracking-tight">0</div>
            <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1">
              <span>Success Rate</span>
              <span id="txSuccessRate" class="text-emerald-600 font-semibold">0%</span>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex flex-col justify-between">
          <div class="flex items-center justify-between text-xs text-gray-500 font-medium">
            <span>Net Settled Amount</span>
            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h3m-7 5h18a2 2 0 002-2V6a2 2 0 00-2-2H3a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
          </div>
          <div class="mt-3">
            <div id="txNetSettled" class="text-2xl font-black text-gray-900 tracking-tight">₦0.00</div>
            <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1">
              <span>Gateway Fees</span>
              <span id="txFees" class="text-gray-600 font-semibold">₦0.00</span>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex flex-col justify-between">
          <div class="flex items-center justify-between text-xs text-gray-500 font-medium">
            <span>Failed / Abandoned</span>
            <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
            </div>
          </div>
          <div class="mt-3">
            <div id="txFailedCount" class="text-2xl font-black text-gray-900 tracking-tight">0</div>
            <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1">
              <span>Decline Ratio</span>
              <span id="txFailureRate" class="text-gray-500 font-semibold">0%</span>
            </div>
          </div>
        </div>

      </div>

      <!-- Row 2: 100% Full-Width Responsive Table Container -->
      <div class="w-full bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden flex flex-col">
        
        <!-- Table Toolbar -->
        <div class="p-4 sm:p-5 border-b border-gray-100 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
          
          <!-- Search input -->
          <div class="relative flex-1 max-w-md">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" id="transactionSearchInput" placeholder="Search by Reference (T_...), Customer Name, Email, or Card..." class="w-full pl-10 pr-4 py-2 text-xs border border-gray-200 rounded-full focus:outline-hidden focus:border-[#0b2545] focus:ring-1 focus:ring-[#0b2545] transition bg-gray-50/50">
          </div>

          <!-- Filter Selectors -->
          <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
            
            <select id="channelFilter" onchange="filterTransactions()" class="px-3 py-2 rounded-full border border-gray-200 bg-white text-xs font-semibold text-gray-700 focus:outline-hidden cursor-pointer">
              <option value="all">All Channels</option>
              <option value="card">Card (Visa/Mastercard)</option>
              <option value="apple_pay">Apple Pay</option>
              <option value="bank_transfer">Bank Transfer</option>
              <option value="crypto">Crypto (USDT)</option>
            </select>

            <select id="statusFilter" onchange="filterTransactions()" class="px-3 py-2 rounded-full border border-gray-200 bg-white text-xs font-semibold text-gray-700 focus:outline-hidden cursor-pointer">
              <option value="all">All Statuses</option>
              <option value="successful">Successful</option>
              <option value="pending">Pending</option>
              <option value="failed">Failed</option>
              <option value="refunded">Refunded</option>
            </select>

            <select id="currencyFilter" onchange="filterTransactions()" class="px-3 py-2 rounded-full border border-gray-200 bg-white text-xs font-semibold text-gray-700 focus:outline-hidden cursor-pointer">
              <option value="all">All Currencies</option>
              <option value="NGN">NGN (₦)</option>
            </select>

          </div>
        </div>

        <!-- 100% Full-Width Responsive Table with Balanced Columns -->
        <div class="w-full overflow-x-auto">
          <table class="w-full text-left border-collapse min-w-[950px]">
            <thead>
              <tr class="border-b border-gray-100 text-[11px] font-bold uppercase tracking-wider text-gray-400 bg-gray-50/50">
                <th class="py-3 px-5">Customer / Payer</th>
                <th class="py-3 px-4">Amount &amp; Net</th>
                <th class="py-3 px-4">Reference &amp; Channel</th>
                <th class="py-3 px-4">Date &amp; Time</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-5 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-xs" id="transactionsTableBody">
              
              <!-- 1. Sophia Martinez -->
              <tr class="hover:bg-blue-50/20 transition group transaction-row" data-page="1" data-ref="T8492019481" data-name="Sophia Martinez" data-email="sophia.m@enterprise.io" data-channel="card" data-status="successful" data-currency="USD">
                <td class="py-5 px-6 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-[#0b2545] text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs">SM</div>
                    <div>
                      <div class="font-bold text-gray-900 group-hover:text-[#0b2545] transition">Sophia Martinez</div>
                      <div class="text-[11px] text-gray-400 font-mono">sophia.m@enterprise.io</div>
                    </div>
                  </div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="font-black text-gray-900 text-sm">$2,450.00</div>
                  <div class="text-[11px] text-gray-400">Net: <span class="font-bold text-emerald-700">$2,413.00</span> &bull; Fee: $37.00</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="flex items-center gap-1.5 font-mono font-bold text-gray-900">
                    <span>T8492019481</span>
                    <button onclick="copyRef('T8492019481')" title="Copy Ref" class="text-gray-400 hover:text-[#0b2545] transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                  <div class="text-[11px] text-gray-500 font-medium mt-0.5">Visa &bull;&bull;&bull;&bull; 4242</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">18 Sep 2026</div>
                  <div class="text-[10px] text-gray-400">01:45 PM</div>
                </td>
                <td class="py-5 px-5 text-center whitespace-nowrap">
                  <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center shadow-2xs" title="Successful">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1">
                    <button onclick="openReceiptModal('T8492019481', '$2,450.00 USD', 'Sophia Martinez', 'sophia.m@enterprise.io', 'Visa ending in 4242', 'Successful', '18 Sep 2026, 01:45 PM', '$37.00 USD', '$2,413.00 USD')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="Print Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                    </button>
                    <button onclick="viewTransactionDetails('T8492019481', '$2,450.00 USD', '$2,413.00 USD', '$37.00 USD', 'Sophia Martinez', 'sophia.m@enterprise.io', 'CUS_9482910', 'Card', 'Visa ending in 4242', 'AUTH_948102', 'Successful', '18 Sep 2026, 01:45 PM', 'USD Primary Vault')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Details">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- 2. Liam Chen -->
              <tr class="hover:bg-blue-50/20 transition group transaction-row" data-page="1" data-ref="T7391028472" data-name="Liam Chen" data-email="liam.chen@vancouverdesign.ca" data-channel="apple_pay" data-status="successful" data-currency="CAD">
                <td class="py-5 px-6 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs">LC</div>
                    <div>
                      <div class="font-bold text-gray-900 group-hover:text-[#0b2545] transition">Liam Chen</div>
                      <div class="text-[11px] text-gray-400 font-mono">liam.chen@vancouverdesign.ca</div>
                    </div>
                  </div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="font-black text-gray-900 text-sm">CA$4,200.00</div>
                  <div class="text-[11px] text-gray-400">Net: <span class="font-bold text-emerald-700">CA$4,137.00</span> &bull; Fee: CA$63.00</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="flex items-center gap-1.5 font-mono font-bold text-gray-900">
                    <span>T7391028472</span>
                    <button onclick="copyRef('T7391028472')" title="Copy Ref" class="text-gray-400 hover:text-[#0b2545] transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                  <div class="text-[11px] text-gray-500 font-medium mt-0.5">Apple Pay &bull; Mastercard</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">18 Sep 2026</div>
                  <div class="text-[10px] text-gray-400">11:20 AM</div>
                </td>
                <td class="py-5 px-5 text-center whitespace-nowrap">
                  <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center shadow-2xs" title="Successful">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1">
                    <button onclick="openReceiptModal('T7391028472', 'CA$4,200.00 CAD', 'Liam Chen', 'liam.chen@vancouverdesign.ca', 'Apple Pay', 'Successful', '18 Sep 2026, 11:20 AM', 'CA$63.00 CAD', 'CA$4,137.00 CAD')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="Print Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                    </button>
                    <button onclick="viewTransactionDetails('T7391028472', 'CA$4,200.00 CAD', 'CA$4,137.00 CAD', 'CA$63.00 CAD', 'Liam Chen', 'liam.chen@vancouverdesign.ca', 'CUS_8401928', 'Apple Pay', 'Mastercard Token', 'AUTH_883019', 'Successful', '18 Sep 2026, 11:20 AM', 'CAD Operating Vault')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Details">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- 3. Alexander Okafor -->
              <tr class="hover:bg-blue-50/20 transition group transaction-row" data-page="1" data-ref="T6201948201" data-name="Alexander Okafor" data-email="a.okafor@lagosfintech.ng" data-channel="bank_transfer" data-status="successful" data-currency="NGN">
                <td class="py-5 px-6 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-emerald-700 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs">AO</div>
                    <div>
                      <div class="font-bold text-gray-900 group-hover:text-[#0b2545] transition">Alexander Okafor</div>
                      <div class="text-[11px] text-gray-400 font-mono">a.okafor@lagosfintech.ng</div>
                    </div>
                  </div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="font-black text-gray-900 text-sm">₦1,500,000.00</div>
                  <div class="text-[11px] text-gray-400">Net: <span class="font-bold text-emerald-700">₦1,477,500.00</span> &bull; Fee: ₦22,500.00</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="flex items-center gap-1.5 font-mono font-bold text-gray-900">
                    <span>T6201948201</span>
                    <button onclick="copyRef('T6201948201')" title="Copy Ref" class="text-gray-400 hover:text-[#0b2545] transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                  <div class="text-[11px] text-gray-500 font-medium mt-0.5">Virtual Account Transfer</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">17 Sep 2026</div>
                  <div class="text-[10px] text-gray-400">04:12 PM</div>
                </td>
                <td class="py-5 px-5 text-center whitespace-nowrap">
                  <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center shadow-2xs" title="Successful">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1">
                    <button onclick="openReceiptModal('T6201948201', '₦1,500,000.00 NGN', 'Alexander Okafor', 'a.okafor@lagosfintech.ng', 'Direct Virtual Account', 'Successful', '17 Sep 2026, 04:12 PM', '₦22,500.00 NGN', '₦1,477,500.00 NGN')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="Print Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                    </button>
                    <button onclick="viewTransactionDetails('T6201948201', '₦1,500,000.00 NGN', '₦1,477,500.00 NGN', '₦22,500.00 NGN', 'Alexander Okafor', 'a.okafor@lagosfintech.ng', 'CUS_1948201', 'Bank Transfer', 'Titan Trust Bank VA', 'NIP_20260917', 'Successful', '17 Sep 2026, 04:12 PM', 'NGN Main Vault')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Details">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- 4. Emma Watson -->
              <tr class="hover:bg-blue-50/20 transition group transaction-row" data-page="1" data-ref="T5190284729" data-name="Emma Watson" data-email="emma.w@oxfordstudio.co.uk" data-channel="card" data-status="pending" data-currency="GBP">
                <td class="py-5 px-6 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-amber-600 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs">EW</div>
                    <div>
                      <div class="font-bold text-gray-900 group-hover:text-[#0b2545] transition">Emma Watson</div>
                      <div class="text-[11px] text-gray-400 font-mono">emma.w@oxfordstudio.co.uk</div>
                    </div>
                  </div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="font-black text-gray-900 text-sm">£850.00</div>
                  <div class="text-[11px] text-gray-400">Net: <span class="font-bold text-amber-700">£837.25</span> &bull; Fee: £12.75</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="flex items-center gap-1.5 font-mono font-bold text-gray-900">
                    <span>T5190284729</span>
                    <button onclick="copyRef('T5190284729')" title="Copy Ref" class="text-gray-400 hover:text-[#0b2545] transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                  <div class="text-[11px] text-gray-500 font-medium mt-0.5">Mastercard &bull;&bull;&bull;&bull; 8831</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">17 Sep 2026</div>
                  <div class="text-[10px] text-gray-400">02:30 PM</div>
                </td>
                <td class="py-5 px-5 text-center whitespace-nowrap">
                  <span class="w-7 h-7 rounded-full bg-amber-50 text-amber-600 border border-amber-200 inline-flex items-center justify-center shadow-2xs" title="Pending 3DS">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1">
                    <button class="p-1.5 rounded-lg text-gray-300 cursor-not-allowed" disabled title="No Receipt (Pending)">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                    </button>
                    <button onclick="viewTransactionDetails('T5190284729', '£850.00 GBP', '£837.25 GBP', '£12.75 GBP', 'Emma Watson', 'emma.w@oxfordstudio.co.uk', 'CUS_3910284', 'Card', 'Mastercard ending in 8831', 'PENDING_3DS', 'Pending', '17 Sep 2026, 02:30 PM', 'GBP Settlement Vault')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Details">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- 5. Marcus Brody -->
              <tr class="hover:bg-blue-50/20 transition group transaction-row" data-page="1" data-ref="T4019284712" data-name="Marcus Brody" data-email="m.brody@globaltrade.de" data-channel="card" data-status="failed" data-currency="EUR">
                <td class="py-5 px-6 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-rose-600 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs">MB</div>
                    <div>
                      <div class="font-bold text-gray-900 group-hover:text-[#0b2545] transition">Marcus Brody</div>
                      <div class="text-[11px] text-gray-400 font-mono">m.brody@globaltrade.de</div>
                    </div>
                  </div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="font-black text-gray-900 text-sm">€1,200.00</div>
                  <div class="text-[11px] text-rose-500 font-medium">Declined by Cardholder Bank</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="flex items-center gap-1.5 font-mono font-bold text-gray-900">
                    <span>T4019284712</span>
                    <button onclick="copyRef('T4019284712')" title="Copy Ref" class="text-gray-400 hover:text-[#0b2545] transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                  <div class="text-[11px] text-gray-500 font-medium mt-0.5">Visa &bull;&bull;&bull;&bull; 1092</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">16 Sep 2026</div>
                  <div class="text-[10px] text-gray-400">08:45 AM</div>
                </td>
                <td class="py-5 px-5 text-center whitespace-nowrap">
                  <span class="w-7 h-7 rounded-full bg-rose-50 text-rose-600 border border-rose-200 inline-flex items-center justify-center shadow-2xs" title="Failed">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1">
                    <button class="p-1.5 rounded-lg text-gray-300 cursor-not-allowed" disabled title="No Receipt (Failed)">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                    </button>
                    <button onclick="viewTransactionDetails('T4019284712', '€1,200.00 EUR', '€0.00 EUR', '€0.00 EUR', 'Marcus Brody', 'm.brody@globaltrade.de', 'CUS_2019482', 'Card', 'Visa ending in 1092', 'ERR_DECLINED', 'Failed', '16 Sep 2026, 08:45 AM', 'EUR Settlement Vault')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Details">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- 6. David Miller -->
              <tr class="hover:bg-blue-50/20 transition group transaction-row" data-page="1" data-ref="T3910284710" data-name="David Miller" data-email="david.miller@acmecorp.ca" data-channel="card" data-status="refunded" data-currency="USD">
                <td class="py-5 px-6 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-purple-700 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs">DM</div>
                    <div>
                      <div class="font-bold text-gray-900 group-hover:text-[#0b2545] transition">David Miller</div>
                      <div class="text-[11px] text-gray-400 font-mono">david.miller@acmecorp.ca</div>
                    </div>
                  </div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="font-black text-gray-900 text-sm">$320.00</div>
                  <div class="text-[11px] text-purple-600 font-bold">Fully Refunded to Card</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="flex items-center gap-1.5 font-mono font-bold text-gray-900">
                    <span>T3910284710</span>
                    <button onclick="copyRef('T3910284710')" title="Copy Ref" class="text-gray-400 hover:text-[#0b2545] transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                  <div class="text-[11px] text-gray-500 font-medium mt-0.5">Visa &bull;&bull;&bull;&bull; 3319</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">15 Sep 2026</div>
                  <div class="text-[10px] text-gray-400">09:15 AM</div>
                </td>
                <td class="py-5 px-5 text-center whitespace-nowrap">
                  <span class="w-7 h-7 rounded-full bg-purple-50 text-purple-600 border border-purple-200 inline-flex items-center justify-center shadow-2xs" title="Refunded">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1">
                    <button onclick="openReceiptModal('T3910284710', '$320.00 USD', 'David Miller', 'david.miller@acmecorp.ca', 'Visa ending in 3319', 'Refunded', '15 Sep 2026, 09:15 AM', '$0.00 USD', '$0.00 USD (Refunded)')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="Print Refund Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                    </button>
                    <button onclick="viewTransactionDetails('T3910284710', '$320.00 USD', '$0.00 USD', '$0.00 USD', 'David Miller', 'david.miller@acmecorp.ca', 'CUS_9481928', 'Card', 'Visa ending in 3319', 'REF_39102948', 'Refunded', '15 Sep 2026, 09:15 AM', 'USD Primary Vault')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Details">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- 7. Olivia Sterling -->
              <tr class="hover:bg-blue-50/20 transition group transaction-row" data-page="1" data-ref="T2948102948" data-name="Olivia Sterling" data-email="olivia@sterlinggroup.com" data-channel="card" data-status="successful" data-currency="USD">
                <td class="py-5 px-6 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-[#0b2545] text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs">OS</div>
                    <div>
                      <div class="font-bold text-gray-900 group-hover:text-[#0b2545] transition">Olivia Sterling</div>
                      <div class="text-[11px] text-gray-400 font-mono">olivia@sterlinggroup.com</div>
                    </div>
                  </div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="font-black text-gray-900 text-sm">$8,900.00</div>
                  <div class="text-[11px] text-gray-400">Net: <span class="font-bold text-emerald-700">$8,766.50</span> &bull; Fee: $133.50</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="flex items-center gap-1.5 font-mono font-bold text-gray-900">
                    <span>T2948102948</span>
                    <button onclick="copyRef('T2948102948')" title="Copy Ref" class="text-gray-400 hover:text-[#0b2545] transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                  <div class="text-[11px] text-gray-500 font-medium mt-0.5">Mastercard &bull;&bull;&bull;&bull; 9042</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">15 Sep 2026</div>
                  <div class="text-[10px] text-gray-400">07:20 AM</div>
                </td>
                <td class="py-5 px-5 text-center whitespace-nowrap">
                  <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center shadow-2xs" title="Successful">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1">
                    <button onclick="openReceiptModal('T2948102948', '$8,900.00 USD', 'Olivia Sterling', 'olivia@sterlinggroup.com', 'Mastercard ending in 9042', 'Successful', '15 Sep 2026, 07:20 AM', '$133.50 USD', '$8,766.50 USD')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="Print Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                    </button>
                    <button onclick="viewTransactionDetails('T2948102948', '$8,900.00 USD', '$8,766.50 USD', '$133.50 USD', 'Olivia Sterling', 'olivia@sterlinggroup.com', 'CUS_9910284', 'Card', 'Mastercard ending in 9042', 'AUTH_991028', 'Successful', '15 Sep 2026, 07:20 AM', 'USD Primary Vault')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Details">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- 8. Chukwudi Eze -->
              <tr class="hover:bg-blue-50/20 transition group transaction-row" data-page="1" data-ref="T1839201948" data-name="Chukwudi Eze" data-email="c.eze@enuguent.ng" data-channel="bank_transfer" data-status="successful" data-currency="NGN">
                <td class="py-5 px-6 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs">CE</div>
                    <div>
                      <div class="font-bold text-gray-900 group-hover:text-[#0b2545] transition">Chukwudi Eze</div>
                      <div class="text-[11px] text-gray-400 font-mono">c.eze@enuguent.ng</div>
                    </div>
                  </div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="font-black text-gray-900 text-sm">₦750,000.00</div>
                  <div class="text-[11px] text-gray-400">Net: <span class="font-bold text-emerald-700">₦738,750.00</span> &bull; Fee: ₦11,250.00</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="flex items-center gap-1.5 font-mono font-bold text-gray-900">
                    <span>T1839201948</span>
                    <button onclick="copyRef('T1839201948')" title="Copy Ref" class="text-gray-400 hover:text-[#0b2545] transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                  <div class="text-[11px] text-gray-500 font-medium mt-0.5">Bank Wire Transfer</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">14 Sep 2026</div>
                  <div class="text-[10px] text-gray-400">06:40 PM</div>
                </td>
                <td class="py-5 px-5 text-center whitespace-nowrap">
                  <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center shadow-2xs" title="Successful">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1">
                    <button onclick="openReceiptModal('T1839201948', '₦750,000.00 NGN', 'Chukwudi Eze', 'c.eze@enuguent.ng', 'Bank Wire', 'Successful', '14 Sep 2026, 06:40 PM', '₦11,250.00 NGN', '₦738,750.00 NGN')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="Print Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                    </button>
                    <button onclick="viewTransactionDetails('T1839201948', '₦750,000.00 NGN', '₦738,750.00 NGN', '₦11,250.00 NGN', 'Chukwudi Eze', 'c.eze@enuguent.ng', 'CUS_1839201', 'Bank Transfer', 'First Bank Direct', 'NIP_20260914', 'Successful', '14 Sep 2026, 06:40 PM', 'NGN Main Vault')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Details">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- 9. Elena Rostova -->
              <tr class="hover:bg-blue-50/20 transition group transaction-row" data-page="1" data-ref="T0928104819" data-name="Elena Rostova" data-email="elena@berlinfin.eu" data-channel="card" data-status="successful" data-currency="EUR">
                <td class="py-5 px-6 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-indigo-700 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs">ER</div>
                    <div>
                      <div class="font-bold text-gray-900 group-hover:text-[#0b2545] transition">Elena Rostova</div>
                      <div class="text-[11px] text-gray-400 font-mono">elena@berlinfin.eu</div>
                    </div>
                  </div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="font-black text-gray-900 text-sm">€3,400.00</div>
                  <div class="text-[11px] text-gray-400">Net: <span class="font-bold text-emerald-700">€3,349.00</span> &bull; Fee: €51.00</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="flex items-center gap-1.5 font-mono font-bold text-gray-900">
                    <span>T0928104819</span>
                    <button onclick="copyRef('T0928104819')" title="Copy Ref" class="text-gray-400 hover:text-[#0b2545] transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                  <div class="text-[11px] text-gray-500 font-medium mt-0.5">Visa &bull;&bull;&bull;&bull; 7712</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">14 Sep 2026</div>
                  <div class="text-[10px] text-gray-400">01:15 PM</div>
                </td>
                <td class="py-5 px-5 text-center whitespace-nowrap">
                  <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center shadow-2xs" title="Successful">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1">
                    <button onclick="openReceiptModal('T0928104819', '€3,400.00 EUR', 'Elena Rostova', 'elena@berlinfin.eu', 'Visa ending in 7712', 'Successful', '14 Sep 2026, 01:15 PM', '€51.00 EUR', '€3,349.00 EUR')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="Print Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                    </button>
                    <button onclick="viewTransactionDetails('T0928104819', '€3,400.00 EUR', '€3,349.00 EUR', '€51.00 EUR', 'Elena Rostova', 'elena@berlinfin.eu', 'CUS_0928104', 'Card', 'Visa ending in 7712', 'AUTH_092810', 'Successful', '14 Sep 2026, 01:15 PM', 'EUR Settlement Vault')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Details">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- 10. Lucas Wright -->
              <tr class="hover:bg-blue-50/20 transition group transaction-row" data-page="1" data-ref="T9920194820" data-name="Lucas Wright" data-email="l.wright@torontotech.ca" data-channel="card" data-status="successful" data-currency="CAD">
                <td class="py-5 px-6 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-teal-700 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs">LW</div>
                    <div>
                      <div class="font-bold text-gray-900 group-hover:text-[#0b2545] transition">Lucas Wright</div>
                      <div class="text-[11px] text-gray-400 font-mono">l.wright@torontotech.ca</div>
                    </div>
                  </div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="font-black text-gray-900 text-sm">CA$1,850.00</div>
                  <div class="text-[11px] text-gray-400">Net: <span class="font-bold text-emerald-700">CA$1,822.25</span> &bull; Fee: CA$27.75</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="flex items-center gap-1.5 font-mono font-bold text-gray-900">
                    <span>T9920194820</span>
                    <button onclick="copyRef('T9920194820')" title="Copy Ref" class="text-gray-400 hover:text-[#0b2545] transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                  <div class="text-[11px] text-gray-500 font-medium mt-0.5">Mastercard &bull;&bull;&bull;&bull; 4910</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">13 Sep 2026</div>
                  <div class="text-[10px] text-gray-400">11:05 AM</div>
                </td>
                <td class="py-5 px-5 text-center whitespace-nowrap">
                  <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center shadow-2xs" title="Successful">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1">
                    <button onclick="openReceiptModal('T9920194820', 'CA$1,850.00 CAD', 'Lucas Wright', 'l.wright@torontotech.ca', 'Mastercard ending in 4910', 'Successful', '13 Sep 2026, 11:05 AM', 'CA$27.75 CAD', 'CA$1,822.25 CAD')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="Print Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                    </button>
                    <button onclick="viewTransactionDetails('T9920194820', 'CA$1,850.00 CAD', 'CA$1,822.25 CAD', 'CA$27.75 CAD', 'Lucas Wright', 'l.wright@torontotech.ca', 'CUS_9920194', 'Card', 'Mastercard ending in 4910', 'AUTH_992019', 'Successful', '13 Sep 2026, 11:05 AM', 'CAD Operating Vault')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Details">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- ================= PAGE 2 ROWS (11 - 20) ================= -->

              <!-- 11. Chloe Dubois -->
              <tr class="hover:bg-blue-50/20 transition group transaction-row" data-page="2" style="display:none;" data-ref="T8810293819" data-name="Chloe Dubois" data-email="chloe@parismedia.fr" data-channel="card" data-status="successful" data-currency="EUR">
                <td class="py-5 px-6 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-[#0b2545] text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs">CD</div>
                    <div>
                      <div class="font-bold text-gray-900 group-hover:text-[#0b2545] transition">Chloe Dubois</div>
                      <div class="text-[11px] text-gray-400 font-mono">chloe@parismedia.fr</div>
                    </div>
                  </div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="font-black text-gray-900 text-sm">€5,120.00</div>
                  <div class="text-[11px] text-gray-400">Net: <span class="font-bold text-emerald-700">€5,043.20</span> &bull; Fee: €76.80</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="flex items-center gap-1.5 font-mono font-bold text-gray-900">
                    <span>T8810293819</span>
                    <button onclick="copyRef('T8810293819')" title="Copy Ref" class="text-gray-400 hover:text-[#0b2545] transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                  <div class="text-[11px] text-gray-500 font-medium mt-0.5">Visa &bull;&bull;&bull;&bull; 1948</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">12 Sep 2026</div>
                  <div class="text-[10px] text-gray-400">03:45 PM</div>
                </td>
                <td class="py-5 px-5 text-center whitespace-nowrap">
                  <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center shadow-2xs" title="Successful">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1">
                    <button onclick="openReceiptModal('T8810293819', '€5,120.00 EUR', 'Chloe Dubois', 'chloe@parismedia.fr', 'Visa ending in 1948', 'Successful', '12 Sep 2026, 03:45 PM', '€76.80 EUR', '€5,043.20 EUR')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="Print Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                    </button>
                    <button onclick="viewTransactionDetails('T8810293819', '€5,120.00 EUR', '€5,043.20 EUR', '€76.80 EUR', 'Chloe Dubois', 'chloe@parismedia.fr', 'CUS_8810293', 'Card', 'Visa ending in 1948', 'AUTH_881029', 'Successful', '12 Sep 2026, 03:45 PM', 'EUR Settlement Vault')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Details">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- 12. Tariq Al-Mansoor -->
              <tr class="hover:bg-blue-50/20 transition group transaction-row" data-page="2" style="display:none;" data-ref="T7730192841" data-name="Tariq Al-Mansoor" data-email="tariq@gulfholdings.ae" data-channel="card" data-status="successful" data-currency="USD">
                <td class="py-5 px-6 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-slate-800 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs">TA</div>
                    <div>
                      <div class="font-bold text-gray-900 group-hover:text-[#0b2545] transition">Tariq Al-Mansoor</div>
                      <div class="text-[11px] text-gray-400 font-mono">tariq@gulfholdings.ae</div>
                    </div>
                  </div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="font-black text-gray-900 text-sm">$15,400.00</div>
                  <div class="text-[11px] text-gray-400">Net: <span class="font-bold text-emerald-700">$15,169.00</span> &bull; Fee: $231.00</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="flex items-center gap-1.5 font-mono font-bold text-gray-900">
                    <span>T7730192841</span>
                    <button onclick="copyRef('T7730192841')" title="Copy Ref" class="text-gray-400 hover:text-[#0b2545] transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                  <div class="text-[11px] text-gray-500 font-medium mt-0.5">Visa Corporate &bull;&bull;&bull;&bull; 8820</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">12 Sep 2026</div>
                  <div class="text-[10px] text-gray-400">01:10 PM</div>
                </td>
                <td class="py-5 px-5 text-center whitespace-nowrap">
                  <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center shadow-2xs" title="Successful">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1">
                    <button onclick="openReceiptModal('T7730192841', '$15,400.00 USD', 'Tariq Al-Mansoor', 'tariq@gulfholdings.ae', 'Visa ending in 8820', 'Successful', '12 Sep 2026, 01:10 PM', '$231.00 USD', '$15,169.00 USD')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="Print Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                    </button>
                    <button onclick="viewTransactionDetails('T7730192841', '$15,400.00 USD', '$15,169.00 USD', '$231.00 USD', 'Tariq Al-Mansoor', 'tariq@gulfholdings.ae', 'CUS_7730192', 'Card', 'Visa ending in 8820', 'AUTH_773019', 'Successful', '12 Sep 2026, 01:10 PM', 'USD Primary Vault')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Details">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- 13. Blessing Adeyemi -->
              <tr class="hover:bg-blue-50/20 transition group transaction-row" data-page="2" style="display:none;" data-ref="T6620194810" data-name="Blessing Adeyemi" data-email="blessing@lagoslogistics.ng" data-channel="bank_transfer" data-status="successful" data-currency="NGN">
                <td class="py-5 px-6 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-emerald-800 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs">BA</div>
                    <div>
                      <div class="font-bold text-gray-900 group-hover:text-[#0b2545] transition">Blessing Adeyemi</div>
                      <div class="text-[11px] text-gray-400 font-mono">blessing@lagoslogistics.ng</div>
                    </div>
                  </div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="font-black text-gray-900 text-sm">₦3,200,000.00</div>
                  <div class="text-[11px] text-gray-400">Net: <span class="font-bold text-emerald-700">₦3,152,000.00</span> &bull; Fee: ₦48,000.00</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="flex items-center gap-1.5 font-mono font-bold text-gray-900">
                    <span>T6620194810</span>
                    <button onclick="copyRef('T6620194810')" title="Copy Ref" class="text-gray-400 hover:text-[#0b2545] transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                  <div class="text-[11px] text-gray-500 font-medium mt-0.5">Virtual Account Transfer</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">11 Sep 2026</div>
                  <div class="text-[10px] text-gray-400">09:50 AM</div>
                </td>
                <td class="py-5 px-5 text-center whitespace-nowrap">
                  <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center shadow-2xs" title="Successful">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1">
                    <button onclick="openReceiptModal('T6620194810', '₦3,200,000.00 NGN', 'Blessing Adeyemi', 'blessing@lagoslogistics.ng', 'Virtual Account', 'Successful', '11 Sep 2026, 09:50 AM', '₦48,000.00 NGN', '₦3,152,000.00 NGN')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="Print Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                    </button>
                    <button onclick="viewTransactionDetails('T6620194810', '₦3,200,000.00 NGN', '₦3,152,000.00 NGN', '₦48,000.00 NGN', 'Blessing Adeyemi', 'blessing@lagoslogistics.ng', 'CUS_6620194', 'Bank Transfer', 'Providus Bank VA', 'NIP_20260911', 'Successful', '11 Sep 2026, 09:50 AM', 'NGN Main Vault')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Details">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- 14. James O'Connor -->
              <tr class="hover:bg-blue-50/20 transition group transaction-row" data-page="2" style="display:none;" data-ref="T5520194821" data-name="James O'Connor" data-email="james@dublindesign.ie" data-channel="card" data-status="successful" data-currency="EUR">
                <td class="py-5 px-6 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-blue-900 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs">JO</div>
                    <div>
                      <div class="font-bold text-gray-900 group-hover:text-[#0b2545] transition">James O'Connor</div>
                      <div class="text-[11px] text-gray-400 font-mono">james@dublindesign.ie</div>
                    </div>
                  </div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="font-black text-gray-900 text-sm">€2,150.00</div>
                  <div class="text-[11px] text-gray-400">Net: <span class="font-bold text-emerald-700">€2,117.75</span> &bull; Fee: €32.25</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="flex items-center gap-1.5 font-mono font-bold text-gray-900">
                    <span>T5520194821</span>
                    <button onclick="copyRef('T5520194821')" title="Copy Ref" class="text-gray-400 hover:text-[#0b2545] transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                  <div class="text-[11px] text-gray-500 font-medium mt-0.5">Mastercard &bull;&bull;&bull;&bull; 6192</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">10 Sep 2026</div>
                  <div class="text-[10px] text-gray-400">02:15 PM</div>
                </td>
                <td class="py-5 px-5 text-center whitespace-nowrap">
                  <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center shadow-2xs" title="Successful">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1">
                    <button onclick="openReceiptModal('T5520194821', '€2,150.00 EUR', 'James O\'Connor', 'james@dublindesign.ie', 'Mastercard ending in 6192', 'Successful', '10 Sep 2026, 02:15 PM', '€32.25 EUR', '€2,117.75 EUR')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="Print Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                    </button>
                    <button onclick="viewTransactionDetails('T5520194821', '€2,150.00 EUR', '€2,117.75 EUR', '€32.25 EUR', 'James O\'Connor', 'james@dublindesign.ie', 'CUS_5520194', 'Card', 'Mastercard ending in 6192', 'AUTH_552019', 'Successful', '10 Sep 2026, 02:15 PM', 'EUR Settlement Vault')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Details">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- 15. Maya Tanaka -->
              <tr class="hover:bg-blue-50/20 transition group transaction-row" data-page="2" style="display:none;" data-ref="T4410293810" data-name="Maya Tanaka" data-email="maya@tokyoai.jp" data-channel="card" data-status="successful" data-currency="USD">
                <td class="py-5 px-6 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-rose-700 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs">MT</div>
                    <div>
                      <div class="font-bold text-gray-900 group-hover:text-[#0b2545] transition">Maya Tanaka</div>
                      <div class="text-[11px] text-gray-400 font-mono">maya@tokyoai.jp</div>
                    </div>
                  </div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="font-black text-gray-900 text-sm">$6,800.00</div>
                  <div class="text-[11px] text-gray-400">Net: <span class="font-bold text-emerald-700">$6,698.00</span> &bull; Fee: $102.00</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="flex items-center gap-1.5 font-mono font-bold text-gray-900">
                    <span>T4410293810</span>
                    <button onclick="copyRef('T4410293810')" title="Copy Ref" class="text-gray-400 hover:text-[#0b2545] transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                  <div class="text-[11px] text-gray-500 font-medium mt-0.5">Visa &bull;&bull;&bull;&bull; 2041</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">09 Sep 2026</div>
                  <div class="text-[10px] text-gray-400">11:30 AM</div>
                </td>
                <td class="py-5 px-5 text-center whitespace-nowrap">
                  <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center shadow-2xs" title="Successful">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1">
                    <button onclick="openReceiptModal('T4410293810', '$6,800.00 USD', 'Maya Tanaka', 'maya@tokyoai.jp', 'Visa ending in 2041', 'Successful', '09 Sep 2026, 11:30 AM', '$102.00 USD', '$6,698.00 USD')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="Print Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                    </button>
                    <button onclick="viewTransactionDetails('T4410293810', '$6,800.00 USD', '$6,698.00 USD', '$102.00 USD', 'Maya Tanaka', 'maya@tokyoai.jp', 'CUS_4410293', 'Card', 'Visa ending in 2041', 'AUTH_441029', 'Successful', '09 Sep 2026, 11:30 AM', 'USD Primary Vault')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Details">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- 16. Ibrahim Sani -->
              <tr class="hover:bg-blue-50/20 transition group transaction-row" data-page="2" style="display:none;" data-ref="T3310294829" data-name="Ibrahim Sani" data-email="i.sani@kanoagro.ng" data-channel="bank_transfer" data-status="successful" data-currency="NGN">
                <td class="py-5 px-6 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-emerald-700 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs">IS</div>
                    <div>
                      <div class="font-bold text-gray-900 group-hover:text-[#0b2545] transition">Ibrahim Sani</div>
                      <div class="text-[11px] text-gray-400 font-mono">i.sani@kanoagro.ng</div>
                    </div>
                  </div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="font-black text-gray-900 text-sm">₦5,400,000.00</div>
                  <div class="text-[11px] text-gray-400">Net: <span class="font-bold text-emerald-700">₦5,319,000.00</span> &bull; Fee: ₦81,000.00</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="flex items-center gap-1.5 font-mono font-bold text-gray-900">
                    <span>T3310294829</span>
                    <button onclick="copyRef('T3310294829')" title="Copy Ref" class="text-gray-400 hover:text-[#0b2545] transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                  <div class="text-[11px] text-gray-500 font-medium mt-0.5">Virtual Account Transfer</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">08 Sep 2026</div>
                  <div class="text-[10px] text-gray-400">04:40 PM</div>
                </td>
                <td class="py-5 px-5 text-center whitespace-nowrap">
                  <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center shadow-2xs" title="Successful">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1">
                    <button onclick="openReceiptModal('T3310294829', '₦5,400,000.00 NGN', 'Ibrahim Sani', 'i.sani@kanoagro.ng', 'Virtual Account', 'Successful', '08 Sep 2026, 04:40 PM', '₦81,000.00 NGN', '₦5,319,000.00 NGN')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="Print Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                    </button>
                    <button onclick="viewTransactionDetails('T3310294829', '₦5,400,000.00 NGN', '₦5,319,000.00 NGN', '₦81,000.00 NGN', 'Ibrahim Sani', 'i.sani@kanoagro.ng', 'CUS_3310294', 'Bank Transfer', 'Titan Trust Bank', 'NIP_20260908', 'Successful', '08 Sep 2026, 04:40 PM', 'NGN Main Vault')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Details">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- 17. Hannah Schmidt -->
              <tr class="hover:bg-blue-50/20 transition group transaction-row" data-page="2" style="display:none;" data-ref="T2210294819" data-name="Hannah Schmidt" data-email="hannah@munichdev.de" data-channel="card" data-status="successful" data-currency="EUR">
                <td class="py-5 px-6 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-indigo-900 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs">HS</div>
                    <div>
                      <div class="font-bold text-gray-900 group-hover:text-[#0b2545] transition">Hannah Schmidt</div>
                      <div class="text-[11px] text-gray-400 font-mono">hannah@munichdev.de</div>
                    </div>
                  </div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="font-black text-gray-900 text-sm">€950.00</div>
                  <div class="text-[11px] text-gray-400">Net: <span class="font-bold text-emerald-700">€935.75</span> &bull; Fee: €14.25</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="flex items-center gap-1.5 font-mono font-bold text-gray-900">
                    <span>T2210294819</span>
                    <button onclick="copyRef('T2210294819')" title="Copy Ref" class="text-gray-400 hover:text-[#0b2545] transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                  <div class="text-[11px] text-gray-500 font-medium mt-0.5">Mastercard &bull;&bull;&bull;&bull; 1029</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">08 Sep 2026</div>
                  <div class="text-[10px] text-gray-400">10:15 AM</div>
                </td>
                <td class="py-5 px-5 text-center whitespace-nowrap">
                  <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center shadow-2xs" title="Successful">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1">
                    <button onclick="openReceiptModal('T2210294819', '€950.00 EUR', 'Hannah Schmidt', 'hannah@munichdev.de', 'Mastercard ending in 1029', 'Successful', '08 Sep 2026, 10:15 AM', '€14.25 EUR', '€935.75 EUR')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="Print Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                    </button>
                    <button onclick="viewTransactionDetails('T2210294819', '€950.00 EUR', '€935.75 EUR', '€14.25 EUR', 'Hannah Schmidt', 'hannah@munichdev.de', 'CUS_2210294', 'Card', 'Mastercard ending in 1029', 'AUTH_221029', 'Successful', '08 Sep 2026, 10:15 AM', 'EUR Settlement Vault')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Details">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- 18. Noah Van Der Beek -->
              <tr class="hover:bg-blue-50/20 transition group transaction-row" data-page="2" style="display:none;" data-ref="T1120194829" data-name="Noah Van Der Beek" data-email="noah@amsterdamflow.nl" data-channel="card" data-status="successful" data-currency="EUR">
                <td class="py-5 px-6 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-blue-700 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs">NV</div>
                    <div>
                      <div class="font-bold text-gray-900 group-hover:text-[#0b2545] transition">Noah Van Der Beek</div>
                      <div class="text-[11px] text-gray-400 font-mono">noah@amsterdamflow.nl</div>
                    </div>
                  </div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="font-black text-gray-900 text-sm">€4,600.00</div>
                  <div class="text-[11px] text-gray-400">Net: <span class="font-bold text-emerald-700">€4,531.00</span> &bull; Fee: €69.00</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="flex items-center gap-1.5 font-mono font-bold text-gray-900">
                    <span>T1120194829</span>
                    <button onclick="copyRef('T1120194829')" title="Copy Ref" class="text-gray-400 hover:text-[#0b2545] transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                  <div class="text-[11px] text-gray-500 font-medium mt-0.5">Visa &bull;&bull;&bull;&bull; 9940</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">07 Sep 2026</div>
                  <div class="text-[10px] text-gray-400">03:30 PM</div>
                </td>
                <td class="py-5 px-5 text-center whitespace-nowrap">
                  <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center shadow-2xs" title="Successful">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1">
                    <button onclick="openReceiptModal('T1120194829', '€4,600.00 EUR', 'Noah Van Der Beek', 'noah@amsterdamflow.nl', 'Visa ending in 9940', 'Successful', '07 Sep 2026, 03:30 PM', '€69.00 EUR', '€4,531.00 EUR')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="Print Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                    </button>
                    <button onclick="viewTransactionDetails('T1120194829', '€4,600.00 EUR', '€4,531.00 EUR', '€69.00 EUR', 'Noah Van Der Beek', 'noah@amsterdamflow.nl', 'CUS_1120194', 'Card', 'Visa ending in 9940', 'AUTH_112019', 'Successful', '07 Sep 2026, 03:30 PM', 'EUR Settlement Vault')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Details">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- 19. Gabriel Santos -->
              <tr class="hover:bg-blue-50/20 transition group transaction-row" data-page="2" style="display:none;" data-ref="T9018274619" data-name="Gabriel Santos" data-email="gabriel@saopaulo.br" data-channel="card" data-status="successful" data-currency="USD">
                <td class="py-5 px-6 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-amber-700 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs">GS</div>
                    <div>
                      <div class="font-bold text-gray-900 group-hover:text-[#0b2545] transition">Gabriel Santos</div>
                      <div class="text-[11px] text-gray-400 font-mono">gabriel@saopaulo.br</div>
                    </div>
                  </div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="font-black text-gray-900 text-sm">$1,450.00</div>
                  <div class="text-[11px] text-gray-400">Net: <span class="font-bold text-emerald-700">$1,428.25</span> &bull; Fee: $21.75</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="flex items-center gap-1.5 font-mono font-bold text-gray-900">
                    <span>T9018274619</span>
                    <button onclick="copyRef('T9018274619')" title="Copy Ref" class="text-gray-400 hover:text-[#0b2545] transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                  <div class="text-[11px] text-gray-500 font-medium mt-0.5">Mastercard &bull;&bull;&bull;&bull; 3019</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">06 Sep 2026</div>
                  <div class="text-[10px] text-gray-400">01:05 PM</div>
                </td>
                <td class="py-5 px-5 text-center whitespace-nowrap">
                  <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center shadow-2xs" title="Successful">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1">
                    <button onclick="openReceiptModal('T9018274619', '$1,450.00 USD', 'Gabriel Santos', 'gabriel@saopaulo.br', 'Mastercard ending in 3019', 'Successful', '06 Sep 2026, 01:05 PM', '$21.75 USD', '$1,428.25 USD')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="Print Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                    </button>
                    <button onclick="viewTransactionDetails('T9018274619', '$1,450.00 USD', '$1,428.25 USD', '$21.75 USD', 'Gabriel Santos', 'gabriel@saopaulo.br', 'CUS_9018274', 'Card', 'Mastercard ending in 3019', 'AUTH_901827', 'Successful', '06 Sep 2026, 01:05 PM', 'USD Primary Vault')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Details">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- 20. Fatima Mansur -->
              <tr class="hover:bg-blue-50/20 transition group transaction-row" data-page="2" style="display:none;" data-ref="T8019284729" data-name="Fatima Mansur" data-email="f.mansur@cairobiz.eg" data-channel="card" data-status="successful" data-currency="USD">
                <td class="py-5 px-6 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-teal-800 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs">FM</div>
                    <div>
                      <div class="font-bold text-gray-900 group-hover:text-[#0b2545] transition">Fatima Mansur</div>
                      <div class="text-[11px] text-gray-400 font-mono">f.mansur@cairobiz.eg</div>
                    </div>
                  </div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="font-black text-gray-900 text-sm">$3,750.00</div>
                  <div class="text-[11px] text-gray-400">Net: <span class="font-bold text-emerald-700">$3,693.75</span> &bull; Fee: $56.25</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="flex items-center gap-1.5 font-mono font-bold text-gray-900">
                    <span>T8019284729</span>
                    <button onclick="copyRef('T8019284729')" title="Copy Ref" class="text-gray-400 hover:text-[#0b2545] transition">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                  <div class="text-[11px] text-gray-500 font-medium mt-0.5">Visa &bull;&bull;&bull;&bull; 4410</div>
                </td>
                <td class="py-5 px-5 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">05 Sep 2026</div>
                  <div class="text-[10px] text-gray-400">10:50 AM</div>
                </td>
                <td class="py-5 px-5 text-center whitespace-nowrap">
                  <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center shadow-2xs" title="Successful">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1">
                    <button onclick="openReceiptModal('T8019284729', '$3,750.00 USD', 'Fatima Mansur', 'f.mansur@cairobiz.eg', 'Visa ending in 4410', 'Successful', '05 Sep 2026, 10:50 AM', '$56.25 USD', '$3,693.75 USD')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="Print Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                    </button>
                    <button onclick="viewTransactionDetails('T8019284729', '$3,750.00 USD', '$3,693.75 USD', '$56.25 USD', 'Fatima Mansur', 'f.mansur@cairobiz.eg', 'CUS_8019284', 'Card', 'Visa ending in 4410', 'AUTH_801928', 'Successful', '05 Sep 2026, 10:50 AM', 'USD Primary Vault')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Details">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>

            </tbody>
          </table>
        </div>

        <!-- Table Footer / Dynamic Pagination (10 items per page) -->
        <div class="p-4 sm:p-5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
          <div>
            Showing <span class="font-semibold text-gray-900" id="paginationRange">0 – 0</span> of <span id="paginationTotal" class="font-semibold text-gray-900">0</span> transactions
          </div>
          <div class="flex items-center gap-1.5">
            <button type="button" id="prevPageBtn" onclick="changePage(currentPage - 1)" class="px-3.5 py-1.5 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition text-xs font-semibold disabled:opacity-40 disabled:cursor-not-allowed">
              Previous
            </button>
            <button type="button" id="pageBtn1" onclick="changePage(1)" class="px-3 py-1.5 rounded-lg bg-[#0b2545] text-white text-xs font-semibold transition">
              1
            </button>
            <button type="button" id="pageBtn2" onclick="changePage(2)" class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition text-xs font-semibold">
              2
            </button>
            <button type="button" id="nextPageBtn" onclick="changePage(currentPage + 1)" class="px-3.5 py-1.5 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition text-xs font-semibold">
              Next
            </button>
          </div>
        </div>

      </div>

    </main>
  </div>
</div>

<!-- ================= TRANSACTION DETAILS MODAL ================= -->
<div id="transactionDetailsModal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-xs flex items-stretch justify-end transition-opacity duration-300">
  <div id="transactionDetailsPanel" class="relative w-full max-w-lg h-full bg-white shadow-2xl border-l border-gray-100 overflow-hidden flex flex-col transform translate-x-full transition-transform duration-300 ease-out">
    
    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/60 flex items-start justify-between">
      <div>
        <div class="flex items-center gap-2.5">
          <h3 id="tdModalAmount" class="text-xl font-black text-gray-900">$0.00</h3>
          <span id="tdModalStatusBadge" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
            Successful
          </span>
        </div>
        <div class="flex items-center gap-2 mt-1">
          <span class="font-mono text-xs text-gray-500" id="tdModalRef">T0000000000</span>
          <button onclick="copyTdRef()" title="Copy Reference" class="text-gray-400 hover:text-[#0b2545] transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
          </button>
        </div>
      </div>
      <button onclick="closeTransactionDetailsModal()" class="w-9 h-9 rounded-full bg-white hover:bg-gray-100 border border-gray-200 text-gray-500 hover:text-gray-700 flex items-center justify-center transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
      </button>
    </div>

    <div class="p-6 space-y-4 flex-1 overflow-y-auto">
      <div class="bg-gray-50 border border-gray-200/70 rounded-xl p-4 divide-y divide-gray-200/60 text-xs">
        <div class="pb-2 flex items-center justify-between">
          <span class="text-gray-500">Gross Amount</span>
          <span class="font-bold text-gray-900" id="tdModalGross">--</span>
        </div>
        <div class="py-2 flex items-center justify-between">
          <span class="text-gray-500">Processing Fee</span>
          <span class="font-medium text-gray-600" id="tdModalFee">--</span>
        </div>
        <div class="pt-2 flex items-center justify-between">
          <span class="text-gray-800 font-bold">Net Payout to Wallet</span>
          <span class="font-black text-emerald-700 text-sm" id="tdModalNet">--</span>
        </div>
      </div>

      <div>
        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Customer Info</h4>
        <div class="bg-white border border-gray-200/80 rounded-xl divide-y divide-gray-100 text-xs">
          <div class="py-2.5 px-3.5 flex items-center justify-between">
            <span class="text-gray-500">Name</span>
            <span class="font-semibold text-gray-900" id="tdModalCustomerName">--</span>
          </div>
          <div class="py-2.5 px-3.5 flex items-center justify-between">
            <span class="text-gray-500">Email</span>
            <span class="font-mono text-gray-800" id="tdModalCustomerEmail">--</span>
          </div>
          <div class="py-2.5 px-3.5 flex items-center justify-between">
            <span class="text-gray-500">Customer Code</span>
            <span class="font-mono font-medium text-blue-900 bg-blue-50 px-2 py-0.5 rounded text-[11px]" id="tdModalCustomerCode">--</span>
          </div>
        </div>
      </div>

      <div>
        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Payment Details</h4>
        <div class="bg-white border border-gray-200/80 rounded-xl divide-y divide-gray-100 text-xs">
          <div class="py-2.5 px-3.5 flex items-center justify-between">
            <span class="text-gray-500">Channel</span>
            <span class="font-semibold text-gray-900" id="tdModalChannel">--</span>
          </div>
          <div class="py-2.5 px-3.5 flex items-center justify-between">
            <span class="text-gray-500">Instrument</span>
            <span class="font-medium text-gray-800" id="tdModalAuth">--</span>
          </div>
          <div class="py-2.5 px-3.5 flex items-center justify-between">
            <span class="text-gray-500">Paid Timestamp</span>
            <span class="text-gray-700" id="tdModalPaidAt">--</span>
          </div>
        </div>
      </div>
    </div>

    <div class="p-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
      <button onclick="triggerRefund()" class="px-3 py-1.5 rounded-xl border border-rose-200 text-rose-700 hover:bg-rose-50 text-xs font-semibold transition">
        Refund
      </button>
      <button onclick="showReceiptFromDrawer()" class="px-4 py-1.5 rounded-xl bg-[#0b2545] hover:bg-[#134074] text-white text-xs font-semibold transition">
        View Receipt
      </button>
    </div>

  </div>
</div>

<!-- ================= OFFICIAL RECEIPT MODAL ================= -->
<div id="receiptModal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-xs flex items-stretch justify-end transition-opacity duration-300">
  <div id="receiptPanel" class="relative w-full max-w-md h-full bg-white shadow-2xl border-l border-gray-100 overflow-hidden flex flex-col transform translate-x-full transition-transform duration-300 ease-out">
    
    <div class="p-6 space-y-5 flex-1 overflow-y-auto" id="printableReceiptContent">
      <div class="flex items-center justify-between border-b border-gray-100 pb-3">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-xl bg-[#0b2545] text-white flex items-center justify-center font-black text-xs">FS</div>
          <div class="text-sm font-black text-gray-900">FLOWWITHLIT</div>
        </div>
        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full" id="receiptStatusBadge">
          Paid &bull; Settled
        </span>
      </div>

      <div class="text-center py-2">
        <div class="text-xs text-gray-400">Total Paid</div>
        <div class="text-3xl font-black text-gray-900 mt-0.5" id="receiptAmount">$0.00</div>
      </div>

      <div class="bg-gray-50 border border-gray-200/80 rounded-xl p-3.5 divide-y divide-gray-200/60 text-xs">
        <div class="pb-2 flex items-center justify-between">
          <span class="text-gray-500">Reference:</span>
          <span class="font-mono font-bold text-gray-900" id="receiptRef">--</span>
        </div>
        <div class="py-2 flex items-center justify-between">
          <span class="text-gray-500">Customer:</span>
          <span class="font-medium text-gray-900" id="receiptCustomer">--</span>
        </div>
        <div class="py-2 flex items-center justify-between">
          <span class="text-gray-500">Email:</span>
          <span class="font-mono text-gray-700 text-[11px]" id="receiptEmail">--</span>
        </div>
        <div class="py-2 flex items-center justify-between">
          <span class="text-gray-500">Payment Channel:</span>
          <span class="font-medium text-gray-900" id="receiptMethod">--</span>
        </div>
        <div class="py-2 flex items-center justify-between">
          <span class="text-gray-500">Net Settled:</span>
          <span class="font-bold text-emerald-700" id="receiptNet">--</span>
        </div>
        <div class="pt-2 flex items-center justify-between">
          <span class="text-gray-500">Date:</span>
          <span class="text-gray-700" id="receiptDate">--</span>
        </div>
      </div>
    </div>

    <div class="p-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
      <button type="button" onclick="closeReceiptModal()" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-200 rounded-xl transition">
        Close
      </button>
      <button type="button" onclick="window.print()" class="px-5 py-2 text-xs font-semibold text-white bg-[#0b2545] hover:bg-[#134074] rounded-xl shadow-xs transition">
        Print
      </button>
    </div>

  </div>
</div>

<!-- ================= FLOATING TOAST ================= -->
<div id="floatingToast" class="fixed bottom-6 right-6 z-60 hidden transition-all duration-300 transform translate-y-3 opacity-0">
  <div class="bg-gray-900 text-white px-4 py-2.5 rounded-xl shadow-xl flex items-center gap-2 border border-gray-700 text-xs">
    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
    <span id="toastMessage">Done</span>
  </div>
</div>

<!-- ================= SCRIPTS ================= -->
<script>
  let currentPage = 1;
  const rowsPerPage = 10;
  let currentDrawerData = {};

  // Pagination Handler
  function changePage(page) {
    if (page < 1 || page > 2) return;
    currentPage = page;

    const rows = document.querySelectorAll('.transaction-row');
    rows.forEach(row => {
      const rowPage = parseInt(row.getAttribute('data-page'));
      if (rowPage === currentPage) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });

    // Update Pagination UI
    const prevBtn = document.getElementById('prevPageBtn');
    const nextBtn = document.getElementById('nextPageBtn');
    const btn1 = document.getElementById('pageBtn1');
    const btn2 = document.getElementById('pageBtn2');
    const range = document.getElementById('paginationRange');

    if (currentPage === 1) {
      prevBtn.disabled = true;
      nextBtn.disabled = false;
      btn1.className = 'px-3 py-1.5 rounded-lg bg-[#0b2545] text-white text-xs font-semibold transition';
      btn2.className = 'px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition text-xs font-semibold';
      range.textContent = '1 – 10';
    } else {
      prevBtn.disabled = false;
      nextBtn.disabled = true;
      btn2.className = 'px-3 py-1.5 rounded-lg bg-[#0b2545] text-white text-xs font-semibold transition';
      btn1.className = 'px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition text-xs font-semibold';
      range.textContent = '11 – 20';
    }

    // Scroll to top of table smoothly
    document.querySelector('table').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  // Copy Reference
  function copyRef(ref) {
    navigator.clipboard.writeText(ref).then(() => {
      showToast(`Copied ${ref}`);
    });
  }

  function copyTdRef() {
    if (currentDrawerData.ref) copyRef(currentDrawerData.ref);
  }

  // Toast
  function showToast(message) {
    const toast = document.getElementById('floatingToast');
    document.getElementById('toastMessage').textContent = message;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.remove('translate-y-3', 'opacity-0'), 10);
    setTimeout(() => {
      toast.classList.add('translate-y-3', 'opacity-0');
      setTimeout(() => toast.classList.add('hidden'), 250);
    }, 2500);
  }

  // Open & Close Details Modal
  function viewTransactionDetails(ref, amount, net, fee, customer, email, cusCode, channel, auth, clearingCode, status, paidAt, vault) {
    currentDrawerData = { ref, amount, net, fee, customer, email, cusCode, channel, auth, clearingCode, status, paidAt, vault };

    document.getElementById('tdModalAmount').textContent = amount;
    document.getElementById('tdModalRef').textContent = ref;
    document.getElementById('tdModalGross').textContent = amount;
    document.getElementById('tdModalFee').textContent = fee;
    document.getElementById('tdModalNet').textContent = net;
    document.getElementById('tdModalCustomerName').textContent = customer;
    document.getElementById('tdModalCustomerEmail').textContent = email;
    document.getElementById('tdModalCustomerCode').textContent = cusCode;
    document.getElementById('tdModalChannel').textContent = channel;
    document.getElementById('tdModalAuth').textContent = auth;
    document.getElementById('tdModalPaidAt').textContent = paidAt;

    const modal = document.getElementById('transactionDetailsModal');
    const panel = document.getElementById('transactionDetailsPanel');
    modal.classList.remove('hidden');
    requestAnimationFrame(() => panel.classList.remove('translate-x-full'));
    document.body.classList.add('overflow-hidden');
  }

  function closeTransactionDetailsModal() {
    const modal = document.getElementById('transactionDetailsModal');
    const panel = document.getElementById('transactionDetailsPanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => modal.classList.add('hidden'), 300);
    document.body.classList.remove('overflow-hidden');
  }

  // Open & Close Receipt Modal
  function openReceiptModal(ref, amount, customer, email, method, status, date, fee, net) {
    document.getElementById('receiptRef').textContent = ref;
    document.getElementById('receiptAmount').textContent = amount;
    document.getElementById('receiptCustomer').textContent = customer;
    document.getElementById('receiptEmail').textContent = email;
    document.getElementById('receiptMethod').textContent = method;
    document.getElementById('receiptDate').textContent = date;
    document.getElementById('receiptNet').textContent = net;

    const modal = document.getElementById('receiptModal');
    const panel = document.getElementById('receiptPanel');
    modal.classList.remove('hidden');
    requestAnimationFrame(() => panel.classList.remove('translate-x-full'));
    document.body.classList.add('overflow-hidden');
  }

  function closeReceiptModal() {
    const modal = document.getElementById('receiptModal');
    const panel = document.getElementById('receiptPanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => modal.classList.add('hidden'), 300);
    document.body.classList.remove('overflow-hidden');
  }

  function showReceiptFromDrawer() {
    closeTransactionDetailsModal();
    openReceiptModal(
      currentDrawerData.ref,
      currentDrawerData.amount,
      currentDrawerData.customer,
      currentDrawerData.email,
      currentDrawerData.auth,
      currentDrawerData.status,
      currentDrawerData.paidAt,
      currentDrawerData.fee,
      currentDrawerData.net
    );
  }

  function triggerRefund() {
    if (confirm(`Issue refund for ${currentDrawerData.ref}?`)) {
      showToast(`Refund initiated for ${currentDrawerData.ref}`);
      closeTransactionDetailsModal();
    }
  }

  // Filter Table
  function filterTransactions() {
    const search = (document.getElementById('transactionSearchInput')?.value || '').toLowerCase();
    const channel = document.getElementById('channelFilter')?.value || 'all';
    const status = document.getElementById('statusFilter')?.value || 'all';
    const currency = document.getElementById('currencyFilter')?.value || 'all';

    const rows = document.querySelectorAll('.transaction-row');
    let visible = 0;

    rows.forEach(row => {
      const ref = (row.getAttribute('data-ref') || '').toLowerCase();
      const name = (row.getAttribute('data-name') || '').toLowerCase();
      const email = (row.getAttribute('data-email') || '').toLowerCase();
      const rChannel = row.getAttribute('data-channel') || '';
      const rStatus = row.getAttribute('data-status') || '';
      const rCurrency = row.getAttribute('data-currency') || '';

      const matchSearch = !search || ref.includes(search) || name.includes(search) || email.includes(search);
      const matchChannel = channel === 'all' || rChannel === channel;
      const matchStatus = status === 'all' || rStatus === status;
      const matchCurrency = currency === 'all' || rCurrency === currency;

      if (matchSearch && matchChannel && matchStatus && matchCurrency) {
        // If filtering, display matching row irrespective of page
        row.style.display = '';
        visible++;
      } else {
        row.style.display = 'none';
      }
    });

    if (!search && channel === 'all' && status === 'all' && currency === 'all') {
      changePage(currentPage);
    }
  }

  document.getElementById('transactionSearchInput')?.addEventListener('input', filterTransactions);

  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeTransactionDetailsModal();
      closeReceiptModal();
    }
  });
</script>

<script>
  (() => {
    const state = { page: 1, pageSize: 10, rows: [], totalPages: 1 };
    const token = () => localStorage.getItem('flow_token') || '';
    const esc = (value) => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
    const money = (value, currency) => { try { return new Intl.NumberFormat('en-NG', {style:'currency', currency, maximumFractionDigits: 2}).format(Number(value || 0)); } catch (_) { return `${Number(value || 0).toLocaleString()} ${currency}`; } };
    const label = (value) => ({successful:'Successful',pending:'Pending',failed:'Failed',refunded:'Refunded'}[value] || value);
    const channelLabel = (value) => ({card:'Card',apple_pay:'Apple Pay',bank_transfer:'Bank Transfer',crypto:'Crypto'}[value] || value);
    const dateLabel = (value) => value ? new Date(String(value).replace(' ','T') + 'Z').toLocaleString('en-NG', {dateStyle:'medium', timeStyle:'short'}) : '—';
    const filters = () => ({ range: document.getElementById('dateRangeFilter')?.value || '30d', search: document.getElementById('transactionSearchInput')?.value.trim() || '', channel: document.getElementById('channelFilter')?.value || 'all', status: document.getElementById('statusFilter')?.value || 'all', currency: document.getElementById('currencyFilter')?.value || 'all', page: state.page, page_size: state.pageSize });
    const api = async (path, params) => { const query = new URLSearchParams(params); const response = await fetch(`../api/transactions/${path}?${query}`, {headers:{Authorization:`Bearer ${token()}`,Accept:'application/json'}}); const result = await response.json(); if (!response.ok || !result.success) throw new Error(result.error || 'Unable to load transactions.'); return result; };
    function updatePagination(p) {
      state.totalPages = p.total_pages || 1; document.getElementById('paginationRange').textContent = `${p.from || 0} – ${p.to || 0}`; document.getElementById('paginationTotal').textContent = p.total_count || 0;
      const prev=document.getElementById('prevPageBtn'), next=document.getElementById('nextPageBtn'), b1=document.getElementById('pageBtn1'), b2=document.getElementById('pageBtn2');
      prev.disabled = state.page <= 1; next.disabled = state.page >= state.totalPages; b1.textContent = state.page; b2.textContent = state.totalPages > state.page ? state.page + 1 : ''; b2.classList.toggle('hidden', state.totalPages <= state.page); b1.className = 'px-3 py-1.5 rounded-lg bg-[#0b2545] text-white text-xs font-semibold transition';
    }
    function renderRows(rows) {
      const body = document.getElementById('transactionsTableBody'); state.rows = rows;
      if (!rows.length) { body.innerHTML = '<tr><td colspan="6" class="py-16 text-center text-sm text-gray-400">No transactions match these filters.</td></tr>'; body.style.visibility='visible'; return; }
      body.innerHTML = rows.map((r,i) => { const initials = (r.customer_name || '—').split(/\s+/).map(x=>x[0]).join('').slice(0,2).toUpperCase(); const instrument = r.card_last4 ? `Card ending in ${esc(r.card_last4)}` : channelLabel(r.channel); const amount=money(r.amount,r.currency), net=money(r.net,r.currency), fee=money(r.fee,r.currency); return `<tr class="hover:bg-blue-50/20 transition group transaction-row"><td class="py-5 px-6 whitespace-nowrap"><div class="flex items-center gap-3"><div class="w-9 h-9 rounded-full bg-[#0b2545] text-white font-bold flex items-center justify-center text-xs shrink-0">${esc(initials)}</div><div><div class="font-bold text-gray-900 group-hover:text-[#0b2545]">${esc(r.customer_name)}</div><div class="text-[11px] text-gray-400 font-mono">${esc(r.customer_email)}</div></div></div></td><td class="py-5 px-4 whitespace-nowrap"><div class="font-black text-gray-900">${esc(amount)}</div><div class="text-[11px] text-emerald-700 font-semibold mt-0.5">Net ${esc(net)}</div><div class="text-[10px] text-gray-400">Fee ${esc(fee)}</div></td><td class="py-5 px-4 whitespace-nowrap"><div class="font-mono font-semibold text-gray-700">${esc(r.reference)}</div><div class="text-[11px] text-gray-400 mt-1">${esc(channelLabel(r.channel))}${r.provider ? ` · ${esc(r.provider)}` : ''}</div></td><td class="py-5 px-4 whitespace-nowrap"><div class="text-gray-700 font-medium">${esc(dateLabel(r.created_at))}</div><div class="text-[10px] text-gray-400">${esc(r.currency)}</div></td><td class="py-5 px-4 text-center"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold ${r.status==='successful'?'bg-emerald-50 text-emerald-700 border border-emerald-200':r.status==='pending'?'bg-amber-50 text-amber-700 border border-amber-200':r.status==='refunded'?'bg-blue-50 text-blue-700 border border-blue-200':'bg-rose-50 text-rose-700 border border-rose-200'}">${esc(label(r.status))}</span></td><td class="py-5 px-5 text-right whitespace-nowrap"><button type="button" data-action="receipt" data-index="${i}" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View receipt">▣</button><button type="button" data-action="details" data-index="${i}" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View details">⋮</button></td></tr>`; }).join(''); body.style.visibility='visible';
      body.querySelectorAll('button[data-action]').forEach(btn => btn.addEventListener('click', () => { const r=state.rows[Number(btn.dataset.index)]; if (!r) return; const amount=money(r.amount,r.currency), net=money(r.net,r.currency), fee=money(r.fee,r.currency), auth=r.card_last4?`Card ending in ${r.card_last4}`:channelLabel(r.channel), when=dateLabel(r.created_at); if(btn.dataset.action==='receipt') openReceiptModal(r.reference,amount,r.customer_name,r.customer_email,auth,label(r.status),when,fee,net); else viewTransactionDetails(r.reference,amount,net,fee,r.customer_name,r.customer_email,'—',channelLabel(r.channel),auth,'',label(r.status),when, r.currency); }));
    }
    async function loadTransactions() { const body=document.getElementById('transactionsTableBody'); body.style.visibility='hidden'; try { const result=await api('list.php',filters()); const s=result.summary||{}; const c=state.rows[0]?.currency || (result.transactions[0]?.currency) || 'NGN'; document.getElementById('txGrossVolume').textContent=money(s.gross_volume,c); document.getElementById('txNetSettled').textContent=money(s.net_settled,c); document.getElementById('txFees').textContent=money(s.fees,c); document.getElementById('txSuccessfulCount').textContent=Number(s.successful_count||0).toLocaleString(); document.getElementById('txFailedCount').textContent=Number(s.failed_count||0).toLocaleString(); document.getElementById('txSuccessRate').textContent=`${Number(s.success_rate||0).toFixed(1)}%`; const total=Number(s.successful_count||0)+Number(s.failed_count||0); document.getElementById('txFailureRate').textContent=`${total ? ((Number(s.failed_count||0)/total)*100).toFixed(1) : '0.0'}%`; renderRows(result.transactions||[]); updatePagination(result.pagination||{}); const select=document.getElementById('currencyFilter'); if(select && result.currencies) { const selected=select.value; select.innerHTML='<option value="all">All Currencies</option>'+result.currencies.map(x=>`<option value="${esc(x.code)}">${esc(x.code)} (${esc(x.name)})</option>`).join(''); select.value=result.currencies.some(x=>x.code===selected)?selected:'all'; } } catch (error) { document.getElementById('transactionsTableBody').innerHTML=`<tr><td colspan="6" class="py-16 text-center text-sm text-rose-600">${esc(error.message)}</td></tr>`; document.getElementById('transactionsTableBody').style.visibility='visible'; } }
    window.changePage = (page) => { if (page<1 || page>state.totalPages) return; state.page=page; loadTransactions(); document.querySelector('table')?.scrollIntoView({behavior:'smooth',block:'nearest'}); };
    window.filterTransactions = () => { state.page=1; loadTransactions(); };
    let debounce; document.getElementById('transactionSearchInput')?.addEventListener('input',()=>{clearTimeout(debounce); debounce=setTimeout(()=>window.filterTransactions(),300);});
    ['dateRangeFilter','channelFilter','statusFilter','currencyFilter'].forEach(id=>document.getElementById(id)?.addEventListener('change',window.filterTransactions));
    document.querySelector('button[onclick*="Exporting full Transactions CSV"]')?.addEventListener('click',async()=>{ try { const response=await fetch('../api/transactions/export.php',{method:'POST',headers:{'Content-Type':'application/json',Authorization:`Bearer ${token()}`},body:JSON.stringify(filters())}); if(!response.ok) throw new Error('Export failed.'); const blob=await response.blob(); const url=URL.createObjectURL(blob), link=document.createElement('a'); link.href=url; link.download='flowwithlit-transactions.csv'; link.click(); URL.revokeObjectURL(url); } catch(error) { showToast(error.message); } });
    loadTransactions();
  })();
</script>

<?php
require_once __DIR__ . '/includes/bottom_nav.php';
require_once __DIR__ . '/includes/footer.php';
?>
