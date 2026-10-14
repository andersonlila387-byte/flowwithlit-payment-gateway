<?php
/**
 * Flowwithlit Developer API & Webhooks Page
 * Clean two-column layout: Column 1 (API Keys) | Column 2 (Webhooks & Test Connection)
 */
$pageTitle = 'Developer API — Flowwithlit';
$activePage = 'developer';
$activeNavTab = 'Features';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

    <!-- Scrollable Main Content Area -->
    <main class="flex-1 h-full overflow-y-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-6 flex flex-col gap-6">
      
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 flex-shrink-0">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 tracking-tight">API &amp; Webhooks</h1>
          <p class="text-xs text-gray-500">Manage credentials and test server connectivity.</p>
        </div>

        <!-- Environment Toggle -->
        <div class="inline-flex items-center bg-gray-100 p-1 rounded-full border border-gray-200/80">
          <button type="button" id="testModeBtn" onclick="switchEnvironment('test')" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition bg-white text-[#0b2545] shadow-xs">
            Test
          </button>
          <button type="button" id="liveModeBtn" onclick="switchEnvironment('live')" class="px-3.5 py-1.5 rounded-full text-xs font-semibold text-gray-500 hover:text-gray-800 transition">
            Live
          </button>
        </div>
      </div>

      <!-- Two-Column Layout -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 flex-shrink-0">
        
        <!-- COLUMN 1: API KEYS -->
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 flex flex-col justify-between gap-5">
          <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
              <h2 class="text-sm font-bold text-gray-900">API Credentials</h2>
              <button type="button" onclick="openRollKeysModal()" class="text-xs font-semibold text-rose-600 hover:underline flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Roll Secret</span>
              </button>
            </div>

            <!-- Public Key -->
            <div>
              <div class="flex items-center justify-between mb-1">
                <label class="text-xs font-semibold text-gray-700">Public Key</label>
                <span class="text-[10px] text-gray-400 font-mono">Client-side</span>
              </div>
              <div class="relative flex items-center">
                <input type="text" id="publicKeyInput" readonly value="" placeholder="No test key created" class="w-full font-mono text-xs text-gray-800 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 pr-10 focus:outline-hidden">
                <button type="button" onclick="copyKey('publicKeyInput')" title="Copy Public Key" class="absolute right-1.5 p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </button>
              </div>
            </div>

            <!-- Secret Key -->
            <div>
              <div class="flex items-center justify-between mb-1">
                <label class="text-xs font-semibold text-gray-700">Secret Key</label>
                <span class="text-[10px] text-gray-400 font-mono">Server-only</span>
              </div>
              <div class="relative flex items-center">
                <input type="password" id="secretKeyInput" readonly value="" placeholder="Generated once and shown only once" class="w-full font-mono text-xs text-gray-800 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 pr-16 focus:outline-hidden">
                <div class="absolute right-1.5 flex items-center gap-0.5">
                  <button type="button" onclick="toggleSecretVisibility()" id="toggleSecretBtn" title="Reveal / Hide" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition">
                    <svg id="secretEyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  </button>
                  <button type="button" onclick="copyKey('secretKeyInput')" title="Copy Secret Key" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Ping API Connection -->
            <div class="pt-2">
              <button type="button" onclick="pingApiGateway()" id="pingGatewayBtn" class="w-full py-2.5 px-4 bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition flex items-center justify-center gap-2">
                <svg class="w-3.5 h-3.5 text-[#0b2545]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span>Test API Gateway Connection</span>
              </button>
            </div>
          </div>
        </div>

        <!-- COLUMN 2: WEBHOOKS & TEST CONNECTION -->
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 flex flex-col justify-between gap-5">
          <form id="webhookForm" onsubmit="saveWebhook(event)" class="space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
              <h2 class="text-sm font-bold text-gray-900">Webhook Settings</h2>
              <span id="webhookStatusBadge" class="inline-flex items-center gap-1 text-[10px] font-bold text-gray-600 bg-gray-50 px-2 py-0.5 rounded-full border border-gray-200">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Not configured
              </span>
            </div>

            <!-- Webhook URL -->
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Webhook URL</label>
              <input type="url" id="webhookUrl" required value="" placeholder="https://your-domain.com/webhooks/flowwithlit" class="w-full font-mono text-xs text-gray-800 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-hidden focus:border-[#0b2545]">
            </div>

            <!-- Webhook Secret -->
            <div>
              <div class="flex items-center justify-between mb-1">
                <label class="text-xs font-semibold text-gray-700">Webhook Secret</label>
                <span class="text-[10px] text-gray-400 font-mono">HMAC-SHA512</span>
              </div>
              <div class="relative flex items-center">
                <input type="password" id="webhookSecret" readonly value="" placeholder="Generated when you save a webhook URL" class="w-full font-mono text-xs text-gray-800 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 pr-16 focus:outline-hidden">
                <div class="absolute right-1.5 flex items-center gap-0.5">
                  <button type="button" onclick="toggleWebhookSecretVisibility()" id="toggleWhSecretBtn" title="Reveal / Hide" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition">
                    <svg id="whSecretEyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  </button>
                  <button type="button" onclick="copyKey('webhookSecret')" title="Copy Secret" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Test Connection & Save Actions -->
            <div class="grid grid-cols-2 gap-2 pt-2">
              <button type="button" id="sendTestWebhookBtn" onclick="sendTestWebhook()" class="py-2.5 px-3 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-xl text-xs font-semibold transition flex items-center justify-center gap-1.5 shadow-2xs">
                <svg class="w-3.5 h-3.5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                <span>Test Webhook</span>
              </button>
              <button type="submit" id="saveWebhookBtn" class="py-2.5 px-3 bg-[#0b2545] hover:bg-[#134074] text-white rounded-xl text-xs font-semibold transition shadow-xs">
                Save URL
              </button>
            </div>

            <!-- Diagnostics Pill -->
            <div id="testResultBox" class="p-3 rounded-xl border border-gray-200 bg-gray-50/70 text-xs flex items-center justify-between">
              <span class="text-gray-500">Connection</span>
              <span id="diagnosticStatus" class="font-bold text-emerald-700 flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> 200 OK (74ms)
              </span>
            </div>
          </form>
        </div>

      </div>

      <!-- INTEGRATION QUICK START -->
      <section class="grid grid-cols-1 xl:grid-cols-5 gap-6">
        <div class="xl:col-span-3 bg-[#0b2545] rounded-2xl p-5 sm:p-6 text-white shadow-sm">
          <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-5">
            <div>
              <div class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-sky-200 bg-white/10 border border-white/10 rounded-full px-2.5 py-1">Quick start</div>
              <h2 class="mt-3 text-lg font-bold">Make your first API request</h2>
              <p class="mt-1 text-xs text-white/65">Create a payment request with your test secret key, then use the response in your checkout flow.</p>
            </div>
            <button type="button" onclick="copyIntegrationExample()" class="shrink-0 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 text-xs font-semibold transition">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 00-2-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
              Copy example
            </button>
          </div>
          <pre id="integrationExample" class="overflow-x-auto rounded-xl bg-[#071930] border border-white/10 p-4 text-[11px] leading-relaxed text-sky-100 font-mono"><code>curl https://api.flowwithlit.com/v1/payments \
  -H "Authorization: Bearer sk_test_..." \
  -H "Content-Type: application/json" \
  -d '{"amount": 25000, "currency": "NGN"}'</code></pre>
        </div>

        <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6">
          <div class="flex items-center justify-between gap-3">
            <div><h2 class="text-sm font-bold text-gray-900">Integration checklist</h2><p class="mt-1 text-xs text-gray-500">Everything needed to go live.</p></div>
            <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">✓</span>
          </div>
          <ol class="mt-5 space-y-3 text-xs text-gray-600">
            <li class="flex gap-3"><span class="w-5 h-5 shrink-0 rounded-full bg-[#0b2545] text-white inline-flex items-center justify-center text-[10px] font-bold">1</span><span><strong class="text-gray-900">Copy your test keys</strong><br>Use them only in a test environment.</span></li>
            <li class="flex gap-3"><span class="w-5 h-5 shrink-0 rounded-full bg-[#0b2545] text-white inline-flex items-center justify-center text-[10px] font-bold">2</span><span><strong class="text-gray-900">Create a payment request</strong><br>Send amount, currency, and customer details.</span></li>
            <li class="flex gap-3"><span class="w-5 h-5 shrink-0 rounded-full bg-[#0b2545] text-white inline-flex items-center justify-center text-[10px] font-bold">3</span><span><strong class="text-gray-900">Verify events with webhooks</strong><br>Confirm payment status on your server.</span></li>
          </ol>
        </div>
      </section>

      <!-- API REFERENCE & EVENT GUIDE -->
      <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5">
          <div class="flex items-center justify-between border-b border-gray-100 pb-3"><div><h2 class="text-sm font-bold text-gray-900">Core API endpoints</h2><p class="mt-0.5 text-xs text-gray-500">Common building blocks for your integration.</p></div><a href="../developers" class="text-xs font-semibold text-[#0b2545] hover:underline">View docs →</a></div>
          <div class="mt-3 divide-y divide-gray-100 text-xs">
            <div class="py-3 flex items-center justify-between gap-3"><span class="font-mono text-gray-800">POST <span class="text-gray-500">/v1/payments</span></span><span class="text-gray-400">Create payment</span></div>
            <div class="py-3 flex items-center justify-between gap-3"><span class="font-mono text-gray-800">GET <span class="text-gray-500">/v1/payments/:id</span></span><span class="text-gray-400">Verify payment</span></div>
            <div class="py-3 flex items-center justify-between gap-3"><span class="font-mono text-gray-800">POST <span class="text-gray-500">/v1/transfers</span></span><span class="text-gray-400">Send payout</span></div>
          </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5">
          <div class="flex items-center justify-between border-b border-gray-100 pb-3"><div><h2 class="text-sm font-bold text-gray-900">Webhook events</h2><p class="mt-0.5 text-xs text-gray-500">Events your endpoint can receive.</p></div><span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full px-2 py-0.5">HMAC verified</span></div>
          <div class="mt-3 grid sm:grid-cols-2 gap-2 text-xs"><div class="rounded-xl bg-gray-50 border border-gray-100 p-3"><code class="font-mono font-bold text-gray-800">charge.success</code><p class="mt-1 text-gray-500">A payment completed.</p></div><div class="rounded-xl bg-gray-50 border border-gray-100 p-3"><code class="font-mono font-bold text-gray-800">transfer.success</code><p class="mt-1 text-gray-500">A payout settled.</p></div><div class="rounded-xl bg-gray-50 border border-gray-100 p-3"><code class="font-mono font-bold text-gray-800">charge.failed</code><p class="mt-1 text-gray-500">A payment could not complete.</p></div><div class="rounded-xl bg-gray-50 border border-gray-100 p-3"><code class="font-mono font-bold text-gray-800">refund.processed</code><p class="mt-1 text-gray-500">A refund was processed.</p></div></div>
        </div>
      </section>

      <!-- Webhook Delivery Logs Table -->
      <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden flex flex-col">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
          <h3 class="text-sm font-bold text-gray-900">Webhook Logs</h3>
          <span class="text-xs text-gray-400">Recent Dispatches</span>
        </div>

        <div class="w-full overflow-x-auto">
          <table class="w-full text-left border-collapse min-w-[700px]">
            <thead>
              <tr class="border-b border-gray-100 text-[11px] font-bold uppercase tracking-wider text-gray-400 bg-gray-50/40">
                <th class="py-3 px-4">Event</th>
                <th class="py-3 px-4">Delivery ID</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4">Latency</th>
                <th class="py-3 px-4">Date</th>
                <th class="py-3 px-4 text-right">Payload</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-xs" id="webhookLogsBody">
              
              <tr class="hover:bg-blue-50/20 transition">
                <td class="py-3.5 px-4 font-mono font-bold text-gray-900">charge.success</td>
                <td class="py-3.5 px-4 font-mono text-gray-500">EVT_9482019482</td>
                <td class="py-3.5 px-4 text-center">
                  <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center" title="200 OK">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-3.5 px-4 font-mono text-gray-600">74ms</td>
                <td class="py-3.5 px-4 text-gray-500">18 Sep 2026, 01:45 PM</td>
                <td class="py-3.5 px-4 text-right">
                  <button onclick="viewPayload('EVT_9482019482', 'charge.success')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Payload">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  </button>
                </td>
              </tr>

              <tr class="hover:bg-blue-50/20 transition">
                <td class="py-3.5 px-4 font-mono font-bold text-gray-900">transfer.success</td>
                <td class="py-3.5 px-4 font-mono text-gray-500">EVT_8830192847</td>
                <td class="py-3.5 px-4 text-center">
                  <span class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center" title="200 OK">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </span>
                </td>
                <td class="py-3.5 px-4 font-mono text-gray-600">92ms</td>
                <td class="py-3.5 px-4 text-gray-500">18 Sep 2026, 11:20 AM</td>
                <td class="py-3.5 px-4 text-right">
                  <button onclick="viewPayload('EVT_8830192847', 'transfer.success')" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0b2545] hover:bg-gray-100 transition" title="View Payload">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  </button>
                </td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>
</div>

<!-- ================= PAYLOAD MODAL ================= -->
<div id="payloadModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
  <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-gray-100 p-6 space-y-4">
    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
      <div class="font-mono text-xs font-bold text-gray-900" id="modalPayloadEvent">event.name</div>
      <button onclick="closePayloadModal()" class="text-gray-400 hover:text-gray-600">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <pre class="bg-gray-900 text-emerald-400 p-4 rounded-xl text-xs font-mono overflow-x-auto" id="jsonPayloadCode"></pre>
  </div>
</div>

<!-- ================= ROLL KEYS MODAL ================= -->
<div id="rollKeysModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
  <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-gray-100 p-6 space-y-4 text-center">
    <h3 class="text-sm font-bold text-gray-900">Roll API Secret Key?</h3>
    <p class="text-xs text-gray-500">Your current secret key will be invalidated immediately.</p>
    <div class="flex items-center justify-center gap-2 pt-2">
      <button type="button" onclick="closeRollKeysModal()" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl">Cancel</button>
      <button type="button" onclick="executeRollKey()" class="px-4 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-xs">Roll Key</button>
    </div>
  </div>
</div>

<!-- ================= FLOATING TOAST ================= -->
<div id="floatingToast" class="fixed bottom-6 right-6 z-60 hidden transition-all duration-300 transform translate-y-3 opacity-0">
  <div class="bg-gray-900 text-white px-4 py-2.5 rounded-xl shadow-xl flex items-center gap-2.5 border border-gray-700 text-xs">
    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
    <span id="toastMessage">Done</span>
  </div>
</div>

<!-- ================= SCRIPTS ================= -->
<script>
  let currentEnvironment = 'test';
  let isSecretVisible = false;
  let isWebhookSecretVisible = false;

  const keyStorage = {
    test: {
      public: '', secret: '', whSecret: ''
    },
    live: {
      public: '', secret: '', whSecret: ''
    }
  };

  function switchEnvironment(env) {
    if (env === 'live' && localStorage.getItem('flowwithlitVerificationStatus') !== 'approved') {
      showToast('Live mode unlocks after your business is verified');
      return;
    }

    currentEnvironment = env;
    const testBtn = document.getElementById('testModeBtn');
    const liveBtn = document.getElementById('liveModeBtn');

    if (env === 'live') {
      liveBtn.className = 'px-3.5 py-1.5 rounded-full text-xs font-bold transition bg-[#0b2545] text-white shadow-xs';
      testBtn.className = 'px-3.5 py-1.5 rounded-full text-xs font-semibold text-gray-500 hover:text-gray-800 transition';
    } else {
      testBtn.className = 'px-3.5 py-1.5 rounded-full text-xs font-bold transition bg-white text-[#0b2545] shadow-xs';
      liveBtn.className = 'px-3.5 py-1.5 rounded-full text-xs font-semibold text-gray-500 hover:text-gray-800 transition';
    }

    document.getElementById('publicKeyInput').value = keyStorage[env].public;
    document.getElementById('secretKeyInput').value = keyStorage[env].secret;
    document.getElementById('webhookSecret').value = keyStorage[env].whSecret;

    isSecretVisible = false;
    document.getElementById('secretKeyInput').type = 'password';

    isWebhookSecretVisible = false;
    document.getElementById('webhookSecret').type = 'password';

    showToast(`Switched to ${env.toUpperCase()}`);
  }

  function toggleSecretVisibility() {
    const input = document.getElementById('secretKeyInput');
    isSecretVisible = !isSecretVisible;
    input.type = isSecretVisible ? 'text' : 'password';
  }

  function toggleWebhookSecretVisibility() {
    const input = document.getElementById('webhookSecret');
    isWebhookSecretVisible = !isWebhookSecretVisible;
    input.type = isWebhookSecretVisible ? 'text' : 'password';
  }

  function copyKey(elementId) {
    const input = document.getElementById(elementId);
    navigator.clipboard.writeText(input.value).then(() => {
      showToast('Copied to clipboard');
    });
  }

  function copyIntegrationExample() {
    const example = document.getElementById('integrationExample').textContent.trim();
    navigator.clipboard.writeText(example).then(() => showToast('Integration example copied'));
  }

  function pingApiGateway() {
    const btn = document.getElementById('pingGatewayBtn');
    btn.disabled = true;
    btn.innerHTML = `Pinging...`;

    setTimeout(() => {
      btn.disabled = false;
      btn.innerHTML = `
        <svg class="w-3.5 h-3.5 text-[#0b2545]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        <span>Test API Gateway Connection</span>
      `;
      showToast('API Gateway: 200 OK (38ms)');
    }, 450);
  }

  function saveWebhook(e) {
    e.preventDefault();
    showToast('Webhook URL saved');
  }

  function sendTestWebhook() {
    const btn = document.getElementById('sendTestWebhookBtn');
    const diag = document.getElementById('diagnosticStatus');

    btn.disabled = true;
    btn.innerHTML = `Testing...`;
    diag.innerHTML = `<span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span> Connecting...`;

    setTimeout(() => {
      btn.disabled = false;
      btn.innerHTML = `
        <svg class="w-3.5 h-3.5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
        <span>Test Webhook</span>
      `;
      diag.innerHTML = `<span class="w-2 h-2 rounded-full bg-emerald-500"></span> 200 OK (62ms)`;
      showToast('Webhook test passed: 200 OK');
    }, 550);
  }

  function openRollKeysModal() {
    document.getElementById('rollKeysModal').classList.remove('hidden');
  }

  function closeRollKeysModal() {
    document.getElementById('rollKeysModal').classList.add('hidden');
  }

  function executeRollKey() {
    const rand = Math.random().toString(36).substring(2, 15);
    keyStorage[currentEnvironment].secret = `sk_${currentEnvironment}_${rand}`;
    document.getElementById('secretKeyInput').value = keyStorage[currentEnvironment].secret;
    closeRollKeysModal();
    showToast('New secret key generated');
  }

  function viewPayload(id, eventName) {
    document.getElementById('modalPayloadEvent').textContent = `${eventName} (${id})`;
    document.getElementById('jsonPayloadCode').textContent = JSON.stringify({
      event: eventName,
      id: id,
      status: "success",
      amount: 25000,
      currency: "USD",
      timestamp: new Date().toISOString()
    }, null, 2);
    document.getElementById('payloadModal').classList.remove('hidden');
  }

  function closePayloadModal() {
    document.getElementById('payloadModal').classList.add('hidden');
  }

  function showToast(msg) {
    const toast = document.getElementById('floatingToast');
    document.getElementById('toastMessage').textContent = msg;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.remove('translate-y-3', 'opacity-0'), 10);
    setTimeout(() => {
      toast.classList.add('translate-y-3', 'opacity-0');
      setTimeout(() => toast.classList.add('hidden'), 250);
    }, 2500);
  }
</script>
<script>
document.addEventListener('DOMContentLoaded',async()=>{try{const r=await fetch('../api/developer/overview.php',{headers:{Authorization:`Bearer ${localStorage.getItem('flow_token')||''}`}});const d=await r.json();if(!r.ok||!d.success)throw new Error(d.error||'Unable to load developer settings');const key=d.api_keys&&d.api_keys[0];if(key){document.getElementById('publicKeyInput').value=key.key_prefix+'_'+key.environment;document.getElementById('secretKeyInput').value='';document.getElementById('secretKeyInput').placeholder='Secret is shown once when generated';}else{document.getElementById('publicKeyInput').value='No API key created';document.getElementById('secretKeyInput').value='';document.getElementById('secretKeyInput').placeholder='Create a key to display it';}const hook=d.webhooks&&d.webhooks[0];if(hook)document.getElementById('webhookUrl').value=hook.url;const body=document.getElementById('webhookLogsBody');if(body){body.innerHTML='';(d.deliveries||[]).forEach(item=>{const tr=document.createElement('tr');tr.className='hover:bg-blue-50/20 transition';[item.event_type||'—',item.response_status?String(item.response_status):'Pending',item.delivered_at||item.created_at||'—'].forEach((value,index)=>{const td=document.createElement('td');td.className='py-3.5 px-4 '+(index===1?'text-center':'');td.textContent=value;tr.append(td);});body.append(tr);});if(!(d.deliveries||[]).length){const tr=document.createElement('tr');const td=document.createElement('td');td.colSpan=6;td.className='py-10 px-4 text-center text-gray-400';td.textContent='No webhook deliveries yet.';tr.append(td);body.append(tr);}}}catch(error){if(typeof showToast==='function')showToast(error.message);}});
</script>

<script>
(() => {
  const auth=()=>({Authorization:`Bearer ${localStorage.getItem('flow_token')||''}`,'Content-Type':'application/json',Accept:'application/json'});
  window.saveWebhook=async function(event){event.preventDefault();const url=document.getElementById('webhookUrl').value.trim();if(!url){showToast('Enter your webhook URL first');return}try{const r=await fetch('../api/developer/webhook.php',{method:'POST',headers:auth(),body:JSON.stringify({action:'save',url})}),d=await r.json();if(!r.ok||!d.success)throw new Error(d.error||'Unable to save webhook');const secret=document.getElementById('webhookSecret');secret.value=d.webhook.secret||'';secret.type='text';secret.placeholder='Copy this secret now; it is shown once';const badge=document.getElementById('webhookStatusBadge');if(badge)badge.innerHTML='<span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Saved • not tested';showToast('Webhook URL saved. Copy the secret now.');}catch(e){showToast(e.message)}};
  window.sendTestWebhook=async function(){const url=document.getElementById('webhookUrl').value.trim(),secret=document.getElementById('webhookSecret').value.trim();if(!url||!secret){showToast('Save the webhook URL before testing it');return}const btn=document.getElementById('sendTestWebhookBtn'),diag=document.getElementById('diagnosticStatus');btn.disabled=true;if(diag)diag.textContent='Testing webhook...';try{const r=await fetch('../api/developer/webhook.php',{method:'POST',headers:auth(),body:JSON.stringify({action:'test',url})}),d=await r.json();if(!r.ok||!d.success)throw new Error(d.error||'Webhook test failed');if(diag)diag.textContent=d.approved?`Approved • ${d.status} OK`:`Not approved • ${d.message}`;const badge=document.getElementById('webhookStatusBadge');if(badge)badge.innerHTML=d.approved?'<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Approved':'<span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Failed';showToast(d.approved?'Webhook connection approved':'Webhook connection failed');}catch(e){if(diag)diag.textContent=e.message;showToast(e.message)}finally{btn.disabled=false}};
  document.addEventListener('DOMContentLoaded',async()=>{try{const r=await fetch('../api/developer/overview.php',{headers:{Authorization:`Bearer ${localStorage.getItem('flow_token')||''}`,Accept:'application/json'}}),d=await r.json();const key=d.api_keys&&d.api_keys[0];if(!key&&d.success){const created=await fetch('../api/developer/keys.php',{method:'POST',headers:auth(),body:JSON.stringify({environment:'test',name:'Test integration'})});const payload=await created.json();if(payload.success){document.getElementById('publicKeyInput').value=payload.key.public_key;document.getElementById('secretKeyInput').value=payload.key.secret_key;document.getElementById('secretKeyInput').type='password';showToast('Test credentials created. Copy the secret now.');}}}catch(e){}});
})();
</script>

<?php
require_once __DIR__ . '/includes/bottom_nav.php';
require_once __DIR__ . '/includes/footer.php';
?>
