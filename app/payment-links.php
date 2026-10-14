<?php
/**
 * Flowwithlit Payment Links & Invoices Page
 * Instant Shareable Checkout Links & Professional Invoicing
 */
$pageTitle = 'Payment Links & Invoices — Flowwithlit';
$activePage = 'payment-links';
$activeNavTab = 'Features';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<style>#ledgerTableBody { visibility:hidden; }</style>

    <!-- Scrollable Main Content Area -->
    <main class="flex-1 h-full overflow-y-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-6 flex flex-col gap-6">
      
      <!-- Top Title & Quick Action Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 flex-shrink-0">
        <div>
          <div class="flex items-center gap-2">
            <span class="text-xs text-gray-400 font-medium tracking-wide">Merchant &amp; Invoicing</span>
            <span id="activeCollectsBadge" class="text-[11px] font-bold text-sky-700 bg-sky-50 border border-sky-200/50 px-2 py-0.5 rounded-full">0 Active Collects</span>
          </div>
          <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight mt-0.5">Payment Links &amp; Invoices</h1>
        </div>

        <!-- Action buttons -->
        <div class="flex items-center gap-2.5 flex-wrap">
          <button id="openInvoiceModalBtn" class="px-4 py-2.5 rounded-full bg-white hover:bg-gray-50 border border-gray-200/80 text-gray-700 text-xs font-semibold flex items-center gap-2 shadow-xs transition hover:scale-105 active:scale-95">
            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            <span>Create Invoice</span>
          </button>

          <button id="openLinkModalBtn" class="bg-[#0b2545] hover:bg-[#132847] text-white px-5 py-2.5 rounded-full text-xs font-semibold flex items-center gap-2 shadow-sm transition hover:scale-105 active:scale-95">
            <span class="w-4 h-4 rounded-full border border-white/40 flex items-center justify-center text-[11px] leading-none font-bold">+</span>
            <span>Create Payment Link</span>
          </button>
        </div>
      </div>

      <!-- Row 1: KPI Stats Row -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 flex-shrink-0">
        
        <!-- Stat 1: Total Online Revenue -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex flex-col justify-between">
          <div class="flex items-center justify-between text-xs text-gray-500 font-medium">
            <span>Online Revenue Collected</span>
            <div class="w-8 h-8 rounded-xl bg-sky-50 text-[#0b2545] flex items-center justify-center font-bold">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-3">
            <div id="linksRevenueMetric" class="text-2xl font-black text-gray-900 tracking-tight">₦0.00</div>
            <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1">
              <span>This billing cycle</span>
              <span id="linksRevenueTrend" class="font-bold text-gray-400">No data yet</span>
            </div>
          </div>
        </div>

        <!-- Stat 2: Active Links -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex flex-col justify-between">
          <div class="flex items-center justify-between text-xs text-gray-500 font-medium">
            <span>Active Payment Links</span>
            <div class="w-8 h-8 rounded-xl bg-sky-50 text-[#0b2545] flex items-center justify-center font-bold">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
              </svg>
            </div>
          </div>
          <div class="mt-3">
            <div id="linksCountMetric" class="text-2xl font-black text-gray-900 tracking-tight">0 Links</div>
            <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1">
              <span id="linksCountDetail">No links created</span>
              <span class="font-bold text-gray-400">—</span>
            </div>
          </div>
        </div>

        <!-- Stat 3: Outstanding Invoices -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex flex-col justify-between">
          <div class="flex items-center justify-between text-xs text-gray-500 font-medium">
            <span>Outstanding Invoices</span>
            <div class="w-8 h-8 rounded-xl bg-sky-50 text-[#0b2545] flex items-center justify-center font-bold">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-3">
            <div id="linksOutstandingMetric" class="text-2xl font-black text-gray-900 tracking-tight">₦0.00</div>
            <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1">
              <span id="linksOutstandingDetail">No pending invoices</span>
              <span class="font-bold text-gray-400">—</span>
            </div>
          </div>
        </div>

        <!-- Stat 4: Conversion Rate -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex flex-col justify-between">
          <div class="flex items-center justify-between text-xs text-gray-500 font-medium">
            <span>Checkout Conversion</span>
            <div class="w-8 h-8 rounded-xl bg-sky-50 text-[#0b2545] flex items-center justify-center font-bold">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.25 2.25L15 4.5" />
              </svg>
            </div>
          </div>
          <div class="mt-3">
            <div id="linksConversionMetric" class="text-2xl font-black text-gray-900 tracking-tight">—</div>
            <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1">
              <span>No payment data yet</span>
              <span class="font-bold text-gray-400">—</span>
            </div>
          </div>
        </div>

      </div>

      <!-- Main Section: Clean Payment Links & Invoices Ledger -->
      <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs flex flex-col">
        
        <!-- Controls Bar: Segmented Pills, Search, Filter -->
        <div class="p-5 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
          
          <!-- Filter Tabs / Pills -->
          <div class="flex items-center gap-1.5 overflow-x-auto pb-1 lg:pb-0 scrollbar-none text-xs font-semibold">
            <button class="filter-tab active px-3.5 py-1.5 rounded-full bg-[#0b2545] text-white transition shadow-2xs" data-filter="all">
              All <span data-link-count="all" class="ml-1 opacity-70 text-[11px]">0</span>
            </button>
            <button class="filter-tab px-3.5 py-1.5 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 transition" data-filter="link">
              Links <span data-link-count="link" class="ml-1 opacity-70 text-[11px]">0</span>
            </button>
            <button class="filter-tab px-3.5 py-1.5 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 transition" data-filter="invoice">
              Invoices <span data-link-count="invoice" class="ml-1 opacity-70 text-[11px]">0</span>
            </button>
            <button class="filter-tab px-3.5 py-1.5 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 transition" data-filter="paid">
              Paid <span data-link-count="paid" class="ml-1 opacity-70 text-[11px]">0</span>
            </button>
            <button class="filter-tab px-3.5 py-1.5 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 transition" data-filter="pending">
              Pending <span data-link-count="pending" class="ml-1 opacity-70 text-[11px]">0</span>
            </button>
            <button class="filter-tab px-3.5 py-1.5 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 transition" data-filter="expired">
              Expired <span data-link-count="expired" class="ml-1 opacity-70 text-[11px]">0</span>
            </button>
          </div>

          <!-- Search & Filter Dropdown -->
          <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap">
            <!-- Search Input -->
            <div class="relative flex-1 sm:w-64">
              <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
              </span>
              <input type="text" id="searchInput" placeholder="Search title, client, or ref..." class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-full text-xs text-gray-800 focus:outline-hidden focus:bg-white focus:border-[#0b2545] transition" />
            </div>

            <!-- Currency selector -->
            <select id="currencyFilter" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-full text-xs text-gray-700 font-medium focus:outline-hidden focus:border-[#0b2545] transition">
              <option value="all">All Currencies</option>
              <option value="USD">USD ($)</option>
              <option value="EUR">EUR (€)</option>
              <option value="GBP">GBP (£)</option>
              <option value="CAD">CAD ($)</option>
              <option value="NGN">NGN (₦)</option>
              <option value="USDT">USDT (₮)</option>
            </select>

            <!-- Export Button -->
            <button class="px-3.5 py-2 rounded-full bg-white hover:bg-gray-50 border border-gray-200/80 text-gray-700 text-xs font-semibold flex items-center gap-1.5 shadow-2xs transition hover:scale-105 active:scale-95" onclick="alert('Exporting Payment Links & Invoices ledger...')">
              <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
              </svg>
              <span>Export</span>
            </button>
          </div>
        </div>

        <!-- 100% Full Width Responsive Table Container -->
        <div class="w-full overflow-x-auto">
          <table class="w-full text-left border-collapse min-w-[900px]">
            <thead>
              <tr class="border-b border-gray-100 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                <th class="py-3 px-4">Title &amp; Reference</th>
                <th class="py-3 px-4">Client / Recipient</th>
                <th class="py-3 px-4">Type</th>
                <th class="py-3 px-4 text-right">Amount</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4">Created / Due</th>
                <th class="py-3 px-4 text-right">Action / Share</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100/80 text-xs" id="ledgerTableBody">
              
              <!-- Row 1: Retainer Invoice - Paid -->
              <tr class="hover:bg-gray-50/70 transition group" data-type="invoice" data-status="paid" data-currency="USD">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gray-100 text-[#0b2545] flex items-center justify-center font-bold text-xs flex-shrink-0 border border-gray-200/60">
                      <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                      </svg>
                    </div>
                    <div>
                      <div class="font-bold text-gray-900">Q3 Enterprise Platform Retainer</div>
                      <div class="text-[10px] text-gray-400 font-mono">INV-2026-0048</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="font-bold text-gray-900">Acme Cloud Solutions Inc</div>
                  <div class="text-[10px] text-gray-400">billing@acmecloud.com</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <span class="text-[11px] font-semibold text-gray-700">Invoice</span>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-bold text-gray-900">$24,500.00</div>
                  <div class="text-[10px] text-gray-400">USD &bull; Wire/Card</div>
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
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">16 Jun 2026</div>
                  <div class="text-[10px] text-emerald-600 font-medium">Paid on 17 Jun</div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1.5">
                    <!-- View/Print Invoice Icon -->
                    <div class="relative inline-flex items-center group/tip">
                      <button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-500 hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" onclick="previewInvoice('INV-2026-0048', 'Acme Cloud Solutions Inc', '$24,500.00', '16 Jun 2026', 'Paid', 'Q3 Enterprise Platform Retainer')" aria-label="View Invoice">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z" />
                        </svg>
                      </button>
                      <div class="absolute right-0 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                        View Invoice
                        <div class="absolute top-full right-3 border-4 border-transparent border-t-[#0b2545]"></div>
                      </div>
                    </div>

                    <!-- Copy Direct URL -->
                    <div class="relative inline-flex items-center group/tip">
                      <button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-500 hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" onclick="copyShareUrl('https://pay.flowwithlit.io/inv/INV-2026-0048')" aria-label="Copy Link">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                        </svg>
                      </button>
                      <div class="absolute right-0 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                        Copy Link
                        <div class="absolute top-full right-3 border-4 border-transparent border-t-[#0b2545]"></div>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>

              <!-- Row 2: Recurring Saas Subscription Link - Paid -->
              <tr class="hover:bg-gray-50/70 transition group" data-type="link" data-status="paid" data-currency="USD">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gray-100 text-[#0b2545] flex items-center justify-center font-bold text-xs flex-shrink-0 border border-gray-200/60">
                      <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                      </svg>
                    </div>
                    <div>
                      <div class="font-bold text-gray-900">API Pro Tier (Annual Pass)</div>
                      <div class="text-[10px] text-gray-400 font-mono">LNK-8830192</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="font-bold text-gray-900">Public Checkout</div>
                  <div class="text-[10px] text-gray-400">Multi-use &bull; 42 Customers</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <span class="text-[11px] font-semibold text-gray-700">Payment Link</span>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-bold text-gray-900">$1,200.00</div>
                  <div class="text-[10px] text-gray-400">USD &bull; Reusable</div>
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <div class="relative inline-flex items-center justify-center group/status">
                    <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/80 flex items-center justify-center shadow-2xs transition group-hover/status:scale-110" aria-label="Status: Paid">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </span>
                    <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/status:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      Paid / Active
                      <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">10 Jun 2026</div>
                  <div class="text-[10px] text-gray-400">No Expiration</div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1.5">
                    <!-- QR Code button -->
                    <div class="relative inline-flex items-center group/tip">
                      <button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-500 hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" onclick="showQrModal('https://pay.flowwithlit.io/c/LNK-8830192', 'API Pro Tier (Annual Pass)', '$1,200.00 USD')" aria-label="Show QR Code">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                        </svg>
                      </button>
                      <div class="absolute right-0 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                        QR Code
                        <div class="absolute top-full right-3 border-4 border-transparent border-t-[#0b2545]"></div>
                      </div>
                    </div>

                    <!-- Copy Direct URL -->
                    <div class="relative inline-flex items-center group/tip">
                      <button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-500 hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" onclick="copyShareUrl('https://pay.flowwithlit.io/c/LNK-8830192')" aria-label="Copy Link">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                        </svg>
                      </button>
                      <div class="absolute right-0 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                        Copy Link
                        <div class="absolute top-full right-3 border-4 border-transparent border-t-[#0b2545]"></div>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>

              <!-- Row 3: Design Milestone Invoice - Pending -->
              <tr class="hover:bg-gray-50/70 transition group" data-type="invoice" data-status="pending" data-currency="EUR">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gray-100 text-[#0b2545] flex items-center justify-center font-bold text-xs flex-shrink-0 border border-gray-200/60">
                      <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                      </svg>
                    </div>
                    <div>
                      <div class="font-bold text-gray-900">Brand Identity &amp; UI System</div>
                      <div class="text-[10px] text-gray-400 font-mono">INV-2026-0049</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="font-bold text-gray-900">Nordic Digital Studio</div>
                  <div class="text-[10px] text-gray-400">finance@nordicstudio.se</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <span class="text-[11px] font-semibold text-gray-700">Invoice</span>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-bold text-gray-900">€8,900.00</div>
                  <div class="text-[10px] text-gray-400">EUR &bull; SEPA Instant</div>
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <div class="relative inline-flex items-center justify-center group/status">
                    <span class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 border border-gray-200 flex items-center justify-center shadow-2xs transition group-hover/status:scale-110" aria-label="Status: Pending">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </span>
                    <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/status:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      Pending Payment
                      <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">15 Jun 2026</div>
                  <div class="text-[10px] text-amber-600 font-medium">Due in 2 days</div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1.5">
                    <div class="relative inline-flex items-center group/tip">
                      <button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-500 hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" onclick="previewInvoice('INV-2026-0049', 'Nordic Digital Studio', '€8,900.00', '15 Jun 2026', 'Pending', 'Brand Identity & UI System')" aria-label="View Invoice">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z" />
                        </svg>
                      </button>
                      <div class="absolute right-0 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                        View Invoice
                        <div class="absolute top-full right-3 border-4 border-transparent border-t-[#0b2545]"></div>
                      </div>
                    </div>

                    <div class="relative inline-flex items-center group/tip">
                      <button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-500 hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" onclick="copyShareUrl('https://pay.flowwithlit.io/inv/INV-2026-0049')" aria-label="Copy Link">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                        </svg>
                      </button>
                      <div class="absolute right-0 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                        Copy Link
                        <div class="absolute top-full right-3 border-4 border-transparent border-t-[#0b2545]"></div>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>

              <!-- Row 4: Custom Single-Use Client Payment Link - Paid -->
              <tr class="hover:bg-gray-50/70 transition group" data-type="link" data-status="paid" data-currency="GBP">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gray-100 text-[#0b2545] flex items-center justify-center font-bold text-xs flex-shrink-0 border border-gray-200/60">
                      <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                      </svg>
                    </div>
                    <div>
                      <div class="font-bold text-gray-900">Mayfair Advisory Retainer</div>
                      <div class="text-[10px] text-gray-400 font-mono">LNK-9102451</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="font-bold text-gray-900">Lord Harrington Partners</div>
                  <div class="text-[10px] text-gray-400">advisory@harrington.co.uk</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <span class="text-[11px] font-semibold text-gray-700">Payment Link</span>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-bold text-gray-900">£14,200.00</div>
                  <div class="text-[10px] text-gray-400">GBP &bull; Faster Payments</div>
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
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">12 Jun 2026</div>
                  <div class="text-[10px] text-emerald-600 font-medium">Completed</div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1.5">
                    <div class="relative inline-flex items-center group/tip">
                      <button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-500 hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" onclick="showQrModal('https://pay.flowwithlit.io/c/LNK-9102451', 'Mayfair Advisory Retainer', '£14,200.00 GBP')" aria-label="Show QR Code">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                        </svg>
                      </button>
                      <div class="absolute right-0 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                        QR Code
                        <div class="absolute top-full right-3 border-4 border-transparent border-t-[#0b2545]"></div>
                      </div>
                    </div>

                    <div class="relative inline-flex items-center group/tip">
                      <button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-500 hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" onclick="copyShareUrl('https://pay.flowwithlit.io/c/LNK-9102451')" aria-label="Copy Link">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                        </svg>
                      </button>
                      <div class="absolute right-0 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                        Copy Link
                        <div class="absolute top-full right-3 border-4 border-transparent border-t-[#0b2545]"></div>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>

              <!-- Row 5: Expired Promotional Link -->
              <tr class="hover:bg-gray-50/70 transition group" data-type="link" data-status="expired" data-currency="USD">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gray-100 text-[#0b2545] flex items-center justify-center font-bold text-xs flex-shrink-0 border border-gray-200/60">
                      <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                      </svg>
                    </div>
                    <div>
                      <div class="font-bold text-gray-900">Summer Early Bird Pass</div>
                      <div class="text-[10px] text-gray-400 font-mono">LNK-3901928</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="font-bold text-gray-900">Open Public</div>
                  <div class="text-[10px] text-gray-400">Marketing &bull; 85 conversions</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <span class="text-[11px] font-semibold text-gray-700">Payment Link</span>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-bold text-gray-900">$299.00</div>
                  <div class="text-[10px] text-gray-400">USD &bull; Expired</div>
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <div class="relative inline-flex items-center justify-center group/status">
                    <span class="w-8 h-8 rounded-full bg-rose-50 text-rose-600 border border-rose-200/80 flex items-center justify-center shadow-2xs transition group-hover/status:scale-110" aria-label="Status: Expired">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </span>
                    <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/status:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      Expired
                      <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">01 Jun 2026</div>
                  <div class="text-[10px] text-rose-500 font-medium">Expired 15 Jun</div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1.5">
                    <div class="relative inline-flex items-center group/tip">
                      <button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-500 hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95 opacity-50 cursor-not-allowed" title="Link Expired" disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                        </svg>
                      </button>
                      <div class="absolute right-0 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                        Link Inactive
                        <div class="absolute top-full right-3 border-4 border-transparent border-t-[#0b2545]"></div>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>

              <!-- Row 6: USDT Crypto Settlement Invoice - Paid -->
              <tr class="hover:bg-gray-50/70 transition group" data-type="invoice" data-status="paid" data-currency="USDT">
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#26a17b]/10 text-[#26a17b] flex items-center justify-center font-bold text-xs flex-shrink-0 border border-[#26a17b]/20">
                      ₮
                    </div>
                    <div>
                      <div class="font-bold text-gray-900">Decentralized Infrastructure Node</div>
                      <div class="text-[10px] text-gray-400 font-mono">INV-2026-0050</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="font-bold text-gray-900">Helios Web3 Labs</div>
                  <div class="text-[10px] text-gray-400">payments@helioslabs.eth</div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <span class="text-[11px] font-semibold text-gray-700">Crypto Invoice</span>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="font-bold text-gray-900">18,500.00 ₮</div>
                  <div class="text-[10px] text-gray-400">USDT &bull; Tron TRC-20</div>
                </td>
                <td class="py-4 px-4 text-center whitespace-nowrap">
                  <div class="relative inline-flex items-center justify-center group/status">
                    <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/80 flex items-center justify-center shadow-2xs transition group-hover/status:scale-110" aria-label="Status: Paid">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </span>
                    <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/status:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                      Settled On-Chain
                      <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-[#0b2545]"></div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 whitespace-nowrap">
                  <div class="text-gray-900 font-medium">17 Jun 2026</div>
                  <div class="text-[10px] text-emerald-600 font-medium">Block #928104</div>
                </td>
                <td class="py-4 px-4 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1.5">
                    <div class="relative inline-flex items-center group/tip">
                      <button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-500 hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" onclick="previewInvoice('INV-2026-0050', 'Helios Web3 Labs', '18,500.00 USDT', '17 Jun 2026', 'Paid', 'Decentralized Infrastructure Node')" aria-label="View Invoice">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z" />
                        </svg>
                      </button>
                      <div class="absolute right-0 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                        View Invoice
                        <div class="absolute top-full right-3 border-4 border-transparent border-t-[#0b2545]"></div>
                      </div>
                    </div>

                    <div class="relative inline-flex items-center group/tip">
                      <button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-[#0b2545] text-gray-500 hover:text-white flex items-center justify-center transition shadow-2xs active:scale-95" onclick="copyShareUrl('https://pay.flowwithlit.io/inv/INV-2026-0050')" aria-label="Copy Link">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                        </svg>
                      </button>
                      <div class="absolute right-0 bottom-full mb-1.5 px-2.5 py-1 bg-[#0b2545] text-white text-[10px] font-semibold rounded-md shadow-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-30">
                        Copy Link
                        <div class="absolute top-full right-3 border-4 border-transparent border-t-[#0b2545]"></div>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>

            </tbody>
          </table>
        </div>

        <!-- Table Footer / Pagination -->
        <div class="p-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
          <div>
            Showing <span class="font-semibold text-gray-800" id="visibleCount">0</span> of <span class="font-semibold text-gray-800" id="totalLinkCount">0</span> items
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

<!-- ========================================================= -->
<!-- MODAL 1: Create Payment Link Modal (Mobile Full Screen) -->
<!-- ========================================================= -->
<div id="createLinkModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-0 sm:p-4 hidden transition-opacity">
  <div class="bg-white w-full h-full sm:h-auto sm:max-w-lg sm:rounded-[28px] p-6 sm:p-8 flex flex-col justify-between overflow-y-auto shadow-2xl relative">
    
    <!-- Modal Close Button -->
    <button id="closeLinkModalBtn" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition p-2">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>

    <div>
      <div class="flex items-center gap-2">
        <span class="w-2.5 h-2.5 rounded-full bg-[#0b2545]"></span>
        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Checkout Builder</span>
      </div>
      <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">Create Instant Payment Link</h2>
      <p class="text-xs text-gray-500 mt-1">Generate a branded hosted payment link for your customers. Nigerian bank transfer is available for the current launch.</p>

      <div id="linkCreateFormView">
      <form id="createLinkForm" class="mt-6 space-y-4" onsubmit="event.preventDefault(); submitCreateLink();">
        <!-- Title / Product Name -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Payment Title / Purpose</label>
          <input type="text" id="linkTitle" required placeholder="e.g. Monthly Retainer, Product License, Web Design" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:outline-hidden focus:bg-white focus:border-[#0b2545] transition" />
        </div>

        <!-- Currency & Amount Mode -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Currency</label>
            <select id="linkCurrency" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:outline-hidden focus:bg-white focus:border-[#0b2545] transition font-medium">
              <option value="NGN">NGN (₦) - Nigerian Naira</option>
              <option value="USDT">USDT (₮) - Tether USD</option>
              <option value="USDC">USDC - USD Coin</option>
              <option value="BTC">BTC - Bitcoin</option>
              <option value="ETH">ETH - Ethereum</option>
              <option value="SOL">SOL - Solana</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Amount (Fixed)</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-xs text-gray-500 font-bold" id="currencyPrefix">$</span>
              <input type="number" step="0.01" min="1" id="linkAmount" placeholder="150.00" class="w-full pl-8 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:outline-hidden focus:bg-white focus:border-[#0b2545] transition font-bold" />
            </div>
          </div>
        </div>

        <!-- Link Settings: Single vs Reusable -->
        <div class="pt-1">
          <label class="block text-xs font-semibold text-gray-700 mb-2">Usage Type</label>
          <div class="grid grid-cols-2 gap-2.5">
            <label class="flex items-center gap-2.5 p-3 rounded-xl border border-gray-200 bg-gray-50/70 hover:bg-gray-50 cursor-pointer text-xs font-medium text-gray-800 transition">
              <input type="radio" name="linkUsage" value="reusable" checked class="text-[#0b2545] focus:ring-[#0b2545]" />
              <div>
                <div class="font-bold text-gray-900">Reusable</div>
                <div class="text-[10px] text-gray-500">Multiple customers</div>
              </div>
            </label>
            <label class="flex items-center gap-2.5 p-3 rounded-xl border border-gray-200 bg-gray-50/70 hover:bg-gray-50 cursor-pointer text-xs font-medium text-gray-800 transition">
              <input type="radio" name="linkUsage" value="single" class="text-[#0b2545] focus:ring-[#0b2545]" />
              <div>
                <div class="font-bold text-gray-900">Single-Use</div>
                <div class="text-[10px] text-gray-500">Deactivates after pay</div>
              </div>
            </label>
          </div>
        </div>

        <!-- Success Redirect URL (Optional) -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Redirect URL (Optional)</label>
          <input type="url" id="linkRedirect" placeholder="https://yourdomain.com/thank-you" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:outline-hidden focus:bg-white focus:border-[#0b2545] transition" />
        </div>

        <!-- Submit Button -->
        <div class="pt-3">
          <button type="submit" id="btnSubmitCreateLink" class="w-full py-3 rounded-full bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-bold tracking-wide transition shadow-sm hover:scale-[1.02] active:scale-95">
            Generate Checkout Link
          </button>
        </div>
      </form>
      </div>
      <div id="linkSuccessView" class="hidden mt-8 text-center">
        <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-emerald-100 text-emerald-700 text-3xl">✓</div>
        <h3 class="mt-5 text-xl font-black text-gray-900">Payment link ready</h3>
        <p class="mt-2 text-xs leading-5 text-gray-500">Share this secure checkout link with your customer.</p>
        <div class="mt-6 flex items-center gap-2 rounded-xl border border-sky-200 bg-sky-50 p-2">
          <input type="text" id="generatedLinkUrl" readonly value="" class="min-w-0 flex-1 bg-transparent px-2 py-2 text-[11px] font-mono text-sky-950 outline-none select-all" />
          <button type="button" onclick="window.open(document.getElementById('generatedLinkUrl').value,'_blank','noopener,noreferrer')" class="shrink-0 rounded-lg border border-sky-200 bg-white px-3 py-2 text-xs font-semibold text-sky-900 hover:bg-sky-50">Open</button>
        </div>
        <button type="button" onclick="copyGeneratedLink()" class="mt-3 w-full rounded-xl bg-[#0b2545] px-4 py-3 text-xs font-bold text-white hover:bg-[#132847]">Copy checkout link</button>
        <button type="button" id="createAnotherLinkBtn" class="mt-3 text-xs font-semibold text-sky-700 hover:underline">Create another link</button>
      </div>
    </div>

  </div>
</div>

<!-- ========================================================= -->
<!-- MODAL 2: Create Professional Invoice Modal (Mobile Full Screen) -->
<!-- ========================================================= -->
<div id="createInvoiceModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-stretch justify-end hidden transition-opacity">
  <div id="invoiceDrawerPanel" class="bg-white w-full max-w-xl h-full p-6 sm:p-8 flex flex-col justify-between overflow-y-auto shadow-2xl relative transform translate-x-full transition-transform duration-300 ease-out">
    
    <!-- Modal Close Button -->
    <button id="closeInvoiceModalBtn" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition p-2">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>

    <div>
      <div class="flex items-center gap-2">
        <span class="w-2.5 h-2.5 rounded-full bg-[#0b2545]"></span>
        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Invoice Engine</span>
      </div>
      <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">Issue Professional Invoice</h2>
      <p class="text-xs text-gray-500 mt-1">Send a compliant corporate invoice with dynamic line items and auto calculations.</p>

      <form id="invoiceForm" class="mt-6 space-y-4" onsubmit="event.preventDefault(); submitInvoiceForm();">
        
        <!-- Header details: Invoice Number & Currency -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Invoice Number</label>
            <input type="text" id="invoiceNumberInput" readonly value="INV-2026-0051" class="w-full px-4 py-2 bg-gray-100 border border-gray-200 rounded-xl text-xs font-mono text-gray-600 font-bold" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Currency</label>
            <select id="invoiceCurrencyInput" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 font-medium focus:outline-hidden focus:border-[#0b2545] transition">
              <option value="NGN">NGN (₦) - Nigerian Naira</option>
              <option value="USDT">USDT (₮) - Tether USD</option>
              <option value="USDC">USDC - USD Coin</option>
              <option value="BTC">BTC - Bitcoin</option>
              <option value="ETH">ETH - Ethereum</option>
              <option value="SOL">SOL - Solana</option>
            </select>
          </div>
        </div>

        <!-- Client Information -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Client / Business Name</label>
            <input type="text" id="invClientName" required placeholder="e.g. Acme Corporation Inc" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:outline-hidden focus:bg-white focus:border-[#0b2545] transition" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Client Email Address</label>
            <input type="email" id="invClientEmail" required placeholder="billing@acme.com" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:outline-hidden focus:bg-white focus:border-[#0b2545] transition" />
          </div>
        </div>

        <!-- Dates: Issue & Due -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Issue Date</label>
            <input type="date" id="invIssueDate" value="2026-06-18" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:outline-hidden focus:border-[#0b2545] transition" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Payment Due Date</label>
            <input type="date" id="invDueDate" value="2026-07-02" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:outline-hidden focus:border-[#0b2545] transition" />
          </div>
        </div>

        <!-- Dynamic Line Items Table -->
        <div class="pt-2">
          <div class="flex items-center justify-between mb-2">
            <label class="text-xs font-bold text-gray-800">Items &amp; Deliverables</label>
            <button type="button" onclick="addLineItem()" class="text-xs font-bold text-[#0b2545] hover:underline flex items-center gap-1">
              <span>+ Add Line Item</span>
            </button>
          </div>

          <div class="space-y-2" id="lineItemsContainer">
            <!-- Row 1 -->
            <div class="flex items-center gap-2 line-item-row">
              <input type="text" placeholder="Description of service or item" class="flex-1 px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 line-desc focus:outline-hidden focus:border-[#0b2545]" value="Enterprise Cloud Architecture Consulting" required />
              <input type="number" min="1" value="1" placeholder="Qty" class="w-16 px-2.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 line-qty text-center focus:outline-hidden focus:border-[#0b2545]" onchange="calcInvoiceTotal()" />
              <input type="number" step="0.01" min="0" value="4500.00" placeholder="Price" class="w-24 px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 line-price text-right font-bold focus:outline-hidden focus:border-[#0b2545]" onchange="calcInvoiceTotal()" />
              <button type="button" onclick="removeLineItem(this)" class="p-2 text-gray-400 hover:text-rose-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Totals summary block -->
          <div class="mt-4 p-4 rounded-xl bg-gray-50 border border-gray-200/80 flex flex-col items-end gap-1 text-xs">
            <div class="flex items-center justify-between w-48 text-gray-500">
              <span>Subtotal:</span>
              <span class="font-bold text-gray-900" id="invSubtotal">$4,500.00</span>
            </div>
            <div class="flex items-center justify-between w-48 text-gray-500">
              <span>Tax / VAT (0%):</span>
              <span class="font-bold text-gray-900">$0.00</span>
            </div>
            <div class="flex items-center justify-between w-48 text-sm font-black text-gray-900 border-t border-gray-200 pt-1.5 mt-1">
              <span>Total:</span>
              <span class="text-[#0b2545]" id="invTotalAmount">$4,500.00</span>
            </div>
          </div>
        </div>

        <!-- Notes / Terms -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5">Customer Notes / Bank Details</label>
          <textarea id="invNotes" rows="2" placeholder="Payment terms: Net 14 days. Please remit payment via ACH or direct card checkout." class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:outline-hidden focus:border-[#0b2545] transition"></textarea>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
          <button type="submit" class="w-full py-3 rounded-full bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-bold tracking-wide transition shadow-sm hover:scale-[1.02] active:scale-95">
            Issue &amp; Send Invoice
          </button>
        </div>

      </form>
    </div>

  </div>
</div>

<!-- ========================================================= -->
<!-- MODAL 3: Invoice Preview Modal (Branded PDF-Style) -->
<!-- ========================================================= -->
<div id="invoicePreviewModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-0 sm:p-4 hidden transition-opacity">
  <div class="bg-white w-full h-full sm:h-auto sm:max-w-xl sm:rounded-[28px] p-6 sm:p-8 flex flex-col justify-between overflow-y-auto shadow-2xl relative">
    
    <!-- Modal Close Button -->
    <button onclick="closeInvoicePreview()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition p-2">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>

    <div>
      <!-- Invoice Header preview -->
      <div class="flex items-center justify-between border-b border-gray-100 pb-5">
        <div>
          <div class="text-xs font-bold tracking-wider uppercase text-gray-400">FLOWWITHLIT CORPORATE INVOICE</div>
          <div class="text-2xl font-black text-gray-900 tracking-tight mt-0.5" id="prevInvNumber">INV-2026-0048</div>
        </div>
        <div class="text-right">
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/70" id="prevInvStatusBadge">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
            <span id="prevInvStatusText">Paid</span>
          </span>
        </div>
      </div>

      <!-- Bill To & Meta -->
      <div class="grid grid-cols-2 gap-4 py-5 border-b border-gray-100 text-xs">
        <div>
          <div class="text-gray-400 font-medium">Billed To:</div>
          <div class="font-bold text-gray-900 mt-0.5 text-sm" id="prevClientName">Acme Cloud Solutions Inc</div>
          <div class="text-gray-500 mt-0.5" id="prevItemTitle">Q3 Enterprise Platform Retainer</div>
        </div>
        <div class="text-right">
          <div class="text-gray-400 font-medium">Issue Date:</div>
          <div class="font-bold text-gray-900 mt-0.5" id="prevIssueDate">16 Jun 2026</div>
          <div class="text-gray-400 font-medium mt-2">Payment Terms:</div>
          <div class="text-gray-700">Due Upon Receipt</div>
        </div>
      </div>

      <!-- Amount Summary -->
      <div class="py-6 flex flex-col items-center justify-center bg-gray-50 rounded-2xl border border-gray-200/80 my-5">
        <span class="text-xs text-gray-400 font-medium">Total Amount Due</span>
        <div class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight mt-1" id="prevTotalAmount">$24,500.00</div>
        <div class="text-[11px] text-gray-400 mt-1 font-mono">Accepted Rails: ACH, Wire, SEPA, Cards, USDT</div>
      </div>

      <!-- Quick Action Buttons -->
      <div class="flex items-center gap-2.5">
        <button onclick="window.print()" class="flex-1 py-2.5 rounded-full bg-white hover:bg-gray-50 border border-gray-200/80 text-gray-700 text-xs font-semibold flex items-center justify-center gap-1.5 shadow-2xs transition">
          <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.04-.37-2.126-.37-3.243a7.5 7.5 0 0115 0c0 1.117-.13 2.203-.37 3.243m-14.26 0A7.5 7.5 0 0012 18.75m0 0a7.5 7.5 0 006.72-4.921M12 18.75V21m-4.5 0h9" />
          </svg>
          <span>Print / PDF</span>
        </button>
        <button onclick="copyShareUrl(document.getElementById('prevInvNumber').textContent)" class="flex-1 py-2.5 rounded-full bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-semibold flex items-center justify-center gap-1.5 shadow-sm transition">
          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
          </svg>
          <span>Copy Invoice URL</span>
        </button>
      </div>
    </div>

  </div>
</div>

<!-- ========================================================= -->
<!-- MODAL 4: QR Code Modal -->
<!-- ========================================================= -->
<div id="qrCodeModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-0 sm:p-4 hidden transition-opacity">
  <div class="bg-white w-full h-full sm:h-auto sm:max-w-sm sm:rounded-[28px] p-6 sm:p-8 flex flex-col justify-between items-center text-center shadow-2xl relative">
    
    <button onclick="closeQrModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition p-2">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>

    <div>
      <div class="w-10 h-10 rounded-full bg-sky-50 text-[#0b2545] flex items-center justify-center mx-auto mb-2 font-bold">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
        </svg>
      </div>
      <h3 class="text-lg font-bold text-gray-900" id="qrModalTitle">Scan to Pay</h3>
      <p class="text-xs text-gray-500 mt-0.5" id="qrModalAmount">$1,200.00 USD</p>

      <!-- SVG QR Code Canvas Simulation -->
      <div class="mt-5 p-4 bg-white rounded-2xl border-2 border-gray-100 inline-block shadow-xs">
        <svg class="w-48 h-48 mx-auto" viewBox="0 0 100 100" fill="currentColor">
          <!-- Outer border markers -->
          <path d="M5 5h30v30H5zM10 10v20h20V10zM15 15h10v10H15zM65 5h30v30H65zM70 10v20h20V10zM75 15h10v10H75zM5 65h30v30H5zM10 70v20h20V70zM15 75h10v10H15z" fill="#0b2545"/>
          <!-- Random Matrix Bits -->
          <rect x="42" y="10" width="6" height="6" fill="#0b2545"/>
          <rect x="52" y="10" width="6" height="6" fill="#0b2545"/>
          <rect x="42" y="24" width="6" height="12" fill="#0b2545"/>
          <rect x="52" y="20" width="6" height="6" fill="#0b2545"/>
          <rect x="10" y="42" width="6" height="6" fill="#0b2545"/>
          <rect x="24" y="42" width="12" height="6" fill="#0b2545"/>
          <rect x="10" y="52" width="6" height="6" fill="#0b2545"/>
          <rect x="20" y="52" width="6" height="6" fill="#0b2545"/>
          <rect x="42" y="42" width="16" height="16" fill="#0b2545"/>
          <rect x="65" y="42" width="6" height="6" fill="#0b2545"/>
          <rect x="75" y="42" width="6" height="6" fill="#0b2545"/>
          <rect x="85" y="42" width="6" height="12" fill="#0b2545"/>
          <rect x="65" y="52" width="12" height="6" fill="#0b2545"/>
          <rect x="42" y="65" width="6" height="12" fill="#0b2545"/>
          <rect x="52" y="75" width="6" height="6" fill="#0b2545"/>
          <rect x="65" y="65" width="6" height="6" fill="#0b2545"/>
          <rect x="75" y="70" width="6" height="12" fill="#0b2545"/>
          <rect x="85" y="80" width="6" height="10" fill="#0b2545"/>
          <rect x="42" y="85" width="16" height="6" fill="#0b2545"/>
        </svg>
      </div>

      <div class="mt-4 text-[11px] font-mono text-gray-500 break-all px-2" id="qrModalUrl">
        https://pay.flowwithlit.io/c/LNK-8830192
      </div>

      <div class="mt-5 w-full">
        <button onclick="copyShareUrl(document.getElementById('qrModalUrl').textContent)" class="w-full py-2.5 rounded-full bg-[#0b2545] text-white text-xs font-semibold hover:bg-[#132847] transition shadow-xs">
          Copy Link URL
        </button>
      </div>
    </div>

  </div>
</div>

<!-- ========================================================= -->
<!-- TOAST NOTIFICATION: Floating Pill for Copy feedback -->
<!-- ========================================================= -->
<div id="floatingToast" class="fixed bottom-6 right-6 z-50 bg-[#0b2545] text-white px-4 py-2.5 rounded-full text-xs font-semibold shadow-xl flex items-center gap-2 transform translate-y-12 opacity-0 transition-all duration-200 pointer-events-none">
  <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
  </svg>
  <span id="toastMessage">Link copied to clipboard!</span>
</div>

<?php
require_once __DIR__ . '/includes/bottom_nav.php';
?>

  <!-- Javascript Logic for Filter, Modals, Dynamic Line Items, Copy Toast -->
  <script>
    // Copy To Clipboard with Floating Toast
    function copyShareUrl(url) {
      navigator.clipboard.writeText(url).then(() => {
        showToast('Link copied to clipboard!');
      }).catch(() => {
        showToast('Copied: ' + url);
      });
    }

    function showToast(msg) {
      const toast = document.getElementById('floatingToast');
      const toastMsg = document.getElementById('toastMessage');
      toastMsg.textContent = msg;
      toast.classList.remove('translate-y-12', 'opacity-0');
      toast.classList.add('translate-y-0', 'opacity-100');
      setTimeout(() => {
        toast.classList.remove('translate-y-0', 'opacity-100');
        toast.classList.add('translate-y-12', 'opacity-0');
      }, 2500);
    }

    // Filter Tabs Handler
    const filterTabs = document.querySelectorAll('.filter-tab');
    const tableRows = document.querySelectorAll('#ledgerTableBody tr');
    const searchInput = document.getElementById('searchInput');
    const currencyFilter = document.getElementById('currencyFilter');
    const visibleCountEl = document.getElementById('visibleCount');

    let currentFilter = 'all';

    filterTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        filterTabs.forEach(t => {
          t.classList.remove('bg-[#0b2545]', 'text-white', 'shadow-2xs');
          t.classList.add('bg-gray-100', 'text-gray-600');
        });
        tab.classList.remove('bg-gray-100', 'text-gray-600');
        tab.classList.add('bg-[#0b2545]', 'text-white', 'shadow-2xs');
        
        currentFilter = tab.getAttribute('data-filter');
        applyFilters();
      });
    });

    searchInput.addEventListener('input', applyFilters);
    currencyFilter.addEventListener('change', applyFilters);

    function applyFilters() {
      const searchVal = searchInput.value.toLowerCase().trim();
      const currVal = currencyFilter.value;
      let count = 0;

      tableRows.forEach(row => {
        const type = row.getAttribute('data-type');
        const status = row.getAttribute('data-status');
        const currency = row.getAttribute('data-currency');
        const text = row.innerText.toLowerCase();

        let matchesTab = false;
        if (currentFilter === 'all') matchesTab = true;
        else if (currentFilter === 'link' && type === 'link') matchesTab = true;
        else if (currentFilter === 'invoice' && type === 'invoice') matchesTab = true;
        else if (currentFilter === 'paid' && status === 'paid') matchesTab = true;
        else if (currentFilter === 'pending' && status === 'pending') matchesTab = true;
        else if (currentFilter === 'expired' && status === 'expired') matchesTab = true;

        const matchesSearch = !searchVal || text.includes(searchVal);
        const matchesCurrency = currVal === 'all' || currency === currVal;

        if (matchesTab && matchesSearch && matchesCurrency) {
          row.classList.remove('hidden');
          count++;
        } else {
          row.classList.add('hidden');
        }
      });

      if (visibleCountEl) visibleCountEl.textContent = count;
    }

    // Modal 1: Create Link Modal
    const createLinkModal = document.getElementById('createLinkModal');
    const openLinkModalBtn = document.getElementById('openLinkModalBtn');
    const closeLinkModalBtn = document.getElementById('closeLinkModalBtn');
    const linkCurrency = document.getElementById('linkCurrency');
    const currencyPrefix = document.getElementById('currencyPrefix');

    openLinkModalBtn.addEventListener('click', () => {
      document.getElementById('linkGeneratedBox').classList.add('hidden');
      createLinkModal.classList.remove('hidden');
    });
    closeLinkModalBtn.addEventListener('click', () => {
      createLinkModal.classList.add('hidden');
    });

    linkCurrency.addEventListener('change', (e) => {
      const symbols = { USD: '$', EUR: '€', GBP: '£', CAD: '$', NGN: '₦', USDT: '₮' };
      currencyPrefix.textContent = symbols[e.target.value] || '$';
    });

    function copyGeneratedLink() {
      const url = document.getElementById('generatedLinkUrl').value;
      copyShareUrl(url);
    }

    // Modal 2: Create Invoice Modal
    const createInvoiceModal = document.getElementById('createInvoiceModal');
    const openInvoiceModalBtn = document.getElementById('openInvoiceModalBtn');
    const closeInvoiceModalBtn = document.getElementById('closeInvoiceModalBtn');

    const invoiceDrawerPanel = document.getElementById('invoiceDrawerPanel');
    openInvoiceModalBtn.addEventListener('click', () => {
      createInvoiceModal.classList.remove('hidden');
      requestAnimationFrame(() => invoiceDrawerPanel.classList.remove('translate-x-full'));
    });
    function closeInvoiceDrawer() {
      invoiceDrawerPanel.classList.add('translate-x-full');
      setTimeout(() => createInvoiceModal.classList.add('hidden'), 300);
    }
    closeInvoiceModalBtn.addEventListener('click', closeInvoiceDrawer);
    createInvoiceModal.addEventListener('click', event => { if (event.target === createInvoiceModal) closeInvoiceDrawer(); });

    function addLineItem() {
      const container = document.getElementById('lineItemsContainer');
      const row = document.createElement('div');
      row.className = 'flex items-center gap-2 line-item-row';
      row.innerHTML = `
        <input type="text" placeholder="Description of service or item" class="flex-1 px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 line-desc focus:outline-hidden focus:border-[#0b2545]" required />
        <input type="number" min="1" value="1" placeholder="Qty" class="w-16 px-2.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 line-qty text-center focus:outline-hidden focus:border-[#0b2545]" onchange="calcInvoiceTotal()" />
        <input type="number" step="0.01" min="0" value="500.00" placeholder="Price" class="w-24 px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 line-price text-right font-bold focus:outline-hidden focus:border-[#0b2545]" onchange="calcInvoiceTotal()" />
        <button type="button" onclick="removeLineItem(this)" class="p-2 text-gray-400 hover:text-rose-600 transition">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      `;
      container.appendChild(row);
      calcInvoiceTotal();
    }

    function removeLineItem(btn) {
      const rows = document.querySelectorAll('.line-item-row');
      if (rows.length > 1) {
        btn.closest('.line-item-row').remove();
        calcInvoiceTotal();
      } else {
        alert('An invoice must contain at least one line item.');
      }
    }

    function calcInvoiceTotal() {
      const rows = document.querySelectorAll('.line-item-row');
      let total = 0;
      rows.forEach(r => {
        const qty = parseFloat(r.querySelector('.line-qty').value) || 0;
        const price = parseFloat(r.querySelector('.line-price').value) || 0;
        total += (qty * price);
      });
      const formatted = '$' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      document.getElementById('invSubtotal').textContent = formatted;
      document.getElementById('invTotalAmount').textContent = formatted;
    }

    function submitInvoiceForm() {
      const client = document.getElementById('invClientName').value;
      const num = document.getElementById('invoiceNumberInput').value;
      closeInvoiceDrawer();
      showToast(`Invoice ${num} successfully issued to ${client}!`);
    }

    // Modal 3: Invoice Preview Modal
    const invoicePreviewModal = document.getElementById('invoicePreviewModal');
    function previewInvoice(number, client, amount, issueDate, status, title) {
      document.getElementById('prevInvNumber').textContent = number;
      document.getElementById('prevClientName').textContent = client;
      document.getElementById('prevTotalAmount').textContent = amount;
      document.getElementById('prevIssueDate').textContent = issueDate;
      document.getElementById('prevItemTitle').textContent = title;
      document.getElementById('prevInvStatusText').textContent = status;
      invoicePreviewModal.classList.remove('hidden');
    }
    function closeInvoicePreview() {
      invoicePreviewModal.classList.add('hidden');
    }

    // Modal 4: QR Code Modal
    const qrCodeModal = document.getElementById('qrCodeModal');
    function showQrModal(url, title, amount) {
      document.getElementById('qrModalTitle').textContent = title;
      document.getElementById('qrModalAmount').textContent = amount;
      document.getElementById('qrModalUrl').textContent = url;
      qrCodeModal.classList.remove('hidden');
    }
    function closeQrModal() {
      qrCodeModal.classList.add('hidden');
    }
  </script>

<script>
document.getElementById('currencyFilter').innerHTML='<option value="all">NGN + Crypto</option><option value="NGN">NGN (₦)</option><option value="USDT">USDT (₮)</option><option value="USDC">USDC ($)</option><option value="BTC">BTC (₿)</option><option value="ETH">ETH (Ξ)</option><option value="SOL">SOL (◎)</option>';
if(document.getElementById('linkCurrency'))document.getElementById('linkCurrency').innerHTML='<option value="NGN">NGN (₦)</option><option value="USDT">USDT (₮)</option><option value="USDC">USDC ($)</option><option value="BTC">BTC (₿)</option><option value="ETH">ETH (Ξ)</option><option value="SOL">SOL (◎)</option>';
async function loadPaymentLinks(){const body=document.getElementById('ledgerTableBody');try{const token=localStorage.getItem('flow_token')||'';const r=await fetch('../api/payment-links/list.php',{credentials:'same-origin',headers:{Authorization:`Bearer ${token}`,Accept:'application/json'},cache:'no-store'}),d=await r.json();if(!r.ok||!d.success)throw new Error(d.error||'Unable to load payment links');body.innerHTML=d.links.length?'':'<tr><td colspan="5" class="py-16 text-center text-sm text-gray-400">No payment links created yet.</td></tr>';d.links.forEach(x=>{const tr=document.createElement('tr');tr.dataset.type='link';tr.dataset.status=x.status==='active'?'pending':x.status;tr.dataset.currency=x.currency_code;tr.innerHTML=`<td class="py-5 px-6 font-semibold">${x.title}</td><td class="py-5 px-6 font-mono text-[11px]">${x.public_token}</td><td class="py-5 px-6">${x.amount===null?'Open amount':Number(x.amount).toLocaleString()} ${x.currency_code}</td><td class="py-5 px-6">${x.status}</td><td class="py-5 px-6 text-[11px]">${x.expires_at||'No expiry'}</td>`;body.appendChild(tr)});body.style.visibility='visible';window.paymentLinksLoaded=true}catch(e){console.error(e);body.innerHTML='<tr><td colspan="5" class="py-16 text-center text-sm text-gray-400">Payment links are unavailable.</td></tr>';body.style.visibility='visible'}}
async function submitCreateLink(){const title=document.getElementById('linkTitle').value.trim(),amount=document.getElementById('linkAmount').value||null,currency=document.getElementById('linkCurrency').value,returnUrl=document.getElementById('linkRedirect').value.trim(),button=document.getElementById('btnSubmitCreateLink');if(!title){alert('Enter a link title.');return}button.disabled=true;button.classList.add('opacity-70','cursor-wait');button.innerHTML='<span class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-white/35 border-t-white align-[-3px]"></span><span>Generating link…</span>';try{const token=localStorage.getItem('flow_token')||'';const r=await fetch('../api/payment-links/create.php',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json',Authorization:`Bearer ${token}`},body:JSON.stringify({title,amount,currency,return_url:returnUrl,description:document.getElementById('linkDescription')?.value||''})}),d=await r.json();if(!r.ok||!d.success)throw new Error(d.error||'Unable to create link');const generated=/^https?:\/\//i.test(d.url)?d.url:location.origin+d.url;document.getElementById('generatedLinkUrl').value=generated;document.getElementById('linkCreateFormView').classList.add('hidden');document.getElementById('linkSuccessView').classList.remove('hidden');loadPaymentLinks();showToast('Checkout link created.')}catch(e){alert(e.message);button.disabled=false;button.classList.remove('opacity-70','cursor-wait');button.innerHTML='Generate Checkout Link'}}
document.getElementById('createAnotherLinkBtn')?.addEventListener('click',()=>{document.getElementById('linkSuccessView').classList.add('hidden');document.getElementById('linkCreateFormView').classList.remove('hidden');const b=document.getElementById('btnSubmitCreateLink');b.disabled=false;b.classList.remove('opacity-70','cursor-wait');b.innerHTML='Generate Checkout Link';document.getElementById('createLinkForm')?.reset();});
document.getElementById('closeLinkModalBtn')?.addEventListener('click',()=>{document.getElementById('linkSuccessView')?.classList.add('hidden');document.getElementById('linkCreateFormView')?.classList.remove('hidden');});
</script>
<script>
(() => {
  fetch('../api/payment-links/list.php', {headers:{Authorization:`Bearer ${localStorage.getItem('flow_token') || ''}`,Accept:'application/json'},cache:'no-store'})
    .then(response => response.json()).then(result => {
      if (!result.success) return;
      const links=result.links||[], counts={all:links.length,link:links.filter(item=>item.type==='link').length,invoice:links.filter(item=>item.type==='invoice').length,paid:links.filter(item=>item.status==='paid').length,pending:links.filter(item=>item.status==='active'||item.status==='pending').length,expired:links.filter(item=>item.status==='expired').length};
      Object.entries(counts).forEach(([key,value])=>{const el=document.querySelector(`[data-link-count="${key}"]`);if(el)el.textContent=value;});
    }).catch(()=>{});
})();
</script>
<script>
(() => {
  const body=document.getElementById('ledgerTableBody');
  const esc=v=>String(v??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
  const token=()=>localStorage.getItem('flow_token')||'';
  const checkoutUrl=x=>/^https?:\/\//i.test(x)?x:location.origin+(location.pathname.split('/app/')[0]||'')+x;
  window.loadPaymentLinksWithActions=async()=>{try{const r=await fetch('../api/payment-links/list.php',{headers:{Authorization:`Bearer ${token()}`,Accept:'application/json'},cache:'no-store'}),d=await r.json();if(!r.ok||!d.success)throw Error(d.error||'Unable to load payment links');const links=d.links||[];body.innerHTML=links.length?links.map(x=>{const status=x.status==='active'?'Active':x.status==='disabled'?'Disabled':'Expired',badge=x.status==='active'?'bg-emerald-50 text-emerald-700 border-emerald-200':'bg-gray-100 text-gray-500 border-gray-200';return `<tr class="hover:bg-gray-50/70 transition" data-type="link" data-status="${esc(x.status==='active'?'pending':x.status)}" data-currency="${esc(x.currency_code)}"><td class="py-4 px-4"><div class="font-bold text-gray-900">${esc(x.title)}</div><div class="text-[10px] text-gray-400 font-mono">${esc(x.public_token)}</div></td><td class="py-4 px-4"><div class="font-medium text-gray-700">Open to customers</div><div class="text-[10px] text-gray-400">Hosted checkout</div></td><td class="py-4 px-4"><span class="text-[11px] font-semibold text-gray-700">Payment link</span></td><td class="py-4 px-4 text-right"><div class="font-bold text-gray-900">${x.amount===null?'Open amount':esc(Number(x.amount).toLocaleString())+' '+esc(x.currency_code)}</div><div class="text-[10px] text-gray-400">${esc(x.currency_code)}</div></td><td class="py-4 px-4 text-center"><span class="inline-flex rounded-full border px-2.5 py-1 text-[10px] font-bold ${badge}">${status}</span></td><td class="py-4 px-4"><div class="text-gray-700">${esc(x.created_at||'—')}</div><div class="text-[10px] text-gray-400">${x.expires_at?'Expires '+esc(x.expires_at):'No expiry'}</div></td><td class="py-4 px-4 text-right"><div class="flex justify-end gap-1"><button type="button" data-link-action="open" data-url="${esc(checkoutUrl('/checkout/'+x.public_token))}" class="rounded-lg p-2 text-gray-500 hover:bg-sky-50 hover:text-sky-700" title="Open checkout">↗</button><button type="button" data-link-action="copy" data-url="${esc(checkoutUrl('/checkout/'+x.public_token))}" class="rounded-lg p-2 text-gray-500 hover:bg-sky-50 hover:text-sky-700" title="Copy link">⧉</button><button type="button" data-link-action="edit" data-token="${esc(x.public_token)}" data-title="${esc(x.title)}" data-amount="${esc(x.amount||'')}" class="rounded-lg p-2 text-gray-500 hover:bg-amber-50 hover:text-amber-700" title="Edit link">✎</button><button type="button" data-link-action="delete" data-token="${esc(x.public_token)}" class="rounded-lg p-2 text-gray-500 hover:bg-rose-50 hover:text-rose-700" title="Disable link">×</button></div></td></tr>`}).join(''):'<tr><td colspan="7" class="py-16 text-center text-sm text-gray-400">No payment links created yet.</td></tr>';body.style.visibility='visible';}catch(e){body.innerHTML='<tr><td colspan="7" class="py-16 text-center text-sm text-amber-700">Payment links are unavailable.</td></tr>';body.style.visibility='visible';}};
  body.addEventListener('click',async event=>{const button=event.target.closest('[data-link-action]');if(!button)return;const action=button.dataset.linkAction;if(action==='open')window.open(button.dataset.url,'_blank','noopener,noreferrer');if(action==='copy')copyShareUrl(button.dataset.url);if(action==='edit'){window.editingPaymentLinkToken=button.dataset.token;document.getElementById('linkTitle').value=button.dataset.title||'';document.getElementById('linkAmount').value=button.dataset.amount||'';document.getElementById('createLinkModal').classList.remove('hidden');}if(action==='delete'&&confirm('Disable this payment link?')){const r=await fetch('../api/payment-links/delete.php',{method:'POST',headers:{'Content-Type':'application/json',Authorization:`Bearer ${token()}`},body:JSON.stringify({public_token:button.dataset.token})});const d=await r.json();if(!r.ok||!d.success){alert(d.error||'Unable to disable link');return}showToast('Payment link disabled.');loadPaymentLinksWithActions();}});
  window.loadPaymentLinks=window.loadPaymentLinksWithActions;
  loadPaymentLinksWithActions();
})();
</script>
<script>
// The live table uses the same form for creating and editing a payment link.
const originalSubmitCreateLink=window.submitCreateLink;
window.submitCreateLink=async function(){
  if(!window.editingPaymentLinkToken){return originalSubmitCreateLink();}
  const button=document.getElementById('btnSubmitCreateLink'); button.disabled=true; button.innerHTML='<span class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-white/35 border-t-white align-[-3px]"></span><span>Saving changes…</span>';
  try{const r=await fetch('../api/payment-links/update.php',{method:'POST',headers:{'Content-Type':'application/json',Authorization:`Bearer ${localStorage.getItem('flow_token')||''}`},body:JSON.stringify({public_token:window.editingPaymentLinkToken,title:document.getElementById('linkTitle').value.trim(),amount:document.getElementById('linkAmount').value||null,return_url:document.getElementById('linkRedirect').value.trim()})}),d=await r.json();if(!r.ok||!d.success)throw Error(d.error||'Unable to update link');window.editingPaymentLinkToken=null;document.getElementById('createLinkModal').classList.add('hidden');button.disabled=false;button.innerHTML='Generate Checkout Link';showToast('Payment link updated.');window.loadPaymentLinksWithActions?.();}catch(e){alert(e.message);button.disabled=false;button.innerHTML='Save changes';}
};
</script>
<script>
(() => {
  const table=document.getElementById('ledgerTableBody');
  const icon=(name)=>`<i data-lucide="${name}" class="h-4 w-4"></i>`;
  const menus=()=>{table.querySelectorAll('tr').forEach(row=>{if(row.dataset.menuReady||!row.querySelector('[data-link-action]'))return;const cell=row.lastElementChild,buttons=[...cell.querySelectorAll('[data-link-action]')],open=buttons.find(b=>b.dataset.linkAction==='open'),copy=buttons.find(b=>b.dataset.linkAction==='copy'),edit=buttons.find(b=>b.dataset.linkAction==='edit'),del=buttons.find(b=>b.dataset.linkAction==='delete');if(!open||!copy||!edit||!del)return;row.dataset.menuReady='1';cell.innerHTML=`<div class="relative inline-flex justify-end"><button type="button" data-link-menu-trigger class="grid h-9 w-9 place-items-center rounded-xl text-gray-500 hover:bg-gray-100 hover:text-[#0b2545]" aria-label="Payment link actions" aria-expanded="false">${icon('more-horizontal')}</button><div data-link-menu class="absolute right-0 top-10 z-40 hidden w-44 overflow-hidden rounded-xl border border-gray-200 bg-white py-1 text-left shadow-xl"><button type="button" data-link-action="open" data-url="${open.dataset.url}" class="flex w-full items-center gap-2 px-3 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">${icon('external-link')}<span>Open checkout</span></button><button type="button" data-link-action="copy" data-url="${copy.dataset.url}" class="flex w-full items-center gap-2 px-3 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">${icon('copy')}<span>Copy link</span></button><button type="button" data-link-action="edit" data-token="${edit.dataset.token}" data-title="${edit.dataset.title}" data-amount="${edit.dataset.amount||''}" class="flex w-full items-center gap-2 px-3 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">${icon('pencil')}<span>Edit link</span></button><button type="button" data-link-action="delete" data-token="${del.dataset.token}" class="flex w-full items-center gap-2 px-3 py-2.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">${icon('trash-2')}<span>Disable link</span></button></div></div>`;});window.lucide?.createIcons?.();};
  table.addEventListener('click',event=>{const trigger=event.target.closest('[data-link-menu-trigger]');if(trigger){event.stopPropagation();const menu=trigger.parentElement.querySelector('[data-link-menu]');table.querySelectorAll('[data-link-menu]').forEach(m=>{if(m!==menu)m.classList.add('hidden')});menu.classList.toggle('hidden');trigger.setAttribute('aria-expanded',String(!menu.classList.contains('hidden')));}});document.addEventListener('click',event=>{if(!event.target.closest('[data-link-menu]')&&!event.target.closest('[data-link-menu-trigger]'))table.querySelectorAll('[data-link-menu]').forEach(m=>m.classList.add('hidden'));});
  table.addEventListener('click',event=>{const trigger=event.target.closest('[data-link-menu-trigger]');if(!trigger)return;const menu=trigger.parentElement.querySelector('[data-link-menu]');if(!menu||menu.classList.contains('hidden'))return;const rect=trigger.getBoundingClientRect(),menuWidth=176,gap=8;menu.style.position='fixed';menu.style.zIndex='100';menu.style.right='auto';menu.style.left=Math.max(8,Math.min(rect.right-menuWidth,window.innerWidth-menuWidth-8))+'px';menu.style.top=(rect.bottom+gap)+'px';requestAnimationFrame(()=>{if(menu.getBoundingClientRect().bottom>window.innerHeight-8)menu.style.top=Math.max(8,rect.top-menu.offsetHeight-gap)+'px';});});
  // Watch only rows being replaced by the live loader. Watching the whole subtree
  // also observes Lucide replacing <i> nodes with SVGs and can create an endless
  // mutation/render loop that freezes the page.
  const observer=new MutationObserver(menus);observer.observe(table,{childList:true});menus();
  const syncLiveCounts=()=>{const rows=[...table.querySelectorAll('tr[data-type="link"]')],active=rows.filter(row=>row.dataset.status==='pending').length,visible=rows.filter(row=>!row.classList.contains('hidden')&&row.style.display!=='none').length;const badge=document.getElementById('activeCollectsBadge'),shown=document.getElementById('visibleCount'),total=document.getElementById('totalLinkCount');if(badge)badge.textContent=`${active} Active Collect${active===1?'':'s'}`;if(shown)shown.textContent=String(visible);if(total)total.textContent=String(rows.length);};
  const countObserver=new MutationObserver(syncLiveCounts);countObserver.observe(table,{childList:true});syncLiveCounts();
  const shortenReferences=()=>{table.querySelectorAll('tr[data-type="link"] td:first-child .font-mono').forEach(reference=>{const value=reference.textContent.trim();if(value.length>15&&!value.endsWith('…'))reference.textContent=value.slice(0,12)+'…';});};
  const referenceObserver=new MutationObserver(shortenReferences);referenceObserver.observe(table,{childList:true});shortenReferences();
  const applyLiveFilters=()=>{const query=(document.getElementById('searchInput')?.value||'').toLowerCase().trim(),currency=document.getElementById('currencyFilter')?.value||'all',filter=typeof currentFilter==='string'?currentFilter:'all';table.querySelectorAll('tr[data-type="link"]').forEach(row=>{const status=row.dataset.status,type=row.dataset.type,text=row.textContent.toLowerCase(),tabMatch=filter==='all'||filter===type||(filter==='pending'&&status==='pending')||(filter==='expired'&&status==='expired')||(filter==='paid'&&status==='paid'),currencyMatch=currency==='all'||row.dataset.currency===currency,searchMatch=!query||text.includes(query);row.style.display=tabMatch&&currencyMatch&&searchMatch?'':'none';});syncLiveCounts();};
  document.querySelectorAll('.filter-tab').forEach(tab=>tab.addEventListener('click',()=>setTimeout(applyLiveFilters,0)));document.getElementById('searchInput')?.addEventListener('input',applyLiveFilters);document.getElementById('currencyFilter')?.addEventListener('change',applyLiveFilters);
})();
</script>
<?php
require_once __DIR__ . '/includes/footer.php';
?>
