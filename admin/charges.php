<?php
declare(strict_types=1);

$localPreview = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true) && ($_GET['preview'] ?? '') === '1';
$adminApiBase = rtrim((string) (getenv('FLOW_ADMIN_API_URL') ?: '../api/admin'), '/');
require_once __DIR__.'/components/layout.php';
admin_use_shared_layout('Charge', $localPreview);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow,noarchive">
<title>Charges · Flowwithlit Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="assets/dashboard.css?v=4"><link rel="stylesheet" href="assets/deposit.css?v=1"><link rel="stylesheet" href="assets/charges.css?v=1">
</head>
<body class="min-h-screen w-full antialiased">
<div class="dashboard-shell w-full min-h-screen"><aside class="sidebar" aria-label="Main navigation"></aside><div class="main-layout"><header class="topbar"></header>
<main>
<div class="page-heading"><div><h1>Charges</h1><div class="breadcrumb"><span>Home</span><span>/</span><strong>Charges</strong></div></div></div>

<section class="panel operations-panel" aria-labelledby="charge-summary-heading">
  <div class="panel-heading operations-panel-heading"><div><h2 id="charge-summary-heading">Fees you charge</h2><p class="panel-description">Set what each type of payment costs. Changes apply to new payments only; payments already made keep the fee they were charged.</p></div></div>
  <div class="operation-metrics metrics-4">
    <article class="operation-metric"><span class="operation-icon"><i data-lucide="badge-dollar-sign" class="icon" aria-hidden="true"></i></span><span>Active charges</span><strong id="charge-active">—</strong><small id="charge-active-note">—</small></article>
    <article class="operation-metric"><span class="operation-icon"><i data-lucide="receipt-text" class="icon" aria-hidden="true"></i></span><span>Fees collected this month</span><strong id="charge-month">—</strong><small>Successful payments only</small></article>
    <article class="operation-metric"><span class="operation-icon warning"><i data-lucide="history" class="icon" aria-hidden="true"></i></span><span>Last change</span><strong id="charge-last">—</strong><small id="charge-last-by">—</small></article><article class="operation-metric"><span class="operation-icon"><i data-lucide="layers-3" class="icon" aria-hidden="true"></i></span><span>Payment types</span><strong id="charge-types">—</strong><small>Operational fee rules</small></article>
  </div>
</section>

<section class="panel" aria-labelledby="charge-list-heading">
  <div class="panel-heading"><div><h2 id="charge-list-heading">Charge rules</h2><p class="panel-description">One rule for each type of payment. Turning a charge off makes that payment free.</p></div></div>
  <div id="charge-loading" class="dashboard-state" role="status"><span class="spinner" aria-hidden="true"></span>Loading charges…</div>
  <div id="charge-error" class="dashboard-state dashboard-error" role="alert" hidden><i data-lucide="circle-alert" class="icon" aria-hidden="true"></i><span>Charges could not be loaded.</span><button class="button" type="button" id="charge-retry">Try again</button></div>
  <div id="charge-content" hidden><div class="table-scroll"><table class="operations-table charge-table">
    <thead><tr><th>Payment type</th><th>Fee</th><th>Minimum and cap</th><th>Paid by</th><th>Status</th><th>Updated</th><th aria-label="Actions"></th></tr></thead>
    <tbody id="charge-rows"></tbody></table><p id="charge-empty" class="dashboard-empty" hidden>No charges are set up yet.</p></div></div>
</section>

<section class="panel" aria-labelledby="charge-history-heading">
  <div class="panel-heading"><div><h2 id="charge-history-heading">Recent changes</h2><p class="panel-description">Who changed a fee, and what changed.</p></div></div>
  <div class="table-scroll"><table class="operations-table"><thead><tr><th>Time</th><th>Admin</th><th>Payment type</th><th>Change</th></tr></thead><tbody id="charge-changes"></tbody></table><p id="charge-changes-empty" class="dashboard-empty" hidden>No changes recorded yet.</p></div>
</section>
</main></div></div>

<div id="charge-modal" class="deposit-modal" role="dialog" aria-modal="true" aria-labelledby="charge-modal-title" hidden>
  <div class="deposit-modal-backdrop" data-close-charge></div>
  <div class="deposit-modal-card">
    <button class="deposit-modal-close" type="button" data-close-charge aria-label="Close"><i data-lucide="x" class="icon" aria-hidden="true"></i></button>
    <span class="modal-icon"><i data-lucide="badge-dollar-sign" class="icon" aria-hidden="true"></i></span>
    <h2 id="charge-modal-title">Edit charge</h2>
    <p id="charge-modal-sub">Amounts are in naira.</p>
    <form id="charge-form" novalidate>
      <input type="hidden" name="uuid">
      <label>How the fee is worked out<select name="fee_type"><option value="flat">Flat amount</option><option value="percent">Percentage of the payment</option><option value="percent_plus_flat">Percentage plus flat amount</option><option value="tiered">By payment size (bands)</option></select></label>
      <div class="charge-fields">
        <label data-show="flat percent_plus_flat">Flat fee (₦)<input name="flat_amount" type="number" min="0" step="any"></label>
        <label data-show="percent percent_plus_flat">Percentage (%)<input name="percent" type="number" min="0" max="100" step="any"></label>
        <label>Minimum fee (₦)<input name="min_fee" type="number" min="0" step="any" placeholder="None"></label>
        <label>Maximum fee (₦)<input name="max_fee" type="number" min="0" step="any" placeholder="No cap"></label>
      </div>
      <div data-show="tiered" class="charge-bands-wrap"><h3 class="charge-group">Payment size bands</h3><div id="charge-bands"></div><button class="button" type="button" id="charge-add-band"><i data-lucide="plus" class="icon" aria-hidden="true"></i>Add band</button></div>
      <label>Who pays the fee<select name="bearer"><option value="on_top">The customer, added on top of the amount</option><option value="deducted">Taken out of the amount</option></select></label>
      <label class="charge-check"><input type="checkbox" name="enabled">Charge this fee</label>
      <div class="charge-preview"><label>Try an amount (₦)<input id="charge-try" type="number" min="0" step="any" value="10000"></label><p id="charge-preview-out" aria-live="polite"></p></div>
      <div class="modal-actions"><button class="button" type="button" data-close-charge>Cancel</button><button class="button slate-button" type="submit" id="charge-submit">Save charge</button></div>
    </form>
  </div>
</div>

<div id="toast" class="toast" role="status" hidden></div>
<script>window.dashboardConfig=<?= json_encode(['localPreview'=>$localPreview,'apiBase'=>$adminApiBase], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>;</script>
<script src="assets/dashboard.js?v=5"></script>
<script src="assets/charges.js?v=1"></script>
</body></html>
