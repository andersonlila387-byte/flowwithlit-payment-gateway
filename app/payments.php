<?php
/**
 * Payments & Transfers Page - Flowwithlit
 * 
 * Multi-currency disbursements, cross-border payouts, on-chain crypto transfers,
 * and comprehensive 100% full-width ledger history.
 */

$pageTitle = "Payments & Transfers | Flowwithlit";
$activePage = 'payments';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<style>#paymentsTableBody { visibility:hidden; }</style>

    <!-- Scrollable Main Content Area -->
    <main class="flex-1 h-full overflow-y-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-8 flex flex-col gap-6 sm:gap-8 min-w-0">
      
      <!-- Top Page Header & Quick Action Buttons -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2.5">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Payments &amp; Transfers</h1>
          </div>
          <p class="text-xs sm:text-sm text-gray-500 mt-1">Disburse cross-border payouts, execute multi-currency transfers, and manage settlements.</p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap">
          <!-- Batch payouts have their own workflow page. -->
          <a href="batch-payouts.php" class="px-4 py-2.5 rounded-full bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-semibold shadow-2xs transition hover:scale-105 active:scale-95 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            <span>Batch Payouts</span>
          </a>

          <!-- Primary Action Button: Bank Transfer (Navy Blue) -->
          <a href="send-money.php" class="px-5 py-2.5 rounded-full bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-bold shadow-sm transition hover:scale-105 active:scale-95 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5M4.5 21V10.5M3 21h18" />
            </svg>
            <span>Transfer</span>
          </a>
        </div>
      </div>

      <!-- KPI Overview Cards (4 Metric Cards) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-5">
        
        <!-- Card 1: Total Payouts (30D) -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-gray-200/80 shadow-xs flex flex-col justify-between gap-4">
          <div>
            <div class="flex items-center justify-between">
              <span class="text-xs font-semibold text-gray-500">Disbursed (30 Days)</span>
              <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider bg-gray-50 border border-gray-100 px-2 py-0.5 rounded-full">NGN / crypto</span>
            </div>
            <div id="paymentsDisbursed" class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight mt-2.5">₦0.00</div>
            <div class="flex items-center gap-1.5 mt-2 text-xs font-semibold text-emerald-600">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
              </svg>
              <span>No prior-period data</span>
            </div>
          </div>

          <div class="pt-3 border-t border-gray-100">
            <div class="flex items-center justify-between text-[11px] text-gray-500 mb-1.5">
              <span>Monthly Volume Quota</span>
              <span class="font-bold text-gray-700">0%</span>
            </div>
            <!-- Light blue range bar as requested -->
            <div class="w-full h-1.5 rounded-full bg-gray-100 overflow-hidden">
              <div class="h-full bg-sky-400 rounded-full" style="width: 0%"></div>
            </div>
          </div>
        </div>

        <!-- Card 2: Settlement Speed -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-gray-200/80 shadow-xs flex flex-col justify-between gap-4">
          <div>
            <div class="flex items-center justify-between">
              <span class="text-xs font-semibold text-gray-500">Median Settlement</span>
              <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" title="Real-time Rails"></span>
            </div>
            <div id="paymentsMedianSettlement" class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight mt-2.5">—</div>
            <p class="text-xs text-gray-400 mt-2">Nigerian bank rails and enabled crypto networks</p>
          </div>

          <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500 font-medium">
            <span>Direct Clearing</span>
            <span class="font-semibold text-gray-700">0 Active Networks</span>
          </div>
        </div>

        <!-- Card 3: Pending & Scheduled -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-gray-200/80 shadow-xs flex flex-col justify-between gap-4">
          <div>
            <div class="flex items-center justify-between">
              <span class="text-xs font-semibold text-gray-500">Scheduled Payouts</span>
              <span class="text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">0 Queued</span>
            </div>
            <div id="paymentsPendingAmount" class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight mt-2.5">₦0.00</div>
            <p class="text-xs text-gray-400 mt-2">Scheduled for next batch window (02:00 UTC)</p>
          </div>

          <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
            <span class="text-gray-500 font-medium">Auto-release</span>
            <a href="#paymentsLedger" class="font-bold text-[#0b2545] hover:underline">View queue &rarr;</a>
          </div>
        </div>

        <!-- Card 4: Multi-Currency Rails -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-gray-200/80 shadow-xs flex flex-col justify-between gap-4">
          <div>
            <div class="flex items-center justify-between">
              <span class="text-xs font-semibold text-gray-500">Supported Rails</span>
              <span class="text-[10px] font-bold text-gray-600 bg-gray-100 px-2 py-0.5 rounded-full">0 Rails</span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight mt-2.5">Global Payouts</div>
            <p class="text-xs text-gray-400 mt-2">Enabled payment rails will appear here.</p>
          </div>

          <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500 font-medium">
            <span>Global Coverage</span>
            <span class="font-bold text-gray-700">No coverage data</span>
          </div>
        </div>

      </div>

      <!-- Segmented Rail Tabs & Search Controls -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 bg-gray-50/90 p-2 sm:p-2.5 rounded-2xl sm:rounded-[22px] border border-gray-200/70">
        
        <!-- Responsive Segmented Tabs (Equal on Mobile, Inline on Desktop) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 sm:flex sm:items-center p-1 bg-gray-200/60 rounded-xl sm:rounded-full gap-1 w-full sm:w-auto">
          <button class="payment-filter-btn flex items-center justify-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2 rounded-lg sm:rounded-full text-xs font-bold bg-[#0b2545] text-white shadow-xs transition" data-filter="all">
            <span>All</span>
            <span class="payment-filter-badge px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-white/20 text-white leading-none">0</span>
          </button>
          
          <button class="payment-filter-btn flex items-center justify-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2 rounded-lg sm:rounded-full text-xs font-semibold text-gray-600 hover:text-gray-900 transition" data-filter="bank">
            <span>Bank</span>
            <span class="hidden md:inline">Payouts</span>
            <span class="payment-filter-badge px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-300/60 text-gray-600 leading-none">0</span>
          </button>
          
          <button class="payment-filter-btn flex items-center justify-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2 rounded-lg sm:rounded-full text-xs font-semibold text-gray-600 hover:text-gray-900 transition" data-filter="crypto">
            <span>Crypto</span>
            <span class="hidden md:inline">Transfers</span>
            <span class="payment-filter-badge px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-300/60 text-gray-600 leading-none">0</span>
          </button>

          <button class="payment-filter-btn flex items-center justify-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2 rounded-lg sm:rounded-full text-xs font-semibold text-gray-600 hover:text-gray-900 transition" data-filter="internal">
            <span>Internal</span>
            <span class="payment-filter-badge px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-300/60 text-gray-600 leading-none">0</span>
          </button>
        </div>

        <!-- Live Search Input -->
        <div class="relative w-full sm:w-72">
          <input type="text" id="paymentSearchInput" placeholder="Search beneficiary, reference, account..." class="w-full bg-white border border-gray-200 rounded-xl sm:rounded-full pl-9 pr-4 py-2 text-xs text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#0b2545]/20 focus:border-[#0b2545] transition shadow-2xs">
          <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <circle cx="11" cy="11" r="7" />
            <path stroke-linecap="round" d="M21 21l-4.35-4.35" />
          </svg>
        </div>

      </div>

      <!-- 100% FULL WIDTH PAYMENTS & TRANSFERS LEDGER TABLE -->
      <!-- Following the principle: "The table should take the full 100% width. It should take its own full space. It should not bargain with anybody." -->
      <section id="paymentsLedger" class="w-full bg-white rounded-[28px] p-6 lg:p-7 shadow-sm border border-gray-200/80 flex flex-col gap-5">
        
        <!-- Table Top Header Controls -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-5">
          <div>
            <h2 class="text-lg lg:text-xl font-bold text-gray-900 tracking-tight" id="ledgerTableTitle">Payments &amp; Disbursements Ledger</h2>
            <p class="text-xs text-gray-400 mt-0.5">Comprehensive audit trail of external bank settlements, blockchain transfers &amp; wallet disbursements</p>
          </div>

          <!-- Table Filters & Export -->
          <div class="flex items-center gap-2.5 flex-wrap">
            <!-- Status Filter Dropdown -->
            <select id="paymentStatusFilter" class="bg-gray-50 border border-gray-200 text-xs font-semibold text-gray-700 rounded-full px-3.5 py-2 focus:outline-none focus:ring-2 focus:ring-[#0b2545]/20 cursor-pointer">
              <option value="all">All Statuses</option>
              <option value="paid">Paid</option>
              <option value="completed">Completed</option>
              <option value="processing">Processing</option>
              <option value="scheduled">Scheduled</option>
              <option value="failed">Failed</option>
            </select>

            <!-- Export CSV Button (White) -->
            <button class="px-4 py-2 rounded-full bg-white hover:bg-gray-50 border border-gray-200/80 text-gray-700 text-xs font-semibold flex items-center gap-1.5 shadow-2xs transition hover:scale-105 active:scale-95" onclick="alert('Exporting Payments Ledger CSV...')">
              <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
              </svg>
              <span>Export CSV</span>
            </button>
          </div>
        </div>

        <!-- 100% Full Width Table Container -->
        <div class="w-full overflow-x-auto">
          <table class="w-full text-left border-collapse min-w-[850px]">
            <thead>
              <tr class="border-b border-gray-100 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                <th class="py-3 px-4">Beneficiary / Destination</th>
                <th class="py-3 px-4">Reference &amp; Rail</th>
                <th class="py-3 px-4">Date &amp; Time</th>
                <th class="py-3 px-4">Source Wallet</th>
                <th class="py-3 px-4 text-right">Amount &amp; Fee</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4 text-right">Receipt</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100/80 text-xs" id="paymentsTableBody">
              
              <!-- Row 1: USD Fedwire to Stripe Supplier -->
              <tr class="hover:bg-gray-50/70 transition group" data-type="bank" data-status="paid">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <!-- USA Flag SVG -->
                    <span class="w-9 h-9 rounded-full overflow-hidden inline-flex items-center justify-center flex-shrink-0 shadow-2xs border border-gray-200">
                      <svg viewBox="0 0 640 480" class="w-full h-full object-cover">
                        <path fill="#bd3d44" d="M0 0h640v480H0z"/>
                        <path stroke="#fff" stroke-width="37" d="M0 55h640M0 129h640M0 203h640M0 277h640M0 351h640M0 425h640"/>
                        <path fill="#192f5d" d="M0 0h256v258H0z"/>
                        <circle cx="128" cy="129" r="18" fill="#fff"/>
                      </svg>
                    </span>
                    <div>
                      <div class="font-bold text-gray-900">Stripe Payments USA Inc</div>
                      <div class="text-[10px] text-gray-400 font-mono">Chase Bank &bull; Acct: 94028194</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="font-bold text-gray-900">ACH / Fedwire</div>
                  <div class="text-[10px] text-gray-400 font-mono">PAY-89201948</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">18 June 2026</div>
                  <div class="text-[10px] text-gray-400">09:42 AM</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">USD Balance Wallet</div>
                  <div class="text-[10px] text-gray-400">Primary Bank</div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-extrabold text-gray-900 text-sm">$45,000.00</div>
                  <div class="text-[10px] text-gray-400 font-normal">Fee: $0.25</div>
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <div class="relative inline-flex items-center justify-center group/status">
                    <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/80 flex items-center justify-center shadow-2xs transition group-hover/status:scale-110" aria-label="Status: Paid">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </span>
                    <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/status:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      Paid
                      <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="relative inline-flex items-center group/tip">
                    <button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-500 hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" onclick="showReceipt('PAY-89201948', 'Stripe Payments USA Inc', '$45,000.00 USD', 'ACH / Fedwire', 'Paid')" aria-label="View Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z" />
                      </svg>
                    </button>
                    <div class="absolute right-0 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      View Receipt
                      <div class="absolute top-full right-3 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
              </tr>

              <!-- Row 2: USDT Crypto Transfer -->
              <tr class="hover:bg-gray-50/70 transition group" data-type="crypto" data-status="completed">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <!-- USDT Logo -->
                    <div class="w-9 h-9 rounded-full bg-[#26a17b] text-white flex items-center justify-center font-bold text-xs shadow-2xs">
                      ₮
                    </div>
                    <div>
                      <div class="font-bold text-gray-900">Alpha Core Contractors</div>
                      <div class="text-[10px] text-gray-400 font-mono">TXr9aN8m...LeQvZ7xYq</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="font-bold text-gray-900">TRC-20 On-Chain</div>
                  <div class="text-[10px] text-gray-400 font-mono">PAY-74920183</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">17 June 2026</div>
                  <div class="text-[10px] text-gray-400">11:15 PM</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">USDT Vault</div>
                  <div class="text-[10px] text-gray-400">Tron Network</div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-extrabold text-gray-900 text-sm">28,500.00 USDT</div>
                  <div class="text-[10px] text-gray-400 font-normal">Fee: 1.00 USDT</div>
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <div class="relative inline-flex items-center justify-center group/status">
                    <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/80 flex items-center justify-center shadow-2xs transition group-hover/status:scale-110" aria-label="Status: Completed">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5m-5 0l-4.5 4.5" />
                      </svg>
                    </span>
                    <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/status:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      Completed
                      <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="relative inline-flex items-center group/tip">
                    <button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-500 hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" onclick="showReceipt('PAY-74920183', 'Alpha Core Contractors', '28,500.00 USDT', 'TRC-20 On-Chain', 'Completed')" aria-label="View Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z" />
                      </svg>
                    </button>
                    <div class="absolute right-0 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      View Receipt
                      <div class="absolute top-full right-3 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
              </tr>

              <!-- Row 3: NGN NIP Instant Payout -->
              <tr class="hover:bg-gray-50/70 transition group" data-type="bank" data-status="paid">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <!-- Nigeria Flag SVG -->
                    <span class="w-9 h-9 rounded-full overflow-hidden inline-flex items-center justify-center flex-shrink-0 shadow-2xs border border-gray-200">
                      <svg viewBox="0 0 640 480" class="w-full h-full object-cover">
                        <path fill="#008751" d="M0 0h213.3v480H0zm426.7 0H640v480H426.7z"/>
                        <path fill="#ffffff" d="M213.3 0h213.4v480H213.3z"/>
                      </svg>
                    </span>
                    <div>
                      <div class="font-bold text-gray-900">Emeka Chukwudi &amp; Sons Ltd</div>
                      <div class="text-[10px] text-gray-400 font-mono">Access Bank &bull; 0692819201</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="font-bold text-gray-900">NIBSS / NIP Instant</div>
                  <div class="text-[10px] text-gray-400 font-mono">PAY-61928401</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">17 June 2026</div>
                  <div class="text-[10px] text-gray-400">04:30 PM</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">NGN Main Account</div>
                  <div class="text-[10px] text-gray-400">Wema Virtual</div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-extrabold text-gray-900 text-sm">₦12,400,000.00</div>
                  <div class="text-[10px] text-gray-400 font-normal">Fee: ₦50.00</div>
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <div class="relative inline-flex items-center justify-center group/status">
                    <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/80 flex items-center justify-center shadow-2xs transition group-hover/status:scale-110" aria-label="Status: Paid">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </span>
                    <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/status:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      Paid
                      <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="relative inline-flex items-center group/tip">
                    <button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-500 hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" onclick="showReceipt('PAY-61928401', 'Emeka Chukwudi & Sons Ltd', '₦12,400,000.00 NGN', 'NIBSS / NIP Instant', 'Paid')" aria-label="View Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z" />
                      </svg>
                    </button>
                    <div class="absolute right-0 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      View Receipt
                      <div class="absolute top-full right-3 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
              </tr>

              <!-- Row 4: EUR SEPA Instant Payout (Failed) -->
              <tr class="hover:bg-gray-50/70 transition group" data-type="bank" data-status="failed">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <!-- EU Flag SVG -->
                    <span class="w-9 h-9 rounded-full overflow-hidden inline-flex items-center justify-center flex-shrink-0 shadow-2xs border border-gray-200">
                      <svg viewBox="0 0 640 480" class="w-full h-full object-cover">
                        <path fill="#003399" d="M0 0h640v480H0z"/>
                        <circle cx="320" cy="240" r="120" fill="none" stroke="#ffcc00" stroke-width="8" stroke-dasharray="1 28"/>
                      </svg>
                    </span>
                    <div>
                      <div class="font-bold text-gray-900">Klausner Logistics GmbH</div>
                      <div class="text-[10px] text-rose-500 font-mono">IBAN: DE89...3719 &bull; Invalid BIC</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="font-bold text-gray-900">SEPA Instant</div>
                  <div class="text-[10px] text-gray-400 font-mono">PAY-50192834</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">16 June 2026</div>
                  <div class="text-[10px] text-gray-400">01:10 PM</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">EUR Commercial</div>
                  <div class="text-[10px] text-gray-400">SEPA Wallet</div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-extrabold text-gray-900 text-sm">€18,650.00</div>
                  <div class="text-[10px] text-gray-400 font-normal">Fee: €0.00 (Refunded)</div>
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <div class="relative inline-flex items-center justify-center group/status">
                    <span class="w-8 h-8 rounded-full bg-rose-50 text-rose-600 border border-rose-200/80 flex items-center justify-center shadow-2xs transition group-hover/status:scale-110" aria-label="Status: Failed">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </span>
                    <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/status:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      Failed
                      <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="relative inline-flex items-center group/tip">
                    <button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-500 hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" onclick="showReceipt('PAY-50192834', 'Klausner Logistics GmbH', '€18,650.00 EUR', 'SEPA Instant', 'Failed')" aria-label="View Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z" />
                      </svg>
                    </button>
                    <div class="absolute right-0 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      View Receipt
                      <div class="absolute top-full right-3 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
              </tr>

              <!-- Row 5: Bitcoin Treasury Settlement -->
              <tr class="hover:bg-gray-50/70 transition group" data-type="crypto" data-status="processing">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <!-- BTC Logo -->
                    <div class="w-9 h-9 rounded-full bg-[#f7931a] text-white flex items-center justify-center font-bold text-xs shadow-2xs">
                      ₿
                    </div>
                    <div>
                      <div class="font-bold text-gray-900">BitGo Cold Custody</div>
                      <div class="text-[10px] text-gray-400 font-mono">bc1qar0s...5mdq</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="font-bold text-gray-900">Bitcoin SegWit</div>
                  <div class="text-[10px] text-gray-400 font-mono">PAY-41029482</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">16 June 2026</div>
                  <div class="text-[10px] text-gray-400">10:05 AM</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">BTC Vault</div>
                  <div class="text-[10px] text-gray-400">Native On-chain</div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-extrabold text-gray-900 text-sm">0.7500 BTC</div>
                  <div class="text-[10px] text-gray-400 font-normal">Fee: 0.0001 BTC</div>
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <div class="relative inline-flex items-center justify-center group/status">
                    <span class="w-8 h-8 rounded-full bg-amber-50 text-amber-600 border border-amber-200/80 flex items-center justify-center shadow-2xs transition group-hover/status:scale-110" aria-label="Status: Processing">
                      <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                      </svg>
                    </span>
                    <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/status:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      Processing (2/3)
                      <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="relative inline-flex items-center group/tip">
                    <button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-500 hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" onclick="showReceipt('PAY-41029482', 'BitGo Cold Custody', '0.7500 BTC', 'Bitcoin SegWit', 'Processing')" aria-label="View Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z" />
                      </svg>
                    </button>
                    <div class="absolute right-0 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      View Receipt
                      <div class="absolute top-full right-3 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
              </tr>

              <!-- Row 6: Solana Fast Settlement -->
              <tr class="hover:bg-gray-50/70 transition group" data-type="crypto" data-status="completed">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <!-- SOL Logo -->
                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-[#9945FF] to-[#14F195] text-white flex items-center justify-center font-bold text-xs shadow-2xs">
                      ◎
                    </div>
                    <div>
                      <div class="font-bold text-gray-900">Solana Foundation Validator</div>
                      <div class="text-[10px] text-gray-400 font-mono">7xKXtg...JosgAsU</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="font-bold text-gray-900">Solana SPL</div>
                  <div class="text-[10px] text-gray-400 font-mono">PAY-39102948</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">15 June 2026</div>
                  <div class="text-[10px] text-gray-400">06:22 PM</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">SOL Wallet</div>
                  <div class="text-[10px] text-gray-400">SPL Mainnet</div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-extrabold text-gray-900 text-sm">45.00 SOL</div>
                  <div class="text-[10px] text-gray-400 font-normal">Fee: 0.00005 SOL</div>
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <div class="relative inline-flex items-center justify-center group/status">
                    <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/80 flex items-center justify-center shadow-2xs transition group-hover/status:scale-110" aria-label="Status: Completed">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5m-5 0l-4.5 4.5" />
                      </svg>
                    </span>
                    <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/status:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      Completed
                      <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="relative inline-flex items-center group/tip">
                    <button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-500 hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" onclick="showReceipt('PAY-39102948', 'Solana Foundation Validator', '45.00 SOL', 'Solana SPL', 'Completed')" aria-label="View Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z" />
                      </svg>
                    </button>
                    <div class="absolute right-0 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      View Receipt
                      <div class="absolute top-full right-3 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
              </tr>

              <!-- Row 7: GBP Faster Payments Payout -->
              <tr class="hover:bg-gray-50/70 transition group" data-type="bank" data-status="paid">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <!-- UK Flag SVG -->
                    <span class="w-9 h-9 rounded-full overflow-hidden inline-flex items-center justify-center flex-shrink-0 shadow-2xs border border-gray-200">
                      <svg viewBox="0 0 640 480" class="w-full h-full object-cover">
                        <path fill="#012169" d="M0 0h640v480H0z"/>
                        <path fill="#fff" d="M0 0l640 480M640 0L0 480" stroke="#fff" stroke-width="50"/>
                        <path fill="#c8102e" d="M0 0l640 480M640 0L0 480" stroke="#c8102e" stroke-width="20"/>
                        <path fill="#fff" d="M280 0h80v480H280zM0 200h640v80H0z"/>
                        <path fill="#c8102e" d="M300 0h40v480H300zM0 220h640v40H0z"/>
                      </svg>
                    </span>
                    <div>
                      <div class="font-bold text-gray-900">London Creative Works LLP</div>
                      <div class="text-[10px] text-gray-400 font-mono">Barclays &bull; 04-00-04 / 84920194</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="font-bold text-gray-900">Faster Payments (FPS)</div>
                  <div class="text-[10px] text-gray-400 font-mono">PAY-28401928</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">14 June 2026</div>
                  <div class="text-[10px] text-gray-400">03:40 PM</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">GBP Wallet</div>
                  <div class="text-[10px] text-gray-400">UK Business</div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-extrabold text-gray-900 text-sm">£9,420.00</div>
                  <div class="text-[10px] text-gray-400 font-normal">Fee: £0.20</div>
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <div class="relative inline-flex items-center justify-center group/status">
                    <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/80 flex items-center justify-center shadow-2xs transition group-hover/status:scale-110" aria-label="Status: Paid">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </span>
                    <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/status:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      Paid
                      <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="relative inline-flex items-center group/tip">
                    <button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-500 hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" onclick="showReceipt('PAY-28401928', 'London Creative Works LLP', '£9,420.00 GBP', 'Faster Payments (FPS)', 'Paid')" aria-label="View Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z" />
                      </svg>
                    </button>
                    <div class="absolute right-0 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      View Receipt
                      <div class="absolute top-full right-3 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
              </tr>

              <!-- Row 8: CAD Interac Payout -->
              <tr class="hover:bg-gray-50/70 transition group" data-type="bank" data-status="scheduled">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <!-- Canada Flag SVG -->
                    <span class="w-9 h-9 rounded-full overflow-hidden inline-flex items-center justify-center flex-shrink-0 shadow-2xs border border-gray-200">
                      <svg viewBox="0 0 640 480" class="w-full h-full object-cover">
                        <path fill="#d80027" d="M0 0h160v480H0zm480 0h160v480H480z"/>
                        <path fill="#f0f0f0" d="M160 0h320v480H160z"/>
                        <circle cx="320" cy="240" r="50" fill="#d80027"/>
                      </svg>
                    </span>
                    <div>
                      <div class="font-bold text-gray-900">Toronto Dev Studio Inc</div>
                      <div class="text-[10px] text-gray-400 font-mono">Transit: 12049 &bull; RBC Royal</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="font-bold text-gray-900">Bank transfer</div>
                  <div class="text-[10px] text-gray-400 font-mono">PAY-19284019</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">Tomorrow (Scheduled)</div>
                  <div class="text-[10px] text-gray-400">02:00 UTC</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">CAD Balance</div>
                  <div class="text-[10px] text-gray-400">Commercial CAD</div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-extrabold text-gray-900 text-sm">CA$14,250.00</div>
                  <div class="text-[10px] text-gray-400 font-normal">Fee: CA$0.50</div>
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <div class="relative inline-flex items-center justify-center group/status">
                    <span class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 border border-gray-200 flex items-center justify-center shadow-2xs transition group-hover/status:scale-110" aria-label="Status: Scheduled">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </span>
                    <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/status:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      Scheduled
                      <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="relative inline-flex items-center group/tip">
                    <button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-500 hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" onclick="showReceipt('PAY-19284019', 'Toronto Dev Studio Inc', 'CA$14,250.00 CAD', 'Bank transfer', 'Scheduled')" aria-label="View Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z" />
                      </svg>
                    </button>
                    <div class="absolute right-0 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      View Receipt
                      <div class="absolute top-full right-3 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
              </tr>

              <!-- Row 9: ETH Smart Contract Milestone -->
              <tr class="hover:bg-gray-50/70 transition group" data-type="crypto" data-status="completed">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <!-- ETH Logo -->
                    <div class="w-9 h-9 rounded-full bg-[#627eea] text-white flex items-center justify-center font-bold text-xs shadow-2xs">
                      Ξ
                    </div>
                    <div>
                      <div class="font-bold text-gray-900">OpenZeppelin Audit Lab</div>
                      <div class="text-[10px] text-gray-400 font-mono">0x71C...3298</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="font-bold text-gray-900">Ethereum Mainnet</div>
                  <div class="text-[10px] text-gray-400 font-mono">PAY-09283719</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">13 June 2026</div>
                  <div class="text-[10px] text-gray-400">08:50 AM</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">ETH Treasury</div>
                  <div class="text-[10px] text-gray-400">Smart Contract</div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-extrabold text-gray-900 text-sm">3.5000 ETH</div>
                  <div class="text-[10px] text-gray-400 font-normal">Fee: 0.0018 ETH</div>
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <div class="relative inline-flex items-center justify-center group/status">
                    <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/80 flex items-center justify-center shadow-2xs transition group-hover/status:scale-110" aria-label="Status: Completed">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5m-5 0l-4.5 4.5" />
                      </svg>
                    </span>
                    <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/status:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      Completed
                      <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="relative inline-flex items-center group/tip">
                    <button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-500 hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" onclick="showReceipt('PAY-09283719', 'OpenZeppelin Audit Lab', '3.5000 ETH', 'Ethereum Mainnet', 'Completed')" aria-label="View Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z" />
                      </svg>
                    </button>
                    <div class="absolute right-0 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      View Receipt
                      <div class="absolute top-full right-3 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
              </tr>

              <!-- Row 10: Internal Transfer to User -->
              <tr class="hover:bg-gray-50/70 transition group" data-type="internal" data-status="paid">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <!-- Flowwithlit User Avatar -->
                    <div class="w-9 h-9 rounded-full bg-[#0b2545] text-white flex items-center justify-center font-bold text-xs shadow-2xs">
                      ML
                    </div>
                    <div>
                      <div class="font-bold text-gray-900">Marcus Lit (@marcus_lit)</div>
                      <div class="text-[10px] text-gray-400">Flowwithlit Internal ID: USR-8401</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="font-bold text-gray-900">Internal P2P Settlement</div>
                  <div class="text-[10px] text-gray-400 font-mono">PAY-01928341</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">12 June 2026</div>
                  <div class="text-[10px] text-gray-400">02:15 PM</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">USD Balance Wallet</div>
                  <div class="text-[10px] text-gray-400">Primary Bank</div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-extrabold text-gray-900 text-sm">$2,500.00</div>
                  <div class="text-[10px] text-emerald-600 font-semibold">Zero Fee ($0.00)</div>
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <div class="relative inline-flex items-center justify-center group/status">
                    <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/80 flex items-center justify-center shadow-2xs transition group-hover/status:scale-110" aria-label="Status: Paid">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </span>
                    <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/status:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      Paid
                      <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="relative inline-flex items-center group/tip">
                    <button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-500 hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" onclick="showReceipt('PAY-01928341', 'Marcus Lit (@marcus_lit)', '$2,500.00 USD', 'Internal P2P Settlement', 'Paid')" aria-label="View Receipt">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z" />
                      </svg>
                    </button>
                    <div class="absolute right-0 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      View Receipt
                      <div class="absolute top-full right-3 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
              </tr>

            </tbody>
          </table>
        </div>

        <!-- Table Footer / Pagination Controls -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-gray-100 text-xs text-gray-500">
          <span>Showing 1 to 10 of 184 payments</span>
          
          <div class="flex items-center gap-1.5">
            <button class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 disabled:opacity-40" disabled>Previous</button>
            <button class="px-3 py-1.5 rounded-lg bg-[#0b2545] text-white font-bold">1</button>
            <button class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700">2</button>
            <button class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700">3</button>
            <span class="px-1 text-gray-400">...</span>
            <button class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700">19</button>
            <button class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700">Next</button>
          </div>
        </div>

      </section>

    </main>

  <!-- ====================================================================== -->
  <!-- MODAL 1: BANK SELECTION TRANSFER MODAL (Primary Bank Modal)              -->
  <!-- ====================================================================== -->
  <div id="transferModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full h-full sm:h-auto sm:max-h-[92vh] sm:max-w-lg sm:rounded-[28px] shadow-2xl border-0 sm:border sm:border-gray-200 flex flex-col overflow-hidden transition-all duration-300">
      
      <!-- Top Sticky App Bar -->
      <div class="flex items-center justify-between px-5 sm:px-7 py-3.5 border-b border-gray-100 bg-white flex-shrink-0">
        <div class="flex items-center gap-3">
          <button id="transferBackBtn" class="hidden w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition active:scale-95" aria-label="Go Back">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
          </button>
          <div>
            <h3 id="transferModalTitle" class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">Bank Transfer</h3>
            <p id="transferModalSubtitle" class="text-xs text-gray-400">Step 1 of 4 &bull; Bank &amp; Account Lookup</p>
          </div>
        </div>
        <button id="closeTransferModalBtn" class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition active:scale-95" aria-label="Close Modal">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <div class="px-5 sm:px-7 py-2 bg-blue-50/50 border-b border-blue-100/70 text-xs text-gray-600 font-medium flex-shrink-0">Nigeria bank transfer • NGN settlement</div>

      <!-- 4-Step Progress Indicator -->
      <div id="transferStepProgress" class="flex items-center gap-1.5 px-5 sm:px-7 py-2 bg-gray-50/70 border-b border-gray-100 flex-shrink-0">
        <div id="stepProgress1" class="flex-1 h-1.5 rounded-full bg-[#0b2545] transition-all duration-300"></div>
        <div id="stepProgress2" class="flex-1 h-1.5 rounded-full bg-gray-200 transition-all duration-300"></div>
        <div id="stepProgress3" class="flex-1 h-1.5 rounded-full bg-gray-200 transition-all duration-300"></div>
        <div id="stepProgress4" class="flex-1 h-1.5 rounded-full bg-gray-200 transition-all duration-300"></div>
      </div>

      <!-- Scrollable Content Body -->
      <div class="flex-1 overflow-y-auto px-5 sm:px-7 py-5 flex flex-col justify-between">

        <!-- PHASE 1: BANK SELECTION & ACCOUNT LOOKUP -->
        <div id="stepRecipientContainer" class="flex flex-col gap-4">
          <div class="flex flex-col gap-3">
            <div>
              <div class="flex items-center justify-between mb-1">
                <label class="block text-xs font-bold text-gray-700">Account Number</label>
                <span class="text-[10px] text-gray-400">Enter the 10-digit NUBAN first</span>
              </div>
              <div class="relative">
                <input type="text" id="bankAccountNumberInput" value="" placeholder="Enter Nigerian account number" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-800 font-mono tracking-wider focus:outline-none focus:ring-2 focus:ring-[#0b2545]/20 focus:border-[#0b2545]">
                <span id="accountNumberStatus" class="absolute right-3 top-2.5 text-[10px] font-bold text-gray-400">10 digits</span>
              </div>
              <p class="mt-1 text-[10px] text-gray-400">After the account number is complete, select its Nigerian bank so OnePipe can perform the account-name lookup.</p>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Destination Bank</label>
              <select id="bankSelect" disabled class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-800 font-semibold focus:outline-none focus:ring-2 focus:ring-[#0b2545]/20 focus:border-[#0b2545] disabled:opacity-60 disabled:cursor-not-allowed">
                <option value="">Enter a 10-digit account number first</option>
              </select>
              <p class="mt-1 text-[10px] text-gray-400">The account number alone cannot identify a Nigerian bank reliably; lookup requires the bank code too.</p>
            </div>
          </div>

          <!-- Live Account Verification Card -->
          <div id="accountVerificationCard" class="p-3.5 rounded-2xl bg-gray-50 border border-gray-200 transition-all">
            <!-- Loading indicator -->
              <div id="verificationLoading" class="hidden flex items-center gap-2.5 py-1 text-xs text-gray-500">
              <svg class="w-4 h-4 animate-spin text-[#0b2545]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
              <span>Verifying beneficiary details with clearing switch...</span>
            </div>

            <!-- Verified Result Details -->
            <div id="verificationResult" class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center font-bold text-sm flex-shrink-0">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                </div>
                <div>
                  <div class="flex items-center gap-1.5">
                    <span id="verifiedRecipientName" class="font-extrabold text-sm text-gray-900">Account verification pending</span>
                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-amber-100/70 text-amber-800">
                      Pending lookup
                    </span>
                  </div>
                  <div id="verifiedRecipientDetails" class="text-xs text-gray-500 font-medium">Enter an account number and select Verify.</div>
                </div>
              </div>
              <span id="verifiedRailTag" class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-gray-200 text-gray-700">NIP lookup</span>
            </div>
          </div>

          <!-- Bottom Action Button for Step 1 -->
          <div class="pt-2">
            <button type="button" id="step1NextBtn" disabled class="w-full py-3 rounded-full bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-bold transition text-center shadow-md active:scale-95 flex items-center justify-center gap-2 opacity-50 cursor-not-allowed">
              <span>Next: Set Amount</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </button>
          </div>
        </div>

        <!-- PHASE 2: AMOUNT & BALANCE VERIFICATION -->
        <div id="stepAmountContainer" class="hidden flex flex-col gap-4">
          <!-- Recipient Compact Bar -->
          <div class="p-3 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
              <div class="w-7 h-7 rounded-full bg-[#0b2545] text-white font-bold text-xs flex items-center justify-center">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5M4.5 21V10.5M3 21h18"/></svg>
              </div>
              <div>
                <div class="text-xs font-bold text-gray-900" id="step2RecipientDisplay">Sarah Jenkins</div>
              <div class="text-[10px] text-gray-400" id="step2RailDisplay">Nigerian bank transfer</div>
              </div>
            </div>
            <button type="button" id="step2ChangeRecipientBtn" class="text-xs font-bold text-[#0b2545] hover:underline">Edit</button>
          </div>

          <!-- Source Wallet Selector -->
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-gray-700">Debit From Wallet</label>
              <span class="text-[11px] font-bold text-gray-500" id="selectedWalletBalanceDisplay">Avail: $248,650.00</span>
            </div>
            <select id="sourceWalletSelect" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-900 font-semibold focus:outline-none focus:ring-2 focus:ring-[#0b2545]/20 focus:border-[#0b2545]">
              <option value="">Loading NGN and crypto wallets...</option>
            </select>
          </div>

          <!-- Amount Input Box -->
          <div class="bg-gray-50 p-4 rounded-2xl border border-gray-200 flex flex-col gap-3">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-gray-700">Enter Transfer Amount</span>
              <div class="flex items-center gap-1">
                <button type="button" class="px-2 py-0.5 rounded-md bg-white border border-gray-200 text-[10px] font-semibold text-gray-600 hover:bg-gray-100 transition" onclick="setTransferAmount(100)">+100</button>
                <button type="button" class="px-2 py-0.5 rounded-md bg-white border border-gray-200 text-[10px] font-semibold text-gray-600 hover:bg-gray-100 transition" onclick="setTransferAmount(500)">+500</button>
                <button type="button" class="px-2 py-0.5 rounded-md bg-white border border-gray-200 text-[10px] font-semibold text-gray-600 hover:bg-gray-100 transition" onclick="setTransferAmount(2500)">+2.5K</button>
                <button type="button" class="px-2 py-0.5 rounded-md bg-[#0b2545] text-[10px] font-bold text-white transition hover:bg-[#132847]" onclick="setMaxTransferAmount()">Max</button>
              </div>
            </div>

            <div class="relative flex items-center">
              <span id="amountCurrencySymbol" class="absolute left-3.5 text-gray-500 font-extrabold text-xl">$</span>
              <input type="number" id="transferAmountInput" step="any" placeholder="0.00" value="1250.00" class="w-full bg-white border border-gray-200 rounded-xl pl-9 pr-4 py-2.5 text-xl font-black text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0b2545]/20 focus:border-[#0b2545]">
            </div>

            <!-- Real-time Balance Check Feedback -->
            <div id="balanceCheckBanner" class="p-2.5 rounded-xl border transition-all text-xs">
              <!-- Dynamically populated by JS: Insufficient or Verified -->
            </div>

            <!-- Fee & Clearance Breakdown -->
            <div class="flex flex-col gap-1.5 pt-2 border-t border-gray-200/70 text-[11px] text-gray-500">
              <div class="flex items-center justify-between">
                <span>Transfer Rail Fee</span>
                <span id="step2RailFeeText" class="font-semibold text-gray-700">$0.25 USD</span>
              </div>
              <div class="flex items-center justify-between">
                <span>Total Debit from Wallet</span>
                <span id="step2TotalDebitText" class="font-bold text-gray-900">$1,250.25 USD</span>
              </div>
              <div class="flex items-center justify-between">
                <span>Estimated Clearance</span>
                <span id="step2DeliveryText" class="font-semibold text-emerald-600">Immediate (&lt; 2 seconds)</span>
              </div>
            </div>
          </div>

          <!-- Bottom Action Buttons for Step 2 -->
          <div class="flex items-center gap-3 pt-2">
            <button type="button" id="step2BackBtn" class="flex-1 py-3 rounded-full bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-semibold transition text-center active:scale-95">
              Back
            </button>
            <button type="button" id="step2NextBtn" class="flex-1 py-3 rounded-full bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-bold transition text-center shadow-md active:scale-95 flex items-center justify-center gap-2">
              <span>Next: Transfer Code</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </button>
          </div>
        </div>

        <!-- PHASE 3: TRANSFER CODE (4-DIGIT PIN) -->
        <div id="stepPinContainer" class="hidden flex flex-col items-center text-center gap-4">
          <div class="px-4 py-2 rounded-full bg-gray-50 border border-gray-200 text-xs text-gray-700">
            Transferring <span id="pinModalAmount" class="font-extrabold text-gray-900">$1,250.00</span> to <span id="pinModalRecipient" class="font-bold text-gray-900">Sarah Jenkins</span>
          </div>

          <div>
            <h4 class="text-lg font-bold text-gray-900">Enter Transfer Code</h4>
            <p class="text-xs text-gray-400 mt-0.5">Input your 4-digit security PIN to authorize this transfer</p>
          </div>

          <!-- 4-Digit PIN Masked Boxes -->
          <div class="flex items-center justify-center gap-3 my-1">
            <div class="bank-pin-box w-12 h-14 rounded-2xl border-2 border-gray-200 flex items-center justify-center text-2xl font-bold text-gray-900 bg-gray-50/50" data-index="0"></div>
            <div class="bank-pin-box w-12 h-14 rounded-2xl border-2 border-gray-200 flex items-center justify-center text-2xl font-bold text-gray-900 bg-gray-50/50" data-index="1"></div>
            <div class="bank-pin-box w-12 h-14 rounded-2xl border-2 border-gray-200 flex items-center justify-center text-2xl font-bold text-gray-900 bg-gray-50/50" data-index="2"></div>
            <div class="bank-pin-box w-12 h-14 rounded-2xl border-2 border-gray-200 flex items-center justify-center text-2xl font-bold text-gray-900 bg-gray-50/50" data-index="3"></div>
          </div>

          <!-- Mobile Keypad -->
          <div class="grid grid-cols-3 gap-2 w-full max-w-[280px]">
            <button type="button" class="bank-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="1">1</button>
            <button type="button" class="bank-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="2">2</button>
            <button type="button" class="bank-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="3">3</button>
            <button type="button" class="bank-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="4">4</button>
            <button type="button" class="bank-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="5">5</button>
            <button type="button" class="bank-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="6">6</button>
            <button type="button" class="bank-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="7">7</button>
            <button type="button" class="bank-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="8">8</button>
            <button type="button" class="bank-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="9">9</button>
            <button type="button" class="bank-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-400 font-semibold text-xs transition active:scale-95 flex items-center justify-center" data-val="clear">Clear</button>
            <button type="button" class="bank-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="0">0</button>
            <button type="button" class="bank-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="backspace">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9.75L14.25 12m0 0l2.25 2.25M14.25 12l2.25-2.25M14.25 12L12 14.25m-2.58 4.92l-6.375-6.375a1.125 1.125 0 010-1.59L9.42 4.83c.21-.21.49-.33.78-.33H19.5A2.25 2.25 0 0121.75 6.75v10.5A2.25 2.25 0 0119.5 19.5H10.2c-.29 0-.57-.12-.78-.33z"/></svg>
            </button>
          </div>

          <!-- Bottom Action Buttons for Step 3 -->
          <div class="flex items-center gap-3 w-full pt-1">
            <button type="button" id="step3BackBtn" class="flex-1 py-3 rounded-full bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-semibold transition text-center active:scale-95">
              Back
            </button>
            <button type="button" id="step3SubmitBtn" class="flex-1 py-3 rounded-full bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-bold transition text-center shadow-md active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
              Next: Authenticator
            </button>
          </div>
        </div>

        <!-- PHASE 4: 2FA AUTHENTICATOR CODE -->
        <div id="step2faContainer" class="hidden flex flex-col items-center text-center gap-4">
          <div class="w-12 h-12 rounded-full bg-blue-50 text-[#0b2545] border border-blue-200/60 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
            </svg>
          </div>

          <div>
            <h4 class="text-lg font-bold text-gray-900">Two-Factor Authenticator</h4>
            <p class="text-xs text-gray-400 mt-0.5">Enter the 6-digit code from Google Authenticator or your 2FA app</p>
          </div>

          <!-- 6-Digit Authenticator Boxes -->
          <div class="flex items-center justify-center gap-2 my-1">
            <div class="bank-2fa-box w-10 h-13 sm:w-11 sm:h-14 rounded-2xl border-2 border-gray-200 flex items-center justify-center text-xl font-bold text-gray-900 bg-gray-50/50" data-index="0"></div>
            <div class="bank-2fa-box w-10 h-13 sm:w-11 sm:h-14 rounded-2xl border-2 border-gray-200 flex items-center justify-center text-xl font-bold text-gray-900 bg-gray-50/50" data-index="1"></div>
            <div class="bank-2fa-box w-10 h-13 sm:w-11 sm:h-14 rounded-2xl border-2 border-gray-200 flex items-center justify-center text-xl font-bold text-gray-900 bg-gray-50/50" data-index="2"></div>
            <div class="bank-2fa-box w-10 h-13 sm:w-11 sm:h-14 rounded-2xl border-2 border-gray-200 flex items-center justify-center text-xl font-bold text-gray-900 bg-gray-50/50" data-index="3"></div>
            <div class="bank-2fa-box w-10 h-13 sm:w-11 sm:h-14 rounded-2xl border-2 border-gray-200 flex items-center justify-center text-xl font-bold text-gray-900 bg-gray-50/50" data-index="4"></div>
            <div class="bank-2fa-box w-10 h-13 sm:w-11 sm:h-14 rounded-2xl border-2 border-gray-200 flex items-center justify-center text-xl font-bold text-gray-900 bg-gray-50/50" data-index="5"></div>
          </div>

          <!-- Mobile Keypad for 2FA -->
          <div class="grid grid-cols-3 gap-2 w-full max-w-[280px]">
            <button type="button" class="bank-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="1">1</button>
            <button type="button" class="bank-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="2">2</button>
            <button type="button" class="bank-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="3">3</button>
            <button type="button" class="bank-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="4">4</button>
            <button type="button" class="bank-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="5">5</button>
            <button type="button" class="bank-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="6">6</button>
            <button type="button" class="bank-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="7">7</button>
            <button type="button" class="bank-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="8">8</button>
            <button type="button" class="bank-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="9">9</button>
            <button type="button" class="bank-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-400 font-semibold text-xs transition active:scale-95 flex items-center justify-center" data-val="clear">Clear</button>
            <button type="button" class="bank-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="0">0</button>
            <button type="button" class="bank-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="backspace">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9.75L14.25 12m0 0l2.25 2.25M14.25 12l2.25-2.25M14.25 12L12 14.25m-2.58 4.92l-6.375-6.375a1.125 1.125 0 010-1.59L9.42 4.83c.21-.21.49-.33.78-.33H19.5A2.25 2.25 0 0121.75 6.75v10.5A2.25 2.25 0 0119.5 19.5H10.2c-.29 0-.57-.12-.78-.33z"/></svg>
            </button>
          </div>

          <!-- Bottom Action Buttons for Step 4 -->
          <div class="flex items-center gap-3 w-full pt-1">
            <button type="button" id="step4BackBtn" class="flex-1 py-3 rounded-full bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-semibold transition text-center active:scale-95">
              Back
            </button>
            <button type="button" id="step4SubmitBtn" class="flex-1 py-3 rounded-full bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-bold transition text-center shadow-md active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
              Authorize &amp; Disburse
            </button>
          </div>
        </div>

        <!-- PHASE 5: ROLLING CONCURRENCY PROCESS -->
        <div id="stepRollingContainer" class="hidden flex flex-col items-center justify-center text-center py-8 gap-5">
          <!-- Rolling Spinner Animation -->
          <div class="relative w-20 h-20 flex items-center justify-center">
            <svg class="w-20 h-20 animate-spin text-[#0b2545]" viewBox="0 0 100 100" fill="none">
              <circle cx="50" cy="50" r="42" stroke="#e5e7eb" stroke-width="8"/>
              <path d="M50 8 A 42 42 0 0 1 92 50" stroke="#0b2545" stroke-width="8" stroke-linecap="round"/>
            </svg>
            <div class="absolute inset-0 flex items-center justify-center">
              <svg class="w-7 h-7 text-[#0b2545]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
          </div>

          <div>
            <h4 class="text-lg font-bold text-gray-900">Processing Bank Transfer...</h4>
            <p class="text-xs text-gray-400 mt-0.5">Please wait while your disbursement is verified and cleared</p>
          </div>

          <!-- Real-Time Check Ticks -->
          <div class="w-full max-w-sm bg-gray-50 rounded-2xl p-4 border border-gray-200/80 flex flex-col gap-2.5 text-xs text-left">
            <div id="checkTick1" class="flex items-center justify-between text-gray-700">
              <span class="flex items-center gap-2">
                <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold">✓</span>
                <span>Checking concurrency lock &amp; active session</span>
              </span>
              <span class="text-[10px] font-bold text-emerald-600">Passed</span>
            </div>
            <div id="checkTick2" class="flex items-center justify-between text-gray-700">
              <span class="flex items-center gap-2">
                <span id="tickIcon2" class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold">✓</span>
                <span id="tickText2">Deducting balance from wallet</span>
              </span>
              <span id="tickStatus2" class="text-[10px] font-bold text-emerald-600">Debited</span>
            </div>
            <div id="checkTick3" class="flex items-center justify-between text-gray-700">
              <span class="flex items-center gap-2">
                <span id="tickIcon3" class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold">✓</span>
                <span id="tickText3">Routing via bank clearing rail</span>
              </span>
              <span id="tickStatus3" class="text-[10px] font-bold text-emerald-600">Settled</span>
            </div>
          </div>
        </div>

        <!-- PHASE 6: COMPLETED SCREEN WITH GREEN CIRCLE -->
        <div id="stepSuccessContainer" class="hidden flex flex-col items-center text-center py-4 gap-4">
          <!-- Big Green Circle with Completed Checkmark Icon -->
          <div class="w-20 h-20 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center shadow-sm mx-auto">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
          </div>

          <div>
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60 mb-1.5">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
              Transfer Completed
            </span>
            <h4 class="text-2xl font-black text-gray-900 tracking-tight" id="successAmountDisplay">$1,250.00 USD</h4>
            <p class="text-xs text-gray-500 mt-0.5" id="successSubtitleDisplay">Disbursed to Sarah Jenkins via Chase Bank</p>
          </div>

          <!-- Transaction Summary Card -->
          <div class="w-full bg-gray-50 rounded-2xl p-4 flex flex-col gap-2.5 text-xs border border-gray-100 text-left">
            <div class="flex items-center justify-between">
              <span class="text-gray-400">Recipient Name</span>
              <span id="successBeneficiaryName" class="font-bold text-gray-900">Sarah Jenkins</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-gray-400">Destination Bank</span>
              <span id="successRailName" class="font-semibold text-gray-700">Chase Bank &bull; Acct #0218492019</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-gray-400">Reference ID</span>
              <div class="flex items-center gap-1.5 font-mono font-bold text-gray-900">
                <span id="successRefId">PAY-98201948</span>
                <button type="button" class="text-gray-400 hover:text-gray-700" onclick="navigator.clipboard.writeText(document.getElementById('successRefId').textContent); alert('Reference copied!');" title="Copy Reference">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </button>
              </div>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-gray-400">Status</span>
              <span class="font-bold text-emerald-600 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                Completed
              </span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-gray-400">Timestamp</span>
              <span class="font-medium text-gray-700" id="successTimestamp">18 Jun 2026, 12:25 PM</span>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-3 w-full pt-1">
            <button type="button" id="successViewReceiptBtn" class="flex-1 py-3 rounded-full bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-semibold transition text-center active:scale-95 flex items-center justify-center gap-1.5">
              <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
              <span>View Receipt</span>
            </button>
            <button type="button" id="successDoneBtn" class="flex-1 py-3 rounded-full bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-bold transition text-center shadow-md active:scale-95">
              Done
            </button>
          </div>
        </div>

      </div>

    </div>
  </div>

  <!-- ====================================================================== -->
  <!-- MODAL 2: SEPARATE E-TRANSFER MODAL (Email, PayPal, Cash App)             -->
  <!-- ====================================================================== -->
  <div id="etransferModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full h-full sm:h-auto sm:max-h-[92vh] sm:max-w-lg sm:rounded-[28px] shadow-2xl border-0 sm:border sm:border-gray-200 flex flex-col overflow-hidden transition-all duration-300">
      
      <!-- Top Sticky App Bar -->
      <div class="flex items-center justify-between px-5 sm:px-7 py-3.5 border-b border-gray-100 bg-white flex-shrink-0">
        <div class="flex items-center gap-3">
          <button id="etransferBackBtn" class="hidden w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition active:scale-95" aria-label="Go Back">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
          </button>
          <div>
            <h3 id="etransferModalTitle" class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">e-Transfer &amp; Payout</h3>
            <p id="etransferModalSubtitle" class="text-xs text-gray-400">Step 1 of 4 &bull; Recipient Details</p>
          </div>
        </div>
        <button id="closeEtransferModalBtn" class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition active:scale-95" aria-label="Close Modal">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <!-- Quick Switch Banner to Bank Transfer -->
      <div class="px-5 sm:px-7 py-2 bg-blue-50/50 border-b border-blue-100/70 flex items-center justify-between text-xs flex-shrink-0">
        <span class="text-gray-600 font-medium">Need to send a direct bank wire instead?</span>
        <button type="button" id="switchToBankTransferBtn" class="font-bold text-[#0b2545] hover:underline flex items-center gap-1 transition">
          <span>Switch to Bank Transfer</span>
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
        </button>
      </div>

      <!-- 4-Step Progress Indicator -->
      <div id="etransferStepProgress" class="flex items-center gap-1.5 px-5 sm:px-7 py-2 bg-gray-50/70 border-b border-gray-100 flex-shrink-0">
        <div id="etransferProgress1" class="flex-1 h-1.5 rounded-full bg-[#0b2545] transition-all duration-300"></div>
        <div id="etransferProgress2" class="flex-1 h-1.5 rounded-full bg-gray-200 transition-all duration-300"></div>
        <div id="etransferProgress3" class="flex-1 h-1.5 rounded-full bg-gray-200 transition-all duration-300"></div>
        <div id="etransferProgress4" class="flex-1 h-1.5 rounded-full bg-gray-200 transition-all duration-300"></div>
      </div>

      <!-- Scrollable Content Body -->
      <div class="flex-1 overflow-y-auto px-5 sm:px-7 py-5 flex flex-col justify-between">

        <!-- PHASE 1: E-TRANSFER CHANNEL & LOOKUP -->
        <div id="etransferStepRecipientContainer" class="flex flex-col gap-4">
          
          <!-- e-Transfer Channel Selector Tabs -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Select e-Transfer Rail</label>
            <div class="grid grid-cols-3 gap-2">
              <!-- Direct Email -->
              <button type="button" class="etransfer-channel-btn active px-3 py-2.5 rounded-xl border border-[#0b2545] bg-[#0b2545] text-white text-xs font-bold flex items-center justify-center gap-1.5 transition active:scale-95 shadow-xs" data-channel="email">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                <span>Email</span>
              </button>
              <!-- PayPal -->
              <button type="button" class="etransfer-channel-btn px-3 py-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold flex items-center justify-center gap-1.5 transition active:scale-95" data-channel="paypal">
                <span class="font-black text-[#0b2545]">P</span>
                <span>PayPal</span>
              </button>
              <!-- Cash App -->
              <button type="button" class="etransfer-channel-btn px-3 py-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold flex items-center justify-center gap-1.5 transition active:scale-95" data-channel="cashapp">
                <span class="font-black text-[#0b2545]">$</span>
                <span>Cash App</span>
              </button>
            </div>
          </div>

          <!-- CHANNEL 1: DIRECT EMAIL / INTERAC -->
          <div id="etransferChannelEmail" class="etransfer-channel-block flex flex-col gap-3">
            <div>
              <div class="flex items-center justify-between mb-1">
                <label class="block text-xs font-bold text-gray-700">Recipient Email Address</label>
                <span class="text-[10px] text-gray-400">Interac / Direct Payout</span>
              </div>
              <div class="relative">
                <input type="email" id="etransferEmailInput" value="david.miller@acmecorp.ca" placeholder="name@domain.com" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-800 font-medium focus:outline-none focus:ring-2 focus:ring-[#0b2545]/20 focus:border-[#0b2545]">
                <button type="button" id="etransferEmailVerifyBtn" class="absolute right-2 top-2 px-2.5 py-1 rounded-lg bg-gray-200 hover:bg-gray-300 text-[10px] font-bold text-gray-700 transition active:scale-95">Verify</button>
              </div>
            </div>
          </div>

          <!-- CHANNEL 2: PAYPAL WITH RECEIVING BANK -->
          <div id="etransferChannelPaypal" class="etransfer-channel-block hidden flex flex-col gap-3">
            <div>
              <div class="flex items-center justify-between mb-1">
                <label class="block text-xs font-bold text-gray-700">Recipient PayPal Email or @Handle</label>
                <span class="text-[10px] text-emerald-600 font-semibold">Instant Resolution</span>
              </div>
              <div class="relative">
                <input type="text" id="etransferPaypalInput" value="@sarahj_tech" placeholder="@username or sarah@domain.com" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-800 font-medium focus:outline-none focus:ring-2 focus:ring-[#0b2545]/20 focus:border-[#0b2545]">
                <button type="button" id="etransferPaypalVerifyBtn" class="absolute right-2 top-2 px-2.5 py-1 rounded-lg bg-gray-200 hover:bg-gray-300 text-[10px] font-bold text-gray-700 transition active:scale-95">Verify</button>
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Select Receiving Partner Bank</label>
              <select id="etransferPaypalBankSelect" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-800 font-semibold focus:outline-none focus:ring-2 focus:ring-[#0b2545]/20 focus:border-[#0b2545]">
                <option value="JPMorgan Chase (US)">JPMorgan Chase (Linked PayPal Account)</option>
                <option value="Wells Fargo (US)">Wells Fargo (Linked Account)</option>
                <option value="Bank of America (US)">Bank of America (Linked Account)</option>
                <option value="PayPal Balance">PayPal Wallet Stored Balance</option>
              </select>
            </div>
          </div>

          <!-- CHANNEL 3: CASH APP WITH RECEIVING BANK -->
          <div id="etransferChannelCashapp" class="etransfer-channel-block hidden flex flex-col gap-3">
            <div>
              <div class="flex items-center justify-between mb-1">
                <label class="block text-xs font-bold text-gray-700">Recipient Cashtag</label>
                <span class="text-[10px] text-emerald-600 font-semibold">Zero Fee Payout</span>
              </div>
              <div class="relative">
                <span class="absolute left-3.5 top-2.5 text-gray-400 font-bold text-xs">$</span>
                <input type="text" id="etransferCashappInput" value="alex_chukwu" placeholder="cashtag" class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-7 pr-16 py-2.5 text-xs text-gray-800 font-semibold focus:outline-none focus:ring-2 focus:ring-[#0b2545]/20 focus:border-[#0b2545]">
                <button type="button" id="etransferCashappVerifyBtn" class="absolute right-2 top-2 px-2.5 py-1 rounded-lg bg-gray-200 hover:bg-gray-300 text-[10px] font-bold text-gray-700 transition active:scale-95">Verify</button>
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Select Receiving Partner Bank</label>
              <select id="etransferCashappBankSelect" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-800 font-semibold focus:outline-none focus:ring-2 focus:ring-[#0b2545]/20 focus:border-[#0b2545]">
                <option value="Sutton Bank (FDIC)">Sutton Bank (Cash App Card &amp; Direct Deposit)</option>
                <option value="Lincoln Savings Bank">Lincoln Savings Bank (Direct Deposit Partner)</option>
                <option value="Cash App Balance">Cash App Stored Balance</option>
              </select>
            </div>
          </div>

          <!-- LIVE E-TRANSFER ACCOUNT VERIFICATION CARD -->
          <div id="etransferAccountVerificationCard" class="p-3.5 rounded-2xl bg-gray-50 border border-gray-200 transition-all">
            <!-- Loading indicator -->
            <div id="etransferVerificationLoading" class="hidden flex items-center gap-2.5 py-1 text-xs text-gray-500">
              <svg class="w-4 h-4 animate-spin text-[#0b2545]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
              <span>Verifying e-Transfer profile &amp; clearing switch...</span>
            </div>

            <!-- Verified Result Details -->
            <div id="etransferVerificationResult" class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center font-bold text-sm flex-shrink-0">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                </div>
                <div>
                  <div class="flex items-center gap-1.5">
                    <span id="etransferVerifiedName" class="font-extrabold text-sm text-gray-900">David Miller</span>
                    <span id="etransferVerifiedBadge" class="inline-flex items-center gap-0.5 px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-emerald-100/70 text-emerald-800">
                      ✓ Verified
                    </span>
                  </div>
                  <div id="etransferVerifiedDetails" class="text-xs text-gray-500 font-medium">david.miller@acmecorp.ca &bull; Auto-Deposit</div>
                </div>
              </div>
              <span id="etransferVerifiedTag" class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-gray-200 text-gray-700">Interac</span>
            </div>
          </div>

          <!-- Bottom Action Button for Step 1 -->
          <div class="pt-2">
            <button type="button" id="etransferStep1NextBtn" class="w-full py-3 rounded-full bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-bold transition text-center shadow-md active:scale-95 flex items-center justify-center gap-2">
              <span>Next: Set Amount</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </button>
          </div>

        </div>

        <!-- PHASE 2: AMOUNT & LIVE BALANCE VERIFICATION -->
        <div id="etransferStepAmountContainer" class="hidden flex flex-col gap-4">
          <!-- Recipient Compact Bar -->
          <div class="p-3 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
              <div class="w-7 h-7 rounded-full bg-[#0b2545] text-white font-bold text-xs flex items-center justify-center">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
              </div>
              <div>
                <div class="text-xs font-bold text-gray-900" id="etransferStep2RecipientDisplay">David Miller</div>
                <div class="text-[10px] text-gray-400" id="etransferStep2DetailsDisplay">david.miller@acmecorp.ca</div>
              </div>
            </div>
            <button type="button" id="etransferStep2ChangeRecipientBtn" class="text-xs font-bold text-[#0b2545] hover:underline">Edit</button>
          </div>

          <!-- Source Wallet Selector -->
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-gray-700">Debit From Wallet</label>
              <span class="text-[11px] font-bold text-gray-500" id="etransferSelectedWalletBalanceDisplay">Avail: $248,650.00</span>
            </div>
            <select id="etransferSourceWalletSelect" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-900 font-semibold focus:outline-none focus:ring-2 focus:ring-[#0b2545]/20 focus:border-[#0b2545]">
              <option value="USD" data-currency="$" data-balance="248650.00" selected>US Dollar (USD) &bull; Balance: $248,650.00</option>
              <option value="CAD" data-currency="CA$" data-balance="48920.00">Canadian Dollar (CAD) &bull; Balance: CA$48,920.00</option>
              <option value="NGN" data-currency="₦" data-balance="192450800.00">Nigerian Naira (NGN) &bull; Balance: ₦192,450,800.00</option>
              <option value="EUR" data-currency="€" data-balance="54210.20">Euro (EUR) &bull; Balance: €54,210.20</option>
              <option value="GBP" data-currency="£" data-balance="31840.15">British Pound (GBP) &bull; Balance: £31,840.15</option>
              <option value="USDT" data-currency="USDT" data-balance="124500.00">Tether USD (USDT) &bull; Balance: 124,500.00 USDT</option>
            </select>
          </div>

          <!-- Amount Input Box -->
          <div class="bg-gray-50 p-4 rounded-2xl border border-gray-200 flex flex-col gap-3">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-gray-700">Enter Transfer Amount</span>
              <div class="flex items-center gap-1">
                <button type="button" class="px-2 py-0.5 rounded-md bg-white border border-gray-200 text-[10px] font-semibold text-gray-600 hover:bg-gray-100 transition" onclick="setEtransferAmount(100)">+100</button>
                <button type="button" class="px-2 py-0.5 rounded-md bg-white border border-gray-200 text-[10px] font-semibold text-gray-600 hover:bg-gray-100 transition" onclick="setEtransferAmount(500)">+500</button>
                <button type="button" class="px-2 py-0.5 rounded-md bg-white border border-gray-200 text-[10px] font-semibold text-gray-600 hover:bg-gray-100 transition" onclick="setEtransferAmount(2500)">+2.5K</button>
                <button type="button" class="px-2 py-0.5 rounded-md bg-[#0b2545] text-[10px] font-bold text-white transition hover:bg-[#132847]" onclick="setMaxEtransferAmount()">Max</button>
              </div>
            </div>

            <div class="relative flex items-center">
              <span id="etransferAmountCurrencySymbol" class="absolute left-3.5 text-gray-500 font-extrabold text-xl">$</span>
              <input type="number" id="etransferAmountInput" step="any" placeholder="0.00" value="750.00" class="w-full bg-white border border-gray-200 rounded-xl pl-9 pr-4 py-2.5 text-xl font-black text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0b2545]/20 focus:border-[#0b2545]">
            </div>

            <!-- Real-time Balance Check Feedback -->
            <div id="etransferBalanceCheckBanner" class="p-2.5 rounded-xl border transition-all text-xs">
              <!-- Dynamically populated by JS -->
            </div>

            <!-- Fee & Clearance Breakdown -->
            <div class="flex flex-col gap-1.5 pt-2 border-t border-gray-200/70 text-[11px] text-gray-500">
              <div class="flex items-center justify-between">
                <span>Transfer Rail Fee</span>
                <span id="etransferFeeText" class="font-semibold text-gray-700">Zero Fee ($0.00)</span>
              </div>
              <div class="flex items-center justify-between">
                <span>Total Debit from Wallet</span>
                <span id="etransferTotalDebitText" class="font-bold text-gray-900">$750.00 USD</span>
              </div>
              <div class="flex items-center justify-between">
                <span>Estimated Clearance</span>
                <span id="etransferDeliveryText" class="font-semibold text-emerald-600">Immediate (&lt; 2 seconds)</span>
              </div>
            </div>
          </div>

          <!-- Bottom Action Buttons for Step 2 -->
          <div class="flex items-center gap-3 pt-2">
            <button type="button" id="etransferStep2BackBtn" class="flex-1 py-3 rounded-full bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-semibold transition text-center active:scale-95">
              Back
            </button>
            <button type="button" id="etransferStep2NextBtn" class="flex-1 py-3 rounded-full bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-bold transition text-center shadow-md active:scale-95 flex items-center justify-center gap-2">
              <span>Next: Transfer Code</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </button>
          </div>
        </div>

        <!-- PHASE 3: TRANSFER CODE (4-DIGIT PIN) -->
        <div id="etransferStepPinContainer" class="hidden flex flex-col items-center text-center gap-4">
          <div class="px-4 py-2 rounded-full bg-gray-50 border border-gray-200 text-xs text-gray-700">
            Transferring <span id="etransferPinModalAmount" class="font-extrabold text-gray-900">$750.00</span> to <span id="etransferPinModalRecipient" class="font-bold text-gray-900">David Miller</span>
          </div>

          <div>
            <h4 class="text-lg font-bold text-gray-900">Enter Transfer Code</h4>
            <p class="text-xs text-gray-400 mt-0.5">Input your 4-digit security PIN to authorize this e-Transfer</p>
          </div>

          <!-- 4-Digit PIN Masked Boxes -->
          <div class="flex items-center justify-center gap-3 my-1">
            <div class="etransfer-pin-box w-12 h-14 rounded-2xl border-2 border-gray-200 flex items-center justify-center text-2xl font-bold text-gray-900 bg-gray-50/50" data-index="0"></div>
            <div class="etransfer-pin-box w-12 h-14 rounded-2xl border-2 border-gray-200 flex items-center justify-center text-2xl font-bold text-gray-900 bg-gray-50/50" data-index="1"></div>
            <div class="etransfer-pin-box w-12 h-14 rounded-2xl border-2 border-gray-200 flex items-center justify-center text-2xl font-bold text-gray-900 bg-gray-50/50" data-index="2"></div>
            <div class="etransfer-pin-box w-12 h-14 rounded-2xl border-2 border-gray-200 flex items-center justify-center text-2xl font-bold text-gray-900 bg-gray-50/50" data-index="3"></div>
          </div>

          <!-- Mobile Keypad -->
          <div class="grid grid-cols-3 gap-2 w-full max-w-[280px]">
            <button type="button" class="etransfer-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="1">1</button>
            <button type="button" class="etransfer-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="2">2</button>
            <button type="button" class="etransfer-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="3">3</button>
            <button type="button" class="etransfer-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="4">4</button>
            <button type="button" class="etransfer-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="5">5</button>
            <button type="button" class="etransfer-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="6">6</button>
            <button type="button" class="etransfer-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="7">7</button>
            <button type="button" class="etransfer-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="8">8</button>
            <button type="button" class="etransfer-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="9">9</button>
            <button type="button" class="etransfer-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-400 font-semibold text-xs transition active:scale-95 flex items-center justify-center" data-val="clear">Clear</button>
            <button type="button" class="etransfer-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="0">0</button>
            <button type="button" class="etransfer-pin-key h-12 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="backspace">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9.75L14.25 12m0 0l2.25 2.25M14.25 12l2.25-2.25M14.25 12L12 14.25m-2.58 4.92l-6.375-6.375a1.125 1.125 0 010-1.59L9.42 4.83c.21-.21.49-.33.78-.33H19.5A2.25 2.25 0 0121.75 6.75v10.5A2.25 2.25 0 0119.5 19.5H10.2c-.29 0-.57-.12-.78-.33z"/></svg>
            </button>
          </div>

          <!-- Bottom Action Buttons for Step 3 -->
          <div class="flex items-center gap-3 w-full pt-1">
            <button type="button" id="etransferStep3BackBtn" class="flex-1 py-3 rounded-full bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-semibold transition text-center active:scale-95">
              Back
            </button>
            <button type="button" id="etransferStep3SubmitBtn" class="flex-1 py-3 rounded-full bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-bold transition text-center shadow-md active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
              Next: Authenticator
            </button>
          </div>
        </div>

        <!-- PHASE 4: 2FA AUTHENTICATOR CODE -->
        <div id="etransferStep2faContainer" class="hidden flex flex-col items-center text-center gap-4">
          <div class="w-12 h-12 rounded-full bg-blue-50 text-[#0b2545] border border-blue-200/60 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
            </svg>
          </div>

          <div>
            <h4 class="text-lg font-bold text-gray-900">Two-Factor Authenticator</h4>
            <p class="text-xs text-gray-400 mt-0.5">Enter the 6-digit verification code from your authenticator app</p>
          </div>

          <!-- 6-Digit Authenticator Boxes -->
          <div class="flex items-center justify-center gap-2 my-1">
            <div class="etransfer-2fa-box w-10 h-13 sm:w-11 sm:h-14 rounded-2xl border-2 border-gray-200 flex items-center justify-center text-xl font-bold text-gray-900 bg-gray-50/50" data-index="0"></div>
            <div class="etransfer-2fa-box w-10 h-13 sm:w-11 sm:h-14 rounded-2xl border-2 border-gray-200 flex items-center justify-center text-xl font-bold text-gray-900 bg-gray-50/50" data-index="1"></div>
            <div class="etransfer-2fa-box w-10 h-13 sm:w-11 sm:h-14 rounded-2xl border-2 border-gray-200 flex items-center justify-center text-xl font-bold text-gray-900 bg-gray-50/50" data-index="2"></div>
            <div class="etransfer-2fa-box w-10 h-13 sm:w-11 sm:h-14 rounded-2xl border-2 border-gray-200 flex items-center justify-center text-xl font-bold text-gray-900 bg-gray-50/50" data-index="3"></div>
            <div class="etransfer-2fa-box w-10 h-13 sm:w-11 sm:h-14 rounded-2xl border-2 border-gray-200 flex items-center justify-center text-xl font-bold text-gray-900 bg-gray-50/50" data-index="4"></div>
            <div class="etransfer-2fa-box w-10 h-13 sm:w-11 sm:h-14 rounded-2xl border-2 border-gray-200 flex items-center justify-center text-xl font-bold text-gray-900 bg-gray-50/50" data-index="5"></div>
          </div>

          <!-- Mobile Keypad for 2FA -->
          <div class="grid grid-cols-3 gap-2 w-full max-w-[280px]">
            <button type="button" class="etransfer-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="1">1</button>
            <button type="button" class="etransfer-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="2">2</button>
            <button type="button" class="etransfer-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="3">3</button>
            <button type="button" class="etransfer-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="4">4</button>
            <button type="button" class="etransfer-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="5">5</button>
            <button type="button" class="etransfer-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="6">6</button>
            <button type="button" class="etransfer-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="7">7</button>
            <button type="button" class="etransfer-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="8">8</button>
            <button type="button" class="etransfer-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="9">9</button>
            <button type="button" class="etransfer-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-400 font-semibold text-xs transition active:scale-95 flex items-center justify-center" data-val="clear">Clear</button>
            <button type="button" class="etransfer-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-800 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="0">0</button>
            <button type="button" class="etransfer-2fa-key h-11 rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold text-base transition active:scale-95 flex items-center justify-center" data-val="backspace">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9.75L14.25 12m0 0l2.25 2.25M14.25 12l2.25-2.25M14.25 12L12 14.25m-2.58 4.92l-6.375-6.375a1.125 1.125 0 010-1.59L9.42 4.83c.21-.21.49-.33.78-.33H19.5A2.25 2.25 0 0121.75 6.75v10.5A2.25 2.25 0 0119.5 19.5H10.2c-.29 0-.57-.12-.78-.33z"/></svg>
            </button>
          </div>

          <!-- Bottom Action Buttons for Step 4 -->
          <div class="flex items-center gap-3 w-full pt-1">
            <button type="button" id="etransferStep4BackBtn" class="flex-1 py-3 rounded-full bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-semibold transition text-center active:scale-95">
              Back
            </button>
            <button type="button" id="etransferStep4SubmitBtn" class="flex-1 py-3 rounded-full bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-bold transition text-center shadow-md active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
              Authorize &amp; Disburse
            </button>
          </div>
        </div>

        <!-- PHASE 5: ROLLING CONCURRENCY PROCESS -->
        <div id="etransferStepRollingContainer" class="hidden flex flex-col items-center justify-center text-center py-8 gap-5">
          <!-- Rolling Spinner Animation -->
          <div class="relative w-20 h-20 flex items-center justify-center">
            <svg class="w-20 h-20 animate-spin text-[#0b2545]" viewBox="0 0 100 100" fill="none">
              <circle cx="50" cy="50" r="42" stroke="#e5e7eb" stroke-width="8"/>
              <path d="M50 8 A 42 42 0 0 1 92 50" stroke="#0b2545" stroke-width="8" stroke-linecap="round"/>
            </svg>
            <div class="absolute inset-0 flex items-center justify-center">
              <svg class="w-7 h-7 text-[#0b2545]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
            </div>
          </div>

          <div>
            <h4 class="text-lg font-bold text-gray-900">Processing e-Transfer...</h4>
            <p class="text-xs text-gray-400 mt-0.5">Please wait while your e-Transfer payout is verified and cleared</p>
          </div>

          <!-- Real-Time Check Ticks -->
          <div class="w-full max-w-sm bg-gray-50 rounded-2xl p-4 border border-gray-200/80 flex flex-col gap-2.5 text-xs text-left">
            <div id="etransferCheckTick1" class="flex items-center justify-between text-gray-700">
              <span class="flex items-center gap-2">
                <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold">✓</span>
                <span>Validating e-Transfer channel &amp; session lock</span>
              </span>
              <span class="text-[10px] font-bold text-emerald-600">Passed</span>
            </div>
            <div id="etransferCheckTick2" class="flex items-center justify-between text-gray-700">
              <span class="flex items-center gap-2">
                <span id="etransferTickIcon2" class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold">✓</span>
                <span id="etransferTickText2">Deducting balance from wallet</span>
              </span>
              <span id="etransferTickStatus2" class="text-[10px] font-bold text-emerald-600">Debited</span>
            </div>
            <div id="etransferCheckTick3" class="flex items-center justify-between text-gray-700">
              <span class="flex items-center gap-2">
                <span id="etransferTickIcon3" class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold">✓</span>
                <span id="etransferTickText3">Routing via partner bank clearing switch</span>
              </span>
              <span id="etransferTickStatus3" class="text-[10px] font-bold text-emerald-600">Settled</span>
            </div>
          </div>
        </div>

        <!-- PHASE 6: COMPLETED SCREEN WITH GREEN CIRCLE -->
        <div id="etransferStepSuccessContainer" class="hidden flex flex-col items-center text-center py-4 gap-4">
          <!-- Big Green Circle with Completed Checkmark Icon -->
          <div class="w-20 h-20 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center shadow-sm mx-auto">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
          </div>

          <div>
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60 mb-1.5">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
              e-Transfer Completed
            </span>
            <h4 class="text-2xl font-black text-gray-900 tracking-tight" id="etransferSuccessAmountDisplay">$750.00 USD</h4>
            <p class="text-xs text-gray-500 mt-0.5" id="etransferSuccessSubtitleDisplay">Disbursed to David Miller via Interac</p>
          </div>

          <!-- Transaction Summary Card -->
          <div class="w-full bg-gray-50 rounded-2xl p-4 flex flex-col gap-2.5 text-xs border border-gray-100 text-left">
            <div class="flex items-center justify-between">
              <span class="text-gray-400">Recipient Name</span>
              <span id="etransferSuccessBeneficiaryName" class="font-bold text-gray-900">David Miller</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-gray-400">Channel / Partner Bank</span>
              <span id="etransferSuccessChannelName" class="font-semibold text-gray-700">Interac &bull; david.miller@acmecorp.ca</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-gray-400">Reference ID</span>
              <div class="flex items-center gap-1.5 font-mono font-bold text-gray-900">
                <span id="etransferSuccessRefId">ET-82019482</span>
                <button type="button" class="text-gray-400 hover:text-gray-700" onclick="navigator.clipboard.writeText(document.getElementById('etransferSuccessRefId').textContent); alert('Reference copied!');" title="Copy Reference">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </button>
              </div>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-gray-400">Status</span>
              <span class="font-bold text-emerald-600 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                Completed
              </span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-gray-400">Timestamp</span>
              <span class="font-medium text-gray-700" id="etransferSuccessTimestamp">18 Jun 2026, 12:28 PM</span>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-3 w-full pt-1">
            <button type="button" id="etransferSuccessViewReceiptBtn" class="flex-1 py-3 rounded-full bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-semibold transition text-center active:scale-95 flex items-center justify-center gap-1.5">
              <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
              <span>View Receipt</span>
            </button>
            <button type="button" id="etransferSuccessDoneBtn" class="flex-1 py-3 rounded-full bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-bold transition text-center shadow-md active:scale-95">
              Done
            </button>
          </div>
        </div>

      </div>

    </div>
  </div>


  <!-- INTERACTIVE MODAL 2: BATCH CSV PAYOUT MODAL -->
  <div id="batchModal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-3xl p-6 sm:p-7 shadow-xl border border-gray-200 flex flex-col gap-5">
      <div class="flex items-center justify-between border-b border-gray-100 pb-4">
        <div>
          <h3 class="text-lg font-bold text-gray-900">Batch Payout via CSV</h3>
          <p class="text-xs text-gray-400 mt-0.5">Disburse multiple payments in a single upload</p>
        </div>
        <button id="closeBatchModalBtn" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <div class="border-2 border-dashed border-gray-200 hover:border-[#0b2545] rounded-2xl p-6 flex flex-col items-center justify-center text-center gap-2 cursor-pointer transition bg-gray-50/50 hover:bg-gray-50">
        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
        </svg>
        <span class="text-xs font-bold text-gray-700">Click to upload or drag and drop</span>
        <span class="text-[10px] text-gray-400">CSV file with beneficiary details, amount, currency &amp; account</span>
      </div>

      <div class="flex items-center justify-between text-xs pt-1">
        <span class="text-gray-500">Need the layout template?</span>
        <button class="font-bold text-[#0b2545] hover:underline" onclick="alert('Downloading sample_batch_payout.csv template')">Download Sample CSV</button>
      </div>

      <div class="flex items-center gap-3 pt-2">
        <button type="button" id="cancelBatchBtn" class="flex-1 py-2.5 rounded-full bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-semibold transition text-center">
          Cancel
        </button>
        <button type="button" class="flex-1 py-2.5 rounded-full bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-bold transition text-center shadow-sm" onclick="alert('Please select a CSV file first.');">
          Validate &amp; Process
        </button>
      </div>
    </div>
  </div>

  <!-- INTERACTIVE MODAL 3: RECEIPT VIEW MODAL -->
  <div id="receiptModal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-sm rounded-3xl p-6 shadow-xl border border-gray-200 flex flex-col gap-4">
      <div class="flex items-center justify-between border-b border-gray-100 pb-3">
        <h4 class="text-base font-bold text-gray-900">Transfer Receipt</h4>
        <button id="closeReceiptBtn" class="w-7 h-7 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <div class="flex flex-col items-center text-center py-2">
        <div id="receiptStatusIconContainer" class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mb-2">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
        </div>
        <div id="receiptAmount" class="text-2xl font-extrabold text-gray-900 tracking-tight">$0.00</div>
        <span id="receiptStatus" class="inline-flex items-center gap-1 mt-1 text-[11px] font-bold text-emerald-700">Completed</span>
      </div>

      <div class="bg-gray-50 rounded-2xl p-4 flex flex-col gap-2.5 text-xs">
        <div class="flex items-center justify-between">
          <span class="text-gray-400">Beneficiary</span>
          <span id="receiptBeneficiary" class="font-bold text-gray-900">--</span>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-gray-400">Reference ID</span>
          <span id="receiptRef" class="font-mono font-bold text-gray-800">--</span>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-gray-400">Payment Rail</span>
          <span id="receiptRail" class="font-semibold text-gray-700">--</span>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-gray-400">Timestamp</span>
          <span class="font-medium text-gray-700">18 June 2026, 11:20 AM</span>
        </div>
      </div>

      <div class="flex items-center gap-2 pt-2">
        <button class="flex-1 py-2 rounded-full bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-bold transition text-center shadow-xs" onclick="window.print()">
          Print Receipt
        </button>
      </div>
    </div>
  </div>

  <!-- Page Logic: Filter Tabs, Live Search, Rail Switcher & Modals -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // =====================================================================
      // SHARED DATA: WALLET BALANCES & FORMATTERS
      // =====================================================================
      const walletBalances = {
        USD: 248650.00,
        NGN: 192450800.00,
        EUR: 54210.20,
        GBP: 31840.15,
        CAD: 48920.00,
        USDT: 124500.00,
        BTC: 1.4582,
        ETH: 12.845,
        SOL: 114.50
      };

      const walletSymbols = {
        USD: '$',
        NGN: '₦',
        EUR: '€',
        GBP: '£',
        CAD: 'CA$',
        USDT: 'USDT ',
        BTC: '₿',
        ETH: 'Ξ',
        SOL: 'SOL '
      };

      // =====================================================================
      // CONTROLLER 1: BANK SELECTION TRANSFER MODAL (Primary Bank Modal)
      // =====================================================================
      const transferModal = document.getElementById('transferModal');
      const openTransferModalBtn = document.getElementById('openTransferModalBtn');
      const closeTransferModalBtn = document.getElementById('closeTransferModalBtn');
      const transferBackBtn = document.getElementById('transferBackBtn');
      const transferModalTitle = document.getElementById('transferModalTitle');
      const transferModalSubtitle = document.getElementById('transferModalSubtitle');
      const transferStepProgress = document.getElementById('transferStepProgress');
      const switchToEtransferBtn = document.getElementById('switchToEtransferBtn');

      // Steps Containers
      const stepRecipientContainer = document.getElementById('stepRecipientContainer');
      const stepAmountContainer = document.getElementById('stepAmountContainer');
      const stepPinContainer = document.getElementById('stepPinContainer');
      const step2faContainer = document.getElementById('step2faContainer');
      const stepRollingContainer = document.getElementById('stepRollingContainer');
      const stepSuccessContainer = document.getElementById('stepSuccessContainer');

      // Progress bars
      const stepProgress1 = document.getElementById('stepProgress1');
      const stepProgress2 = document.getElementById('stepProgress2');
      const stepProgress3 = document.getElementById('stepProgress3');
      const stepProgress4 = document.getElementById('stepProgress4');

      // Bank Step 1 elements
      const bankSelect = document.getElementById('bankSelect');
      const bankAccountNumberInput = document.getElementById('bankAccountNumberInput');
      const accountNumberStatus = document.getElementById('accountNumberStatus');
      const bankVerifyTriggerBtn = document.getElementById('bankVerifyTriggerBtn');
      const verificationLoading = document.getElementById('verificationLoading');
      const verificationResult = document.getElementById('verificationResult');
      const verifiedRecipientName = document.getElementById('verifiedRecipientName');
      const verifiedRecipientDetails = document.getElementById('verifiedRecipientDetails');
      const verifiedRailTag = document.getElementById('verifiedRailTag');
      const step1NextBtn = document.getElementById('step1NextBtn');

      // Bank Step 2 elements
      const step2RecipientDisplay = document.getElementById('step2RecipientDisplay');
      const step2RailDisplay = document.getElementById('step2RailDisplay');
      const step2ChangeRecipientBtn = document.getElementById('step2ChangeRecipientBtn');
      const sourceWalletSelect = document.getElementById('sourceWalletSelect');
      const selectedWalletBalanceDisplay = document.getElementById('selectedWalletBalanceDisplay');
      const transferAmountInput = document.getElementById('transferAmountInput');
      const amountCurrencySymbol = document.getElementById('amountCurrencySymbol');
      const balanceCheckBanner = document.getElementById('balanceCheckBanner');
      const step2RailFeeText = document.getElementById('step2RailFeeText');
      const step2TotalDebitText = document.getElementById('step2TotalDebitText');
      const step2BackBtn = document.getElementById('step2BackBtn');
      const step2NextBtn = document.getElementById('step2NextBtn');

      // Bank Step 3 elements (PIN)
      const pinModalAmount = document.getElementById('pinModalAmount');
      const pinModalRecipient = document.getElementById('pinModalRecipient');
      const bankPinBoxes = document.querySelectorAll('.bank-pin-box');
      const bankPinKeys = document.querySelectorAll('.bank-pin-key');
      const step3BackBtn = document.getElementById('step3BackBtn');
      const step3SubmitBtn = document.getElementById('step3SubmitBtn');

      // Bank Step 4 elements (2FA Authenticator)
      const bank2faBoxes = document.querySelectorAll('.bank-2fa-box');
      const bank2faKeys = document.querySelectorAll('.bank-2fa-key');
      const step4BackBtn = document.getElementById('step4BackBtn');
      const step4SubmitBtn = document.getElementById('step4SubmitBtn');

      // Bank Step 5 elements (Rolling Process)
      const tickIcon2 = document.getElementById('tickIcon2');
      const tickText2 = document.getElementById('tickText2');
      const tickStatus2 = document.getElementById('tickStatus2');
      const tickIcon3 = document.getElementById('tickIcon3');
      const tickText3 = document.getElementById('tickText3');
      const tickStatus3 = document.getElementById('tickStatus3');

      // Bank Step 6 elements (Completed)
      const successAmountDisplay = document.getElementById('successAmountDisplay');
      const successSubtitleDisplay = document.getElementById('successSubtitleDisplay');
      const successBeneficiaryName = document.getElementById('successBeneficiaryName');
      const successRailName = document.getElementById('successRailName');
      const successRefId = document.getElementById('successRefId');
      const successTimestamp = document.getElementById('successTimestamp');
      const successViewReceiptBtn = document.getElementById('successViewReceiptBtn');
      const successDoneBtn = document.getElementById('successDoneBtn');

      // State variables for Bank Transfer
      let bankCurrentStep = 1;
      let bankVerifiedBeneficiary = {
        name: 'Account verification pending',
        details: 'Enter a Nigerian bank and 10-digit NUBAN.',
        bank: '',
        account: '',
        rail: 'NIP'
      };
      let bankEnteredPin = '';
      let bankRequestKey = '', bankRequestFingerprint = '', bankRequestPending = false;
      let bankEntered2fa = '';
      let bankFinalSummary = {};

      function resetBankTransferFlow() {
        bankCurrentStep = 1;
        bankEnteredPin = '';
        bankEntered2fa = '';
        updateBankPinDisplay();
        updateBank2faDisplay();
        showBankStep(1);
        verifyBankAccount();
      }

      function showBankStep(step) {
        bankCurrentStep = step;

        stepRecipientContainer.classList.add('hidden');
        stepAmountContainer.classList.add('hidden');
        stepPinContainer.classList.add('hidden');
        if (step2faContainer) step2faContainer.classList.add('hidden');
        stepRollingContainer.classList.add('hidden');
        stepSuccessContainer.classList.add('hidden');

        if (step <= 4) {
          transferStepProgress.classList.remove('hidden');
        } else {
          transferStepProgress.classList.add('hidden');
        }

        if (step === 1) {
          stepRecipientContainer.classList.remove('hidden');
          transferBackBtn.classList.add('hidden');
          transferModalTitle.textContent = 'Bank Transfer';
          transferModalSubtitle.textContent = 'Step 1 of 4 \u2022 Destination Bank & Account';

          stepProgress1.className = 'flex-1 h-1.5 rounded-full bg-[#0b2545] transition-all duration-300';
          stepProgress2.className = 'flex-1 h-1.5 rounded-full bg-gray-200 transition-all duration-300';
          stepProgress3.className = 'flex-1 h-1.5 rounded-full bg-gray-200 transition-all duration-300';
          stepProgress4.className = 'flex-1 h-1.5 rounded-full bg-gray-200 transition-all duration-300';
        } else if (step === 2) {
          stepAmountContainer.classList.remove('hidden');
          transferBackBtn.classList.remove('hidden');
          transferModalTitle.textContent = 'Transfer Amount';
          transferModalSubtitle.textContent = 'Step 2 of 4 \u2022 Set Amount & Verify Balance';

          step2RecipientDisplay.textContent = bankVerifiedBeneficiary.name;
          step2RailDisplay.textContent = bankVerifiedBeneficiary.details;

          stepProgress1.className = 'flex-1 h-1.5 rounded-full bg-[#0b2545] transition-all duration-300';
          stepProgress2.className = 'flex-1 h-1.5 rounded-full bg-[#0b2545] transition-all duration-300';
          stepProgress3.className = 'flex-1 h-1.5 rounded-full bg-gray-200 transition-all duration-300';
          stepProgress4.className = 'flex-1 h-1.5 rounded-full bg-gray-200 transition-all duration-300';

          checkBankBalanceAndLimits();
        } else if (step === 3) {
          stepPinContainer.classList.remove('hidden');
          transferBackBtn.classList.remove('hidden');
          transferModalTitle.textContent = 'Enter Transfer Code';
          transferModalSubtitle.textContent = 'Step 3 of 4 \u2022 Security Authorization';

          const wallet = sourceWalletSelect.value;
          const symbol = walletSymbols[wallet] || '$';
          const amt = parseFloat(transferAmountInput.value) || 0;
          pinModalAmount.textContent = `${symbol}${amt.toLocaleString(undefined, {minimumFractionDigits: 2})} ${wallet}`;
          pinModalRecipient.textContent = bankVerifiedBeneficiary.name;

          stepProgress1.className = 'flex-1 h-1.5 rounded-full bg-[#0b2545] transition-all duration-300';
          stepProgress2.className = 'flex-1 h-1.5 rounded-full bg-[#0b2545] transition-all duration-300';
          stepProgress3.className = 'flex-1 h-1.5 rounded-full bg-[#0b2545] transition-all duration-300';
          stepProgress4.className = 'flex-1 h-1.5 rounded-full bg-gray-200 transition-all duration-300';

          bankEnteredPin = '';
          updateBankPinDisplay();
        } else if (step === 4) {
          if (step2faContainer) step2faContainer.classList.remove('hidden');
          transferBackBtn.classList.remove('hidden');
          transferModalTitle.textContent = 'Two-Factor Authenticator';
          transferModalSubtitle.textContent = 'Step 4 of 4 \u2022 6-Digit Verification Code';

          stepProgress1.className = 'flex-1 h-1.5 rounded-full bg-[#0b2545] transition-all duration-300';
          stepProgress2.className = 'flex-1 h-1.5 rounded-full bg-[#0b2545] transition-all duration-300';
          stepProgress3.className = 'flex-1 h-1.5 rounded-full bg-[#0b2545] transition-all duration-300';
          stepProgress4.className = 'flex-1 h-1.5 rounded-full bg-[#0b2545] transition-all duration-300';

          bankEntered2fa = '';
          updateBank2faDisplay();
        } else if (step === 5) {
          stepRollingContainer.classList.remove('hidden');
          transferBackBtn.classList.add('hidden');
          transferModalTitle.textContent = 'Processing Bank Transfer';
          transferModalSubtitle.textContent = 'Routing via payment clearing rail...';

          runBankRollingProcess();
        } else if (step === 6) {
          stepSuccessContainer.classList.remove('hidden');
          transferBackBtn.classList.add('hidden');
          transferModalTitle.textContent = 'Transfer Completed';
          transferModalSubtitle.textContent = 'Disbursement successfully dispatched';
        }
      }

      function verifyBankAccount() {
        if (!verificationLoading || !verificationResult) return;
        const bank = bankSelect ? bankSelect.value : '';
        const acct = bankAccountNumberInput ? bankAccountNumberInput.value.replace(/\D/g, '') : '';
        if (!/^\d{10}$/.test(acct) || !bank) {
          verificationLoading.classList.add('hidden');
          verificationResult.classList.remove('hidden');
          verifiedRecipientName.textContent = 'Account verification pending';
          verifiedRecipientDetails.textContent = !/^\d{10}$/.test(acct) ? 'Enter a 10-digit NUBAN to continue.' : 'Select the Nigerian bank for this account.';
          verifiedRailTag.textContent = 'NIP lookup';
          step1NextBtn.disabled = true;
          return;
        }
        verificationLoading.classList.remove('hidden');
        verificationResult.classList.add('hidden');

        fetch('../api/transfers/lookup-account.php', { method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json',Authorization:`Bearer ${token()}`}, body:JSON.stringify({account_number:acct, bank_code:bank}) })
          .then(r=>r.json().then(body=>({ok:r.ok,body})))
          .then(({ok,body})=>{
            verificationLoading.classList.add('hidden'); verificationResult.classList.remove('hidden');
            if(!ok || !body.success){ bankVerifiedBeneficiary={name:'Account could not be verified',details:body.error||'The bank could not verify this account.',bank,account:acct,rail:'NIP'}; verifiedRecipientName.textContent=bankVerifiedBeneficiary.name; verifiedRecipientDetails.textContent=bankVerifiedBeneficiary.details; verifiedRailTag.textContent='Lookup failed'; step1NextBtn.disabled=true; return; }
            bankVerifiedBeneficiary={name:body.account_name,details:`••••${body.account_number_last4} • ${body.bank_name||'Nigerian bank'}`,bank,account:acct,rail:'NIP',verificationToken:body.verification_token};
            verifiedRecipientName.textContent=bankVerifiedBeneficiary.name; verifiedRecipientDetails.textContent=bankVerifiedBeneficiary.details; verifiedRailTag.textContent='NIP verified'; step1NextBtn.disabled=false;
          }).catch(()=>{verificationLoading.classList.add('hidden');verificationResult.classList.remove('hidden');verifiedRecipientName.textContent='Account verification unavailable';verifiedRecipientDetails.textContent='Try again when the bank lookup service is available.';verifiedRailTag.textContent='Lookup unavailable';step1NextBtn.disabled=true;});
      }

      if (bankSelect) bankSelect.addEventListener('change', verifyBankAccount);
      if (bankAccountNumberInput) {
        bankAccountNumberInput.addEventListener('input', () => {
          const digits = bankAccountNumberInput.value.replace(/\D/g, '').slice(0, 10);
          bankAccountNumberInput.value = digits;
          const complete = digits.length === 10;
          if (accountNumberStatus) {
            accountNumberStatus.textContent = `${digits.length}/10`;
            accountNumberStatus.className = `absolute right-3 top-2.5 text-[10px] font-bold ${complete ? 'text-emerald-600' : 'text-gray-400'}`;
          }
          if (bankSelect) bankSelect.disabled = !complete;
          verifyBankAccount();
        });
        bankAccountNumberInput.addEventListener('blur', verifyBankAccount);
      }

      step1NextBtn.addEventListener('click', () => showBankStep(2));

      sourceWalletSelect.addEventListener('change', () => {
        const opt = sourceWalletSelect.options[sourceWalletSelect.selectedIndex];
        const wallet = opt.value;
        const symbol = walletSymbols[wallet] || '$';
        amountCurrencySymbol.textContent = symbol;
        const bal = walletBalances[wallet] || 0;
        selectedWalletBalanceDisplay.textContent = `Avail: ${symbol}${bal.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        checkBankBalanceAndLimits();
      });

      transferAmountInput.addEventListener('input', checkBankBalanceAndLimits);

      function checkBankBalanceAndLimits() {
        const wallet = sourceWalletSelect.value;
        const symbol = walletSymbols[wallet] || '$';
        const balance = walletBalances[wallet] || 0;
        const amount = parseFloat(transferAmountInput.value) || 0;
        const fee = 0;
        const totalToDebit = amount + fee;

        step2RailFeeText.textContent = `0.00 ${wallet}`;
        step2TotalDebitText.textContent = `${symbol}${totalToDebit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})} ${wallet}`;

        if (amount <= 0) {
          balanceCheckBanner.className = 'p-2.5 rounded-xl border border-gray-200 bg-white text-gray-500 font-medium text-xs';
          balanceCheckBanner.innerHTML = 'Enter an amount to verify available wallet balance.';
          step2NextBtn.disabled = true;
          step2NextBtn.classList.add('opacity-50', 'cursor-not-allowed');
          return;
        }

        if (totalToDebit > balance) {
          balanceCheckBanner.className = 'p-2.5 rounded-xl border border-rose-200 bg-rose-50 text-rose-700 font-medium text-xs flex items-center gap-2';
          balanceCheckBanner.innerHTML = `
            <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            <span>Insufficient balance! Available balance is ${symbol}${balance.toLocaleString(undefined, {minimumFractionDigits: 2})} ${wallet}.</span>
          `;
          step2NextBtn.disabled = true;
          step2NextBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
          const remaining = balance - totalToDebit;
          balanceCheckBanner.className = 'p-2.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 font-medium text-xs flex items-center gap-2';
          balanceCheckBanner.innerHTML = `
            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Balance Verified: Sufficient funds. Remaining: ${symbol}${remaining.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})} ${wallet}</span>
          `;
          step2NextBtn.disabled = false;
          step2NextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
      }

      window.setTransferAmount = function(val) {
        transferAmountInput.value = val.toFixed(2);
        checkBankBalanceAndLimits();
      };

      window.setMaxTransferAmount = function() {
        const wallet = sourceWalletSelect.value;
        const balance = walletBalances[wallet] || 0;
        const maxVal = Math.max(0, balance - 0.25);
        transferAmountInput.value = maxVal.toFixed(2);
        checkBankBalanceAndLimits();
      };

      step2BackBtn.addEventListener('click', () => showBankStep(1));
      step2ChangeRecipientBtn.addEventListener('click', () => showBankStep(1));
      step2NextBtn.addEventListener('click', () => showBankStep(3));

      // Bank PIN Handling (Step 3)
      function updateBankPinDisplay() {
        bankPinBoxes.forEach((box, i) => {
          if (i < bankEnteredPin.length) {
            box.textContent = '\u25CF';
            box.classList.add('border-[#0b2545]', 'bg-white');
            box.classList.remove('border-gray-200', 'bg-gray-50/50');
          } else {
            box.textContent = '';
            box.classList.remove('border-[#0b2545]', 'bg-white');
            box.classList.add('border-gray-200', 'bg-gray-50/50');
          }
        });

        if (bankEnteredPin.length === 4) {
          step3SubmitBtn.removeAttribute('disabled');
          step3SubmitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
          step3SubmitBtn.setAttribute('disabled', 'true');
          step3SubmitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
      }

      bankPinKeys.forEach(key => {
        key.addEventListener('click', () => {
          const val = key.getAttribute('data-val');
          if (val === 'clear') {
            bankEnteredPin = '';
          } else if (val === 'backspace') {
            bankEnteredPin = bankEnteredPin.slice(0, -1);
          } else if (bankEnteredPin.length < 4) {
            bankEnteredPin += val;
          }
          updateBankPinDisplay();
        });
      });

      step3BackBtn.addEventListener('click', () => showBankStep(2));
      step3SubmitBtn.addEventListener('click', () => showBankStep(4));

      // Bank 2FA Handling (Step 4)
      function updateBank2faDisplay() {
        bank2faBoxes.forEach((box, i) => {
          if (i < bankEntered2fa.length) {
            box.textContent = '\u25CF';
            box.classList.add('border-[#0b2545]', 'bg-white');
            box.classList.remove('border-gray-200', 'bg-gray-50/50');
          } else {
            box.textContent = '';
            box.classList.remove('border-[#0b2545]', 'bg-white');
            box.classList.add('border-gray-200', 'bg-gray-50/50');
          }
        });

        if (bankEntered2fa.length === 6) {
          step4SubmitBtn.removeAttribute('disabled');
          step4SubmitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
          step4SubmitBtn.setAttribute('disabled', 'true');
          step4SubmitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
      }

      bank2faKeys.forEach(key => {
        key.addEventListener('click', () => {
          const val = key.getAttribute('data-val');
          if (val === 'clear') {
            bankEntered2fa = '';
          } else if (val === 'backspace') {
            bankEntered2fa = bankEntered2fa.slice(0, -1);
          } else if (bankEntered2fa.length < 6) {
            bankEntered2fa += val;
          }
          updateBank2faDisplay();
        });
      });

      step4BackBtn.addEventListener('click', () => showBankStep(3));
      step4SubmitBtn.addEventListener('click', () => showBankStep(5));

      // Global keyboard handler for Bank Modal (PIN and 2FA)
      document.addEventListener('keydown', (e) => {
        if (transferModal.classList.contains('hidden')) return;

        if (bankCurrentStep === 3) {
          if (e.key >= '0' && e.key <= '9') {
            if (bankEnteredPin.length < 4) {
              bankEnteredPin += e.key;
              updateBankPinDisplay();
            }
          } else if (e.key === 'Backspace') {
            bankEnteredPin = bankEnteredPin.slice(0, -1);
            updateBankPinDisplay();
          } else if (e.key === 'Enter' && bankEnteredPin.length === 4) {
            showBankStep(4);
          }
        } else if (bankCurrentStep === 4) {
          if (e.key >= '0' && e.key <= '9') {
            if (bankEntered2fa.length < 6) {
              bankEntered2fa += e.key;
              updateBank2faDisplay();
            }
          } else if (e.key === 'Backspace') {
            bankEntered2fa = bankEntered2fa.slice(0, -1);
            updateBank2faDisplay();
          } else if (e.key === 'Enter' && bankEntered2fa.length === 6) {
            showBankStep(5);
          }
        }
      });

      // Bank Rolling Process (Step 5)
      function runBankRollingProcess() {
        const wallet = sourceWalletSelect.value;
        const symbol = walletSymbols[wallet] || '$';
        const amount = parseFloat(transferAmountInput.value) || 0;

        if (wallet !== 'NGN') { showBankStep(2); return; }
        if (bankRequestPending) return;
        const fingerprint = JSON.stringify([bankVerifiedBeneficiary.account,bankVerifiedBeneficiary.bank,transferAmountInput.value]);
        if (fingerprint !== bankRequestFingerprint) { bankRequestFingerprint = fingerprint; bankRequestKey = crypto.randomUUID(); }
        const requestKey = bankRequestKey; bankRequestPending = true;
        fetch('../api/transfers/initiate.php', {method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json',Authorization:`Bearer ${token()}`,'Idempotency-Key':requestKey}, body:JSON.stringify({account_number:bankVerifiedBeneficiary.account,verification_token:bankVerifiedBeneficiary.verificationToken,bank_code:bankVerifiedBeneficiary.bank,amount:transferAmountInput.value,pin:bankEnteredPin,narration:'Flowwithlit bank transfer',idempotency_key:requestKey})})
          .then(r=>r.json().then(body=>({ok:r.ok,body})))
          .then(({ok,body})=>{
            if(!ok || !body.success){ alert(body.error||'The bank transfer could not be initiated.'); showBankStep(2); return; }
            const ref=body.reference||'Pending'; bankFinalSummary={ref,amount:`${symbol}${amount.toLocaleString(undefined,{minimumFractionDigits:2})} ${wallet}`,beneficiary:bankVerifiedBeneficiary.name,rail:`${bankVerifiedBeneficiary.details}`,status:body.status==='completed'?'Completed':'Processing'};
            if(tickIcon2){tickIcon2.className='w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold';tickIcon2.textContent='✓';tickStatus2.className='text-[10px] font-bold text-emerald-600';tickStatus2.textContent='Reserved';}
            if(tickIcon3){tickIcon3.className='w-4 h-4 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-[10px] font-bold';tickIcon3.textContent='•';tickStatus3.className='text-[10px] font-bold text-amber-600';tickStatus3.textContent=body.status==='completed'?'Settled':'Processing';}
            successAmountDisplay.textContent=bankFinalSummary.amount;successSubtitleDisplay.textContent=`${body.status==='completed'?'Disbursed':'Processing'} to ${bankFinalSummary.beneficiary} via ${bankVerifiedBeneficiary.bank}`;successBeneficiaryName.textContent=bankFinalSummary.beneficiary;successRailName.textContent=bankFinalSummary.rail;successRefId.textContent=ref;successTimestamp.textContent=new Date().toLocaleString('en-GB',{dateStyle:'medium',timeStyle:'short'});showBankStep(6);
          }).catch(()=>{alert('The bank transfer service is unavailable. Retry the same request.');showBankStep(2);}).finally(()=>{bankRequestPending=false;});
        return;

        const ref = 'PAY-' + Math.floor(10000000 + Math.random() * 90000000);
        bankFinalSummary = {
          ref: ref,
          amount: `${symbol}${amount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})} ${wallet}`,
          beneficiary: bankVerifiedBeneficiary.name,
          rail: `${bankVerifiedBeneficiary.details}`,
          status: 'Completed'
        };

        tickIcon2.className = 'w-4 h-4 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-[10px] font-bold';
        tickIcon2.textContent = '\u2022';
        tickStatus2.className = 'text-[10px] font-bold text-gray-400';
        tickStatus2.textContent = 'Pending';

        tickIcon3.className = 'w-4 h-4 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-[10px] font-bold';
        tickIcon3.textContent = '\u2022';
        tickStatus3.className = 'text-[10px] font-bold text-gray-400';
        tickStatus3.textContent = 'Queued';

        setTimeout(() => {
          tickIcon2.className = 'w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold';
          tickIcon2.textContent = '\u2713';
          tickStatus2.className = 'text-[10px] font-bold text-emerald-600';
          tickStatus2.textContent = 'Debited';
        }, 550);

        setTimeout(() => {
          tickIcon3.className = 'w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold';
          tickIcon3.textContent = '\u2713';
          tickStatus3.className = 'text-[10px] font-bold text-emerald-600';
          tickStatus3.textContent = 'Settled';
        }, 1150);

        setTimeout(() => {
          successAmountDisplay.textContent = bankFinalSummary.amount;
          successSubtitleDisplay.textContent = `Disbursed to ${bankFinalSummary.beneficiary} via ${bankVerifiedBeneficiary.bank}`;
          successBeneficiaryName.textContent = bankFinalSummary.beneficiary;
          successRailName.textContent = bankFinalSummary.rail;
          successRefId.textContent = bankFinalSummary.ref;

          const now = new Date();
          successTimestamp.textContent = now.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) + ', ' + now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

          showBankStep(6);
        }, 1750);
      }

      successDoneBtn.addEventListener('click', () => {
        transferModal.classList.add('hidden');
        resetBankTransferFlow();
      });

      successViewReceiptBtn.addEventListener('click', () => {
        transferModal.classList.add('hidden');
        showReceipt(
          bankFinalSummary.ref,
          bankFinalSummary.beneficiary,
          bankFinalSummary.amount,
          bankFinalSummary.rail,
          'Completed'
        );
        resetBankTransferFlow();
      });

      if (openTransferModalBtn) {
        openTransferModalBtn.addEventListener('click', () => {
          resetBankTransferFlow();
          transferModal.classList.remove('hidden');
        });
      }

      if (closeTransferModalBtn) {
        closeTransferModalBtn.addEventListener('click', () => {
          transferModal.classList.add('hidden');
          resetBankTransferFlow();
        });
      }

      if (transferBackBtn) {
        transferBackBtn.addEventListener('click', () => {
          if (bankCurrentStep === 2) showBankStep(1);
          else if (bankCurrentStep === 3) showBankStep(2);
          else if (bankCurrentStep === 4) showBankStep(3);
        });
      }

      // =====================================================================
      // CONTROLLER 2: SEPARATE E-TRANSFER MODAL (Email, PayPal, Cash App)
      // =====================================================================
      const etransferModal = document.getElementById('etransferModal');
      const openEtransferModalBtn = document.getElementById('openEtransferModalBtn');
      const closeEtransferModalBtn = document.getElementById('closeEtransferModalBtn');
      const etransferBackBtn = document.getElementById('etransferBackBtn');
      const etransferModalTitle = document.getElementById('etransferModalTitle');
      const etransferModalSubtitle = document.getElementById('etransferModalSubtitle');
      const etransferStepProgress = document.getElementById('etransferStepProgress');
      const switchToBankTransferBtn = document.getElementById('switchToBankTransferBtn');

      // Containers
      const etransferStepRecipientContainer = document.getElementById('etransferStepRecipientContainer');
      const etransferStepAmountContainer = document.getElementById('etransferStepAmountContainer');
      const etransferStepPinContainer = document.getElementById('etransferStepPinContainer');
      const etransferStep2faContainer = document.getElementById('etransferStep2faContainer');
      const etransferStepRollingContainer = document.getElementById('etransferStepRollingContainer');
      const etransferStepSuccessContainer = document.getElementById('etransferStepSuccessContainer');

      // Progress bars
      const etransferProgress1 = document.getElementById('etransferProgress1');
      const etransferProgress2 = document.getElementById('etransferProgress2');
      const etransferProgress3 = document.getElementById('etransferProgress3');
      const etransferProgress4 = document.getElementById('etransferProgress4');

      // Channel Tabs & Blocks
      const etransferChannelBtns = document.querySelectorAll('.etransfer-channel-btn');
      const etransferChannelBlocks = document.querySelectorAll('.etransfer-channel-block');
      let activeEtransferChannel = 'email';

      // Channel Inputs
      const etransferEmailInput = document.getElementById('etransferEmailInput');
      const etransferEmailVerifyBtn = document.getElementById('etransferEmailVerifyBtn');
      const etransferPaypalInput = document.getElementById('etransferPaypalInput');
      const etransferPaypalBankSelect = document.getElementById('etransferPaypalBankSelect');
      const etransferPaypalVerifyBtn = document.getElementById('etransferPaypalVerifyBtn');
      const etransferCashappInput = document.getElementById('etransferCashappInput');
      const etransferCashappBankSelect = document.getElementById('etransferCashappBankSelect');
      const etransferCashappVerifyBtn = document.getElementById('etransferCashappVerifyBtn');

      // Verification elements
      const etransferVerificationLoading = document.getElementById('etransferVerificationLoading');
      const etransferVerificationResult = document.getElementById('etransferVerificationResult');
      const etransferVerifiedName = document.getElementById('etransferVerifiedName');
      const etransferVerifiedDetails = document.getElementById('etransferVerifiedDetails');
      const etransferVerifiedTag = document.getElementById('etransferVerifiedTag');
      const etransferStep1NextBtn = document.getElementById('etransferStep1NextBtn');

      // Amount Step Elements
      const etransferStep2RecipientDisplay = document.getElementById('etransferStep2RecipientDisplay');
      const etransferStep2DetailsDisplay = document.getElementById('etransferStep2DetailsDisplay');
      const etransferStep2ChangeRecipientBtn = document.getElementById('etransferStep2ChangeRecipientBtn');
      const etransferSourceWalletSelect = document.getElementById('etransferSourceWalletSelect');
      const etransferSelectedWalletBalanceDisplay = document.getElementById('etransferSelectedWalletBalanceDisplay');
      const etransferAmountInput = document.getElementById('etransferAmountInput');
      const etransferAmountCurrencySymbol = document.getElementById('etransferAmountCurrencySymbol');
      const etransferBalanceCheckBanner = document.getElementById('etransferBalanceCheckBanner');
      const etransferFeeText = document.getElementById('etransferFeeText');
      const etransferTotalDebitText = document.getElementById('etransferTotalDebitText');
      const etransferStep2BackBtn = document.getElementById('etransferStep2BackBtn');
      const etransferStep2NextBtn = document.getElementById('etransferStep2NextBtn');

      // PIN Elements
      const etransferPinModalAmount = document.getElementById('etransferPinModalAmount');
      const etransferPinModalRecipient = document.getElementById('etransferPinModalRecipient');
      const etransferPinBoxes = document.querySelectorAll('.etransfer-pin-box');
      const etransferPinKeys = document.querySelectorAll('.etransfer-pin-key');
      const etransferStep3BackBtn = document.getElementById('etransferStep3BackBtn');
      const etransferStep3SubmitBtn = document.getElementById('etransferStep3SubmitBtn');

      // 2FA Elements
      const etransfer2faBoxes = document.querySelectorAll('.etransfer-2fa-box');
      const etransfer2faKeys = document.querySelectorAll('.etransfer-2fa-key');
      const etransferStep4BackBtn = document.getElementById('etransferStep4BackBtn');
      const etransferStep4SubmitBtn = document.getElementById('etransferStep4SubmitBtn');

      // Rolling Elements
      const etransferTickIcon2 = document.getElementById('etransferTickIcon2');
      const etransferTickText2 = document.getElementById('etransferTickText2');
      const etransferTickStatus2 = document.getElementById('etransferTickStatus2');
      const etransferTickIcon3 = document.getElementById('etransferTickIcon3');
      const etransferTickText3 = document.getElementById('etransferTickText3');
      const etransferTickStatus3 = document.getElementById('etransferTickStatus3');

      // Success Elements
      const etransferSuccessAmountDisplay = document.getElementById('etransferSuccessAmountDisplay');
      const etransferSuccessSubtitleDisplay = document.getElementById('etransferSuccessSubtitleDisplay');
      const etransferSuccessBeneficiaryName = document.getElementById('etransferSuccessBeneficiaryName');
      const etransferSuccessChannelName = document.getElementById('etransferSuccessChannelName');
      const etransferSuccessRefId = document.getElementById('etransferSuccessRefId');
      const etransferSuccessTimestamp = document.getElementById('etransferSuccessTimestamp');
      const etransferSuccessViewReceiptBtn = document.getElementById('etransferSuccessViewReceiptBtn');
      const etransferSuccessDoneBtn = document.getElementById('etransferSuccessDoneBtn');

      // State variables for e-Transfer
      let etransferCurrentStep = 1;
      let etransferVerifiedBeneficiary = {
        name: 'David Miller',
        username: '@david_m',
        email: 'david.miller@acmecorp.ca',
        details: 'david.miller@acmecorp.ca \u2022 Auto-Deposit',
        channel: 'Interac e-Transfer',
        partnerBank: 'Auto-Deposit'
      };
      let etransferEnteredPin = '';
      let etransferEntered2fa = '';
      let etransferFinalSummary = {};

      function resetEtransferFlow() {
        etransferCurrentStep = 1;
        etransferEnteredPin = '';
        etransferEntered2fa = '';
        updateEtransferPinDisplay();
        updateEtransfer2faDisplay();
        showEtransferStep(1);
        verifyEtransferAccount();
      }

      function showEtransferStep(step) {
        etransferCurrentStep = step;

        etransferStepRecipientContainer.classList.add('hidden');
        etransferStepAmountContainer.classList.add('hidden');
        etransferStepPinContainer.classList.add('hidden');
        if (etransferStep2faContainer) etransferStep2faContainer.classList.add('hidden');
        etransferStepRollingContainer.classList.add('hidden');
        etransferStepSuccessContainer.classList.add('hidden');

        if (step <= 4) {
          etransferStepProgress.classList.remove('hidden');
        } else {
          etransferStepProgress.classList.add('hidden');
        }

        if (step === 1) {
          etransferStepRecipientContainer.classList.remove('hidden');
          etransferBackBtn.classList.add('hidden');
          etransferModalTitle.textContent = 'e-Transfer & Payout';
          etransferModalSubtitle.textContent = 'Step 1 of 4 \u2022 Recipient Lookup';

          etransferProgress1.className = 'flex-1 h-1.5 rounded-full bg-[#0b2545] transition-all duration-300';
          etransferProgress2.className = 'flex-1 h-1.5 rounded-full bg-gray-200 transition-all duration-300';
          etransferProgress3.className = 'flex-1 h-1.5 rounded-full bg-gray-200 transition-all duration-300';
          etransferProgress4.className = 'flex-1 h-1.5 rounded-full bg-gray-200 transition-all duration-300';
        } else if (step === 2) {
          etransferStepAmountContainer.classList.remove('hidden');
          etransferBackBtn.classList.remove('hidden');
          etransferModalTitle.textContent = 'Transfer Amount';
          etransferModalSubtitle.textContent = 'Step 2 of 4 \u2022 Enter Amount & Verify Balance';

          etransferStep2RecipientDisplay.textContent = etransferVerifiedBeneficiary.name;
          etransferStep2DetailsDisplay.textContent = etransferVerifiedBeneficiary.details;

          etransferProgress1.className = 'flex-1 h-1.5 rounded-full bg-[#0b2545] transition-all duration-300';
          etransferProgress2.className = 'flex-1 h-1.5 rounded-full bg-[#0b2545] transition-all duration-300';
          etransferProgress3.className = 'flex-1 h-1.5 rounded-full bg-gray-200 transition-all duration-300';
          etransferProgress4.className = 'flex-1 h-1.5 rounded-full bg-gray-200 transition-all duration-300';

          checkEtransferBalanceAndLimits();
        } else if (step === 3) {
          etransferStepPinContainer.classList.remove('hidden');
          etransferBackBtn.classList.remove('hidden');
          etransferModalTitle.textContent = 'Enter Transfer Code';
          etransferModalSubtitle.textContent = 'Step 3 of 4 \u2022 Security Authorization';

          const wallet = etransferSourceWalletSelect.value;
          const symbol = walletSymbols[wallet] || '$';
          const amt = parseFloat(etransferAmountInput.value) || 0;
          etransferPinModalAmount.textContent = `${symbol}${amt.toLocaleString(undefined, {minimumFractionDigits: 2})} ${wallet}`;
          etransferPinModalRecipient.textContent = etransferVerifiedBeneficiary.name;

          etransferProgress1.className = 'flex-1 h-1.5 rounded-full bg-[#0b2545] transition-all duration-300';
          etransferProgress2.className = 'flex-1 h-1.5 rounded-full bg-[#0b2545] transition-all duration-300';
          etransferProgress3.className = 'flex-1 h-1.5 rounded-full bg-[#0b2545] transition-all duration-300';
          etransferProgress4.className = 'flex-1 h-1.5 rounded-full bg-gray-200 transition-all duration-300';

          etransferEnteredPin = '';
          updateEtransferPinDisplay();
        } else if (step === 4) {
          if (etransferStep2faContainer) etransferStep2faContainer.classList.remove('hidden');
          etransferBackBtn.classList.remove('hidden');
          etransferModalTitle.textContent = 'Two-Factor Authenticator';
          etransferModalSubtitle.textContent = 'Step 4 of 4 \u2022 6-Digit Verification Code';

          etransferProgress1.className = 'flex-1 h-1.5 rounded-full bg-[#0b2545] transition-all duration-300';
          etransferProgress2.className = 'flex-1 h-1.5 rounded-full bg-[#0b2545] transition-all duration-300';
          etransferProgress3.className = 'flex-1 h-1.5 rounded-full bg-[#0b2545] transition-all duration-300';
          etransferProgress4.className = 'flex-1 h-1.5 rounded-full bg-[#0b2545] transition-all duration-300';

          etransferEntered2fa = '';
          updateEtransfer2faDisplay();
        } else if (step === 5) {
          etransferStepRollingContainer.classList.remove('hidden');
          etransferBackBtn.classList.add('hidden');
          etransferModalTitle.textContent = 'Processing e-Transfer';
          etransferModalSubtitle.textContent = 'Routing via partner clearing network...';

          runEtransferRollingProcess();
        } else if (step === 6) {
          etransferStepSuccessContainer.classList.remove('hidden');
          etransferBackBtn.classList.add('hidden');
          etransferModalTitle.textContent = 'e-Transfer Completed';
          etransferModalSubtitle.textContent = 'Disbursement successfully dispatched';
        }
      }

      // e-Transfer Channel Selector Tabs
      etransferChannelBtns.forEach(btn => {
        btn.addEventListener('click', () => {
          etransferChannelBtns.forEach(b => {
            b.classList.remove('active', 'border-[#0b2545]', 'bg-[#0b2545]', 'text-white', 'shadow-xs');
            b.classList.add('border-gray-200', 'bg-white', 'text-gray-700');
          });
          btn.classList.add('active', 'border-[#0b2545]', 'bg-[#0b2545]', 'text-white', 'shadow-xs');
          btn.classList.remove('border-gray-200', 'bg-white', 'text-gray-700');

          activeEtransferChannel = btn.getAttribute('data-channel');
          etransferChannelBlocks.forEach(blk => blk.classList.add('hidden'));

          if (activeEtransferChannel === 'email') document.getElementById('etransferChannelEmail').classList.remove('hidden');
          else if (activeEtransferChannel === 'paypal') document.getElementById('etransferChannelPaypal').classList.remove('hidden');
          else if (activeEtransferChannel === 'cashapp') document.getElementById('etransferChannelCashapp').classList.remove('hidden');

          verifyEtransferAccount();
        });
      });

      function verifyEtransferAccount() {
        if (!etransferVerificationLoading || !etransferVerificationResult) return;
        etransferVerificationLoading.classList.remove('hidden');
        etransferVerificationResult.classList.add('hidden');

        setTimeout(() => {
          etransferVerificationLoading.classList.add('hidden');
          etransferVerificationResult.classList.remove('hidden');

          if (activeEtransferChannel === 'email') {
            const email = etransferEmailInput ? (etransferEmailInput.value || 'david.miller@acmecorp.ca') : 'david.miller@acmecorp.ca';
            etransferVerifiedBeneficiary = {
              name: 'David Miller',
              username: '@david_m',
              email: email,
              details: `${email} \u2022 Auto-Deposit Registered`,
              channel: 'Interac e-Transfer',
              partnerBank: 'Auto-Deposit'
            };
            etransferVerifiedName.textContent = 'David Miller';
            etransferVerifiedDetails.textContent = `${email} \u2022 Auto-Deposit`;
            etransferVerifiedTag.textContent = 'Interac';
          } else if (activeEtransferChannel === 'paypal') {
            const handle = etransferPaypalInput ? (etransferPaypalInput.value || '@sarahj_tech') : '@sarahj_tech';
            const bank = etransferPaypalBankSelect ? etransferPaypalBankSelect.value : 'JPMorgan Chase (US)';
            etransferVerifiedBeneficiary = {
              name: 'Sarah Jenkins (TechCorp)',
              username: handle.startsWith('@') ? handle : `@${handle}`,
              email: 'sarah.j@techcorp.io',
              details: `PayPal ${handle} \u2022 ${bank.split('(')[0].trim()}`,
              channel: 'PayPal Payout',
              partnerBank: bank
            };
            etransferVerifiedName.textContent = 'Sarah Jenkins';
            etransferVerifiedDetails.textContent = `${handle} \u2022 ${bank.split('(')[0].trim()}`;
            etransferVerifiedTag.textContent = 'PayPal';
          } else if (activeEtransferChannel === 'cashapp') {
            const tag = etransferCashappInput ? (etransferCashappInput.value || 'alex_chukwu') : 'alex_chukwu';
            const bank = etransferCashappBankSelect ? etransferCashappBankSelect.value : 'Sutton Bank (FDIC)';
            const cleanTag = tag.replace(/^\$/, '');
            etransferVerifiedBeneficiary = {
              name: 'Alexander Chukwuemeka',
              username: `$${cleanTag}`,
              email: 'alex.c@flowwithlit.com',
              details: `Cash App $${cleanTag} \u2022 ${bank.split('(')[0].trim()}`,
              channel: 'Cash App Payout',
              partnerBank: bank
            };
            etransferVerifiedName.textContent = 'Alexander Chukwuemeka';
            etransferVerifiedDetails.textContent = `$${cleanTag} \u2022 ${bank.split('(')[0].trim()}`;
            etransferVerifiedTag.textContent = 'Cash App';
          }

          etransferStep1NextBtn.removeAttribute('disabled');
        }, 250);
      }

      // Input triggers for e-Transfer
      if (etransferEmailInput) etransferEmailInput.addEventListener('change', verifyEtransferAccount);
      if (etransferEmailVerifyBtn) etransferEmailVerifyBtn.addEventListener('click', verifyEtransferAccount);
      if (etransferPaypalInput) etransferPaypalInput.addEventListener('change', verifyEtransferAccount);
      if (etransferPaypalBankSelect) etransferPaypalBankSelect.addEventListener('change', verifyEtransferAccount);
      if (etransferPaypalVerifyBtn) etransferPaypalVerifyBtn.addEventListener('click', verifyEtransferAccount);
      if (etransferCashappInput) etransferCashappInput.addEventListener('change', verifyEtransferAccount);
      if (etransferCashappBankSelect) etransferCashappBankSelect.addEventListener('change', verifyEtransferAccount);
      if (etransferCashappVerifyBtn) etransferCashappVerifyBtn.addEventListener('click', verifyEtransferAccount);

      etransferStep1NextBtn.addEventListener('click', () => showEtransferStep(2));

      etransferSourceWalletSelect.addEventListener('change', () => {
        const opt = etransferSourceWalletSelect.options[etransferSourceWalletSelect.selectedIndex];
        const wallet = opt.value;
        const symbol = walletSymbols[wallet] || '$';
        etransferAmountCurrencySymbol.textContent = symbol;
        const bal = walletBalances[wallet] || 0;
        etransferSelectedWalletBalanceDisplay.textContent = `Avail: ${symbol}${bal.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        checkEtransferBalanceAndLimits();
      });

      etransferAmountInput.addEventListener('input', checkEtransferBalanceAndLimits);

      function checkEtransferBalanceAndLimits() {
        const wallet = etransferSourceWalletSelect.value;
        const symbol = walletSymbols[wallet] || '$';
        const balance = walletBalances[wallet] || 0;
        const amount = parseFloat(etransferAmountInput.value) || 0;
        const fee = 0.00; // Zero fee for peer e-transfers
        const totalToDebit = amount + fee;

        etransferFeeText.textContent = `Zero Fee ($0.00)`;
        etransferTotalDebitText.textContent = `${symbol}${totalToDebit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})} ${wallet}`;

        if (amount <= 0) {
          etransferBalanceCheckBanner.className = 'p-2.5 rounded-xl border border-gray-200 bg-white text-gray-500 font-medium text-xs';
          etransferBalanceCheckBanner.innerHTML = 'Enter an amount to verify available wallet balance.';
          etransferStep2NextBtn.disabled = true;
          etransferStep2NextBtn.classList.add('opacity-50', 'cursor-not-allowed');
          return;
        }

        if (totalToDebit > balance) {
          etransferBalanceCheckBanner.className = 'p-2.5 rounded-xl border border-rose-200 bg-rose-50 text-rose-700 font-medium text-xs flex items-center gap-2';
          etransferBalanceCheckBanner.innerHTML = `
            <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            <span>Insufficient balance! Available balance is ${symbol}${balance.toLocaleString(undefined, {minimumFractionDigits: 2})} ${wallet}.</span>
          `;
          etransferStep2NextBtn.disabled = true;
          etransferStep2NextBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
          const remaining = balance - totalToDebit;
          etransferBalanceCheckBanner.className = 'p-2.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 font-medium text-xs flex items-center gap-2';
          etransferBalanceCheckBanner.innerHTML = `
            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Balance Verified: Sufficient funds. Remaining: ${symbol}${remaining.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})} ${wallet}</span>
          `;
          etransferStep2NextBtn.disabled = false;
          etransferStep2NextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
      }

      window.setEtransferAmount = function(val) {
        etransferAmountInput.value = val.toFixed(2);
        checkEtransferBalanceAndLimits();
      };

      window.setMaxEtransferAmount = function() {
        const wallet = etransferSourceWalletSelect.value;
        const balance = walletBalances[wallet] || 0;
        etransferAmountInput.value = balance.toFixed(2);
        checkEtransferBalanceAndLimits();
      };

      etransferStep2BackBtn.addEventListener('click', () => showEtransferStep(1));
      etransferStep2ChangeRecipientBtn.addEventListener('click', () => showEtransferStep(1));
      etransferStep2NextBtn.addEventListener('click', () => showEtransferStep(3));

      // e-Transfer PIN Handling (Step 3)
      function updateEtransferPinDisplay() {
        etransferPinBoxes.forEach((box, i) => {
          if (i < etransferEnteredPin.length) {
            box.textContent = '\u25CF';
            box.classList.add('border-[#0b2545]', 'bg-white');
            box.classList.remove('border-gray-200', 'bg-gray-50/50');
          } else {
            box.textContent = '';
            box.classList.remove('border-[#0b2545]', 'bg-white');
            box.classList.add('border-gray-200', 'bg-gray-50/50');
          }
        });

        if (etransferEnteredPin.length === 4) {
          etransferStep3SubmitBtn.removeAttribute('disabled');
          etransferStep3SubmitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
          etransferStep3SubmitBtn.setAttribute('disabled', 'true');
          etransferStep3SubmitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
      }

      etransferPinKeys.forEach(key => {
        key.addEventListener('click', () => {
          const val = key.getAttribute('data-val');
          if (val === 'clear') {
            etransferEnteredPin = '';
          } else if (val === 'backspace') {
            etransferEnteredPin = etransferEnteredPin.slice(0, -1);
          } else if (etransferEnteredPin.length < 4) {
            etransferEnteredPin += val;
          }
          updateEtransferPinDisplay();
        });
      });

      etransferStep3BackBtn.addEventListener('click', () => showEtransferStep(2));
      etransferStep3SubmitBtn.addEventListener('click', () => showEtransferStep(4));

      // e-Transfer 2FA Handling (Step 4)
      function updateEtransfer2faDisplay() {
        etransfer2faBoxes.forEach((box, i) => {
          if (i < etransferEntered2fa.length) {
            box.textContent = '\u25CF';
            box.classList.add('border-[#0b2545]', 'bg-white');
            box.classList.remove('border-gray-200', 'bg-gray-50/50');
          } else {
            box.textContent = '';
            box.classList.remove('border-[#0b2545]', 'bg-white');
            box.classList.add('border-gray-200', 'bg-gray-50/50');
          }
        });

        if (etransferEntered2fa.length === 6) {
          etransferStep4SubmitBtn.removeAttribute('disabled');
          etransferStep4SubmitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
          etransferStep4SubmitBtn.setAttribute('disabled', 'true');
          etransferStep4SubmitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
      }

      etransfer2faKeys.forEach(key => {
        key.addEventListener('click', () => {
          const val = key.getAttribute('data-val');
          if (val === 'clear') {
            etransferEntered2fa = '';
          } else if (val === 'backspace') {
            etransferEntered2fa = etransferEntered2fa.slice(0, -1);
          } else if (etransferEntered2fa.length < 6) {
            etransferEntered2fa += val;
          }
          updateEtransfer2faDisplay();
        });
      });

      etransferStep4BackBtn.addEventListener('click', () => showEtransferStep(3));
      etransferStep4SubmitBtn.addEventListener('click', () => showEtransferStep(5));

      // Global keyboard handler for e-Transfer Modal (PIN and 2FA)
      document.addEventListener('keydown', (e) => {
        if (etransferModal.classList.contains('hidden')) return;

        if (etransferCurrentStep === 3) {
          if (e.key >= '0' && e.key <= '9') {
            if (etransferEnteredPin.length < 4) {
              etransferEnteredPin += e.key;
              updateEtransferPinDisplay();
            }
          } else if (e.key === 'Backspace') {
            etransferEnteredPin = etransferEnteredPin.slice(0, -1);
            updateEtransferPinDisplay();
          } else if (e.key === 'Enter' && etransferEnteredPin.length === 4) {
            showEtransferStep(4);
          }
        } else if (etransferCurrentStep === 4) {
          if (e.key >= '0' && e.key <= '9') {
            if (etransferEntered2fa.length < 6) {
              etransferEntered2fa += e.key;
              updateEtransfer2faDisplay();
            }
          } else if (e.key === 'Backspace') {
            etransferEntered2fa = etransferEntered2fa.slice(0, -1);
            updateEtransfer2faDisplay();
          } else if (e.key === 'Enter' && etransferEntered2fa.length === 6) {
            showEtransferStep(5);
          }
        }
      });

      // e-Transfer Rolling Process (Step 5)
      function runEtransferRollingProcess() {
        const wallet = etransferSourceWalletSelect.value;
        const symbol = walletSymbols[wallet] || '$';
        const amount = parseFloat(etransferAmountInput.value) || 0;

        const ref = 'ET-' + Math.floor(10000000 + Math.random() * 90000000);
        etransferFinalSummary = {
          ref: ref,
          amount: `${symbol}${amount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})} ${wallet}`,
          beneficiary: etransferVerifiedBeneficiary.name,
          rail: `${etransferVerifiedBeneficiary.channel} (${etransferVerifiedBeneficiary.details})`,
          status: 'Completed'
        };

        etransferTickIcon2.className = 'w-4 h-4 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-[10px] font-bold';
        etransferTickIcon2.textContent = '\u2022';
        etransferTickStatus2.className = 'text-[10px] font-bold text-gray-400';
        etransferTickStatus2.textContent = 'Pending';

        etransferTickIcon3.className = 'w-4 h-4 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-[10px] font-bold';
        etransferTickIcon3.textContent = '\u2022';
        etransferTickStatus3.className = 'text-[10px] font-bold text-gray-400';
        etransferTickStatus3.textContent = 'Queued';

        setTimeout(() => {
          etransferTickIcon2.className = 'w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold';
          etransferTickIcon2.textContent = '\u2713';
          etransferTickStatus2.className = 'text-[10px] font-bold text-emerald-600';
          etransferTickStatus2.textContent = 'Debited';
        }, 550);

        setTimeout(() => {
          etransferTickIcon3.className = 'w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold';
          etransferTickIcon3.textContent = '\u2713';
          etransferTickStatus3.className = 'text-[10px] font-bold text-emerald-600';
          etransferTickStatus3.textContent = 'Settled';
        }, 1150);

        setTimeout(() => {
          etransferSuccessAmountDisplay.textContent = etransferFinalSummary.amount;
          etransferSuccessSubtitleDisplay.textContent = `Disbursed to ${etransferFinalSummary.beneficiary} via ${etransferVerifiedBeneficiary.channel}`;
          etransferSuccessBeneficiaryName.textContent = etransferFinalSummary.beneficiary;
          etransferSuccessChannelName.textContent = etransferVerifiedBeneficiary.details;
          etransferSuccessRefId.textContent = etransferFinalSummary.ref;

          const now = new Date();
          etransferSuccessTimestamp.textContent = now.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) + ', ' + now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

          showEtransferStep(6);
        }, 1750);
      }

      etransferSuccessDoneBtn.addEventListener('click', () => {
        etransferModal.classList.add('hidden');
        resetEtransferFlow();
      });

      etransferSuccessViewReceiptBtn.addEventListener('click', () => {
        etransferModal.classList.add('hidden');
        showReceipt(
          etransferFinalSummary.ref,
          etransferFinalSummary.beneficiary,
          etransferFinalSummary.amount,
          etransferFinalSummary.rail,
          'Completed'
        );
        resetEtransferFlow();
      });

      if (openEtransferModalBtn) {
        openEtransferModalBtn.addEventListener('click', () => {
          resetEtransferFlow();
          etransferModal.classList.remove('hidden');
        });
      }

      if (closeEtransferModalBtn) {
        closeEtransferModalBtn.addEventListener('click', () => {
          etransferModal.classList.add('hidden');
          resetEtransferFlow();
        });
      }

      if (etransferBackBtn) {
        etransferBackBtn.addEventListener('click', () => {
          if (etransferCurrentStep === 2) showEtransferStep(1);
          else if (etransferCurrentStep === 3) showEtransferStep(2);
          else if (etransferCurrentStep === 4) showEtransferStep(3);
        });
      }

      // =====================================================================
      // CROSS-MODAL QUICK SWITCHERS
      // =====================================================================
      if (switchToEtransferBtn) {
        switchToEtransferBtn.addEventListener('click', () => {
          transferModal.classList.add('hidden');
          resetBankTransferFlow();
          resetEtransferFlow();
          etransferModal.classList.remove('hidden');
        });
      }

      if (switchToBankTransferBtn) {
        switchToBankTransferBtn.addEventListener('click', () => {
          etransferModal.classList.add('hidden');
          resetEtransferFlow();
          resetBankTransferFlow();
          transferModal.classList.remove('hidden');
        });
      }

      // Modal Outside Clicks
      [transferModal, etransferModal, document.getElementById('batchModal'), document.getElementById('receiptModal')].forEach(modal => {
        if (modal) {
          modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.classList.add('hidden');
          });
        }
      });


      // 3. Segmented Filter Tabs for Payments Ledger
      const filterBtns = document.querySelectorAll('.payment-filter-btn');
      const tableRows = document.querySelectorAll('#paymentsTableBody tr');
      const ledgerTableTitle = document.getElementById('ledgerTableTitle');

      filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
          // Reset all
          filterBtns.forEach(b => {
            b.classList.remove('bg-[#0b2545]', 'text-white', 'shadow-xs', 'font-bold');
            b.classList.add('text-gray-600', 'font-semibold');
            const badge = b.querySelector('.payment-filter-badge');
            if (badge) {
              badge.className = 'payment-filter-badge px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-300/60 text-gray-600 leading-none';
            }
          });

          // Activate clicked
          btn.classList.remove('text-gray-600', 'font-semibold');
          btn.classList.add('bg-[#0b2545]', 'text-white', 'shadow-xs', 'font-bold');
          const activeBadge = btn.querySelector('.payment-filter-badge');
          if (activeBadge) {
            activeBadge.className = 'payment-filter-badge px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-white/20 text-white leading-none';
          }

          const filter = btn.getAttribute('data-filter');
          if (ledgerTableTitle) {
            if (filter === 'bank') ledgerTableTitle.textContent = 'Bank Payouts & Global Clearing';
            else if (filter === 'crypto') ledgerTableTitle.textContent = 'Blockchain Payouts & Transfers';
            else if (filter === 'internal') ledgerTableTitle.textContent = 'Internal Peer Settlements';
            else ledgerTableTitle.textContent = 'Payments & Disbursements Ledger';
          }

          applyFilters();
        });
      });

      // 4. Status Filter Dropdown
      const paymentStatusFilter = document.getElementById('paymentStatusFilter');
      if (paymentStatusFilter) {
        paymentStatusFilter.addEventListener('change', applyFilters);
      }

      // 5. Live Search Filter
      const searchInput = document.getElementById('paymentSearchInput');
      if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
      }

      function applyFilters() {
        const activeTabBtn = document.querySelector('.payment-filter-btn.bg-\\[\\#0b2545\\]');
        const railFilter = activeTabBtn ? activeTabBtn.getAttribute('data-filter') : 'all';
        const statusFilter = paymentStatusFilter ? paymentStatusFilter.value : 'all';
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';

        tableRows.forEach(row => {
          const rowRail = row.getAttribute('data-type');
          const rowStatus = row.getAttribute('data-status');
          const rowText = row.textContent.toLowerCase();

          const matchesRail = (railFilter === 'all' || rowRail === railFilter);
          const matchesStatus = (statusFilter === 'all' || rowStatus === statusFilter);
          const matchesSearch = (!query || rowText.includes(query));

          if (matchesRail && matchesStatus && matchesSearch) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      }
    });

    // Receipt helper
    function showReceipt(ref, beneficiary, amount, rail, status) {
      document.getElementById('receiptRef').textContent = ref;
      document.getElementById('receiptBeneficiary').textContent = beneficiary;
      document.getElementById('receiptAmount').textContent = amount;
      document.getElementById('receiptRail').textContent = rail;
      const statusEl = document.getElementById('receiptStatus');
      const iconContainer = document.getElementById('receiptStatusIconContainer');
      statusEl.textContent = status;

      if (status === 'Paid') {
        iconContainer.className = 'w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mb-2';
        iconContainer.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
        statusEl.className = 'inline-flex items-center gap-1 mt-1 text-[11px] font-bold text-emerald-700';
      } else if (status === 'Completed') {
        iconContainer.className = 'w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mb-2';
        iconContainer.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5m-5 0l-4.5 4.5" /></svg>';
        statusEl.className = 'inline-flex items-center gap-1 mt-1 text-[11px] font-bold text-emerald-700';
      } else if (status === 'Failed') {
        iconContainer.className = 'w-12 h-12 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center mb-2';
        iconContainer.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
        statusEl.className = 'inline-flex items-center gap-1 mt-1 text-[11px] font-bold text-rose-700';
      } else if (status.startsWith('Processing')) {
        iconContainer.className = 'w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mb-2';
        iconContainer.innerHTML = '<svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>';
        statusEl.className = 'inline-flex items-center gap-1 mt-1 text-[11px] font-bold text-amber-700';
      } else {
        iconContainer.className = 'w-12 h-12 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center mb-2';
        iconContainer.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
        statusEl.className = 'inline-flex items-center gap-1 mt-1 text-[11px] font-bold text-gray-700';
      }

      document.getElementById('receiptModal').classList.remove('hidden');
    }
  </script>

  <script>
    (() => {
      const esc=value=>String(value??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
      const money=(value,code)=>{try{return new Intl.NumberFormat('en-NG',{style:'currency',currency:code,maximumFractionDigits:8}).format(Number(value||0));}catch(_){return `${Number(value||0).toLocaleString()} ${code}`;}};
      const date=value=>value?new Date(String(value).replace(' ','T')+'Z').toLocaleString('en-NG',{dateStyle:'medium',timeStyle:'short'}):'—';
      const uiStatus=value=>({successful:'Completed',pending:'Processing',failed:'Failed',refunded:'Refunded'}[value]||value);
      const loadPayments=async()=>{const body=document.getElementById('paymentsTableBody'); try {const token=localStorage.getItem('flow_token')||''; const response=await fetch('../api/transactions/list.php?range=all&page=1&page_size=50',{headers:{Authorization:`Bearer ${token}`,Accept:'application/json'}}); const result=await response.json(); if(!response.ok||!result.success) throw new Error(result.error||'Unable to load payments.'); const rows=(result.transactions||[]).filter(row=>['withdrawal','transfer','payment','fee'].includes(row.type)); const successful=rows.filter(row=>row.status==='successful'), pending=rows.filter(row=>row.status==='pending'), currency=rows[0]?.currency||'NGN'; document.getElementById('paymentsDisbursed').textContent=money(successful.reduce((sum,row)=>sum+Number(row.amount||0),0),currency); document.getElementById('paymentsPendingAmount').textContent=money(pending.reduce((sum,row)=>sum+Number(row.amount||0),0),currency); document.querySelectorAll('.payment-filter-badge').forEach((badge,index)=>{badge.textContent=index===0?rows.length:index===1?rows.filter(row=>row.channel!=='crypto').length:index===2?rows.filter(row=>row.channel==='crypto').length:rows.filter(row=>row.type==='transfer').length;}); body.innerHTML=rows.length?rows.map(row=>{const rail=row.channel==='crypto'?'Crypto transfer':row.channel==='bank_transfer'?'Bank transfer':row.channel||'Payment'; const kind=row.channel==='crypto'?'crypto':row.type==='transfer'?'internal':'bank'; return `<tr class="hover:bg-gray-50/70 transition" data-type="${kind}" data-status="${row.status}" data-search="${esc(`${row.reference} ${row.customer_name} ${row.customer_email} ${row.currency}`.toLowerCase())}"><td class="py-4 px-4 whitespace-nowrap"><div class="font-bold text-gray-900">${esc(row.customer_name||row.provider||'Wallet transfer')}</div><div class="text-[10px] text-gray-400">${esc(row.customer_email||'')}</div></td><td class="py-4 px-4 whitespace-nowrap"><div class="font-bold text-gray-900">${esc(rail)}</div><div class="text-[10px] text-gray-400 font-mono">${esc(row.reference)}</div></td><td class="py-4 px-4 whitespace-nowrap"><div class="text-gray-900 font-medium">${esc(date(row.created_at))}</div></td><td class="py-4 px-4 whitespace-nowrap"><div class="font-semibold text-gray-900">${esc(row.currency)} wallet</div><div class="text-[10px] text-gray-400">Flowwithlit balance</div></td><td class="py-4 px-4 text-right whitespace-nowrap"><div class="font-extrabold text-gray-900 text-sm">-${esc(money(row.amount,row.currency))}</div><div class="text-[10px] text-gray-400">Fee: ${esc(money(row.fee,row.currency))}</div></td><td class="py-4 px-4 text-center"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold ${row.status==='successful'?'bg-emerald-50 text-emerald-700 border border-emerald-200/60':row.status==='pending'?'bg-amber-50 text-amber-700 border border-amber-200/60':'bg-rose-50 text-rose-700 border border-rose-200/60'}">${esc(uiStatus(row.status))}</span></td><td class="py-4 px-4 text-right"><a href="transactions.php" class="text-xs font-semibold text-[#0b2545] hover:underline">Details</a></td></tr>`;}).join(''):'<tr><td colspan="7" class="py-16 text-center text-sm text-gray-400">No payments or transfers yet.</td></tr>'; body.style.visibility='visible'; const apply=()=>{const type=document.querySelector('.payment-filter-btn.bg-\\[\\#0b2545\\]')?.dataset.filter||'all', status=document.getElementById('paymentStatusFilter')?.value||'all', query=(document.getElementById('paymentSearchInput')?.value||'').toLowerCase().trim(); body.querySelectorAll('tr[data-type]').forEach(row=>{const typeMatch=type==='all'||row.dataset.type===type, statusMatch=status==='all'||(status==='paid'&&row.dataset.status==='successful')||(status==='completed'&&row.dataset.status==='successful')||(status==='processing'&&row.dataset.status==='pending')||(status==='failed'&&row.dataset.status==='failed'), searchMatch=!query||row.dataset.search.includes(query); row.style.display=typeMatch&&statusMatch&&searchMatch?'':'none';});}; document.querySelectorAll('.payment-filter-btn').forEach(btn=>btn.addEventListener('click',()=>setTimeout(apply,0))); document.getElementById('paymentStatusFilter')?.addEventListener('change',apply); document.getElementById('paymentSearchInput')?.addEventListener('input',apply); apply(); } catch(error) {body.innerHTML=`<tr><td colspan="7" class="py-16 text-center text-sm text-amber-700">${esc(error.message)}</td></tr>`; body.style.visibility='visible';}}; loadPayments(); window.addEventListener('flowwithlit:dashboard-refresh',loadPayments); document.addEventListener('visibilitychange',()=>{if(!document.hidden)loadPayments();});
    })();
  </script>

  <script>
    (() => {
      const token=()=>localStorage.getItem('flow_token')||'';
      const loadNigeriaRails=async()=>{try{const [bankResponse,walletResponse]=await Promise.all([fetch('../api/transfers/banks.php',{headers:{Authorization:`Bearer ${token()}`,Accept:'application/json'}}),fetch('../api/wallets/list.php',{headers:{Authorization:`Bearer ${token()}`,Accept:'application/json'}})]); const banks=await bankResponse.json(), wallets=await walletResponse.json(); const bankSelect=document.getElementById('bankSelect'); if(bankSelect&&banks.success){bankSelect.innerHTML='<option value="">Select Nigerian bank</option>'+banks.banks.map(bank=>`<option value="${bank.code}" data-name="${bank.name}">${bank.name} (${bank.code})</option>`).join('');} const source=document.getElementById('sourceWalletSelect'); if(source&&wallets.success){const allowed=(wallets.wallets||[]).filter(wallet=>wallet.currency_code==='NGN'||wallet.kind==='crypto'); source.innerHTML=allowed.length?allowed.map(wallet=>`<option value="${wallet.currency_code}" data-currency="${wallet.currency_code}" data-balance="${wallet.available_balance}">${wallet.name} (${wallet.currency_code}) · Balance: ${wallet.available_balance}</option>`).join(''):'<option value="NGN">NGN wallet unavailable</option>';} const eButton=document.getElementById('openEtransferModalBtn'); if(eButton){eButton.disabled=true;eButton.title='E-transfer is not enabled for the Nigeria launch';eButton.classList.add('opacity-50','cursor-not-allowed');eButton.querySelector('span:last-child').textContent='E-transfer unavailable';} document.querySelectorAll('#etransferModal input,#etransferModal select,#etransferModal button:not([id="closeEtransferModalBtn"])').forEach(control=>{if(control.id!=='switchToBankTransferBtn')control.disabled=true;});}catch(error){console.error(error);}}; loadNigeriaRails();
    })();
  </script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
