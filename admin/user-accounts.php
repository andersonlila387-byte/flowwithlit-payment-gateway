<?php
declare(strict_types=1);

$localPreview = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true) && ($_GET['preview'] ?? '') === '1';
$adminApiBase = rtrim((string) (getenv('FLOW_ADMIN_API_URL') ?: '../api/admin'), '/');
require_once __DIR__.'/components/layout.php';
admin_use_shared_layout('Users', $localPreview, [
    ['Users List', 'users-list.php', false],
    ['User Accounts', 'user-accounts.php', true],
    ['User Identity', 'identity.php', false],
    ['User Settings', 'users.php', false],
]);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow,noarchive">
<title>User accounts · Flowwithlit Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="assets/dashboard.css?v=4"><link rel="stylesheet" href="assets/users-list.css?v=1"><link rel="stylesheet" href="assets/user-accounts.css?v=1">
</head>
<body class="min-h-screen w-full antialiased">
<div class="dashboard-shell w-full min-h-screen"><aside class="sidebar" aria-label="Main navigation"></aside><div class="main-layout"><header class="topbar"></header>
<main>
<div class="page-heading"><div><h1>User accounts</h1><div class="breadcrumb"><span>Home</span><span>/</span><span>Users</span><span>/</span><strong>User accounts</strong></div></div><span class="ua-provider" id="ua-provider" hidden></span></div>

<section class="user-summary-grid ua-summary-grid" aria-label="Account overview">
<article class="user-summary-card"><span class="user-summary-icon"><i data-lucide="users-round" class="icon" aria-hidden="true"></i></span><span>Total customers</span><strong data-ua-summary="total">—</strong><small>All registered accounts</small></article>
<article class="user-summary-card"><span class="user-summary-icon"><i data-lucide="landmark" class="icon" aria-hidden="true"></i></span><span>With account number</span><strong data-ua-summary="with_account">—</strong><small>Active bank account assigned</small></article>
<article class="user-summary-card" data-ua-filter-card="missing"><span class="user-summary-icon warning"><i data-lucide="circle-help" class="icon" aria-hidden="true"></i></span><span>Missing account</span><strong data-ua-summary="missing">—</strong><small>Approved but no account number</small></article>
<article class="user-summary-card" data-ua-filter-card="failed"><span class="user-summary-icon danger"><i data-lucide="circle-alert" class="icon" aria-hidden="true"></i></span><span>Failed generation</span><strong data-ua-summary="failed">—</strong><small>Provider did not return a number</small></article>
<article class="user-summary-card"><span class="user-summary-icon"><i data-lucide="wallet-cards" class="icon" aria-hidden="true"></i></span><span>Crypto addresses</span><strong data-ua-summary="crypto">—</strong><small>Active deposit addresses</small></article>
<article class="user-summary-card" data-ua-filter-card="duplicates"><span class="user-summary-icon danger"><i data-lucide="copy" class="icon" aria-hidden="true"></i></span><span>Duplicate alerts</span><strong data-ua-summary="duplicates">—</strong><small>Customers with duplicate accounts</small></article>
</section>

<section class="panel ua-dup-panel" id="ua-dup-panel" aria-labelledby="ua-dup-heading" hidden>
<div class="panel-heading"><div><h2 id="ua-dup-heading"><i data-lucide="shield-alert" class="icon" aria-hidden="true"></i> Duplicate account monitor</h2><p class="panel-description">The same account number on more than one record, a customer with more than one active account, or one crypto address on more than one wallet.</p></div></div>
<div class="table-scroll"><table class="users-table"><thead><tr><th>Problem</th><th>Customer</th><th>Bank / network</th><th>Account number</th><th>Status</th><th aria-label="Actions"></th></tr></thead><tbody id="ua-dup-body"></tbody></table></div>
</section>

<section class="panel user-list-panel" aria-labelledby="ua-table-heading">
<div class="panel-heading user-list-heading"><div><h2 id="ua-table-heading">Customer accounts</h2><p class="panel-description">Account numbers, crypto addresses, bank accounts and country for every customer.</p></div><div class="user-list-tools"><label class="customer-search"><i data-lucide="search" class="icon" aria-hidden="true"></i><input id="ua-search" type="search" placeholder="Name, email, phone, account no., bank, country…" autocomplete="off"><button type="button" id="ua-clear-search" aria-label="Clear search" hidden><i data-lucide="x" class="icon" aria-hidden="true"></i></button></label><label class="select-control"><select id="ua-filter" aria-label="Filter accounts"><option value="">All customers</option><option value="with_account">Has account number</option><option value="missing">Missing account number</option><option value="failed">Failed generation</option><option value="duplicates">Duplicate alerts</option></select><i data-lucide="chevron-down" class="icon" aria-hidden="true"></i></label></div></div>
<div id="ua-loading" class="dashboard-state" role="status"><span class="spinner" aria-hidden="true"></span>Loading accounts…</div>
<div id="ua-error" class="dashboard-state dashboard-error" role="alert" hidden><i data-lucide="circle-alert" class="icon" aria-hidden="true"></i><span>Accounts could not be loaded.</span><button class="button" type="button" data-ua-retry>Try again</button></div>
<div id="ua-content" hidden><div class="table-scroll"><table class="users-table ua-table"><thead><tr><th>Customer</th><th>Country</th><th>Bank name</th><th>Account number</th><th>Crypto</th><th>Bank accounts</th><th>Status</th><th aria-label="Actions"></th></tr></thead><tbody id="ua-body"></tbody></table><p id="ua-empty" class="dashboard-empty" hidden>No customers match the current search.</p></div><footer class="users-pagination"><span id="ua-pagination-summary"></span><div><button type="button" id="ua-previous">Previous</button><span id="ua-page-current"></span><button type="button" id="ua-next">Next</button></div></footer></div>
</section>
</main></div></div>

<div id="user-drawer-backdrop" class="user-drawer-backdrop" hidden></div>
<aside id="user-drawer" class="user-drawer" aria-labelledby="user-drawer-title" aria-hidden="true"><header><div><p>Customer accounts</p><h2 id="user-drawer-title">Loading…</h2></div><button type="button" id="close-user-drawer" aria-label="Close"><i data-lucide="x" class="icon" aria-hidden="true"></i></button></header><div id="user-drawer-content" class="user-drawer-content"></div></aside>
<div id="toast" class="toast" role="status" hidden></div>
<script>window.dashboardConfig=<?= json_encode(['localPreview'=>$localPreview,'apiBase'=>$adminApiBase], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>;</script>
<script src="assets/dashboard.js?v=5"></script><script src="assets/user-accounts.js?v=1"></script>
</body></html>
