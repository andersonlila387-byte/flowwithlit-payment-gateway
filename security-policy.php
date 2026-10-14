<?php
$pageTitle = 'Security Policy — Flowwithlit';
$pageDesc  = 'How Flowwithlit engineers system resilience, multi-layer encryption, and responsible vulnerability disclosure.';
$activePage = 'security-policy';
require_once __DIR__ . '/includes/header.php';
?>

  <!-- HERO SECTION -->
  <section class="hero-bg text-white pt-36 pb-20 px-6 text-center">
    <div class="max-w-4xl mx-auto">
      <div class="reveal badge-pill bg-white/10 text-white border-white/20 mb-4 inline-flex">
        <span class="badge-dot bg-white"></span> Infrastructure &amp; Trust
      </div>
      <h1 class="reveal text-3xl sm:text-5xl font-bold tracking-tight">Security Policy</h1>
      <p class="reveal mt-4 text-white/80 text-sm sm:text-base max-w-2xl mx-auto">
        Last Updated: September 17, 2026. How Flowwithlit engineers system resilience, multi-layer encryption, and responsible vulnerability disclosure.
      </p>
    </div>
  </section>

  <!-- CONTENT SECTION -->
  <section class="py-16 sm:py-24 px-6 bg-white">
    <div class="max-w-4xl mx-auto">
      
      <!-- Plain English Summary Box -->
      <div class="reveal bg-mint border border-brand-500/10 rounded-2xl p-6 sm:p-8 mb-12 space-y-3">
        <div class="text-xs font-bold uppercase tracking-wider text-blue-600">Plain English Summary</div>
        <h2 class="text-lg font-bold text-ink">System Security &amp; Protection Overview</h2>
        <p class="text-ink/80 text-xs sm:text-sm leading-relaxed text-justify">
          Flowwithlit enforces defense-in-depth security standards across all services. We use AES-256 encryption for data at rest, TLS 1.3 for data in transit, automated web application firewalls (WAF), real-time fraud monitoring, and isolated multi-signature cold wallets. Security researchers can safely report vulnerabilities directly to our team via our Responsible Disclosure program.
        </p>
      </div>

      <!-- Legal Text Clauses -->
      <div class="space-y-10 text-justify text-ink/85 text-xs sm:text-sm leading-relaxed">
        
        <div>
          <h2 class="text-xl font-bold text-ink mb-3">1. Cryptographic Controls &amp; Data Encryption</h2>
          <p>
            All sensitive customer data, API keys, database records, and authentication tokens processed by Flowwithlit are encrypted at rest using AES-256-GCM symmetric key encryption. In-transit communication across user applications, merchant checkout widgets, and internal banking microservices is enforced over HTTPS utilizing mandatory TLS 1.3 encryption protocols.
          </p>
        </div>

        <div>
          <h2 class="text-xl font-bold text-ink mb-3">2. Infrastructure Resilience &amp; Access Controls</h2>
          <p>
            Flowwithlit infrastructure operates on isolated Virtual Private Cloud (VPC) environments with zero direct public access to database servers. Application access is strictly governed by the Principle of Least Privilege (PoLP) and identity-based access management (IAM) requiring mandatory hardware multi-factor authentication (MFA).
          </p>
        </div>

        <div>
          <h2 class="text-xl font-bold text-ink mb-3">3. Digital Asset &amp; Treasury Custody Architecture</h2>
          <p>
            Digital asset reserves handled by Flowwithlit utilize multi-signature threshold cryptography (MPC/Multi-Sig). Over 95% of asset liquidity is stored in air-gapped, distributed cold custody solutions. Hot wallet operational funds are governed by automated algorithmic limits to mitigate exposure to zero-day external threats.
          </p>
        </div>

        <div>
          <h2 class="text-xl font-bold text-ink mb-3">4. Application Security &amp; Continuous Auditing</h2>
          <p>
            Our core payment engine, webhooks, and REST APIs undergo continuous vulnerability scanning, static application security testing (SAST), and independent third-party penetration testing. Automated Web Application Firewalls (WAF) actively block Distributed Denial of Service (DDoS) attempts, SQL injection, cross-site scripting (XSS), and automated credential stuffing.
          </p>
        </div>

        <div>
          <h2 class="text-xl font-bold text-ink mb-3">5. Responsible Vulnerability Disclosure Program</h2>
          <p>
            Flowwithlit values the security research community. If you discover a security flaw, vulnerability, or potential exploit in our systems, we encourage you to report it responsibly. Please email detailed steps to reproduce to <strong>security@flowwithlit.com</strong>.
          </p>
          <ul class="list-disc pl-5 mt-2 space-y-1">
            <li>Do not access or attempt to access another user's account or personal data.</li>
            <li>Do not execute Denial of Service (DoS/DDoS) attacks against our infrastructure.</li>
            <li>Allow our engineering team a minimum of 14 business days to patch verified flaws before public disclosure.</li>
          </ul>
        </div>

        <div>
          <h2 class="text-xl font-bold text-ink mb-3">6. Incident Response &amp; Breach Notification</h2>
          <p>
            Flowwithlit maintains an active 24/7 Security Operations Center (SOC). In the event of a verified security incident affecting customer data, Flowwithlit will notify impacted users and regulatory authorities within 72 hours in full compliance with statutory data privacy mandates.
          </p>
        </div>

      </div>
    </div>
  </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
