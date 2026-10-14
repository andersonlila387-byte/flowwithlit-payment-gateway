<?php
/**
 * Flowwithlit Crypto & Naira Exchange Page
 * Two-Column Top: Instant Conversion Widget & Live Market Rates / Wallets
 * Full-Width Bottom: Converted Transaction History
 */
$pageTitle = 'Crypto Exchange — Flowwithlit';
$activePage = 'exchange';
$activeNavTab = 'Features';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

    <!-- Scrollable Main Content Area -->
    <main class="flex-1 h-full overflow-y-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-6 flex flex-col gap-6">
      
      <!-- Top Title & Live Rate Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 flex-shrink-0">
        <div>
          <div class="flex items-center gap-2">
            <span class="text-xs text-gray-400 font-medium">OTC Liquidity &amp; Instant Swap</span>
            <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-2 py-0.5 rounded-full flex items-center gap-1">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
              Live rates unavailable
            </span>
          </div>
          <h1 class="text-2xl font-bold text-gray-900 tracking-tight mt-0.5">Crypto &amp; Naira Exchange</h1>
        </div>

        <!-- Action Icons: Refresh & Quick Info -->
        <div class="flex items-center gap-2">
          <button type="button" onclick="refreshCryptoRates()" class="p-2 rounded-xl bg-white hover:bg-gray-50 border border-gray-200/80 text-gray-600 hover:text-[#0b2545] transition shadow-2xs" title="Refresh Live Rates">
            <svg id="refreshIcon" class="w-4 h-4 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
          </button>
        </div>
      </div>

      <!-- ================= TOP SECTION: TWO COLUMNS ================= -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        
        <!-- COLUMN 1: Instant Conversion Box -->
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 flex flex-col gap-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-[#0b2545]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
              </div>
              <h2 class="text-sm font-bold text-gray-900">Instant Convert</h2>
            </div>
            <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60">0.00% Fee</span>
          </div>

          <!-- Mode Tabs: Sell Crypto vs Buy Crypto -->
          <div class="grid grid-cols-2 p-1 bg-gray-100 rounded-xl">
            <button type="button" id="tabSellCrypto" onclick="setExchangeDirection('crypto_to_ngn')" class="py-2 text-xs font-bold rounded-lg bg-[#0b2545] text-white shadow-xs transition flex items-center justify-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
              <span>Sell Crypto to ₦</span>
            </button>
            <button type="button" id="tabBuyCrypto" onclick="setExchangeDirection('ngn_to_crypto')" class="py-2 text-xs font-semibold text-gray-600 hover:text-gray-900 rounded-lg transition flex items-center justify-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
              <span>Buy Crypto with ₦</span>
            </button>
          </div>

          <!-- Input 1: You Send -->
          <div class="bg-gray-50 border border-gray-200/80 rounded-xl p-3.5 focus-within:bg-white focus-within:border-[#0b2545] transition">
            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
              <span class="font-semibold text-gray-700" id="sendLabel">You Send (Crypto)</span>
              <div class="flex items-center gap-1">
                <span class="text-[11px]">Bal: <span class="font-mono font-bold text-gray-900" id="sourceBalDisplay">—</span></span>
                <button type="button" onclick="setMaxSourceAmount()" class="text-[10px] font-bold uppercase text-[#0b2545] bg-blue-50 px-1 rounded hover:underline">Max</button>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <input type="number" id="sendAmount" value="" min="0" step="any" oninput="calculateSwap()" placeholder="0.00" class="w-full text-xl font-black text-gray-900 bg-transparent focus:outline-hidden">
              
              <!-- Source Currency Selector -->
              <select id="sourceToken" onchange="calculateSwap()" class="shrink-0 px-2.5 py-1.5 rounded-lg bg-white border border-gray-200 text-xs font-bold text-gray-800 shadow-2xs focus:outline-hidden cursor-pointer">
                <option value="USDT" selected>₮ USDT</option>
              </select>
            </div>
          </div>

          <!-- Flip Button -->
          <div class="flex items-center justify-center -my-2 relative z-10">
            <button type="button" onclick="toggleDirection()" class="w-9 h-9 rounded-full bg-white border border-gray-200 shadow-sm hover:bg-[#0b2545] text-gray-600 hover:text-white flex items-center justify-center transition active:scale-95" title="Swap Direction">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
            </button>
          </div>

          <!-- Input 2: You Receive -->
          <div class="bg-gray-50 border border-gray-200/80 rounded-xl p-3.5 focus-within:bg-white focus-within:border-[#0b2545] transition">
            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
              <span class="font-semibold text-gray-700" id="receiveLabel">You Receive (Direct to Wallet)</span>
              <span class="text-[11px]">Bal: <span class="font-mono font-bold text-gray-900" id="destBalDisplay">—</span></span>
            </div>
            <div class="flex items-center gap-2">
              <input type="text" id="receiveAmount" readonly value="—" class="w-full text-xl font-black text-emerald-700 bg-transparent focus:outline-hidden">
              
              <!-- Dest Token Display / Selector -->
              <div id="destCurrencyBadge" class="shrink-0 px-2.5 py-1.5 rounded-lg bg-white border border-gray-200 text-xs font-bold text-gray-800 shadow-2xs flex items-center gap-1">
                <span>🇳🇬 NGN (₦)</span>
              </div>
            </div>
          </div>

          <!-- Rate Details Strip -->
          <div class="p-3 bg-gray-50/80 border border-gray-100 rounded-xl text-xs space-y-1.5">
            <div class="flex items-center justify-between">
              <span class="text-gray-500">Effective Rate</span>
              <span class="font-mono font-bold text-gray-900" id="currentRateDisplay">Rate unavailable</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-gray-500">Settlement Speed</span>
              <span class="font-semibold text-emerald-700 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Instant to Wallet
              </span>
            </div>
          </div>

          <!-- Submit Button -->
          <button type="button" onclick="openConfirmSwapModal()" class="w-full py-3 bg-[#0b2545] hover:bg-[#134074] text-white rounded-xl font-bold text-xs shadow-sm transition active:scale-95 flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            <span id="ctaButtonText">Convert to Naira</span>
          </button>
        </div>

        <!-- COLUMN 2: Live Market Rates & Crypto Balances -->
        <div class="flex flex-col gap-4">
          
          <!-- Live Market Rates Watchlist -->
          <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-700">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Live Rates to Naira</h3>
              </div>
              <span class="text-[11px] text-gray-400">Updated Real-Time</span>
            </div>

            <!-- Rates List -->
            <div class="divide-y divide-gray-100 text-xs">
              
              <!-- USDT -->
              <div class="py-2.5 flex items-center justify-between hover:bg-gray-50/50 px-2 rounded-lg cursor-pointer transition" onclick="selectCrypto('USDT')">
                <div class="flex items-center gap-2.5">
                  <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-800 font-black text-xs flex items-center justify-center">₮</div>
                  <div>
                    <div class="font-bold text-gray-900">Tether USD</div>
                    <div class="text-[10px] text-gray-400">USDT / NGN</div>
                  </div>
                </div>
                <div class="flex items-center gap-3">
                  <div class="text-right">
                    <div class="font-mono font-bold text-gray-900 rate-value" data-rate-token="USDT">—</div>
                    <div class="text-[10px] text-gray-400 font-semibold rate-change" data-rate-token="USDT">Unavailable</div>
                  </div>
                  <button type="button" class="p-1 rounded-md text-gray-400 hover:text-[#0b2545] hover:bg-gray-100" title="Select USDT">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                  </button>
                </div>
              </div>

              <!-- BTC -->
              <div class="py-2.5 flex items-center justify-between hover:bg-gray-50/50 px-2 rounded-lg cursor-pointer transition" onclick="selectCrypto('BTC')">
                <div class="flex items-center gap-2.5">
                  <div class="w-7 h-7 rounded-full bg-amber-100 text-amber-800 font-black text-xs flex items-center justify-center">₿</div>
                  <div>
                    <div class="font-bold text-gray-900">Bitcoin</div>
                    <div class="text-[10px] text-gray-400">BTC / NGN</div>
                  </div>
                </div>
                <div class="flex items-center gap-3">
                  <div class="text-right">
                    <div class="font-mono font-bold text-gray-900 rate-value" data-rate-token="BTC">—</div>
                    <div class="text-[10px] text-gray-400 font-semibold rate-change" data-rate-token="BTC">Unavailable</div>
                  </div>
                  <button type="button" class="p-1 rounded-md text-gray-400 hover:text-[#0b2545] hover:bg-gray-100" title="Select BTC">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                  </button>
                </div>
              </div>

              <!-- ETH -->
              <div class="py-2.5 flex items-center justify-between hover:bg-gray-50/50 px-2 rounded-lg cursor-pointer transition" onclick="selectCrypto('ETH')">
                <div class="flex items-center gap-2.5">
                  <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-800 font-black text-xs flex items-center justify-center">Ξ</div>
                  <div>
                    <div class="font-bold text-gray-900">Ethereum</div>
                    <div class="text-[10px] text-gray-400">ETH / NGN</div>
                  </div>
                </div>
                <div class="flex items-center gap-3">
                  <div class="text-right">
                    <div class="font-mono font-bold text-gray-900 rate-value" data-rate-token="ETH">—</div>
                    <div class="text-[10px] text-gray-400 font-semibold rate-change" data-rate-token="ETH">Unavailable</div>
                  </div>
                  <button type="button" class="p-1 rounded-md text-gray-400 hover:text-[#0b2545] hover:bg-gray-100" title="Select ETH">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                  </button>
                </div>
              </div>

              <!-- SOL -->
              <div class="py-2.5 flex items-center justify-between hover:bg-gray-50/50 px-2 rounded-lg cursor-pointer transition" onclick="selectCrypto('SOL')">
                <div class="flex items-center gap-2.5">
                  <div class="w-7 h-7 rounded-full bg-purple-100 text-purple-800 font-black text-xs flex items-center justify-center">◎</div>
                  <div>
                    <div class="font-bold text-gray-900">Solana</div>
                    <div class="text-[10px] text-gray-400">SOL / NGN</div>
                  </div>
                </div>
                <div class="flex items-center gap-3">
                  <div class="text-right">
                    <div class="font-mono font-bold text-gray-900 rate-value" data-rate-token="SOL">—</div>
                    <div class="text-[10px] text-gray-400 font-semibold rate-change" data-rate-token="SOL">Unavailable</div>
                  </div>
                  <button type="button" class="p-1 rounded-md text-gray-400 hover:text-[#0b2545] hover:bg-gray-100" title="Select SOL">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                  </button>
                </div>
              </div>

            </div>
          </div>

          <!-- Crypto & Naira Balances Card -->
          <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-[#0b2545]">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Available Balances</h3>
              </div>
              <span class="text-[11px] font-mono font-bold text-gray-800" id="ngnBalanceSummary">— NGN</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
              <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                <div class="text-[10px] font-bold text-gray-400 uppercase">USDT</div>
                <div class="font-mono font-black text-gray-900 text-xs mt-0.5" id="cardBalUSDT">—</div>
              </div>
              <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                <div class="text-[10px] font-bold text-gray-400 uppercase">BTC</div>
                <div class="font-mono font-black text-gray-900 text-xs mt-0.5" id="cardBalBTC">—</div>
              </div>
              <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                <div class="text-[10px] font-bold text-gray-400 uppercase">ETH</div>
                <div class="font-mono font-black text-gray-900 text-xs mt-0.5" id="cardBalETH">—</div>
              </div>
              <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                <div class="text-[10px] font-bold text-gray-400 uppercase">SOL</div>
                <div class="font-mono font-black text-gray-900 text-xs mt-0.5" id="cardBalSOL">—</div>
              </div>
            </div>
          </div>

        </div>

      </div>

      <!-- ================= BOTTOM SECTION: FULL-WIDTH TRANSACTION HISTORY ================= -->
      <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden flex flex-col w-full">
        <div class="p-4 sm:px-6 border-b border-gray-100 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center text-gray-700">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
              <h3 class="text-sm font-bold text-gray-900">Exchange Transaction History</h3>
              <p class="text-[11px] text-gray-400">Past conversions between Crypto and Nigerian Naira</p>
            </div>
          </div>
          <span class="text-xs font-semibold text-gray-500 bg-gray-50 border border-gray-200/70 px-2.5 py-1 rounded-lg">8 Completed</span>
        </div>

        <div class="w-full overflow-x-auto">
          <table class="w-full text-left border-collapse min-w-[760px] min-h-[480px]">
            <thead>
              <tr class="border-b border-gray-100 text-[11px] font-bold uppercase tracking-wider text-gray-400 bg-gray-50/40">
                <th class="py-3 px-5">Pair</th>
                <th class="py-3 px-5">Reference</th>
                <th class="py-3 px-5">Sold Amount</th>
                <th class="py-3 px-5">Received Amount</th>
                <th class="py-3 px-5">Rate</th>
                <th class="py-3 px-5 text-center">Status</th>
                <th class="py-3 px-5">Date</th>
                <th class="py-3 px-5 text-right">Receipt</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-xs" id="exchangeHistoryBody">
              
              <!-- Row 1 -->
              <tr class="hover:bg-blue-50/20 transition">
                <td class="py-5 px-6 font-bold text-gray-900 flex items-center gap-1.5">
                  <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black flex items-center justify-center">₮</span>
                  <span>&rarr;</span>
                  <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-black flex items-center justify-center">₦</span>
                  <span>USDT / NGN</span>
                </td>
                <td class="py-5 px-6 font-mono text-gray-500 text-[11px]">
                  <div class="flex items-center gap-1">
                    <span>EXC_9401928</span>
                    <button type="button" onclick="navigator.clipboard.writeText('EXC_9401928'); showToast('Reference copied');" class="text-gray-400 hover:text-[#0b2545]" title="Copy Reference">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                </td>
                <td class="py-5 px-6 font-mono font-bold text-rose-600">-500.00 USDT</td>
                <td class="py-5 px-6 font-mono font-bold text-emerald-700">+₦742,500.00</td>
                <td class="py-5 px-6 font-mono text-gray-600">₦1,485.00</td>
                <td class="py-5 px-6 text-center">
                  <span class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center" title="Completed">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-gray-500 text-[11px]">18 Sep 2026, 01:10 PM</td>
                <td class="py-5 px-6 text-right">
                  <button onclick="openReceipt('EXC_9401928', '500.00 USDT', '₦742,500.00 NGN', '₦1,485.00', '18 Sep 2026, 01:10 PM')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Receipt">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                  </button>
                </td>
              </tr>

              <!-- Row 2 -->
              <tr class="hover:bg-blue-50/20 transition">
                <td class="py-5 px-6 font-bold text-gray-900 flex items-center gap-1.5">
                  <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-black flex items-center justify-center">₦</span>
                  <span>&rarr;</span>
                  <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black flex items-center justify-center">₮</span>
                  <span>NGN / USDT</span>
                </td>
                <td class="py-5 px-6 font-mono text-gray-500 text-[11px]">
                  <div class="flex items-center gap-1">
                    <span>EXC_8830192</span>
                    <button type="button" onclick="navigator.clipboard.writeText('EXC_8830192'); showToast('Reference copied');" class="text-gray-400 hover:text-[#0b2545]" title="Copy Reference">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                </td>
                <td class="py-5 px-6 font-mono font-bold text-rose-600">-₦1,000,000.00</td>
                <td class="py-5 px-6 font-mono font-bold text-emerald-700">+671.14 USDT</td>
                <td class="py-5 px-6 font-mono text-gray-600">₦1,490.00</td>
                <td class="py-5 px-6 text-center">
                  <span class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center" title="Completed">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-gray-500 text-[11px]">17 Sep 2026, 04:30 PM</td>
                <td class="py-5 px-6 text-right">
                  <button onclick="openReceipt('EXC_8830192', '₦1,000,000.00 NGN', '671.14 USDT', '₦1,490.00', '17 Sep 2026, 04:30 PM')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Receipt">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                  </button>
                </td>
              </tr>

              <!-- Row 3 -->
              <tr class="hover:bg-blue-50/20 transition">
                <td class="py-5 px-6 font-bold text-gray-900 flex items-center gap-1.5">
                  <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black flex items-center justify-center">₿</span>
                  <span>&rarr;</span>
                  <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-black flex items-center justify-center">₦</span>
                  <span>BTC / NGN</span>
                </td>
                <td class="py-5 px-6 font-mono text-gray-500 text-[11px]">
                  <div class="flex items-center gap-1">
                    <span>EXC_7749201</span>
                    <button type="button" onclick="navigator.clipboard.writeText('EXC_7749201'); showToast('Reference copied');" class="text-gray-400 hover:text-[#0b2545]" title="Copy Reference">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                </td>
                <td class="py-5 px-6 font-mono font-bold text-rose-600">-0.05 BTC</td>
                <td class="py-5 px-6 font-mono font-bold text-emerald-700">+₦4,910,000.00</td>
                <td class="py-5 px-6 font-mono text-gray-600">₦98,200,000.00</td>
                <td class="py-5 px-6 text-center">
                  <span class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center" title="Completed">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-gray-500 text-[11px]">16 Sep 2026, 11:20 AM</td>
                <td class="py-5 px-6 text-right">
                  <button onclick="openReceipt('EXC_7749201', '0.05 BTC', '₦4,910,000.00 NGN', '₦98,200,000.00', '16 Sep 2026, 11:20 AM')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Receipt">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                  </button>
                </td>
              </tr>

              <!-- Row 4 -->
              <tr class="hover:bg-blue-50/20 transition">
                <td class="py-5 px-6 font-bold text-gray-900 flex items-center gap-1.5">
                  <span class="w-5 h-5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-black flex items-center justify-center">◎</span>
                  <span>&rarr;</span>
                  <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-black flex items-center justify-center">₦</span>
                  <span>SOL / NGN</span>
                </td>
                <td class="py-5 px-6 font-mono text-gray-500 text-[11px]">
                  <div class="flex items-center gap-1">
                    <span>EXC_6520931</span>
                    <button type="button" onclick="navigator.clipboard.writeText('EXC_6520931'); showToast('Reference copied');" class="text-gray-400 hover:text-[#0b2545]" title="Copy Reference">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                </td>
                <td class="py-5 px-6 font-mono font-bold text-rose-600">-10.00 SOL</td>
                <td class="py-5 px-6 font-mono font-bold text-emerald-700">+₦2,140,000.00</td>
                <td class="py-5 px-6 font-mono text-gray-600">₦214,000.00</td>
                <td class="py-5 px-6 text-center">
                  <span class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center" title="Completed">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-gray-500 text-[11px]">15 Sep 2026, 06:45 PM</td>
                <td class="py-5 px-6 text-right">
                  <button onclick="openReceipt('EXC_6520931', '10.00 SOL', '₦2,140,000.00 NGN', '₦214,000.00', '15 Sep 2026, 06:45 PM')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Receipt">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                  </button>
                </td>
              </tr>

              <!-- Row 5 -->
              <tr class="hover:bg-blue-50/20 transition">
                <td class="py-5 px-6 font-bold text-gray-900 flex items-center gap-1.5">
                  <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-black flex items-center justify-center">Ξ</span>
                  <span>&rarr;</span>
                  <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-black flex items-center justify-center">₦</span>
                  <span>ETH / NGN</span>
                </td>
                <td class="py-5 px-6 font-mono text-gray-500 text-[11px]">
                  <div class="flex items-center gap-1">
                    <span>EXC_5419824</span>
                    <button type="button" onclick="navigator.clipboard.writeText('EXC_5419824'); showToast('Reference copied');" class="text-gray-400 hover:text-[#0b2545]" title="Copy Reference">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                </td>
                <td class="py-5 px-6 font-mono font-bold text-rose-600">-0.50 ETH</td>
                <td class="py-5 px-6 font-mono font-bold text-emerald-700">+₦1,925,000.00</td>
                <td class="py-5 px-6 font-mono text-gray-600">₦3,850,000.00</td>
                <td class="py-5 px-6 text-center">
                  <span class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center" title="Completed">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-gray-500 text-[11px]">14 Sep 2026, 09:15 AM</td>
                <td class="py-5 px-6 text-right">
                  <button onclick="openReceipt('EXC_5419824', '0.50 ETH', '₦1,925,000.00 NGN', '₦3,850,000.00', '14 Sep 2026, 09:15 AM')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Receipt">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                  </button>
                </td>
              </tr>

              <!-- Row 6 -->
              <tr class="hover:bg-blue-50/20 transition">
                <td class="py-5 px-6 font-bold text-gray-900 flex items-center gap-1.5">
                  <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black flex items-center justify-center">₮</span>
                  <span>&rarr;</span>
                  <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-black flex items-center justify-center">₦</span>
                  <span>USDT / NGN</span>
                </td>
                <td class="py-5 px-6 font-mono text-gray-500 text-[11px]">
                  <div class="flex items-center gap-1">
                    <span>EXC_4901842</span>
                    <button type="button" onclick="navigator.clipboard.writeText('EXC_4901842'); showToast('Reference copied');" class="text-gray-400 hover:text-[#0b2545]" title="Copy Reference">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                  </div>
                </td>
                <td class="py-5 px-6 font-mono font-bold text-rose-600">-1,200.00 USDT</td>
                <td class="py-5 px-6 font-mono font-bold text-emerald-700">+₦1,782,000.00</td>
                <td class="py-5 px-6 font-mono text-gray-600">₦1,485.00</td>
                <td class="py-5 px-6 text-center">
                  <span class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center" title="Completed">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-5 px-6 text-gray-500 text-[11px]">13 Sep 2026, 03:40 PM</td>
                <td class="py-5 px-6 text-right">
                  <button onclick="openReceipt('EXC_4901842', '1,200.00 USDT', '₦1,782,000.00 NGN', '₦1,485.00', '13 Sep 2026, 03:40 PM')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Receipt">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
                  </button>
                </td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>
</div>

<!-- ================= CONFIRM SWAP MODAL ================= -->
<div id="confirmSwapModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
  <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-gray-100 p-6 space-y-4">
    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
      <h3 class="text-sm font-bold text-gray-900">Confirm Conversion</h3>
      <button onclick="closeConfirmSwapModal()" class="text-gray-400 hover:text-gray-600">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <!-- Summary -->
    <div class="bg-gray-50 rounded-xl p-3.5 space-y-2 text-xs">
      <div class="flex justify-between">
        <span class="text-gray-500">You Pay:</span>
        <span class="font-mono font-bold text-rose-600" id="modalSoldAmount">--</span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-500">You Receive:</span>
        <span class="font-mono font-bold text-emerald-700" id="modalBoughtAmount">--</span>
      </div>
      <div class="flex justify-between border-t border-gray-200/60 pt-2">
        <span class="text-gray-500">Rate:</span>
        <span class="font-mono font-bold text-gray-900" id="modalRate">--</span>
      </div>
    </div>

    <!-- 4-Digit Security PIN -->
    <div>
      <label class="block text-xs font-semibold text-gray-700 mb-1">Enter 4-Digit PIN</label>
      <input type="password" id="swapPin" maxlength="4" placeholder="••••" class="w-full text-center tracking-[1em] text-base font-mono py-2 border border-gray-200 rounded-xl focus:outline-hidden focus:border-[#0b2545]">
    </div>

    <div class="flex gap-2 pt-2">
      <button type="button" onclick="closeConfirmSwapModal()" class="w-1/2 py-2.5 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl">Cancel</button>
      <button type="button" id="executeBtn" onclick="executeSwap()" class="w-1/2 py-2.5 text-xs font-bold text-white bg-[#0b2545] hover:bg-[#134074] rounded-xl shadow-xs">Convert Now</button>
    </div>
  </div>
</div>

<!-- ================= RECEIPT MODAL ================= -->
<div id="receiptModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
  <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-gray-100 p-6 space-y-4">
    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
      <div class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
        Conversion Receipt
      </div>
      <button onclick="closeReceiptModal()" class="text-gray-400 hover:text-gray-600">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <div class="text-center py-2">
      <div class="text-xs text-gray-400">Credited Amount</div>
      <div class="text-2xl font-black text-gray-900 mt-1" id="recBought">--</div>
      <div class="text-xs text-rose-600 font-medium" id="recSold">--</div>
    </div>

    <div class="bg-gray-50 rounded-xl p-3 text-xs space-y-1.5">
      <div class="flex justify-between">
        <span class="text-gray-500">Reference:</span>
        <span class="font-mono font-bold text-gray-900" id="recRef">--</span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-500">Rate:</span>
        <span class="font-mono text-gray-800" id="recRate">--</span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-500">Date:</span>
        <span class="text-gray-700" id="recDate">--</span>
      </div>
    </div>

    <div class="flex justify-between gap-2 pt-2">
      <button type="button" onclick="closeReceiptModal()" class="w-1/2 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl">Close</button>
      <button type="button" onclick="window.print()" class="w-1/2 py-2 text-xs font-bold text-white bg-[#0b2545] rounded-xl">Print</button>
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
  // Crypto Rates against NGN (Sell rate and Buy rate)
  const cryptoNgnRates = {};

  const userBalances = { NGN: 0 };

  let direction = 'crypto_to_ngn'; // 'crypto_to_ngn' or 'ngn_to_crypto'

  function updateBalanceDisplays() {
    ['USDT','BTC','ETH','SOL'].forEach(token => {
      const el = document.getElementById(`cardBal${token}`);
      if (el) el.textContent = userBalances[token] === undefined ? '—' : Number(userBalances[token]).toLocaleString();
    });
  }

  function setExchangeDirection(dir) {
    direction = dir;
    const tabSell = document.getElementById('tabSellCrypto');
    const tabBuy = document.getElementById('tabBuyCrypto');
    const ctaBtn = document.getElementById('ctaButtonText');
    const sendLbl = document.getElementById('sendLabel');
    const recLbl = document.getElementById('receiveLabel');

    if (dir === 'crypto_to_ngn') {
      tabSell.className = 'py-2 text-xs font-bold rounded-lg bg-[#0b2545] text-white shadow-xs transition flex items-center justify-center gap-1.5';
      tabBuy.className = 'py-2 text-xs font-semibold text-gray-600 hover:text-gray-900 rounded-lg transition flex items-center justify-center gap-1.5';
      ctaBtn.textContent = 'Convert to Naira';
      sendLbl.textContent = 'You Send (Crypto)';
      recLbl.textContent = 'You Receive (₦ Naira)';
      document.getElementById('destCurrencyBadge').innerHTML = '<span>🇳🇬 NGN (₦)</span>';
    } else {
      tabBuy.className = 'py-2 text-xs font-bold rounded-lg bg-[#0b2545] text-white shadow-xs transition flex items-center justify-center gap-1.5';
      tabSell.className = 'py-2 text-xs font-semibold text-gray-600 hover:text-gray-900 rounded-lg transition flex items-center justify-center gap-1.5';
      ctaBtn.textContent = 'Buy Crypto';
      sendLbl.textContent = 'You Send (₦ Naira)';
      recLbl.textContent = 'You Receive (Crypto)';
      const token = document.getElementById('sourceToken').value;
      document.getElementById('destCurrencyBadge').innerHTML = `<span>${token}</span>`;
    }

    calculateSwap();
  }

  function toggleDirection() {
    setExchangeDirection(direction === 'crypto_to_ngn' ? 'ngn_to_crypto' : 'crypto_to_ngn');
  }

  function selectCrypto(token) {
    document.getElementById('sourceToken').value = token;
    if (direction === 'ngn_to_crypto') {
      document.getElementById('destCurrencyBadge').innerHTML = `<span>${token}</span>`;
    }
    calculateSwap();
    showToast(`${token} selected`);
  }

  function calculateSwap() {
    const token = document.getElementById('sourceToken').value;
    const amount = parseFloat(document.getElementById('sendAmount').value) || 0;
    const rateData = cryptoNgnRates[token];
    if (!rateData) {
      document.getElementById('receiveAmount').value = '—';
      document.getElementById('currentRateDisplay').textContent = 'Rate unavailable';
      return;
    }

    if (direction === 'crypto_to_ngn') {
      const rate = rateData.sell;
      const totalNgn = amount * rate;
      document.getElementById('receiveAmount').value = `₦${totalNgn.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
      document.getElementById('currentRateDisplay').textContent = `1 ${token} = ₦${rate.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
      document.getElementById('sourceBalDisplay').textContent = `${userBalances[token]} ${token}`;
      document.getElementById('destBalDisplay').textContent = `₦${userBalances.NGN.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
    } else {
      const rate = rateData.buy;
      const totalCrypto = rate > 0 ? (amount / rate) : 0;
      document.getElementById('receiveAmount').value = `${totalCrypto < 1 ? totalCrypto.toFixed(6) : totalCrypto.toFixed(4)} ${token}`;
      document.getElementById('currentRateDisplay').textContent = `1 ${token} = ₦${rate.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
      document.getElementById('sourceBalDisplay').textContent = `₦${userBalances.NGN.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
      document.getElementById('destBalDisplay').textContent = `${userBalances[token]} ${token}`;
    }
  }

  function setMaxSourceAmount() {
    const token = document.getElementById('sourceToken').value;
    if (direction === 'crypto_to_ngn') {
      document.getElementById('sendAmount').value = userBalances[token];
    } else {
      document.getElementById('sendAmount').value = userBalances.NGN;
    }
    calculateSwap();
  }

  function openConfirmSwapModal() {
    const token = document.getElementById('sourceToken').value;
    const amount = parseFloat(document.getElementById('sendAmount').value) || 0;
    if (amount <= 0) return;

    const rateData = cryptoNgnRates[token];
    if (direction === 'crypto_to_ngn') {
      if (amount > userBalances[token]) {
        alert(`Insufficient ${token} balance.`);
        return;
      }
      const totalNgn = amount * rateData.sell;
      document.getElementById('modalSoldAmount').textContent = `-${amount} ${token}`;
      document.getElementById('modalBoughtAmount').textContent = `+₦${totalNgn.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
      document.getElementById('modalRate').textContent = `1 ${token} = ₦${rateData.sell.toLocaleString()}`;
    } else {
      if (amount > userBalances.NGN) {
        alert('Insufficient NGN balance.');
        return;
      }
      const totalCrypto = (amount / rateData.buy).toFixed(4);
      document.getElementById('modalSoldAmount').textContent = `-₦${amount.toLocaleString()}`;
      document.getElementById('modalBoughtAmount').textContent = `+${totalCrypto} ${token}`;
      document.getElementById('modalRate').textContent = `1 ${token} = ₦${rateData.buy.toLocaleString()}`;
    }

    document.getElementById('swapPin').value = '';
    document.getElementById('confirmSwapModal').classList.remove('hidden');
  }

  function closeConfirmSwapModal() {
    document.getElementById('confirmSwapModal').classList.add('hidden');
  }

  function executeSwap() {
    const pin = document.getElementById('swapPin').value;
    if (pin.length < 4) {
      alert('Enter 4-digit PIN');
      return;
    }

    const btn = document.getElementById('executeBtn');
    btn.disabled = true;
    btn.textContent = 'Converting...';

    setTimeout(() => {
      btn.disabled = false;
      btn.textContent = 'Convert Now';

      const token = document.getElementById('sourceToken').value;
      const amount = parseFloat(document.getElementById('sendAmount').value) || 0;
      const newRef = 'EXC_' + Math.floor(1000000 + Math.random() * 9000000);
      const rateData = cryptoNgnRates[token];

      let soldStr = '', boughtStr = '', rateStr = '';
      if (direction === 'crypto_to_ngn') {
        const totalNgn = amount * rateData.sell;
        userBalances[token] -= amount;
        userBalances.NGN += totalNgn;
        soldStr = `-${amount} ${token}`;
        boughtStr = `+₦${totalNgn.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        rateStr = `₦${rateData.sell.toLocaleString()}`;
      } else {
        const totalCrypto = parseFloat((amount / rateData.buy).toFixed(4));
        userBalances.NGN -= amount;
        userBalances[token] += totalCrypto;
        soldStr = `-₦${amount.toLocaleString()}`;
        boughtStr = `+${totalCrypto} ${token}`;
        rateStr = `₦${rateData.buy.toLocaleString()}`;
      }

      updateBalanceDisplays();

      // Prepend to history table
      const tbody = document.getElementById('exchangeHistoryBody');
      const tr = document.createElement('tr');
      tr.className = 'hover:bg-blue-50/20 transition';
      tr.innerHTML = `
        <td class="py-5 px-6 font-bold text-gray-900 flex items-center gap-1.5">
          <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black flex items-center justify-center">₮</span>
          <span>&rarr;</span>
          <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-black flex items-center justify-center">₦</span>
          <span>${token} / NGN</span>
        </td>
        <td class="py-5 px-6 font-mono text-gray-500 text-[11px]">
          <div class="flex items-center gap-1">
            <span>${newRef}</span>
            <button type="button" onclick="navigator.clipboard.writeText('${newRef}'); showToast('Reference copied');" class="text-gray-400 hover:text-[#0b2545]" title="Copy Reference">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            </button>
          </div>
        </td>
        <td class="py-5 px-6 font-mono font-bold text-rose-600">${soldStr}</td>
        <td class="py-5 px-6 font-mono font-bold text-emerald-700">${boughtStr}</td>
        <td class="py-5 px-6 font-mono text-gray-600">${rateStr}</td>
        <td class="py-5 px-6 text-center">
          <span class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center" title="Completed">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </span>
        </td>
        <td class="py-5 px-6 text-gray-500 text-[11px]">Just now</td>
        <td class="py-5 px-6 text-right">
          <button onclick="openReceipt('${newRef}', '${soldStr}', '${boughtStr}', '${rateStr}', 'Just now')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Receipt">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg>
          </button>
        </td>
      `;
      tbody.prepend(tr);

      closeConfirmSwapModal();
      calculateSwap();
      showToast('Conversion completed successfully!');
    }, 500);
  }

  function openReceipt(ref, sold, bought, rate, date) {
    document.getElementById('recRef').textContent = ref;
    document.getElementById('recSold').textContent = `Sold: ${sold}`;
    document.getElementById('recBought').textContent = bought;
    document.getElementById('recRate').textContent = rate;
    document.getElementById('recDate').textContent = date;
    document.getElementById('receiptModal').classList.remove('hidden');
  }

  function closeReceiptModal() {
    document.getElementById('receiptModal').classList.add('hidden');
  }

  function refreshCryptoRates() {
    const icon = document.getElementById('refreshIcon');
    icon.classList.add('rotate-180');
    setTimeout(() => {
      icon.classList.remove('rotate-180');
      calculateSwap();
      showToast('Live crypto rates updated');
    }, 450);
  }

  function showToast(msg) {
    const toast = document.getElementById('floatingToast');
    document.getElementById('toastMessage').textContent = msg;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.remove('translate-y-3', 'opacity-0'), 10);
    setTimeout(() => {
      toast.classList.add('translate-y-3', 'opacity-0');
      setTimeout(() => toast.classList.add('hidden'), 250);
    }, 2500);
  }

  // Initial calculation
  calculateSwap();
  updateBalanceDisplays();
</script>

<script>
  (() => {
    const auth = () => ({Authorization: `Bearer ${localStorage.getItem('flow_token') || ''}`, Accept: 'application/json'});
    const esc = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
    const format = (value, code) => `${code === 'NGN' ? '₦' : ''}${Number(value || 0).toLocaleString('en-US', {maximumFractionDigits: code === 'NGN' ? 2 : 8})} ${code === 'NGN' ? '' : code}`.trim();
    let liveDirection = 'crypto_to_ngn';
    let liveBalances = {};
    let liveRates = {};

    async function fetchQuote(from, to, amount) {
      const params = new URLSearchParams({from_currency: from, to_currency: to});
      if (amount) params.set('amount', amount);
      const response = await fetch(`../api/financial/quote.php?${params}`, {headers: auth()});
      const result = await response.json();
      if (!response.ok || !result.success) throw new Error(result.error || 'Unable to load quote.');
      return result;
    }

    async function loadExchangeData() {
      try {
        const walletsResponse = await fetch('../api/wallets/list.php', {headers: auth()});
        const walletsResult = await walletsResponse.json();
        if (!walletsResponse.ok || !walletsResult.success) throw new Error(walletsResult.error || 'Unable to load wallet balances.');
        liveBalances = {};
        (walletsResult.wallets || []).forEach(wallet => { liveBalances[wallet.currency_code] = Number(wallet.available_balance || 0); });
        Object.keys(userBalances).forEach(key => delete userBalances[key]);
        Object.assign(userBalances, liveBalances);
        const source = document.getElementById('sourceToken');
        const crypto = (walletsResult.wallets || []).filter(wallet => wallet.kind === 'crypto' && wallet.currency_code);
        source.innerHTML = crypto.length ? crypto.map(wallet => `<option value="${esc(wallet.currency_code)}">${esc(wallet.currency_code)}</option>`).join('') : '<option value="">No crypto wallet</option>';
        if (crypto.length && !crypto.some(wallet => wallet.currency_code === source.value)) source.value = crypto[0].currency_code;
        document.getElementById('ngnBalanceSummary').textContent = `${format(liveBalances.NGN || 0, 'NGN')} NGN`;
        updateBalanceDisplays();
        await refreshLiveQuote();
        await loadExchangeHistory();
      } catch (error) {
        showToast(error.message);
        document.getElementById('currentRateDisplay').textContent = 'Rate unavailable';
      }
    }

    async function refreshLiveQuote() {
      const token = document.getElementById('sourceToken').value;
      if (!token) return;
      const from = liveDirection === 'crypto_to_ngn' ? token : 'NGN';
      const to = liveDirection === 'crypto_to_ngn' ? 'NGN' : token;
      try {
        const quote = await fetchQuote(from, to);
        if (!quote.available || !quote.rate) throw new Error(quote.message || 'Rate unavailable');
        const rate = Number(quote.rate);
        liveRates[token] = liveDirection === 'crypto_to_ngn' ? {sell: rate, buy: rate} : {sell: rate, buy: rate};
        cryptoNgnRates[token] = liveRates[token];
        const header = document.querySelector('.text-emerald-700.bg-emerald-50');
        if (header) header.lastChild.textContent = ` Live: 1 ${token} = ${format(rate, 'NGN')}`;
        document.querySelectorAll('.rate-value[data-rate-token]').forEach(el => { if (el.dataset.rateToken === token) el.textContent = format(rate, 'NGN'); });
        document.querySelectorAll('.rate-change[data-rate-token]').forEach(el => { if (el.dataset.rateToken === token) el.textContent = 'Live'; });
        calculateSwap();
      } catch (error) {
        delete cryptoNgnRates[token];
        document.getElementById('currentRateDisplay').textContent = 'Rate unavailable';
        document.getElementById('receiveAmount').value = '—';
      }
    }

    window.setExchangeDirection = function(directionName) {
      liveDirection = directionName;
      const token = document.getElementById('sourceToken').value;
      const legacy = directionName === 'crypto_to_ngn' ? 'Sell Crypto to ₦' : 'Buy Crypto with ₦';
      document.getElementById('ctaButtonText').textContent = directionName === 'crypto_to_ngn' ? 'Convert to Naira' : 'Buy Crypto';
      document.getElementById('sendLabel').textContent = directionName === 'crypto_to_ngn' ? 'You Send (Crypto)' : 'You Send (₦ Naira)';
      document.getElementById('receiveLabel').textContent = directionName === 'crypto_to_ngn' ? 'You Receive (₦ Naira)' : 'You Receive (Crypto)';
      document.getElementById('destCurrencyBadge').innerHTML = `<span>${directionName === 'crypto_to_ngn' ? '🇳🇬 NGN (₦)' : esc(token)}</span>`;
      document.getElementById('tabSellCrypto').classList.toggle('bg-[#0b2545]', directionName === 'crypto_to_ngn');
      document.getElementById('tabSellCrypto').classList.toggle('text-white', directionName === 'crypto_to_ngn');
      document.getElementById('tabBuyCrypto').classList.toggle('bg-[#0b2545]', directionName === 'ngn_to_crypto');
      document.getElementById('tabBuyCrypto').classList.toggle('text-white', directionName === 'ngn_to_crypto');
      refreshLiveQuote();
      calculateSwap();
    };
    window.toggleDirection = () => setExchangeDirection(liveDirection === 'crypto_to_ngn' ? 'ngn_to_crypto' : 'crypto_to_ngn');
    window.calculateSwap = function() {
      const token = document.getElementById('sourceToken').value;
      const amount = Number(document.getElementById('sendAmount').value || 0);
      const rate = Number((cryptoNgnRates[token] || {}).sell || 0);
      const sourceCode = liveDirection === 'crypto_to_ngn' ? token : 'NGN';
      const destCode = liveDirection === 'crypto_to_ngn' ? 'NGN' : token;
      document.getElementById('sourceBalDisplay').textContent = format(liveBalances[sourceCode] || 0, sourceCode);
      document.getElementById('destBalDisplay').textContent = format(liveBalances[destCode] || 0, destCode);
      if (!rate) { document.getElementById('receiveAmount').value = '—'; document.getElementById('currentRateDisplay').textContent = 'Rate unavailable'; return; }
      const received = liveDirection === 'crypto_to_ngn' ? amount * rate : amount / rate;
      document.getElementById('receiveAmount').value = format(received, destCode);
      document.getElementById('currentRateDisplay').textContent = `1 ${token} = ${format(rate, 'NGN')}`;
    };
    window.setMaxSourceAmount = () => { const code = liveDirection === 'crypto_to_ngn' ? document.getElementById('sourceToken').value : 'NGN'; document.getElementById('sendAmount').value = liveBalances[code] || ''; calculateSwap(); };
    window.openConfirmSwapModal = async function() {
      const token = document.getElementById('sourceToken').value; const amount = Number(document.getElementById('sendAmount').value || 0); const rate = Number((cryptoNgnRates[token] || {}).sell || 0);
      if (!token || amount <= 0 || !rate) { showToast('Enter an amount after a live rate is available.'); return; }
      const sourceCode = liveDirection === 'crypto_to_ngn' ? token : 'NGN';
      if (amount > (liveBalances[sourceCode] || 0)) { showToast(`Insufficient ${sourceCode} balance.`); return; }
      document.getElementById('modalSoldAmount').textContent = `-${format(amount, sourceCode)}`;
      document.getElementById('modalBoughtAmount').textContent = `+${format(liveDirection === 'crypto_to_ngn' ? amount * rate : amount / rate, liveDirection === 'crypto_to_ngn' ? 'NGN' : token)}`;
      document.getElementById('modalRate').textContent = `1 ${token} = ${format(rate, 'NGN')}`;
      document.getElementById('swapPin').value = ''; document.getElementById('confirmSwapModal').classList.remove('hidden');
    };
    window.executeSwap = async function() {
      const token = document.getElementById('sourceToken').value; const amount = Number(document.getElementById('sendAmount').value || 0); const rate = Number((cryptoNgnRates[token] || {}).sell || 0); const pin = document.getElementById('swapPin').value;
      if (pin.length < 4 || !rate) { showToast('Enter your PIN after a live rate is available.'); return; }
      const button = document.getElementById('executeBtn'); button.disabled = true; button.textContent = 'Converting…';
      try { const response = await fetch('../api/financial/action.php', {method:'POST', headers:{...auth(),'Content-Type':'application/json'}, body:JSON.stringify({action:'swap',from_currency:liveDirection === 'crypto_to_ngn' ? token : 'NGN',to_currency:liveDirection === 'crypto_to_ngn' ? 'NGN' : token,amount,rate,pin})}); const result = await response.json(); if (!response.ok || !result.success) throw new Error(result.error || 'Swap failed.'); closeConfirmSwapModal(); showToast(`Conversion ${result.reference} completed.`); await loadExchangeData(); } catch(error) { showToast(error.message); } finally { button.disabled = false; button.textContent = 'Convert Now'; }
    };
    async function loadExchangeHistory() {
      const body = document.getElementById('exchangeHistoryBody'); if (!body) return;
      try { const response = await fetch('../api/transactions/list.php?type=swap&range=all&page=1&page_size=50', {headers:auth()}); const result = await response.json(); if (!response.ok || !result.success) throw new Error(result.error || 'Unable to load exchange history.'); const rows = result.transactions || []; body.innerHTML = rows.length ? rows.map(row => `<tr class="hover:bg-blue-50/20 transition"><td class="py-5 px-6 font-bold text-gray-900">${esc(row.currency || 'Swap')}</td><td class="py-5 px-6 font-mono text-gray-600">${esc(row.reference)}</td><td class="py-5 px-6 font-mono text-rose-600">-${esc(format(row.amount, row.currency))}</td><td class="py-5 px-6 font-mono text-emerald-700">${esc(row.metadata?.to_amount || '—')}</td><td class="py-5 px-6 font-mono text-gray-600">${esc(row.metadata?.rate || '—')}</td><td class="py-5 px-6 text-center">${esc(row.status)}</td><td class="py-5 px-6 text-gray-500">${esc(row.created_at)}</td><td></td></tr>`).join('') : '<tr><td colspan="8" class="py-16 text-center text-sm text-gray-400">No exchanges yet.</td></tr>'; } catch(error) { body.innerHTML = `<tr><td colspan="8" class="py-16 text-center text-sm text-amber-700">${esc(error.message)}</td></tr>`; }
    }
    window.refreshCryptoRates = () => { document.getElementById('refreshIcon')?.classList.add('rotate-180'); refreshLiveQuote().finally(() => document.getElementById('refreshIcon')?.classList.remove('rotate-180')); };
    document.getElementById('sourceToken')?.addEventListener('change', refreshLiveQuote);
    loadExchangeData();
  })();
</script>

<?php
require_once __DIR__ . '/includes/bottom_nav.php';
require_once __DIR__ . '/includes/footer.php';
?>
