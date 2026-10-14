<?php
$pageTitle = 'Flowwithlit — Pay, Swap & Transfer Money with Crypto & Naira';
$pageDesc  = 'Flowwithlit is your all-in-one payment gateway. Pay with Crypto or Naira, convert currencies instantly, and send money fast with Flow Tags.';
$activePage = 'home';
require_once __DIR__ . '/includes/header.php';
?>

  <!-- HERO -->
  <section class="hero-bg relative min-h-[96vh] flex flex-col items-center justify-center text-center px-6 pt-32 pb-24">
    <!-- Announcement Badge -->
    <div class="reveal mb-6">
      <span class="badge-pill">
        <span class="badge-dot"></span> New — Instant Crypto &amp; Naira Swaps
      </span>
    </div>

    <!-- Main Headline -->
    <div class="max-w-4xl">
      <h1 class="reveal text-white text-3xl sm:text-5xl md:text-[54px] font-semibold tracking-[-0.035em] leading-[1.12]">
        Pay, swap &amp; transfer money<br />in Crypto and Naira
      </h1>
      <p class="reveal mt-5 text-white/85 text-sm sm:text-base md:text-lg max-w-2xl mx-auto font-normal leading-relaxed">
        Flowwithlit is the all-in-one payment gateway for businesses. Accept Crypto and Naira payments seamlessly, convert Naira to Crypto or Crypto to Naira in seconds, send instant bank transfers, and collect payments with simple Flow Tags.
      </p>
      
      <div class="reveal mt-8 flex flex-col sm:flex-row items-center justify-center gap-3.5 w-full max-w-xl mx-auto">
        <!-- Button 1: Create Free Account (White BG + Dark Circle Arrow with Hover Effect) -->
        <a href="register" class="group w-[90%] sm:w-64 h-13 py-2.5 inline-flex items-center justify-between bg-white hover:bg-[#eef4ff] transition-all duration-300 text-[#0b2545] text-sm font-semibold pl-6 pr-2 rounded-full shadow-lg shrink-0">
          <span>Create Free Account</span>
          <span class="w-8 h-8 rounded-full bg-[#0b2545] text-white flex items-center justify-center font-bold shrink-0 transition-transform duration-300 group-hover:scale-105">
            <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M7 17L17 7M17 7H7M17 7V17"/></svg>
          </span>
        </a>

        <!-- Button 2: Access Your Account -->
        <a href="login" class="group w-[90%] sm:w-64 h-13 py-2.5 inline-flex items-center justify-between bg-white/10 hover:bg-white/20 transition text-white text-sm font-semibold pl-6 pr-2 rounded-full shadow-lg border border-white/20 shrink-0">
          <span>Access Your Account</span>
          <span class="w-8 h-8 rounded-full bg-white text-[#0b2545] flex items-center justify-center font-bold shrink-0">
            <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M7 17L17 7M17 7H7M17 7V17"/></svg>
          </span>
        </a>
      </div>
    </div>
  </section>

  <!-- STATS -->
  <section class="py-20 sm:py-24 px-6 bg-white">
    <div class="max-w-6xl mx-auto text-center">
      <div class="reveal badge-pill mb-4">
        <span class="badge-dot"></span> Simple payments
      </div>
      <h2 class="reveal text-2xl sm:text-3xl md:text-[38px] font-bold tracking-[-0.03em] leading-tight text-ink">Built for fast, easy money moves</h2>
      <p class="reveal mt-3.5 text-ink/70 text-xs sm:text-sm md:text-base max-w-xl mx-auto leading-relaxed">
        No complicated steps or hidden fees. Flowwithlit makes sending, receiving, and swapping money simple for everyone.
      </p>

      <div class="mt-12 grid md:grid-cols-3 gap-6 text-left">
        <!-- Stat Card 1: Processing Speed -->
        <div class="reveal bg-mint border border-brand-500/10 rounded-3xl p-7 sm:p-8 transition hover:shadow-card">
          <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#0b2545] shadow-sm mb-10 font-bold">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
          </div>
          <div class="text-3xl sm:text-4xl font-bold text-ink tracking-tight">Instant</div>
          <div class="font-bold text-ink text-sm sm:text-base mt-2">Zero Waiting Time</div>
          <div class="text-ink/60 text-xs sm:text-sm mt-1 leading-relaxed">Swaps and transfers settle in real-time on your dashboard.</div>
        </div>

        <!-- Stat Card 2: Uptime & Reliability -->
        <div class="reveal bg-mint border border-brand-500/10 rounded-3xl p-7 sm:p-8 transition hover:shadow-card">
          <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#0b2545] shadow-sm mb-10 font-bold">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
          </div>
          <div class="text-3xl sm:text-4xl font-bold text-ink tracking-tight">99.99%</div>
          <div class="font-bold text-ink text-sm sm:text-base mt-2">Platform Uptime</div>
          <div class="text-ink/60 text-xs sm:text-sm mt-1 leading-relaxed">Always online so your money is always moving smoothly.</div>
        </div>

        <!-- Stat Card 3: Supported Currencies & Methods -->
        <div class="reveal bg-mint border border-brand-500/10 rounded-3xl p-7 sm:p-8 transition hover:shadow-card">
          <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#0b2545] shadow-sm mb-10 font-bold">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
          </div>
          <div class="text-3xl sm:text-4xl font-bold text-ink tracking-tight">Multi-Asset</div>
          <div class="font-bold text-ink text-sm sm:text-base mt-2">Crypto &amp; Fiat</div>
          <div class="text-ink/60 text-xs sm:text-sm mt-1 leading-relaxed">Support for USDT, BTC, ETH, Naira bank accounts &amp; e-Transfers.</div>
        </div>
      </div>
    </div>
  </section>

  <!-- FEATURES -->
  <section id="features" class="py-20 sm:py-24 px-6 bg-slate-50 border-t border-slate-100">
    <div class="max-w-6xl mx-auto">
      <div class="text-center max-w-2xl mx-auto mb-16">
        <div class="reveal badge-pill mb-4">
          <span class="badge-dot"></span> Full payment suite
        </div>
        <h2 class="reveal text-2xl sm:text-3xl md:text-[38px] font-bold tracking-[-0.03em] leading-tight text-ink">Everything you need to pay &amp; get paid</h2>
        <p class="reveal mt-3.5 text-ink/70 text-xs sm:text-sm md:text-base leading-relaxed">
          From individual money transfers to full merchant payment gateways, Flowwithlit empowers your financial freedom.
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-8">
        <!-- Feature 1: Crypto & Naira Swaps -->
        <div class="reveal bg-white border border-brand-500/10 rounded-3xl p-8 transition hover:shadow-card">
          <div class="w-12 h-12 rounded-2xl bg-mint text-[#0b2545] flex items-center justify-center font-bold mb-6">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M7 16V4M7 4L3 8M7 4L11 8M17 8V20M17 20L21 16M17 20L13 16"/></svg>
          </div>
          <h3 class="text-xl font-bold text-ink">Instant Crypto &amp; Naira Swaps</h3>
          <p class="text-ink/70 text-xs sm:text-sm mt-2 leading-relaxed">
            Convert Naira directly to USDT, BTC, or ETH, or swap your Crypto back to Naira instantly at competitive real-time market rates.
          </p>
        </div>

        <!-- Feature 2: Flow Tags -->
        <div class="reveal bg-white border border-brand-500/10 rounded-3xl p-8 transition hover:shadow-card">
          <div class="w-12 h-12 rounded-2xl bg-mint text-[#0b2545] flex items-center justify-center font-bold mb-6">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="4"/><path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-3.92 7.94"/></svg>
          </div>
          <h3 class="text-xl font-bold text-ink">Simple Flow Tags (@handle)</h3>
          <p class="text-ink/70 text-xs sm:text-sm mt-2 leading-relaxed">
            Send and receive payments using memorable handles like <span class="font-mono text-[#0b2545] font-bold">@alex</span>. No long account numbers needed.
          </p>
        </div>

        <!-- Feature 3: Merchant E-Commerce Plugins -->
        <div class="reveal bg-white border border-brand-500/10 rounded-3xl p-8 transition hover:shadow-card">
          <div class="w-12 h-12 rounded-2xl bg-mint text-[#0b2545] flex items-center justify-center font-bold mb-6">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          </div>
          <h3 class="text-xl font-bold text-ink">Ready-Made E-Commerce Plugins</h3>
          <p class="text-ink/70 text-xs sm:text-sm mt-2 leading-relaxed">
            Connect Flowwithlit to WooCommerce, Shopify, Wix, or custom sites in minutes with ready plugins and SDKs.
          </p>
        </div>

        <!-- Feature 4: E-Transfers & Bank Wire -->
        <div class="reveal bg-white border border-brand-500/10 rounded-3xl p-8 transition hover:shadow-card">
          <div class="w-12 h-12 rounded-2xl bg-mint text-[#0b2545] flex items-center justify-center font-bold mb-6">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
          </div>
          <h3 class="text-xl font-bold text-ink">Fast E-Transfers &amp; Payouts</h3>
          <p class="text-ink/70 text-xs sm:text-sm mt-2 leading-relaxed">
            Withdraw funds directly to any Nigerian bank account or crypto wallet with sub-minute confirmation times.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- HOW IT WORKS -->
  <section id="how-it-works" class="py-20 sm:py-24 px-6 bg-white border-t border-slate-100">
    <div class="max-w-6xl mx-auto">
      <div class="text-center max-w-2xl mx-auto mb-14">
        <div class="reveal badge-pill mb-4"><span class="badge-dot"></span> Made for momentum</div>
        <h2 class="reveal text-2xl sm:text-3xl md:text-[38px] font-bold tracking-[-0.03em] leading-tight text-ink">Move money in three simple steps</h2>
        <p class="reveal mt-3.5 text-ink/70 text-xs sm:text-sm md:text-base leading-relaxed">Whether you are paying, collecting, or converting, every Flowwithlit transaction starts with the same simple flow.</p>
      </div>
      <div class="grid md:grid-cols-3 gap-6 relative">
        <div class="hidden md:block absolute top-11 left-[18%] right-[18%] border-t-2 border-dashed border-brand-500/20"></div>
        <div class="reveal relative bg-slate-50 border border-brand-500/10 rounded-3xl p-7 sm:p-8 text-center"><div class="w-14 h-14 mx-auto rounded-full bg-[#0b2545] text-white flex items-center justify-center text-xl font-bold mb-6 shadow-lg">01</div><h3 class="text-xl font-bold text-ink">Create your account</h3><p class="mt-2 text-ink/70 text-xs sm:text-sm leading-relaxed">Get started in minutes with a secure Flowwithlit account built around you or your business.</p></div>
        <div class="reveal relative bg-slate-50 border border-brand-500/10 rounded-3xl p-7 sm:p-8 text-center"><div class="w-14 h-14 mx-auto rounded-full bg-[#0b2545] text-white flex items-center justify-center text-xl font-bold mb-6 shadow-lg">02</div><h3 class="text-xl font-bold text-ink">Choose how to move</h3><p class="mt-2 text-ink/70 text-xs sm:text-sm leading-relaxed">Send Naira, accept crypto, swap assets, or share a Flow Tag to request payment.</p></div>
        <div class="reveal relative bg-slate-50 border border-brand-500/10 rounded-3xl p-7 sm:p-8 text-center"><div class="w-14 h-14 mx-auto rounded-full bg-[#0b2545] text-white flex items-center justify-center text-xl font-bold mb-6 shadow-lg">03</div><h3 class="text-xl font-bold text-ink">Track it instantly</h3><p class="mt-2 text-ink/70 text-xs sm:text-sm leading-relaxed">See every transaction, balance, and payout in one clear real-time dashboard.</p></div>
      </div>
    </div>
  </section>

  <!-- PAYMENT METHODS -->
  <section class="py-20 sm:py-24 px-6 bg-[#0b2545] text-white overflow-hidden">
    <div class="max-w-6xl mx-auto grid lg:grid-cols-2 gap-12 items-center">
      <div><div class="reveal inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 border border-white/15 text-xs mb-5"><span class="w-2 h-2 rounded-full bg-blue-300"></span> One wallet, more ways to pay</div><h2 class="reveal text-3xl sm:text-4xl md:text-[42px] font-bold tracking-[-0.03em] leading-tight">Every payment method your customers expect</h2><p class="reveal mt-4 text-white/70 text-sm sm:text-base leading-relaxed max-w-xl">Accept the value your customers already hold, then settle and manage it from one reliable place.</p><a href="register" class="reveal mt-7 inline-flex items-center gap-3 bg-white hover:bg-[#eef4ff] text-[#0b2545] text-sm font-bold py-3 pl-5 pr-3 rounded-full transition"><span>Start accepting payments</span><span class="w-7 h-7 rounded-full bg-[#0b2545] text-white flex items-center justify-center">→</span></a></div>
      <div class="grid sm:grid-cols-2 gap-4"><div class="reveal bg-white/10 border border-white/15 rounded-2xl p-6"><div class="w-10 h-10 rounded-xl bg-white text-[#0b2545] flex items-center justify-center text-lg font-bold">₦</div><h3 class="mt-5 text-xl font-bold">Naira transfers</h3><p class="mt-1 text-white/65 text-xs sm:text-sm">Send, receive, and settle to Nigerian bank accounts.</p></div><div class="reveal bg-white/10 border border-white/15 rounded-2xl p-6"><div class="w-10 h-10 rounded-xl bg-blue-300 text-[#0b2545] flex items-center justify-center text-sm font-bold">₿</div><h3 class="mt-5 text-xl font-bold">Crypto payments</h3><p class="mt-1 text-white/65 text-xs sm:text-sm">Accept digital assets with transparent conversion.</p></div><div class="reveal bg-white/10 border border-white/15 rounded-2xl p-6"><div class="w-10 h-10 rounded-xl bg-emerald-200 text-[#0b2545] flex items-center justify-center text-lg font-bold">@</div><h3 class="mt-5 text-xl font-bold">Flow Tags</h3><p class="mt-1 text-white/65 text-xs sm:text-sm">Get paid through a simple, memorable handle.</p></div><div class="reveal bg-white/10 border border-white/15 rounded-2xl p-6"><div class="w-10 h-10 rounded-xl bg-violet-200 text-[#0b2545] flex items-center justify-center text-lg font-bold">↔</div><h3 class="mt-5 text-xl font-bold">Instant swaps</h3><p class="mt-1 text-white/65 text-xs sm:text-sm">Move between Naira and crypto when you need to.</p></div></div>
    </div>
  </section>

  <!-- BUSINESS INTEGRATIONS -->
  <section class="py-20 sm:py-24 px-6 bg-slate-50 border-t border-slate-100"><div class="max-w-6xl mx-auto grid lg:grid-cols-2 gap-12 items-center"><div class="reveal grid grid-cols-2 gap-4 order-2 lg:order-1"><div class="bg-white rounded-2xl border border-brand-500/10 p-6 shadow-sm"><div class="text-2xl font-bold text-ink">API</div><p class="mt-2 text-xs text-ink/60">Build a custom payment experience.</p></div><div class="bg-[#0b2545] text-white rounded-2xl p-6 mt-7"><div class="text-2xl font-bold">Plugins</div><p class="mt-2 text-xs text-white/65">Connect your store in minutes.</p></div><div class="bg-[#d9e6ff] rounded-2xl p-6 -mt-7"><div class="text-2xl font-bold text-ink">Links</div><p class="mt-2 text-xs text-ink/60">Share a payment link anywhere.</p></div><div class="bg-white rounded-2xl border border-brand-500/10 p-6 shadow-sm"><div class="text-2xl font-bold text-ink">Payouts</div><p class="mt-2 text-xs text-ink/60">Pay customers and teams fast.</p></div></div><div class="order-1 lg:order-2"><div class="reveal badge-pill mb-4"><span class="badge-dot"></span> Built for businesses</div><h2 class="reveal text-2xl sm:text-3xl md:text-[38px] font-bold tracking-[-0.03em] leading-tight text-ink">Payments that fit how your business works</h2><p class="reveal mt-4 text-ink/70 text-sm sm:text-base leading-relaxed">From a payment link in your bio to a complete checkout integration, Flowwithlit gives you flexible ways to collect and manage payments.</p><ul class="reveal mt-6 space-y-3 text-sm text-ink/80"><li class="flex gap-3"><span class="font-bold">✓</span> Developer-friendly APIs for tailored checkout flows</li><li class="flex gap-3"><span class="font-bold">✓</span> Store plugins for faster integration</li><li class="flex gap-3"><span class="font-bold">✓</span> Payment links and business payouts from one dashboard</li></ul><a href="developers" class="reveal inline-block mt-7 text-sm font-bold text-[#0b2545] border-b-2 border-[#0b2545] pb-1 hover:text-blue-700 hover:border-blue-700 transition">Explore developer tools →</a></div></div></section>

  <!-- SECURITY -->
  <section class="py-20 sm:py-24 px-6 bg-white border-t border-slate-100"><div class="max-w-6xl mx-auto"><div class="text-center max-w-2xl mx-auto mb-12"><div class="reveal badge-pill mb-4"><span class="badge-dot"></span> Safety by design</div><h2 class="reveal text-2xl sm:text-3xl md:text-[38px] font-bold tracking-[-0.03em] leading-tight text-ink">Secure payments, clear controls</h2><p class="reveal mt-3.5 text-ink/70 text-sm sm:text-base leading-relaxed">Your money and information deserve strong protection at every step.</p></div><div class="grid md:grid-cols-3 gap-6"><div class="reveal border border-brand-500/10 rounded-3xl p-7"><div class="w-11 h-11 rounded-2xl bg-mint flex items-center justify-center text-xl">⌁</div><h3 class="mt-5 text-xl font-bold text-ink">Protected data</h3><p class="mt-2 text-xs sm:text-sm text-ink/65 leading-relaxed">Sensitive information is handled with encryption and secure practices.</p></div><div class="reveal border border-brand-500/10 rounded-3xl p-7"><div class="w-11 h-11 rounded-2xl bg-mint flex items-center justify-center text-xl">✓</div><h3 class="mt-5 text-xl font-bold text-ink">Verified access</h3><p class="mt-2 text-xs sm:text-sm text-ink/65 leading-relaxed">Account checks and authentication help keep access in the right hands.</p></div><div class="reveal border border-brand-500/10 rounded-3xl p-7"><div class="w-11 h-11 rounded-2xl bg-mint flex items-center justify-center text-xl">◉</div><h3 class="mt-5 text-xl font-bold text-ink">Always visible</h3><p class="mt-2 text-xs sm:text-sm text-ink/65 leading-relaxed">Keep an eye on your balances and transaction activity in real time.</p></div></div></div></section>

  <!-- TESTIMONIALS -->
  <section class="py-20 sm:py-24 px-6 bg-slate-50 border-t border-slate-100"><div class="max-w-6xl mx-auto"><div class="text-center max-w-2xl mx-auto mb-12"><div class="reveal badge-pill mb-4"><span class="badge-dot"></span> Growing with our users</div><h2 class="reveal text-2xl sm:text-3xl md:text-[38px] font-bold tracking-[-0.03em] leading-tight text-ink">Built for everyday money movement</h2></div><div class="grid md:grid-cols-3 gap-6"><figure class="reveal bg-white rounded-3xl border border-brand-500/10 p-7 shadow-sm"><div class="text-[#0b2545] tracking-[0.18em] text-sm">★★★★★</div><blockquote class="mt-5 text-lg leading-relaxed text-ink font-semibold">“The Flow Tag makes collecting payments from clients feel effortless.”</blockquote><figcaption class="mt-6 flex items-center gap-3"><span class="w-10 h-10 rounded-full bg-[#d9e6ff] flex items-center justify-center font-bold">AO</span><span><strong class="block text-sm">Ada O.</strong><small class="text-xs text-ink/55">Creative business owner</small></span></figcaption></figure><figure class="reveal bg-[#0b2545] text-white rounded-3xl p-7 shadow-lg"><div class="text-blue-300 tracking-[0.18em] text-sm">★★★★★</div><blockquote class="mt-5 text-lg leading-relaxed font-semibold">“It gives our customers more ways to pay without making checkout complicated.”</blockquote><figcaption class="mt-6 flex items-center gap-3"><span class="w-10 h-10 rounded-full bg-white/15 flex items-center justify-center font-bold">TM</span><span><strong class="block text-sm">Tomi M.</strong><small class="text-xs text-white/60">Online store operator</small></span></figcaption></figure><figure class="reveal bg-white rounded-3xl border border-brand-500/10 p-7 shadow-sm"><div class="text-[#0b2545] tracking-[0.18em] text-sm">★★★★★</div><blockquote class="mt-5 text-lg leading-relaxed text-ink font-semibold">“The dashboard is simple, fast, and lets me see every payment at a glance.”</blockquote><figcaption class="mt-6 flex items-center gap-3"><span class="w-10 h-10 rounded-full bg-[#d9e6ff] flex items-center justify-center font-bold">KS</span><span><strong class="block text-sm">Kunle S.</strong><small class="text-xs text-ink/55">Independent consultant</small></span></figcaption></figure></div></div></section>

  <!-- FAQ SECTION -->
  <section id="faq" class="py-20 sm:py-24 px-6 bg-white border-t border-slate-100">
    <div class="max-w-4xl mx-auto">
      <div class="text-center mb-12">
        <div class="reveal badge-pill mb-4">
          <span class="badge-dot"></span> Common Questions
        </div>
        <h2 class="reveal text-2xl sm:text-3xl md:text-[38px] font-bold tracking-[-0.03em] leading-tight text-ink">Frequently Asked Questions</h2>
      </div>

      <div class="divide-y divide-slate-200" id="faq-container">
        <!-- FAQ Item 1 -->
        <details class="group py-5" open>
          <summary class="flex items-center justify-between cursor-pointer list-none select-none">
            <span class="font-semibold text-ink text-base sm:text-lg tracking-tight">How fast is Naira to Crypto conversion?</span>
            <span class="w-8 h-8 rounded-full bg-[#0b2545] text-white flex items-center justify-center font-medium text-lg shrink-0">
              <span class="group-open:hidden">+</span>
              <span class="hidden group-open:inline">&minus;</span>
            </span>
          </summary>
          <p class="mt-3 text-ink/70 text-xs sm:text-sm leading-relaxed max-w-2xl">
            It happens instantly! As soon as you confirm your swap, your currency is converted and updated in your wallet balance right away.
          </p>
        </details>

        <!-- FAQ Item 2 -->
        <details class="group py-5">
          <summary class="flex items-center justify-between cursor-pointer list-none select-none">
            <span class="font-semibold text-ink text-base sm:text-lg tracking-tight">What is a Flow Tag?</span>
            <span class="w-8 h-8 rounded-full bg-[#0b2545] text-white flex items-center justify-center font-medium text-lg shrink-0">
              <span class="group-open:hidden">+</span>
              <span class="hidden group-open:inline">&minus;</span>
            </span>
          </summary>
          <p class="mt-3 text-ink/70 text-xs sm:text-sm leading-relaxed max-w-2xl">
            Flow Tag is your personal handle (like @yourname). It lets anyone send you money instantly without typing long bank account numbers or crypto addresses.
          </p>
        </details>

        <!-- FAQ Item 3 -->
        <details class="group py-5">
          <summary class="flex items-center justify-between cursor-pointer list-none select-none">
            <span class="font-semibold text-ink text-base sm:text-lg tracking-tight">Can I accept payments on my online store?</span>
            <span class="w-8 h-8 rounded-full bg-[#0b2545] text-white flex items-center justify-center font-medium text-lg shrink-0">
              <span class="group-open:hidden">+</span>
              <span class="hidden group-open:inline">&minus;</span>
            </span>
          </summary>
          <p class="mt-3 text-ink/70 text-xs sm:text-sm leading-relaxed max-w-2xl">
            Yes! Flowwithlit offers simple plugins for Shopify, WooCommerce, and WordPress, as well as easy developer APIs for custom sites.
          </p>
        </details>

        <!-- FAQ Item 4 -->
        <details class="group py-5">
          <summary class="flex items-center justify-between cursor-pointer list-none select-none">
            <span class="font-semibold text-ink text-base sm:text-lg tracking-tight">Is my money safe on Flowwithlit?</span>
            <span class="w-8 h-8 rounded-full bg-[#0b2545] text-white flex items-center justify-center font-medium text-lg shrink-0">
              <span class="group-open:hidden">+</span>
              <span class="hidden group-open:inline">&minus;</span>
            </span>
          </summary>
          <p class="mt-3 text-ink/70 text-xs sm:text-sm leading-relaxed max-w-2xl">
            Yes! We use bank-grade security, top-level encryption, and strict protection protocols to ensure your money and data stay 100% safe.
          </p>
        </details>
      </div>
    </div>
  </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
