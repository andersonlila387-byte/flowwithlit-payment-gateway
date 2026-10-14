(() => {
  'use strict';
  const $ = selector => document.querySelector(selector);
  const api = `${window.dashboardConfig.apiBase}/payouts.php`;
  const modal = $('#withdraw-decision-modal');
  const form = $('#withdraw-decision-form');
  const esc = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const money = (amount, currency = 'NGN') => `${currency} ${Number(amount || 0).toLocaleString('en-NG', {maximumFractionDigits: 8})}`;
  const date = value => value ? new Date(String(value).replace(' ', 'T') + 'Z').toLocaleString() : '—';
  let payouts = [], modalGeneration = 0;
  const toast = text => { $('#toast').textContent = text; $('#toast').hidden = false; setTimeout(() => $('#toast').hidden = true, 4000); };
  const close = () => { modalGeneration++; modal.hidden = true; form.reset(); };
  async function request(body) {
    const options = {headers: {Authorization: `Bearer ${localStorage.getItem('flow_admin_token') || ''}`}};
    if (body) { options.method = 'POST'; options.headers['Content-Type'] = 'application/json'; options.body = JSON.stringify(body); }
    const response = await fetch(api, options), result = await response.json();
    if (!response.ok || !result.success) throw Error(result.error || 'Request failed.');
    return result;
  }
  const details = document.createElement('div');
  details.innerHTML = '<p id="payout-destination" class="panel-description" style="overflow-wrap:anywhere" role="status"></p><label id="payout-reference-label">Bank or blockchain payment reference<input name="provider_reference" maxlength="191" autocomplete="off"></label><label id="payout-not-paid-label"><input name="confirmed_not_paid" type="checkbox" value="1"> I confirmed that no payment was sent. Release the reserved funds.</label>';
  form.insertBefore(details, form.querySelector('.modal-actions'));
  form.elements.review_note.required = true; form.elements.review_note.minLength = 10;
  async function show(payout, status) {
    const generation = ++modalGeneration;
    form.reset(); form.elements.payout_id.value = payout.id; form.elements.status.value = status;
    $('#withdraw-decision-title').textContent = `Mark withdrawal ${status}`;
    $('#withdraw-decision-copy').textContent = `${payout.full_name}: ${money(payout.amount, payout.currency_code)}. Due ${date(payout.due_at)}.`;
    $('#withdraw-decision-submit').textContent = `Mark ${status}`;
    $('#payout-reference-label').hidden = status !== 'paid'; form.elements.provider_reference.required = status === 'paid';
    $('#payout-not-paid-label').hidden = !['failed', 'cancelled'].includes(status);
    form.elements.confirmed_not_paid.required = ['failed', 'cancelled'].includes(status);
    $('#payout-destination').textContent = 'Loading payment destination…'; modal.hidden = false;
    try {
      const result = await request({action: 'destination', payout_id: payout.id});
      if (generation !== modalGeneration) return;
      const d = result.destination;
      $('#payout-destination').textContent = d.account_number
        ? `${d.account_name} · bank code ${d.bank_code} · ${d.account_number}`
        : `${d.network} · ${d.address}${d.memo ? ` · Memo: ${d.memo}` : ''}`;
    } catch (error) { if (generation === modalGeneration) $('#payout-destination').textContent = error.message; }
  }
  function totals(items, field = 'amount') {
    const sums = new Map();
    for (const item of items) sums.set(item.currency_code, (sums.get(item.currency_code) || 0) + Number(item[field] || 0));
    return [...sums].map(([currency, value]) => money(value, currency)).join(' · ') || '—';
  }
  function actions(p) {
    if (p.execution_mode !== 'manual') return 'Provider reconciliation';
    const statuses = p.status === 'pending' ? [['approved','Approve'], ['failed','Decline']] : [['paid','Mark paid'], ['failed','Mark failed']];
    return '<div class="withdraw-action-group">' + statuses.map(([status, label]) => `<button class="withdraw-action ${status === 'failed' ? 'fail' : 'approve'}" data-payout="${esc(p.id)}" data-status="${status}">${label}</button>`).join('') + '</div>';
  }
  async function load() {
    $('#withdraw-loading').hidden = false; $('#withdraw-loading').textContent = 'Loading withdrawal requests…';
    try {
      payouts = (await request()).payouts;
      const awaiting = payouts.filter(p => ['pending','approved','processing'].includes(p.status));
      const paid = payouts.filter(p => p.status === 'paid');
      $('#withdraw-total').textContent = totals(payouts); $('#withdraw-pending').textContent = awaiting.length;
      $('#withdraw-pending-value').textContent = totals(awaiting); $('#withdraw-paid').textContent = totals(paid);
      $('#withdraw-paid-count').textContent = paid.length; $('#withdraw-unsuccessful').textContent = totals(payouts.filter(p => ['failed','cancelled'].includes(p.status)));
      $('#withdraw-pending-rows').innerHTML = awaiting.map(p => `<tr><td><strong>${esc(p.full_name)}</strong><small>${esc(p.email)}</small></td><td>${esc(p.uuid)}</td><td>${esc(p.destination_display || p.bank_name || 'Legacy request')}</td><td>${esc(money(p.amount,p.currency_code))}</td><td>${esc(date(p.created_at))}<small>${Number(p.overdue) ? 'OVERDUE · ' : ''}Due ${esc(date(p.due_at))}</small></td><td>${esc(p.status)}</td><td>${actions(p)}</td></tr>`).join('');
      $('#withdraw-pending-empty').hidden = awaiting.length > 0;
      $('#withdraw-all-rows').innerHTML = payouts.map(p => `<tr><td>${esc(p.uuid)}</td><td>${esc(p.full_name)}</td><td>${esc(p.currency_code)}</td><td>${esc(money(p.amount,p.currency_code))}</td><td>${esc(money(p.fee_amount,p.currency_code))}</td><td>${esc(p.provider_reference || '—')}</td><td>${esc(p.status)}</td><td>${esc(date(p.updated_at))}</td></tr>`).join('');
      $('#withdraw-all-empty').hidden = payouts.length > 0; $('#withdraw-loading').hidden = true; $('#withdraw-content').hidden = false;
    } catch (error) { $('#withdraw-loading').textContent = error.message; }
  }
  document.addEventListener('click', event => {
    const button = event.target.closest('[data-payout][data-status]');
    if (button) { const p = payouts.find(p => String(p.id) === button.dataset.payout); if (p) show(p, button.dataset.status); }
  });
  document.querySelectorAll('[data-close-decision]').forEach(button => button.addEventListener('click', close));
  document.addEventListener('keydown', e => { if(e.key === 'Escape') close(); });
  $('#withdraw-refresh').addEventListener('click', load);
  form.addEventListener('submit', async event => {
    event.preventDefault(); const button = $('#withdraw-decision-submit'); if (button.disabled) return;
    button.disabled = true; const text = button.textContent; button.textContent = 'Saving…';
    try { await request(Object.fromEntries(new FormData(form))); close(); toast('Withdrawal updated.'); await load(); }
    catch (error) { toast(error.message); }
    finally { button.disabled = false; button.textContent = text; }
  });
  load();
})();
