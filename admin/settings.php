<?php
declare(strict_types=1);
$localPreview = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true) && ($_GET['preview'] ?? '') === '1';
$adminApiBase = rtrim((string) (getenv('FLOW_ADMIN_API_URL') ?: '../api/admin'), '/');
require_once __DIR__ . '/components/layout.php';
admin_use_shared_layout('Settings', $localPreview);
?>
<!doctype html><html lang="en"><head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow,noarchive"><title>Settings · Flowwithlit Admin</title>
  <script src="https://cdn.tailwindcss.com"></script><link rel="stylesheet" href="assets/dashboard.css?v=4"><link rel="stylesheet" href="assets/settings.css?v=3">
</head><body class="min-h-screen w-full antialiased"><div class="dashboard-shell w-full min-h-screen"><aside class="sidebar"></aside><div class="main-layout"><header class="topbar"></header><main>
  <div class="page-heading settings-heading"><div><p class="eyebrow">Administration</p><h1>Settings</h1><p class="page-subtitle">Control platform defaults, operational safeguards, and transactional email delivery.</p></div><a class="button settings-help" href="#smtp-panel"><i data-lucide="mail-check" class="icon" aria-hidden="true"></i> Verify email</a></div>
  <section class="panel operations-panel settings-overview" aria-label="Platform overview"><div class="panel-heading"><div><h2>Platform overview</h2><p class="panel-description">A live snapshot of the services affected by these controls.</p></div></div><div class="operation-metrics metrics-4">
    <article class="operation-metric"><span class="operation-icon"><i data-lucide="users" class="icon" aria-hidden="true"></i></span><small>Customers</small><strong id="settings-users">—</strong></article>
    <article class="operation-metric"><span class="operation-icon"><i data-lucide="building-2" class="icon" aria-hidden="true"></i></span><small>Businesses</small><strong id="settings-businesses">—</strong></article>
    <article class="operation-metric"><span class="operation-icon"><i data-lucide="key-round" class="icon" aria-hidden="true"></i></span><small>Active API keys</small><strong id="settings-keys">—</strong></article>
    <article class="operation-metric"><span class="operation-icon warning"><i data-lucide="clock-3" class="icon" aria-hidden="true"></i></span><small>In processing</small><strong id="settings-processing">—</strong></article>
  </div></section>
  <div id="settings-loading" class="panel dashboard-state"><span class="spinner"></span>Loading platform settings…</div><div id="settings-error" class="panel dashboard-state dashboard-error" hidden><span>Settings could not be loaded.</span><button id="settings-retry" class="button slate-button" type="button">Try again</button></div>
  <div id="settings-content" class="settings-layout" hidden>
    <section class="panel settings-card"><div class="panel-heading"><div><h2>Operational defaults</h2><p class="panel-description" id="settings-updated"></p></div><span class="section-badge"><i data-lucide="shield-check" class="icon" aria-hidden="true"></i> Audited</span></div>
      <form id="settings-form" class="settings-form"><div class="settings-form-grid">
        <label><span>Support email</span><input type="email" name="support_email" autocomplete="email" placeholder="support@yourdomain.com" required><small>Shown to customers who need help.</small></label>
        <label><span>Default currency</span><select name="default_currency"><option>NGN</option><option>USD</option><option>USDT</option><option>USDC</option></select><small>Used when a flow has no currency selected.</small></label>
        <label><span>Payout review limit (₦)</span><input type="number" name="payout_review_limit" min="0" inputmode="decimal" required><small>Higher payouts enter the review queue.</small></label>
        <label class="switch-setting"><input type="checkbox" name="maintenance_mode"><span class="switch-ui" aria-hidden="true"></span><span><b>Maintenance mode</b><small>Temporarily restrict customer access while work is in progress.</small></span></label>
      </div><div class="settings-actions"><p><i data-lucide="info" class="icon" aria-hidden="true"></i> Changes are recorded in the administrator audit log.</p><button class="button slate-button" id="settings-save" type="submit"><i data-lucide="save" class="icon" aria-hidden="true"></i> Save platform settings</button></div></form>
    </section>
    <section class="panel settings-card smtp-card" id="smtp-panel"><div class="panel-heading"><div><h2>Email delivery</h2><p class="panel-description">Configure SMTP once, then use a real inbox test to verify delivery.</p></div><span id="smtp-badge" class="section-badge neutral"><i data-lucide="circle-dot" class="icon" aria-hidden="true"></i> Checking</span></div>
      <div id="smtp-status" class="smtp-status" role="status" aria-live="polite"></div><div class="smtp-steps" aria-label="Email setup steps"><span><b>1</b> Add provider</span><i></i><span><b>2</b> Save securely</span><i></i><span><b>3</b> Send test</span></div>
      <form id="smtp-form" class="settings-form" autocomplete="off"><fieldset><legend>SMTP connection</legend><div class="settings-form-grid smtp-connection-grid"><label class="span-2"><span>SMTP host</span><input name="host" placeholder="smtp.example.com" autocomplete="off" required></label><label><span>Port</span><input name="port" type="number" min="1" max="65535" value="587" required></label><label><span>Encryption</span><select name="encryption"><option value="tls">TLS — recommended (587)</option><option value="ssl">SSL (465)</option></select></label></div></fieldset>
        <fieldset><legend>Sender identity</legend><div class="settings-form-grid"><label><span>Username</span><input name="username" autocomplete="username" placeholder="mailer@yourdomain.com" required></label><label><span>Password or app password</span><input name="password" type="password" autocomplete="new-password" placeholder="Enter to save or replace"><small>Kept private and never returned to the browser.</small></label><label><span>From email</span><input name="from_address" type="email" placeholder="no-reply@yourdomain.com" required></label><label><span>From name</span><input name="from_name" value="Flowwithlit" required></label></div></fieldset>
        <div class="smtp-test-row"><label><span>Test recipient</span><input name="recipient" type="email" placeholder="you@yourdomain.com"><small>We’ll send one actual email to this inbox.</small></label><div class="smtp-actions"><button class="button outline-button" id="smtp-test" type="button"><i data-lucide="send" class="icon" aria-hidden="true"></i> Send test email</button><button class="button slate-button" id="smtp-save" type="submit"><i data-lucide="lock-keyhole" class="icon" aria-hidden="true"></i> Save SMTP settings</button></div></div>
      </form>
    </section>
  </div>
</main></div></div><div id="toast" class="toast" hidden></div><script>window.dashboardConfig=<?=json_encode(['localPreview'=>$localPreview,'apiBase'=>$adminApiBase],JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)?></script><script src="assets/dashboard.js?v=5"></script><script src="assets/settings-live.js?v=3"></script></body></html>
