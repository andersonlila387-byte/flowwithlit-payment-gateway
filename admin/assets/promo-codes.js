(() => {
  'use strict';
  const $ = (s, r = document) => r.querySelector(s);
  const api = `${window.dashboardConfig.apiBase}/promo-codes.php`;
  const token = () => localStorage.getItem('flow_admin_token') || '';
  const modal = $('#promo-modal'), form = $('#promo-form');
  const icons = () => window.lucide?.createIcons({ attrs: { 'stroke-width': 1.8 } });
  const toast = m => (window.dashboardShowToast ? window.dashboardShowToast(m) : null);
  const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const money = (n, cur = 'NGN') => new Intl.NumberFormat('en-NG', { style: 'currency', currency: cur, maximumFractionDigits: 2 }).format(Number(n || 0));
  const day = v => v ? new Date(`${String(v).slice(0, 10)}T00:00:00`).toLocaleDateString('en-NG', { day: 'numeric', month: 'short', year: 'numeric' }) : null;
  const TYPES = {
    wallet_bonus: { label: 'Bonus amount (₦)', percent: false, text: p => `${money(p.value)} wallet bonus` },
    cashback: { label: 'Cashback (%)', percent: true, text: p => `${p.value}% cashback` },
    fee_discount: { label: 'Fee discount (%)', percent: true, text: p => `${p.value}% off fees` },
    free_transfers: { label: 'Number of free transfers', percent: false, text: p => `${p.value} fee-free transfer${Number(p.value) === 1 ? '' : 's'}` },
  };
  const AUDIENCE = { all: 'All users', new_users: 'New users only', verified: 'Verified users only' };
  let promos = [];

  const statusOf = p => {
    const now = new Date(), start = p.starts_at && new Date(`${p.starts_at.slice(0, 10)}T00:00:00`), end = p.expires_at && new Date(`${p.expires_at.slice(0, 10)}T23:59:59`);
    if (p.status === 'paused') return 'paused';
    if (end && end < now) return 'expired';
    if (p.usage_limit && Number(p.used_count) >= Number(p.usage_limit)) return 'exhausted';
    if (start && start > now) return 'scheduled';
    return 'active';
  };
  const STATUS_LABEL = { active: 'Active', scheduled: 'Scheduled', paused: 'Paused', expired: 'Expired', exhausted: 'Fully used' };

  const renderRows = () => {
    const q = $('#promo-search').value.trim().toLowerCase(), type = $('#promo-type-filter').value, status = $('#promo-status-filter').value;
    const rows = promos.filter(p => (!q || `${p.code} ${p.title}`.toLowerCase().includes(q)) && (!type || p.type === type) && (!status || statusOf(p) === status));
    $('#promo-rows').innerHTML = rows.map(p => {
      const st = statusOf(p), used = Number(p.used_count || 0), limit = Number(p.usage_limit || 0), pct = limit ? Math.min(100, used / limit * 100) : 0;
      const cap = TYPES[p.type]?.percent && p.max_reward ? `<small>Up to ${esc(money(p.max_reward))}</small>` : '';
      const min = p.min_amount ? `<small>On payments from ${esc(money(p.min_amount))}</small>` : '';
      const from = day(p.starts_at), to = day(p.expires_at);
      return `<tr>
        <td><span class="promo-code">${esc(p.code)}</span><small>${esc(p.title)}</small></td>
        <td><strong>${esc(TYPES[p.type]?.text(p) || p.type)}</strong>${cap}${min}</td>
        <td>${esc(AUDIENCE[p.audience] || 'All users')}<small>${esc(p.per_user_limit || 1)} use${Number(p.per_user_limit) === 1 ? '' : 's'} per user</small></td>
        <td class="promo-usage">${used}${limit ? ` of ${limit}` : ' used'}${limit ? `<div class="promo-bar" aria-hidden="true"><i style="width:${pct}%"></i></div>` : '<small>No limit</small>'}</td>
        <td>${from ? esc(from) : 'Now'}<small>${to ? `Ends ${esc(to)}` : 'No end date'}</small></td>
        <td><span class="promo-status ${st}">${STATUS_LABEL[st]}</span></td>
        <td><div class="promo-actions">
          <button class="icon-button" type="button" data-copy="${esc(p.code)}" aria-label="Copy code ${esc(p.code)}"><i data-lucide="copy" class="icon" aria-hidden="true"></i></button>
          <button class="icon-button" type="button" data-edit="${esc(p.uuid)}" aria-label="Edit ${esc(p.code)}"><i data-lucide="pencil" class="icon" aria-hidden="true"></i></button>
          <button class="icon-button" type="button" data-toggle="${esc(p.uuid)}" aria-label="${p.status === 'paused' ? 'Resume' : 'Pause'} ${esc(p.code)}"><i data-lucide="${p.status === 'paused' ? 'play' : 'pause'}" class="icon" aria-hidden="true"></i></button>
        </div></td></tr>`;
    }).join('');
    const empty = $('#promo-empty');
    empty.hidden = rows.length > 0;
    empty.textContent = promos.length ? 'No promo codes match these filters.' : 'No promo codes yet. Create one to start rewarding users.';
    icons();
  };

  const render = data => {
    const s = data.summary || {};
    $('#promo-active').textContent = s.active_codes ?? 0;
    $('#promo-redemptions').textContent = Number(s.total_redemptions || 0).toLocaleString('en-NG');
    $('#promo-paid').textContent = money(s.rewards_paid);
    $('#promo-expiring').textContent = s.expiring_soon ?? 0;
    promos = data.promos || [];
    $('#promo-loading').hidden = true; $('#promo-error').hidden = true; $('#promo-content').hidden = false;
    renderRows();
  };

  const request = async (options = {}) => {
    const res = await fetch(api, { ...options, headers: { Authorization: `Bearer ${token()}`, ...(options.body ? { 'Content-Type': 'application/json' } : {}) } });
    const body = await res.json().catch(() => ({}));
    if (!res.ok || !body.success) throw new Error(body.error || 'Something went wrong. Try again.');
    return body;
  };
  const load = () => {
    $('#promo-error').hidden = true; if (!promos.length) $('#promo-loading').hidden = false;
    return request().then(render).catch(e => { $('#promo-loading').hidden = true; $('#promo-content').hidden = true; $('#promo-error').hidden = false; $('#promo-error span').textContent = e.message; icons(); });
  };

  // ---- modal ----
  const syncType = () => {
    const t = TYPES[form.type.value];
    $('#promo-value-label').textContent = t.label;
    form.querySelector('[data-when="percent"]').hidden = !t.percent;
    form.value.max = t.percent ? 100 : '';
    const v = form.value.value;
    $('#promo-summary-line').textContent = v ? `Users get ${t.text({ value: v })}${form.min_amount.value ? ` on payments from ${money(form.min_amount.value)}` : ''}. Rewards are credited to their wallet automatically.` : "Rewards are credited to the user's wallet automatically when they qualify.";
  };
  const openModal = promo => {
    form.reset(); form.uuid.value = promo?.uuid || '';
    $('#promo-modal-title').textContent = promo ? `Edit ${promo.code}` : 'New promo code';
    if (promo) Object.keys(promo).forEach(k => { if (form.elements[k] && k !== 'uuid') form.elements[k].value = String(promo[k] ?? '').slice(0, k.endsWith('_at') ? 10 : undefined); });
    form.code.readOnly = Boolean(promo && Number(promo.used_count) > 0);
    syncType(); modal.hidden = false; setTimeout(() => (promo ? form.elements.title : form.elements.code).focus(), 0);
  };
  const closeModal = () => { modal.hidden = true; };
  $('#open-promo-modal').addEventListener('click', () => openModal());
  document.querySelectorAll('[data-close-promo]').forEach(b => b.addEventListener('click', closeModal));
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && !modal.hidden) closeModal(); });
  form.addEventListener('input', syncType);
  form.code.addEventListener('input', () => { form.code.value = form.code.value.toUpperCase().replace(/[^A-Z0-9_-]/g, ''); });
  $('#promo-generate').addEventListener('click', () => {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789', bytes = crypto.getRandomValues(new Uint8Array(8));
    form.code.value = [...bytes].map(b => chars[b % chars.length]).join(''); form.code.focus();
  });

  form.addEventListener('submit', async e => {
    e.preventDefault();
    if (!form.reportValidity()) return;
    const f = Object.fromEntries(new FormData(form));
    if (f.starts_at && f.expires_at && f.expires_at < f.starts_at) { toast('The end date must be after the start date.'); return; }
    if (TYPES[f.type].percent && Number(f.value) > 100) { toast('Percentages cannot be more than 100.'); return; }
    ['max_reward', 'min_amount', 'usage_limit', 'starts_at', 'expires_at'].forEach(k => { if (f[k] === '') f[k] = null; });
    const btn = $('#promo-submit'), label = btn.innerHTML; btn.disabled = true; btn.innerHTML = '<span class="spinner"></span> Saving…';
    try {
      const r = await request({ method: 'POST', body: JSON.stringify({ action: f.uuid ? 'update' : 'create', ...f }) });
      closeModal(); toast(r.message || 'Promo code saved.'); load();
    } catch (err) { toast(err.message); } finally { btn.disabled = false; btn.innerHTML = label; icons(); }
  });

  // ---- table actions ----
  $('#promo-rows').addEventListener('click', async e => {
    const btn = e.target.closest('button'); if (!btn) return;
    if (btn.dataset.copy) { try { await navigator.clipboard.writeText(btn.dataset.copy); toast(`Copied ${btn.dataset.copy}`); } catch { toast('Copy is not available in this browser.'); } return; }
    if (btn.dataset.edit) return openModal(promos.find(p => p.uuid === btn.dataset.edit));
    if (btn.dataset.toggle) {
      const p = promos.find(x => x.uuid === btn.dataset.toggle), next = p.status === 'paused' ? 'active' : 'paused';
      btn.disabled = true;
      try { const r = await request({ method: 'POST', body: JSON.stringify({ action: 'set_status', uuid: p.uuid, status: next }) }); toast(r.message || (next === 'paused' ? `${p.code} paused.` : `${p.code} resumed.`)); load(); }
      catch (err) { toast(err.message); btn.disabled = false; }
    }
  });
  ['#promo-search', '#promo-type-filter', '#promo-status-filter'].forEach(s => $(s).addEventListener('input', renderRows));
  $('#promo-retry').addEventListener('click', load);
  load();
})();
