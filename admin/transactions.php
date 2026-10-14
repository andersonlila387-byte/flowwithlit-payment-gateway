<?php
declare(strict_types=1);

$localPreview = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true) && ($_GET['preview'] ?? '') === '1';
$adminApiBase = rtrim((string) (getenv('FLOW_ADMIN_API_URL') ?: '../api/admin'), '/');
require_once __DIR__.'/components/layout.php';
admin_use_shared_layout('Transaction', $localPreview);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow,noarchive">
<title>Transactions · Flowwithlit Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="assets/dashboard.css?v=4"><link rel="stylesheet" href="assets/users-list.css?v=1">
<style>
  @media(min-width: 1101px) {
    .operation-metrics.metrics-4 {
      grid-template-columns: repeat(4, minmax(0, 1fr));
    }
  }
  .transaction-list-tools { display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; }
  .transaction-search { position: relative; flex: 1; min-width: 250px; }
  .transaction-search .icon { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); width: 18px; height: 18px; pointer-events: none; }
  .transaction-search input { width: 100%; padding: 0.5rem 0.75rem 0.5rem 2.5rem; border: 1px solid var(--border-color); border-radius: 0.5rem; background: var(--bg-base); color: var(--text-base); font-size: 0.875rem; }
  .transaction-search input:focus { outline: none; border-color: var(--brand-color); box-shadow: 0 0 0 2px var(--brand-color-alpha); }
  .transaction-search button { position: absolute; right: 0.5rem; top: 50%; transform: translateY(-50%); background: transparent; border: none; padding: 0.25rem; cursor: pointer; color: var(--text-muted); display: flex; align-items: center; justify-content: center; border-radius: 0.25rem; }
  .transaction-search button:hover { background: var(--bg-hover); color: var(--text-base); }
  .transaction-search button .icon { position: static; transform: none; width: 16px; height: 16px; pointer-events: auto; }
  
  .filter-group { display: flex; gap: 0.5rem; align-items: center; }
  .filter-input { padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; background: var(--bg-base); color: var(--text-base); font-size: 0.875rem; }
  .filter-input:focus { outline: none; border-color: var(--brand-color); box-shadow: 0 0 0 2px var(--brand-color-alpha); }
  .transactions-table { width: 100%; text-align: left; border-collapse: collapse; white-space: nowrap; }
  .transactions-table th { padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
  .transactions-table td { padding: 1rem; border-bottom: 1px solid var(--border-color); font-size: 0.875rem; color: var(--text-base); }
  .transactions-table tbody tr:hover { background: var(--bg-hover); }
  .transactions-table tbody tr:last-child td { border-bottom: none; }
  
  .status-badge { display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
  .status-badge.success { background: rgba(16, 185, 129, 0.1); color: #10b981; }
  .status-badge.pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
  .status-badge.failed { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
  .status-badge.incoming { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
</style>
</head>
<body class="min-h-screen w-full antialiased">
<div class="dashboard-shell w-full min-h-screen"><aside class="sidebar" aria-label="Main navigation"></aside><div class="main-layout"><header class="topbar"></header>
<main>
<div class="page-heading"><div><h1>Transactions</h1><div class="breadcrumb"><span>Home</span><span>/</span><strong>Transactions</strong></div></div></div>

<section class="panel operations-panel" aria-labelledby="transaction-summary-heading">
  <div class="panel-heading operations-panel-heading">
    <div>
      <h2 id="transaction-summary-heading">Transactions overview</h2>
      <p class="panel-description">Live summary of all recorded operations and their status.</p>
    </div>
  </div>
  <div class="operation-metrics metrics-4">
    <article class="operation-metric">
      <span class="operation-icon"><i data-lucide="bar-chart-3" class="icon" aria-hidden="true"></i></span>
      <span>Total Transactions</span>
      <strong id="summary-total">—</strong>
      <small>All recorded transactions</small>
    </article>
    <article class="operation-metric">
      <span class="operation-icon"><i data-lucide="check-circle" class="icon" aria-hidden="true"></i></span>
      <span>Successful</span>
      <strong id="summary-success">—</strong>
      <small>Completed payments</small>
    </article>
    <article class="operation-metric">
      <span class="operation-icon warning"><i data-lucide="clock" class="icon" aria-hidden="true"></i></span>
      <span>Pending</span>
      <strong id="summary-pending">—</strong>
      <small>Awaiting confirmation</small>
    </article>
    <article class="operation-metric">
      <span class="operation-icon danger"><i data-lucide="x-circle" class="icon" aria-hidden="true"></i></span>
      <span>Failed</span>
      <strong id="summary-failed">—</strong>
      <small>Unsuccessful payments</small>
    </article>
  </div>
</section>

<section class="panel transaction-list-panel" aria-labelledby="transaction-table-heading">
<div class="panel-heading transaction-list-heading">
  <div>
    <h2 id="transaction-table-heading">Transaction Records</h2>
    <p class="panel-description">Search by user, filter by date and status to track payments.</p>
  </div>
  <div class="transaction-list-tools">
    <label class="transaction-search">
      <i data-lucide="search" class="icon" aria-hidden="true"></i>
      <input id="transaction-search-user" type="search" placeholder="Search user (e.g. JTech)..." autocomplete="off">
      <button type="button" id="clear-transaction-search" aria-label="Clear user search" hidden><i data-lucide="x" class="icon" aria-hidden="true"></i></button>
    </label>
    
    <div class="filter-group">
      <label class="select-control">
        <input type="date" id="transaction-date" class="filter-input" aria-label="Filter by date">
      </label>
    </div>
    
    <label class="select-control">
      <select id="transaction-status" aria-label="Filter by status">
        <option value="">All Statuses</option>
        <option value="incoming">Incoming</option>
        <option value="successful">Successful</option>
        <option value="pending">Pending</option>
        <option value="failed">Failed</option>
      </select>
      <i data-lucide="chevron-down" class="icon" aria-hidden="true"></i>
    </label>
  </div>
</div>

<div id="transactions-loading" class="dashboard-state" role="status"><span class="spinner" aria-hidden="true"></span>Loading transactions…</div>
<div id="transactions-error" class="dashboard-state dashboard-error" role="alert" hidden><i data-lucide="circle-alert" class="icon" aria-hidden="true"></i><span>Transactions could not be loaded.</span><button class="button" type="button" data-transactions-retry>Try again</button></div>

<div id="transactions-content" hidden>
  <div class="table-scroll">
    <table class="transactions-table">
      <thead>
        <tr>
          <th>Reference</th>
          <th>User</th>
          <th>Amount</th>
          <th>Type</th>
          <th>Status</th>
          <th>Date</th>
          <th aria-label="Actions"></th>
        </tr>
      </thead>
      <tbody id="transactions-table-body">
        <!-- JS will populate rows -->
      </tbody>
    </table>
    <p id="transactions-empty" class="dashboard-empty" hidden>No transactions match the current filters.</p>
  </div>
  <footer class="users-pagination">
    <span id="transactions-pagination-summary"></span>
    <div>
      <button type="button" id="transactions-previous">Previous</button>
      <span id="transactions-page-current"></span>
      <button type="button" id="transactions-next">Next</button>
    </div>
  </footer>
</div>
</section>
</main></div></div>

<div id="transaction-drawer-backdrop" class="user-drawer-backdrop" hidden></div>
<aside id="transaction-drawer" class="user-drawer" aria-labelledby="transaction-drawer-title" aria-hidden="true">
  <header>
    <div>
      <p>Transaction details</p>
      <h2 id="transaction-drawer-title">Loading…</h2>
    </div>
    <button type="button" id="close-transaction-drawer" aria-label="Close transaction details"><i data-lucide="x" class="icon" aria-hidden="true"></i></button>
  </header>
  <div id="transaction-drawer-content" class="user-drawer-content"></div>
</aside>

<div id="toast" class="toast" role="status" hidden></div>
<script>window.dashboardConfig=<?= json_encode(['localPreview'=>$localPreview,'apiBase'=>$adminApiBase], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>;</script>
<script src="assets/dashboard.js?v=5"></script>
<script src="assets/transactions.js?v=1"></script>
</body></html>
