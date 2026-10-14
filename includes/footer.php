<?php
/**
 * Flowwithlit Global Footer Component
 * Ultra-fast PHP inclusion with zero layout shift & zero extra client HTTP requests.
 */
?>

  <!-- COMBINED CTA BANNER & FOOTER WITH LANDSCAPE BACKGROUND -->
  <section class="nature-card-bg relative pt-20 sm:pt-24 pb-16 px-6 text-white overflow-hidden">
    <!-- CTA Banner Container -->
    <div id="contact-banner" class="max-w-3xl mx-auto text-center pb-20">
      <h2 class="reveal text-white text-3xl sm:text-4xl md:text-[46px] font-bold tracking-[-0.03em] leading-tight">
        Ready to start flowing with<br class="hidden sm:block"/> smooth payments today?
      </h2>
      <p class="reveal mt-5 text-white/90 text-xs sm:text-sm md:text-base max-w-lg mx-auto leading-relaxed">
        Join thousands of people and sellers using Flowwithlit for easy Crypto, Naira, and instant transfers.
      </p>
      <div class="reveal mt-8 flex justify-center">
        <a href="register" class="group inline-flex items-center gap-3 bg-white hover:bg-[#eef4ff] transition-all duration-300 text-[#0b2545] text-sm font-semibold pl-5 pr-1.5 py-1.5 rounded-full shadow-lg border border-white/20">
          <span>Create Free Account</span>
          <span class="w-7 h-7 rounded-full bg-[#0b2545] text-white flex items-center justify-center font-bold">
            <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M7 17L17 7M17 7H7M17 7V17"/></svg>
          </span>
        </a>
      </div>
    </div>

    <!-- FOOTER NAV & SUBSCRIPTION -->
    <div id="blog" class="max-w-6xl mx-auto pt-16 border-t border-white/20 grid md:grid-cols-12 gap-10">
      <!-- Left Column: Logo + Text + Email Pill Form -->
      <div class="reveal md:col-span-6">
        <a href="index" class="inline-flex items-center mb-4">
          <img src="logo/flowwithlit-wordmark.png" alt="Flowwithlit" class="h-10 sm:h-11 w-auto object-contain" />
        </a>
        <p class="text-white/80 text-sm sm:text-base leading-relaxed max-w-md">
          The all-in-one payment gateway for seamless crypto &amp; naira banking, transfers, and e-commerce payments.
        </p>

        <!-- White Pill Email Subscription Input Box -->
        <form class="mt-6 bg-white/10 backdrop-blur-md rounded-full p-1.5 pl-6 border border-white/25 flex items-center justify-between shadow-lg max-w-md" onsubmit="event.preventDefault(); alert('Thank you for subscribing to Flowwithlit updates!');">
          <input type="email" required placeholder="Enter your email" class="bg-transparent border-none text-white placeholder-white/60 text-sm focus:outline-none flex-1 pr-2" />
          <button type="submit" class="bg-white hover:bg-gray-100 transition text-[#0b2545] text-sm font-semibold pl-5 pr-1.5 py-1.5 rounded-full inline-flex items-center gap-2.5 shadow-sm">
            <span>Subscribe</span>
            <span class="w-7 h-7 rounded-full bg-[#0b2545] flex items-center justify-center text-white font-bold">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M7 17L17 7M17 7H7M17 7V17"/></svg>
            </span>
          </button>
        </form>
      </div>

      <!-- Right Columns: Links -->
      <div class="reveal md:col-span-6 grid grid-cols-3 gap-8">
        <div>
          <h4 class="font-bold text-white text-base mb-4">Product</h4>
          <ul class="space-y-3 text-sm text-white/80 font-medium">
            <li><a href="index#features" class="hover:text-white transition">Features</a></li>
            <li><a href="developers" class="hover:text-white transition">Developers</a></li>
            <li><a href="index#security" class="hover:text-white transition">Security</a></li>
            <li><a href="index#integrations-section" class="hover:text-white transition">Integrations</a></li>
          </ul>
        </div>

        <div>
          <h4 class="font-bold text-white text-base mb-4">Company</h4>
          <ul class="space-y-3 text-sm text-white/80 font-medium">
            <li><a href="about" class="hover:text-white transition">About Us</a></li>
            <li><a href="contact" class="hover:text-white transition">Contact Us</a></li>
            <li><a href="index#faq" class="hover:text-white transition">FAQ</a></li>
          </ul>
        </div>

        <div>
          <h4 class="font-bold text-white text-base mb-4">Legal &amp; Policy</h4>
          <ul class="space-y-2.5 text-xs text-white/80 font-medium">
            <li><a href="terms" class="hover:text-white transition">Terms of Service</a></li>
            <li><a href="privacy" class="hover:text-white transition">Privacy Policy</a></li>
            <li><a href="refund-policy" class="hover:text-white transition">Refund Policy</a></li>
            <li><a href="cookie-policy" class="hover:text-white transition">Cookie Policy</a></li>
            <li><a href="security-policy" class="hover:text-white transition">Security Policy</a></li>
            <li><a href="aml-kyc-policy" class="hover:text-white transition">AML / KYC Policy</a></li>
            <li><a href="acceptable-use" class="hover:text-white transition">Acceptable Use</a></li>
            <li><a href="sla" class="hover:text-white transition">SLA Agreement</a></li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const header = document.getElementById('main-header');
      const logo = document.getElementById('header-logo');
      const navLinks = document.getElementById('header-nav-links');
      const mobileBtn = document.getElementById('mobile-menu-btn');
      const mobileOverlay = document.getElementById('mobile-menu-overlay');
      const iconOpen = document.getElementById('menu-icon-open');
      const iconClose = document.getElementById('menu-icon-close');
      const loginBtn = document.getElementById('header-login-btn');

      let isMobileMenuOpen = false;

      function updateHeaderOnScroll() {
        if (window.scrollY > 20) {
          header.classList.add('bg-white/95', 'backdrop-blur-md', 'shadow-sm', 'border-b', 'border-gray-100', 'py-1');
          header.classList.remove('py-3', 'py-5');
          if (!isMobileMenuOpen) {
            logo.classList.remove('text-white');
            logo.classList.add('text-[#0b2545]');
            if (navLinks) {
              navLinks.classList.remove('text-white/90');
              navLinks.classList.add('text-[#0b2545]');
            }
            mobileBtn.classList.remove('text-white');
            mobileBtn.classList.add('text-[#0b2545]');
          }
        } else {
          header.classList.remove('bg-white/95', 'backdrop-blur-md', 'shadow-sm', 'border-b', 'border-gray-100', 'py-1');
          header.classList.add('py-3');
          if (!isMobileMenuOpen) {
            logo.classList.add('text-white');
            logo.classList.remove('text-[#0b2545]');
            if (navLinks) {
              navLinks.classList.add('text-white/90');
              navLinks.classList.remove('text-[#0b2545]');
            }
            mobileBtn.classList.add('text-white');
            mobileBtn.classList.remove('text-[#0b2545]');
          }
        }
      }

      window.addEventListener('scroll', updateHeaderOnScroll);
      updateHeaderOnScroll();

      // Mobile Menu Toggle
      function toggleMobileMenu() {
        isMobileMenuOpen = !isMobileMenuOpen;
        if (isMobileMenuOpen) {
          mobileOverlay.classList.remove('-translate-y-full');
          mobileOverlay.classList.add('translate-y-0');
          iconOpen.classList.add('hidden');
          iconClose.classList.remove('hidden');
          logo.classList.remove('text-white');
          logo.classList.add('text-[#0b2545]');
          mobileBtn.classList.remove('text-white');
          mobileBtn.classList.add('text-[#0b2545]');
          document.body.classList.add('overflow-hidden');
        } else {
          mobileOverlay.classList.remove('translate-y-0');
          mobileOverlay.classList.add('-translate-y-full');
          iconOpen.classList.remove('hidden');
          iconClose.classList.add('hidden');
          document.body.classList.remove('overflow-hidden');
          updateHeaderOnScroll();
        }
      }

      if (mobileBtn) {
        mobileBtn.addEventListener('click', toggleMobileMenu);
      }

      document.querySelectorAll('.mobile-nav-link').forEach(link => {
        link.addEventListener('click', () => {
          if (isMobileMenuOpen) toggleMobileMenu();
        });
      });

      // Scroll Reveal Animations
      const reveals = document.querySelectorAll('.reveal');
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('in');
          }
        });
      }, { threshold: 0.1 });

      reveals.forEach(el => observer.observe(el));
    });
  </script>
</body>
</html>
