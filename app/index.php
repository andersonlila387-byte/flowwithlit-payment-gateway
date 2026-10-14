<?php
/**
 * Flowwithlit Main Dashboard Page
 */
$pageTitle = 'Flowwithlit - Financial Dashboard';
$activePage = 'dashboard';
$activeNavTab = 'Overview';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

    <!-- Scrollable Main Dashboard Area -->
    <main class="flex-1 h-full overflow-y-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-6 flex flex-col gap-6">
      
      <!-- Welcome Greeting & Quick Controls Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 flex-shrink-0">
        <div>
          <p id="dashboardDate" class="text-xs text-gray-400 font-medium tracking-wide"></p>
          <h1 id="dashboardGreeting" class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight mt-0.5">Welcome back</h1>
        </div>

        <!-- Action buttons (Search, Filter, Date Range) -->
        <div class="flex items-center gap-2.5">
          <!-- Search Button -->
          <button aria-label="Search" class="w-10 h-10 rounded-full bg-white hover:bg-gray-50 border border-gray-200/80 flex items-center justify-center text-gray-500 hover:text-gray-900 shadow-sm transition hover:scale-105 active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <circle cx="11" cy="11" r="7" />
              <path stroke-linecap="round" d="M21 21l-4.35-4.35" />
            </svg>
          </button>

          <!-- Sliders / Filter Button -->
          <button aria-label="Filters" class="w-10 h-10 rounded-full bg-white hover:bg-gray-50 border border-gray-200/80 flex items-center justify-center text-gray-500 hover:text-gray-900 shadow-sm transition hover:scale-105 active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" d="M4 6h10M18 6h2M8 12h12M4 12h2M14 18h6M4 18h6" />
              <circle cx="16" cy="6" r="2" />
              <circle cx="6" cy="12" r="2" />
              <circle cx="12" cy="18" r="2" />
            </svg>
          </button>

          <!-- Date Range Pill -->
          <button class="px-4 py-2 rounded-full bg-white hover:bg-gray-50 border border-gray-200/80 flex items-center gap-2 text-xs font-semibold text-gray-700 shadow-sm transition hover:scale-105 active:scale-95">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
              <rect x="3" y="4" width="18" height="18" rx="3" />
              <path d="M16 2v4M8 2v4M3 10h18" stroke-linecap="round" />
            </svg>
            <span>11-17 June , 2026</span>
          </button>
        </div>
      </div>

      <!-- Row 1: 3 KPI Cards (Total Balance + Monthly Spending + Card Usage) -->
      <div class="grid grid-cols-1 md:grid-cols-12 gap-5 lg:gap-6 flex-shrink-0">
        
        <!-- Card 1: Total Balance (spans 6 cols on lg) -->
        <div class="md:col-span-12 lg:col-span-6 bg-white rounded-[28px] p-6 lg:p-7 shadow-sm border border-gray-200/80 flex flex-col justify-between gap-5 relative">
          <div>
            <!-- Header with Currency and Period Dropdowns -->
            <div class="flex items-center justify-between relative">
              <span class="text-xs font-semibold text-gray-500 tracking-wide">Total Balance</span>
              
              <div class="flex items-center gap-2">
                
                <!-- Currency / Country Dropdown Wrapper -->
                <div class="relative" id="currencyDropdownWrapper">
                  <button id="currencyBtn" class="flex items-center gap-1.5 bg-gray-50 hover:bg-gray-100 border border-gray-200/80 rounded-full px-3 py-1.5 text-xs font-semibold text-gray-700 transition active:scale-95 shadow-xs" aria-expanded="false" aria-haspopup="true">
                    <!-- Dynamic Flag Icon Container -->
                    <span id="currencyFlagContainer" class="w-4 h-4 rounded-full overflow-hidden inline-flex items-center justify-center flex-shrink-0 shadow-2xs border border-gray-200/60">
                      <!-- Default: Nigerian Flag SVG (NGN is the selected currency) -->
                      <svg viewBox="0 0 640 480" class="w-full h-full object-cover">
                        <path fill="#008751" d="M0 0h213.3v480H0zm426.7 0H640v480H426.7z"/>
                        <path fill="#ffffff" d="M213.3 0h213.4v480H213.3z"/>
                      </svg>
                    </span>
                    <span id="currencyText">NGN</span>
                    <svg id="currencyChevron" class="w-3 h-3 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                  </button>

                  <!-- Currency Dropdown Menu -->
                  <div id="currencyDropdownMenu" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50 transform origin-top-right transition-all duration-150 animate-in fade-in zoom-in-95">
                    <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-400 border-b border-gray-100 pb-1.5 mb-1">
                      Select Currency &amp; Country
                    </div>

                    <!-- Currency Options List -->
                    <div class="max-h-60 overflow-y-auto space-y-0.5 px-1.5" id="currencyOptionsList">
                      <!-- Populated dynamically via JS -->
                    </div>
                  </div>
                </div>

                <!-- Time Period Dropdown Wrapper -->
                <div class="relative" id="periodDropdownWrapper">
                  <button id="periodBtn" class="flex items-center gap-1.5 bg-gray-50 hover:bg-gray-100 border border-gray-200/80 rounded-full px-3 py-1.5 text-xs font-semibold text-gray-700 transition active:scale-95 shadow-xs" aria-expanded="false" aria-haspopup="true">
                    <span id="periodText">All Time</span>
                    <svg id="periodChevron" class="w-3 h-3 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                  </button>

                  <!-- Period Dropdown Menu -->
                  <div id="periodDropdownMenu" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50 transform origin-top-right transition-all duration-150 animate-in fade-in zoom-in-95">
                    <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-400 border-b border-gray-100 pb-1.5 mb-1">
                      Filter Time Range
                    </div>

                    <div class="space-y-0.5 px-1.5" id="periodOptionsList">
                      <!-- Populated dynamically via JS -->
                    </div>
                  </div>
                </div>

              </div>
            </div>

            <!-- Main Amount & Visibility Toggle -->
            <div class="flex items-center gap-3 mt-3">
              <span id="totalBalanceVal" class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight transition-all duration-200">₦0.00</span>
              <button id="toggleBalanceBtn" class="w-7 h-7 rounded-full bg-gray-50 hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-700 transition active:scale-95" title="Toggle balance privacy">
                <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </button>
            </div>
            <p id="availableCurrencyVal" class="mt-1 text-xs font-semibold text-gray-500">Available NGN balance</p>

            <!-- Indicator Badge -->
            <div class="flex items-center gap-1.5 mt-2 text-xs font-medium text-gray-500">
              <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/>
                </svg>
              </span>
              <span id="balanceTrendBadge">Loading balance data…</span>
            </div>
          </div>

          <!-- Action Buttons: Add Money (Navy Blue), Send Money, Request Money -->
          <div class="flex items-center gap-2.5 flex-wrap pt-2">
            <!-- Navy Blue Add Money Button -->
            <a href="add-money.php" class="bg-[#0a192f] hover:bg-[#132847] text-white px-4 py-2.5 rounded-full text-xs font-semibold flex items-center gap-2 shadow-sm transition hover:scale-105 active:scale-95">
              <span class="w-4 h-4 rounded-full border border-white/40 flex items-center justify-center text-[11px] leading-none">+</span>
              <span>Add Money</span>
            </a>

            <a href="send-money.php" class="bg-[#f3f5f4] hover:bg-gray-200/80 dark:bg-[#16304d] dark:hover:bg-[#1d3c5e] text-gray-800 dark:text-white px-4 py-2.5 rounded-full text-xs font-semibold flex items-center gap-2 border border-gray-200/60 dark:border-gray-600 transition hover:scale-105 active:scale-95">
              <span class="w-4 h-4 rounded-full border border-gray-400 dark:border-gray-300 flex items-center justify-center text-[10px] leading-none">$</span>
              <span>Send Money</span>
            </a>

            <a href="request-money.php" class="bg-[#f3f5f4] hover:bg-gray-200/80 dark:bg-[#16304d] dark:hover:bg-[#1d3c5e] text-gray-800 dark:text-white px-4 py-2.5 rounded-full text-xs font-semibold flex items-center gap-2 border border-gray-200/60 dark:border-gray-600 transition hover:scale-105 active:scale-95">
              <svg class="w-3.5 h-3.5 text-gray-600 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <rect x="3" y="5" width="18" height="14" rx="2"/>
                <path d="M7 15h4M17 9h.01"/>
              </svg>
              <span>Request Money</span>
            </a>
          </div>
        </div>

        <!-- Card 2: Monthly Spending (spans 3 cols on lg) -->
        <div class="md:col-span-6 lg:col-span-3 bg-white rounded-[28px] p-6 shadow-sm border border-gray-200/80 flex flex-col justify-between relative overflow-hidden group">
          
          <!-- Top Header & Chart Graphic -->
          <div>
            <div class="flex items-center justify-between">
              <span class="text-xs font-semibold text-gray-500 tracking-wide">Monthly Spending</span>
            </div>

            <!-- Sparkline Chart with light blue gradient fill and key dot -->
            <div class="h-20 w-full relative mt-2">
              <svg class="w-full h-full overflow-visible" viewBox="0 0 220 80" preserveAspectRatio="none">
                <defs>
                  <linearGradient id="blueAreaGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#38bdf8" stop-opacity="0.28" />
                    <stop offset="100%" stop-color="#38bdf8" stop-opacity="0.0" />
                  </linearGradient>
                </defs>
                
                <!-- Area Fill -->
                <path d="M 0 65 Q 80 65 140 40 T 220 10 L 220 80 L 0 80 Z" fill="url(#blueAreaGrad)" />
                
                <!-- Vertical dashed marker line -->
                <line x1="140" y1="0" x2="140" y2="80" stroke="#bae6fd" stroke-width="1.2" stroke-dasharray="3 3" opacity="0.9" />
                
                <!-- Light Blue Line Curve -->
                <path d="M 0 65 Q 80 65 140 40 T 220 10" fill="none" stroke="#0ea5e9" stroke-width="2.5" stroke-linecap="round" />
                
                <!-- Hollow Dot on inflection -->
                <circle cx="140" cy="40" r="4.5" fill="#ffffff" stroke="#0ea5e9" stroke-width="2" />
              </svg>
            </div>
          </div>

          <!-- Bottom Stats -->
          <div class="mt-4">
            <div id="monthlySpendingVal" class="text-2xl lg:text-3xl font-extrabold text-gray-900 tracking-tight">₦0.00</div>
            <div class="flex items-center gap-1.5 mt-2 text-xs font-medium text-gray-500">
              <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/>
                </svg>
              </span>
              <span id="monthlySpendingTrend">No spending data yet</span>
            </div>
          </div>
        </div>

        <!-- Card 3: Card Usage (spans 3 cols on lg) -->
        <div class="md:col-span-6 lg:col-span-3 bg-white rounded-[28px] p-6 shadow-sm border border-gray-200/80 flex flex-col justify-between relative overflow-hidden group">
          
          <!-- Top Header & Chart Graphic -->
          <div>
            <div class="flex items-center justify-between">
              <span class="text-xs font-semibold text-gray-500 tracking-wide">Card Usage</span>
            </div>

            <!-- Sparkline Chart with pink/rose gradient fill and key dot -->
            <div class="h-20 w-full relative mt-2">
              <svg class="w-full h-full overflow-visible" viewBox="0 0 220 80" preserveAspectRatio="none">
                <defs>
                  <linearGradient id="roseAreaGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#f43f5e" stop-opacity="0.22" />
                    <stop offset="100%" stop-color="#f43f5e" stop-opacity="0.0" />
                  </linearGradient>
                </defs>
                
                <!-- Area Fill -->
                <path d="M 0 55 Q 90 55 150 30 T 220 18 L 220 80 L 0 80 Z" fill="url(#roseAreaGrad)" />
                
                <!-- Vertical dashed marker line -->
                <line x1="150" y1="0" x2="150" y2="80" stroke="#fca5a5" stroke-width="1.2" stroke-dasharray="3 3" opacity="0.8" />
                
                <!-- Rose Line Curve -->
                <path d="M 0 55 Q 90 55 150 30 T 220 18" fill="none" stroke="#e11d48" stroke-width="2.5" stroke-linecap="round" />
                
                <!-- Hollow Dot on inflection -->
                <circle cx="150" cy="30" r="4.5" fill="#ffffff" stroke="#e11d48" stroke-width="2" />
              </svg>
            </div>
          </div>

          <!-- Bottom Stats -->
          <div class="mt-4">
            <div id="cardUsageVal" class="text-2xl lg:text-3xl font-extrabold text-gray-900 tracking-tight">₦0.00</div>
            <div class="flex items-center gap-1.5 mt-2 text-xs font-medium text-gray-500">
              <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/>
                </svg>
              </span>
              <span>17% avg. this month</span>
            </div>
          </div>
        </div>

      </div>

      <!-- Row 2: 2 Large Overview Analytics Cards -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-6 flex-shrink-0">
        
        <!-- Card 4: Payment Overview with Segmented Capsule Bars (spans 6 cols) -->
        <div class="lg:col-span-6 bg-white rounded-[28px] p-6 lg:p-7 shadow-sm border border-gray-200/80 flex flex-col justify-between">
          
          <!-- Card Top Header -->
          <div>
            <span class="text-xs font-semibold text-gray-500 tracking-wide">Payment Overview</span>

            <!-- Amount & Eye toggle -->
            <div class="flex items-center gap-3 mt-2">
              <span id="paymentOverviewVal" class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">₦0.00</span>
              <button id="togglePaymentBtn" class="w-7 h-7 rounded-full bg-gray-50 hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-700 transition" title="Toggle privacy">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </button>
            </div>

            <!-- Indicator Badge -->
            <div class="flex items-center gap-1.5 mt-1 text-xs font-medium text-gray-500">
              <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/>
                </svg>
              </span>
              <span id="paymentTrend">No payment data yet</span>
            </div>

            <!-- 3 Sub-metrics Row -->
            <div class="grid grid-cols-3 gap-2 sm:gap-4 mt-5 pt-4 border-t border-gray-100">
              <div>
                <p class="text-[11px] font-medium text-gray-400">Processing Cost</p>
                <p id="processingCostVal" class="text-sm sm:text-base font-bold text-gray-900 mt-0.5">₦0.00</p>
              </div>
              <div>
                <p class="text-[11px] font-medium text-gray-400">Pipeline Output Value</p>
                <p id="pipelineOutputVal" class="text-sm sm:text-base font-bold text-gray-900 mt-0.5">₦0.00</p>
              </div>
              <div>
                <p class="text-[11px] font-medium text-gray-400">Net Efficiency Gain</p>
                <p id="netEfficiencyVal" class="text-sm sm:text-base font-bold text-gray-900 mt-0.5">₦0.00</p>
              </div>
            </div>
          </div>

          <!-- Segmented Pill Capsules Bar Chart -->
          <div id="paymentChart" class="mt-8 relative">
            <!-- Y-Axis Lines and Labels -->
            <div class="flex flex-col justify-between h-44 text-[11px] font-medium text-gray-400 absolute left-0 top-0 bottom-6 w-full pointer-events-none">
              <div class="flex items-center gap-3">
                <span class="w-10 text-left">₦0</span>
                <div class="flex-1 border-b border-gray-100"></div>
              </div>
              <div class="flex items-center gap-3">
                <span class="w-10 text-left">₦0</span>
                <div class="flex-1 border-b border-gray-100"></div>
              </div>
              <div class="flex items-center gap-3">
                <span class="w-10 text-left">₦0</span>
                <div class="flex-1 border-b border-gray-100"></div>
              </div>
              <div class="flex items-center gap-3">
                <span class="w-10 text-left">₦0</span>
                <div class="flex-1 border-b border-gray-100"></div>
              </div>
              <div class="flex items-center gap-3">
                <span class="w-10 text-left">0</span>
                <div class="flex-1 border-b border-gray-200"></div>
              </div>
            </div>

            <!-- Capsule Bars Container (offset to align with Y-axis) -->
            <div class="ml-12 h-44 flex items-end justify-between gap-2 sm:gap-4 pr-2 pb-6">
              
              <!-- 2020 Bar: 3 capsules -->
              <div class="flex flex-col items-center gap-2 group flex-1">
                <div class="flex flex-col gap-1.5 w-7 sm:w-9 items-center justify-end">
                  <div class="w-full h-8 rounded-full bg-[#e8eceb] group-hover:bg-gray-300 transition"></div>
                  <div class="w-full h-8 rounded-full bg-[#e8eceb] group-hover:bg-gray-300 transition"></div>
                  <div class="w-full h-8 rounded-full bg-[#e8eceb] group-hover:bg-gray-300 transition"></div>
                </div>
              </div>

              <!-- 2021 Bar: 4 capsules -->
              <div class="flex flex-col items-center gap-2 group flex-1">
                <div class="flex flex-col gap-1.5 w-7 sm:w-9 items-center justify-end">
                  <div class="w-full h-6 rounded-full bg-[#e8eceb] group-hover:bg-gray-300 transition"></div>
                  <div class="w-full h-6 rounded-full bg-[#e8eceb] group-hover:bg-gray-300 transition"></div>
                  <div class="w-full h-6 rounded-full bg-[#e8eceb] group-hover:bg-gray-300 transition"></div>
                  <div class="w-full h-6 rounded-full bg-[#e8eceb] group-hover:bg-gray-300 transition"></div>
                </div>
              </div>

              <!-- 2022 Bar: 4 capsules -->
              <div class="flex flex-col items-center gap-2 group flex-1">
                <div class="flex flex-col gap-1.5 w-7 sm:w-9 items-center justify-end">
                  <div class="w-full h-7 rounded-full bg-[#e8eceb] group-hover:bg-gray-300 transition"></div>
                  <div class="w-full h-7 rounded-full bg-[#e8eceb] group-hover:bg-gray-300 transition"></div>
                  <div class="w-full h-7 rounded-full bg-[#e8eceb] group-hover:bg-gray-300 transition"></div>
                  <div class="w-full h-7 rounded-full bg-[#e8eceb] group-hover:bg-gray-300 transition"></div>
                </div>
              </div>

              <!-- 2023 Bar: 3 capsules -->
              <div class="flex flex-col items-center gap-2 group flex-1">
                <div class="flex flex-col gap-1.5 w-7 sm:w-9 items-center justify-end">
                  <div class="w-full h-8 rounded-full bg-[#e8eceb] group-hover:bg-gray-300 transition"></div>
                  <div class="w-full h-8 rounded-full bg-[#e8eceb] group-hover:bg-gray-300 transition"></div>
                  <div class="w-full h-8 rounded-full bg-[#e8eceb] group-hover:bg-gray-300 transition"></div>
                </div>
              </div>

              <!-- 2024 Bar: Highlighted with Tooltip, Black Capsule & Diagonal Striped Capsule -->
              <div class="flex flex-col items-center gap-2 relative group flex-1">
                <!-- Floating Tooltip -->
                <div class="absolute -top-11 z-20 bg-white px-3 py-1 rounded-xl shadow-lg border border-gray-200/80 flex flex-col items-center text-center pointer-events-none transform -translate-y-1 transition duration-200">
                  <span class="text-xs font-extrabold text-gray-900">+134%</span>
                  <span class="text-[9px] text-gray-400 font-medium whitespace-nowrap">Monthly revenue</span>
                </div>

                <div class="flex flex-col gap-1.5 w-7 sm:w-9 items-center justify-end">
                  <!-- Top Solid Black Rounded Capsule -->
                  <div class="w-full h-14 rounded-2xl bg-black shadow-md"></div>
                  <!-- Bottom Striped Capsule -->
                  <div class="w-full h-8 rounded-full striped-pattern border border-gray-300"></div>
                </div>
              </div>

              <!-- 2025 Bar: 3 capsules -->
              <div class="flex flex-col items-center gap-2 group flex-1">
                <div class="flex flex-col gap-1.5 w-7 sm:w-9 items-center justify-end">
                  <div class="w-full h-7 rounded-full bg-[#e8eceb] group-hover:bg-gray-300 transition"></div>
                  <div class="w-full h-7 rounded-full bg-[#e8eceb] group-hover:bg-gray-300 transition"></div>
                  <div class="w-full h-7 rounded-full bg-[#e8eceb] group-hover:bg-gray-300 transition"></div>
                </div>
              </div>

              <!-- 2026 Bar: 2 capsules -->
              <div class="flex flex-col items-center gap-2 group flex-1">
                <div class="flex flex-col gap-1.5 w-7 sm:w-9 items-center justify-end">
                  <div class="w-full h-7 rounded-full bg-[#e8eceb] group-hover:bg-gray-300 transition"></div>
                  <div class="w-full h-7 rounded-full bg-[#e8eceb] group-hover:bg-gray-300 transition"></div>
                </div>
              </div>

            </div>

            <!-- X-Axis Labels -->
            <div class="ml-12 flex items-center justify-between text-xs font-semibold text-gray-400 pt-2 px-2">
              <span>2020</span>
              <span>2021</span>
              <span>2022</span>
              <span>2023</span>
              <span class="text-gray-800 font-bold">2024</span>
              <span>2025</span>
              <span>2026</span>
            </div>
          </div>

        </div>

        <!-- Card 5: Earning Overview with Multi-Bar Chart (spans 6 cols) -->
        <div class="lg:col-span-6 bg-white rounded-[28px] p-6 lg:p-7 shadow-sm border border-gray-200/80 flex flex-col justify-between">
          
          <!-- Card Top Header -->
          <div>
            <span class="text-xs font-semibold text-gray-500 tracking-wide">Earning Overview</span>

            <!-- Amount & Eye toggle -->
            <div class="flex items-center gap-3 mt-2">
              <span id="earningOverviewVal" class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">₦0.00</span>
              <button id="toggleEarningBtn" class="w-7 h-7 rounded-full bg-gray-50 hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-700 transition" title="Toggle privacy">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </button>
            </div>

            <!-- Indicator Badge -->
            <div class="flex items-center gap-1.5 mt-1 text-xs font-medium text-gray-500">
              <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/>
                </svg>
              </span>
              <span id="earningTrend">No earnings data yet</span>
            </div>
          </div>

          <!-- Detailed Bar Chart Area with Y-axis and Months -->
          <div id="earningChart" class="mt-8 relative">
            
            <!-- Y-Axis Grid Lines & Numbers -->
            <div class="flex flex-col justify-between h-48 text-[11px] font-medium text-gray-400 absolute left-0 top-0 bottom-6 w-full pointer-events-none">
              <div class="flex items-center gap-3">
                <span class="w-7 text-left">400</span>
                <div class="flex-1 border-b border-gray-100"></div>
              </div>
              <div class="flex items-center gap-3">
                <span class="w-7 text-left">300</span>
                <div class="flex-1 border-b border-gray-100"></div>
              </div>
              <div class="flex items-center gap-3">
                <span class="w-7 text-left">200</span>
                <div class="flex-1 border-b border-gray-100"></div>
              </div>
              <div class="flex items-center gap-3">
                <span class="w-7 text-left">100</span>
                <div class="flex-1 border-b border-gray-100"></div>
              </div>
              <div class="flex items-center gap-3">
                <span class="w-7 text-left">0</span>
                <div class="flex-1 border-b border-gray-200"></div>
              </div>
            </div>

            <!-- Month Clusters Bars Area -->
            <div class="ml-9 h-48 flex items-end justify-between gap-1 sm:gap-2 pb-6 px-1 relative">
              
              <!-- Jan Cluster -->
              <div class="flex items-end justify-center gap-1 flex-1 h-full">
                <div class="w-1.5 sm:w-2 rounded-full bg-[#e5e7eb] h-[45%] hover:bg-gray-400 transition"></div>
                <div class="w-1.5 sm:w-2 rounded-full bg-[#e5e7eb] h-[75%] hover:bg-gray-400 transition"></div>
                <div class="w-1.5 sm:w-2 rounded-full bg-[#e5e7eb] h-[60%] hover:bg-gray-400 transition"></div>
              </div>

              <!-- Feb Cluster -->
              <div class="flex items-end justify-center gap-1 flex-1 h-full">
                <div class="w-1.5 sm:w-2 rounded-full bg-[#e5e7eb] h-[55%] hover:bg-gray-400 transition"></div>
                <div class="w-1.5 sm:w-2 rounded-full bg-[#e5e7eb] h-[92%] hover:bg-gray-400 transition"></div>
                <div class="w-1.5 sm:w-2 rounded-full bg-[#e5e7eb] h-[40%] hover:bg-gray-400 transition"></div>
              </div>

              <!-- Mar Cluster -->
              <div class="flex items-end justify-center gap-1 flex-1 h-full">
                <div class="w-1.5 sm:w-2 rounded-full bg-[#e5e7eb] h-[68%] hover:bg-gray-400 transition"></div>
                <div class="w-1.5 sm:w-2 rounded-full bg-[#e5e7eb] h-[50%] hover:bg-gray-400 transition"></div>
                <div class="w-1.5 sm:w-2 rounded-full bg-[#e5e7eb] h-[82%] hover:bg-gray-400 transition"></div>
              </div>

              <!-- Apr Cluster -->
              <div class="flex items-end justify-center gap-1 flex-1 h-full">
                <div class="w-1.5 sm:w-2 rounded-full bg-[#e5e7eb] h-[60%] hover:bg-gray-400 transition"></div>
                <div class="w-1.5 sm:w-2 rounded-full bg-[#e5e7eb] h-[48%] hover:bg-gray-400 transition"></div>
                <div class="w-1.5 sm:w-2 rounded-full bg-[#e5e7eb] h-[70%] hover:bg-gray-400 transition"></div>
              </div>

              <!-- May Cluster: Highlighted Black Bars with Floating Tooltip -->
              <div class="flex items-end justify-center gap-1 flex-1 h-full relative">
                
                <!-- Tooltip over May -->
                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 z-20 bg-white px-3 py-1.5 rounded-xl shadow-lg border border-gray-200/80 flex flex-col items-center text-center pointer-events-none whitespace-nowrap">
                  <span class="text-[10px] text-gray-400 font-medium">May . 2026</span>
                  <span class="text-xs font-extrabold text-gray-900">₦0.00</span>
                </div>

                <!-- Black Bar 1 -->
                <div class="w-1.5 sm:w-2 rounded-full bg-black h-[70%] shadow-sm"></div>
                <!-- Black Bar 2: Segmented/Dashed tall bar -->
                <div class="w-1.5 sm:w-2 rounded-full dashed-bar h-[82%] shadow-sm"></div>
                <!-- Black Bar 3: Solid tall black bar -->
                <div class="w-1.5 sm:w-2 rounded-full bg-black h-[96%] shadow-sm"></div>
              </div>

              <!-- Jun Cluster -->
              <div class="flex items-end justify-center gap-1 flex-1 h-full">
                <div class="w-1.5 sm:w-2 rounded-full bg-[#e5e7eb] h-[65%] hover:bg-gray-400 transition"></div>
                <div class="w-1.5 sm:w-2 rounded-full bg-[#e5e7eb] h-[78%] hover:bg-gray-400 transition"></div>
                <div class="w-1.5 sm:w-2 rounded-full bg-[#e5e7eb] h-[55%] hover:bg-gray-400 transition"></div>
              </div>

              <!-- Jul Cluster -->
              <div class="flex items-end justify-center gap-1 flex-1 h-full">
                <div class="w-1.5 sm:w-2 rounded-full bg-[#e5e7eb] h-[85%] hover:bg-gray-400 transition"></div>
                <div class="w-1.5 sm:w-2 rounded-full bg-[#e5e7eb] h-[65%] hover:bg-gray-400 transition"></div>
                <div class="w-1.5 sm:w-2 rounded-full bg-[#e5e7eb] h-[50%] hover:bg-gray-400 transition"></div>
              </div>

              <!-- Aug Cluster -->
              <div class="flex items-end justify-center gap-1 flex-1 h-full">
                <div class="w-1.5 sm:w-2 rounded-full bg-[#e5e7eb] h-[52%] hover:bg-gray-400 transition"></div>
                <div class="w-1.5 sm:w-2 rounded-full bg-[#e5e7eb] h-[70%] hover:bg-gray-400 transition"></div>
                <div class="w-1.5 sm:w-2 rounded-full bg-[#e5e7eb] h-[40%] hover:bg-gray-400 transition"></div>
              </div>

            </div>

            <!-- X-Axis Months Labels -->
            <div class="ml-9 flex items-center justify-between text-xs font-semibold text-gray-400 pt-2 px-1">
              <span class="flex-1 text-center">Jan</span>
              <span class="flex-1 text-center">Feb</span>
              <span class="flex-1 text-center">Mar</span>
              <span class="flex-1 text-center">Apr</span>
              <span class="flex-1 text-center text-gray-900 font-bold">May</span>
              <span class="flex-1 text-center">Jun</span>
              <span class="flex-1 text-center">Jul</span>
              <span class="flex-1 text-center">Aug</span>
            </div>

          </div>

        </div>

      </div>

    </main>

  <!-- Interactive JavaScript Logic Specific to Dashboard Cards -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Toggle Eye / Obscure Balances
      const balances = [
        { btnId: 'toggleBalanceBtn', valId: 'totalBalanceVal', actual: '₦0.00', hidden: '••••••••' },
        { btnId: 'togglePaymentBtn', valId: 'paymentOverviewVal', actual: '₦0.00', hidden: '••••••••' },
        { btnId: 'toggleEarningBtn', valId: 'earningOverviewVal', actual: '₦0.00', hidden: '••••••••' }
      ];
      document.querySelectorAll('#paymentChart, #earningChart, .h-20.w-full.relative.mt-2').forEach(chart => chart.style.visibility = 'hidden');

      balances.forEach(item => {
        const btn = document.getElementById(item.btnId);
        const val = document.getElementById(item.valId);
        let isVisible = true;

        if (btn && val) {
          btn.addEventListener('click', () => {
            isVisible = !isVisible;
            val.textContent = isVisible ? item.actual : item.hidden;
            btn.classList.toggle('text-gray-400', isVisible);
            btn.classList.toggle('text-black', !isVisible);
          });
        }
      });

      // Currency & Country Dropdown Data
      const currencyData = [
        {
          code: 'USD',
          country: 'United States',
          name: 'US Dollar',
          symbol: '$',
          balance: '₦0.00',
          flagSvg: `<svg viewBox="0 0 640 480" class="w-full h-full object-cover">
            <path fill="#bd3d44" d="M0 0h640v480H0z"/>
            <path stroke="#fff" stroke-width="37" d="M0 55h640M0 129h640M0 203h640M0 277h640M0 351h640M0 425h640"/>
            <path fill="#192f5d" d="M0 0h256v258H0z"/>
            <circle cx="128" cy="129" r="18" fill="#fff"/>
          </svg>`
        },
        {
          code: 'CAD',
          country: 'Canada',
          name: 'Canadian Dollar',
          symbol: 'CA$',
          balance: '₦0.00',
          flagSvg: `<svg viewBox="0 0 640 480" class="w-full h-full object-cover">
            <path fill="#d80027" d="M0 0h160v480H0zm480 0h160v480H480z"/>
            <path fill="#f0f0f0" d="M160 0h320v480H160z"/>
            <path fill="#d80027" d="M320 120l18 48 45-20-14 42 45 14-38 25 15 48-43-18-28 35-28-35-43 18 15-48-38-25 45-14-14-42 45 20z"/>
          </svg>`
        },
        {
          code: 'EUR',
          country: 'European Union',
          name: 'Euro',
          symbol: '€',
          balance: '₦0.00',
          flagSvg: `<svg viewBox="0 0 640 480" class="w-full h-full object-cover">
            <path fill="#003399" d="M0 0h640v480H0z"/>
            <g fill="#ffcc00" transform="translate(320,240) scale(20)">
              <circle cx="0" cy="-6" r="0.8"/>
              <circle cx="3" cy="-5.2" r="0.8"/>
              <circle cx="5.2" cy="-3" r="0.8"/>
              <circle cx="6" cy="0" r="0.8"/>
              <circle cx="5.2" cy="3" r="0.8"/>
              <circle cx="3" cy="5.2" r="0.8"/>
              <circle cx="0" cy="6" r="0.8"/>
              <circle cx="-3" cy="5.2" r="0.8"/>
              <circle cx="-5.2" cy="3" r="0.8"/>
              <circle cx="-6" cy="0" r="0.8"/>
              <circle cx="-5.2" cy="-3" r="0.8"/>
              <circle cx="-3" cy="-5.2" r="0.8"/>
            </g>
          </svg>`
        },
        {
          code: 'NGN',
          country: 'Nigeria',
          name: 'Nigerian Naira',
          symbol: '₦',
          balance: '₦0.00',
          flagSvg: `<svg viewBox="0 0 640 480" class="w-full h-full object-cover">
            <path fill="#008751" d="M0 0h213.3v480H0zm426.7 0H640v480H426.7z"/>
            <path fill="#ffffff" d="M213.3 0h213.4v480H213.3z"/>
          </svg>`
        },
        {
          code: 'ZAR',
          country: 'South Africa',
          name: 'South African Rand',
          symbol: 'R',
          balance: '₦0.00',
          flagSvg: `<svg viewBox="0 0 640 480" class="w-full h-full object-cover">
            <path fill="#002395" d="M0 240h640v240H0z"/>
            <path fill="#de3831" d="M0 0h640v240H0z"/>
            <path fill="#ffffff" stroke="#ffffff" stroke-width="40" d="M0 0l320 240L0 480"/>
            <path fill="#007a3d" stroke="#007a3d" stroke-width="60" d="M0 240h640M0 0l320 240L0 480"/>
            <path fill="#ffb612" stroke="#ffb612" stroke-width="25" d="M0 60l240 180L0 420"/>
            <path fill="#000000" d="M0 80l213 160L0 400z"/>
          </svg>`
        },
        {
          code: 'GBP',
          country: 'United Kingdom',
          name: 'British Pound',
          symbol: '£',
          balance: '₦0.00',
          flagSvg: `<svg viewBox="0 0 640 480" class="w-full h-full object-cover">
            <path fill="#012169" d="M0 0h640v480H0z"/>
            <path fill="#fff" d="M0 0l640 480M640 0L0 480" stroke="#fff" stroke-width="60"/>
            <path fill="#c8102e" d="M0 0l640 480M640 0L0 480" stroke="#c8102e" stroke-width="25"/>
            <path fill="#fff" d="M270 0h100v480H270zM0 190h640v100H0z"/>
            <path fill="#c8102e" d="M290 0h60v480H290zM0 210h640v60H0z"/>
          </svg>`
        },
        {
          code: 'KES',
          country: 'Kenya',
          name: 'Kenyan Shilling',
          symbol: 'KSh',
          balance: '₦0.00',
          flagSvg: `<svg viewBox="0 0 640 480" class="w-full h-full object-cover">
            <path fill="#000000" d="M0 0h640v140H0z"/>
            <path fill="#bb0000" d="M0 170h640v140H0z"/>
            <path fill="#006600" d="M0 340h640v140H0z"/>
            <path fill="#ffffff" d="M0 140h640v30H0zm0 170h640v30H0z"/>
            <ellipse cx="320" cy="240" rx="45" ry="90" fill="#bb0000" stroke="#fff" stroke-width="5"/>
          </svg>`
        }
      ];

      // Keep the built-in flag catalog while the API controls which currencies are enabled.
      const currencyFlagCatalog = Object.fromEntries(currencyData.map(item => [item.code, item.flagSvg || '']));
      currencyData.splice(0, currencyData.length, currencyData.find(item => item.code === 'NGN'));
      let currentCurrency = currencyData[0];
      const currencyBtn = document.getElementById('currencyBtn');
      const currencyDropdownMenu = document.getElementById('currencyDropdownMenu');
      const currencyDropdownWrapper = document.getElementById('currencyDropdownWrapper');
      const currencyFlagContainer = document.getElementById('currencyFlagContainer');
      const currencyText = document.getElementById('currencyText');
      const currencyChevron = document.getElementById('currencyChevron');
      const currencyOptionsList = document.getElementById('currencyOptionsList');
      const totalBalanceVal = document.getElementById('totalBalanceVal');

      // Populate Currency Options in Dropdown
      function renderCurrencyOptions() {
        if (!currencyOptionsList) return;
        currencyOptionsList.innerHTML = currencyData.map(item => `
          <button class="currency-opt w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs hover:bg-gray-50 transition group ${item.code === currentCurrency.code ? 'bg-gray-50 font-bold text-gray-900' : 'text-gray-600 font-medium'}" data-code="${item.code}">
            <div class="flex items-center gap-2.5">
              <span class="w-4 h-4 rounded-full overflow-hidden inline-flex items-center justify-center flex-shrink-0 border border-gray-200 shadow-2xs">
                ${item.flagSvg}
              </span>
              <div class="flex flex-col text-left">
                <span class="text-xs text-gray-900 group-hover:text-black">${item.code} <span class="text-gray-400 font-normal">(${item.symbol})</span></span>
                <span class="text-[10px] text-gray-400 font-normal leading-tight">${item.country}</span>
              </div>
            </div>
            ${item.code === currentCurrency.code ? `
              <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
              </svg>` : ''}
          </button>
        `).join('');

        // Add click listeners to each option
        document.querySelectorAll('.currency-opt').forEach(opt => {
          opt.addEventListener('click', () => {
            const code = opt.getAttribute('data-code');
            selectCurrency(code);
          });
        });
      }

      function selectCurrency(code) {
        const selected = currencyData.find(c => c.code === code);
        if (!selected) return;
        currentCurrency = selected;
        
        // Update Button UI (Flag + Text)
        currencyFlagContainer.innerHTML = selected.flagSvg;
        currencyText.textContent = selected.code;

        // Update Balance Amount
        totalBalanceVal.textContent = selected.balance;
        balances[0].actual = selected.balance; // sync with privacy eye toggle

        // Close Dropdown & Re-render options for active checkmark
        closeCurrencyDropdown();
        renderCurrencyOptions();
        if (typeof loadDashboardSummary === 'function') loadDashboardSummary(code);
      }

      function openCurrencyDropdown() {
        closePeriodDropdown();
        currencyDropdownMenu.classList.remove('hidden');
        currencyBtn.setAttribute('aria-expanded', 'true');
        if (currencyChevron) currencyChevron.classList.add('rotate-180');
      }

      function closeCurrencyDropdown() {
        currencyDropdownMenu.classList.add('hidden');
        currencyBtn.setAttribute('aria-expanded', 'false');
        if (currencyChevron) currencyChevron.classList.remove('rotate-180');
      }

      if (currencyBtn && currencyDropdownMenu) {
        renderCurrencyOptions();
        currencyBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          const isHidden = currencyDropdownMenu.classList.contains('hidden');
          if (isHidden) openCurrencyDropdown();
          else closeCurrencyDropdown();
        });
      }

      // Time Period Dropdown Data
      const periodData = [
        { id: 'all_time', label: 'All Time', trend: '+10.5% Balance increase , good progress.' },
        { id: 'this_year', label: 'This Year (2026)', trend: '+18.4% Year-to-date growth.' },
        { id: 'this_month', label: 'This Month (June)', trend: '+7.2% Volume surge.' },
        { id: 'last_month', label: 'Last Month (May)', trend: '+14.9% Revenue peak.' },
        { id: 'last_30', label: 'Last 30 Days', trend: '+9.1% Consistent inflows.' },
        { id: 'last_90', label: 'Last 90 Days', trend: '+22.6% Quarterly expansion.' }
      ];

      let currentPeriod = periodData[0];
      const periodBtn = document.getElementById('periodBtn');
      const periodDropdownMenu = document.getElementById('periodDropdownMenu');
      const periodDropdownWrapper = document.getElementById('periodDropdownWrapper');
      const periodText = document.getElementById('periodText');
      const periodChevron = document.getElementById('periodChevron');
      const periodOptionsList = document.getElementById('periodOptionsList');
      const balanceTrendBadge = document.getElementById('balanceTrendBadge');

      function renderPeriodOptions() {
        if (!periodOptionsList) return;
        periodOptionsList.innerHTML = periodData.map(item => `
          <button class="period-opt w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs hover:bg-gray-50 transition group ${item.id === currentPeriod.id ? 'bg-gray-50 font-bold text-gray-900' : 'text-gray-600 font-medium'}" data-id="${item.id}">
            <span class="text-xs text-gray-900 group-hover:text-black">${item.label}</span>
            ${item.id === currentPeriod.id ? `
              <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
              </svg>` : ''}
          </button>
        `).join('');

        document.querySelectorAll('.period-opt').forEach(opt => {
          opt.addEventListener('click', () => {
            const id = opt.getAttribute('data-id');
            selectPeriod(id);
          });
        });
      }

      function selectPeriod(id) {
        const selected = periodData.find(p => p.id === id);
        if (!selected) return;
        currentPeriod = selected;

        periodText.textContent = selected.label;
        if (balanceTrendBadge) {
          balanceTrendBadge.textContent = selected.trend;
        }

        closePeriodDropdown();
        renderPeriodOptions();
      }

      function openPeriodDropdown() {
        closeCurrencyDropdown();
        periodDropdownMenu.classList.remove('hidden');
        periodBtn.setAttribute('aria-expanded', 'true');
        if (periodChevron) periodChevron.classList.add('rotate-180');
      }

      function closePeriodDropdown() {
        periodDropdownMenu.classList.add('hidden');
        periodBtn.setAttribute('aria-expanded', 'false');
        if (periodChevron) periodChevron.classList.remove('rotate-180');
      }

      if (periodBtn && periodDropdownMenu) {
        renderPeriodOptions();
        periodBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          const isHidden = periodDropdownMenu.classList.contains('hidden');
          if (isHidden) openPeriodDropdown();
          else closePeriodDropdown();
        });
      }

      // Replace the presentation defaults with the authenticated NGN wallet summary.
      const money = (value, code = 'NGN') => new Intl.NumberFormat('en-NG', { style: 'currency', currency: code, maximumFractionDigits: 2 }).format(Number(value || 0));
      const loadDashboardSummary = async (code = 'NGN') => {
        try {
          const token = localStorage.getItem('flow_token') || '';
          const response = await fetch(`../api/dashboard/summary.php?currency=${encodeURIComponent(code)}`, { headers: { Authorization: `Bearer ${token}` } });
          const result = await response.json();
          if (!response.ok || !result.success) throw new Error(result.error || 'Unable to load balance.');
          const summary = result.summary;
          const activeCurrency = result.currency.code;
          const values = {
            totalBalanceVal: money(summary.total_balance, activeCurrency),
            monthlySpendingVal: money(summary.monthly_spending, activeCurrency),
            cardUsageVal: money(summary.card_usage, activeCurrency),
            paymentOverviewVal: money(summary.payment_overview, activeCurrency),
            earningOverviewVal: money(summary.earning_overview, activeCurrency)
          };
          Object.entries(values).forEach(([id, value]) => { const node = document.getElementById(id); if (node) node.textContent = value; });
          const availableNode = document.getElementById('availableCurrencyVal');
          if (availableNode) availableNode.textContent = `Available ${activeCurrency} balance · ${money(summary.total_balance, activeCurrency)}`;
          balances.forEach(item => { if (values[item.valId]) item.actual = values[item.valId]; });
          if (balanceTrendBadge) {
            const change = Number(summary.spending_change_percent || 0);
            balanceTrendBadge.textContent = `${change >= 0 ? '+' : ''}${change}% spending change this month.`;
          }
          const monthlyTrend = document.getElementById('monthlySpendingTrend');
          const paymentTrend = document.getElementById('paymentTrend');
          const earningTrend = document.getElementById('earningTrend');
          if (monthlyTrend) monthlyTrend.textContent = Number(summary.monthly_spending) ? 'Current month' : 'No spending data yet';
          if (paymentTrend) paymentTrend.textContent = Number(summary.payment_overview) ? 'Current month' : 'No payment data yet';
          if (earningTrend) earningTrend.textContent = Number(summary.earning_overview) ? 'Current month' : 'No earnings data yet';
          const metricValues = { processingCostVal: summary.processing_cost, pipelineOutputVal: summary.pipeline_output, netEfficiencyVal: summary.net_efficiency };
          Object.entries(metricValues).forEach(([id, value]) => { const node = document.getElementById(id); if (node) node.textContent = money(value, activeCurrency); });
          const renderChart = (id, key, emptyLabel) => {
            const chart = document.getElementById(id); if (!chart) return;
            const points = (summary.months || []).filter(point => Number(point[key]) > 0);
            chart.replaceChildren();
            if (!points.length) {
              const empty = document.createElement('p'); empty.className = 'py-12 text-center text-xs text-gray-400'; empty.textContent = emptyLabel;
              chart.append(empty); return;
            }
            const max = Math.max(...points.map(point => Number(point[key])), 1);
            const bars = document.createElement('div'); bars.className = 'flex h-40 items-end justify-center gap-2 px-4';
            points.forEach(point => {
              const column = document.createElement('div'); column.className = 'flex h-full flex-1 flex-col items-center justify-end gap-2';
              const bar = document.createElement('div'); bar.className = 'w-full max-w-8 rounded-t-lg bg-[#0b2545]'; bar.style.height = `${Math.max(5, Number(point[key]) / max * 100)}%`; bar.title = `${point.month}: ${money(point[key], activeCurrency)}`;
              const label = document.createElement('span'); label.className = 'text-[10px] text-gray-400'; label.textContent = point.month.slice(5);
              column.append(bar, label); bars.append(column);
            });
            chart.append(bars);
          };
          renderChart('paymentChart', 'spending', 'No payment data yet');
          renderChart('earningChart', 'earnings', 'No earnings data yet');
          document.querySelectorAll('#paymentChart, #earningChart').forEach(chart => chart.style.visibility = 'visible');
          const miniCharts = document.querySelectorAll('.h-20.w-full.relative.mt-2');
          miniCharts.forEach((chart, index) => { chart.classList.toggle('hidden', Number(index === 0 ? summary.monthly_spending : summary.card_usage) === 0); chart.style.visibility = 'visible'; });
          currencyText.textContent = activeCurrency;
          currencyData.splice(0, currencyData.length, ...(result.available_currencies || []).map(item => ({ ...item, country: item.code === 'NGN' ? 'Nigeria' : item.name, symbol: item.code === 'NGN' ? '₦' : item.code, balance: money(summary.total_balance, item.code), flagSvg: currencyFlagCatalog[item.code] || '' })));
          if (!currencyData.length) currencyData.push({ code: activeCurrency, country: result.currency.name, name: result.currency.name, symbol: activeCurrency, balance: values.totalBalanceVal, flagSvg: '' });
          currentCurrency = currencyData.find(item => item.code === activeCurrency) || currencyData[0];
          renderCurrencyOptions();
        } catch (error) {
          if (balanceTrendBadge) balanceTrendBadge.textContent = 'Balance data is temporarily unavailable.';
          ['paymentChart','earningChart'].forEach(id => { const chart = document.getElementById(id); if (chart) { chart.replaceChildren(Object.assign(document.createElement('p'), { className: 'py-12 text-center text-xs text-gray-400', textContent: 'No data available' })); } });
          document.querySelectorAll('.h-20.w-full.relative.mt-2').forEach(chart => chart.classList.add('hidden'));
        }
      };
      loadDashboardSummary('NGN');
      window.addEventListener('flowwithlit:dashboard-refresh', () => loadDashboardSummary(currentCurrency?.code || 'NGN'));
      document.addEventListener('visibilitychange', () => { if (!document.hidden) loadDashboardSummary(currentCurrency?.code || 'NGN'); });
      window.setInterval(() => { if (!document.hidden) loadDashboardSummary(currentCurrency?.code || 'NGN'); }, 30000);

      // Global click listener to close Currency & Period dropdowns
      document.addEventListener('click', (e) => {
        if (currencyDropdownWrapper && !currencyDropdownWrapper.contains(e.target)) {
          closeCurrencyDropdown();
        }
        if (periodDropdownWrapper && !periodDropdownWrapper.contains(e.target)) {
          closePeriodDropdown();
        }
      });
    });
  </script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
