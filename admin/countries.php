<?php
declare(strict_types=1);

$localPreview = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true) && ($_GET['preview'] ?? '') === '1';
$adminApiBase = rtrim((string) (getenv('FLOW_ADMIN_API_URL') ?: '../api/admin'), '/');
require_once __DIR__.'/components/layout.php';
admin_use_shared_layout('Countries', $localPreview);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow,noarchive">
<title>Countries · Flowwithlit Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="assets/dashboard.css?v=4"><link rel="stylesheet" href="assets/deposit.css?v=1"><link rel="stylesheet" href="assets/promo-codes.css?v=1">
</head>
<body class="min-h-screen w-full antialiased">
<div class="dashboard-shell w-full min-h-screen"><aside class="sidebar" aria-label="Main navigation"></aside><div class="main-layout"><header class="topbar"></header>
<main>
<div class="page-heading"><div><h1>Countries</h1><div class="breadcrumb"><span>Home</span><span>/</span><strong>Countries</strong></div></div>
<div class="page-actions"><button class="button slate-button" id="open-country-modal" type="button"><i data-lucide="plus" class="icon" aria-hidden="true"></i>Add country</button></div></div>

<section class="panel operations-panel" aria-labelledby="country-summary-heading">
  <div class="panel-heading operations-panel-heading"><div><h2 id="country-summary-heading">Where people can sign up</h2><p class="panel-description">Approve a country once a bank partner covers it. Approved countries appear in the signup form's country list.</p></div></div>
  <div class="operation-metrics metrics-4">
    <article class="operation-metric"><span class="operation-icon"><i data-lucide="globe" class="icon" aria-hidden="true"></i></span><span>Approved</span><strong id="country-approved">—</strong><small>Shown at signup</small></article>
    <article class="operation-metric"><span class="operation-icon"><i data-lucide="circle-pause" class="icon" aria-hidden="true"></i></span><span>Not approved</span><strong id="country-paused">—</strong><small>Ready to switch on</small></article>
    <article class="operation-metric"><span class="operation-icon"><i data-lucide="users-round" class="icon" aria-hidden="true"></i></span><span>Users with a country</span><strong id="country-users">—</strong><small>From signup answers</small></article>
    <article class="operation-metric"><span class="operation-icon"><i data-lucide="phone" class="icon" aria-hidden="true"></i></span><span>Phone codes</span><strong id="country-dials">—</strong><small>In the signup phone picker</small></article>
  </div>
</section>

<section class="panel" aria-labelledby="country-list-heading">
  <div class="panel-heading promo-list-heading">
    <div><h2 id="country-list-heading">All countries</h2><p class="panel-description">People from countries that are not approved can still sign up under "Other countries".</p></div>
    <div class="promo-tools">
      <label class="promo-search"><i data-lucide="search" class="icon" aria-hidden="true"></i><input id="country-search" type="search" placeholder="Search name, code or +dial" autocomplete="off" aria-label="Search countries"></label>
      <label class="select-control"><select id="country-status-filter" aria-label="Filter by status"><option value="">All countries</option><option value="1">Approved</option><option value="0">Not approved</option></select><i data-lucide="chevron-down" class="icon" aria-hidden="true"></i></label>
    </div>
  </div>
  <div id="country-loading" class="dashboard-state" role="status"><span class="spinner" aria-hidden="true"></span>Loading countries…</div>
  <div id="country-error" class="dashboard-state dashboard-error" role="alert" hidden><i data-lucide="circle-alert" class="icon" aria-hidden="true"></i><span>Countries could not be loaded.</span><button class="button" type="button" id="country-retry">Try again</button></div>
  <div id="country-content" hidden>
    <div class="table-scroll"><table class="operations-table promo-table">
      <thead><tr><th>Country</th><th>Phone code</th><th>Users</th><th>Order</th><th>Status</th><th aria-label="Actions"></th></tr></thead>
      <tbody id="country-rows"></tbody>
    </table>
    <p id="country-empty" class="dashboard-empty" hidden></p></div>
  </div>
</section>
</main></div></div>

<div id="country-modal" class="deposit-modal" role="dialog" aria-modal="true" aria-labelledby="country-modal-title" hidden>
  <div class="deposit-modal-backdrop" data-close-country></div>
  <div class="deposit-modal-card">
    <button class="deposit-modal-close" type="button" data-close-country aria-label="Close"><i data-lucide="x" class="icon" aria-hidden="true"></i></button>
    <span class="modal-icon"><i data-lucide="globe" class="icon" aria-hidden="true"></i></span>
    <h2 id="country-modal-title">Add country</h2>
    <p>Add a country your bank partners now cover.</p>
    <form id="country-form" novalidate>
      <div class="promo-grid">
        <label>Country code<input name="code" required minlength="2" maxlength="2" pattern="[A-Za-z]{2}" placeholder="GH" autocomplete="off" spellcheck="false"><small class="promo-hint">2-letter ISO code, e.g. GH for Ghana.</small></label>
        <label>Country name<input name="name" required maxlength="80" placeholder="Ghana"></label>
        <label>Phone code<input name="dial_code" required maxlength="6" pattern="\+?[1-9][0-9]{0,4}" placeholder="+233" autocomplete="off"></label>
        <label>Display order<input name="sort_order" type="number" min="1" max="999" step="1" value="100"><small class="promo-hint">Lower numbers show first. Nigeria is 1.</small></label>
        <label>Status<select name="is_enabled"><option value="1">Approved — show at signup</option><option value="0">Not approved yet</option></select></label>
      </div>
      <div class="manual-credit-notice"><i data-lucide="info" class="icon" aria-hidden="true"></i><span>Only approve a country once deposits and payouts work there. Changes are recorded in the audit log.</span></div>
      <div class="modal-actions"><button class="button" type="button" data-close-country>Cancel</button><button class="button slate-button" type="submit" id="country-submit">Save country</button></div>
    </form>
  </div>
</div>

<div id="toast" class="toast" role="status" hidden></div>
<script>window.dashboardConfig=<?= json_encode(['localPreview'=>$localPreview,'apiBase'=>$adminApiBase], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>;</script>
<script src="assets/dashboard.js?v=5"></script>
<script src="assets/countries.js?v=1"></script>
</body></html>
