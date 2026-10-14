<?php
declare(strict_types=1);

$localPreview = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true) && ($_GET['preview'] ?? '') === '1';
$adminApiBase = rtrim((string) (getenv('FLOW_ADMIN_API_URL') ?: '../api/admin'), '/');
require_once __DIR__.'/components/layout.php';
admin_use_shared_layout('API Connect', $localPreview);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow,noarchive">
<title>API Connect · Flowwithlit Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="assets/dashboard.css?v=4"><link rel="stylesheet" href="assets/deposit.css?v=1"><link rel="stylesheet" href="assets/api-connect.css?v=1">
</head>
<body class="min-h-screen w-full antialiased">
<div class="dashboard-shell w-full min-h-screen"><aside class="sidebar" aria-label="Main navigation"></aside><div class="main-layout"><header class="topbar"></header>
<main>
<div class="page-heading"><div><h1>API Connect</h1><div class="breadcrumb"><span>Home</span><span>/</span><strong>API Connect</strong></div></div></div>

<section class="panel operations-panel" aria-labelledby="api-summary-heading">
  <div class="panel-heading operations-panel-heading"><div><h2 id="api-summary-heading">Connected services</h2><p class="panel-description">Connect payment providers, banks, crypto, email and other third-party services. Secret keys are stored on the server and are never shown again.</p></div></div>
  <div class="operation-metrics metrics-5">
    <article class="operation-metric"><span class="operation-icon"><i data-lucide="plug-zap" class="icon" aria-hidden="true"></i></span><span>Connected</span><strong id="api-connected">—</strong><small>Enabled and validated</small></article>
    <article class="operation-metric"><span class="operation-icon danger"><i data-lucide="circle-alert" class="icon" aria-hidden="true"></i></span><span>Needs attention</span><strong id="api-attention">—</strong><small>Last validation failed</small></article>
    <article class="operation-metric"><span class="operation-icon warning"><i data-lucide="settings-2" class="icon" aria-hidden="true"></i></span><span>Not set up</span><strong id="api-unset">—</strong><small>Available to connect</small></article>
    <article class="operation-metric"><span class="operation-icon"><i data-lucide="flask-conical" class="icon" aria-hidden="true"></i></span><span>Test mode</span><strong id="api-test-mode">—</strong><small>Merchant API keys in testing</small></article>
    <article class="operation-metric"><span class="operation-icon"><i data-lucide="radio-tower" class="icon" aria-hidden="true"></i></span><span>Live mode</span><strong id="api-live-mode">—</strong><small>Merchant API keys in production</small></article>
  </div>
</section>

<section class="panel" aria-labelledby="api-clients-heading">
  <div class="panel-heading"><div><h2 id="api-clients-heading">Merchant API activity</h2><p class="panel-description">Active merchant keys and recent webhook delivery outcomes. Secret API keys are never exposed here.</p></div></div>
  <div class="table-scroll"><table class="operations-table"><thead><tr><th>Merchant</th><th>Key</th><th>Mode</th><th>Last used</th><th>Webhook delivery</th></tr></thead><tbody id="api-clients"></tbody></table><p class="dashboard-empty" id="api-clients-empty" hidden>No merchant API keys have been created yet.</p></div>
</section>

<section class="panel" aria-labelledby="api-list-heading">
  <div class="panel-heading api-heading"><div><h2 id="api-list-heading">Integrations</h2><p class="panel-description">Choose a service to add its keys, switch between test and live, or run a connection test.</p></div>
    <div class="api-tools"><label class="api-search"><i data-lucide="search" class="icon" aria-hidden="true"></i><input id="api-search" type="search" placeholder="Search services" autocomplete="off" aria-label="Search services"></label><div id="api-cats" class="api-cats" role="group" aria-label="Category"></div></div></div>
  <div id="api-loading" class="dashboard-state" role="status"><span class="spinner" aria-hidden="true"></span>Loading integrations…</div>
  <div id="api-error" class="dashboard-state dashboard-error" role="alert" hidden><i data-lucide="circle-alert" class="icon" aria-hidden="true"></i><span>Integrations could not be loaded.</span><button class="button" type="button" id="api-retry">Try again</button></div>
  <div id="api-content" hidden><div id="api-grid" class="api-grid"></div><p id="api-empty" class="dashboard-empty" hidden>No services match your search.</p></div>
</section>
</main></div></div>

<div id="api-modal" class="deposit-modal" role="dialog" aria-modal="true" aria-labelledby="api-modal-title" hidden>
  <div class="deposit-modal-backdrop" data-close-api></div>
  <div class="deposit-modal-card">
    <button class="deposit-modal-close" type="button" data-close-api aria-label="Close"><i data-lucide="x" class="icon" aria-hidden="true"></i></button>
    <span class="modal-icon"><i data-lucide="plug-zap" class="icon" aria-hidden="true"></i></span>
    <h2 id="api-modal-title">Configure service</h2>
    <p id="api-modal-sub"></p>
    <form id="api-form" novalidate autocomplete="off">
      <div id="api-fields" class="api-fields"></div>
      <div class="manual-credit-notice"><i data-lucide="lock" class="icon" aria-hidden="true"></i><span>Saved secrets are encrypted and cannot be viewed again. Leave a secret field empty to keep the saved value.</span></div>
      <div class="modal-actions"><button class="button" type="button" data-close-api>Cancel</button><button class="button" type="submit" data-then="none" id="api-save">Save</button><button class="button slate-button" type="submit" data-then="test" id="api-save-test">Save and test</button></div>
    </form>
  </div>
</div>

<div id="toast" class="toast" role="status" hidden></div>
<script>window.dashboardConfig=<?= json_encode(['localPreview'=>$localPreview,'apiBase'=>$adminApiBase], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>;</script>
<script src="assets/dashboard.js?v=5"></script>
<script src="assets/api-connect.js?v=1"></script>
</body></html>
