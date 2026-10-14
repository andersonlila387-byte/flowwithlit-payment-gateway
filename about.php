<?php
$pageTitle = 'About Us — Flowwithlit';
$pageDesc  = 'Tearing down the borders of global finance for everyone. Learn about Flowwithlit, our mission, and our founders.';
$activePage = 'about';
require_once __DIR__ . '/includes/header.php';
?>

  <!-- ABOUT HERO SECTION -->
  <section class="hero-bg relative min-h-[85vh] flex flex-col items-center justify-center text-center px-6 pt-36 pb-24">
    <div class="reveal mb-6">
      <span class="badge-pill">
        <span class="badge-dot"></span> Our Mission
      </span>
    </div>

    <div class="max-w-4xl">
      <h1 class="reveal text-white text-3xl sm:text-5xl md:text-[54px] font-semibold tracking-[-0.035em] leading-[1.12]">
        Tearing down the borders of<br />global finance for everyone
      </h1>
      <p class="reveal mt-5 text-white/85 text-sm sm:text-base md:text-lg max-w-2xl mx-auto font-normal leading-relaxed">
        Flowwithlit is building the ultimate financial infrastructure to bring seamless payment processing, instant crypto swaps, and virtual bank transfers to businesses and individuals worldwide.
      </p>
      
      <div class="reveal mt-8 flex flex-col sm:flex-row items-center justify-center gap-3.5 w-full max-w-xl mx-auto">
        <!-- Button 1: Create Free Account -->
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

  <!-- OUR STORY & PHILOSOPHY (FINLYTIX STYLED WITH IMAGE METRICS) -->
  <section class="py-20 sm:py-24 px-6 bg-white">
    <div class="max-w-6xl mx-auto">
      <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
        <!-- Story Text -->
        <div class="reveal">
          <div class="badge-pill mb-4">
            <span class="badge-dot"></span> Why We Built Flowwithlit
          </div>
          <h2 class="text-2xl sm:text-3xl md:text-[40px] font-bold tracking-[-0.03em] leading-tight text-ink mb-6">
            Building the payment infrastructure we always wanted
          </h2>
          <div class="space-y-4 text-ink/80 text-xs sm:text-sm md:text-base leading-relaxed">
            <p>
              For years, cross-border payments across Africa suffered from outrageous transaction fees, multi-day bank delays, opaque currency conversions, and fragile API integrations.
            </p>
            <p>
              We believed business owners, remote developers, and online shoppers deserved software as elegant and dependable as the work they do every single day.
            </p>
            <p class="font-medium text-ink">
              So we built Flowwithlit — unifying crypto swaps, local bank payouts, and virtual accounts on a single, sub-second payment engine.
            </p>
          </div>
        </div>

        <!-- Flowwithlit Metrics Image Visual Card -->
        <div class="reveal bg-mint border border-brand-500/10 rounded-3xl p-3 sm:p-4 overflow-hidden group">
          <img 
            src="images/kYz1kouT5vJAS5yrYNunWTAaE.png" 
            alt="Flowwithlit Real-Time Payment Metrics" 
            class="w-full h-auto rounded-2xl object-cover group-hover:scale-[1.02] transition-transform duration-500"
          />
        </div>
      </div>
    </div>
  </section>

  <!-- CLEAN & READABLE FOUNDERS SECTION -->
  <section class="py-20 sm:py-24 px-6 bg-mint/40 border-t border-b border-brand-500/10">
    <div class="max-w-5xl mx-auto space-y-16 sm:space-y-20">
      <div class="text-center max-w-xl mx-auto">
        <div class="reveal badge-pill mb-4">
          <span class="badge-dot"></span> Leadership &amp; Vision
        </div>
        <h2 class="reveal text-2xl sm:text-3xl md:text-[40px] font-bold tracking-[-0.03em] leading-tight text-ink">
          The humans behind Flowwithlit.
        </h2>
      </div>

      <!-- FOUNDER 1: Famakinwa Emmanuel -->
      <div class="reveal grid lg:grid-cols-2 gap-10 items-center">
        <div>
          <img 
            src="images/famakinwa.jpg" 
            alt="Famakinwa Emmanuel — Founder & CEO" 
            class="w-full max-h-[420px] rounded-2xl object-cover object-top"
          />
        </div>
        <div class="space-y-4 text-justify">
          <div>
            <h3 class="text-2xl sm:text-3xl font-bold text-ink tracking-tight">Famakinwa Emmanuel</h3>
            <p class="text-blue-600 font-semibold text-sm mt-1">Founder &amp; Chief Executive Officer</p>
          </div>
          <p class="text-ink/80 text-sm md:text-base leading-relaxed">
            Famakinwa Emmanuel leads executive strategy and global vision at Flowwithlit. Under his leadership, Flowwithlit is building modern payment infrastructure designed to remove financial boundaries across Africa and connect local merchants to global digital commerce seamlessly.
          </p>
          <p class="text-ink/80 text-sm md:text-base leading-relaxed italic">
            "We built Flowwithlit to be the ultimate foundation for digital commerce. Money should move as freely and frictionlessly as information does across the internet."
          </p>
        </div>
      </div>

      <!-- FOUNDER 2: Olagundoye Joseph -->
      <div class="reveal grid lg:grid-cols-2 gap-10 items-center">
        <div class="space-y-4 text-justify order-2 lg:order-1">
          <div>
            <h3 class="text-2xl sm:text-3xl font-bold text-ink tracking-tight">Olagundoye Joseph</h3>
            <p class="text-blue-600 font-semibold text-sm mt-1">Founder &amp; Lead Systems Developer</p>
          </div>
          <p class="text-ink/80 text-sm md:text-base leading-relaxed">
            Olagundoye Joseph oversees core system architecture and protocol engineering at Flowwithlit. He focuses on building sub-second webhook notifications, crypto-to-fiat conversion rails, and bank-grade security protocols that power reliable cross-border transactions.
          </p>
          <p class="text-ink/80 text-sm md:text-base leading-relaxed italic">
            "Engineering payment infrastructure that processes cross-border currency swaps in milliseconds requires zero-compromise architecture. Performance and security drive every line of code."
          </p>
        </div>
        <div class="order-1 lg:order-2">
          <img 
            src="images/olagundoye.png" 
            alt="Olagundoye Joseph — Founder & Lead Developer" 
            class="w-full max-h-[420px] rounded-2xl object-cover object-top"
          />
        </div>
      </div>

    </div>
  </section>

  <!-- WHAT WE BELIEVE SECTION -->
  <section class="py-20 sm:py-24 px-6 bg-white">
    <div class="max-w-6xl mx-auto">
      <div class="text-center max-w-xl mx-auto mb-16">
        <h2 class="reveal text-3xl sm:text-4xl md:text-[46px] font-bold tracking-[-0.035em] leading-tight text-ink">
          What we believe.
        </h2>
      </div>

      <div class="grid lg:grid-cols-3 gap-6 items-stretch">
        <div class="reveal flex flex-col gap-6 justify-between">
          <div class="bg-mint border border-brand-500/10 rounded-3xl p-8 sm:p-9 flex flex-col justify-between h-full">
            <div>
              <div class="w-10 h-10 rounded-xl bg-white text-[#0b2545] flex items-center justify-center font-bold mb-6">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="#0b2545" stroke="none"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
              </div>
              <h3 class="text-xl sm:text-2xl font-bold text-ink mb-3 tracking-tight">Customer obsession</h3>
              <p class="text-ink/75 text-xs sm:text-sm leading-relaxed">
                Every line of code is built to help our customers simplify finance, automate reporting, and make faster, smarter business decisions daily.
              </p>
            </div>
          </div>

          <div class="bg-mint border border-brand-500/10 rounded-3xl p-8 sm:p-9 flex flex-col justify-between h-full">
            <div>
              <div class="w-10 h-10 rounded-xl bg-white text-[#0b2545] flex items-center justify-center font-bold mb-6">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
              </div>
              <h3 class="text-xl sm:text-2xl font-bold text-ink mb-3 tracking-tight">Bias for speed</h3>
              <p class="text-ink/75 text-xs sm:text-sm leading-relaxed">
                Build faster products with smarter support, seamless workflows, and rapid iterations designed to help modern finance teams scale efficiently.
              </p>
            </div>
          </div>
        </div>

        <div class="reveal h-full min-h-[440px]">
          <div class="rounded-3xl overflow-hidden border border-brand-500/10 h-full relative group bg-[#0b2545]">
            <img 
              src="images/team_collaboration.jpg" 
              alt="Flowwithlit Team Collaborating" 
              class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500 min-h-[440px]"
            />
          </div>
        </div>

        <div class="reveal flex flex-col gap-6 justify-between">
          <div class="bg-mint border border-brand-500/10 rounded-3xl p-8 sm:p-9 flex flex-col justify-between h-full">
            <div>
              <div class="w-10 h-10 rounded-xl bg-white text-[#0b2545] flex items-center justify-center font-bold mb-6">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
              </div>
              <h3 class="text-xl sm:text-2xl font-bold text-ink mb-3 tracking-tight">Radical clarity</h3>
              <p class="text-ink/75 text-xs sm:text-sm leading-relaxed">
                Simple language, transparent pricing, and honest product roadmaps built to help modern finance teams grow with confidence daily.
              </p>
            </div>
          </div>

          <div class="bg-mint border border-brand-500/10 rounded-3xl p-8 sm:p-9 flex flex-col justify-between h-full">
            <div>
              <div class="w-10 h-10 rounded-xl bg-white text-[#0b2545] flex items-center justify-center font-bold mb-6">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
              </div>
              <h3 class="text-xl sm:text-2xl font-bold text-ink mb-3 tracking-tight">Trust by default</h3>
              <p class="text-ink/75 text-xs sm:text-sm leading-relaxed">
                Security and privacy are built into every layer, protecting your financial data with trusted infrastructure and encryption.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
