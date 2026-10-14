<?php
declare(strict_types=1);

$localPreview = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true) && ($_GET['preview'] ?? '') === '1';
$adminApiBase = rtrim((string) (getenv('FLOW_ADMIN_API_URL') ?: '../api/admin'), '/');
require_once __DIR__.'/components/layout.php';
admin_use_shared_layout('Reports', $localPreview);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow,noarchive">
<title>Reports · Flowwithlit Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="assets/dashboard.css?v=4"><link rel="stylesheet" href="assets/reports.css?v=1">
</head>
<body class="min-h-screen w-full antialiased">
<div class="dashboard-shell w-full min-h-screen"><aside class="sidebar" aria-label="Main navigation"></aside><div class="main-layout"><header class="topbar"></header>
<main>
<div class="page-heading"><div><h1>Reports</h1><div class="breadcrumb"><span>Home</span><span>/</span><strong>Reports</strong></div></div>
<div class="page-actions"><button class="button slate-button" id="report-export" type="button" disabled><i data-lucide="download" class="icon" aria-hidden="true"></i>Export CSV</button></div></div>

<section class="panel operations-panel" aria-labelledby="report-filter-heading">
  <div class="panel-heading operations-panel-heading"><div><h2 id="report-filter-heading">Payment report</h2><p class="panel-description">Choose a period and how to group it. Figures cover payments created in that period.</p></div>
    <div class="panel-tools dashboard-filters report-filters">
      <label class="select-control"><i data-lucide="calendar-days" class="icon" aria-hidden="true"></i><select id="report-preset" aria-label="Period"><option value="today">Today</option><option value="7d">Last 7 days</option><option value="30d" selected>Last 30 days</option><option value="month">This month</option><option value="custom">Custom range</option></select><i data-lucide="chevron-down" class="icon" aria-hidden="true"></i></label>
      <label class="report-date"><span>From</span><input type="date" id="report-from" aria-label="From date"></label>
      <label class="report-date"><span>To</span><input type="date" id="report-to" aria-label="To date"></label>
      <label class="select-control"><i data-lucide="layers" class="icon" aria-hidden="true"></i><select id="report-group" aria-label="Group by"><option value="day">Group by day</option><option value="provider">Group by provider</option><option value="type">Group by payment type</option><option value="status">Group by status</option></select><i data-lucide="chevron-down" class="icon" aria-hidden="true"></i></label>
    </div></div>
  <p id="report-updated" class="dashboard-updated" aria-live="polite"></p>
  <div id="report-loading" class="dashboard-state" role="status"><span class="spinner" aria-hidden="true"></span>Building report…</div>
  <div id="report-error" class="dashboard-state dashboard-error" role="alert" hidden><i data-lucide="circle-alert" class="icon" aria-hidden="true"></i><span>The report could not be loaded.</span><button class="button" type="button" id="report-retry">Try again</button></div>
  <div id="report-content" hidden>
    <div class="operation-metrics metrics-5">
      <article class="operation-metric"><span class="operation-icon"><i data-lucide="badge-check" class="icon" aria-hidden="true"></i></span><span>Successful volume</span><strong id="sum-volume">—</strong><small id="sum-count">—</small></article>
      <article class="operation-metric"><span class="operation-icon"><i data-lucide="percent" class="icon" aria-hidden="true"></i></span><span>Success rate</span><strong id="sum-rate">—</strong><small id="sum-failed">—</small></article>
      <article class="operation-metric"><span class="operation-icon"><i data-lucide="receipt-text" class="icon" aria-hidden="true"></i></span><span>Fees collected</span><strong id="sum-fees">—</strong><small>Successful payments only</small></article>
      <article class="operation-metric"><span class="operation-icon"><i data-lucide="gift" class="icon" aria-hidden="true"></i></span><span>Promo rewards paid</span><strong id="sum-rewards">—</strong><small>Bonuses, cashback and fee savings</small></article>
      <article class="operation-metric"><span class="operation-icon warning"><i data-lucide="clock-3" class="icon" aria-hidden="true"></i></span><span>Still processing</span><strong id="sum-processing">—</strong><small>Not counted as failed</small></article>
    </div>
    <div class="report-chart-wrap"><h3 id="report-chart-title">Volume</h3><div id="report-chart" class="report-chart"></div></div>
    <div class="table-scroll"><table class="operations-table report-table">
      <thead><tr><th id="report-col-label">Day</th><th class="num">Payments</th><th class="num">Successful</th><th class="num">Failed</th><th class="num">Success rate</th><th class="num">Volume</th><th class="num">Fees</th></tr></thead>
      <tbody id="report-rows"></tbody><tfoot id="report-foot"></tfoot></table>
      <p id="report-empty" class="dashboard-empty" hidden>No payments were recorded in this period.</p></div>
  </div>
</section>
</main></div></div>
<div id="toast" class="toast" role="status" hidden></div>
<script>window.dashboardConfig=<?= json_encode(['localPreview'=>$localPreview,'apiBase'=>$adminApiBase], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>;</script>
<script src="assets/dashboard.js?v=5"></script>
<script src="assets/reports.js?v=1"></script>
</body></html>
