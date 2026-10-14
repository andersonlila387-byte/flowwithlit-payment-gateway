<?php
/**
 * Flowwithlit Wallets & Accounts Page
 * Multi-Currency: Fiat (USD, NGN, EUR, GBP, CAD) & Crypto (USDT, BTC, ETH, SOL, USDC)
 */
$pageTitle = 'Wallets & Accounts — Flowwithlit';
$activePage = 'wallets';
$activeNavTab = 'Features';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<style>#walletsGrid, #walletHistoryBody { visibility: hidden; }</style>

    <!-- Scrollable Main Content Area -->
    <main class="flex-1 h-full overflow-y-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-6 flex flex-col gap-6">
      
      <!-- Top Title & Quick Action Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 flex-shrink-0">
        <div>
          <div class="flex items-center gap-2">
            <span class="text-xs text-gray-400 font-medium tracking-wide">Financial Assets</span>
            <span id="activeWalletCount" class="text-[11px] font-bold text-sky-700 bg-sky-50 border border-sky-200/50 px-2 py-0.5 rounded-full">0 Active Balances</span>
          </div>
          <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight mt-0.5">Wallets &amp; Multi-Currency</h1>
        </div>

        <!-- Essential wallet actions only; Send/Receive stay on each wallet card. -->
        <div class="grid grid-cols-2 sm:flex sm:items-center gap-2.5 w-full sm:w-auto">
          <!-- Quick Swap Action -->
          <a href="swap.php" id="quickSwapBtn" class="justify-center px-4 py-2.5 rounded-full bg-white hover:bg-gray-50 border border-gray-200/80 text-gray-700 text-xs font-semibold flex items-center gap-2 shadow-sm transition hover:scale-105 active:scale-95">
            <svg class="w-3.5 h-3.5 text-[#0b2545]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
            </svg>
            <span>Swap Crypto &amp; Fiat</span>
          </a>

          <!-- Primary: Add / Create Wallet Button (Brand Navy) -->
          <button id="addWalletBtn" type="button" class="justify-center bg-[#0b2545] hover:bg-[#132847] text-white px-5 py-2.5 rounded-full text-xs font-semibold flex items-center gap-2 shadow-sm transition hover:scale-105 active:scale-95">
            <span class="w-4 h-4 rounded-full border border-white/40 flex items-center justify-center text-[11px] leading-none">+</span>
            <span>Add New Wallet</span>
          </button>
        </div>
      </div>

      <!-- Row 1: Summary KPI Cards (Combined Net Worth + Fiat Total + Crypto Total) -->
      <div class="grid grid-cols-1 md:grid-cols-12 gap-5 lg:gap-6 flex-shrink-0">
        
        <!-- Total Net Worth Card -->
        <div class="md:col-span-12 lg:col-span-6 bg-white rounded-[28px] p-6 lg:p-7 shadow-sm border border-gray-200/80 flex flex-col justify-between gap-5 relative">
          <div>
            <div class="flex items-center justify-between">
              <span class="text-xs font-semibold text-gray-500 tracking-wide">Combined Portfolio Value</span>
              <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider bg-gray-50 border border-gray-100 px-2.5 py-1 rounded-full">NGN reference</span>
            </div>

            <!-- Amount with Eye privacy toggle -->
            <div class="flex items-center gap-3 mt-3">
              <span id="walletTotalNetWorth" class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">₦0.00</span>
              <button id="toggleWalletBalanceBtn" class="w-7 h-7 rounded-full bg-gray-50 hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-700 transition active:scale-95" title="Toggle privacy">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </button>
            </div>

            <div class="flex items-center gap-2 mt-2 text-xs font-medium text-gray-500">
              <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/>
                </svg>
              </span>
              <span>+$28,490.15 (4.15%) in the last 24h</span>
            </div>
          </div>

          <!-- Quick breakdown bar -->
          <div class="pt-3 border-t border-gray-100">
            <div class="flex items-center justify-between text-xs font-semibold text-gray-600 mb-2">
              <span id="fiatBreakdown" class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#0b2545]"></span> Fiat: ₦0.00</span>
              <span id="cryptoBreakdown" class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-sky-400"></span> Crypto: ₮0.00</span>
            </div>
            <div class="w-full h-2.5 rounded-full bg-gray-100 overflow-hidden flex">
              <div class="h-full bg-[#0b2545] rounded-l-full" style="width: 59%"></div>
              <div class="h-full bg-sky-400 rounded-r-full" style="width: 41%"></div>
            </div>
          </div>
        </div>

        <!-- Total Fiat Currency Card -->
        <div class="md:col-span-6 lg:col-span-3 bg-white dark:bg-[#0c1f36] rounded-[28px] p-6 shadow-sm border border-gray-200/80 dark:border-gray-700 flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between">
              <span class="text-xs font-semibold text-gray-500 dark:text-gray-300 tracking-wide">Fiat Currencies</span>
              <span id="fiatWalletCount" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 dark:bg-blue-950/60 text-[#0b2545] dark:text-blue-200 border border-blue-100 dark:border-blue-800">0 Wallets</span>
            </div>
            <div class="text-2xl lg:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight mt-3" id="fiatTotalVal">₦0.00</div>
            <p class="text-xs text-gray-400 dark:text-gray-400 mt-1">Multi-currency bank balances</p>
          </div>

          <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500 font-medium">
            <div class="flex items-center gap-1">
              <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
              <span id="fiatWalletCodes">No fiat wallets yet</span>
            </div>
            <a href="#fiat-section" class="text-xs font-bold text-[#0b2545] hover:underline">View &rarr;</a>
          </div>
        </div>

        <!-- Total Cryptocurrency Card -->
        <div class="md:col-span-6 lg:col-span-3 bg-white dark:bg-[#0c1f36] rounded-[28px] p-6 shadow-sm border border-gray-200/80 dark:border-gray-700 flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between">
              <span class="text-xs font-semibold text-gray-500 dark:text-gray-300 tracking-wide">Cryptocurrencies</span>
              <span id="cryptoWalletCount" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-200 border border-sky-200/50 dark:border-sky-800">0 Assets</span>
            </div>
            <div class="text-2xl lg:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight mt-3" id="cryptoTotalVal">₮0.00</div>
            <p class="text-xs text-gray-400 dark:text-gray-400 mt-1">On-chain assets · USDT reference</p>
          </div>

          <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500 font-medium">
            <div class="flex items-center gap-1">
              <span class="w-2 h-2 rounded-full bg-sky-400"></span>
              <span id="cryptoWalletCodes">No crypto wallets yet</span>
            </div>
            <a href="#crypto-section" class="text-xs font-bold text-[#0b2545] hover:underline">View &rarr;</a>
          </div>
        </div>

      </div>

      <!-- Asset Category Tabs & Search Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 bg-gray-50/90 p-2 sm:p-2.5 rounded-2xl sm:rounded-[22px] border border-gray-200/70">
        
        <!-- Filter Tabs (Segmented Control on Mobile, Adaptive on Desktop) -->
        <div class="grid grid-cols-3 sm:flex sm:items-center p-1 bg-gray-200/60 rounded-xl sm:rounded-full gap-1 w-full sm:w-auto">
          <button class="wallet-filter-btn flex items-center justify-center gap-1.5 sm:gap-2 px-2.5 sm:px-4 py-2 rounded-lg sm:rounded-full text-xs font-bold bg-[#0b2545] text-white shadow-xs transition" data-filter="all">
            <span>All</span>
            <span class="wallet-filter-badge px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-white/20 text-white leading-none">0</span>
          </button>
          
          <button class="wallet-filter-btn flex items-center justify-center gap-1.5 sm:gap-2 px-2.5 sm:px-4 py-2 rounded-lg sm:rounded-full text-xs font-semibold text-gray-600 hover:text-gray-900 transition" data-filter="fiat">
            <span>Fiat</span>
            <span class="hidden md:inline">Currencies</span>
            <span class="wallet-filter-badge px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-300/60 text-gray-600 leading-none">0</span>
          </button>
          
          <button class="wallet-filter-btn flex items-center justify-center gap-1.5 sm:gap-2 px-2.5 sm:px-4 py-2 rounded-lg sm:rounded-full text-xs font-semibold text-gray-600 hover:text-gray-900 transition" data-filter="crypto">
            <span>Crypto</span>
            <span class="hidden md:inline">currencies</span>
            <span class="wallet-filter-badge px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-300/60 text-gray-600 leading-none">0</span>
          </button>
        </div>

        <!-- Search input for wallets -->
        <div class="relative w-full sm:w-64">
          <input type="text" id="walletSearchInput" placeholder="Search currency or asset..." class="w-full bg-white border border-gray-200 rounded-xl sm:rounded-full pl-9 pr-4 py-2 text-xs text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#0b2545]/20 focus:border-[#0b2545] transition shadow-2xs">
          <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <circle cx="11" cy="11" r="7" />
            <path stroke-linecap="round" d="M21 21l-4.35-4.35" />
          </svg>
        </div>

      </div>

      <!-- Section: Wallets Cards Grid -->
      <div>
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-base font-bold text-gray-900" id="walletsSectionTitle">Active Currency Balances (0)</h2>
          <span class="text-xs text-gray-400">Click any wallet for instant details</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 gap-4" id="walletsGrid">
          <div class="col-span-full rounded-2xl border border-gray-200/80 bg-white p-6 text-center text-sm text-gray-400">
            Loading wallets…
          </div>
        </div>
      </div>

      <!-- FULL WIDTH 100% LEDGER & TRANSACTION HISTORY TABLE -->
      <!-- User explicitly requested: "If you have a table the table should it should take the full 100% width. It should take its own full space. It should not bargain with anybody. It should take its own side." -->
      <section class="w-full bg-white rounded-[28px] p-6 lg:p-7 shadow-sm border border-gray-200/80 flex flex-col gap-5">
        
        <!-- Table Top Header Controls -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-5">
          <div>
            <div class="flex items-center gap-2">
              <h2 class="text-lg lg:text-xl font-bold text-gray-900 tracking-tight">Ledger &amp; Transaction History</h2>
            </div>
            <p class="text-xs text-gray-400 mt-0.5">Comprehensive multi-currency deposits, withdrawals, swaps &amp; on-chain events</p>
          </div>

          <!-- Table Filters & Export -->
          <div class="flex items-center gap-2.5 flex-wrap">
            <!-- Filter Dropdown -->
            <select id="txTypeFilter" class="bg-gray-50 border border-gray-200 text-xs font-semibold text-gray-700 rounded-full px-3.5 py-2 focus:outline-none focus:ring-2 focus:ring-[#0b2545]/20 cursor-pointer">
              <option value="all">All Transactions</option>
              <option value="deposit">Deposits</option>
              <option value="withdrawal">Withdrawals</option>
              <option value="swap">Swaps (FX / Crypto)</option>
              <option value="transfer">Internal Transfers</option>
            </select>

            <!-- Export Statement Button -->
            <button class="px-4 py-2 rounded-full bg-white hover:bg-gray-50 border border-gray-200/80 text-gray-700 text-xs font-semibold flex items-center gap-1.5 shadow-2xs transition hover:scale-105 active:scale-95">
              <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
              </svg>
              <span>Export CSV</span>
            </button>
          </div>
        </div>

        <!-- 100% Full Width Table Container -->
        <div class="w-full overflow-x-auto">
          <table class="w-full text-left border-collapse min-w-[760px]">
            <thead>
              <tr class="border-b border-gray-100 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                <th class="py-3 px-4">Asset / Currency</th>
                <th class="py-3 px-4">Type &amp; Reference</th>
                <th class="py-3 px-4">Date &amp; Time</th>
                <th class="py-3 px-4">Recipient / Counterparty</th>
                <th class="py-3 px-4 text-right">Amount</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4 text-right">Action</th>
              </tr>
            </thead>
            <tbody id="walletHistoryBody" class="divide-y divide-gray-100/80 text-xs">
              
              <!-- Row 1: USDT Deposit -->
              <tr class="hover:bg-gray-50/70 transition group" data-type="deposit">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-[#26a17b] text-white flex items-center justify-center font-bold text-xs shadow-2xs">
                      ₮
                    </div>
                    <div>
                      <div class="font-bold text-gray-900">Tether USD</div>
                      <div class="text-[10px] text-gray-400">TRC-20 on-chain</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="font-bold text-emerald-700 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Deposit (Receive)
                  </div>
                  <div class="text-[10px] text-gray-400 font-mono">TXN-84920194</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">11 June 2026</div>
                  <div class="text-[10px] text-gray-400">10:45 AM</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">External Crypto Wallet</div>
                  <div class="text-[10px] text-gray-400 font-mono">TRX...7xYq</div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-extrabold text-emerald-600 text-sm">+5,000.00 USDT</div>
                  <div class="text-[10px] text-gray-400 font-normal">≈ $5,000.00 USD</div>
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Completed
                  </span>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <button class="text-xs font-semibold text-[#0b2545] dark:text-sky-300 hover:underline dark:hover:text-sky-200" onclick="alert('Viewing Ledger Details for TXN-84920194')">Details</button>
                </td>
              </tr>

              <!-- Row 2: NGN Payout -->
              <tr class="hover:bg-gray-50/70 transition group" data-type="withdrawal">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-full overflow-hidden inline-flex items-center justify-center flex-shrink-0 shadow-2xs border border-gray-200">
                      <svg viewBox="0 0 640 480" class="w-full h-full object-cover">
                        <path fill="#008751" d="M0 0h213.3v480H0zm426.7 0H640v480H426.7z"/>
                        <path fill="#ffffff" d="M213.3 0h213.4v480H213.3z"/>
                      </svg>
                    </span>
                    <div>
                      <div class="font-bold text-gray-900">Nigerian Naira</div>
                      <div class="text-[10px] text-gray-400">Bank Transfer</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="font-bold text-gray-900 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                    Withdrawal (Payout)
                  </div>
                  <div class="text-[10px] text-gray-400 font-mono">TXN-71829401</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">10 June 2026</div>
                  <div class="text-[10px] text-gray-400">03:12 PM</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">Adeyemi O. (GTBank)</div>
                  <div class="text-[10px] text-gray-400 font-mono">0123****92</div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-extrabold text-gray-900 text-sm">-₦4,500,000.00</div>
                  <div class="text-[10px] text-gray-400 font-normal">Fee: ₦50.00</div>
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Completed
                  </span>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <button class="text-xs font-semibold text-[#0b2545] dark:text-sky-300 hover:underline dark:hover:text-sky-200" onclick="alert('Viewing Ledger Details for TXN-71829401')">Details</button>
                </td>
              </tr>

              <!-- Row 3: Swap Crypto to Fiat (BTC -> USD) -->
              <tr class="hover:bg-gray-50/70 transition group" data-type="swap">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-[#f7931a] text-white flex items-center justify-center font-bold text-xs shadow-2xs">
                      ₿
                    </div>
                    <div>
                      <div class="font-bold text-gray-900">Instant Swap</div>
                      <div class="text-[10px] text-gray-400">BTC &rarr; USD</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="font-bold text-[#0b2545] flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#0b2545]"></span>
                    Currency Swap (FX)
                  </div>
                  <div class="text-[10px] text-gray-400 font-mono">SWP-90284102</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">09 June 2026</div>
                  <div class="text-[10px] text-gray-400">08:20 PM</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">Flowwithlit Liquidity Engine</div>
                  <div class="text-[10px] text-gray-400 font-mono">Rate: $66,400/BTC</div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-extrabold text-[#0b2545] text-sm">+ $33,200.00 USD</div>
                  <div class="text-[10px] text-gray-400 font-normal">Sold: 0.5 BTC</div>
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Settled
                  </span>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <button class="text-xs font-semibold text-[#0b2545] dark:text-sky-300 hover:underline dark:hover:text-sky-200" onclick="alert('Viewing Ledger Details for SWP-90284102')">Details</button>
                </td>
              </tr>

              <!-- Row 4: USD Wire Transfer -->
              <tr class="hover:bg-gray-50/70 transition group" data-type="deposit">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-full overflow-hidden inline-flex items-center justify-center flex-shrink-0 shadow-2xs border border-gray-200">
                      <svg viewBox="0 0 640 480" class="w-full h-full object-cover">
                        <path fill="#bd3d44" d="M0 0h640v480H0z"/>
                        <path stroke="#fff" stroke-width="37" d="M0 55h640M0 129h640M0 203h640M0 277h640M0 351h640M0 425h640"/>
                        <path fill="#192f5d" d="M0 0h256v258H0z"/>
                      </svg>
                    </span>
                    <div>
                      <div class="font-bold text-gray-900">US Dollar</div>
                      <div class="text-[10px] text-gray-400">Fedwire Incoming</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="font-bold text-emerald-700 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Deposit (ACH/Wire)
                  </div>
                  <div class="text-[10px] text-gray-400 font-mono">TXN-55192834</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">08 June 2026</div>
                  <div class="text-[10px] text-gray-400">01:15 PM</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">Stripe Payments USA</div>
                  <div class="text-[10px] text-gray-400 font-mono">Payout Ref: 8931</div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-extrabold text-emerald-600 text-sm">+$42,000.00 USD</div>
                  <div class="text-[10px] text-gray-400 font-normal">Fee: $0.00</div>
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Completed
                  </span>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <button class="text-xs font-semibold text-[#0b2545] dark:text-sky-300 hover:underline dark:hover:text-sky-200" onclick="alert('Viewing Ledger Details for TXN-55192834')">Details</button>
                </td>
              </tr>

              <!-- Row 5: Solana (SOL) Outgoing -->
              <tr class="hover:bg-gray-50/70 transition group" data-type="withdrawal">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-[#9945FF] to-[#14F195] text-white flex items-center justify-center font-bold text-xs shadow-2xs">
                      ◎
                    </div>
                    <div>
                      <div class="font-bold text-gray-900">Solana</div>
                      <div class="text-[10px] text-gray-400">SPL Transfer</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="font-bold text-gray-900 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                    Crypto Withdrawal
                  </div>
                  <div class="text-[10px] text-gray-400 font-mono">TXN-41920481</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">07 June 2026</div>
                  <div class="text-[10px] text-gray-400">11:04 AM</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">Phantom Vault</div>
                  <div class="text-[10px] text-gray-400 font-mono">7xKX...gAsU</div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-extrabold text-gray-900 text-sm">-25.00 SOL</div>
                  <div class="text-[10px] text-gray-400 font-normal">≈ $3,663.75 USD</div>
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    14/32 Confs
                  </span>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <button class="text-xs font-semibold text-[#0b2545] dark:text-sky-300 hover:underline dark:hover:text-sky-200" onclick="alert('Viewing Ledger Details for TXN-41920481')">Details</button>
                </td>
              </tr>

            </tbody>
          </table>
        </div>

        <!-- Table Footer / Pagination -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-gray-100 text-xs text-gray-500">
          <span id="walletHistorySummary">No transactions yet</span>
          
          <div class="flex items-center gap-1.5">
            <button class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 disabled:opacity-40" disabled>Previous</button>
            <button class="px-3 py-1.5 rounded-lg bg-[#0b2545] text-white font-bold">1</button>
            <button class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700">2</button>
            <button class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700">3</button>
            <span class="px-1 text-gray-400">...</span>
            <button class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700">15</button>
            <button class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700">Next</button>
          </div>
        </div>

      </section>

    </main>

    <!-- Receive / Deposit slide-out -->
    <div id="walletPanelBackdrop" class="hidden fixed inset-0 z-[70] bg-[#071322]/45 backdrop-blur-[2px]"></div>
    <aside id="receiveWalletPanel" class="fixed inset-y-0 right-0 z-[80] w-full max-w-md translate-x-full overflow-y-auto bg-white dark:bg-[#0c1f36] shadow-2xl transition-transform duration-300" aria-hidden="true">
      <div class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-100 dark:border-gray-700 bg-white/95 dark:bg-[#0c1f36]/95 backdrop-blur px-6 py-5">
        <div><p class="text-xs font-bold uppercase tracking-wider text-sky-600">Receive funds</p><h2 class="text-xl font-bold text-gray-900 dark:text-white">Deposit to your wallet</h2></div>
        <button type="button" data-close-wallet-panel class="w-10 h-10 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 grid place-items-center" aria-label="Close receive panel"><i data-lucide="x" class="w-5 h-5"></i></button>
      </div>
      <div class="p-6">
        <div class="grid grid-cols-3 gap-1 rounded-xl bg-gray-100 dark:bg-gray-800 p-1">
          <button type="button" data-deposit-tab="bank" class="deposit-tab rounded-lg bg-white dark:bg-[#102641] px-2 py-2 text-xs font-bold text-[#0b2545] dark:text-white shadow-sm">Bank</button>
          <button type="button" data-deposit-tab="crypto" class="deposit-tab rounded-lg px-2 py-2 text-xs font-bold text-gray-500 dark:text-gray-300">Crypto</button>
          <button type="button" data-deposit-tab="link" class="deposit-tab rounded-lg px-2 py-2 text-xs font-bold text-gray-500 dark:text-gray-300">Payment link</button>
        </div>
        <div id="depositBankContent" class="deposit-content mt-6">
          <p class="text-sm text-gray-500 dark:text-gray-300">Transfer to your Naira wallet using these details. Include the reference exactly as shown.</p>
          <div class="mt-5 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 space-y-4">
            <div><span class="block text-xs text-gray-400">Account name</span><strong id="walletReceiveAccountName" class="text-base text-gray-900 dark:text-white">Loading…</strong></div>
            <div class="flex items-center justify-between gap-3"><div><span class="block text-xs text-gray-400">Account number</span><strong id="walletReceiveAccountNumber" class="text-lg font-mono text-gray-900 dark:text-white">Loading…</strong></div><button type="button" id="walletReceiveCopy" data-panel-copy="" class="rounded-lg bg-gray-100 dark:bg-gray-800 p-2 text-gray-600 dark:text-gray-200" aria-label="Copy account number"><i data-lucide="copy" class="w-4 h-4"></i></button></div>
            <div><span class="block text-xs text-gray-400">Bank</span><strong id="walletReceiveBank" class="text-base text-gray-900 dark:text-white">Loading…</strong></div>
            <div><span class="block text-xs text-gray-400">Transfer reference</span><strong class="text-base font-mono text-sky-700 dark:text-sky-300">FLW-NGN-8492</strong></div>
          </div>
          <div class="mt-4 rounded-xl bg-amber-50 dark:bg-amber-950/30 p-3 text-xs text-amber-800 dark:text-amber-200">Your balance updates automatically after the transfer is confirmed.</div>
        </div>
        <div id="depositCryptoContent" class="deposit-content hidden mt-6">
          <label class="block text-xs font-bold text-gray-600 dark:text-gray-300">Asset and network<select id="depositCryptoSelect" class="mt-2 w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#09182b] px-3 py-3 text-sm text-gray-900 dark:text-white"><option>USDT — TRC20</option><option>USDC — ERC20</option><option>Bitcoin — Bitcoin</option></select></label>
          <div class="mt-5 flex gap-5 rounded-2xl border border-gray-200 dark:border-gray-700 p-4"><div class="grid h-24 w-24 shrink-0 grid-cols-7 gap-[2px] rounded-lg bg-white p-2"> <span class="bg-[#0b2545]"></span><span></span><span class="bg-[#0b2545]"></span><span class="bg-[#0b2545]"></span><span></span><span class="bg-[#0b2545]"></span><span class="bg-[#0b2545]"></span><span></span><span class="bg-[#0b2545]"></span><span></span><span class="bg-[#0b2545]"></span><span></span><span class="bg-[#0b2545]"></span><span></span><span class="bg-[#0b2545]"></span><span></span><span class="bg-[#0b2545]"></span><span class="bg-[#0b2545]"></span><span></span><span class="bg-[#0b2545]"></span><span></span><span></span><span class="bg-[#0b2545]"></span><span></span><span class="bg-[#0b2545]"></span><span class="bg-[#0b2545]"></span><span></span><span class="bg-[#0b2545]"></span><span></span><span></span><span class="bg-[#0b2545]"></span><span></span><span class="bg-[#0b2545]"></span><span></span><span class="bg-[#0b2545]"></span><span></span><span class="bg-[#0b2545]"></span><span class="bg-[#0b2545]"></span><span></span><span class="bg-[#0b2545]"></span><span></span><span class="bg-[#0b2545]"></span><span></span><span class="bg-[#0b2545]"></span><span class="bg-[#0b2545]"></span><span></span><span class="bg-[#0b2545]"></span></div><div class="min-w-0"><span class="text-xs text-gray-400">Your deposit address</span><p class="mt-2 break-all font-mono text-xs font-bold text-gray-900 dark:text-white">TQxYq8bA4sKjWn7vP2mZfL6rE9cD3hU1aB</p><button type="button" data-panel-copy="TQxYq8bA4sKjWn7vP2mZfL6rE9cD3hU1aB" class="mt-3 text-xs font-bold text-sky-600">Copy address</button></div></div>
          <p class="mt-4 text-xs text-amber-700 dark:text-amber-300">Only send the selected asset on the selected network. Other deposits may be lost.</p>
        </div>
        <div id="depositLinkContent" class="deposit-content hidden mt-6"><p class="text-sm text-gray-500 dark:text-gray-300">Create a payment link to receive money from anyone.</p><label class="mt-5 block text-xs font-bold text-gray-600 dark:text-gray-300">Amount (optional)<input type="number" placeholder="0.00" class="mt-2 w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#09182b] px-3 py-3 text-sm" /></label><a href="request-money.php" class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-[#0b2545] px-4 py-3 text-sm font-bold text-white">Create payment link <i data-lucide="arrow-right" class="w-4 h-4"></i></a></div>
      </div>
    </aside>

    <!-- Add wallet slide-out -->
    <aside id="addWalletPanel" class="fixed inset-y-0 right-0 z-[80] w-full max-w-md translate-x-full overflow-y-auto bg-white dark:bg-[#0c1f36] shadow-2xl transition-transform duration-300" aria-hidden="true">
      <div class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-100 dark:border-gray-700 bg-white/95 dark:bg-[#0c1f36]/95 backdrop-blur px-6 py-5"><div><p class="text-xs font-bold uppercase tracking-wider text-emerald-600">New balance</p><h2 class="text-xl font-bold text-gray-900 dark:text-white">Add a wallet</h2></div><button type="button" data-close-wallet-panel class="w-10 h-10 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 grid place-items-center" aria-label="Close add wallet panel"><i data-lucide="x" class="w-5 h-5"></i></button></div>
      <div class="p-6"><p class="text-sm text-gray-500 dark:text-gray-300">Select a supported currency. Wallets already in your account are marked as active.</p><div class="mt-6 space-y-3" id="newWalletOptions"><button type="button" data-wallet-option="JPY" class="wallet-option w-full flex items-center justify-between rounded-2xl border border-gray-200 dark:border-gray-700 p-4 text-left hover:border-sky-400"><span><strong class="block text-gray-900 dark:text-white">Japanese Yen</strong><small class="text-gray-400">JPY · Fiat wallet</small></span><span class="text-xl">¥</span></button><button type="button" data-wallet-option="AED" class="wallet-option w-full flex items-center justify-between rounded-2xl border border-gray-200 dark:border-gray-700 p-4 text-left hover:border-sky-400"><span><strong class="block text-gray-900 dark:text-white">UAE Dirham</strong><small class="text-gray-400">AED · Fiat wallet</small></span><span class="text-xl">د.إ</span></button><button type="button" data-wallet-option="XRP" class="wallet-option w-full flex items-center justify-between rounded-2xl border border-gray-200 dark:border-gray-700 p-4 text-left hover:border-sky-400"><span><strong class="block text-gray-900 dark:text-white">XRP</strong><small class="text-gray-400">XRP · Crypto wallet</small></span><span class="text-xl text-sky-500">✦</span></button><button type="button" data-wallet-option="USDT" disabled class="w-full flex items-center justify-between rounded-2xl border border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-[#09182b] p-4 text-left opacity-60"><span><strong class="block text-gray-600 dark:text-gray-300">Tether</strong><small class="text-gray-400">USDT · Already active</small></span><span class="rounded-full bg-emerald-100 px-2 py-1 text-[10px] font-bold text-emerald-700">Added</span></button></div><p id="addWalletFeedback" class="hidden mt-5 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 p-3 text-sm text-emerald-700 dark:text-emerald-300"></p></div>
    </aside>

    <!-- Page Specific Script: Copy feedback, Privacy Eye, Wallet Filter Tabs -->
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        // 1. Copy to clipboard
        document.querySelectorAll('.copy-btn').forEach(btn => {
          btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const text = btn.getAttribute('data-copy');
            if (navigator.clipboard) {
              navigator.clipboard.writeText(text).then(() => {
                const originalSvg = btn.innerHTML;
                btn.innerHTML = `<span class="text-[10px] text-emerald-600 font-bold">Copied!</span>`;
                setTimeout(() => {
                  btn.innerHTML = originalSvg;
                }, 1500);
              });
            }
          });
        });

        // 2. Privacy Eye Toggle for Net Worth & Balances
        const toggleWalletBalanceBtn = document.getElementById('toggleWalletBalanceBtn');
        const walletTotalNetWorth = document.getElementById('walletTotalNetWorth');
        const fiatTotalVal = document.getElementById('fiatTotalVal');
        const cryptoTotalVal = document.getElementById('cryptoTotalVal');
        const balanceVals = document.querySelectorAll('.wallet-balance-val');
        
        let isNetWorthVisible = true;
        const originalNetWorth = '₦0.00';
        const originalFiat = '₦0.00';
        const originalCrypto = '₮0.00';
        const originalBalances = Array.from(balanceVals).map(el => el.textContent);

        if (toggleWalletBalanceBtn) {
          toggleWalletBalanceBtn.addEventListener('click', () => {
            isNetWorthVisible = !isNetWorthVisible;
            if (walletTotalNetWorth) walletTotalNetWorth.textContent = isNetWorthVisible ? originalNetWorth : '••••••••';
            if (fiatTotalVal) fiatTotalVal.textContent = isNetWorthVisible ? originalFiat : '••••••••';
            if (cryptoTotalVal) cryptoTotalVal.textContent = isNetWorthVisible ? originalCrypto : '••••••••';
            balanceVals.forEach((el, idx) => {
              el.textContent = isNetWorthVisible ? originalBalances[idx] : '••••••••';
            });
            toggleWalletBalanceBtn.classList.toggle('text-gray-400', isNetWorthVisible);
            toggleWalletBalanceBtn.classList.toggle('text-black', !isNetWorthVisible);
          });
        }

        // 3. Filter tabs (All, Fiat, Crypto)
        const filterBtns = document.querySelectorAll('.wallet-filter-btn');
        const getWalletCards = () => document.querySelectorAll('#walletsGrid .wallet-card');
        const walletsSectionTitle = document.getElementById('walletsSectionTitle');

        filterBtns.forEach(btn => {
          btn.addEventListener('click', () => {
            // Reset all buttons
            filterBtns.forEach(b => {
              b.classList.remove('bg-[#0b2545]', 'text-white', 'shadow-xs', 'font-bold');
              b.classList.add('text-gray-600', 'font-semibold');
              const badge = b.querySelector('.wallet-filter-badge');
              if (badge) {
                badge.className = 'wallet-filter-badge px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-300/60 text-gray-600 leading-none';
              }
            });

            // Activate clicked button
            btn.classList.remove('text-gray-600', 'font-semibold');
            btn.classList.add('bg-[#0b2545]', 'text-white', 'shadow-xs', 'font-bold');
            const activeBadge = btn.querySelector('.wallet-filter-badge');
            if (activeBadge) {
              activeBadge.className = 'wallet-filter-badge px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-white/20 text-white leading-none';
            }

            const filter = btn.getAttribute('data-filter');
            if (walletsSectionTitle) {
              const count = btn.querySelector('.wallet-filter-badge')?.textContent || '0';
              if (filter === 'fiat') walletsSectionTitle.textContent = 'Fiat Currency Accounts (' + count + ')';
              else if (filter === 'crypto') walletsSectionTitle.textContent = 'Cryptocurrency Assets (' + count + ')';
              else walletsSectionTitle.textContent = 'Active Currency Balances (' + count + ')';
            }

            getWalletCards().forEach(card => {
              const cardType = card.getAttribute('data-type');
              if (filter === 'all' || cardType === filter) {
                card.style.display = 'flex';
              } else {
                card.style.display = 'none';
              }
            });
          });
        });

        // 4. Live Search Filter
        const searchInput = document.getElementById('walletSearchInput');
        if (searchInput) {
          searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            getWalletCards().forEach(card => {
              const text = card.textContent.toLowerCase();
              if (text.includes(query)) {
                card.style.display = 'flex';
              } else {
                card.style.display = 'none';
              }
            });
          });
        }

        // 5. Receive and add-wallet side panels
        const walletPanelBackdrop = document.getElementById('walletPanelBackdrop');
        const receiveWalletPanel = document.getElementById('receiveWalletPanel');
        const addWalletPanel = document.getElementById('addWalletPanel');
        const openWalletPanel = (panel) => {
          if (!walletPanelBackdrop || !panel) return;
          walletPanelBackdrop.classList.remove('hidden');
          requestAnimationFrame(() => panel.classList.remove('translate-x-full'));
          panel.setAttribute('aria-hidden', 'false');
          window.refreshIcons?.();
        };
        const closeWalletPanels = () => {
          [receiveWalletPanel, addWalletPanel].forEach((panel) => {
            if (panel) { panel.classList.add('translate-x-full'); panel.setAttribute('aria-hidden', 'true'); }
          });
          setTimeout(() => walletPanelBackdrop?.classList.add('hidden'), 300);
        };
        document.getElementById('addWalletBtn')?.addEventListener('click', () => openWalletPanel(addWalletPanel));
        document.addEventListener('click', (event) => {
          const receive = event.target.closest?.('[data-wallet-receive]');
          const send = event.target.closest?.('[data-wallet-send]');
          if (receive) openWalletPanel(receiveWalletPanel);
          if (send) window.location.href = `send-money.php?currency=${encodeURIComponent(send.dataset.walletSend)}`;
        });
        document.querySelectorAll('[data-close-wallet-panel]').forEach((button) => button.addEventListener('click', closeWalletPanels));
        walletPanelBackdrop?.addEventListener('click', closeWalletPanels);
        document.querySelectorAll('.deposit-tab').forEach((tab) => tab.addEventListener('click', () => {
          const selected = tab.dataset.depositTab;
          document.querySelectorAll('.deposit-tab').forEach((item) => {
            const active = item === tab;
            item.classList.toggle('bg-white', active); item.classList.toggle('dark:bg-[#102641]', active);
            item.classList.toggle('text-[#0b2545]', active); item.classList.toggle('dark:text-white', active); item.classList.toggle('shadow-sm', active);
            item.classList.toggle('text-gray-500', !active); item.classList.toggle('dark:text-gray-300', !active);
          });
          document.querySelectorAll('.deposit-content').forEach((content) => content.classList.toggle('hidden', content.id !== `deposit${selected.charAt(0).toUpperCase() + selected.slice(1)}Content`));
        }));
        document.querySelectorAll('[data-panel-copy]').forEach((button) => button.addEventListener('click', async () => {
          try { await navigator.clipboard.writeText(button.dataset.panelCopy); const label = button.textContent; button.textContent = 'Copied'; setTimeout(() => button.textContent = label, 1200); } catch (error) { alert('Copy this value: ' + button.dataset.panelCopy); }
        }));
        document.querySelectorAll('.wallet-option').forEach((option) => option.addEventListener('click', () => {
          const code = option.dataset.walletOption;
          option.disabled = true;
          option.classList.add('border-emerald-400', 'bg-emerald-50', 'dark:bg-emerald-950/30');
          option.querySelector('small').textContent = `${code} · Wallet added`;
          const feedback = document.getElementById('addWalletFeedback');
          feedback.textContent = `${code} wallet added. You can now receive funds into it.`;
          feedback.classList.remove('hidden');
        }));

        // 6. Transaction Type Filter in Ledger Table
        const txTypeFilter = document.getElementById('txTypeFilter');
        if (txTypeFilter) {
          txTypeFilter.addEventListener('change', (e) => {
            const selected = e.target.value;
            const rows = document.querySelectorAll('tbody tr[data-type]');
            rows.forEach(row => {
              if (selected === 'all' || row.getAttribute('data-type') === selected) {
                row.style.display = '';
              } else {
                row.style.display = 'none';
              }
            });
          });
        }
      });
    </script>

    <script>
      (() => {
        const grid=document.getElementById('walletsGrid'), total=document.getElementById('cryptoTotalVal'), breakdown=document.getElementById('cryptoBreakdown');
        const updateUsdtReference=()=>{
          const card=grid?.querySelector('.wallet-card[data-symbol="USDT"]');
          if(!card){ if(total) total.textContent='—'; if(breakdown) breakdown.innerHTML='<span class="w-2.5 h-2.5 rounded-full bg-sky-400"></span> Crypto: — USDT'; return; }
          const raw=card.querySelector('.wallet-balance-val')?.textContent||''; const amount=Number(raw.replace(/[^0-9.-]/g,''));
          if(!Number.isFinite(amount)){if(total) total.textContent='—';return;}
          const display=`₮${amount.toLocaleString('en-NG',{minimumFractionDigits:2,maximumFractionDigits:8})}`;
          if(total) total.textContent=display; if(breakdown) breakdown.innerHTML=`<span class="w-2.5 h-2.5 rounded-full bg-sky-400"></span> Crypto: ${display}`;
        };
        if(grid){new MutationObserver(updateUsdtReference).observe(grid,{childList:true}); updateUsdtReference();}
      })();
    </script>

    <script>
      (() => {
        const token = () => localStorage.getItem('flow_token') || '';
        const money = (value, code) => { try { return new Intl.NumberFormat('en-NG', {style:'currency', currency:code, maximumFractionDigits:8}).format(Number(value || 0)); } catch (_) { return `${Number(value || 0).toLocaleString()} ${code}`; } };
        let walletData = [];
        const loadWallets = async () => {
          try {
            const response = await fetch('../api/wallets/list.php', {headers:{Authorization:`Bearer ${token()}`,Accept:'application/json'}}); const result = await response.json(); if(!response.ok || !result.success) throw new Error(result.error || 'Unable to load wallets.'); walletData=result.wallets||[]; const esc=value=>String(value??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c])); const grid=document.getElementById('walletsGrid'); if(grid){ grid.innerHTML=walletData.map(wallet=>`<article class="wallet-card bg-white rounded-3xl p-5 border border-gray-200/80 shadow-xs flex flex-col justify-between gap-4" data-type="${esc(wallet.kind)}" data-symbol="${esc(wallet.currency_code)}"><div><div class="flex items-center justify-between"><div><h3 class="text-sm font-bold text-gray-900">${esc(wallet.name)}</h3><span class="text-[10px] text-gray-400">${esc(wallet.currency_code)} · ${wallet.kind==='crypto'?'Crypto wallet':'Fiat wallet'}</span></div><span class="grid h-8 w-8 place-items-center rounded-full bg-sky-50 text-xs font-bold text-sky-700">${esc(wallet.currency_code.slice(0,2))}</span></div><div class="mt-4"><div class="text-xl font-extrabold text-gray-900 tracking-tight wallet-balance-val">${esc(money(wallet.available_balance,wallet.currency_code))}</div><div class="text-[11px] text-gray-400 mt-1">Pending: ${esc(money(wallet.pending_balance,wallet.currency_code))}</div>${wallet.account_number?`<div class="text-[11px] text-gray-400 font-mono mt-2">Account: ••••${esc(wallet.account_number.slice(-4))}</div>`:''}</div></div><div class="flex items-center gap-2 pt-3 border-t border-gray-100"><button type="button" class="flex-1 py-2 rounded-full border border-gray-200 text-gray-700 text-[11px] font-semibold" data-wallet-receive="${esc(wallet.currency_code)}">Receive</button><button type="button" class="flex-1 py-2 rounded-full bg-[#0b2545] text-white text-[11px] font-semibold" data-wallet-send="${esc(wallet.currency_code)}">Send</button></div></article>`).join(''); grid.style.visibility='visible'; }
            const fiat=walletData.filter(wallet=>wallet.kind==='fiat'), crypto=walletData.filter(wallet=>wallet.kind==='crypto'); const counts={all:walletData.length,fiat:fiat.length,crypto:crypto.length}; document.querySelector('[data-filter=all] .wallet-filter-badge').textContent=counts.all; document.querySelector('[data-filter=fiat] .wallet-filter-badge').textContent=counts.fiat; document.querySelector('[data-filter=crypto] .wallet-filter-badge').textContent=counts.crypto; document.getElementById('activeWalletCount').textContent=`${walletData.length} Active Balances`; document.getElementById('fiatWalletCount').textContent=`${fiat.length} Wallets`; document.getElementById('cryptoWalletCount').textContent=`${crypto.length} Assets`; document.getElementById('fiatWalletCodes').textContent=fiat.length?fiat.map(wallet=>wallet.currency_code).join(', '):'No fiat wallets yet'; document.getElementById('cryptoWalletCodes').textContent=crypto.length?crypto.map(wallet=>wallet.currency_code).join(', '):'No crypto wallets yet'; document.getElementById('walletsSectionTitle').textContent=`Active Currency Balances (${walletData.length})`;
            walletData.forEach(wallet => { const card=document.querySelector(`.wallet-card[data-symbol="${wallet.currency_code}"]`); if(!card) return; const balance=card.querySelector('.wallet-balance-val'); if(balance) balance.textContent=money(wallet.available_balance,wallet.currency_code); });
            const ngn=walletData.find(wallet=>wallet.currency_code==='NGN'); const name=document.getElementById('walletReceiveAccountName'), number=document.getElementById('walletReceiveAccountNumber'), bank=document.getElementById('walletReceiveBank'), copy=document.getElementById('walletReceiveCopy'); if(ngn?.account_number){name.textContent=ngn.account_name||'Flowwithlit account'; number.textContent=ngn.account_number; bank.textContent=ngn.bank_name||'Assigned bank'; copy.dataset.panelCopy=ngn.account_number;} else if(name){name.textContent='No receiving account yet'; number.textContent='Click Receive to create'; bank.textContent='OnePipe';}
            const options=document.getElementById('newWalletOptions'); if(options && result.currencies){ const owned=new Set(walletData.map(wallet=>wallet.currency_code)); options.innerHTML=result.currencies.map(currency=>owned.has(currency.code)?`<button type="button" disabled class="w-full flex items-center justify-between rounded-2xl border border-gray-100 bg-gray-50 p-4 text-left opacity-60"><span><strong class="block text-gray-600">${currency.name}</strong><small class="text-gray-400">${currency.code} · Already active</small></span><span class="rounded-full bg-emerald-100 px-2 py-1 text-[10px] font-bold text-emerald-700">Added</span></button>`:`<button type="button" data-wallet-option="${currency.code}" class="wallet-option w-full flex items-center justify-between rounded-2xl border border-gray-200 p-4 text-left hover:border-sky-400"><span><strong class="block text-gray-900">${currency.name}</strong><small class="text-gray-400">${currency.code} · ${currency.kind==='crypto'?'Crypto':'Fiat'} wallet</small></span><span class="text-xl">+</span></button>`).join(''); options.querySelectorAll('.wallet-option').forEach(option=>option.addEventListener('click',()=>{option.disabled=true; option.querySelector('small').textContent=`${option.dataset.walletOption} · Activation requested`; document.getElementById('addWalletFeedback').textContent='Wallet activation has been queued. It will appear after provider setup is complete.'; document.getElementById('addWalletFeedback').classList.remove('hidden');})); }
          } catch(error) { const grid=document.getElementById('walletsGrid'); if(grid){grid.innerHTML='<div class="col-span-full rounded-2xl border border-amber-200 bg-amber-50 p-6 text-center text-sm text-amber-800">Wallet balances are unavailable right now.</div>'; grid.style.visibility='visible';} }
        };
        document.getElementById('depositBtn')?.addEventListener('click', async () => { const ngn=walletData.find(wallet=>wallet.currency_code==='NGN'); if(ngn?.account_number) return; try { const response=await fetch('../api/wallets/provision-fiat.php',{method:'POST',headers:{'Content-Type':'application/json',Authorization:`Bearer ${token()}`},body:JSON.stringify({currency:'NGN'})}); const result=await response.json(); if(result.success&&result.account){document.getElementById('walletReceiveAccountName').textContent=result.account.account_name;document.getElementById('walletReceiveAccountNumber').textContent=result.account.account_number;document.getElementById('walletReceiveBank').textContent=result.account.bank_name;document.getElementById('walletReceiveCopy').dataset.panelCopy=result.account.account_number;} } catch(error) { const status=document.getElementById('walletReceiveStatus'); if(status) status.textContent='Receiving account is unavailable right now.'; } });
        loadWallets(); window.addEventListener('flowwithlit:dashboard-refresh',loadWallets); document.addEventListener('visibilitychange',()=>{if(!document.hidden)loadWallets();});
      })();
    </script>

    <script>
      document.addEventListener('click', async (event) => {
        const option = event.target.closest?.('.wallet-option'); if (!option || option.disabled) return;
        event.preventDefault(); event.stopImmediatePropagation(); option.disabled = true;
        try { const response = await fetch('../api/wallets/activate.php', {method:'POST', headers:{'Content-Type':'application/json', Authorization:`Bearer ${localStorage.getItem('flow_token') || ''}`}, body:JSON.stringify({currency:option.dataset.walletOption})}); const result=await response.json(); if(!response.ok || !result.success) throw new Error(result.error || 'Wallet activation failed.'); option.querySelector('small').textContent=`${option.dataset.walletOption} · Wallet active`; const feedback=document.getElementById('addWalletFeedback'); feedback.textContent=`${option.dataset.walletOption} wallet is active.`; feedback.classList.remove('hidden'); window.dispatchEvent(new Event('flowwithlit:dashboard-refresh')); } catch(error) { option.disabled=false; const feedback=document.getElementById('addWalletFeedback'); feedback.textContent=error.message; feedback.classList.remove('hidden'); }
      }, true);
      document.querySelectorAll('.wallet-filter-btn').forEach(button => button.addEventListener('click', () => { const filter=button.dataset.filter; document.querySelectorAll('#walletsGrid .wallet-card').forEach(card => { card.style.display = filter==='all' || card.dataset.type===filter ? 'flex' : 'none'; }); }));
      document.getElementById('walletSearchInput')?.addEventListener('input', event => { const query=event.target.value.toLowerCase().trim(); document.querySelectorAll('#walletsGrid .wallet-card').forEach(card => { card.style.display=card.textContent.toLowerCase().includes(query)?'flex':'none'; }); });
    </script>

    <script>
      (() => {
        const esc=value=>String(value??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
        const money=(value,code)=>{try{return new Intl.NumberFormat('en-NG',{style:'currency',currency:code,maximumFractionDigits:8}).format(Number(value||0));}catch(_){return `${Number(value||0).toLocaleString()} ${code}`;}};
        const date=value=>value?new Date(String(value).replace(' ','T')+'Z').toLocaleString('en-NG',{dateStyle:'medium',timeStyle:'short'}):'—';
        const statusLabel=value=>({successful:'Completed',pending:'Pending',failed:'Failed',refunded:'Refunded'}[value]||value);
        const loadHistory=async()=>{const body=document.getElementById('walletHistoryBody'); try {const token=localStorage.getItem('flow_token')||''; const response=await fetch('../api/transactions/list.php?page=1&page_size=50&range=all',{headers:{Authorization:`Bearer ${token}`,Accept:'application/json'}}); const result=await response.json(); if(!response.ok||!result.success) throw new Error(result.error||'Unable to load transaction history.'); const rows=result.transactions||[]; body.innerHTML=rows.length?rows.map(row=>`<tr class="hover:bg-gray-50/70 transition" data-type="${esc(row.type)}"><td class="py-4 px-4 whitespace-nowrap"><div class="font-bold text-gray-900">${esc(row.currency)}</div><div class="text-[10px] text-gray-400">${esc(row.channel||'Wallet')}</div></td><td class="py-4 px-4 whitespace-nowrap"><div class="font-bold text-gray-900">${esc(row.type.charAt(0).toUpperCase()+row.type.slice(1))}</div><div class="text-[10px] text-gray-400 font-mono">${esc(row.reference)}</div></td><td class="py-4 px-4 whitespace-nowrap"><div class="text-gray-900 font-medium">${esc(date(row.created_at))}</div></td><td class="py-4 px-4 whitespace-nowrap"><div class="text-gray-900 font-medium">${esc(row.customer_name||row.provider||'—')}</div><div class="text-[10px] text-gray-400">${esc(row.customer_email||'')}</div></td><td class="py-4 px-4 text-right whitespace-nowrap"><div class="font-extrabold ${row.type==='deposit'||row.type==='refund'?'text-emerald-600':'text-gray-900'} text-sm">${row.type==='deposit'||row.type==='refund'?'+':'-'}${esc(money(row.amount,row.currency))}</div><div class="text-[10px] text-gray-400">Fee: ${esc(money(row.fee,row.currency))}</div></td><td class="py-4 px-4 text-center whitespace-nowrap"><span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold ${row.status==='successful'?'bg-emerald-50 text-emerald-700 border border-emerald-200/60':row.status==='pending'?'bg-amber-50 text-amber-700 border border-amber-200/60':'bg-rose-50 text-rose-700 border border-rose-200/60'}">${esc(statusLabel(row.status))}</span></td><td class="py-4 px-4 text-right whitespace-nowrap"><a class="text-xs font-semibold text-[#0b2545] hover:underline" href="transactions.php">Details</a></td></tr>`).join(''):'<tr><td colspan="7" class="py-16 text-center text-sm text-gray-400">No wallet transactions yet.</td></tr>'; body.style.visibility='visible'; document.getElementById('walletHistorySummary').textContent=rows.length?`Showing 1 to ${rows.length} of ${result.pagination?.total_count||rows.length} transactions`:'No transactions yet'; document.getElementById('txTypeFilter')?.addEventListener('change',event=>{const type=event.target.value; body.querySelectorAll('tr[data-type]').forEach(row=>row.style.display=type==='all'||row.dataset.type===type?'':'none');});}catch(error){body.innerHTML=`<tr><td colspan="7" class="py-16 text-center text-sm text-amber-700">${esc(error.message)}</td></tr>`; body.style.visibility='visible'; document.getElementById('walletHistorySummary').textContent='Transaction history unavailable';}}; loadHistory(); window.addEventListener('flowwithlit:dashboard-refresh',loadHistory); document.addEventListener('visibilitychange',()=>{if(!document.hidden)loadHistory();});
      })();
    </script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
