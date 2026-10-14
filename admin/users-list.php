<?php
declare(strict_types=1);

$localPreview = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true) && ($_GET['preview'] ?? '') === '1';
$adminApiBase = rtrim((string) (getenv('FLOW_ADMIN_API_URL') ?: '../api/admin'), '/');
require_once __DIR__.'/components/layout.php';
admin_use_shared_layout('Users', $localPreview, [
    ['Users List', 'users-list.php', true],
    ['User Identity', 'identity.php', false],
    ['User Settings', 'users.php', false],
]);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow,noarchive">
<title>Users list · Flowwithlit Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="assets/dashboard.css?v=4"><link rel="stylesheet" href="assets/users-list.css?v=1">
</head>
<body class="min-h-screen w-full antialiased">
<div class="dashboard-shell w-full min-h-screen"><aside class="sidebar" aria-label="Main navigation"></aside><div class="main-layout"><header class="topbar"></header>
<main>
<div class="page-heading"><div><h1>Users list</h1><div class="breadcrumb"><span>Home</span><span>/</span><span>Users</span><span>/</span><strong>Users list</strong></div></div></div>

<section class="user-summary-grid" aria-label="Customer overview">
<article class="user-summary-card"><span class="user-summary-icon"><i data-lucide="users-round" class="icon" aria-hidden="true"></i></span><span>Total customers</span><strong data-user-summary="total">—</strong><small>All registered accounts</small></article>
<article class="user-summary-card"><span class="user-summary-icon"><i data-lucide="user-check" class="icon" aria-hidden="true"></i></span><span>Active customers</span><strong data-user-summary="active">—</strong><small>Accounts able to use Flowwithlit</small></article>
<article class="user-summary-card"><span class="user-summary-icon warning"><i data-lucide="badge-alert" class="icon" aria-hidden="true"></i></span><span>Awaiting verification</span><strong data-user-summary="pending_verification">—</strong><small>Identity or onboarding required</small></article>
<article class="user-summary-card"><span class="user-summary-icon danger"><i data-lucide="user-x" class="icon" aria-hidden="true"></i></span><span>Suspended customers</span><strong data-user-summary="suspended">—</strong><small>Restricted from transactions</small></article>
<article class="user-summary-card"><span class="user-summary-icon"><i data-lucide="user-plus" class="icon" aria-hidden="true"></i></span><span>New this month</span><strong data-user-summary="new_this_month">—</strong><small>Created since the first day of this month</small></article>
</section>

<section class="panel user-list-panel" aria-labelledby="customer-table-heading">
<div class="panel-heading user-list-heading"><div><h2 id="customer-table-heading">Customers</h2><p class="panel-description">Search, view, suspend, or reactivate customer accounts.</p></div><div class="user-list-tools"><label class="customer-search"><i data-lucide="search" class="icon" aria-hidden="true"></i><input id="customer-search" type="search" placeholder="Name, email, phone, business…" autocomplete="off"><button type="button" id="clear-customer-search" aria-label="Clear customer search" hidden><i data-lucide="x" class="icon" aria-hidden="true"></i></button></label><label class="select-control"><select id="customer-status" aria-label="Filter customer status"><option value="">All statuses</option><option value="active">Active</option><option value="pending_verification">Awaiting verification</option><option value="suspended">Suspended</option><option value="closed">Closed</option></select><i data-lucide="chevron-down" class="icon" aria-hidden="true"></i></label></div></div>
<div id="users-loading" class="dashboard-state" role="status"><span class="spinner" aria-hidden="true"></span>Loading customers…</div>
<div id="users-error" class="dashboard-state dashboard-error" role="alert" hidden><i data-lucide="circle-alert" class="icon" aria-hidden="true"></i><span>Customers could not be loaded.</span><button class="button" type="button" data-users-retry>Try again</button></div>
<div id="users-content" hidden><div class="table-scroll"><table class="users-table"><thead><tr><th>Customer</th><th>Business</th><th>Type</th><th>Verification</th><th>Status</th><th>Joined</th><th aria-label="Actions"></th></tr></thead><tbody id="users-table-body"></tbody></table><p id="users-empty" class="dashboard-empty" hidden>No customers match the current filters.</p></div><footer class="users-pagination"><span id="users-pagination-summary"></span><div><button type="button" id="users-previous">Previous</button><span id="users-page-current"></span><button type="button" id="users-next">Next</button></div></footer></div>
</section>
</main></div></div>

<div id="user-row-menu" class="user-action-menu" role="menu" hidden>
<button type="button" data-user-action="view" role="menuitem"><i data-lucide="eye" class="icon" aria-hidden="true"></i>View customer</button>
<button type="button" data-user-action="toggle-status" role="menuitem"><i data-lucide="user-round-x" class="icon" aria-hidden="true"></i><span>Suspend customer</span></button>
</div>
<div id="user-drawer-backdrop" class="user-drawer-backdrop" hidden></div>
<aside id="user-drawer" class="user-drawer" aria-labelledby="user-drawer-title" aria-hidden="true"><header><div><p>Customer details</p><h2 id="user-drawer-title">Loading…</h2></div><button type="button" id="close-user-drawer" aria-label="Close customer details"><i data-lucide="x" class="icon" aria-hidden="true"></i></button></header><div id="user-drawer-content" class="user-drawer-content"></div></aside>
<div id="toast" class="toast" role="status" hidden></div>
<script>window.dashboardConfig=<?= json_encode(['localPreview'=>$localPreview,'apiBase'=>$adminApiBase], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>;</script>
<script src="assets/dashboard.js?v=5"></script><script src="assets/users-list.js?v=1"></script>
</body></html>

