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
      
      <!-- Greeting + primary actions. Desktop: actions sit in the header (no extra card height).
           Mobile: header is just the greeting; actions sit under the swipe cards. -->
      <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between flex-shrink-0">
        <div>
          <p id="dashboardDate" class="text-xs text-gray-400 font-medium tracking-wide"></p>
          <h1 id="dashboardGreeting" class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight mt-0.5">Welcome back</h1>
        </div>
        <nav class="hidden items-center gap-2 md:flex" aria-label="Quick actions">
          <a href="add-money.php" class="fwl-action fwl-action--primary"><i data-lucide="plus" class="h-4 w-4"></i>Add money</a>
          <a href="send-money.php" class="fwl-action bg-white border-gray-200 text-gray-800"><i data-lucide="send" class="h-4 w-4"></i>Send</a>
          <a href="request-money.php" class="fwl-action bg-white border-gray-200 text-gray-800"><i data-lucide="hand-coins" class="h-4 w-4"></i>Request</a>
        </nav>
      </div>

      <!-- Row 1: 4 KPI cards. Mobile: swipe carousel (one per view) + dots + compact action bar.
           Tablet: 2 per row. Desktop (xl): 4 in a row. -->
      <link rel="stylesheet" href="assets/stat-cards.css?v=1">
      <section class="flex-shrink-0" aria-label="Account summary">
        <div id="statsTrack" class="stats-track -mx-4 flex snap-x snap-mandatory gap-3 overflow-x-auto px-4 md:mx-0 md:grid md:grid-cols-2 md:gap-5 md:overflow-visible md:px-0 xl:grid-cols-4 lg:gap-6">

          <!-- Card 1: Total balance (hero card) -->
          <div class="stat-card fwl-hero relative flex w-full shrink-0 snap-center flex-col rounded-[24px] p-5 md:w-auto" aria-roledescription="slide" aria-label="Total balance, 1 of 4">
            <div class="relative z-10 flex items-center justify-between gap-2">
              <div class="flex items-center gap-2">
                <span class="fwl-hero-chip"><i data-lucide="wallet" class="h-4 w-4"></i></span>
                <span class="whitespace-nowrap text-sm font-semibold text-white/80">Total balance</span>
              </div>

              <!-- Currency picker -->
              <div class="relative" id="currencyDropdownWrapper">
                <button id="currencyBtn" class="fwl-hero-pill" aria-expanded="false" aria-haspopup="true">
                  <span id="currencyFlagContainer" class="w-4 h-4 rounded-full overflow-hidden inline-flex items-center justify-center flex-shrink-0">
                    <svg viewBox="0 0 640 480" class="w-full h-full object-cover"><path fill="#008751" d="M0 0h213.3v480H0zm426.7 0H640v480H426.7z"/><path fill="#ffffff" d="M213.3 0h213.4v480H213.3z"/></svg>
                  </span>
                  <span id="currencyText">NGN</span>
                  <svg id="currencyChevron" class="w-3 h-3 opacity-70 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="currencyDropdownMenu" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50 text-gray-900">
                  <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-400 border-b border-gray-100 pb-1.5 mb-1">Select currency</div>
                  <div class="max-h-60 overflow-y-auto space-y-0.5 px-1.5" id="currencyOptionsList"></div>
                </div>
              </div>
            </div>

            <div class="relative z-10 mt-5">
              <div class="flex items-center gap-2">
                <span id="totalBalanceVal" class="stat-amount text-[2rem] font-extrabold leading-tight tracking-tight text-white">₦0.00</span>
                <button id="toggleBalanceBtn" class="grid h-8 w-8 shrink-0 place-items-center rounded-full text-white/70 transition hover:bg-white/10 hover:text-white active:scale-95" title="Show or hide balance" aria-label="Show or hide balance">
                  <svg id="eyeIcon" class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                </button>
              </div>
              <p id="balanceCaption" class="mt-1.5 text-xs font-medium text-white/60">Available to spend</p>
            </div>
          </div>

          <!-- Card 2: Money in -->
          <div class="stat-card relative flex w-full shrink-0 snap-center flex-col rounded-[24px] border border-gray-200/80 bg-white p-5 shadow-sm md:w-auto" aria-roledescription="slide" aria-label="Money in, 2 of 4">
            <div class="flex items-center justify-between gap-2">
              <div class="flex items-center gap-2.5"><span class="fwl-chip fwl-chip--green"><i data-lucide="arrow-down-left" class="h-4 w-4"></i></span><span class="text-sm font-semibold text-gray-700">Money in</span></div>
              <span class="fwl-tag">This month</span>
            </div>
            <div class="mt-4">
              <div id="moneyInVal" class="stat-amount text-[1.6rem] font-extrabold leading-tight tracking-tight text-gray-900">₦0.00</div>
              <p id="moneyInTrend" class="mt-1 text-xs text-gray-500">Deposits and payments you receive</p>
            </div>
            <div id="moneyInViz" class="mt-auto pt-4" data-empty-href="request-money.php" data-empty-text="Get paid with your Flow Tag"></div>
          </div>

          <!-- Card 3: Money out (monthly spending) -->
          <div class="stat-card relative flex w-full shrink-0 snap-center flex-col rounded-[24px] border border-gray-200/80 bg-white p-5 shadow-sm md:w-auto" aria-roledescription="slide" aria-label="Money out, 3 of 4">
            <div class="flex items-center justify-between gap-2">
              <div class="flex items-center gap-2.5"><span class="fwl-chip fwl-chip--blue"><i data-lucide="arrow-up-right" class="h-4 w-4"></i></span><span class="text-sm font-semibold text-gray-700">Money out</span></div>
              <span class="fwl-tag">This month</span>
            </div>
            <div class="mt-4">
              <div id="monthlySpendingVal" class="stat-amount text-[1.6rem] font-extrabold leading-tight tracking-tight text-gray-900">₦0.00</div>
              <p id="monthlySpendingTrend" class="mt-1 text-xs text-gray-500">Transfers, payments and withdrawals</p>
            </div>
            <div id="moneyOutViz" class="mt-auto pt-4" data-empty-href="send-money.php" data-empty-text="Make your first transfer"></div>
          </div>

          <!-- Card 4: Card spending -->
          <div class="stat-card relative flex w-full shrink-0 snap-center flex-col rounded-[24px] border border-gray-200/80 bg-white p-5 shadow-sm md:w-auto" aria-roledescription="slide" aria-label="Card spending, 4 of 4">
            <div class="flex items-center justify-between gap-2">
              <div class="flex items-center gap-2.5"><span class="fwl-chip fwl-chip--rose"><i data-lucide="credit-card" class="h-4 w-4"></i></span><span class="text-sm font-semibold text-gray-700">Card spending</span></div>
              <span class="fwl-tag">This month</span>
            </div>
            <div class="mt-4">
              <div id="cardUsageVal" class="stat-amount text-[1.6rem] font-extrabold leading-tight tracking-tight text-gray-900">₦0.00</div>
              <p id="cardUsageTrend" class="mt-1 text-xs text-gray-500">Paid with your Flowwithlit cards</p>
            </div>
            <div id="cardViz" class="mt-auto pt-4" data-empty-href="cards.php" data-empty-text="Get a virtual card"></div>
          </div>
        </div>

        <!-- Mobile: carousel dots -->
        <div id="statsDots" class="mt-3 flex items-center justify-center gap-2 md:hidden" role="tablist" aria-label="Summary cards">
          <button type="button" class="stats-dot is-active" role="tab" aria-selected="true" aria-label="Total balance"></button>
          <button type="button" class="stats-dot" role="tab" aria-selected="false" aria-label="Money in"></button>
          <button type="button" class="stats-dot" role="tab" aria-selected="false" aria-label="Money out"></button>
          <button type="button" class="stats-dot" role="tab" aria-selected="false" aria-label="Card spending"></button>
        </div>

        <!-- Mobile: compact action bar -->
        <nav class="mt-4 grid grid-cols-3 gap-2 md:hidden" aria-label="Quick actions">
          <a href="add-money.php" class="fwl-action fwl-action--primary justify-center"><i data-lucide="plus" class="h-4 w-4"></i>Add</a>
          <a href="send-money.php" class="fwl-action justify-center bg-white border-gray-200 text-gray-800"><i data-lucide="send" class="h-4 w-4"></i>Send</a>
          <a href="request-money.php" class="fwl-action justify-center bg-white border-gray-200 text-gray-800"><i data-lucide="hand-coins" class="h-4 w-4"></i>Request</a>
        </nav>

        <style>
          .stats-track { scroll-padding-inline: 1rem; scrollbar-width: none; -webkit-overflow-scrolling: touch; overscroll-behavior-x: contain; }
          .stats-track::-webkit-scrollbar { display: none; }
          @media (min-width: 768px) { .stats-track { scroll-padding-inline: 0; } }

          /* Hero balance card */
          .fwl-hero { overflow: visible; color: #fff; isolation: isolate;
            background: radial-gradient(120% 140% at 100% 0%, rgba(201,164,74,.28) 0%, rgba(201,164,74,0) 45%), linear-gradient(145deg, #13355d 0%, #0b2545 55%, #071930 100%);
            box-shadow: 0 18px 40px -22px rgba(11,37,69,.75); }
          .fwl-hero::after { content: ""; position: absolute; inset: 0; border-radius: inherit; pointer-events: none; z-index: 0;
            background: radial-gradient(circle at 92% 108%, rgba(255,255,255,.08) 0 28%, transparent 28.5%), radial-gradient(circle at 104% 70%, rgba(255,255,255,.05) 0 22%, transparent 22.5%); }
          .dark .fwl-hero { background: radial-gradient(120% 140% at 100% 0%, rgba(201,164,74,.30) 0%, rgba(201,164,74,0) 45%), linear-gradient(145deg, #1a4675 0%, #10325a 55%, #0b2545 100%); box-shadow: 0 0 0 1px rgba(255,255,255,.06); }
          .fwl-hero-chip { display: grid; place-items: center; width: 2rem; height: 2rem; border-radius: .75rem; background: rgba(255,255,255,.12); color: #e9c97a; }
          .fwl-hero-pill { display: inline-flex; align-items: center; gap: .375rem; border-radius: 999px; padding: .375rem .75rem; font-size: .75rem; font-weight: 700; color: #fff; background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.18); transition: background .15s; }
          .fwl-hero-pill:hover { background: rgba(255,255,255,.2); }

          /* Stat cards */
          .fwl-bars { display: flex; align-items: flex-end; gap: .3rem; height: 2.75rem; }
          .fwl-bars span { flex: 1; min-height: 3px; border-radius: 4px 4px 2px 2px; background: #e2e8f0; }
          .fwl-bars span.is-now { background: var(--bar, #0b2545); }
          .dark .fwl-bars span { background: #1a324d; }
          .dark .fwl-bars span.is-now { background: var(--bar, #c9a44a); }
          .fwl-bar-labels { display: flex; gap: .3rem; margin-top: .35rem; font-size: .62rem; font-weight: 600; color: #94a3b8; } .fwl-bar-labels span { flex: 1; text-align: center; }

          /* Actions */
          .fwl-action { display: inline-flex; align-items: center; gap: .45rem; height: 2.5rem; padding: 0 1rem; border-radius: 999px; border-width: 1px; font-size: .8125rem; font-weight: 700; box-shadow: 0 1px 2px rgba(15,23,42,.06); transition: transform .15s, background .15s; }
          .fwl-action:hover { transform: translateY(-1px); } .fwl-action:active { transform: scale(.97); }
          .fwl-action--primary { background: #0b2545; border-color: #0b2545; color: #fff; } .fwl-action--primary:hover { background: #16355f; }
          .dark .fwl-action--primary { background: #c9a44a; border-color: #c9a44a; color: #0b2545; }
          @media (max-width: 767px) { .fwl-action { height: 2.75rem; border-radius: 1rem; } }

          .stats-dot { height: .5rem; width: .5rem; border-radius: 999px; background: #cbd5e1; transition: width .25s ease, background-color .25s ease; }
          .stats-dot.is-active { width: 1.5rem; background: #0b2545; }
          .dark .stats-dot { background: #334e6e; } .dark .stats-dot.is-active { background: #c9a44a; }
          @media (prefers-reduced-motion: reduce) { .stats-dot, .fwl-action { transition: none; } }
        </style>
        <script>
          (() => {
            const track = document.getElementById('statsTrack');
            const dots = [...document.querySelectorAll('#statsDots .stats-dot')];
            const cards = [...track.children];
            const mobile = window.matchMedia('(max-width: 767px)');
            const step = () => (cards[1]?.offsetLeft ?? 0) - cards[0].offsetLeft || track.clientWidth;
            let active = 0;
            const setActive = index => {
              if (index === active) return;
              active = index;
              dots.forEach((dot, i) => { dot.classList.toggle('is-active', i === index); dot.setAttribute('aria-selected', String(i === index)); });
            };
            const closeMenus = () => { const menu = document.getElementById('currencyDropdownMenu'); if (menu && !menu.classList.contains('hidden')) document.body.click(); };
            // Cheap enough to run on every scroll event; the dot moves as soon as a card is mostly in view.
            track.addEventListener('scroll', () => {
              if (!mobile.matches) return;
              setActive(Math.max(0, Math.min(cards.length - 1, Math.round(track.scrollLeft / step()))));
              closeMenus();
            }, { passive: true });
            dots.forEach((dot, i) => dot.addEventListener('click', () => track.scrollTo({ left: i * step(), behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' })));
            // The currency menu lives inside the swipe track; on mobile pin it to the viewport so the track doesn't clip it.
            const btn = document.getElementById('currencyBtn'), menu = document.getElementById('currencyDropdownMenu');
            btn?.addEventListener('click', () => setTimeout(() => {
              if (!mobile.matches || menu.classList.contains('hidden')) { menu.style.cssText = ''; return; }
              const r = btn.getBoundingClientRect(), w = menu.offsetWidth, gutter = 12;
              const left = Math.min(Math.max(gutter, r.right - w), window.innerWidth - w - gutter);
              Object.assign(menu.style, { position: 'fixed', top: `${r.bottom + 8}px`, left: `${Math.max(gutter, left)}px`, right: 'auto', marginTop: '0' });
            }, 0));
            mobile.addEventListener('change', () => { track.scrollLeft = 0; setActive(0); });

            // Card footers: real 6-month bars, a real share-of-spending meter, or a helpful empty-state link.
            const fmtMonth = key => new Date(`${key}-01T00:00:00`).toLocaleString('en', { month: 'short' }).slice(0, 1);
            const lastMonths = n => { const out = [], d = new Date(); d.setDate(1); for (let i = n - 1; i >= 0; i--) { const m = new Date(d.getFullYear(), d.getMonth() - i, 1); out.push(`${m.getFullYear()}-${String(m.getMonth() + 1).padStart(2, '0')}`); } return out; };
            const empty = el => {
              const a = document.createElement('a'); a.className = 'fwl-empty'; a.href = el.dataset.emptyHref;
              a.append(Object.assign(document.createElement('span'), { textContent: el.dataset.emptyText }), Object.assign(document.createElement('span'), { textContent: '→', ariaHidden: 'true' }));
              el.replaceChildren(a);
            };
            const bars = (el, series, color, label) => {
              const max = Math.max(...series.map(s => s.value));
              if (!(max > 0)) return empty(el);
              const wrap = document.createElement('div'); wrap.className = 'fwl-bars'; wrap.style.setProperty('--bar', color); wrap.setAttribute('role', 'img'); wrap.setAttribute('aria-label', `${label} for the last ${series.length} months`);
              const labels = document.createElement('div'); labels.className = 'fwl-bar-labels'; labels.setAttribute('aria-hidden', 'true');
              series.forEach((s, i) => {
                const bar = document.createElement('span'); bar.style.height = `${Math.max(6, s.value / max * 100)}%`; bar.title = `${s.key}: ${s.text}`;
                if (i === series.length - 1) bar.classList.add('is-now');
                wrap.append(bar); labels.append(Object.assign(document.createElement('span'), { textContent: fmtMonth(s.key) }));
              });
              el.replaceChildren(wrap, labels);
            };
            window.fwlRenderStatCards = (summary, money) => {
              const months = Object.fromEntries((summary.months || []).map(m => [m.month, m]));
              const series = key => lastMonths(6).map(k => ({ key: k, value: Number(months[k]?.[key] || 0), text: money(months[k]?.[key] || 0) }));
              bars(document.getElementById('moneyInViz'), series('earnings'), '#059669', 'Money in');
              bars(document.getElementById('moneyOutViz'), series('spending'), '#2563eb', 'Money out');
              const cardEl = document.getElementById('cardViz'), card = Number(summary.card_usage || 0), spent = Number(summary.monthly_spending || 0);
              if (card > 0 && spent > 0) {
                const pct = Math.min(100, Math.round(card / spent * 100));
                const meter = document.createElement('div'); meter.className = 'fwl-meter'; meter.setAttribute('role', 'meter'); meter.setAttribute('aria-valuenow', String(pct)); meter.setAttribute('aria-valuemin', '0'); meter.setAttribute('aria-valuemax', '100'); meter.setAttribute('aria-label', 'Share of this month’s spending paid by card');
                const fill = document.createElement('i'); fill.style.width = `${pct}%`; meter.append(fill);
                const note = Object.assign(document.createElement('p'), { className: 'mt-2 text-[11px] font-semibold text-gray-500', textContent: `${pct}% of this month’s money out` });
                cardEl.replaceChildren(meter, note);
              } else empty(cardEl);
            };
            ['moneyInViz', 'moneyOutViz', 'cardViz'].forEach(id => empty(document.getElementById(id)));
          })();
        </script>
      </section>

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

      <!-- Row 3: Recent transactions (table on tablet/desktop, stacked rows on phones) -->
      <section class="rounded-[24px] border border-gray-200/80 bg-white shadow-sm dark:border-gray-700 dark:bg-[#0c1f36] flex-shrink-0" aria-labelledby="recentTxTitle">
        <div class="flex items-center justify-between gap-3 px-5 pt-5 pb-3 sm:px-6">
          <div>
            <h2 id="recentTxTitle" class="text-lg font-bold text-gray-900 dark:text-white">Recent transactions</h2>
            <p class="text-xs font-medium text-gray-600 dark:text-gray-300">Your latest money in and out</p>
          </div>
          <a href="transactions.php" class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 px-3.5 py-2 text-xs font-bold text-[#0b2545] hover:border-[#0b2545] dark:border-gray-600 dark:text-white">View all <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i></a>
        </div>

        <!-- Desktop / tablet table -->
        <div class="hidden md:block overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead>
              <tr class="border-y border-gray-100 bg-gray-50/70 text-[11px] font-bold uppercase tracking-wider text-gray-600 dark:border-gray-700 dark:bg-white/5 dark:text-gray-300">
                <th scope="col" class="px-6 py-3">Transaction</th>
                <th scope="col" class="px-4 py-3">Date</th>
                <th scope="col" class="px-4 py-3">Method</th>
                <th scope="col" class="px-4 py-3">Status</th>
                <th scope="col" class="px-6 py-3 text-right">Amount</th>
              </tr>
            </thead>
            <tbody id="recentTxTable" class="divide-y divide-gray-100 dark:divide-gray-700/70"></tbody>
          </table>
        </div>

        <!-- Phone list -->
        <ul id="recentTxList" class="md:hidden divide-y divide-gray-100 border-t border-gray-100 dark:divide-gray-700/70 dark:border-gray-700"></ul>

        <div id="recentTxState" class="px-6 py-10 text-center text-sm font-medium text-gray-600 dark:text-gray-300" role="status">Loading transactions…</div>
      </section>

    </main>

  <script>
    // Recent transactions on the dashboard. Built with DOM text nodes only (no HTML from the API).
    document.addEventListener('DOMContentLoaded', async () => {
      const table = document.getElementById('recentTxTable');
      const list = document.getElementById('recentTxList');
      const state = document.getElementById('recentTxState');
      if (!table || !list || !state) return;

      const el = (tag, cls, text) => { const n = document.createElement(tag); if (cls) n.className = cls; if (text !== undefined) n.textContent = text; return n; };
      const labels = { deposit: 'Deposit', withdrawal: 'Withdrawal', swap: 'Currency swap', refund: 'Refund', fee: 'Fee', adjustment: 'Adjustment' };
      const label = (t) => t.type === 'transfer' ? (t.direction === 'in' ? 'Transfer received' : 'Transfer sent')
        : t.type === 'payment' ? (t.direction === 'in' ? 'Payment received' : 'Payment') : (labels[t.type] || 'Transaction');
      const icon = (t) => t.direction === 'swap' ? 'arrow-left-right' : t.direction === 'in' ? 'arrow-down-left' : 'arrow-up-right';
      const methods = { bank_transfer: 'Bank transfer', card: 'Card', crypto: 'Crypto', apple_pay: 'Apple Pay' };
      const statusStyle = {
        successful: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300',
        pending: 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300',
        failed: 'bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300',
        refunded: 'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-200',
      };
      const statusText = { successful: 'Successful', pending: 'Pending', failed: 'Failed', refunded: 'Refunded' };
      const money = (t) => {
        const value = Number(t.amount) || 0;
        const moved = t.status !== 'failed';
        const sign = !moved ? '' : t.direction === 'in' ? '+' : t.direction === 'out' ? '−' : '';
        let text;
        try { text = new Intl.NumberFormat('en-NG', { style: 'currency', currency: t.currency, currencyDisplay: 'narrowSymbol' }).format(value); }
        catch (e) { text = `${value.toLocaleString('en-NG', { maximumFractionDigits: 8 })} ${t.currency}`; } // crypto codes
        return sign + text;
      };
      const when = (v) => { const d = new Date(String(v || '').replace(' ', 'T')); return isNaN(d) ? '' : d.toLocaleString('en-NG', { day: 'numeric', month: 'short', hour: 'numeric', minute: '2-digit', hour12: true }); };
      const pill = (t) => el('span', `inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-bold ${statusStyle[t.status] || statusStyle.refunded}`, statusText[t.status] || t.status);
      const badge = (t) => {
        const b = el('span', `grid h-9 w-9 shrink-0 place-items-center rounded-full ${t.direction === 'in' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-300' : 'bg-[#faf5e8] text-[#8a6a1d]'}`);
        b.innerHTML = `<i data-lucide="${icon(t)}" class="w-4 h-4"></i>`;
        return b;
      };
      const amountClass = (t) => `font-bold tabular-nums ${t.status === 'failed' ? 'text-gray-400 line-through dark:text-gray-500' : t.direction === 'in' ? 'text-emerald-700 dark:text-emerald-300' : 'text-gray-900 dark:text-white'}`;

      try {
        const token = localStorage.getItem('flow_token') || '';
        const res = await fetch('../api/transactions/list.php?range=all&page_size=8', { headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' } });
        const data = await res.json();
        if (!res.ok || !data.success) throw new Error(data.error || 'Unable to load');
        const rows = data.transactions || [];
        if (!rows.length) { state.textContent = 'No transactions yet. Your payments and transfers will show here.'; return; }
        state.remove();

        rows.forEach((t) => {
          // Table row (tablet and desktop)
          const tr = el('tr', 'hover:bg-gray-50/80 dark:hover:bg-white/5');
          const first = el('td', 'px-6 py-3.5');
          const wrap = el('div', 'flex items-center gap-3');
          const names = el('div', 'min-w-0');
          names.append(el('div', 'font-bold text-gray-900 dark:text-white', label(t)), el('div', 'truncate font-mono text-[11px] text-gray-500 dark:text-gray-400', t.reference || ''));
          wrap.append(badge(t), names); first.append(wrap);
          const status = el('td', 'px-4 py-3.5'); status.append(pill(t));
          tr.append(first,
            el('td', 'whitespace-nowrap px-4 py-3.5 text-gray-700 dark:text-gray-300', when(t.created_at)),
            el('td', 'whitespace-nowrap px-4 py-3.5 text-gray-700 dark:text-gray-300', methods[t.channel] || 'Wallet'),
            status,
            el('td', `whitespace-nowrap px-6 py-3.5 text-right ${amountClass(t)}`, money(t)));
          table.append(tr);

          // Stacked row (phones): what + when on the left, amount + status on the right
          const li = el('li', 'flex items-center gap-3 px-5 py-3.5');
          const mid = el('div', 'min-w-0 flex-1');
          mid.append(el('div', 'truncate text-sm font-bold text-gray-900 dark:text-white', label(t)), el('div', 'text-xs text-gray-600 dark:text-gray-400', when(t.created_at)));
          const right = el('div', 'flex shrink-0 flex-col items-end gap-1');
          right.append(el('span', `text-sm ${amountClass(t)}`, money(t)), pill(t));
          li.append(badge(t), mid, right);
          list.append(li);
        });
        window.lucide?.createIcons();
      } catch (error) {
        state.textContent = 'Unable to load recent transactions right now.';
      }
    });
  </script>

  <!-- Interactive JavaScript Logic Specific to Dashboard Cards -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Toggle Eye / Obscure Balances
      const balances = [
        { btnId: 'toggleBalanceBtn', valId: 'totalBalanceVal', actual: '₦0.00', hidden: '••••••••' },
        { btnId: 'togglePaymentBtn', valId: 'paymentOverviewVal', actual: '₦0.00', hidden: '••••••••' },
        { btnId: 'toggleEarningBtn', valId: 'earningOverviewVal', actual: '₦0.00', hidden: '••••••••' }
      ];
      document.querySelectorAll('#paymentChart, #earningChart, .h-12.w-full.relative.mt-2').forEach(chart => chart.style.visibility = 'hidden');

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
        if (!periodDropdownMenu || !periodBtn) return; // the period picker was removed from the balance card
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
            moneyInVal: money(summary.earning_overview, activeCurrency),
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
          // Stat cards keep their explanatory sub-lines; footers show real bars/meter or an empty-state link.
          if (typeof window.fwlRenderStatCards === 'function') window.fwlRenderStatCards(summary, value => money(value, activeCurrency));
          const balanceCaption = document.getElementById('balanceCaption');
          if (balanceCaption) balanceCaption.textContent = Number(summary.pending_balance) > 0 ? `Available to spend · ${money(summary.pending_balance, activeCurrency)} pending` : 'Available to spend';
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
          currencyText.textContent = activeCurrency;
          currencyData.splice(0, currencyData.length, ...(result.available_currencies || []).map(item => ({ ...item, country: item.code === 'NGN' ? 'Nigeria' : item.name, symbol: item.code === 'NGN' ? '₦' : item.code, balance: money(summary.total_balance, item.code), flagSvg: currencyFlagCatalog[item.code] || '' })));
          if (!currencyData.length) currencyData.push({ code: activeCurrency, country: result.currency.name, name: result.currency.name, symbol: activeCurrency, balance: values.totalBalanceVal, flagSvg: '' });
          currentCurrency = currencyData.find(item => item.code === activeCurrency) || currencyData[0];
          renderCurrencyOptions();
        } catch (error) {
          if (balanceTrendBadge) balanceTrendBadge.textContent = 'Balance data is temporarily unavailable.';
          ['paymentChart','earningChart'].forEach(id => { const chart = document.getElementById(id); if (chart) { chart.replaceChildren(Object.assign(document.createElement('p'), { className: 'py-12 text-center text-xs text-gray-400', textContent: 'No data available' })); } });
          const balanceCaption = document.getElementById('balanceCaption');
          if (balanceCaption) balanceCaption.textContent = 'Balance is temporarily unavailable';
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
