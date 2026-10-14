<?php
$pageTitle = 'AML & KYC Compliance Policy — Flowwithlit';
$pageDesc  = 'Framework governing Anti-Money Laundering, Know Your Customer identity verification, and international sanctions screening.';
$activePage = 'aml-kyc-policy';
require_once __DIR__ . '/includes/header.php';
?>

  <!-- HERO SECTION -->
  <section class="hero-bg text-white pt-36 pb-20 px-6 text-center">
    <div class="max-w-4xl mx-auto">
      <div class="reveal badge-pill bg-white/10 text-white border-white/20 mb-4 inline-flex">
        <span class="badge-dot bg-white"></span> Global Regulatory Governance
      </div>
      <h1 class="reveal text-3xl sm:text-5xl font-bold tracking-tight">AML &amp; KYC Compliance Policy</h1>
      <p class="reveal mt-4 text-white/80 text-sm sm:text-base max-w-2xl mx-auto">
        Last Updated: September 17, 2026. Framework governing Anti-Money Laundering, Know Your Customer identity verification, and international sanctions screening.
      </p>
    </div>
  </section>

  <!-- CONTENT SECTION -->
  <section class="py-16 sm:py-24 px-6 bg-white">
    <div class="max-w-4xl mx-auto">
      
      <!-- Plain English Summary Box -->
      <div class="reveal bg-mint border border-brand-500/10 rounded-2xl p-6 sm:p-8 mb-12 space-y-3">
        <div class="text-xs font-bold uppercase tracking-wider text-blue-600">Plain English Summary</div>
        <h2 class="text-lg font-bold text-ink">Identity Verification &amp; Financial Safety</h2>
        <p class="text-ink/80 text-xs sm:text-sm leading-relaxed text-justify">
          To protect Flowwithlit users and maintain international financial compliance, we verify the identity of all account holders. Depending on your transaction volume, we require official government identification, BVN/NIN verification, or corporate registration documents. We screen transactions against global sanctions lists to prevent money laundering and terrorism financing.
        </p>
      </div>

      <!-- Legal Text Clauses -->
      <div class="space-y-10 text-justify text-ink/85 text-xs sm:text-sm leading-relaxed">
        
        <div>
          <h2 class="text-xl font-bold text-ink mb-3">1. Regulatory Commitment &amp; Legislative Framework</h2>
          <p>
            Flowwithlit is committed to strict adherence with global anti-money laundering (AML) and counter-terrorist financing (CTF) regulations. Our compliance architecture incorporates guidelines established by the Financial Action Task Force (FATF), the Money Laundering (Prevention and Prohibition) Act of Nigeria, the US Treasury Office of Foreign Assets Control (OFAC), EU Anti-Money Laundering Directives (AMLD), and local financial intelligence units (FIUs).
          </p>
        </div>

        <div>
          <h2 class="text-xl font-bold text-ink mb-3">2. Tiered Know Your Customer (KYC) Verification</h2>
          <p>
            Flowwithlit enforces a risk-based, tiered identity verification model to ensure customer due diligence (CDD) prior to processing transactions:
          </p>
          <ul class="list-disc pl-5 mt-3 space-y-2">
            <li><strong>Tier 1 (Basic):</strong> Verified email address, phone number, and basic demographic data. Daily swap limits apply.</li>
            <li><strong>Tier 2 (Standard):</strong> Verification of government identification numbers (BVN/NIN/National ID/International Passport) and selfie liveness check.</li>
            <li><strong>Tier 3 (Enterprise &amp; Merchant):</strong> Proof of physical address (utility bill/bank statement within 90 days), corporate registration certificate (CAC/incorporation papers), Tax Identification Number (TIN), and Ultimate Beneficial Ownership (UBO) declaration.</li>
          </ul>
        </div>

        <div>
          <h2 class="text-xl font-bold text-ink mb-3">3. Sanctions &amp; PEP Screening</h2>
          <p>
            All users, account holders, and settlement bank accounts are screened against international sanctions lists, including OFAC, UN Security Council, EU, UK Treasury (HMT), and Politically Exposed Persons (PEPs) databases. Transactions originating from or destined to sanctioned jurisdictions or blocked individuals are automatically restricted and flagged.
          </p>
        </div>

        <div>
          <h2 class="text-xl font-bold text-ink mb-3">4. Transaction Monitoring &amp; Suspicious Activity Reporting (SAR)</h2>
          <p>
            Flowwithlit employs automated machine-learning transaction monitoring systems to detect suspicious patterns, rapid velocity swaps, structuring (smurfing), or unusual transactional spikes. Where suspicious activity is identified, Flowwithlit will submit Suspicious Transaction Reports (STR/SAR) to the appropriate statutory financial intelligence agencies without prior notification to the user ("tipping off" prohibition).
          </p>
        </div>

        <div>
          <h2 class="text-xl font-bold text-ink mb-3">5. Statutory Record Retention</h2>
          <p>
            In accordance with international financial standards, Flowwithlit maintains all customer identity documentation, verification records, and complete transaction histories for a minimum of 5 to 7 years post-account closure.
          </p>
        </div>

        <div>
          <h2 class="text-xl font-bold text-ink mb-3">6. Compliance Officer Contact</h2>
          <p>
            For regulatory inquiries or compliance audits, contact our Chief Compliance Officer at <strong>aml-compliance@flowwithlit.com</strong>.
          </p>
        </div>

      </div>
    </div>
  </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
