<?php
declare(strict_types=1);

$localPreview = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true) && ($_GET['preview'] ?? '') === '1';
$adminApiBase = rtrim((string) (getenv('FLOW_ADMIN_API_URL') ?: '../api/admin'), '/');
require_once __DIR__.'/components/layout.php';
admin_use_shared_layout('Authorization', $localPreview);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow,noarchive">
<title>Authorization · Flowwithlit Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="assets/dashboard.css?v=4"><link rel="stylesheet" href="assets/deposit.css?v=1"><link rel="stylesheet" href="assets/authorization.css?v=1">
</head>
<body class="min-h-screen w-full antialiased">
<div class="dashboard-shell w-full min-h-screen"><aside class="sidebar" aria-label="Main navigation"></aside><div class="main-layout"><header class="topbar"></header>
<main>
<div class="page-heading"><div><h1>Authorization</h1><div class="breadcrumb"><span>Home</span><span>/</span><strong>Authorization</strong></div></div></div>

<div id="auth-loading" class="dashboard-state" role="status"><span class="spinner" aria-hidden="true"></span>Loading authorization settings…</div>
<div id="auth-error" class="dashboard-state dashboard-error" role="alert" hidden><i data-lucide="circle-alert" class="icon" aria-hidden="true"></i><span>Authorization settings could not be loaded.</span><button class="button" type="button" id="auth-retry">Try again</button></div>

<div id="auth-content" hidden>
<section class="panel operations-panel" aria-labelledby="auth-summary-heading">
  <div class="panel-heading operations-panel-heading"><div><h2 id="auth-summary-heading">Who can do what</h2><p class="panel-description">Roles decide what each admin can open or change. Approval rules make a second admin sign off on sensitive actions.</p></div></div>
  <div class="operation-metrics metrics-4">
    <article class="operation-metric"><span class="operation-icon warning"><i data-lucide="hourglass" class="icon" aria-hidden="true"></i></span><span>Waiting for approval</span><strong id="auth-pending-count">—</strong><small>Needs a second admin</small></article>
    <article class="operation-metric"><span class="operation-icon"><i data-lucide="shield-check" class="icon" aria-hidden="true"></i></span><span>Roles</span><strong id="auth-roles-count">—</strong><small>Each with its own permissions</small></article>
    <article class="operation-metric"><span class="operation-icon"><i data-lucide="git-pull-request-arrow" class="icon" aria-hidden="true"></i></span><span>Approval rules on</span><strong id="auth-rules-count">—</strong><small>Sensitive actions that need sign-off</small></article>
    <article class="operation-metric"><span class="operation-icon"><i data-lucide="key-round" class="icon" aria-hidden="true"></i></span><span>Permissions</span><strong id="auth-permissions-count">—</strong><small>Controls available by role</small></article>
  </div>
</section>

<section class="panel" aria-labelledby="auth-queue-heading">
  <div class="panel-heading"><div><h2 id="auth-queue-heading">Approval queue</h2><p class="panel-description">You cannot approve your own request.</p></div></div>
  <div class="table-scroll"><table class="operations-table auth-table"><thead><tr><th>Requested</th><th>Requested by</th><th>Action</th><th>Amount</th><th aria-label="Decision"></th></tr></thead><tbody id="auth-queue"></tbody></table>
  <p id="auth-queue-empty" class="dashboard-empty" hidden>Nothing is waiting for approval.</p></div>
</section>

<section class="panel" aria-labelledby="auth-matrix-heading">
  <div class="panel-heading auth-heading"><div><h2 id="auth-matrix-heading">Role permissions</h2><p class="panel-description">Set access for each role. Super Administrator is locked so nobody can lose access to this page.</p></div>
    <div class="page-actions"><span id="auth-dirty" class="auth-dirty" aria-live="polite"></span><button class="button" type="button" id="auth-reset" disabled>Discard</button><button class="button slate-button" type="button" id="auth-save" disabled>Save permissions</button></div></div>
  <div class="table-scroll auth-matrix-wrap"><table class="auth-matrix"><thead id="auth-matrix-head"></thead><tbody id="auth-matrix-body"></tbody></table></div>
</section>

<section class="panel" aria-labelledby="auth-rules-heading">
  <div class="panel-heading auth-heading"><div><h2 id="auth-rules-heading">Approval rules</h2><p class="panel-description">When a rule is on, the action waits in the queue until a different admin approves it.</p></div>
    <div class="page-actions"><button class="button slate-button" type="button" id="auth-rules-save" disabled>Save rules</button></div></div>
  <div id="auth-rules" class="auth-rules"></div>
</section>
</div>
</main></div></div>

<div id="auth-modal" class="deposit-modal" role="dialog" aria-modal="true" aria-labelledby="auth-modal-title" hidden>
  <div class="deposit-modal-backdrop" data-close-auth></div>
  <div class="deposit-modal-card">
    <button class="deposit-modal-close" type="button" data-close-auth aria-label="Close"><i data-lucide="x" class="icon" aria-hidden="true"></i></button>
    <span class="modal-icon"><i data-lucide="shield-check" class="icon" aria-hidden="true"></i></span>
    <h2 id="auth-modal-title">Review request</h2>
    <p id="auth-modal-summary"></p>
    <form id="auth-form" novalidate>
      <label>Note<textarea name="note" rows="3" maxlength="300" placeholder="Add a note for the audit log"></textarea></label>
      <div class="modal-actions"><button class="button" type="button" data-close-auth>Cancel</button><button class="button slate-button" type="submit" id="auth-confirm">Confirm</button></div>
    </form>
  </div>
</div>

<div id="toast" class="toast" role="status" hidden></div>
<script>window.dashboardConfig=<?= json_encode(['localPreview'=>$localPreview,'apiBase'=>$adminApiBase], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>;</script>
<script src="assets/dashboard.js?v=5"></script>
<script src="assets/authorization.js?v=1"></script>
</body></html>
