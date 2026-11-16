<?php
$pageTitle = 'Add money — Flowwithlit';
$activePage = 'wallets';
$activeNavTab = 'Features';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>
<main class="flex-1 h-full overflow-y-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-6">
  <div class="max-w-5xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6"><div><a href="./" class="inline-flex items-center gap-1 text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-[#0b2545] dark:hover:text-white"><i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Dashboard</a><h1 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Add money</h1><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Fund a wallet using a bank transfer, card, or crypto deposit.</p></div><span class="inline-flex items-center gap-2 px-3 py-2 rounded-full bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-900 text-xs font-bold text-emerald-700 dark:text-emerald-300"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Funding available</span></div>
    <div id="fundingForm" class="grid lg:grid-cols-5 gap-6">
      <section class="lg:col-span-3 bg-white dark:bg-[#0c1f36] rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs p-5 sm:p-6 space-y-6"><div><h2 class="text-lg font-bold text-gray-900 dark:text-white">Choose a funding method</h2><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Your money will be added after we confirm the deposit.</p></div>
        <div class="grid sm:grid-cols-3 gap-3"><button data-method="bank" class="fund-method rounded-2xl border-2 border-[#0b2545] dark:border-sky-400 bg-[#eef4ff] dark:bg-[#13304f] p-4 text-left transition"><i data-lucide="landmark" class="w-5 h-5 text-[#0b2545] dark:text-sky-300"></i><strong class="mt-3 block text-xs text-gray-900 dark:text-white">Bank transfer</strong><small class="mt-1 block text-[10px] text-gray-500 dark:text-gray-300">No card required</small></button><button data-method="card" class="fund-method rounded-2xl border-2 border-gray-100 dark:border-gray-700 p-4 text-left transition"><i data-lucide="credit-card" class="w-5 h-5 text-[#0b2545] dark:text-sky-300"></i><strong class="mt-3 block text-xs text-gray-900 dark:text-white">Debit card</strong><small class="mt-1 block text-[10px] text-gray-500 dark:text-gray-300">Instant funding</small></button><button data-method="crypto" class="fund-method rounded-2xl border-2 border-gray-100 dark:border-gray-700 p-4 text-left transition"><i data-lucide="bitcoin" class="w-5 h-5 text-[#0b2545] dark:text-sky-300"></i><strong class="mt-3 block text-xs text-gray-900 dark:text-white">Crypto</strong><small class="mt-1 block text-[10px] text-gray-500 dark:text-gray-300">On-chain deposit</small></button></div>
        <div class="grid sm:grid-cols-2 gap-4"><label class="fund-field"><span>Wallet to fund</span><select id="fundWallet"><option value="NGN">Nigerian Naira (NGN)</option><option value="USD">US Dollar (USD)</option><option value="USDT">Tether (USDT)</option></select></label><label class="fund-field"><span>Amount</span><input id="fundAmount" min="1" inputmode="decimal" placeholder="0.00" type="number"></label></div>
        <div id="methodDetails" class="rounded-2xl bg-gray-50 dark:bg-[#13304f] border border-gray-200 dark:border-gray-700 p-5"></div>
      </section>
      <aside class="lg:col-span-2 bg-[#0b2545] text-white rounded-2xl p-5 sm:p-6 shadow-sm h-fit"><span class="text-[10px] font-bold uppercase tracking-wider text-sky-200">Funding summary</span><div class="mt-5 pb-5 border-b border-white/15"><span class="text-xs text-white/65">You are adding</span><div id="summaryAmount" class="mt-1 text-3xl font-bold">₦0.00</div><span id="summaryWallet" class="text-xs text-white/65">to your NGN wallet</span></div><div class="py-5 space-y-3 text-xs"><div class="flex justify-between gap-3"><span class="text-white/65">Method</span><strong id="summaryMethod">Bank transfer</strong></div><div class="flex justify-between gap-3"><span class="text-white/65">Estimated fee</span><strong id="summaryFee">₦0.00</strong></div><div class="flex justify-between gap-3"><span class="text-white/65">Status</span><strong class="text-emerald-300">Ready</strong></div></div><button type="button" onclick="completeFunding()" class="w-full py-3 rounded-xl bg-white hover:bg-[#eef4ff] text-[#0b2545] text-xs font-bold transition">Continue funding</button><p class="mt-3 text-[10px] text-white/55 text-center">Only use accounts and cards that belong to you.</p></aside>
    </div>
    <section id="fundingSuccess" class="hidden max-w-xl mx-auto text-center bg-white dark:bg-[#0c1f36] rounded-3xl border border-gray-200 dark:border-gray-800 shadow-sm p-10"><span class="mx-auto flex w-16 h-16 items-center justify-center rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600"><i data-lucide="circle-check-big" class="w-8 h-8"></i></span><h2 class="mt-5 text-2xl font-bold text-gray-900 dark:text-white">Funding instruction created</h2><p id="successText" class="mt-2 text-sm text-gray-500 dark:text-gray-400"></p><a href="wallets.php" class="inline-flex mt-6 px-4 py-2.5 rounded-xl bg-[#0b2545] text-white text-xs font-semibold">View wallets</a></section>
  </div>
</main></div></div>
<style>.fund-field span{display:block;margin-bottom:.4rem;font-size:.78rem;font-weight:600;color:#374151}.fund-field input,.fund-field select{width:100%;border:1px solid #e5e7eb;border-radius:.75rem;background:#fff;color:#111827;padding:.68rem .75rem;font-size:.84rem;outline:0}.dark .fund-field span{color:#e5e7eb}.dark .fund-field input,.dark .fund-field select{background:#0c1f36;border-color:#374151;color:#fff}</style>
<script>
// Add money: real data only. Bank transfer shows the user's own OnePipe account; card and crypto are not live yet.
(() => {
  const token = localStorage.getItem('flow_token') || '';
  const auth = { Authorization: `Bearer ${token}`, Accept: 'application/json' };
  const symbols = { NGN: '₦', USDT: '₮', USDC: '$', BTC: '₿', ETH: 'Ξ', SOL: '◎' };
  const $ = (id) => document.getElementById(id);
  const esc = (v) => String(v ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c]));
  let method = 'bank';
  let wallets = [];
  const ngn = () => wallets.find((w) => w.currency_code === 'NGN');

  function bankDetails() {
    const w = ngn();
    if (!w) return `<h3 class="text-sm font-bold text-gray-900 dark:text-white">Bank transfer</h3><p class="mt-1 text-xs text-gray-600 dark:text-gray-300">Add a Naira (NGN) wallet first to get your own account number.</p>`;
    if (w.account_number && w.receiving_status === 'active') {
      return `<div class="flex items-start justify-between gap-4"><div><h3 class="text-sm font-bold text-gray-900 dark:text-white">Your Naira account</h3><p class="mt-1 text-xs text-gray-600 dark:text-gray-300">Transfer from any Nigerian bank app to this account. It belongs to you only.</p></div><span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-300">FREE</span></div>
        <div class="mt-4 grid sm:grid-cols-2 gap-3 text-xs">
          <div><span class="text-gray-500">Bank</span><strong class="block text-gray-900 dark:text-white">${esc(w.bank_name || '—')}</strong></div>
          <div><span class="text-gray-500">Account number</span><strong class="block font-mono text-base text-gray-900 dark:text-white">${esc(w.account_number)}</strong><button type="button" id="copyAccount" class="mt-1 text-xs font-bold text-[#0b2545] dark:text-sky-300 hover:underline">Copy number</button></div>
          <div class="sm:col-span-2"><span class="text-gray-500">Account name</span><strong class="block text-gray-900 dark:text-white">${esc(w.account_name || '—')}</strong></div>
        </div>`;
    }
    if (w.receiving_status === 'provisioning') {
      return `<h3 class="text-sm font-bold text-gray-900 dark:text-white">Your account number is being created</h3><p class="mt-1 text-xs text-gray-600 dark:text-gray-300">This usually takes a few minutes. Refresh this page shortly.</p>`;
    }
    return `<h3 class="text-sm font-bold text-gray-900 dark:text-white">Get your Naira account number</h3><p class="mt-1 text-xs text-gray-600 dark:text-gray-300">We will open a personal Nigerian bank account number for your wallet. Anyone can then send money to it.</p>
      <button type="button" id="provisionAccount" class="mt-4 rounded-full bg-[#0b2545] px-5 py-2.5 text-xs font-bold text-white hover:bg-[#16355f]">Get my account number</button><p id="provisionStatus" class="mt-2 text-xs text-gray-600 dark:text-gray-300" role="status"></p>`;
  }
  const unavailable = (title, text) => `<h3 class="text-sm font-bold text-gray-900 dark:text-white">${title}</h3><p class="mt-1 text-xs text-gray-600 dark:text-gray-300">${text}</p>`;
  const details = {
    bank: bankDetails,
    card: () => unavailable('Card top-up is not available yet', 'Please use a bank transfer to your Naira account for now.'),
    crypto: () => unavailable('Crypto deposits are not available yet', 'Crypto deposit addresses will appear here once they are live. Please do not send crypto until then.'),
  };
  const ready = () => method === 'bank' && ngn()?.receiving_status === 'active' && !!ngn()?.account_number;

  function updateFunding() {
    const code = $('fundWallet').value || 'NGN';
    const amount = Number($('fundAmount').value || 0);
    $('methodDetails').innerHTML = details[method]();
    $('summaryAmount').textContent = `${symbols[code] || ''}${amount.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 8 })}`;
    $('summaryWallet').textContent = `to your ${code} wallet`;
    $('summaryMethod').textContent = { bank: 'Bank transfer', card: 'Debit card', crypto: 'Crypto deposit' }[method];
    $('summaryFee').textContent = `${symbols[code] || ''}0.00`;
    const btn = document.querySelector('[onclick="completeFunding()"]');
    if (btn) { btn.disabled = !ready(); btn.classList.toggle('opacity-50', !ready()); btn.classList.toggle('cursor-not-allowed', !ready()); }
    $('copyAccount')?.addEventListener('click', async (e) => { try { await navigator.clipboard.writeText(ngn().account_number); e.target.textContent = 'Copied'; } catch (_) {} });
    $('provisionAccount')?.addEventListener('click', provision);
  }

  async function provision(e) {
    e.target.disabled = true; $('provisionStatus').textContent = 'Creating your account number…';
    try {
      const r = await fetch('../api/wallets/provision-fiat.php', { method: 'POST', headers: { ...auth, 'Content-Type': 'application/json' }, body: JSON.stringify({ currency: 'NGN' }) });
      const d = await r.json();
      if (!r.ok || !d.success) throw new Error(d.error || 'Unable to create your account number right now.');
      await loadWallets();
    } catch (err) { $('provisionStatus').textContent = err.message; e.target.disabled = false; }
  }

  async function loadWallets() {
    const select = $('fundWallet');
    try {
      const r = await fetch('../api/wallets/list.php', { headers: auth });
      const d = await r.json();
      if (!r.ok || !d.success) throw new Error(d.error || 'Unable to load wallets');
      wallets = d.wallets || [];
      const previous = select.value;
      select.innerHTML = wallets.length ? wallets.map((w) => `<option value="${esc(w.currency_code)}">${esc(w.name)} (${esc(w.currency_code)}) · ${Number(w.available_balance || 0).toLocaleString('en-NG', { maximumFractionDigits: 8 })}</option>`).join('') : '<option value="">No wallets yet</option>';
      if (previous && wallets.some((w) => w.currency_code === previous)) select.value = previous;
    } catch (err) {
      select.innerHTML = '<option value="">Unable to load wallets</option>';
    }
    updateFunding();
  }

  document.querySelectorAll('.fund-method').forEach((button) => button.addEventListener('click', () => {
    method = button.dataset.method;
    document.querySelectorAll('.fund-method').forEach((item) => { item.className = 'fund-method rounded-2xl border-2 border-gray-100 dark:border-gray-700 p-4 text-left transition'; });
    button.className = 'fund-method rounded-2xl border-2 border-[#0b2545] dark:border-sky-400 bg-[#eef4ff] dark:bg-[#13304f] p-4 text-left transition';
    updateFunding();
  }));
  $('fundAmount').addEventListener('input', updateFunding);
  $('fundWallet').addEventListener('change', updateFunding);

  // Bank transfers are pushed by the bank, so "Continue" confirms the next step; the wallet updates when OnePipe confirms the deposit.
  window.completeFunding = () => {
    if (!ready()) return;
    $('fundingForm').classList.add('hidden');
    $('successText').textContent = `Send the money to ${ngn().bank_name || 'your bank'} account ${ngn().account_number}. Your wallet updates automatically once the transfer is confirmed.`;
    $('fundingSuccess').classList.remove('hidden');
    window.lucide?.createIcons();
  };

  $('fundWallet').innerHTML = '<option value="">Loading wallets…</option>';
  loadWallets();
})();
</script>
<?php require_once __DIR__ . '/includes/bottom_nav.php'; require_once __DIR__ . '/includes/footer.php'; ?>
