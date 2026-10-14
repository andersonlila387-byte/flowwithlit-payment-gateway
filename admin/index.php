<?php
declare(strict_types=1);

$localPreview = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true) && ($_GET['preview'] ?? '') === '1';
$adminApiBase = rtrim((string) (getenv('FLOW_ADMIN_API_URL') ?: '../api/admin'), '/');
require_once __DIR__.'/components/layout.php';
admin_use_shared_layout('Dashboard', $localPreview);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow,noarchive">
<title>Operations overview · Flowwithlit Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="assets/dashboard.css?v=4">
</head>
<body class="min-h-screen w-full antialiased">
<div class="dashboard-shell w-full min-h-screen">
<aside class="sidebar" aria-label="Main navigation"></aside>
<div class="main-layout"><header class="topbar"></header>
<main>
<div class="page-heading operations-heading"><div><h1>Operations overview</h1><div class="breadcrumb"><span>Home</span><span>/</span><strong>Operations overview</strong></div></div><div class="page-actions"><button class="button slate-button" id="dashboard-refresh" type="button"><i data-lucide="refresh-cw" class="icon" aria-hidden="true"></i>Refresh</button></div></div>

<section class="panel operations-panel" aria-labelledby="payment-health-heading">
  <div class="panel-heading operations-panel-heading"><div><h2 id="payment-health-heading">Payment health</h2><p class="panel-description">Live payment processing for the selected currency and period.</p></div><div class="panel-tools dashboard-filters">
  <label class="select-control"><i data-lucide="coins" class="icon" aria-hidden="true"></i><select id="dashboard-currency" aria-label="Reporting currency" disabled><option value="NGN">NGN</option></select><i data-lucide="chevron-down" class="icon" aria-hidden="true"></i></label>
  <label class="select-control"><i data-lucide="calendar-days" class="icon" aria-hidden="true"></i><select id="dashboard-period" aria-label="Reporting period"><option value="today">Today</option><option value="7d">Last 7 days</option><option value="month" selected>This month</option></select><i data-lucide="chevron-down" class="icon" aria-hidden="true"></i></label>
  </div></div>
  <p id="dashboard-updated" class="dashboard-updated" aria-live="polite"></p>
  <div id="dashboard-loading" class="dashboard-state" role="status"><span class="spinner" aria-hidden="true"></span>Loading live operations data…</div>
  <div id="dashboard-error" class="dashboard-state dashboard-error" role="alert" hidden><i data-lucide="circle-alert" class="icon" aria-hidden="true"></i><span>Operations data could not be loaded.</span><button class="button" type="button" data-dashboard-retry>Try again</button></div>
  <div id="dashboard-content" hidden><div class="operation-metrics" aria-label="Payment health metrics">
    <article class="operation-metric"><span class="operation-icon"><i data-lucide="badge-check" class="icon" aria-hidden="true"></i></span><span>Successful payment volume</span><strong data-dashboard="successful_volume">—</strong><small>Completed payments</small></article>
    <article class="operation-metric"><span class="operation-icon"><i data-lucide="circle-check" class="icon" aria-hidden="true"></i></span><span>Successful payments</span><strong data-dashboard="successful_count">—</strong><small>Completed in this period</small></article>
    <article class="operation-metric"><span class="operation-icon warning"><i data-lucide="clock-3" class="icon" aria-hidden="true"></i></span><span>Processing payments</span><strong data-dashboard="processing_count">—</strong><small>Awaiting confirmation</small></article>
    <article class="operation-metric"><span class="operation-icon danger"><i data-lucide="circle-x" class="icon" aria-hidden="true"></i></span><span>Failed payments</span><strong data-dashboard="failed_count">—</strong><small data-dashboard="failure_rate">Failure rate: —</small></article>
    <article class="operation-metric"><span class="operation-icon"><i data-lucide="receipt-text" class="icon" aria-hidden="true"></i></span><span>Fees collected</span><strong data-dashboard="fees_collected">—</strong><small>Successful payments only</small></article>
  </div></div>
</section>

<div class="operations-grid">
<section class="panel queue-panel"><div class="panel-heading"><div><h2>Attention queue</h2><p class="panel-description">Items that need review before the next operational step.</p></div></div><div id="attention-queue" class="attention-queue"></div></section>
<section class="panel links-panel"><div class="panel-heading"><div><h2>Payment links</h2><p class="panel-description">Current payment-link status across active merchants.</p></div></div><dl id="payment-links-summary" class="link-summary"></dl></section>
</div>

<section class="panel activity-panel"><div class="panel-heading"><div><h2>Recent operational activity</h2><p class="panel-description">Latest records available for your administrator role.</p></div></div><div class="table-scroll"><table class="operations-table"><thead><tr><th>Time</th><th>Reference</th><th>Type</th><th>Status</th><th>Currency</th><th>Amount</th><th>Provider</th></tr></thead><tbody id="recent-activity"></tbody></table><p id="recent-activity-empty" class="dashboard-empty" hidden>No operational activity is available for this role yet.</p></div></section>
</main></div></div>
<div id="toast" class="toast" role="status" hidden></div>
<script>window.dashboardConfig=<?= json_encode(['localPreview'=>$localPreview,'apiBase'=>$adminApiBase], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>;</script>
<script src="assets/dashboard.js?v=5"></script>
</body></html>

