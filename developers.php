<?php
$pageTitle = 'Developers & Integrations — Flowwithlit';
$pageDesc  = 'Explore Flowwithlit developer APIs, e-commerce plugins for WooCommerce, Shopify, Wix, Squarespace, and official SDKs for Node.js, Python, PHP, and Flutter.';
$activePage = 'developers';
require_once __DIR__ . '/includes/header.php';
?>

  <!-- HERO SECTION -->
  <section class="hero-bg relative min-h-[85vh] flex flex-col items-center justify-center text-center px-6 pt-36 pb-24">
    <div class="reveal mb-6">
      <span class="badge-pill">
        <span class="badge-dot"></span> Built for Engineers &amp; Merchants
      </span>
    </div>

    <div class="max-w-4xl">
      <h1 class="reveal text-white text-3xl sm:text-5xl md:text-[56px] font-bold tracking-[-0.035em] leading-[1.12]">
        Accept payments anywhere with<br class="hidden sm:block"/> ready-made plugins &amp; APIs
      </h1>

      <p class="reveal mt-6 text-white/80 text-sm sm:text-base md:text-lg max-w-2xl mx-auto leading-relaxed">
        Whether you are running a store on WooCommerce, Shopify, Wix, or building custom web &amp; mobile apps, Flowwithlit provides seamless payment plugins, instant webhooks, and flexible SDKs.
      </p>

      <div class="reveal mt-10 flex flex-col sm:flex-row items-center justify-center gap-3.5 w-full max-w-xl mx-auto">
        <!-- Button 1: Explore Ready Plugins (Primary White Pill with Arrow) -->
        <a href="#plugins" class="group w-[90%] sm:w-64 h-13 py-2.5 inline-flex items-center justify-between bg-white hover:bg-[#eef4ff] transition-all duration-300 text-[#0b2545] text-sm font-semibold pl-6 pr-2 rounded-full shadow-lg shrink-0">
          <span>Explore Ready Plugins</span>
          <span class="w-8 h-8 rounded-full bg-[#0b2545] text-white flex items-center justify-center font-bold shrink-0 transition-transform duration-300 group-hover:scale-105">
            <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M7 17L17 7M17 7H7M17 7V17"/></svg>
          </span>
        </a>
        <!-- Button 2: View SDKs & Tools (Secondary Translucent White Pill with Arrow) -->
        <a href="#sdks" class="group w-[90%] sm:w-64 h-13 py-2.5 inline-flex items-center justify-between bg-white/10 hover:bg-white/20 transition text-white text-sm font-semibold pl-6 pr-2 rounded-full shadow-lg border border-white/20 shrink-0">
          <span>View SDKs &amp; Tools</span>
          <span class="w-8 h-8 rounded-full bg-white text-[#0b2545] flex items-center justify-center font-bold shrink-0">
            <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M7 17L17 7M17 7H7M17 7V17"/></svg>
          </span>
        </a>
      </div>
    </div>
  </section>

  <!-- QUICK STATS BAR -->
  <section class="py-12 bg-mint/50 border-b border-brand-500/10">
    <div class="max-w-6xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
      <div class="reveal">
        <div class="text-2xl sm:text-3xl font-bold text-ink">&lt; 150ms</div>
        <div class="text-xs sm:text-sm text-ink/70 mt-1 font-medium">Webhook Latency</div>
      </div>
      <div class="reveal">
        <div class="text-2xl sm:text-3xl font-bold text-ink">15+</div>
        <div class="text-xs sm:text-sm text-ink/70 mt-1 font-medium">E-Commerce Plugins</div>
      </div>
      <div class="reveal">
        <div class="text-2xl sm:text-3xl font-bold text-ink">99.99%</div>
        <div class="text-xs sm:text-sm text-ink/70 mt-1 font-medium">API Uptime SLA</div>
      </div>
      <div class="reveal">
        <div class="text-2xl sm:text-3xl font-bold text-ink">Instant</div>
        <div class="text-xs sm:text-sm text-ink/70 mt-1 font-medium">Test Mode Sandbox</div>
      </div>
    </div>
  </section>

  <!-- E-COMMERCE & CMS PLUGINS SECTION -->
  <section id="plugins" class="py-20 sm:py-28 px-6 bg-white">
    <div class="max-w-6xl mx-auto">
      <div class="text-center max-w-2xl mx-auto mb-16">
        <div class="reveal badge-pill mb-4">
          <span class="badge-dot"></span> Plug &amp; Play Integration
        </div>
        <h2 class="reveal text-2xl sm:text-3xl md:text-[42px] font-bold tracking-[-0.03em] leading-tight text-ink">
          Connect your favorite store platforms in minutes
        </h2>
        <p class="reveal mt-4 text-ink/70 text-xs sm:text-sm md:text-base leading-relaxed">
          Zero complex code required. Download official Flowwithlit payment modules built specifically for major e-commerce ecosystems and start receiving Naira &amp; Crypto payments today.
        </p>
      </div>

      <div class="grid md:grid-cols-3 gap-7">
        
        <!-- WooCommerce / WordPress -->
        <div class="reveal bg-white border border-brand-500/10 rounded-3xl p-8 flex flex-col justify-between hover:border-blue-500/30 transition group">
          <div>
            <div class="w-12 h-12 rounded-2xl bg-brand-50 text-[#0b2545] flex items-center justify-center font-bold mb-6 group-hover:bg-[#0b2545] group-hover:text-white transition-colors">
              <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21.469 6.825c.84 1.537 1.318 3.3 1.318 5.175 0 3.979-2.156 7.456-5.363 9.325l3.295-9.527c.615-1.54.82-2.771.82-3.864 0-.405-.026-.78-.07-1.11m-7.981.105c.647-.03 1.232-.105 1.232-.105.582-.075.514-.93-.067-.899 0 0-1.755.135-2.88.135-1.064 0-2.85-.15-2.85-.15-.585-.03-.661.855-.075.885 0 0 .54.061 1.125.09l1.68 4.605-2.37 7.08L5.354 6.9c.649-.03 1.234-.1 1.234-.1.585-.075.516-.93-.065-.896 0 0-1.746.138-2.874.138-.2 0-.438-.008-.69-.015C4.911 3.15 8.235 1.215 12 1.215c2.809 0"/></svg>
            </div>
            <div class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-2">WordPress Plugin</div>
            <h3 class="text-xl font-bold text-ink mb-3 tracking-tight">WooCommerce Gateway</h3>
            <p class="text-ink/75 text-xs sm:text-sm leading-relaxed">
              Official WooCommerce extension supporting instant Naira transfers, card payments, and automatic crypto-to-fiat conversion right inside your checkout page.
            </p>
          </div>
          <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-between">
            <span class="text-xs font-semibold text-ink/60">One-click Install</span>
            <span class="text-xs font-bold text-[#0b2545] group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
              Download Plugin &rarr;
            </span>
          </div>
        </div>

        <!-- Shopify -->
        <div class="reveal bg-white border border-brand-500/10 rounded-3xl p-8 flex flex-col justify-between hover:border-blue-500/30 transition group">
          <div>
            <div class="w-12 h-12 rounded-2xl bg-brand-50 text-[#0b2545] flex items-center justify-center font-bold mb-6 group-hover:bg-[#0b2545] group-hover:text-white transition-colors">
              <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M15.337 23.979l7.216-1.561s-2.604-17.613-2.625-17.73c-.018-.116-.114-.192-.211-.192s-1.929-.136-1.929-.136-1.275-1.274-1.439-1.411c-.045-.037-.075-.057-.121-.074l-.914 21.104h.023zM11.71 11.305s-.81-.424-1.774-.424c-1.447 0-1.504.906-1.504 1.141 0 1.232 3.24 1.715 3.24 4.629 0 2.295-1.44 3.76-3.406 3.76-2.354 0-3.54-1.465-3.54-1.465l.646-2.086s1.245 1.066 2.28 1.066c.675 0 .975-.545.975-.932 0-1.619-2.654-1.694-2.654-4.359-.034-2.237 1.571-4.416 4.827-4.416 1.257 0 1.875.361 1.875.361l-.945 2.715-.02.01zM11.17.83c.136 0 .271.038.405.135-.984.465-2.064 1.639-2.508 3.992-.65"/></svg>
            </div>
            <div class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-2">Shopify App</div>
            <h3 class="text-xl font-bold text-ink mb-3 tracking-tight">Shopify Payment App</h3>
            <p class="text-ink/75 text-xs sm:text-sm leading-relaxed">
              Seamlessly connect Flowwithlit to your Shopify storefront. Enable smooth checkout with real-time order fulfillment and auto-settlement.
            </p>
          </div>
          <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-between">
            <span class="text-xs font-semibold text-ink/60">App Store Verified</span>
            <span class="text-xs font-bold text-[#0b2545] group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
              Connect Store &rarr;
            </span>
          </div>
        </div>

        <!-- Wix -->
        <div class="reveal bg-white border border-brand-500/10 rounded-3xl p-8 flex flex-col justify-between hover:border-blue-500/30 transition group">
          <div>
            <div class="w-12 h-12 rounded-2xl bg-brand-50 text-[#0b2545] flex items-center justify-center font-bold mb-6 group-hover:bg-[#0b2545] group-hover:text-white transition-colors">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <div class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-2">Wix Market</div>
            <h3 class="text-xl font-bold text-ink mb-3 tracking-tight">Wix eCommerce</h3>
            <p class="text-ink/75 text-xs sm:text-sm leading-relaxed">
              Accept African &amp; global payments on Wix stores. Easy installation without writing a single line of backend code.
            </p>
          </div>
          <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-between">
            <span class="text-xs font-semibold text-ink/60">No Code Required</span>
            <span class="text-xs font-bold text-[#0b2545] group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
              Add to Wix &rarr;
            </span>
          </div>
        </div>

        <!-- Squarespace -->
        <div class="reveal bg-white border border-brand-500/10 rounded-3xl p-8 flex flex-col justify-between hover:border-blue-500/30 transition group">
          <div>
            <div class="w-12 h-12 rounded-2xl bg-brand-50 text-[#0b2545] flex items-center justify-center font-bold mb-6 group-hover:bg-[#0b2545] group-hover:text-white transition-colors">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
            </div>
            <div class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-2">Squarespace</div>
            <h3 class="text-xl font-bold text-ink mb-3 tracking-tight">Squarespace Extension</h3>
            <p class="text-ink/75 text-xs sm:text-sm leading-relaxed">
              Embed Flowwithlit's smart checkout modal directly into Squarespace sites for instant payments, invoice links, and digital downloads.
            </p>
          </div>
          <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-between">
            <span class="text-xs font-semibold text-ink/60">Embed Widget</span>
            <span class="text-xs font-bold text-[#0b2545] group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
              View Guide &rarr;
            </span>
          </div>
        </div>

        <!-- Magento / Adobe Commerce -->
        <div class="reveal bg-white border border-brand-500/10 rounded-3xl p-8 flex flex-col justify-between hover:border-blue-500/30 transition group">
          <div>
            <div class="w-12 h-12 rounded-2xl bg-brand-50 text-[#0b2545] flex items-center justify-center font-bold mb-6 group-hover:bg-[#0b2545] group-hover:text-white transition-colors">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            </div>
            <div class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-2">Adobe Commerce</div>
            <h3 class="text-xl font-bold text-ink mb-3 tracking-tight">Magento Enterprise</h3>
            <p class="text-ink/75 text-xs sm:text-sm leading-relaxed">
              Enterprise-grade payment extension built for high-volume Magento merchants handling complex multi-currency transactions.
            </p>
          </div>
          <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-between">
            <span class="text-xs font-semibold text-ink/60">High Volume Ready</span>
            <span class="text-xs font-bold text-[#0b2545] group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
              Get Extension &rarr;
            </span>
          </div>
        </div>

        <!-- Custom Webhooks & REST APIs -->
        <div class="reveal bg-white border border-brand-500/10 rounded-3xl p-8 flex flex-col justify-between hover:border-blue-500/30 transition group">
          <div>
            <div class="w-12 h-12 rounded-2xl bg-brand-50 text-[#0b2545] flex items-center justify-center font-bold mb-6 group-hover:bg-[#0b2545] group-hover:text-white transition-colors">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
            </div>
            <div class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-2">GoDaddy &amp; Custom Stacks</div>
            <h3 class="text-xl font-bold text-ink mb-3 tracking-tight">Universal Payment Links &amp; APIs</h3>
            <p class="text-ink/75 text-xs sm:text-sm leading-relaxed">
              Building on GoDaddy, Webflow, or custom backend code? Generate instant payment links or plug into our REST API in minutes.
            </p>
          </div>
          <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-between">
            <span class="text-xs font-semibold text-ink/60">Universal Compatibility</span>
            <span class="text-xs font-bold text-[#0b2545] group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
              View API Specs &rarr;
            </span>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- DEVELOPER TOOLS & OFFICIAL LIBRARIES -->
  <section id="sdks" class="py-20 sm:py-28 px-6 bg-mint/40 border-t border-b border-brand-500/10">
    <div class="max-w-6xl mx-auto">
      <div class="text-center max-w-2xl mx-auto mb-16">
        <div class="reveal badge-pill mb-4">
          <span class="badge-dot"></span> SDKs &amp; Libraries
        </div>
        <h2 class="reveal text-2xl sm:text-3xl md:text-[42px] font-bold tracking-[-0.03em] leading-tight text-ink">
          Official client libraries for your favorite languages
        </h2>
        <p class="reveal mt-4 text-ink/70 text-xs sm:text-sm md:text-base leading-relaxed">
          Type-safe, production-tested SDKs that make initiating swaps, verifying webhooks, and managing virtual bank accounts effortless.
        </p>
      </div>

      <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-6">
        
        <!-- Row 1: Node.js -->
        <div class="reveal bg-white border border-brand-500/10 rounded-3xl p-6 flex flex-col justify-between">
          <div>
            <div class="w-10 h-10 rounded-xl bg-brand-50 text-[#0b2545] flex items-center justify-center font-bold mb-4">
              <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M11.998,24c-0.321,0-0.641-0.084-0.922-0.247l-2.936-1.737c-0.438-0.245-0.224-0.332-0.08-0.383 c0.585-0.203,0.703-0.25,1.328-0.604c0.065-0.037,0.151-0.023,0.218,0.017l2.256,1.339c0.082,0.045,0.197,0.045,0.272,0l8.795-5.076 c0.082-0.047,0.134-0.141,0.134-0.238V6.921c0-0.099-0.053-0.192-0.137-0.242l-8.791-5.072c-0.081-0.047-0.189-0.047-0.271,0 L3.075,6.68C2.99,6.729,2.936,6.825,2.936,6.921v10.15c0,0.097,0.054,0.189,0.139,0.235l2.409,1.392 c1.307,0.654,2.108-0.116,2.108-0.89V7.787c0-0.142,0.114-0.253,0.256-0.253h1.115c0.139,0,0.255,0.112,0.255,0.253v10.021 c0,1.745-0.95,2.745-2.604,2.745c-0.508,0-0.909,0-2.026-0.551L2.28,18.675c-0.57-0.329-0.922-0.945-0.922-1.604V6.921 c0-0.659,0.353-1.275,0.922-1.603l8.795-5"/></svg>
            </div>
            <div class="font-bold text-ink text-lg mb-1">Node.js</div>
            <div class="text-xs text-blue-600 font-semibold mb-3">@flowwithlit/node</div>
            <p class="text-ink/70 text-xs leading-relaxed">Full TypeScript support, auto-retry logic, and built-in webhook signature verification middleware.</p>
          </div>
          <div class="mt-6 text-[11px] font-bold text-[#0b2545] border-t border-gray-100 pt-3">npm i @flowwithlit/node</div>
        </div>

        <!-- Row 1: Python -->
        <div class="reveal bg-white border border-brand-500/10 rounded-3xl p-6 flex flex-col justify-between">
          <div>
            <div class="w-10 h-10 rounded-xl bg-brand-50 text-[#0b2545] flex items-center justify-center font-bold mb-4">
              <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M14.25.18l.9.2.73.26.59.3.45.32.34.34.25.34.16.33.1.3.04.26.02.2-.01.13V8.5l-.05.63-.13.55-.21.46-.26.38-.3.31-.33.25-.35.19-.35.14-.33.1-.3.07-.26.04-.21.02H8.77l-.69.05-.59.14-.5.22-.41.27-.33.32-.27.35-.2.36-.15.37-.1.35-.07.32-.04.27-.02.21v3.06H3.17l-.21-.03-.28-.07-.32-.12-.35-.18-.36-.26-.36-.36-.35-.46-.32-.59-.28-.73-.21-.88-.14-1.05-.05-1.23.06-1.22.16-1.04.24-.87.32-.71.36-.57.4-.44.42-.33.42-.24.4-.16.36-.1.32-.05.24-.01h.16l.06.01h8.16v-.83H6.18l-.01-2.75-.02-.37.05-.34.11-.31.17-.28.25-.26.31-.23.38-.2.44-.18.51-.15.58-.12.64-.1.71-.06.77-.04.84-.02 1.27.05zm-6.3 1.98l-.23.33-.08.41.08.41.23.34.33.22.41.09.41-.09.33-."/></svg>
            </div>
            <div class="font-bold text-ink text-lg mb-1">Python</div>
            <div class="text-xs text-blue-600 font-semibold mb-3">flowwithlit-python</div>
            <p class="text-ink/70 text-xs leading-relaxed">Async/await native client with Django and FastAPI payment webhook wrappers.</p>
          </div>
          <div class="mt-6 text-[11px] font-bold text-[#0b2545] border-t border-gray-100 pt-3">pip install flowwithlit</div>
        </div>

        <!-- Row 1: PHP / Laravel -->
        <div class="reveal bg-white border border-brand-500/10 rounded-3xl p-6 flex flex-col justify-between">
          <div>
            <div class="w-10 h-10 rounded-xl bg-brand-50 text-[#0b2545] flex items-center justify-center font-bold mb-4">
              <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M23.642 5.43a.364.364 0 01.014.1v5.149c0 .135-.073.26-.189.326l-4.323 2.49v4.934a.378.378 0 01-.188.326L9.93 23.949a.316.316 0 01-.066.027c-.008.002-.016.008-.024.01a.348.348 0 01-.192 0c-.011-.002-.02-.008-.03-.012-.02-.008-.042-.014-.062-.025L.533 18.755a.376.376 0 01-.189-.326V2.974c0-.033.005-.066.014-.098.003-.012.01-.02.014-.032a.369.369 0 01.023-.058c.004-.013.015-.022.023-.033l.033-.045c.012-.01.025-.018.037-.027.014-.012.027-.024.041-.034H.53L5.043.05a.375.375 0 01.375 0L9.93 2.647h.002c.015.01.027.021.04.033l.038.027c.013.014.02.03.033.045.008.011.02.021.025.033.01.02.017.038.024.058.003.011.01.021.013.032.01.031.014.064.014.098v9.652l3.76-2.164V5.527c0-.033.004-.066.013-.098.003-.01.01-.02.013-.032a.487.487 0 01.024-.059c.007-.012.018-.02.02"/></svg>
            </div>
            <div class="font-bold text-ink text-lg mb-1">PHP / Laravel</div>
            <div class="text-xs text-blue-600 font-semibold mb-3">flowwithlit/laravel</div>
            <p class="text-ink/70 text-xs leading-relaxed">Composer package with Laravel service provider, facades, and Artisan webhook listeners.</p>
          </div>
          <div class="mt-6 text-[11px] font-bold text-[#0b2545] border-t border-gray-100 pt-3">composer require flowwithlit/sdk</div>
        </div>

        <!-- Row 1: Flutter & Mobile -->
        <div class="reveal bg-white border border-brand-500/10 rounded-3xl p-6 flex flex-col justify-between">
          <div>
            <div class="w-10 h-10 rounded-xl bg-brand-50 text-[#0b2545] flex items-center justify-center font-bold mb-4">
              <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M14.314 0L2.3 12 6 15.7 21.684.013h-7.357zm.014 11.072L7.8 17.587l4.314 4.313h7.357L11.77 14.2l2.558-3.128z"/></svg>
            </div>
            <div class="font-bold text-ink text-lg mb-1">Flutter &amp; Mobile</div>
            <div class="text-xs text-blue-600 font-semibold mb-3">flowwithlit_flutter</div>
            <p class="text-ink/70 text-xs leading-relaxed">Cross-platform mobile payment modal widget for iOS and Android apps with zero native setup.</p>
          </div>
          <div class="mt-6 text-[11px] font-bold text-[#0b2545] border-t border-gray-100 pt-3">flutter pub add flowwithlit</div>
        </div>

        <!-- Row 2: Java / Spring Boot -->
        <div class="reveal bg-white border border-brand-500/10 rounded-3xl p-6 flex flex-col justify-between">
          <div>
            <div class="w-10 h-10 rounded-xl bg-brand-50 text-[#0b2545] flex items-center justify-center font-bold mb-4">
              <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M11.915 0 11.7.215C9.515 2.4 7.47 6.39 6.046 10.483c-1.064 1.024-3.633 2.81-3.711 3.551-.093.87 1.746 2.611 1.55 3.235-.198.625-1.304 1.408-1.014 1.939.1.188.823.011 1.277-.491a13.389 13.389 0 0 0-.017 2.14c.076.906.27 1.668.643 2.232.372.563.956.911 1.667.911.397 0 .727-.114 1.024-.264.298-.149.571-.33.91-.5.68-.34 1.634-.666 3.53-.604 1.903.062 2.872.39 3.559.704.687.314 1.15.664 1.925.664.767 0 1.395-.336 1.807-.9.412-.563.631-1.33.72-2.24.06-.623.055-1.32 0-2.066.454.45 1.117.604 1.213.424.29-.53-.816-1.314-1.013-1.937-.198-.624 1.642-2.366 1.549-3.236-.08-.748-2.707-2.568-3.748-3.586C16.428 6.374 14.308 2.394 12.13.215zm.175 6.038a2.95 2.95 0 0 1 2.943 2.942 2.95 2.95 0 0 1-2.943 2.943A2.95 2.95 0 0 1 9.148 8.98a2.95 2.95 0 0 1 2.942-2.942zM8.685 7.983a3.515 3.515 0 0 0-.145.997c0 1"/></svg>
            </div>
            <div class="font-bold text-ink text-lg mb-1">Java / Spring</div>
            <div class="text-xs text-blue-600 font-semibold mb-3">flowwithlit-java</div>
            <p class="text-ink/70 text-xs leading-relaxed">Enterprise Java SDK with Maven/Gradle support and Spring Boot starter auto-configuration.</p>
          </div>
          <div class="mt-6 text-[11px] font-bold text-[#0b2545] border-t border-gray-100 pt-3">com.flowwithlit:sdk:1.0</div>
        </div>

        <!-- Row 2: GoLang -->
        <div class="reveal bg-white border border-brand-500/10 rounded-3xl p-6 flex flex-col justify-between">
          <div>
            <div class="w-10 h-10 rounded-xl bg-brand-50 text-[#0b2545] flex items-center justify-center font-bold mb-4">
              <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M1.811 10.231c-.047 0-.058-.023-.035-.059l.246-.315c.023-.035.081-.058.128-.058h4.172c.046 0 .058.035.035.07l-.199.303c-.023.036-.082.07-.117.07zM.047 11.306c-.047 0-.059-.023-.035-.058l.245-.316c.023-.035.082-.058.129-.058h5.328c.047 0 .07.035.058.07l-.093.28c-.012.047-.058.07-.105.07zm2.828 1.075c-.047 0-.059-.035-.035-.07l.163-.292c.023-.035.07-.07.117-.07h2.337c.047 0 .07.035.07.082l-.023.28c0 .047-.047.082-.082.082zm12.129-2.36c-.736.187-1.239.327-1.963.514-.176.046-.187.058-.34-.117-.174-.199-.303-.327-.548-.444-.737-.362-1.45-.257-2.115.175-.795.514-1.204 1.274-1.192 2.22.011.935.654 1.706 1.577 1.835.795.105 1.46-.175 1.987-.77.105-.13.198-.27.315-.434H10.47c-.245 0-.304-.152-.222-.35.152-.362.432-.97.596-1.274a.315"/></svg>
            </div>
            <div class="font-bold text-ink text-lg mb-1">GoLang</div>
            <div class="text-xs text-blue-600 font-semibold mb-3">flowwithlit-go</div>
            <p class="text-ink/70 text-xs leading-relaxed">High-concurrency Go module optimized for microservices, webhooks, and sub-second payment routes.</p>
          </div>
          <div class="mt-6 text-[11px] font-bold text-[#0b2545] border-t border-gray-100 pt-3">go get github.com/flowwithlit/go-sdk</div>
        </div>

        <!-- Row 2: Rust -->
        <div class="reveal bg-white border border-brand-500/10 rounded-3xl p-6 flex flex-col justify-between">
          <div>
            <div class="w-10 h-10 rounded-xl bg-brand-50 text-[#0b2545] flex items-center justify-center font-bold mb-4">
              <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M23.8346 11.7033l-1.0073-.6236a13.7268 13.7268 0 00-.0283-.2936l.8656-.8069a.3483.3483 0 00-.1154-.578l-1.1066-.414a8.4958 8.4958 0 00-.087-.2856l.6904-.9587a.3462.3462 0 00-.2257-.5446l-1.1663-.1894a9.3574 9.3574 0 00-.1407-.2622l.49-1.0761a.3437.3437 0 00-.0274-.3361.3486.3486 0 00-.3006-.154l-1.1845.0416a6.7444 6.7444 0 00-.1873-.2268l.2723-1.153a.3472.3472 0 00-.417-.4172l-1.1532.2724a14.0183 14.0183 0 00-.2278-.1873l.0415-1.1845a.3442.3442 0 00-.49-.328l-1.076.491c-.0872-.0476-.1742-.0952-.2623-.1407l-.1903-1.1673A.3483.3483 0 0016.256.955l-.9597.6905a8.4867 8.4867 0 00-.2855-.086l-.414-1.1066a.3483.3483 0 00-.5781-.1154l-.8069.8666a9.2936 9.2936 0 00-.2936-.0284L12.2946.1683a.3462.3462 0 00-.5892 0l-.6236 1.0073a13.7383 13.7383 0 00-.2936.0284L9.9803.3374a.3462.3462 0 00-.578.1154l-.4141 1.1065c-.0962.0274-.1903.0567-.2855.086L7.744.955a.3483.3483 0 00-.5447.2258L7.009 2.348a9.3574 9.3574 0 00-.2622.1407l-1.0762-.491a.3462.3462 0 00-.49.328l.0416 1.1845a7.9826 7.9826 0 00-.2278.1873L3.8413 3.425a.3472.3472 0 00-.4171.4171l.2713 1.1531c-.0628.075-.1255.1509-.1863.2268l-1.1845-.0415a.3462.3462 0 00-.328.49l.491 1.0761a9.167 9.167 0 00-.1407.2622l-1.1662.1894a.3483.3483 0 00-.2258.5446l.6904.9587a13.303 13.303 0 00-.087.2855l-1.1065.414a.3483.3483 0 00-.1155.5781l.8656.807a9.2936 9.2936 0 00-.0283.2935l-1.0073.6236a.3442.3442 0 000 .5892l1.0073.6236c.008.0982.0182.1964.0283.2936l-.8656.8079a.3462.3462 0 00.1155.578l1.1065.4141c.0273.0962.0567.1914.087.2855l-.6904.9587a13.303 13.303 0 00.2268.5447l1.1662.1893c.0456.088.0922.1751.1408.2622l-.491 1.0762a.3462.3462 0 00.328.49l1.1834-.0415c.0618.0769.1235.1528.1873.2277l-.2713 1.1541a.3462.3462 0 00.4171.4161l1.1"/></svg>
            </div>
            <div class="font-bold text-ink text-lg mb-1">Rust</div>
            <div class="text-xs text-blue-600 font-semibold mb-3">flowwithlit-rust</div>
            <p class="text-ink/70 text-xs leading-relaxed">Memory-safe, zero-cost abstraction Crate for ultra-high throughput payment processing engines.</p>
          </div>
          <div class="mt-6 text-[11px] font-bold text-[#0b2545] border-t border-gray-100 pt-3">cargo add flowwithlit</div>
        </div>

        <!-- Row 2: C# / .NET -->
        <div class="reveal bg-white border border-brand-500/10 rounded-3xl p-6 flex flex-col justify-between">
          <div>
            <div class="w-10 h-10 rounded-xl bg-brand-50 text-[#0b2545] flex items-center justify-center font-bold mb-4">
              <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M24 8.77h-2.468v7.565h-1.425V8.77h-2.462V7.53H24zm-6.852 7.565h-4.821V7.53h4.63v1.24h-3.205v2.494h2.953v1.234h-2.953v2.604h3.396zm-6.708 0H8.882L4.78 9.863a2.896 2.896 0 0 1-.258-.51h-.036c.032.189.048.592.048 1."/></svg>
            </div>
            <div class="font-bold text-ink text-lg mb-1">C# / .NET</div>
            <div class="text-xs text-blue-600 font-semibold mb-3">Flowwithlit.NET</div>
            <p class="text-ink/70 text-xs leading-relaxed">NuGet package with ASP.NET Core middleware, dependency injection, and async client routines.</p>
          </div>
          <div class="mt-6 text-[11px] font-bold text-[#0b2545] border-t border-gray-100 pt-3">dotnet add package Flowwithlit</div>
        </div>

      </div>
    </div>
  </section>

  <!-- DEVELOPER EXPERIENCE (DX) PILLARS -->
  <section class="py-20 sm:py-28 px-6 bg-white">
    <div class="max-w-6xl mx-auto">
      <div class="text-center max-w-xl mx-auto mb-16">
        <div class="reveal badge-pill mb-4">
          <span class="badge-dot"></span> Developer Experience
        </div>
        <h2 class="reveal text-2xl sm:text-3xl md:text-[40px] font-bold tracking-[-0.03em] leading-tight text-ink">
          Engineered for developer happiness
        </h2>
        <p class="reveal mt-3.5 text-ink/70 text-xs sm:text-sm md:text-base leading-relaxed">
          Everything you need to test, launch, and scale your financial backend smoothly.
        </p>
      </div>

      <div class="grid md:grid-cols-3 gap-8">
        
        <div class="reveal bg-white border border-brand-500/10 rounded-3xl p-8 flex flex-col justify-between">
          <div>
            <div class="w-10 h-10 rounded-xl bg-brand-50 text-[#0b2545] flex items-center justify-center font-bold mb-6">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            </div>
            <h3 class="text-xl font-bold text-ink mb-3 tracking-tight">Sub-Second Webhooks</h3>
            <p class="text-ink/75 text-xs sm:text-sm leading-relaxed">
              Get notified the exact millisecond a customer completes a Naira transfer or crypto transaction. Built-in HMAC SHA-256 signature verification guarantees payload authenticity.
            </p>
          </div>
        </div>

        <div class="reveal bg-white border border-brand-500/10 rounded-3xl p-8 flex flex-col justify-between">
          <div>
            <div class="w-10 h-10 rounded-xl bg-brand-50 text-[#0b2545] flex items-center justify-center font-bold mb-6">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
            </div>
            <h3 class="text-xl font-bold text-ink mb-3 tracking-tight">Instant Sandbox</h3>
            <p class="text-ink/75 text-xs sm:text-sm leading-relaxed">
              Test your full checkout and settlement flow without using real money. Toggle between Test Mode and Live Mode with a single API key header swap.
            </p>
          </div>
        </div>

        <div class="reveal bg-white border border-brand-500/10 rounded-3xl p-8 flex flex-col justify-between">
          <div>
            <div class="w-10 h-10 rounded-xl bg-brand-50 text-[#0b2545] flex items-center justify-center font-bold mb-6">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-ink mb-3 tracking-tight">Idempotency &amp; Resilience</h3>
            <p class="text-ink/75 text-xs sm:text-sm leading-relaxed">
              Safely retry requests without double-charging users. Idempotency keys prevent duplicate transaction execution during temporary network hiccups.
            </p>
          </div>
        </div>

      </div>
    </div>
  </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
