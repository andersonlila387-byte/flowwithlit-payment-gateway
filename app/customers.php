<?php
/**
 * Flowwithlit Customers Directory (Paystack Style)
 * Complete roster of customers who pay via Checkout, Invoices, Subscriptions & Payment Links
 */
$pageTitle = 'Customers — Flowwithlit';
$activePage = 'customers';
$activeNavTab = 'Features';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<style>#customerTableBody { visibility:hidden; }</style>

    <!-- Scrollable Main Content Area -->
    <main class="flex-1 h-full overflow-y-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-6 flex flex-col gap-6">
      
      <!-- Top Title & Quick Action Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 flex-shrink-0">
        <div>
          <div class="flex items-center gap-2">
            <span class="text-xs text-gray-400 font-medium tracking-wide">Customer Directory</span>
            <span class="text-[11px] font-bold text-sky-700 bg-sky-50 border border-sky-200/50 px-2 py-0.5 rounded-full">1,248 Customers</span>
          </div>
          <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight mt-0.5">Customers</h1>
        </div>

        <!-- Action buttons -->
        <div class="flex items-center gap-2.5 flex-wrap">
          <!-- Export Customers CSV -->
          <button class="px-4 py-2.5 rounded-full bg-white hover:bg-gray-50 border border-gray-200/80 text-gray-700 text-xs font-semibold flex items-center gap-2 shadow-xs transition hover:scale-105 active:scale-95" onclick="alert('Exporting full customer list CSV...')">
            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            <span>Export CSV</span>
          </button>

          <!-- Primary: Add New Customer Modal (Brand Navy) -->
          <button id="openNewCustomerModalBtn" class="bg-[#0b2545] hover:bg-[#132847] text-white px-5 py-2.5 rounded-full text-xs font-semibold flex items-center gap-2 shadow-sm transition hover:scale-105 active:scale-95">
            <span class="w-4 h-4 rounded-full border border-white/40 flex items-center justify-center text-[11px] leading-none font-bold">+</span>
            <span>New Customer</span>
          </button>
        </div>
      </div>

      <!-- Row 1: Paystack Style KPI Metrics (4 Cards) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 flex-shrink-0">
        
        <!-- Stat 1: Total Customer Base -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex flex-col justify-between">
          <div class="flex items-center justify-between text-xs text-gray-500 font-medium">
            <span>Total Customers</span>
            <div class="w-8 h-8 rounded-xl bg-sky-50 text-[#0b2545] flex items-center justify-center font-bold">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-3">
            <div id="customersTotalMetric" class="text-2xl font-black text-gray-900 tracking-tight">0</div>
            <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1">
              <span>All-time payers</span>
              <span id="customersNewMetric" class="font-bold text-gray-400">No new data</span>
            </div>
          </div>
        </div>

        <!-- Stat 2: Active Subscriptions / Reusable Cards -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex flex-col justify-between">
          <div class="flex items-center justify-between text-xs text-gray-500 font-medium">
            <span>Saved Card Authorizations</span>
            <div class="w-8 h-8 rounded-xl bg-sky-50 text-[#0b2545] flex items-center justify-center font-bold">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <rect x="3" y="5" width="18" height="14" rx="3" stroke-linecap="round" stroke-linejoin="round"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h2m3 0h2" />
              </svg>
            </div>
          </div>
          <div class="mt-3">
            <div id="customersCardsMetric" class="text-2xl font-black text-gray-900 tracking-tight">0 Cards</div>
            <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1">
              <span>Reusable tokens</span>
              <span class="font-bold text-gray-400">—</span>
            </div>
          </div>
        </div>

        <!-- Stat 3: Total Customer Lifetime Value (LTV) -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex flex-col justify-between">
          <div class="flex items-center justify-between text-xs text-gray-500 font-medium">
            <span>Customer Lifetime Value</span>
            <div class="w-8 h-8 rounded-xl bg-sky-50 text-[#0b2545] flex items-center justify-center font-bold">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-3">
            <div id="customersValueMetric" class="text-2xl font-black text-gray-900 tracking-tight">₦0.00</div>
            <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1">
              <span>Total revenue collected</span>
              <span id="customersAverageMetric" class="font-bold text-gray-400">—</span>
            </div>
          </div>
        </div>

        <!-- Stat 4: Repeat Customer Rate -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex flex-col justify-between">
          <div class="flex items-center justify-between text-xs text-gray-500 font-medium">
            <span>Repeat Payment Rate</span>
            <div class="w-8 h-8 rounded-xl bg-sky-50 text-[#0b2545] flex items-center justify-center font-bold">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
              </svg>
            </div>
          </div>
          <div class="mt-3">
            <div id="customersRepeatMetric" class="text-2xl font-black text-gray-900 tracking-tight">—</div>
            <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1">
              <span>More than 2 payments</span>
              <span class="font-bold text-gray-400">—</span>
            </div>
          </div>
        </div>

      </div>

      <!-- Main Section: Paystack-Style Customer Ledger -->
      <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs flex flex-col">
        
        <!-- Controls Bar: Segmented Pills, Search, Filter -->
        <div class="p-5 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
          
          <!-- Filter Tabs / Pills -->
          <div class="flex items-center gap-1.5 overflow-x-auto pb-1 lg:pb-0 scrollbar-none text-xs font-semibold">
            <button class="filter-tab active px-3.5 py-1.5 rounded-full bg-[#0b2545] text-white transition shadow-2xs" data-filter="all">
              All Customers <span class="ml-1 opacity-70 text-[11px]">0</span>
            </button>
            <button class="filter-tab px-3.5 py-1.5 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 transition" data-filter="active">
              Active Payers <span class="ml-1 opacity-70 text-[11px]">0</span>
            </button>
            <button class="filter-tab px-3.5 py-1.5 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 transition" data-filter="card">
              Has Saved Card <span class="ml-1 opacity-70 text-[11px]">0</span>
            </button>
            <button class="filter-tab px-3.5 py-1.5 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 transition" data-filter="dormant">
              Dormant <span class="ml-1 opacity-70 text-[11px]">0</span>
            </button>
          </div>

          <!-- Search & Filter Controls -->
          <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap">
            <!-- Search Input -->
            <div class="relative flex-1 sm:w-64">
              <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
              </span>
              <input type="text" id="customerSearchInput" placeholder="Search customer name, email, CUS code..." class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-full text-xs text-gray-800 focus:outline-hidden focus:bg-white focus:border-[#0b2545] transition" />
            </div>

            <!-- Currency filter -->
            <select id="customerCurrencyFilter" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-full text-xs text-gray-700 font-medium focus:outline-hidden focus:border-[#0b2545] transition">
              <option value="all">All Currencies</option>
              <option value="NGN">NGN (₦)</option>
            </select>
          </div>
        </div>

        <!-- 100% Full Width Responsive Table Container -->
        <div class="w-full overflow-x-auto">
          <table class="w-full text-left border-collapse min-w-[900px]">
            <thead>
              <tr class="border-b border-gray-100 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                <th class="py-3 px-4">Customer Name &amp; Code</th>
                <th class="py-3 px-4">Email Address</th>
                <th class="py-3 px-4">Phone Number</th>
                <th class="py-3 px-4 text-center">Saved Card</th>
                <th class="py-3 px-4 text-right">Successful Payments</th>
                <th class="py-3 px-4 text-right">Total Spent (LTV)</th>
                <th class="py-3 px-4">Joined Date</th>
                <th class="py-3 px-4 text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100/80 text-xs" id="customerTableBody">
              
              <!-- Row 1: Acme Cloud Solutions Inc -->
              <tr class="hover:bg-gray-50/70 transition group" data-filter="active card" data-currency="USD">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-[#0b2545] text-white flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-2xs">
                      AC
                    </div>
                    <div>
                      <div class="font-bold text-gray-900">Acme Cloud Solutions Inc</div>
                      <div class="text-[10px] text-gray-400 font-mono">CUS_9402819</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <span class="text-gray-900 font-medium">billing@acmecloud.com</span>
                </td>
                <td class="py-4 px-4 whitespace-nowrap text-gray-600 font-mono">
                  +1 (415) 892-0194
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <div class="relative inline-flex items-center justify-center group/cardtip">
                    <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/80 flex items-center justify-center shadow-2xs transition group-hover/cardtip:scale-110" aria-label="Visa Card Saved">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <rect x="3" y="5" width="18" height="14" rx="3" stroke-linecap="round" stroke-linejoin="round"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h2m3 0h2" />
                      </svg>
                    </span>
                    <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/cardtip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      Visa ending in *4242 (Tokenized)
                      <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <span class="font-bold text-gray-900">14 Payments</span>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-bold text-gray-900">$348,200.00</div>
                  <div class="text-[10px] text-gray-400">USD</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap text-gray-500">
                  12 Jan 2026
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <button class="px-3 py-1.5 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-600 hover:text-white text-xs font-semibold transition shadow-2xs" onclick="viewCustomerDetails('Acme Cloud Solutions Inc', 'CUS_9402819', 'billing@acmecloud.com', '+1 (415) 892-0194', '14', '$348,200.00 USD', 'Visa *4242', '12 Jan 2026')">
                    View
                  </button>
                </td>
              </tr>

              <!-- Row 2: Sophia Thorne -->
              <tr class="hover:bg-gray-50/70 transition group" data-filter="active card" data-currency="GBP">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-sky-100 text-sky-800 flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-2xs">
                      ST
                    </div>
                    <div>
                      <div class="font-bold text-gray-900">Sophia Thorne</div>
                      <div class="text-[10px] text-gray-400 font-mono">CUS_3819204</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <span class="text-gray-900 font-medium">sophia.thorne@mayfairadvisory.co.uk</span>
                </td>
                <td class="py-4 px-4 whitespace-nowrap text-gray-600 font-mono">
                  +44 20 7946 0912
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <div class="relative inline-flex items-center justify-center group/cardtip">
                    <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/80 flex items-center justify-center shadow-2xs transition group-hover/cardtip:scale-110" aria-label="Mastercard Saved">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <rect x="3" y="5" width="18" height="14" rx="3" stroke-linecap="round" stroke-linejoin="round"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h2m3 0h2" />
                      </svg>
                    </span>
                    <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/cardtip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      Mastercard ending in *8821 (Tokenized)
                      <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <span class="font-bold text-gray-900">6 Payments</span>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-bold text-gray-900">£85,200.00</div>
                  <div class="text-[10px] text-gray-400">GBP</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap text-gray-500">
                  04 Feb 2026
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <button class="px-3 py-1.5 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-600 hover:text-white text-xs font-semibold transition shadow-2xs" onclick="viewCustomerDetails('Sophia Thorne', 'CUS_3819204', 'sophia.thorne@mayfairadvisory.co.uk', '+44 20 7946 0912', '6', '£85,200.00 GBP', 'Mastercard *8821', '04 Feb 2026')">
                    View
                  </button>
                </td>
              </tr>

              <!-- Row 3: Marcus Vance -->
              <tr class="hover:bg-gray-50/70 transition group" data-filter="active" data-currency="EUR">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-2xs">
                      MV
                    </div>
                    <div>
                      <div class="font-bold text-gray-900">Marcus Vance</div>
                      <div class="text-[10px] text-gray-400 font-mono">CUS_5920194</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <span class="text-gray-900 font-medium">marcus.vance@helsinkiventures.fi</span>
                </td>
                <td class="py-4 px-4 whitespace-nowrap text-gray-600 font-mono">
                  +358 40 123 4567
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <div class="relative inline-flex items-center justify-center group/cardtip">
                    <span class="w-8 h-8 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center shadow-2xs" aria-label="No Saved Card">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                      </svg>
                    </span>
                    <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/cardtip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      No Reusable Card (Direct SEPA Payer)
                      <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <span class="font-bold text-gray-900">3 Payments</span>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-bold text-gray-900">€26,700.00</div>
                  <div class="text-[10px] text-gray-400">EUR</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap text-gray-500">
                  18 Mar 2026
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <button class="px-3 py-1.5 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-600 hover:text-white text-xs font-semibold transition shadow-2xs" onclick="viewCustomerDetails('Marcus Vance', 'CUS_5920194', 'marcus.vance@helsinkiventures.fi', '+358 40 123 4567', '3', '€26,700.00 EUR', 'None (Bank Direct)', '18 Mar 2026')">
                    View
                  </button>
                </td>
              </tr>

              <!-- Row 4: Olumide Adeyemi -->
              <tr class="hover:bg-gray-50/70 transition group" data-filter="active card" data-currency="NGN">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-2xs">
                      OA
                    </div>
                    <div>
                      <div class="font-bold text-gray-900">Olumide Adeyemi</div>
                      <div class="text-[10px] text-gray-400 font-mono">CUS_1092834</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <span class="text-gray-900 font-medium">olumide@lagostechnology.ng</span>
                </td>
                <td class="py-4 px-4 whitespace-nowrap text-gray-600 font-mono">
                  +234 803 456 7890
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <div class="relative inline-flex items-center justify-center group/cardtip">
                    <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/80 flex items-center justify-center shadow-2xs transition group-hover/cardtip:scale-110" aria-label="Mastercard Saved">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <rect x="3" y="5" width="18" height="14" rx="3" stroke-linecap="round" stroke-linejoin="round"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h2m3 0h2" />
                      </svg>
                    </span>
                    <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/cardtip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      Mastercard ending in *1902 (Tokenized)
                      <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <span class="font-bold text-gray-900">22 Payments</span>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-bold text-gray-900">₦18,450,000.00</div>
                  <div class="text-[10px] text-gray-400">NGN</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap text-gray-500">
                  09 Jan 2026
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <button class="px-3 py-1.5 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-600 hover:text-white text-xs font-semibold transition shadow-2xs" onclick="viewCustomerDetails('Olumide Adeyemi', 'CUS_1092834', 'olumide@lagostechnology.ng', '+234 803 456 7890', '22', '₦18,450,000.00 NGN', 'Mastercard *1902', '09 Jan 2026')">
                    View
                  </button>
                </td>
              </tr>

              <!-- Row 5: Liam Chen -->
              <tr class="hover:bg-gray-50/70 transition group" data-filter="dormant" data-currency="CAD">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-purple-100 text-purple-800 flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-2xs">
                      LC
                    </div>
                    <div>
                      <div class="font-bold text-gray-900">Liam Chen</div>
                      <div class="text-[10px] text-gray-400 font-mono">CUS_8401928</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <span class="text-gray-900 font-medium">liam.chen@vancouverdesign.ca</span>
                </td>
                <td class="py-4 px-4 whitespace-nowrap text-gray-600 font-mono">
                  +1 (604) 555-0182
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <div class="relative inline-flex items-center justify-center group/cardtip">
                    <span class="w-8 h-8 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center shadow-2xs" aria-label="No Saved Card">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                      </svg>
                    </span>
                    <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/cardtip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      No Card on File
                      <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <span class="font-bold text-gray-900">1 Payment</span>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-bold text-gray-900">CA$4,200.00</div>
                  <div class="text-[10px] text-gray-400">CAD</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap text-gray-500">
                  22 Jan 2026
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <button class="px-3 py-1.5 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-600 hover:text-white text-xs font-semibold transition shadow-2xs" onclick="viewCustomerDetails('Liam Chen', 'CUS_8401928', 'liam.chen@vancouverdesign.ca', '+1 (604) 555-0182', '1', 'CA$4,200.00 CAD', 'None', '22 Jan 2026')">
                    View
                  </button>
                </td>
              </tr>

            </tbody>
          </table>
        </div>

        <!-- Table Footer / Pagination -->
        <div class="p-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
          <div>
            Showing <span class="font-semibold text-gray-800" id="visibleCustomerCount">0</span> of <span class="font-semibold text-gray-800" id="totalCustomerCount">0</span> customers
          </div>
          <div class="flex items-center gap-1.5">
            <button class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition text-xs font-semibold disabled:opacity-50" disabled>Previous</button>
            <button class="px-3 py-1.5 rounded-lg bg-[#0b2545] text-white text-xs font-semibold">1</button>
            <button class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition text-xs font-semibold">2</button>
            <button class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition text-xs font-semibold">3</button>
            <button class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition text-xs font-semibold">Next</button>
          </div>
        </div>

      </div>

    </main>
  </div>
</div>

<!-- ================= NEW CUSTOMER MODAL (Paystack Style) ================= -->
<div id="newCustomerModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6 transition-all duration-300">
  <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden transform transition-all animate-in fade-in zoom-in-95 duration-200">
    
    <!-- Modal Header -->
    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
      <div>
        <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
          <svg class="w-5 h-5 text-[#0b2545]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
          </svg>
          Create Customer
        </h3>
        <p class="text-xs text-gray-500 mt-0.5">Add a new customer profile to track charges, recurring subscriptions, or card authorizations.</p>
      </div>
      <button onclick="closeNewCustomerModal()" class="w-8 h-8 rounded-full bg-white hover:bg-gray-100 border border-gray-200 text-gray-500 hover:text-gray-700 flex items-center justify-center transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <!-- Modal Body -->
    <form id="newCustomerForm" onsubmit="handleCreateCustomer(event)" class="p-6 space-y-4">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">First Name <span class="text-red-500">*</span></label>
          <input type="text" id="custFirstName" required placeholder="e.g. Alex" class="w-full px-3 py-2 text-xs border border-gray-200 rounded-xl focus:outline-hidden focus:border-[#0b2545] focus:ring-1 focus:ring-[#0b2545] transition">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Last Name <span class="text-red-500">*</span></label>
          <input type="text" id="custLastName" required placeholder="e.g. Mitchell" class="w-full px-3 py-2 text-xs border border-gray-200 rounded-xl focus:outline-hidden focus:border-[#0b2545] focus:ring-1 focus:ring-[#0b2545] transition">
        </div>
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email Address <span class="text-red-500">*</span></label>
        <div class="relative">
          <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path>
            </svg>
          </span>
          <input type="email" id="custEmail" required placeholder="customer@company.com" class="w-full pl-9 pr-3 py-2 text-xs border border-gray-200 rounded-xl focus:outline-hidden focus:border-[#0b2545] focus:ring-1 focus:ring-[#0b2545] transition">
        </div>
        <p class="text-[11px] text-gray-400 mt-1">Receipts and payment request links will be sent here.</p>
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Phone Number (Optional)</label>
        <div class="relative">
          <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
            </svg>
          </span>
          <input type="tel" id="custPhone" placeholder="+1 (555) 000-0000" class="w-full pl-9 pr-3 py-2 text-xs border border-gray-200 rounded-xl focus:outline-hidden focus:border-[#0b2545] focus:ring-1 focus:ring-[#0b2545] transition">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Default Currency</label>
          <select id="custCurrency" class="w-full px-3 py-2 text-xs border border-gray-200 rounded-xl focus:outline-hidden focus:border-[#0b2545] focus:ring-1 focus:ring-[#0b2545] transition bg-white">
            <option value="USD" selected>USD - US Dollar</option>
            <option value="CAD">CAD - Canadian Dollar</option>
            <option value="NGN">NGN - Nigerian Naira</option>
            <option value="GBP">GBP - British Pound</option>
            <option value="EUR">EUR - Euro</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Customer Tag (Optional)</label>
          <input type="text" id="custTag" placeholder="e.g. VIP, Wholesale" class="w-full px-3 py-2 text-xs border border-gray-200 rounded-xl focus:outline-hidden focus:border-[#0b2545] focus:ring-1 focus:ring-[#0b2545] transition">
        </div>
      </div>

      <div class="bg-blue-50/60 border border-blue-100 rounded-xl p-3 flex items-start gap-2.5">
        <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <p class="text-[11px] text-blue-800 leading-relaxed">
          A unique Paystack-compatible Customer Code (<code class="font-mono text-[10px] bg-blue-100/70 px-1 py-0.5 rounded text-blue-900">CUS_xxxxxx</code>) will be generated automatically. You can use it across API integrations, recurring charges, and invoices.
        </p>
      </div>

      <!-- Modal Actions -->
      <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
        <button type="button" onclick="closeNewCustomerModal()" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">
          Cancel
        </button>
        <button type="submit" id="createCustBtn" class="px-5 py-2 text-xs font-semibold text-white bg-[#0b2545] hover:bg-[#134074] rounded-xl shadow-sm transition flex items-center gap-1.5">
          <span>Create Customer</span>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ================= CUSTOMER DETAILS MODAL (Paystack Style Drawer) ================= -->
<div id="customerDetailsModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6 transition-all duration-300">
  <div class="relative w-full max-w-xl bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden transform transition-all animate-in fade-in zoom-in-95 duration-200">
    
    <!-- Modal Header -->
    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/60 flex items-start justify-between">
      <div class="flex items-center gap-3.5">
        <div id="modalCustomerAvatar" class="w-12 h-12 rounded-full bg-[#0b2545] text-white font-bold flex items-center justify-center text-sm shadow-xs">
          JD
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h3 id="modalCustomerName" class="text-base font-bold text-gray-900">Customer Name</h3>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
              Active
            </span>
          </div>
          <div class="flex items-center gap-2 mt-0.5">
            <span class="font-mono text-xs text-gray-500" id="modalCustomerCode">CUS_0000000</span>
            <button onclick="copyCustomerCode()" title="Copy Code" class="text-gray-400 hover:text-[#0b2545] transition">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
      <button onclick="closeCustomerDetailsModal()" class="w-8 h-8 rounded-full bg-white hover:bg-gray-100 border border-gray-200 text-gray-500 hover:text-gray-700 flex items-center justify-center transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <!-- Modal Content -->
    <div class="p-6 space-y-6">
      
      <!-- Key KPI Mini Badges -->
      <div class="grid grid-cols-3 gap-3">
        <div class="bg-gray-50/80 border border-gray-100 rounded-xl p-3 text-center">
          <div class="text-[10px] uppercase font-bold tracking-wider text-gray-400">Total Spent</div>
          <div class="text-sm font-bold text-gray-900 mt-1" id="modalCustomerTotalSpent">$0.00</div>
        </div>
        <div class="bg-gray-50/80 border border-gray-100 rounded-xl p-3 text-center">
          <div class="text-[10px] uppercase font-bold tracking-wider text-gray-400">Payments</div>
          <div class="text-sm font-bold text-gray-900 mt-1" id="modalCustomerPaymentCount">0</div>
        </div>
        <div class="bg-gray-50/80 border border-gray-100 rounded-xl p-3 text-center">
          <div class="text-[10px] uppercase font-bold tracking-wider text-gray-400">Joined</div>
          <div class="text-xs font-semibold text-gray-700 mt-1.5" id="modalCustomerJoined">--</div>
        </div>
      </div>

      <!-- Contact Info Section -->
      <div>
        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2.5">Customer Information</h4>
        <div class="bg-white border border-gray-200/80 rounded-xl divide-y divide-gray-100 text-xs">
          <div class="py-2.5 px-3.5 flex items-center justify-between">
            <span class="text-gray-500">Email Address</span>
            <span class="font-medium text-gray-900 font-mono" id="modalCustomerEmail">--</span>
          </div>
          <div class="py-2.5 px-3.5 flex items-center justify-between">
            <span class="text-gray-500">Phone Number</span>
            <span class="font-medium text-gray-900 font-mono" id="modalCustomerPhone">--</span>
          </div>
          <div class="py-2.5 px-3.5 flex items-center justify-between">
            <span class="text-gray-500">Paystack Customer ID</span>
            <span class="font-medium text-gray-900 font-mono" id="modalCustomerIdDisplay">--</span>
          </div>
        </div>
      </div>

      <!-- Saved Payment Authorizations Section -->
      <div>
        <div class="flex items-center justify-between mb-2.5">
          <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">Saved Authorizations</h4>
          <span class="text-[10px] text-gray-400">Tokenized Cards</span>
        </div>
        <div id="modalCustomerAuthDetails" class="bg-gray-50/70 border border-gray-200/80 rounded-xl p-3.5 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-9 h-7 rounded bg-white border border-gray-200 flex items-center justify-center shadow-2xs">
              <svg class="w-5 h-5 text-blue-800" fill="currentColor" viewBox="0 0 24 24">
                <path d="M21 4H3c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h18c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H3V6h18v12zm-10-7h8v2h-8zm0 4h5v2h-5z"/>
              </svg>
            </div>
            <div>
              <div class="text-xs font-bold text-gray-800" id="modalAuthCardName">Card Authorization</div>
              <div class="text-[11px] text-gray-400" id="modalAuthCardDesc">Reusable for automated recurring charges</div>
            </div>
          </div>
          <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            Reusable
          </span>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
        <button onclick="copyCustomerCode()" class="px-3 py-1.5 rounded-xl border border-gray-200 hover:bg-gray-50 text-xs font-semibold text-gray-700 transition flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
          </svg>
          Copy Code
        </button>

        <div class="flex items-center gap-2">
          <a href="invoices.php" class="px-3.5 py-1.5 rounded-xl border border-gray-200 hover:border-[#0b2545] text-xs font-semibold text-gray-700 hover:text-[#0b2545] transition flex items-center gap-1">
            <span>Send Invoice</span>
          </a>
          <a href="checkout.php" class="px-3.5 py-1.5 rounded-xl bg-[#0b2545] hover:bg-[#134074] text-white text-xs font-semibold transition flex items-center gap-1">
            <span>Charge Customer</span>
          </a>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- ================= FLOATING TOAST ================= -->
<div id="floatingToast" class="fixed bottom-6 right-6 z-60 hidden transition-all duration-300 transform translate-y-3 opacity-0">
  <div class="bg-gray-900 text-white px-4 py-3 rounded-xl shadow-xl flex items-center gap-3 border border-gray-700 max-w-sm">
    <div id="toastIcon" class="w-5 h-5 text-emerald-400 shrink-0">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
      </svg>
    </div>
    <div class="text-xs font-medium" id="toastMessage">Action completed successfully</div>
  </div>
</div>

<!-- ================= SCRIPTS ================= -->
<script>
  let currentActiveCustomerCode = '';

  // Open & Close New Customer Modal
  function openNewCustomerModal() {
    document.getElementById('newCustomerModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
  }

  function closeNewCustomerModal() {
    document.getElementById('newCustomerModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
  }

  // Open & Close Customer Details Modal
  function viewCustomerDetails(name, code, email, phone, payments, totalSpent, authCard, joined) {
    currentActiveCustomerCode = code;
    document.getElementById('modalCustomerName').textContent = name;
    document.getElementById('modalCustomerCode').textContent = code;
    document.getElementById('modalCustomerIdDisplay').textContent = code;
    document.getElementById('modalCustomerEmail').textContent = email;
    document.getElementById('modalCustomerPhone').textContent = phone;
    document.getElementById('modalCustomerPaymentCount').textContent = payments;
    document.getElementById('modalCustomerTotalSpent').textContent = totalSpent;
    document.getElementById('modalCustomerJoined').textContent = joined;

    // Avatar initials
    const initials = name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
    document.getElementById('modalCustomerAvatar').textContent = initials;

    // Card auth details
    const authBox = document.getElementById('modalCustomerAuthDetails');
    const authName = document.getElementById('modalAuthCardName');
    const authDesc = document.getElementById('modalAuthCardDesc');

    if (authCard && authCard !== 'None') {
      authBox.classList.remove('hidden');
      authName.textContent = authCard;
      authDesc.textContent = 'Active tokenized authorization for recurring charges';
    } else {
      authName.textContent = 'No card authorization on file';
      authDesc.textContent = 'Customer has not yet saved a card via checkout';
    }

    document.getElementById('customerDetailsModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
  }

  function closeCustomerDetailsModal() {
    document.getElementById('customerDetailsModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
  }

  // Copy Customer Code
  function copyCustomerCode() {
    if (!currentActiveCustomerCode) return;
    navigator.clipboard.writeText(currentActiveCustomerCode).then(() => {
      showToast(`Copied ${currentActiveCustomerCode} to clipboard!`);
    }).catch(() => {
      showToast(`Customer code: ${currentActiveCustomerCode}`);
    });
  }

  // Toast Notification
  function showToast(message) {
    const toast = document.getElementById('floatingToast');
    const msgEl = document.getElementById('toastMessage');
    msgEl.textContent = message;
    toast.classList.remove('hidden');
    setTimeout(() => {
      toast.classList.remove('translate-y-3', 'opacity-0');
    }, 10);

    setTimeout(() => {
      toast.classList.add('translate-y-3', 'opacity-0');
      setTimeout(() => toast.classList.add('hidden'), 300);
    }, 3500);
  }

  // Handle New Customer Creation
  function handleCreateCustomer(e) {
    e.preventDefault();
    const btn = document.getElementById('createCustBtn');
    const firstName = document.getElementById('custFirstName').value.trim();
    const lastName = document.getElementById('custLastName').value.trim();
    const email = document.getElementById('custEmail').value.trim();
    const phone = document.getElementById('custPhone').value.trim() || '—';
    const currency = document.getElementById('custCurrency').value;

    btn.disabled = true;
    btn.innerHTML = `
      <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
      </svg>
      Saving...
    `;

    setTimeout(() => {
      btn.disabled = false;
      btn.innerHTML = '<span>Create Customer</span>';
      
      const newCode = 'CUS_' + Math.floor(1000000 + Math.random() * 9000000);
      const fullName = `${firstName} ${lastName}`;
      const initials = `${firstName[0] || ''}${lastName[0] || ''}`.toUpperCase();

      // Append row to table
      const tbody = document.getElementById('customersTableBody');
      const tr = document.createElement('tr');
      tr.className = 'hover:bg-blue-50/20 transition group customer-row';
      tr.setAttribute('data-name', fullName);
      tr.setAttribute('data-email', email);
      tr.setAttribute('data-code', newCode);
      tr.setAttribute('data-currency', currency);
      tr.setAttribute('data-has-card', 'false');

      tr.innerHTML = `
        <td class="py-4 px-4 whitespace-nowrap">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-700 font-bold flex items-center justify-center text-xs shrink-0 group-hover:bg-[#0b2545] group-hover:text-white transition">
              ${initials}
            </div>
            <div>
              <div class="font-bold text-gray-900 group-hover:text-[#0b2545] transition flex items-center gap-1.5">
                ${fullName}
                <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">New</span>
              </div>
              <div class="text-[11px] text-gray-400 font-mono">${newCode}</div>
            </div>
          </div>
        </td>
        <td class="py-4 px-4 whitespace-nowrap">
          <div class="text-gray-900 font-medium">${email}</div>
          <div class="text-[11px] text-gray-400">${phone}</div>
        </td>
        <td class="py-4 px-4 text-center whitespace-nowrap">
          <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 text-gray-400" title="No saved authorization">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
            </svg>
          </span>
        </td>
        <td class="py-4 px-4 text-right whitespace-nowrap">
          <span class="font-bold text-gray-400">0 Payments</span>
        </td>
        <td class="py-4 px-4 text-right whitespace-nowrap">
          <div class="font-bold text-gray-900">$0.00</div>
          <div class="text-[10px] text-gray-400">${currency}</div>
        </td>
        <td class="py-4 px-4 whitespace-nowrap text-gray-500">
          Just now
        </td>
        <td class="py-4 px-4 text-right whitespace-nowrap">
          <button class="px-3 py-1.5 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-600 hover:text-white text-xs font-semibold transition shadow-2xs" 
            onclick="viewCustomerDetails('${fullName}', '${newCode}', '${email}', '${phone}', '0', '$0.00 ${currency}', 'None', 'Just now')">
            View
          </button>
        </td>
      `;

      tbody.prepend(tr);
      closeNewCustomerModal();
      document.getElementById('newCustomerForm').reset();
      showToast(`Customer ${fullName} created (${newCode})`);
      filterCustomers();
    }, 450);
  }

  // Filter and Search
  function filterCustomers() {
    const searchVal = (document.getElementById('customerSearchInput')?.value || '').toLowerCase();
    const statusVal = document.getElementById('customerStatusFilter')?.value || 'all';
    const currencyVal = document.getElementById('customerCurrencyFilter')?.value || 'all';

    const rows = document.querySelectorAll('.customer-row');
    let visibleCount = 0;

    rows.forEach(row => {
      const name = (row.getAttribute('data-name') || '').toLowerCase();
      const email = (row.getAttribute('data-email') || '').toLowerCase();
      const code = (row.getAttribute('data-code') || '').toLowerCase();
      const currency = row.getAttribute('data-currency') || '';
      const hasCard = row.getAttribute('data-has-card') === 'true';

      const matchesSearch = !searchVal || name.includes(searchVal) || email.includes(searchVal) || code.includes(searchVal);
      const matchesCurrency = currencyVal === 'all' || currency === currencyVal;
      
      let matchesStatus = true;
      if (statusVal === 'has_card') matchesStatus = hasCard;
      if (statusVal === 'no_card') matchesStatus = !hasCard;

      if (matchesSearch && matchesCurrency && matchesStatus) {
        row.style.display = '';
        visibleCount++;
      } else {
        row.style.display = 'none';
      }
    });

    const countEl = document.getElementById('visibleCustomerCount');
    if (countEl) countEl.textContent = visibleCount;
  }

  // Attach search input event listener
  document.getElementById('customerSearchInput')?.addEventListener('input', filterCustomers);

  // Close modals on Escape key or backdrop click
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeNewCustomerModal();
      closeCustomerDetailsModal();
    }
  });

  document.getElementById('newCustomerModal')?.addEventListener('click', (e) => {
    if (e.target.id === 'newCustomerModal') closeNewCustomerModal();
  });

  document.getElementById('customerDetailsModal')?.addEventListener('click', (e) => {
    if (e.target.id === 'customerDetailsModal') closeCustomerDetailsModal();
  });
</script>

<script>
(() => {
  const token = () => localStorage.getItem('flow_token') || '';
  const esc = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
  const initials = name => String(name || '?').split(/\s+/).filter(Boolean).slice(0,2).map(part => part[0]).join('').toUpperCase();
  const money = value => `₦${Number(value || 0).toLocaleString('en-NG',{minimumFractionDigits:2,maximumFractionDigits:2})}`;
  async function loadCustomers() {
    const body = document.getElementById('customerTableBody'); if (!body) return;
    try {
      const q = document.getElementById('customerSearchInput')?.value || '';
      const response = await fetch(`../api/customers/list.php?q=${encodeURIComponent(q)}`, {headers:{Authorization:`Bearer ${token()}`,Accept:'application/json'},cache:'no-store'});
      const result = await response.json(); if (!response.ok || !result.success) throw new Error(result.error || 'Unable to load customers.');
      const customers = result.customers || [];
      const totalValue = customers.reduce((sum, customer) => sum + Number(customer.lifetime_value || 0), 0);
      const repeatCustomers = customers.filter(customer => Number(customer.payment_count || 0) > 1).length;
      const totalMetric = document.getElementById('customersTotalMetric'); if (totalMetric) totalMetric.textContent = String(result.total || customers.length);
      const cardsMetric = document.getElementById('customersCardsMetric'); if (cardsMetric) cardsMetric.textContent = '0 Cards';
      const valueMetric = document.getElementById('customersValueMetric'); if (valueMetric) valueMetric.textContent = money(totalValue);
      const averageMetric = document.getElementById('customersAverageMetric'); if (averageMetric) averageMetric.textContent = customers.length ? `Avg ${money(totalValue / customers.length)} / user` : '—';
      const repeatMetric = document.getElementById('customersRepeatMetric'); if (repeatMetric) repeatMetric.textContent = customers.length ? `${(repeatCustomers / customers.length * 100).toFixed(1)}%` : '—';
      body.innerHTML = customers.length ? customers.map(customer => `<tr class="hover:bg-gray-50/70 transition group customer-row" data-filter="active" data-currency="NGN" data-name="${esc(customer.full_name)}" data-email="${esc(customer.email)}" data-code="${esc(customer.uuid)}" data-has-card="false"><td class="py-4 px-4 whitespace-nowrap"><div class="flex items-center gap-3"><div class="w-9 h-9 rounded-full bg-[#0b2545] text-white flex items-center justify-center font-bold text-xs flex-shrink-0">${esc(initials(customer.full_name))}</div><div><div class="font-bold text-gray-900">${esc(customer.full_name)}</div><div class="text-[10px] text-gray-400 font-mono">${esc(customer.uuid)}</div></div></div></td><td class="py-4 px-4 whitespace-nowrap"><span class="text-gray-900 font-medium">${esc(customer.email || '—')}</span></td><td class="py-4 px-4 whitespace-nowrap text-gray-600 font-mono">${esc(customer.phone || '—')}</td><td class="py-4 px-4 text-center whitespace-nowrap"><span class="text-xs text-gray-400">—</span></td><td class="py-4 px-4 text-right whitespace-nowrap"><span class="font-bold text-gray-900">${esc(customer.payment_count || 0)} Payments</span></td><td class="py-4 px-4 text-right whitespace-nowrap"><div class="font-bold text-gray-900">${esc(money(customer.lifetime_value))}</div><div class="text-[10px] text-gray-400">NGN</div></td><td class="py-4 px-4 whitespace-nowrap text-gray-500">${esc(new Date(customer.created_at).toLocaleDateString('en-NG'))}</td><td class="py-4 px-4 text-right whitespace-nowrap"><button class="px-3 py-1.5 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-600 hover:text-white text-xs font-semibold transition" data-customer-view="${esc(customer.uuid)}">View</button></td></tr>`).join('') : '<tr><td colspan="8" class="py-16 text-center text-sm text-gray-400">No customers found.</td></tr>';
      body.style.visibility = 'visible';
      const count = document.getElementById('visibleCustomerCount'); if (count) count.textContent = customers.length; const total = document.getElementById('totalCustomerCount'); if (total) total.textContent = result.total || customers.length;
      body.querySelectorAll('[data-customer-view]').forEach(button => button.addEventListener('click', () => { const customer = customers.find(item => item.uuid === button.dataset.customerView); if (customer) viewCustomerDetails(customer.full_name, customer.uuid, customer.email || '—', customer.phone || '—', customer.payment_count || 0, money(customer.lifetime_value), 'None', new Date(customer.created_at).toLocaleDateString('en-NG')); }));
    } catch (error) { body.innerHTML = '<tr><td colspan="8" class="py-16 text-center text-sm text-gray-400">Customer data is unavailable.</td></tr>'; body.style.visibility = 'visible'; }
  }
  window.loadCustomers = loadCustomers;
  window.handleCreateCustomer = async function(event) {
    event.preventDefault();
    const name=[document.getElementById('custFirstName')?.value.trim(),document.getElementById('custLastName')?.value.trim()].filter(Boolean).join(' '), email=document.getElementById('custEmail')?.value.trim()||'', phone=document.getElementById('custPhone')?.value.trim()||'';
    if (!name || !email) { alert('Enter the customer name and email.'); return; }
    try {
      const response=await fetch('../api/customers/save.php',{method:'POST',headers:{'Content-Type':'application/json',Authorization:`Bearer ${token()}`},body:JSON.stringify({full_name:name,email,phone})});
      const result=await response.json(); if(!response.ok||!result.success) throw new Error(result.error||'Unable to create customer.');
      closeNewCustomerModal(); document.getElementById('newCustomerForm')?.reset(); await loadCustomers(); showToast('Customer created.');
    } catch(error) { alert(error.message); }
  };
  document.getElementById('customerSearchInput')?.addEventListener('input', () => { clearTimeout(window.customerSearchTimer); window.customerSearchTimer = setTimeout(loadCustomers, 250); });
  loadCustomers();
})();
</script>

<?php
require_once __DIR__ . '/includes/bottom_nav.php';
require_once __DIR__ . '/includes/footer.php';
?>
