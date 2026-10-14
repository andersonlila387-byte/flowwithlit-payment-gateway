<?php
declare(strict_types=1);

$localPreview = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true) && ($_GET['preview'] ?? '') === '1';
$adminApiBase = rtrim((string) (getenv('FLOW_ADMIN_API_URL') ?: '../api/admin'), '/');
require_once __DIR__.'/components/layout.php';
admin_use_shared_layout('Levels', $localPreview);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow,noarchive">
<title>Levels · Flowwithlit Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="assets/dashboard.css?v=4"><link rel="stylesheet" href="assets/deposit.css?v=1"><link rel="stylesheet" href="assets/levels.css?v=1">
</head>
<body class="min-h-screen w-full antialiased">
<div class="dashboard-shell w-full min-h-screen"><aside class="sidebar" aria-label="Main navigation"></aside><div class="main-layout"><header class="topbar"></header>
<main>
<div class="page-heading"><div><h1>Levels</h1><div class="breadcrumb"><span>Home</span><span>/</span><strong>Levels</strong></div></div></div>

<section class="panel operations-panel" aria-labelledby="levels-summary-heading">
  <div class="panel-heading operations-panel-heading"><div><h2 id="levels-summary-heading">Account levels</h2><p class="panel-description">Levels set what verified users can do. Identity reviews move people up; this page sets the limits, fees and features for each level.</p></div></div>
  <div class="operation-metrics metrics-4">
    <article class="operation-metric"><span class="operation-icon"><i data-lucide="layers" class="icon" aria-hidden="true"></i></span><span>Levels</span><strong id="levels-count">—</strong><small>Across personal and business</small></article>
    <article class="operation-metric"><span class="operation-icon"><i data-lucide="users-round" class="icon" aria-hidden="true"></i></span><span>Users on a level</span><strong id="levels-users">—</strong><small>Changes apply to all of them</small></article>
    <article class="operation-metric"><span class="operation-icon warning"><i data-lucide="scan-search" class="icon" aria-hidden="true"></i></span><span>Waiting for identity review</span><strong id="levels-pending">—</strong><small><a class="levels-link" href="identity.php">Open User Identity</a></small></article><article class="operation-metric"><span class="operation-icon"><i data-lucide="shield-check" class="icon" aria-hidden="true"></i></span><span>API-enabled users</span><strong id="levels-api-users">—</strong><small>On levels with API access</small></article>
  </div>
</section>

<section class="panel" aria-labelledby="levels-list-heading">
  <div class="panel-heading levels-heading"><div><h2 id="levels-list-heading">Level rules</h2><p class="panel-description">Higher levels can never have lower limits or higher fees than the level below.</p></div>
    <div class="levels-tabs" role="group" aria-label="Account type"><button type="button" data-type="personal" aria-pressed="true">Personal</button><button type="button" data-type="business" aria-pressed="false">Business</button></div></div>
  <div id="levels-loading" class="dashboard-state" role="status"><span class="spinner" aria-hidden="true"></span>Loading levels…</div>
  <div id="levels-error" class="dashboard-state dashboard-error" role="alert" hidden><i data-lucide="circle-alert" class="icon" aria-hidden="true"></i><span>Levels could not be loaded.</span><button class="button" type="button" id="levels-retry">Try again</button></div>
  <div id="levels-content" hidden><div id="levels-grid" class="levels-grid"></div><p id="levels-empty" class="dashboard-empty" hidden>No levels are set up for this account type yet.</p></div>
</section>

<section class="panel" aria-labelledby="levels-history-heading">
  <div class="panel-heading"><div><h2 id="levels-history-heading">Recent changes</h2><p class="panel-description">Who changed a level, and what changed.</p></div></div>
  <div class="table-scroll"><table class="operations-table"><thead><tr><th>Time</th><th>Admin</th><th>Level</th><th>Change</th></tr></thead><tbody id="levels-changes"></tbody></table><p id="levels-changes-empty" class="dashboard-empty" hidden>No changes recorded yet.</p></div>
</section>
</main></div></div>

<div id="level-modal" class="deposit-modal" role="dialog" aria-modal="true" aria-labelledby="level-modal-title" hidden>
  <div class="deposit-modal-backdrop" data-close-level></div>
  <div class="deposit-modal-card">
    <button class="deposit-modal-close" type="button" data-close-level aria-label="Close"><i data-lucide="x" class="icon" aria-hidden="true"></i></button>
    <span class="modal-icon"><i data-lucide="layers" class="icon" aria-hidden="true"></i></span>
    <h2 id="level-modal-title">Edit level</h2>
    <p id="level-modal-sub">Amounts are in naira. Use 0 to block an action.</p>
    <form id="level-form" novalidate>
      <input type="hidden" name="uuid">
      <label>Level name<input name="name" required maxlength="40"></label>
      <h3 class="level-group">Limits</h3>
      <div class="level-fields">
        <label>Deposit, per transaction (₦)<input name="deposit_single" type="number" min="0" step="any" required></label>
        <label>Deposit, per day (₦)<input name="deposit_daily" type="number" min="0" step="any" required></label>
        <label>Withdraw, per transaction (₦)<input name="withdraw_single" type="number" min="0" step="any" required></label>
        <label>Withdraw, per day (₦)<input name="withdraw_daily" type="number" min="0" step="any" required></label>
        <label>Transfer, per transaction (₦)<input name="transfer_single" type="number" min="0" step="any" required></label>
        <label>Transfer, per day (₦)<input name="transfer_daily" type="number" min="0" step="any" required></label>
        <label>Maximum wallet balance (₦)<input name="max_balance" type="number" min="0" step="any" placeholder="No limit"></label>
        <label>Processing fee (%)<input name="fee_percent" type="number" min="0" max="100" step="any" required></label>
      </div>
      <h3 class="level-group">Features</h3>
      <label class="level-check"><input type="checkbox" name="api_access">API access</label>
      <label class="level-check"><input type="checkbox" name="payment_links">Payment links</label>
      <label class="level-check"><input type="checkbox" name="bulk_transfers">Bulk transfers</label>
      <div class="manual-credit-notice"><i data-lucide="triangle-alert" class="icon" aria-hidden="true"></i><span id="level-notice">Changes apply to every user on this level straight away.</span></div>
      <div class="modal-actions"><button class="button" type="button" data-close-level>Cancel</button><button class="button slate-button" type="submit" id="level-submit">Save level</button></div>
    </form>
  </div>
</div>

<div id="toast" class="toast" role="status" hidden></div>
<script>window.dashboardConfig=<?= json_encode(['localPreview'=>$localPreview,'apiBase'=>$adminApiBase], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>;</script>
<script src="assets/dashboard.js?v=5"></script>
<script src="assets/levels.js?v=1"></script>
</body></html>
