<?php
/**
 * Flowwithlit Cards Page
 * Multi-Currency Virtual & Physical Debit Card Management & Generation
 */
$pageTitle = 'Cards — Flowwithlit';
$activePage = 'cards';
$activeNavTab = 'Features';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

    <!-- Scrollable Main Content Area -->
    <style>#cardsPageMain.cards-live-pending{visibility:hidden}</style>
    <main id="cardsPageMain" class="cards-live-pending flex-1 h-full overflow-y-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-6 flex flex-col gap-6">
      
      <!-- Top Title & Quick Action Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 flex-shrink-0">
        <div>
          <div class="flex items-center gap-2">
            <span class="text-xs text-gray-400 font-medium tracking-wide">Corporate Issuing</span>
            <span id="cardsActiveBadge" class="text-[11px] font-bold text-sky-700 bg-sky-50 border border-sky-200/50 px-2 py-0.5 rounded-full">0 Active Cards</span>
          </div>
          <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight mt-0.5">Corporate &amp; Virtual Cards</h1>
        </div>

        <!-- Action buttons -->
        <div class="flex items-center gap-2.5 flex-wrap">
          <!-- Card Settings / Limits -->
          <button id="cardLimitsBtn" class="px-4 py-2.5 rounded-full bg-white hover:bg-gray-50 border border-gray-200/80 text-gray-700 text-xs font-semibold flex items-center gap-2 shadow-xs transition hover:scale-105 active:scale-95">
            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
            </svg>
            <span>Global Spend Limits</span>
          </button>

          <!-- Primary: Generate New Card Button (Brand Navy) -->
          <button id="openGenerateCardModalBtn" class="bg-[#0b2545] hover:bg-[#132847] text-white px-5 py-2.5 rounded-full text-xs font-semibold flex items-center gap-2 shadow-sm transition hover:scale-105 active:scale-95">
            <span class="w-4 h-4 rounded-full border border-white/40 flex items-center justify-center text-[11px] leading-none font-bold">+</span>
            <span>Generate New Card</span>
          </button>
        </div>
      </div>

      <!-- Row 1: KPI Stats Row -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 flex-shrink-0">
        
        <!-- Stat 1: Total Card Spend -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex flex-col justify-between">
          <div class="flex items-center justify-between text-xs text-gray-500 font-medium">
            <span>Total Card Spend (This Month)</span>
            <div class="w-8 h-8 rounded-xl bg-sky-50 text-[#0b2545] flex items-center justify-center font-bold">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v10.5m0-10.5h19.5m0 0v10.5m0-10.5h.75a.75.75 0 01.75.75v.75m0 10.5v.75a.75.75 0 01-.75.75h-.75m-19.5 0h19.5"/></svg>
            </div>
          </div>
          <div class="mt-3">
            <div id="cardSpendMetric" class="text-2xl font-black text-gray-900 tracking-tight">₦0.00</div>
            <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1">
              <span id="cardLimitMetric">Limit: ₦0.00</span>
              <span id="cardSpendPercentMetric" class="font-bold text-gray-400">—</span>
            </div>
            <div class="w-full h-1.5 rounded-full bg-gray-100 mt-2 overflow-hidden">
              <div id="cardSpendProgress" class="h-full rounded-full bg-[#0b2545]" style="width: 0%;"></div>
            </div>
          </div>
        </div>

        <!-- Stat 2: Active Cards Count -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex flex-col justify-between">
          <div class="flex items-center justify-between text-xs text-gray-500 font-medium">
            <span>Active Cards</span>
            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
          </div>
          <div class="mt-3">
            <div id="activeCardsMetric" class="text-2xl font-black text-gray-900 tracking-tight">0 Cards</div>
            <p id="activeCardsDetail" class="text-[11px] text-gray-500 mt-1">No cards issued</p>
          </div>
        </div>

        <!-- Stat 3: Available Balance for Card Debits -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex flex-col justify-between">
          <div class="flex items-center justify-between text-xs text-gray-500 font-medium">
            <span>Funding Wallets Backing</span>
            <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5M4.5 21V10.5M3 21h18"/></svg>
            </div>
          </div>
          <div class="mt-3">
            <div id="cardFundingMetric" class="text-2xl font-black text-gray-900 tracking-tight">₦0.00</div>
            <p id="cardFundingDetail" class="text-[11px] text-gray-500 font-medium mt-1">No funding wallets linked</p>
          </div>
        </div>

        <!-- Stat 4: Security & 3D Secure Status -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex flex-col justify-between">
          <div class="flex items-center justify-between text-xs text-gray-500 font-medium">
            <span>Security &amp; 3D Secure</span>
            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-2.14-8.855A48.667 48.667 0 0012 3.473c-1.396.033-2.78.16-4.11.378-1.373.224-2.43 1.34-2.502 2.73a49.19 49.19 0 00-.77 8.528c0 7.637 6.6 13.536 7.382 14.122a1.05 1.05 0 001.2 0c.782-.586 7.382-6.485 7.382-14.122 0-2.88-.266-5.73-.77-8.528-.072-1.39-1.129-2.506-2.502-2.73z"/></svg>
            </div>
          </div>
          <div class="mt-3">
            <div id="cardSecurityMetric" class="text-2xl font-black text-gray-500 tracking-tight">Unavailable</div>
            <p id="cardSecurityDetail" class="text-[11px] text-gray-500 mt-1">3D Secure provider not configured</p>
          </div>
        </div>

      </div>

      <!-- Row 2: Card Showcase & Live Interactive Controls -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 flex-shrink-0">
        
        <!-- Left: Interactive Card Visual (Col-span 5) -->
        <div class="lg:col-span-5 bg-white rounded-3xl p-6 border border-gray-200/80 shadow-xs flex flex-col gap-5">
          
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-base font-bold text-gray-900">Card Selector</h3>
              <p class="text-xs text-gray-400">Select a card to view details and control settings</p>
            </div>
            <!-- Card Selector Dropdown / Badges -->
            <select id="cardSelectDropdown" class="bg-gray-50 border border-gray-200 rounded-xl px-3 py-1.5 text-xs text-gray-800 font-semibold focus:outline-none focus:ring-2 focus:ring-[#0b2545]/20 focus:border-[#0b2545]">
              <option value="card-1" selected>Cloud &amp; SaaS (•••• 4829)</option>
              <option value="card-2">Marketing &amp; Ads (•••• 9104)</option>
              <option value="card-3">Executive Travel (•••• 3012)</option>
              <option value="card-4">EU Operations (•••• 7745)</option>
            </select>
          </div>

          <!-- THE PHYSICAL / VIRTUAL CARD CONTAINER (Styled in Brand Navy #0b2545) -->
          <div id="cardVisualContainer" class="relative w-full aspect-[1.586/1] rounded-3xl p-6 sm:p-7 text-white shadow-xl flex flex-col justify-between overflow-hidden transition-all duration-300 select-none bg-[#0b2545]">
            
            <!-- Subtle background decorative geometric rings -->
            <div class="absolute -right-12 -top-12 w-56 h-56 rounded-full bg-white/5 pointer-events-none"></div>
            <div class="absolute right-10 -bottom-16 w-48 h-48 rounded-full bg-sky-400/10 pointer-events-none"></div>

            <!-- Frozen overlay (hidden by default) -->
            <div id="cardFrozenOverlay" class="hidden absolute inset-0 bg-slate-900/85 backdrop-blur-xs flex flex-col items-center justify-center gap-2 z-20">
              <svg class="w-10 h-10 text-sky-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
              </svg>
              <span class="text-sm font-bold uppercase tracking-widest text-white">Card Frozen</span>
              <span class="text-xs text-gray-300">Transactions temporarily disabled</span>
            </div>

            <!-- Card Top: Chip & Contactless & Brand -->
            <div class="flex items-center justify-between relative z-10">
              <div class="flex items-center gap-3">
                <!-- Gold EMV Chip -->
                <div class="w-11 h-8 rounded-lg bg-gradient-to-tr from-amber-300 via-yellow-200 to-amber-400 border border-yellow-500/50 shadow-inner flex items-center justify-center">
                  <div class="w-8 h-5 border border-yellow-700/30 rounded-xs flex flex-col justify-between p-0.5">
                    <div class="w-full h-[1px] bg-yellow-700/40"></div>
                    <div class="w-full h-[1px] bg-yellow-700/40"></div>
                  </div>
                </div>
                <!-- Contactless Waves -->
                <svg class="w-5 h-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5a14.25 14.25 0 010 15M11.25 7.5a9.75 9.75 0 010 9M14.25 10.5a5.25 5.25 0 010 3" />
                </svg>
              </div>

              <!-- Card Rail Badge -->
              <div class="flex items-center gap-2">
                <span id="cardTypeBadge" class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md bg-white/10 text-sky-200 border border-white/10">Virtual Debit</span>
                <span class="text-xs font-black tracking-widest text-white/80">FLOWWITHLIT</span>
              </div>
            </div>

            <!-- Card Middle: 16-digit Number -->
            <div class="relative z-10 my-2">
              <div class="flex items-center gap-2">
                <span id="cardNumberDisplay" class="font-mono text-base sm:text-xl font-bold tracking-[0.22em] text-white">
                  &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; 4829
                </span>
                <button type="button" id="copyCardNumberBtn" class="text-white/60 hover:text-white transition" title="Copy Card Number">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </button>
              </div>
              <span id="cardPurposeDisplay" class="text-[11px] text-sky-200 font-medium">Cloud &amp; SaaS Infrastructure</span>
            </div>

            <!-- Card Bottom: Cardholder, Expiry, CVV & Network Logo -->
            <div class="flex items-end justify-between relative z-10">
              <div class="flex gap-5 sm:gap-6">
                <div>
                  <span class="block text-[9px] uppercase tracking-wider text-white/50 font-bold">Cardholder</span>
                  <span id="cardHolderDisplay" class="font-bold text-xs sm:text-sm tracking-wider uppercase text-white">Sarah Jenkins</span>
                </div>
                <div>
                  <span class="block text-[9px] uppercase tracking-wider text-white/50 font-bold">Expires</span>
                  <span id="cardExpiryDisplay" class="font-mono text-xs sm:text-sm font-semibold text-white">08/29</span>
                </div>
                <div>
                  <span class="block text-[9px] uppercase tracking-wider text-white/50 font-bold">CVV</span>
                  <span id="cardCvvDisplay" class="font-mono text-xs sm:text-sm font-semibold text-white">&bull;&bull;&bull;</span>
                </div>
              </div>

              <!-- Network Logo (Mastercard / Visa) -->
              <div id="cardNetworkLogo" class="flex items-center">
                <!-- Mastercard Two Overlapping Circles -->
                <div class="flex -space-x-2.5">
                  <div class="w-6 h-6 rounded-full bg-red-500/90 shadow-xs"></div>
                  <div class="w-6 h-6 rounded-full bg-amber-400/90 shadow-xs"></div>
                </div>
              </div>
            </div>

          </div>

          <!-- Quick Action Buttons right below card -->
          <div class="grid grid-cols-3 gap-2 pt-1">
            <!-- Reveal / Hide Card Details -->
            <button type="button" id="toggleRevealDetailsBtn" class="py-2 px-2 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200/80 text-gray-700 text-xs font-semibold flex items-center justify-center gap-1.5 transition active:scale-95">
              <svg id="revealEyeIcon" class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span id="revealText">Reveal Details</span>
            </button>

            <!-- Freeze / Unfreeze -->
            <button type="button" id="toggleFreezeCardBtn" class="py-2 px-2 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200/80 text-gray-700 text-xs font-semibold flex items-center justify-center gap-1.5 transition active:scale-95">
              <svg id="freezeLockIcon" class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
              </svg>
              <span id="freezeText">Freeze Card</span>
            </button>

            <!-- View PIN -->
            <button type="button" id="viewPinBtn" class="py-2 px-2 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200/80 text-gray-700 text-xs font-semibold flex items-center justify-center gap-1.5 transition active:scale-95">
              <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
              </svg>
              <span>Card PIN</span>
            </button>
          </div>

        </div>

        <!-- Right: Active Card Parameters & Security Controls (Col-span 7) -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 border border-gray-200/80 shadow-xs flex flex-col justify-between gap-6">
          
          <div>
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
              <div>
                <h3 class="text-base font-bold text-gray-900" id="cardDetailTitle">Cloud &amp; SaaS Infrastructure Card</h3>
                <p class="text-xs text-gray-400 mt-0.5" id="cardDetailSubtitle">Linked to Corporate Nigerian Naira (NGN) Wallet</p>
              </div>
              <span id="cardStatusBadge" class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Active
              </span>
            </div>

            <!-- Spending Limit Bar -->
            <div class="mt-4 p-4 rounded-2xl bg-gray-50 border border-gray-100 flex flex-col gap-2">
              <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500 font-medium">Monthly Spending Limit</span>
                <span class="font-bold text-gray-900" id="cardLimitDisplay">₦0.00 / ₦0.00</span>
              </div>
              <div class="w-full h-2 rounded-full bg-gray-200 overflow-hidden">
                <div id="cardLimitProgressBar" class="h-full rounded-full bg-[#0b2545]" style="width: 0%;"></div>
              </div>
              <div class="flex items-center justify-between text-[11px] text-gray-400 mt-0.5">
                <span>Monthly spending limit</span>
                <span id="cardRemainingLimit" class="text-emerald-600 font-semibold">₦0.00 remaining</span>
              </div>
            </div>

            <!-- Feature Toggles Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
              
              <!-- Toggle 1: Online Purchases -->
              <div class="p-3.5 rounded-2xl bg-gray-50/60 border border-gray-200/60 flex items-center justify-between">
                <div>
                  <span class="block text-xs font-bold text-gray-800">Online Transactions</span>
                  <span class="text-[10px] text-gray-400">Allow e-commerce &amp; SaaS billing</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" checked class="sr-only peer">
                  <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#0b2545]"></div>
                </label>
              </div>

              <!-- Toggle 2: International / Multi-Currency -->
              <div class="p-3.5 rounded-2xl bg-gray-50/60 border border-gray-200/60 flex items-center justify-between">
                <div>
                  <span class="block text-xs font-bold text-gray-800">International Payments</span>
                  <span class="text-[10px] text-gray-400">Cross-border FX settlements</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" checked class="sr-only peer">
                  <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#0b2545]"></div>
                </label>
              </div>

              <!-- Toggle 3: ATM Withdrawals (Physical only) -->
              <div class="p-3.5 rounded-2xl bg-gray-50/60 border border-gray-200/60 flex items-center justify-between">
                <div>
                  <span class="block text-xs font-bold text-gray-800">ATM Withdrawals</span>
                  <span class="text-[10px] text-gray-400">Cash dispensation at terminals</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" class="sr-only peer">
                  <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#0b2545]"></div>
                </label>
              </div>

              <!-- Toggle 4: Merchant Lock -->
              <div class="p-3.5 rounded-2xl bg-gray-50/60 border border-gray-200/60 flex items-center justify-between">
                <div>
                  <span class="block text-xs font-bold text-gray-800">Merchant Category Lock</span>
                  <span class="text-[10px] text-gray-400">Restrict to Tech &amp; Cloud MCC</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" checked class="sr-only peer">
                  <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#0b2545]"></div>
                </label>
              </div>

            </div>
          </div>

          <!-- Bottom Meta & Termination Button -->
          <div class="flex items-center justify-between pt-3 border-t border-gray-100 text-xs">
            <div class="text-gray-400">
              Card Token: <span class="font-mono text-gray-700 font-semibold" id="cardTokenDisplay">tok_mastercard_8920184</span>
            </div>
            <button type="button" class="text-rose-600 hover:text-rose-800 font-bold text-xs hover:underline flex items-center gap-1" onclick="if(confirm('Are you sure you want to terminate this card permanently? This action cannot be undone.')) alert('Card terminated successfully.');">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
              <span>Terminate Card</span>
            </button>
          </div>

        </div>

      </div>

      <!-- Row 3: Card Transactions Ledger Table (100% Full Width, Icon-only status with tooltips) -->
      <div class="w-full bg-white rounded-3xl p-6 border border-gray-200/80 shadow-xs flex flex-col gap-4">
        
        <!-- Header & Search Filter -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-4">
          <div>
            <h3 class="text-base font-bold text-gray-900">Recent Card Transactions</h3>
            <p class="text-xs text-gray-400 mt-0.5">Real-time authorizations and settlement ledger for all cards</p>
          </div>

          <div class="flex items-center gap-3">
            <!-- Search bar -->
            <div class="relative">
              <input type="text" id="cardTxSearch" placeholder="Search merchant or amount..." class="bg-gray-50 border border-gray-200 rounded-full pl-8 pr-3.5 py-1.5 text-xs text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#0b2545]/20 focus:border-[#0b2545]">
              <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <!-- Filter Status -->
            <select id="cardTxStatusFilter" class="bg-gray-50 border border-gray-200 rounded-full px-3 py-1.5 text-xs text-gray-700 font-semibold focus:outline-none">
              <option value="all">All Statuses</option>
              <option value="Paid">Paid / Settled</option>
              <option value="Completed">Completed</option>
              <option value="Failed">Failed / Declined</option>
            </select>
          </div>
        </div>

        <!-- 100% Full Width Table -->
        <div class="w-full overflow-x-auto">
          <table class="w-full text-left text-xs text-gray-600 border-collapse">
            <thead>
              <tr class="border-b border-gray-100 text-[11px] font-bold uppercase tracking-wider text-gray-400 bg-gray-50/50">
                <th class="py-3 px-4 rounded-l-xl">Merchant &amp; Category</th>
                <th class="py-3 px-4">Card Used</th>
                <th class="py-3 px-4">Billing Date</th>
                <th class="py-3 px-4">Authorization Code</th>
                <th class="py-3 px-4">Amount</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4 text-center rounded-r-xl">Receipt</th>
              </tr>
            </thead>
            <tbody id="cardTxTableBody" class="divide-y divide-gray-100">
              
              <!-- Row 1: Amazon Web Services -->
              <tr class="hover:bg-gray-50/70 transition" data-status="Paid">
                <td class="py-3.5 px-4">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 font-bold text-xs flex items-center justify-center">
                      AWS
                    </div>
                    <div>
                      <span class="font-bold text-gray-900 block">Amazon Web Services</span>
                      <span class="text-[11px] text-gray-400">Cloud Hosting &amp; GPU Compute</span>
                    </div>
                  </div>
                </td>
                <td class="py-3.5 px-4 font-medium text-gray-700">Virtual &bull;&bull;&bull;&bull; 4829</td>
                <td class="py-3.5 px-4 text-gray-500">18 Jun 2026, 09:14 AM</td>
                <td class="py-3.5 px-4 font-mono text-gray-500">AUTH-892019</td>
                <td class="py-3.5 px-4 font-black text-gray-900">-$3,450.00 USD</td>
                <!-- STATUS ICON ONLY WITH HOVER TOOLTIP -->
                <td class="py-3.5 px-4 text-center">
                  <div class="relative group inline-block">
                    <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 inline-flex items-center justify-center cursor-pointer transition group-hover:scale-110">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </span>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 px-2 py-1 bg-[#0b2545] text-white text-[10px] font-bold rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 transition pointer-events-none z-30 shadow-md">
                      Paid &bull; Settled
                    </div>
                  </div>
                </td>
                <!-- RECEIPT ICON ONLY WITH HOVER TOOLTIP -->
                <td class="py-3.5 px-4 text-center">
                  <div class="relative group inline-block">
                    <button type="button" class="w-7 h-7 rounded-full hover:bg-gray-100 text-gray-500 hover:text-[#0b2545] inline-flex items-center justify-center transition" onclick="showReceipt('AUTH-892019', 'Amazon Web Services', '$3,450.00 USD', 'Mastercard Virtual •••• 4829', 'Paid')">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                    </button>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 px-2 py-1 bg-[#0b2545] text-white text-[10px] font-bold rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 transition pointer-events-none z-30 shadow-md">
                      View Receipt
                    </div>
                  </div>
                </td>
              </tr>

              <!-- Row 2: Google Ads -->
              <tr class="hover:bg-gray-50/70 transition" data-status="Completed">
                <td class="py-3.5 px-4">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-700 font-bold text-xs flex items-center justify-center">
                      GAD
                    </div>
                    <div>
                      <span class="font-bold text-gray-900 block">Google Ads EMEA</span>
                      <span class="text-[11px] text-gray-400">Search Engine Marketing</span>
                    </div>
                  </div>
                </td>
                <td class="py-3.5 px-4 font-medium text-gray-700">Virtual &bull;&bull;&bull;&bull; 9104</td>
                <td class="py-3.5 px-4 text-gray-500">17 Jun 2026, 04:30 PM</td>
                <td class="py-3.5 px-4 font-mono text-gray-500">AUTH-784012</td>
                <td class="py-3.5 px-4 font-black text-gray-900">-$2,100.00 USD</td>
                <!-- STATUS ICON ONLY WITH HOVER TOOLTIP -->
                <td class="py-3.5 px-4 text-center">
                  <div class="relative group inline-block">
                    <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 inline-flex items-center justify-center cursor-pointer transition group-hover:scale-110">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5m-5 0l-4.5 4.5" /></svg>
                    </span>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 px-2 py-1 bg-[#0b2545] text-white text-[10px] font-bold rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 transition pointer-events-none z-30 shadow-md">
                      Completed
                    </div>
                  </div>
                </td>
                <!-- RECEIPT ICON ONLY WITH HOVER TOOLTIP -->
                <td class="py-3.5 px-4 text-center">
                  <div class="relative group inline-block">
                    <button type="button" class="w-7 h-7 rounded-full hover:bg-gray-100 text-gray-500 hover:text-[#0b2545] inline-flex items-center justify-center transition" onclick="showReceipt('AUTH-784012', 'Google Ads EMEA', '$2,100.00 USD', 'Visa Virtual •••• 9104', 'Completed')">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                    </button>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 px-2 py-1 bg-[#0b2545] text-white text-[10px] font-bold rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 transition pointer-events-none z-30 shadow-md">
                      View Receipt
                    </div>
                  </div>
                </td>
              </tr>

              <!-- Row 3: Figma Design Systems -->
              <tr class="hover:bg-gray-50/70 transition" data-status="Paid">
                <td class="py-3.5 px-4">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-700 font-bold text-xs flex items-center justify-center">
                      FIG
                    </div>
                    <div>
                      <span class="font-bold text-gray-900 block">Figma Inc</span>
                      <span class="text-[11px] text-gray-400">Enterprise Design Software</span>
                    </div>
                  </div>
                </td>
                <td class="py-3.5 px-4 font-medium text-gray-700">Virtual &bull;&bull;&bull;&bull; 4829</td>
                <td class="py-3.5 px-4 text-gray-500">15 Jun 2026, 11:20 AM</td>
                <td class="py-3.5 px-4 font-mono text-gray-500">AUTH-441092</td>
                <td class="py-3.5 px-4 font-black text-gray-900">-$450.00 USD</td>
                <!-- STATUS ICON ONLY WITH HOVER TOOLTIP -->
                <td class="py-3.5 px-4 text-center">
                  <div class="relative group inline-block">
                    <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 inline-flex items-center justify-center cursor-pointer transition group-hover:scale-110">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </span>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 px-2 py-1 bg-[#0b2545] text-white text-[10px] font-bold rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 transition pointer-events-none z-30 shadow-md">
                      Paid &bull; Settled
                    </div>
                  </div>
                </td>
                <!-- RECEIPT ICON ONLY WITH HOVER TOOLTIP -->
                <td class="py-3.5 px-4 text-center">
                  <div class="relative group inline-block">
                    <button type="button" class="w-7 h-7 rounded-full hover:bg-gray-100 text-gray-500 hover:text-[#0b2545] inline-flex items-center justify-center transition" onclick="showReceipt('AUTH-441092', 'Figma Inc', '$450.00 USD', 'Mastercard Virtual •••• 4829', 'Paid')">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                    </button>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 px-2 py-1 bg-[#0b2545] text-white text-[10px] font-bold rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 transition pointer-events-none z-30 shadow-md">
                      View Receipt
                    </div>
                  </div>
                </td>
              </tr>

              <!-- Row 4: British Airways (Physical Card) -->
              <tr class="hover:bg-gray-50/70 transition" data-status="Paid">
                <td class="py-3.5 px-4">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-700 font-bold text-xs flex items-center justify-center">
                      BA
                    </div>
                    <div>
                      <span class="font-bold text-gray-900 block">British Airways PLC</span>
                      <span class="text-[11px] text-gray-400">Flight London LHR &rarr; New York JFK</span>
                    </div>
                  </div>
                </td>
                <td class="py-3.5 px-4 font-medium text-gray-700">Physical &bull;&bull;&bull;&bull; 3012</td>
                <td class="py-3.5 px-4 text-gray-500">12 Jun 2026, 08:15 AM</td>
                <td class="py-3.5 px-4 font-mono text-gray-500">AUTH-109482</td>
                <td class="py-3.5 px-4 font-black text-gray-900">-£1,850.00 GBP</td>
                <!-- STATUS ICON ONLY WITH HOVER TOOLTIP -->
                <td class="py-3.5 px-4 text-center">
                  <div class="relative group inline-block">
                    <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 inline-flex items-center justify-center cursor-pointer transition group-hover:scale-110">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </span>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 px-2 py-1 bg-[#0b2545] text-white text-[10px] font-bold rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 transition pointer-events-none z-30 shadow-md">
                      Paid &bull; Settled
                    </div>
                  </div>
                </td>
                <!-- RECEIPT ICON ONLY WITH HOVER TOOLTIP -->
                <td class="py-3.5 px-4 text-center">
                  <div class="relative group inline-block">
                    <button type="button" class="w-7 h-7 rounded-full hover:bg-gray-100 text-gray-500 hover:text-[#0b2545] inline-flex items-center justify-center transition" onclick="showReceipt('AUTH-109482', 'British Airways PLC', '£1,850.00 GBP', 'Visa Physical •••• 3012', 'Paid')">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                    </button>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 px-2 py-1 bg-[#0b2545] text-white text-[10px] font-bold rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 transition pointer-events-none z-30 shadow-md">
                      View Receipt
                    </div>
                  </div>
                </td>
              </tr>

              <!-- Row 5: Suspicious POS Declined -->
              <tr class="hover:bg-gray-50/70 transition" data-status="Failed">
                <td class="py-3.5 px-4">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-700 font-bold text-xs flex items-center justify-center">
                      ATM
                    </div>
                    <div>
                      <span class="font-bold text-gray-900 block">Unauthorized ATM Terminal #401</span>
                      <span class="text-[11px] text-rose-500 font-medium">Blocked: ATM Withdrawal Disabled</span>
                    </div>
                  </div>
                </td>
                <td class="py-3.5 px-4 font-medium text-gray-700">Virtual &bull;&bull;&bull;&bull; 4829</td>
                <td class="py-3.5 px-4 text-gray-500">10 Jun 2026, 02:45 AM</td>
                <td class="py-3.5 px-4 font-mono text-gray-500">DECL-009182</td>
                <td class="py-3.5 px-4 font-black text-rose-600">-$500.00 USD</td>
                <!-- STATUS ICON ONLY WITH HOVER TOOLTIP -->
                <td class="py-3.5 px-4 text-center">
                  <div class="relative group inline-block">
                    <span class="w-7 h-7 rounded-full bg-rose-50 text-rose-600 inline-flex items-center justify-center cursor-pointer transition group-hover:scale-110">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </span>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 px-2 py-1 bg-[#0b2545] text-white text-[10px] font-bold rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 transition pointer-events-none z-30 shadow-md">
                      Failed &bull; Blocked by Policy
                    </div>
                  </div>
                </td>
                <!-- RECEIPT ICON ONLY WITH HOVER TOOLTIP -->
                <td class="py-3.5 px-4 text-center">
                  <div class="relative group inline-block">
                    <button type="button" class="w-7 h-7 rounded-full hover:bg-gray-100 text-gray-500 hover:text-[#0b2545] inline-flex items-center justify-center transition" onclick="showReceipt('DECL-009182', 'Unauthorized ATM #401', '$500.00 USD', 'Mastercard Virtual •••• 4829', 'Failed')">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                    </button>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 px-2 py-1 bg-[#0b2545] text-white text-[10px] font-bold rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 transition pointer-events-none z-30 shadow-md">
                      View Decline Notice
                    </div>
                  </div>
                </td>
              </tr>

            </tbody>
          </table>
        </div>

      </div>

    </main>

  <!-- ======================================================= -->
  <!-- MODAL: GENERATE NEW CARD (Full Screen on Mobile View)   -->
  <!-- ======================================================= -->
  <div id="generateCardModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full h-full sm:h-auto sm:max-h-[92vh] sm:max-w-lg sm:rounded-[28px] shadow-2xl border-0 sm:border sm:border-gray-200 flex flex-col overflow-hidden transition-all duration-300">
      
      <!-- Top Sticky App Bar -->
      <div class="flex items-center justify-between px-5 sm:px-7 py-3.5 border-b border-gray-100 bg-white flex-shrink-0">
        <div class="flex items-center gap-3">
          <button id="cardModalBackBtn" class="hidden w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
          </button>
          <div>
            <h3 id="cardModalTitle" class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">Generate Corporate Card</h3>
            <p id="cardModalSubtitle" class="text-xs text-gray-400">Step 1 of 2 &bull; Card Specifications</p>
          </div>
        </div>
        <button id="closeGenerateCardModalBtn" class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition active:scale-95">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <!-- Scrollable Form Body -->
      <div class="flex-1 overflow-y-auto px-5 sm:px-7 py-5 flex flex-col justify-between">
        
        <!-- STEP 1: CARD TYPE & LIMIT CONFIG -->
        <form id="generateCardForm" class="flex flex-col gap-4">
          
          <!-- Card Form Factor Picker (Virtual vs Physical) -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Select Card Type</label>
            <div class="grid grid-cols-2 gap-3">
              <!-- Virtual Card -->
              <label class="p-3.5 rounded-2xl border-2 border-[#0b2545] bg-[#0b2545]/5 flex flex-col gap-1 cursor-pointer transition relative">
                <input type="radio" name="newCardType" value="virtual" checked class="sr-only">
                <div class="flex items-center justify-between">
                  <span class="font-bold text-xs text-gray-900 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#0b2545]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/></svg>
                    Virtual Card
                  </span>
                  <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-800">Instant</span>
                </div>
                <span class="text-[11px] text-gray-500">For SaaS subscriptions, ads &amp; online checkout</span>
              </label>

              <!-- Physical Card -->
              <label class="p-3.5 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 flex flex-col gap-1 cursor-pointer transition relative">
                <input type="radio" name="newCardType" value="physical" class="sr-only">
                <div class="flex items-center justify-between">
                  <span class="font-bold text-xs text-gray-900 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="3" stroke-linecap="round" stroke-linejoin="round"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h2m3 0h2" /></svg>
                    Physical Metal
                  </span>
                  <span class="text-[9px] text-gray-400 font-semibold">3-5 Days</span>
                </div>
                <span class="text-[11px] text-gray-500">Laser-etched metal card for global POS &amp; travel</span>
              </label>
            </div>
          </div>

          <!-- Card Label & Purpose -->
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Card Label / Department Purpose</label>
            <input type="text" id="newCardLabel" value="Software &amp; AI Cloud Tools" placeholder="e.g. AWS &amp; Cloud Infra, Marketing, Executive Travel" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-900 font-medium focus:outline-none focus:ring-2 focus:ring-[#0b2545]/20 focus:border-[#0b2545]" required>
          </div>

          <!-- Cardholder Name -->
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Cardholder Legal Name</label>
            <input type="text" id="newCardHolder" value="Sarah Jenkins" placeholder="Full name on card" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-900 font-medium focus:outline-none focus:ring-2 focus:ring-[#0b2545]/20 focus:border-[#0b2545]" required>
          </div>

          <!-- Network & Funding Wallet Selection -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Payment Network</label>
              <select id="newCardNetwork" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-900 font-semibold focus:outline-none focus:ring-2 focus:ring-[#0b2545]/20 focus:border-[#0b2545]">
                <option value="mastercard">Mastercard Corporate</option>
                <option value="visa">Visa Commercial</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Funding Wallet</label>
              <select id="newCardWallet" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-900 font-semibold focus:outline-none focus:ring-2 focus:ring-[#0b2545]/20 focus:border-[#0b2545]">
                <option value="USD">USD ($248,650.00)</option>
                <option value="EUR">EUR (€54,210.20)</option>
                <option value="GBP">GBP (£31,840.15)</option>
                <option value="CAD">CAD (CA$48,920.00)</option>
                <option value="NGN">NGN (₦192,450,800)</option>
              </select>
            </div>
          </div>

          <!-- Spending Limit Setting -->
          <div class="bg-gray-50 p-4 rounded-2xl border border-gray-200 flex flex-col gap-3">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-gray-800">Monthly Spending Limit</span>
              <span class="text-[10px] text-gray-500">Can be adjusted at any time</span>
            </div>
            <div class="relative">
              <span class="absolute left-3.5 top-2.5 text-gray-400 font-extrabold text-sm">$</span>
              <input type="number" id="newCardLimit" value="10000" step="500" class="w-full bg-white border border-gray-200 rounded-xl pl-8 pr-3.5 py-2 text-sm font-black text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#0b2545]/20 focus:border-[#0b2545]" required>
            </div>
            <div class="flex items-center gap-1.5 text-[11px] text-gray-500">
              <svg class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span>Instant provisioning &bull; 0% issuance fee for virtual cards</span>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="pt-2">
            <button type="submit" class="w-full py-3.5 rounded-full bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-bold transition text-center shadow-md active:scale-95 flex items-center justify-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
              <span>Issue &amp; Provision Card Now</span>
            </button>
          </div>

        </form>

        <!-- STEP 2: ROLLING GENERATION SCREEN (Hidden initially) -->
        <div id="cardRollingScreen" class="hidden flex flex-col items-center justify-center text-center py-10 gap-5">
          <div class="relative w-20 h-20 flex items-center justify-center">
            <svg class="w-20 h-20 animate-spin text-[#0b2545]" viewBox="0 0 100 100" fill="none">
              <circle cx="50" cy="50" r="42" stroke="#e5e7eb" stroke-width="8"/>
              <path d="M50 8 A 42 42 0 0 1 92 50" stroke="#0b2545" stroke-width="8" stroke-linecap="round"/>
            </svg>
            <div class="absolute inset-0 flex items-center justify-center">
              <svg class="w-7 h-7 text-[#0b2545]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="3" stroke-linecap="round" stroke-linejoin="round"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h2m3 0h2" /></svg>
            </div>
          </div>
          <div>
            <h4 class="text-lg font-bold text-gray-900">Provisioning Your Card...</h4>
            <p class="text-xs text-gray-400 mt-1">Allocating secure 16-digit BIN, CVV &amp; 3D Secure credentials</p>
          </div>
        </div>

        <!-- STEP 3: FRESHLY GENERATED CARD SUCCESS (Hidden initially) -->
        <div id="cardSuccessScreen" class="hidden flex flex-col items-center text-center py-4 gap-4">
          
          <!-- Green Circle Completed Icon -->
          <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center shadow-xs">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
          </div>

          <div>
            <span class="inline-block px-3 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60 mb-1">Card Ready for Payments</span>
            <h4 class="text-xl font-black text-gray-900 tracking-tight" id="generatedCardTitle">Software &amp; AI Cloud Tools</h4>
            <p class="text-xs text-gray-500 mt-0.5">Your card is active and ready for online payments</p>
          </div>

          <!-- Card Details Display Box -->
          <div class="w-full bg-gray-50 rounded-2xl p-4 border border-gray-100 flex flex-col gap-2.5 text-xs text-left">
            <div class="flex items-center justify-between">
              <span class="text-gray-400">Card Number</span>
              <span class="font-mono font-bold text-gray-900 tracking-wider" id="generatedCardNumber">5412 8901 3412 7709</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-gray-400">Cardholder</span>
              <span class="font-bold text-gray-900 uppercase" id="generatedCardHolder">Sarah Jenkins</span>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div class="flex items-center justify-between">
                <span class="text-gray-400">Expires</span>
                <span class="font-mono font-bold text-gray-900" id="generatedCardExpiry">06/31</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-gray-400">CVV</span>
                <span class="font-mono font-bold text-gray-900" id="generatedCardCvv">492</span>
              </div>
            </div>
            <div class="flex items-center justify-between pt-1 border-t border-gray-200/60">
              <span class="text-gray-400">Monthly Limit</span>
            <span class="font-bold text-emerald-700" id="generatedCardLimitDisplay">₦0.00 NGN</span>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-3 w-full pt-1">
            <button type="button" class="flex-1 py-3 rounded-full bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-semibold transition text-center active:scale-95" onclick="navigator.clipboard.writeText(document.getElementById('generatedCardNumber').textContent); alert('Card number copied!');">
              Copy Number
            </button>
            <button type="button" id="cardSuccessDoneBtn" class="flex-1 py-3 rounded-full bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-bold transition text-center shadow-md active:scale-95">
              Done &amp; View Cards
            </button>
          </div>

        </div>

      </div>

    </div>
  </div>

  <!-- INTERACTIVE RECEIPT MODAL -->
  <div id="receiptModal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-sm rounded-3xl p-6 shadow-xl border border-gray-200 flex flex-col gap-4">
      <div class="flex items-center justify-between border-b border-gray-100 pb-3">
        <h4 class="text-base font-bold text-gray-900">Card Transaction Receipt</h4>
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
          <span class="text-gray-400">Merchant</span>
          <span id="receiptBeneficiary" class="font-bold text-gray-900">--</span>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-gray-400">Auth Code</span>
          <span id="receiptRef" class="font-mono font-bold text-gray-800">--</span>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-gray-400">Card Billed</span>
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

  <!-- Cards Page JavaScript Logic -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Mock Cards Database
      const cardsData = {
        'card-1': {
          id: 'card-1',
          name: 'Sarah Jenkins',
          type: 'Virtual Debit',
          purpose: 'Cloud & SaaS Infrastructure',
          numberMasked: '•••• •••• •••• 4829',
          numberFull: '5412 7532 9018 4829',
          expiry: '08/29',
          cvv: '832',
          pin: '4091',
          wallet: 'USD',
          limitSpent: 14280,
          limitTotal: 25000,
          token: 'tok_mastercard_8920184',
          isFrozen: false,
          network: 'mastercard'
        },
        'card-2': {
          id: 'card-2',
          name: 'Alexander Chukwuemeka',
          type: 'Virtual Debit',
          purpose: 'Marketing & Global Paid Ads',
          numberMasked: '•••• •••• •••• 9104',
          numberFull: '4111 8291 0034 9104',
          expiry: '11/28',
          cvv: '419',
          pin: '7721',
          wallet: 'USD',
          limitSpent: 18200,
          limitTotal: 30000,
          token: 'tok_visa_7740192',
          isFrozen: false,
          network: 'visa'
        },
        'card-3': {
          id: 'card-3',
          name: 'Sarah Jenkins',
          type: 'Physical Metal',
          purpose: 'Executive Travel & Stipend',
          numberMasked: '•••• •••• •••• 3012',
          numberFull: '4242 4242 4242 3012',
          expiry: '05/30',
          cvv: '109',
          pin: '9845',
          wallet: 'GBP',
          limitSpent: 4200,
          limitTotal: 10000,
          token: 'tok_visa_metal_3012',
          isFrozen: false,
          network: 'visa'
        },
        'card-4': {
          id: 'card-4',
          name: 'Hans Becker GmbH',
          type: 'Virtual Debit',
          purpose: 'EU Operations & Logistics',
          numberMasked: '•••• •••• •••• 7745',
          numberFull: '5500 2910 8820 7745',
          expiry: '02/29',
          cvv: '667',
          pin: '1209',
          wallet: 'EUR',
          limitSpent: 7800,
          limitTotal: 20000,
          token: 'tok_mastercard_7745',
          isFrozen: false,
          network: 'mastercard'
        }
      };

      let activeCardKey = 'card-1';
      let isDetailsRevealed = false;

      const cardSelectDropdown = document.getElementById('cardSelectDropdown');
      const cardNumberDisplay = document.getElementById('cardNumberDisplay');
      const cardHolderDisplay = document.getElementById('cardHolderDisplay');
      const cardExpiryDisplay = document.getElementById('cardExpiryDisplay');
      const cardCvvDisplay = document.getElementById('cardCvvDisplay');
      const cardPurposeDisplay = document.getElementById('cardPurposeDisplay');
      const cardTypeBadge = document.getElementById('cardTypeBadge');
      const cardNetworkLogo = document.getElementById('cardNetworkLogo');
      const cardFrozenOverlay = document.getElementById('cardFrozenOverlay');
      const toggleRevealDetailsBtn = document.getElementById('toggleRevealDetailsBtn');
      const revealText = document.getElementById('revealText');
      const toggleFreezeCardBtn = document.getElementById('toggleFreezeCardBtn');
      const freezeText = document.getElementById('freezeText');
      const viewPinBtn = document.getElementById('viewPinBtn');
      const copyCardNumberBtn = document.getElementById('copyCardNumberBtn');

      const cardDetailTitle = document.getElementById('cardDetailTitle');
      const cardDetailSubtitle = document.getElementById('cardDetailSubtitle');
      const cardLimitDisplay = document.getElementById('cardLimitDisplay');
      const cardLimitProgressBar = document.getElementById('cardLimitProgressBar');
      const cardTokenDisplay = document.getElementById('cardTokenDisplay');
      const cardStatusBadge = document.getElementById('cardStatusBadge');

      function updateCardVisual() {
        const c = cardsData[activeCardKey];
        if (!c) return;

        cardHolderDisplay.textContent = c.name;
        cardPurposeDisplay.textContent = c.purpose;
        cardTypeBadge.textContent = c.type;
        cardDetailTitle.textContent = c.purpose + ' Card';
        cardDetailSubtitle.textContent = `Linked to Corporate ${c.wallet} Wallet (${c.type})`;
        cardTokenDisplay.textContent = c.token;

        // Limit progress
        const pct = Math.round((c.limitSpent / c.limitTotal) * 100);
        cardLimitDisplay.textContent = `$${c.limitSpent.toLocaleString()}.00 / $${c.limitTotal.toLocaleString()}.00`;
        cardLimitProgressBar.style.width = `${pct}%`;

        // Network Logo
        if (c.network === 'visa') {
          cardNetworkLogo.innerHTML = '<span class="font-black italic text-lg tracking-widest text-white">VISA</span>';
        } else {
          cardNetworkLogo.innerHTML = `
            <div class="flex -space-x-2.5">
              <div class="w-6 h-6 rounded-full bg-red-500/90 shadow-xs"></div>
              <div class="w-6 h-6 rounded-full bg-amber-400/90 shadow-xs"></div>
            </div>`;
        }

        // Freeze status
        if (c.isFrozen) {
          cardFrozenOverlay.classList.remove('hidden');
          freezeText.textContent = 'Unfreeze Card';
          cardStatusBadge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200/60 flex items-center gap-1';
          cardStatusBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span> Frozen';
        } else {
          cardFrozenOverlay.classList.add('hidden');
          freezeText.textContent = 'Freeze Card';
          cardStatusBadge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60 flex items-center gap-1';
          cardStatusBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active';
        }

        // Numbers reveal
        if (isDetailsRevealed) {
          cardNumberDisplay.textContent = c.numberFull;
          cardExpiryDisplay.textContent = c.expiry;
          cardCvvDisplay.textContent = c.cvv;
          revealText.textContent = 'Hide Details';
        } else {
          cardNumberDisplay.textContent = c.numberMasked;
          cardExpiryDisplay.textContent = c.expiry;
          cardCvvDisplay.textContent = '•••';
          revealText.textContent = 'Reveal Details';
        }
      }

      if (cardSelectDropdown) {
        cardSelectDropdown.addEventListener('change', (e) => {
          activeCardKey = e.target.value;
          isDetailsRevealed = false;
          updateCardVisual();
        });
      }

      // Toggle Reveal
      if (toggleRevealDetailsBtn) {
        toggleRevealDetailsBtn.addEventListener('click', () => {
          isDetailsRevealed = !isDetailsRevealed;
          updateCardVisual();
        });
      }

      // Toggle Freeze
      if (toggleFreezeCardBtn) {
        toggleFreezeCardBtn.addEventListener('click', () => {
          const c = cardsData[activeCardKey];
          c.isFrozen = !c.isFrozen;
          updateCardVisual();
        });
      }

      // View PIN
      if (viewPinBtn) {
        viewPinBtn.addEventListener('click', () => {
          const c = cardsData[activeCardKey];
          alert(`Card Security PIN for ${c.purpose} (•••• ${c.numberFull.slice(-4)}): \n\nPIN: ${c.pin}\n\nNever share your PIN with anyone.`);
        });
      }

      // Copy Number
      if (copyCardNumberBtn) {
        copyCardNumberBtn.addEventListener('click', () => {
          const c = cardsData[activeCardKey];
          navigator.clipboard.writeText(c.numberFull.replace(/\s+/g, ''));
          alert('16-digit Card Number copied to clipboard: ' + c.numberFull);
        });
      }

      // ===========================================
      // GENERATE CARD MODAL LOGIC
      // ===========================================
      const generateCardModal = document.getElementById('generateCardModal');
      const openGenerateCardModalBtn = document.getElementById('openGenerateCardModalBtn');
      const closeGenerateCardModalBtn = document.getElementById('closeGenerateCardModalBtn');
      const cardModalBackBtn = document.getElementById('cardModalBackBtn');
      const generateCardForm = document.getElementById('generateCardForm');
      const cardRollingScreen = document.getElementById('cardRollingScreen');
      const cardSuccessScreen = document.getElementById('cardSuccessScreen');
      const cardSuccessDoneBtn = document.getElementById('cardSuccessDoneBtn');
      const cardModalTitle = document.getElementById('cardModalTitle');
      const cardModalSubtitle = document.getElementById('cardModalSubtitle');

      function resetCardModal() {
        generateCardForm.classList.remove('hidden');
        cardRollingScreen.classList.add('hidden');
        cardSuccessScreen.classList.add('hidden');
        cardModalTitle.textContent = 'Generate Corporate Card';
        cardModalSubtitle.textContent = 'Step 1 of 2 • Card Specifications';
      }

      if (openGenerateCardModalBtn) {
        openGenerateCardModalBtn.addEventListener('click', () => {
          resetCardModal();
          generateCardModal.classList.remove('hidden');
        });
      }

      if (closeGenerateCardModalBtn) {
        closeGenerateCardModalBtn.addEventListener('click', () => {
          generateCardModal.classList.add('hidden');
          resetCardModal();
        });
      }

      if (cardSuccessDoneBtn) {
        cardSuccessDoneBtn.addEventListener('click', () => {
          generateCardModal.classList.add('hidden');
          resetCardModal();
        });
      }

      // Form submission -> rolling -> success
      if (generateCardForm) {
        generateCardForm.addEventListener('submit', (e) => {
          e.preventDefault();
          const label = document.getElementById('newCardLabel').value || 'Corporate SaaS Card';
          const holder = document.getElementById('newCardHolder').value || 'Sarah Jenkins';
          const network = document.getElementById('newCardNetwork').value;
          const wallet = document.getElementById('newCardWallet').value;
          const limit = parseFloat(document.getElementById('newCardLimit').value) || 10000;

          // Generate simulated card numbers
          const bin = network === 'visa' ? '4111' : '5412';
          const mid1 = Math.floor(1000 + Math.random() * 9000);
          const mid2 = Math.floor(1000 + Math.random() * 9000);
          const last4 = Math.floor(1000 + Math.random() * 9000);
          const fullNum = `${bin} ${mid1} ${mid2} ${last4}`;
          const cvv = Math.floor(100 + Math.random() * 900).toString();
          const newCardId = 'card-' + (Object.keys(cardsData).length + 1);

          // Add to local database
          cardsData[newCardId] = {
            id: newCardId,
            name: holder,
            type: 'Virtual Debit',
            purpose: label,
            numberMasked: `•••• •••• •••• ${last4}`,
            numberFull: fullNum,
            expiry: '06/31',
            cvv: cvv,
            pin: Math.floor(1000 + Math.random() * 9000).toString(),
            wallet: wallet,
            limitSpent: 0,
            limitTotal: limit,
            token: `tok_${network}_${last4}`,
            isFrozen: false,
            network: network
          };

          // Transition to rolling
          generateCardForm.classList.add('hidden');
          cardRollingScreen.classList.remove('hidden');
          cardModalTitle.textContent = 'Provisioning Card';
          cardModalSubtitle.textContent = 'Clearing with issuing processor...';

          setTimeout(() => {
            cardRollingScreen.classList.add('hidden');
            cardSuccessScreen.classList.remove('hidden');

            document.getElementById('generatedCardTitle').textContent = label;
            document.getElementById('generatedCardNumber').textContent = fullNum;
            document.getElementById('generatedCardHolder').textContent = holder;
            document.getElementById('generatedCardExpiry').textContent = '06/31';
            document.getElementById('generatedCardCvv').textContent = cvv;
            document.getElementById('generatedCardLimitDisplay').textContent = `$${limit.toLocaleString()}.00 ${wallet}`;

            cardModalTitle.textContent = 'Card Issued Successfully';
            cardModalSubtitle.textContent = 'Ready for immediate online payment';

            // Add new card to select dropdown
            const opt = document.createElement('option');
            opt.value = newCardId;
            opt.textContent = `${label} (•••• ${last4})`;
            opt.selected = true;
            cardSelectDropdown.appendChild(opt);

            activeCardKey = newCardId;
            updateCardVisual();
          }, 1600);
        });
      }

      // Close modal on outside click
      [generateCardModal, document.getElementById('receiptModal')].forEach(modal => {
        if (modal) {
          modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.classList.add('hidden');
          });
        }
      });

      // Filter transactions
      const searchInput = document.getElementById('cardTxSearch');
      const statusFilter = document.getElementById('cardTxStatusFilter');
      const txRows = document.querySelectorAll('#cardTxTableBody tr');

      function filterTx() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const st = statusFilter ? statusFilter.value : 'all';

        txRows.forEach(row => {
          const rowStatus = row.getAttribute('data-status');
          const rowText = row.textContent.toLowerCase();

          const matchesQuery = !query || rowText.includes(query);
          const matchesStatus = st === 'all' || rowStatus === st;

          if (matchesQuery && matchesStatus) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      }

      if (searchInput) searchInput.addEventListener('input', filterTx);
      if (statusFilter) statusFilter.addEventListener('change', filterTx);

      // Receipt modal close
      const closeReceiptBtn = document.getElementById('closeReceiptBtn');
      if (closeReceiptBtn) {
        closeReceiptBtn.addEventListener('click', () => {
          document.getElementById('receiptModal').classList.add('hidden');
        });
      }
    });

    // Receipt helper for card tx
    function showReceipt(ref, merchant, amount, rail, status) {
      document.getElementById('receiptRef').textContent = ref;
      document.getElementById('receiptBeneficiary').textContent = merchant;
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
      }

      document.getElementById('receiptModal').classList.remove('hidden');
    }
  </script>

  <script>
    // Replace the prototype card UI with provider-backed, non-sensitive data.
    (async function loadLiveCards() {
      const main = document.getElementById('cardsPageMain');
      const esc = (value) => String(value ?? '').replace(/[&<>'"]/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','\"':'&quot;'}[ch]));
      const money = (value, currency) => {
        const n = Number(value || 0);
        return new Intl.NumberFormat('en-NG', {style:'currency', currency: currency || 'NGN', maximumFractionDigits: 2}).format(Number.isFinite(n) ? n : 0);
      };
      const empty = (message) => {
        const body = document.getElementById('cardTxTableBody');
        if (body) body.innerHTML = `<tr><td colspan="7" class="py-10 text-center text-xs text-gray-400">${esc(message)}</td></tr>`;
      };
      try {
        const authHeaders = {Authorization:`Bearer ${localStorage.getItem('flow_token') || ''}`,Accept:'application/json'};
        const [cardsResponse, txResponse] = await Promise.all([
          fetch('../api/cards/list.php', {credentials:'same-origin', headers:authHeaders, cache:'no-store'}),
          fetch('../api/transactions/list.php?channel=card&type=payment&range=all&page_size=50', {credentials:'same-origin', headers:authHeaders, cache:'no-store'})
        ]);
        const cardsPayload = await cardsResponse.json();
        const txPayload = await txResponse.json();
        if (!cardsPayload.success) throw new Error(cardsPayload.error || 'Unable to load cards.');
        const cards = Array.isArray(cardsPayload.cards) ? cardsPayload.cards : [];
        const summary = cardsPayload.summary || {};
        const currency = cards[0]?.currency || 'NGN';
        const monthlySpend = Number(summary.total_spend || 0), totalLimit = Number(summary.total_limit || 0);
        const spendMetric = document.getElementById('cardSpendMetric'); if (spendMetric) spendMetric.textContent = money(monthlySpend, currency);
        const limitMetric = document.getElementById('cardLimitMetric'); if (limitMetric) limitMetric.textContent = `Limit: ${money(totalLimit, currency)}`;
        const spendPercent = totalLimit > 0 ? Math.min(100, monthlySpend / totalLimit * 100) : 0;
        const percentMetric = document.getElementById('cardSpendPercentMetric'); if (percentMetric) percentMetric.textContent = totalLimit > 0 ? `${spendPercent.toFixed(1)}% used` : '—';
        const spendProgress = document.getElementById('cardSpendProgress'); if (spendProgress) spendProgress.style.width = `${spendPercent}%`;
        const activeMetric = document.getElementById('activeCardsMetric'); if (activeMetric) activeMetric.textContent = `${Number(summary.active_count || 0)} Cards`;
        const virtualCount = cards.filter(card => card.type === 'virtual' && card.status === 'active').length, physicalCount = cards.filter(card => card.type === 'physical' && card.status === 'active').length;
        const activeDetail = document.getElementById('activeCardsDetail'); if (activeDetail) activeDetail.textContent = `${virtualCount} Virtual • ${physicalCount} Physical`;
        const funding = cards.reduce((sum, card) => sum + Number(card.available_balance || 0), 0);
        const fundingMetric = document.getElementById('cardFundingMetric'); if (fundingMetric) fundingMetric.textContent = money(funding, currency);
        const fundingDetail = document.getElementById('cardFundingDetail'); if (fundingDetail) fundingDetail.textContent = cards.length ? 'Linked wallet balances' : 'No funding wallets linked';
        const select = document.getElementById('cardSelectDropdown');
        const totalSpend = document.querySelector('.grid.grid-cols-1.sm\\:grid-cols-2.lg\\:grid-cols-4 > div:first-child .text-2xl');
        const count = document.querySelector('.grid.grid-cols-1.sm\\:grid-cols-2.lg\\:grid-cols-4 > div:nth-child(2) .text-2xl');
        if (totalSpend) totalSpend.textContent = money(summary.total_spend, cards[0]?.currency || 'NGN');
        if (count) count.textContent = `${Number(summary.active_count || 0)} Active Cards`;
        const activeBadge = document.getElementById('cardsActiveBadge'); if (activeBadge) activeBadge.textContent = `${Number(summary.active_count || 0)} Active Cards`;
        if (select) {
          select.innerHTML = cards.length ? cards.map((c, i) => `<option value="${esc(c.id)}" ${i===0?'selected':''}>${esc(c.type)} •••• ${esc(c.last4)} (${esc(c.currency)})</option>`).join('') : '<option value="">No issued cards</option>';
        }
        const cardsById = Object.fromEntries(cards.map(c => [String(c.id), c]));
        const renderCard = (card) => {
          if (!card) {
            document.getElementById('cardNumberDisplay').textContent = 'No card issued';
            document.getElementById('cardPurposeDisplay').textContent = 'Issue a card when a card provider is configured';
            document.getElementById('cardHolderDisplay').textContent = '—';
            document.getElementById('cardExpiryDisplay').textContent = '—';
            document.getElementById('cardLimitDisplay').textContent = '—';
            document.getElementById('cardTokenDisplay').textContent = 'Provider-managed';
            return;
          }
          const currency = card.currency || 'NGN';
          const spent = Number(card.month_spend || 0), limit = Number(card.spending_limit || 0);
          document.getElementById('cardNumberDisplay').textContent = `•••• •••• •••• ${card.last4}`;
          document.getElementById('cardPurposeDisplay').textContent = `${card.type === 'physical' ? 'Physical' : 'Virtual'} card • ${currency}`;
          document.getElementById('cardHolderDisplay').textContent = 'Cardholder';
          document.getElementById('cardExpiryDisplay').textContent = '••/••';
          document.getElementById('cardCvvDisplay').textContent = '•••';
          document.getElementById('cardTypeBadge').textContent = card.type;
          document.getElementById('cardDetailTitle').textContent = `${card.type === 'physical' ? 'Physical' : 'Virtual'} ${currency} Card`;
          document.getElementById('cardDetailSubtitle').textContent = `Issued by ${card.provider || 'configured provider'}`;
          document.getElementById('cardTokenDisplay').textContent = 'Provider-managed';
          document.getElementById('cardLimitDisplay').textContent = limit > 0 ? `${money(spent,currency)} / ${money(limit,currency)}` : `${money(spent,currency)} spent`;
          const remainingLimit = document.getElementById('cardRemainingLimit'); if (remainingLimit) remainingLimit.textContent = limit > 0 ? `${money(Math.max(0, limit - spent), currency)} remaining` : 'No limit configured';
          document.getElementById('cardLimitProgressBar').style.width = `${limit > 0 ? Math.min(100, Math.round(spent / limit * 100)) : 0}%`;
          const badge = document.getElementById('cardStatusBadge');
          badge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full ${card.status === 'frozen' ? 'bg-sky-500' : 'bg-emerald-500'}"></span> ${esc(card.status)}`;
          badge.className = `px-2.5 py-1 rounded-full text-xs font-bold ${card.status === 'frozen' ? 'bg-sky-50 text-sky-700 border-sky-200/60' : 'bg-emerald-50 text-emerald-700 border-emerald-200/60'} border flex items-center gap-1`;
        };
        renderCard(cards[0]);
        if (select) select.onchange = () => renderCard(cardsById[String(select.value)]);
        const body = document.getElementById('cardTxTableBody');
        const txs = Array.isArray(txPayload.transactions) ? txPayload.transactions : [];
        if (body) body.innerHTML = txs.length ? txs.map(tx => `<tr class="hover:bg-gray-50/70 transition" data-status="${esc(tx.status)}"><td class="py-3.5 px-4"><span class="font-bold text-gray-900 block">${esc(tx.customer_name === '—' ? 'Card payment' : tx.customer_name)}</span><span class="text-[11px] text-gray-400">${esc(tx.channel)}</span></td><td class="py-3.5 px-4 font-medium text-gray-700">•••• ${esc(tx.card_last4 || '—')}</td><td class="py-3.5 px-4 text-gray-500">${esc(tx.created_at)}</td><td class="py-3.5 px-4 font-mono text-gray-500">${esc(tx.reference)}</td><td class="py-3.5 px-4 font-black text-gray-900">${esc(money(tx.amount,tx.currency))}</td><td class="py-3.5 px-4 text-center"><span class="text-[11px] font-bold">${esc(tx.status)}</span></td><td class="py-3.5 px-4 text-center">—</td></tr>`).join('') : '<tr><td colspan="7" class="py-10 text-center text-xs text-gray-400">No card transactions yet.</td></tr>';
        document.querySelectorAll('#toggleRevealDetailsBtn,#viewPinBtn,#copyCardNumberBtn').forEach(btn => { btn.disabled = true; btn.title = 'Sensitive card details are available only through the issuing provider.'; btn.classList.add('opacity-50','cursor-not-allowed'); });
        const generate = document.getElementById('openGenerateCardModalBtn');
        if (generate) { generate.disabled = true; generate.querySelector('span:last-child').textContent = 'Card issuing unavailable'; generate.title = 'Configure a card issuing provider before issuing cards.'; generate.classList.add('opacity-60','cursor-not-allowed'); }
        const freeze = document.getElementById('toggleFreezeCardBtn');
        if (freeze) freeze.onclick = async () => {
          const card = cardsById[String(select?.value || '')]; if (!card) return;
          const action = card.status === 'frozen' ? 'unfreeze' : 'freeze';
          const response = await fetch('../api/cards/action.php', {method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/json',Authorization:`Bearer ${localStorage.getItem('flow_token') || ''}`}, body:JSON.stringify({card_id:card.id,action})});
          const result = await response.json(); if (!result.success) { alert(result.error || 'Unable to update card.'); return; }
          card.status = result.status; renderCard(card);
        };
      } catch (error) {
        empty('Cards are unavailable until a card issuing provider is configured.');
        document.getElementById('cardNumberDisplay').textContent = 'No card data';
        ['cardPurposeDisplay','cardHolderDisplay','cardExpiryDisplay','cardCvvDisplay','cardLimitDisplay','cardTokenDisplay','cardRemainingLimit'].forEach(id => { const el=document.getElementById(id); if(el) el.textContent='—'; });
        const progress=document.getElementById('cardLimitProgressBar'); if(progress) progress.style.width='0%';
        const select=document.getElementById('cardSelectDropdown'); if(select) select.innerHTML='<option value="">No issued cards</option>';
      } finally {
        if (main) main.classList.remove('cards-live-pending');
      }
    })();
  </script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
