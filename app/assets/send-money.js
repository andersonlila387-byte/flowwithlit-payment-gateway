(() => {
  'use strict';
  const $ = id => document.getElementById(id);
  const auth = () => ({Authorization: `Bearer ${localStorage.getItem('flow_token') || ''}`, Accept: 'application/json'});
  let pending = false, requestKey = null, requestBody = null;
  const format = (value, currency) => `${currency || ''} ${Number(value || 0).toLocaleString(undefined, {maximumFractionDigits: 8})}`;
  function summary() {
    const amount = $('sendAmount').value, currency = $('sourceWallet').value;
    $('receiveAmount').textContent = format(amount, currency);
    $('receiveCurrency').textContent = currency;
    $('transferFee').textContent = format(0, currency);
    $('totalDebit').textContent = format(amount, currency);
  }
  async function request(path, options = {}) {
    const r = await fetch(path, {...options, headers: {...auth(), ...options.headers}});
    const data = await r.json();
    if (!r.ok || !data.success) throw Error(data.error || 'Request failed.');
    return data;
  }
  async function load() {
    $('sendSubmit').disabled = true;
    try {
      const [data, settings] = await Promise.all([request('../api/wallets/list.php'), request('../api/settings/profile.php')]);
      $('sourceWallet').replaceChildren();
      for (const wallet of data.wallets.filter(w => w.status === 'active')) {
        $('sourceWallet').add(new Option(format(wallet.available_balance, wallet.currency_code), wallet.currency_code));
      }
      const pinSet = Boolean((settings.profile || settings.user || {}).withdrawal_pin_set);
      $('pinSection').classList.toggle('hidden', !pinSet);
      $('pinMissing').classList.toggle('hidden', pinSet);
      $('sendSubmit').disabled = !pinSet || !data.wallets.length;
      if (!data.wallets.length) $('sendStatus').textContent = 'Activate and fund a wallet first.';
      summary();
    } catch (error) { $('sendStatus').textContent = error.message; }
  }
  $('sendSubmit').addEventListener('click', async () => {
    if (pending) return;
    const payload = {action: 'transfer', amount: $('sendAmount').value, currency: $('sourceWallet').value,
      recipient_account: $('recipientAccount').value.trim(), note: $('transferNote').value.trim()};
    if (!payload.amount || !payload.currency || !payload.recipient_account.startsWith('@')) {
      $('sendStatus').textContent = 'Enter an amount and the recipient’s Flow Tag.'; return;
    }
    if (!/^\d{4}$/.test($('sendPin').value)) { $('sendStatus').textContent = 'Enter your four-digit PIN.'; return; }
    const fingerprint = JSON.stringify(payload);
    if (requestBody !== fingerprint) { requestBody = fingerprint; requestKey = crypto.randomUUID(); }
    pending = true; $('sendSubmit').disabled = true; $('sendSubmit').textContent = 'Sending…'; $('sendStatus').textContent = '';
    try {
      const result = await request('../api/financial/action.php', {method: 'POST',
        headers: {'Content-Type': 'application/json', 'Idempotency-Key': requestKey},
        body: JSON.stringify({...payload, pin: $('sendPin').value})});
      $('sendPin').value = ''; $('sendForm').classList.add('hidden');
      $('sendSuccessText').textContent = `Transfer ${result.reference} completed. The recipient’s wallet has been credited.`;
      $('sendSuccess').classList.remove('hidden');
    } catch (error) { $('sendStatus').textContent = error.message; }
    finally { pending = false; $('sendSubmit').disabled = false; $('sendSubmit').textContent = 'Send money'; }
  });
  $('sourceWallet').addEventListener('change', summary);
  $('sendAmount').addEventListener('input', summary);
  load();
})();
