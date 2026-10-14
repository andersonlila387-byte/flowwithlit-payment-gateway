<?php
declare(strict_types=1);

$localPreview = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true) && ($_GET['preview'] ?? '') === '1';
$adminApiBase = rtrim((string) (getenv('FLOW_ADMIN_API_URL') ?: '../api/admin'), '/');
require_once __DIR__.'/components/layout.php';
admin_use_shared_layout('Users', $localPreview, [
    ['Users List', 'users-list.php', false],
    ['User Identity', 'identity.php', true],
    ['User Settings', 'users.php', false],
]);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow,noarchive">
<title>User Identity · Flowwithlit Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="assets/dashboard.css?v=4"><link rel="stylesheet" href="assets/identity.css?v=1">
</head>
<body class="min-h-screen w-full antialiased">
<div class="dashboard-shell w-full min-h-screen"><aside class="sidebar" aria-label="Main navigation"></aside><div class="main-layout"><header class="topbar"></header>
<main>
<div class="page-heading"><div><h1>User Identity</h1><div class="breadcrumb"><span>Home</span><span>/</span><span>Users</span><span>/</span><strong>User Identity</strong></div></div></div>
<section class="identity-summary-grid" aria-label="Identity verification overview">
<article class="identity-summary-card"><span class="identity-summary-icon"><i data-lucide="inbox" class="icon" aria-hidden="true"></i></span><span>New submissions</span><strong data-identity-summary="submitted">—</strong><small>Ready to start review</small></article>
<article class="identity-summary-card"><span class="identity-summary-icon warning"><i data-lucide="scan-search" class="icon" aria-hidden="true"></i></span><span>In review</span><strong data-identity-summary="under_review">—</strong><small>Awaiting a decision</small></article>
<article class="identity-summary-card"><span class="identity-summary-icon success"><i data-lucide="badge-check" class="icon" aria-hidden="true"></i></span><span>Approved</span><strong data-identity-summary="approved">—</strong><small>Completed identity reviews</small></article>
<article class="identity-summary-card"><span class="identity-summary-icon danger"><i data-lucide="file-warning" class="icon" aria-hidden="true"></i></span><span>Changes requested</span><strong data-identity-summary="rejected">—</strong><small>Needs customer correction</small></article>
</section>

<section class="panel identity-list-panel">
<div class="panel-heading identity-list-heading"><div><h2>Verification queue</h2><p class="panel-description">Review personal identity, business details, and submitted documents.</p></div><div class="identity-list-tools"><label class="identity-search"><i data-lucide="search" class="icon" aria-hidden="true"></i><input id="identity-search" type="search" placeholder="Customer, email, business…" autocomplete="off"><button id="clear-identity-search" type="button" aria-label="Clear search" hidden><i data-lucide="x" class="icon" aria-hidden="true"></i></button></label><label class="select-control"><select id="identity-status" aria-label="Filter identity status"><option value="">All review statuses</option><option value="submitted">New submissions</option><option value="under_review">In review</option><option value="approved">Approved</option><option value="rejected">Changes requested</option></select><i data-lucide="chevron-down" class="icon" aria-hidden="true"></i></label></div></div>
<div id="identity-loading" class="dashboard-state" role="status"><span class="spinner" aria-hidden="true"></span>Loading verification queue…</div>
<div id="identity-error" class="dashboard-state dashboard-error" role="alert" hidden><i data-lucide="circle-alert" class="icon" aria-hidden="true"></i><span>Identity submissions could not be loaded.</span><button class="button" type="button" data-identity-retry>Try again</button></div>
<div id="identity-content" hidden><div class="table-scroll"><table class="identity-table"><thead><tr><th>Customer</th><th>Business</th><th>Account type</th><th>Documents</th><th>Submitted</th><th>Status</th><th aria-label="Actions"></th></tr></thead><tbody id="identity-table-body"></tbody></table><p id="identity-empty" class="dashboard-empty" hidden>No identity submissions match the current filters.</p></div><footer class="identity-pagination"><span id="identity-pagination-summary"></span><div><button type="button" id="identity-previous">Previous</button><span id="identity-page-current"></span><button type="button" id="identity-next">Next</button></div></footer></div>
</section>
</main></div></div>

<div id="identity-drawer-backdrop" class="identity-drawer-backdrop" hidden></div>
<aside id="identity-drawer" class="identity-drawer" aria-labelledby="identity-drawer-title" aria-hidden="true"><header><div><p>Identity review</p><h2 id="identity-drawer-title">Loading…</h2></div><button id="close-identity-drawer" type="button" aria-label="Close identity review"><i data-lucide="x" class="icon" aria-hidden="true"></i></button></header><div id="identity-drawer-content" class="identity-drawer-content"></div></aside>
<div id="toast" class="toast" role="status" hidden></div>
<script>window.dashboardConfig=<?= json_encode(['localPreview'=>$localPreview,'apiBase'=>$adminApiBase], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>;</script>
<script src="assets/dashboard.js?v=5"></script><script src="assets/identity.js?v=1"></script>
</body></html>

