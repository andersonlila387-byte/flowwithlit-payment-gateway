<?php
declare(strict_types=1);

$localPreview = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true) && ($_GET['preview'] ?? '') === '1';
$adminApiBase = rtrim((string) (getenv('FLOW_ADMIN_API_URL') ?: '../api/admin'), '/');
require_once __DIR__.'/components/layout.php';
admin_use_shared_layout('Promo Code', $localPreview);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow,noarchive">
<title>Promo codes · Flowwithlit Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="assets/dashboard.css?v=4"><link rel="stylesheet" href="assets/deposit.css?v=1"><link rel="stylesheet" href="assets/promo-codes.css?v=1">
</head>
<body class="min-h-screen w-full antialiased">
<div class="dashboard-shell w-full min-h-screen"><aside class="sidebar" aria-label="Main navigation"></aside><div class="main-layout"><header class="topbar"></header>
<main>
<div class="page-heading"><div><h1>Promo codes</h1><div class="breadcrumb"><span>Home</span><span>/</span><strong>Promo codes</strong></div></div>
<div class="page-actions"><button class="button slate-button" id="open-promo-modal" type="button"><i data-lucide="plus" class="icon" aria-hidden="true"></i>New promo code</button></div></div>

<section class="panel operations-panel" aria-labelledby="promo-summary-heading">
  <div class="panel-heading operations-panel-heading"><div><h2 id="promo-summary-heading">Bonus programme</h2><p class="panel-description">Wallet bonuses, cashback and fee savings given to users through promo codes.</p></div></div>
  <div class="operation-metrics metrics-4">
    <article class="operation-metric"><span class="operation-icon"><i data-lucide="ticket-percent" class="icon" aria-hidden="true"></i></span><span>Active codes</span><strong id="promo-active">—</strong><small>Redeemable right now</small></article>
    <article class="operation-metric"><span class="operation-icon"><i data-lucide="users-round" class="icon" aria-hidden="true"></i></span><span>Redemptions</span><strong id="promo-redemptions">—</strong><small>All time</small></article>
    <article class="operation-metric"><span class="operation-icon"><i data-lucide="gift" class="icon" aria-hidden="true"></i></span><span>Rewards paid</span><strong id="promo-paid">—</strong><small>Bonuses, cashback and fee savings</small></article>
    <article class="operation-metric"><span class="operation-icon warning"><i data-lucide="clock-3" class="icon" aria-hidden="true"></i></span><span>Ending soon</span><strong id="promo-expiring">—</strong><small>Expire within 7 days</small></article>
  </div>
</section>

<section class="panel" aria-labelledby="promo-list-heading">
  <div class="panel-heading promo-list-heading">
    <div><h2 id="promo-list-heading">All promo codes</h2><p class="panel-description">Search by code or name, or filter by reward type and status.</p></div>
    <div class="promo-tools">
      <label class="promo-search"><i data-lucide="search" class="icon" aria-hidden="true"></i><input id="promo-search" type="search" placeholder="Search code or name" autocomplete="off" aria-label="Search promo codes"></label>
      <label class="select-control"><select id="promo-type-filter" aria-label="Filter by reward type"><option value="">All reward types</option><option value="wallet_bonus">Wallet bonus</option><option value="cashback">Cashback</option><option value="fee_discount">Fee discount</option><option value="free_transfers">Free transfers</option></select><i data-lucide="chevron-down" class="icon" aria-hidden="true"></i></label>
      <label class="select-control"><select id="promo-status-filter" aria-label="Filter by status"><option value="">All statuses</option><option value="active">Active</option><option value="scheduled">Scheduled</option><option value="paused">Paused</option><option value="expired">Expired</option><option value="exhausted">Fully used</option></select><i data-lucide="chevron-down" class="icon" aria-hidden="true"></i></label>
    </div>
  </div>
  <div id="promo-loading" class="dashboard-state" role="status"><span class="spinner" aria-hidden="true"></span>Loading promo codes…</div>
  <div id="promo-error" class="dashboard-state dashboard-error" role="alert" hidden><i data-lucide="circle-alert" class="icon" aria-hidden="true"></i><span>Promo codes could not be loaded.</span><button class="button" type="button" id="promo-retry">Try again</button></div>
  <div id="promo-content" hidden>
    <div class="table-scroll"><table class="operations-table promo-table">
      <thead><tr><th>Code</th><th>Reward</th><th>Who can use it</th><th>Usage</th><th>Valid</th><th>Status</th><th aria-label="Actions"></th></tr></thead>
      <tbody id="promo-rows"></tbody>
    </table>
    <p id="promo-empty" class="dashboard-empty" hidden></p></div>
  </div>
</section>
</main></div></div>

<div id="promo-modal" class="deposit-modal" role="dialog" aria-modal="true" aria-labelledby="promo-modal-title" hidden>
  <div class="deposit-modal-backdrop" data-close-promo></div>
  <div class="deposit-modal-card">
    <button class="deposit-modal-close" type="button" data-close-promo aria-label="Close"><i data-lucide="x" class="icon" aria-hidden="true"></i></button>
    <span class="modal-icon"><i data-lucide="ticket-percent" class="icon" aria-hidden="true"></i></span>
    <h2 id="promo-modal-title">New promo code</h2>
    <p>Choose what users receive and the rules for getting it.</p>
    <form id="promo-form" novalidate>
      <input type="hidden" name="uuid">
      <div class="promo-grid">
        <label>Code<span class="promo-code-field"><input name="code" required minlength="4" maxlength="20" pattern="[A-Za-z0-9_\-]{4,20}" placeholder="WELCOME5000" autocomplete="off" spellcheck="false"><button type="button" class="button" id="promo-generate">Generate</button></span><small class="promo-hint">Letters, numbers, dash or underscore. 4–20 characters.</small></label>
        <label>Name<input name="title" required maxlength="80" placeholder="Welcome bonus"><small class="promo-hint">Only admins see this.</small></label>
        <label>Reward type<select name="type" required><option value="wallet_bonus">Wallet bonus — fixed amount</option><option value="cashback">Cashback — % of payment</option><option value="fee_discount">Fee discount — % off fees</option><option value="free_transfers">Free transfers — number of transfers</option></select></label>
        <label><span id="promo-value-label">Bonus amount (₦)</span><input name="value" type="number" min="0" step="any" required></label>
        <label data-when="percent">Maximum reward (₦)<input name="max_reward" type="number" min="0" step="any" placeholder="No limit"></label>
        <label>Minimum payment (₦)<input name="min_amount" type="number" min="0" step="any" placeholder="None"></label>
        <label>Who can use it<select name="audience"><option value="all">All users</option><option value="new_users">New users only</option><option value="verified">Verified users only</option></select></label>
        <label>Total redemptions<input name="usage_limit" type="number" min="1" step="1" placeholder="Unlimited"></label>
        <label>Uses per user<input name="per_user_limit" type="number" min="1" step="1" value="1" required></label>
        <label>Starts<input name="starts_at" type="date"></label>
        <label>Ends<input name="expires_at" type="date"><small class="promo-hint">Leave empty for no end date.</small></label>
      </div>
      <div class="manual-credit-notice"><i data-lucide="info" class="icon" aria-hidden="true"></i><span id="promo-summary-line">Rewards are credited to the user's wallet automatically when they qualify.</span></div>
      <div class="modal-actions"><button class="button" type="button" data-close-promo>Cancel</button><button class="button slate-button" type="submit" id="promo-submit">Save promo code</button></div>
    </form>
  </div>
</div>

<div id="toast" class="toast" role="status" hidden></div>
<script>window.dashboardConfig=<?= json_encode(['localPreview'=>$localPreview,'apiBase'=>$adminApiBase], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>;</script>
<script src="assets/dashboard.js?v=5"></script>
<script src="assets/promo-codes.js?v=1"></script>
</body></html>
