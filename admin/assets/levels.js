(() => {
  'use strict';
  const $ = (s, r = document) => r.querySelector(s);
  const api = `${window.dashboardConfig.apiBase}/levels.php`;
  const token = () => localStorage.getItem('flow_admin_token') || '';
  const icons = () => window.lucide?.createIcons({ attrs: { 'stroke-width': 1.8 } });
  const toast = m => window.dashboardShowToast?.(m);
  const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const money = n => new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN', maximumFractionDigits: 0 }).format(Number(n || 0));
  const NUM = ['deposit_single', 'deposit_daily', 'withdraw_single', 'withdraw_daily', 'transfer_single', 'transfer_daily', 'max_balance', 'fee_percent'];
  const FLAGS = { api_access: 'API access', payment_links: 'Payment links', bulk_transfers: 'Bulk transfers' };
  const LIMIT_KEYS = NUM.filter(k => k !== 'fee_percent');
  const modal = $('#level-modal'), form = $('#level-form');
  let levels = [], type = 'personal';

  const request = async (options = {}) => {
    const res = await fetch(api, { ...options, headers: { Authorization: `Bearer ${token()}`, ...(options.body ? { 'Content-Type': 'application/json' } : {}) } });
    const body = await res.json().catch(() => ({}));
    if (!res.ok || !body.success) throw new Error(body.error || 'Something went wrong. Try again.');
    return body;
  };
  const ofType = () => levels.filter(l => l.account_type === type).sort((a, b) => a.rank - b.rank);

  const renderCards = () => {
    const list = ofType();
    $('#levels-empty').hidden = list.length > 0;
    $('#levels-grid').innerHTML = list.map(l => `<article class="level-card">
      <div class="level-top"><div><span class="level-rank">Level ${esc(l.rank)}</span><h3>${esc(l.name)}</h3></div><div class="level-users"><strong>${Number(l.users_count || 0).toLocaleString('en-NG')}</strong>users</div></div>
      <p class="level-req">${esc(l.requirement || 'No requirement set')}</p>
      <dl class="level-limits">
        ${['deposit', 'withdraw', 'transfer'].map(k => `<div><dt>${k[0].toUpperCase() + k.slice(1)}</dt><dd>${esc(money(l[`${k}_single`]))}<small>${esc(money(l[`${k}_daily`]))} per day</small></dd></div>`).join('')}
        <div><dt>Wallet balance</dt><dd>${l.max_balance == null ? 'No limit' : esc(money(l.max_balance))}</dd></div>
        <div><dt>Processing fee</dt><dd>${esc(l.fee_percent)}%</dd></div>
      </dl>
      <ul class="level-features">${Object.entries(FLAGS).map(([k, label]) => `<li class="${Number(l[k]) ? 'on' : ''}"><i data-lucide="${Number(l[k]) ? 'check' : 'minus'}" class="icon" aria-hidden="true"></i>${label}</li>`).join('')}</ul>
      <button class="button" type="button" data-edit="${esc(l.uuid)}"><i data-lucide="pencil" class="icon" aria-hidden="true"></i>Edit ${esc(l.name)}</button>
    </article>`).join('');
    icons();
  };

  const render = data => {
    const s = data.summary || {};
    levels = data.levels || [];
    $('#levels-count').textContent = levels.length;
    $('#levels-users').textContent = Number(s.users ?? levels.reduce((n, l) => n + Number(l.users_count || 0), 0)).toLocaleString('en-NG');
    $('#levels-pending').textContent = s.pending_reviews ?? 0; $('#levels-api-users').textContent = Number(s.api_users || 0).toLocaleString('en-NG');
    const changes = data.changes || [];
    $('#levels-changes').innerHTML = changes.map(c => `<tr><td>${esc(c.created_at ? new Date(String(c.created_at).replace(' ', 'T') + 'Z').toLocaleString() : '—')}</td><td>${esc(c.admin_name || 'Administrator')}</td><td>${esc(c.level_name)}</td><td>${esc(c.summary)}</td></tr>`).join('');
    $('#levels-changes-empty').hidden = changes.length > 0;
    $('#levels-loading').hidden = true; $('#levels-error').hidden = true; $('#levels-content').hidden = false;
    renderCards();
  };
  const load = () => {
    $('#levels-error').hidden = true; if (!levels.length) $('#levels-loading').hidden = false;
    return request().then(render).catch(e => { $('#levels-loading').hidden = true; $('#levels-content').hidden = true; $('#levels-error').hidden = false; $('#levels-error span').textContent = e.message; icons(); });
  };

  // ---- edit ----
  const openModal = level => {
    form.reset(); form.uuid.value = level.uuid;
    $('#level-modal-title').textContent = `Edit ${level.name}`;
    $('#level-modal-sub').textContent = `${level.account_type === 'business' ? 'Business' : 'Personal'} accounts · Level ${level.rank} · ${Number(level.users_count || 0).toLocaleString('en-NG')} users. Amounts are in naira; use 0 to block an action.`;
    form.elements.name.value = level.name;
    NUM.forEach(k => { form.elements[k].value = level[k] ?? ''; });
    Object.keys(FLAGS).forEach(k => { form.elements[k].checked = Boolean(Number(level[k])); });
    $('#level-notice').textContent = Number(level.users_count) ? `Changes apply straight away to ${Number(level.users_count).toLocaleString('en-NG')} users on this level.` : 'No users are on this level yet.';
    modal.hidden = false; setTimeout(() => form.elements.name.focus(), 0);
  };
  const closeModal = () => { modal.hidden = true; };
  document.querySelectorAll('[data-close-level]').forEach(b => b.addEventListener('click', closeModal));
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && !modal.hidden) closeModal(); });

  // Higher levels must never have lower limits, or a higher fee, than the level below.
  const orderProblem = (level, next) => {
    const list = ofType(), i = list.findIndex(l => l.uuid === level.uuid), below = list[i - 1], above = list[i + 1];
    const val = v => (v == null || v === '' ? Infinity : Number(v));
    for (const k of LIMIT_KEYS) {
      if (below && val(next[k]) < val(below[k])) return `${k.replace('_', ' ')} is lower than ${below.name}. A higher level cannot have lower limits.`;
      if (above && val(next[k]) > val(above[k])) return `${k.replace('_', ' ')} is higher than ${above.name}. Raise ${above.name} first.`;
    }
    if (below && next.fee_percent > Number(below.fee_percent)) return `The fee is higher than ${below.name}. A higher level should not pay more.`;
    if (above && next.fee_percent < Number(above.fee_percent)) return `The fee is lower than ${above.name}. Lower ${above.name} first.`;
    return '';
  };

  form.addEventListener('submit', async e => {
    e.preventDefault();
    if (!form.reportValidity()) return;
    const level = levels.find(l => l.uuid === form.uuid.value); if (!level) return;
    const next = { uuid: level.uuid, name: form.elements.name.value.trim() };
    NUM.forEach(k => { const v = form.elements[k].value; next[k] = v === '' ? null : Number(v); });
    Object.keys(FLAGS).forEach(k => { next[k] = form.elements[k].checked ? 1 : 0; });
    for (const op of ['deposit', 'withdraw', 'transfer']) if (next[`${op}_single`] > next[`${op}_daily`]) { toast(`${op[0].toUpperCase() + op.slice(1)} per transaction cannot be more than the daily limit.`); return; }
    const problem = orderProblem(level, next); if (problem) { toast(problem); return; }
    const lowered = LIMIT_KEYS.some(k => next[k] != null && next[k] < (level[k] == null ? Infinity : Number(level[k])));
    if (lowered && Number(level.users_count) && !window.confirm(`You are lowering limits for ${Number(level.users_count).toLocaleString('en-NG')} users on ${level.name}. Save these changes?`)) return;
    const btn = $('#level-submit'), label = btn.innerHTML; btn.disabled = true; btn.innerHTML = '<span class="spinner"></span> Saving…';
    try { const r = await request({ method: 'POST', body: JSON.stringify({ action: 'update', ...next }) }); closeModal(); toast(r.message || 'Level saved.'); load(); }
    catch (err) { toast(err.message); } finally { btn.disabled = false; btn.innerHTML = label; icons(); }
  });

  $('#levels-grid').addEventListener('click', e => { const b = e.target.closest('[data-edit]'); if (b) openModal(levels.find(l => l.uuid === b.dataset.edit)); });
  document.querySelectorAll('.levels-tabs button').forEach(b => b.addEventListener('click', () => {
    type = b.dataset.type; document.querySelectorAll('.levels-tabs button').forEach(x => x.setAttribute('aria-pressed', String(x === b))); renderCards();
  }));
  $('#levels-retry').addEventListener('click', load);
  load();
})();
