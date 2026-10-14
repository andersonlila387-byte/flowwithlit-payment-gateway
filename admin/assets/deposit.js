(() => {
  'use strict';
  const $ = selector => document.querySelector(selector);
  const api = `${window.dashboardConfig.apiBase}/deposits.php`;
  const token = () => localStorage.getItem('flow_admin_token') || '';
  const modal = $('#manual-credit-modal');
  const form = $('#manual-credit-form');
  const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
  const money = (amount, currency = 'NGN') => new Intl.NumberFormat('en-NG', {style:'currency', currency}).format(Number(amount || 0));
  const date = value => value ? new Date(String(value).replace(' ', 'T') + 'Z').toLocaleString() : '—';
  const toast = message => { const node = $('#toast'); if (!node) return; node.textContent = message; node.hidden = false; setTimeout(() => { node.hidden = true; }, 3600); };
  const setModal = show => { modal.hidden = !show; if (show) setTimeout(() => $('#credit-wallet').focus(), 0); };
  const render = data => {
    const summary = data.summary;
    $('#deposits-today').textContent = money(summary.deposits_today);
    $('#deposit-count-today').textContent = summary.deposit_count_today;
    $('#deposits-month').textContent = money(summary.deposits_30_days);
    $('#manual-credits').textContent = money(summary.manual_credits_today);
    $('#manual-credit-count').textContent = summary.manual_credit_count;
    $('#unprocessed-events').textContent = summary.unprocessed_events;
    const select = $('#credit-wallet');
    select.replaceChildren(new Option('Select an active wallet', ''));
    (data.wallets || []).forEach(wallet => select.add(new Option(`${wallet.full_name} · ${wallet.email} · ${wallet.currency_code} (${money(wallet.available_balance, wallet.currency_code)})`, wallet.uuid)));
    const deposits = data.deposits || [];
    $('#deposit-rows').innerHTML = deposits.map(item => {
      const isManual = item.provider === 'admin_manual_credit';
      const method = isManual ? 'Manual credit' : (item.provider || 'Provider deposit');
      const details = isManual ? `${item.admin_name || 'Administrator'} · ${item.external_reference || item.provider_reference || '—'}<small>${item.reason || ''}</small>` : (item.provider_reference || '—');
      return `<tr><td><strong>${escapeHtml(item.reference)}</strong></td><td><strong>${escapeHtml(item.full_name)}</strong><small>${escapeHtml(item.email)}</small></td><td><span class="deposit-method ${isManual ? 'manual' : ''}">${escapeHtml(method)}</span></td><td>${escapeHtml(money(item.amount,item.currency_code))}</td><td><span class="operation-status ${escapeHtml(item.status)}">${escapeHtml(item.status)}</span></td><td>${escapeHtml(date(item.created_at))}</td><td class="deposit-detail">${escapeHtml(details.split('<small>')[0])}${isManual && item.reason ? `<small>${escapeHtml(item.reason)}</small>` : ''}</td></tr>`;
    }).join('');
    $('#deposit-empty').hidden = deposits.length > 0;
    $('#deposit-loading').hidden = true;
    $('#deposit-content').hidden = false;
    if (window.lucide) window.lucide.createIcons();
  };
  const load = () => fetch(api, {headers:{Authorization:`Bearer ${token()}`}}).then(async response => { const body = await response.json(); if (!response.ok || !body.success) throw new Error(body.error || 'Unable to load deposits.'); return body; }).then(render).catch(error => { $('#deposit-loading').textContent = error.message; });
  $('#open-manual-credit').addEventListener('click', () => setModal(true));
  document.querySelectorAll('[data-close-credit-modal]').forEach(button => button.addEventListener('click', () => setModal(false)));
  document.addEventListener('keydown', event => { if (event.key === 'Escape' && !modal.hidden) setModal(false); });
  form.addEventListener('submit', event => {
    event.preventDefault();
    const submit = $('#submit-manual-credit');
    const fields = Object.fromEntries(new FormData(form));
    submit.disabled = true; const original = submit.innerHTML; submit.innerHTML = '<span class="spinner"></span> Recording…';
    fetch(api, {method:'POST', headers:{'Content-Type':'application/json', Authorization:`Bearer ${token()}`}, body:JSON.stringify({action:'manual_credit', ...fields})})
      .then(async response => { const body = await response.json(); if (!response.ok || !body.success) throw new Error(body.error || 'Unable to record credit.'); return body; })
      .then(result => { form.reset(); setModal(false); toast(`${result.message} ${result.credit.reference}`); load(); })
      .catch(error => toast(error.message))
      .finally(() => { submit.disabled = false; submit.innerHTML = original; if (window.lucide) window.lucide.createIcons(); });
  });
  load();
})();
