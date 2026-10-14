<?php
$pageTitle = 'Service Level Agreement — Flowwithlit';
$pageDesc  = 'High availability commitments, incident response guarantees, and API reliability standards.';
$activePage = 'sla';
require_once __DIR__ . '/includes/header.php';
?>

  <!-- HERO SECTION -->
  <section class="hero-bg text-white pt-36 pb-20 px-6 text-center">
    <div class="max-w-4xl mx-auto">
      <div class="reveal badge-pill bg-white/10 text-white border-white/20 mb-4 inline-flex">
        <span class="badge-dot bg-white"></span> Platform Reliability Guarantee
      </div>
      <h1 class="reveal text-3xl sm:text-5xl font-bold tracking-tight">Service Level Agreement</h1>
      <p class="reveal mt-4 text-white/80 text-sm sm:text-base max-w-2xl mx-auto">
        Last Updated: September 17, 2026. High availability commitments, incident response guarantees, and API reliability standards.
      </p>
    </div>
  </section>

  <!-- CONTENT SECTION -->
  <section class="py-16 sm:py-24 px-6 bg-white">
    <div class="max-w-4xl mx-auto">
      
      <!-- Plain English Summary Box -->
      <div class="reveal bg-mint border border-brand-500/10 rounded-2xl p-6 sm:p-8 mb-12 space-y-3">
        <div class="text-xs font-bold uppercase tracking-wider text-blue-600">Plain English Summary</div>
        <h2 class="text-lg font-bold text-ink">Our 99.9% Uptime Commitment</h2>
        <p class="text-ink/80 text-xs sm:text-sm leading-relaxed text-justify">
          Flowwithlit guarantees a monthly service availability of 99.9% for our core payment gateway and REST APIs. If our system availability falls below this threshold due to unexcused platform outages, eligible business and merchant accounts receive service fee credits.
        </p>
      </div>

      <!-- Legal Text Clauses -->
      <div class="space-y-10 text-justify text-ink/85 text-xs sm:text-sm leading-relaxed">
        
        <div>
          <h2 class="text-xl font-bold text-ink mb-3">1. Service Availability Commitment</h2>
          <p>
            Flowwithlit commits to providing 99.9% Monthly Uptime Percentage for core API endpoints, checkout widgets, and payout processing pipelines. Monthly Uptime Percentage is calculated as total operational minutes minus unexcused downtime minutes, divided by total minutes in the calendar month.
          </p>
        </div>

        <div>
          <h2 class="text-xl font-bold text-ink mb-3">2. Incident Severity &amp; Response Times</h2>
          <p>
            Flowwithlit maintains dedicated 24/7 engineering operations. In the event of platform disruption, initial response times are governed by incident severity:
          </p>
          <ul class="list-disc pl-5 mt-3 space-y-2">
            <li><strong>Severity 1 (Critical Outage):</strong> Complete inability to process transactions or access API endpoints. Target initial response within 15 minutes.</li>
            <li><strong>Severity 2 (Major Impairment):</strong> Partial API latency degradation or non-critical feature outage. Target initial response within 1 hour.</li>
            <li><strong>Severity 3 (Minor Issue):</strong> Non-disruptive dashboard bugs or minor UI glitches. Target initial response within 4 hours.</li>
          </ul>
        </div>

        <div>
          <h2 class="text-xl font-bold text-ink mb-3">3. Service Credit Structure</h2>
          <p>
            If Flowwithlit fails to meet the 99.9% uptime commitment in any calendar month, verified merchant accounts are eligible to claim Service Credits against future processing fees:
          </p>
          <ul class="list-disc pl-5 mt-2 space-y-1">
            <li><strong>99.0% – 99.89% Availability:</strong> 10% credit of monthly transaction processing fees.</li>
            <li><strong>95.0% – 98.99% Availability:</strong> 25% credit of monthly transaction processing fees.</li>
            <li><strong>Below 95.0% Availability:</strong> 50% credit of monthly transaction processing fees.</li>
          </ul>
        </div>

        <div>
          <h2 class="text-xl font-bold text-ink mb-3">4. Exclusions &amp; Uncontrollable Events</h2>
          <p>
            The SLA uptime calculation excludes downtime resulting from: scheduled maintenance announced at least 48 hours in advance, third-party commercial bank network downtime, decentralized blockchain network congestion (e.g. Bitcoin/Solana/Ethereum gas delays), client-side integration bugs, or events of Force Majeure.
          </p>
        </div>

        <div>
          <h2 class="text-xl font-bold text-ink mb-3">5. Requesting Service Credits</h2>
          <p>
            To receive a service credit, merchants must submit a written claim to <strong>support@flowwithlit.com</strong> within 30 days of the affected billing cycle.
          </p>
        </div>

      </div>
    </div>
  </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
