(() => {
  'use strict';
  const $ = (s, r = document) => r.querySelector(s);
  const api = `${window.dashboardConfig.apiBase}/authorization.php`;
  const token = () => localStorage.getItem('flow_admin_token') || '';
  const icons = () => window.lucide?.createIcons({ attrs: { 'stroke-width': 1.8 } });
  const toast = m => window.dashboardShowToast?.(m);
  const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const money = n => new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN', maximumFractionDigits: 0 }).format(Number(n || 0));
  const when = v => v ? new Date(String(v).replace(' ', 'T') + 'Z').toLocaleString() : '—';
  const LEVELS = { access: [['none', 'No access'], ['view', 'View'], ['manage', 'Manage']], action: [['none', 'Not allowed'], ['manage', 'Allowed']] };
  const modal = $('#auth-modal'), form = $('#auth-form');
  let data = null, draft = {}, rules = [], pending = null;

  const request = async (options = {}) => {
    const res = await fetch(api, { ...options, headers: { Authorization: `Bearer ${token()}`, ...(options.body ? { 'Content-Type': 'application/json' } : {}) } });
    const body = await res.json().catch(() => ({}));
    if (!res.ok || !body.success) throw new Error(body.error || 'Something went wrong. Try again.');
    return body;
  };
  const post = payload => request({ method: 'POST', body: JSON.stringify(payload) });

  // ---- approval queue ----
  const renderQueue = () => {
    const list = data.approvals || [];
    $('#auth-pending-count').textContent = list.length;
    $('#auth-queue').innerHTML = list.map(a => `<tr>
      <td>${esc(when(a.requested_at))}</td><td>${esc(a.requester_name)}</td>
      <td><strong>${esc(a.action_label)}</strong><small>${esc(a.summary)}</small></td>
      <td>${a.amount == null ? '—' : esc(money(a.amount))}</td>
      <td><div class="auth-actions">
        <button class="button" type="button" data-decide="reject" data-id="${esc(a.uuid)}" ${a.can_decide ? '' : 'disabled title="You cannot decide your own request"'}>Reject</button>
        <button class="button approve" type="button" data-decide="approve" data-id="${esc(a.uuid)}" ${a.can_decide ? '' : 'disabled title="You cannot decide your own request"'}>Approve</button>
      </div></td></tr>`).join('');
    $('#auth-queue-empty').hidden = list.length > 0;
  };
  $('#auth-queue').addEventListener('click', e => {
    const b = e.target.closest('[data-decide]'); if (!b) return;
    const item = (data.approvals || []).find(a => a.uuid === b.dataset.id); if (!item) return;
    pending = { item, decision: b.dataset.decide };
    const approve = pending.decision === 'approve';
    $('#auth-modal-title').textContent = approve ? 'Approve request' : 'Reject request';
    $('#auth-modal-summary').textContent = `${item.action_label}${item.amount == null ? '' : ` · ${money(item.amount)}`} · requested by ${item.requester_name}. ${item.summary || ''}`;
    form.note.required = !approve; form.note.placeholder = approve ? 'Add a note for the audit log (optional)' : 'Say why you are rejecting this request';
    $('#auth-confirm').textContent = approve ? 'Approve' : 'Reject request'; form.reset(); modal.hidden = false; setTimeout(() => form.note.focus(), 0);
  });
  const closeModal = () => { modal.hidden = true; pending = null; };
  document.querySelectorAll('[data-close-auth]').forEach(b => b.addEventListener('click', closeModal));
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && !modal.hidden) closeModal(); });
  form.addEventListener('submit', async e => {
    e.preventDefault(); if (!pending || !form.reportValidity()) return;
    const btn = $('#auth-confirm'), label = btn.textContent; btn.disabled = true; btn.textContent = 'Saving…';
    try { const r = await post({ action: 'decide', uuid: pending.item.uuid, decision: pending.decision, note: form.note.value.trim() }); closeModal(); toast(r.message || 'Decision recorded.'); load(); }
    catch (err) { toast(err.message); } finally { btn.disabled = false; btn.textContent = label; }
  });

  // ---- permission matrix ----
  const changedCells = () => {
    const out = {}; let count = 0;
    data.roles.forEach(r => data.permissions.forEach(p => {
      if (draft[r.key][p.key] !== data.matrix[r.key][p.key]) { (out[r.key] ||= {})[p.key] = draft[r.key][p.key]; count++; }
    }));
    return { out, count };
  };
  const syncDirty = () => {
    const { count } = changedCells();
    $('#auth-dirty').textContent = count ? `${count} unsaved change${count === 1 ? '' : 's'}` : '';
    $('#auth-save').disabled = $('#auth-reset').disabled = !count;
  };
  const renderMatrix = () => {
    draft = JSON.parse(JSON.stringify(data.matrix));
    $('#auth-roles-count').textContent = data.roles.length;
    $('#auth-permissions-count').textContent = data.permissions.length;
    $('#auth-matrix-head').innerHTML = `<tr><th scope="col">Permission</th>${data.roles.map(r => `<th scope="col"><span class="auth-lock">${esc(r.label)}${r.locked ? '<i data-lucide="lock" class="icon" aria-label="Locked"></i>' : ''}</span></th>`).join('')}</tr>`;
    let group = '', html = '';
    data.permissions.forEach(p => {
      if (p.group !== group) { group = p.group; html += `<tr class="group"><th colspan="${data.roles.length + 1}" scope="colgroup">${esc(group)}</th></tr>`; }
      html += `<tr><th scope="row">${esc(p.label)}${p.description ? `<small>${esc(p.description)}</small>` : ''}</th>${data.roles.map(r => `<td><select data-role="${esc(r.key)}" data-perm="${esc(p.key)}" aria-label="${esc(`${r.label}: ${p.label}`)}" ${r.locked ? 'disabled' : ''}>${LEVELS[p.kind === 'action' ? 'action' : 'access'].map(([v, l]) => `<option value="${v}" ${draft[r.key][p.key] === v ? 'selected' : ''}>${l}</option>`).join('')}</select></td>`).join('')}</tr>`;
    });
    $('#auth-matrix-body').innerHTML = html; syncDirty(); icons();
  };
  $('#auth-matrix-body').addEventListener('change', e => {
    const s = e.target.closest('select'); if (!s) return;
    draft[s.dataset.role][s.dataset.perm] = s.value;
    s.classList.toggle('changed', s.value !== data.matrix[s.dataset.role][s.dataset.perm]); syncDirty();
  });
  $('#auth-reset').addEventListener('click', renderMatrix);
  $('#auth-save').addEventListener('click', async () => {
    const { out, count } = changedCells(); if (!count) return;
    if (!window.confirm(`Save ${count} permission change${count === 1 ? '' : 's'}? Affected admins get the new access on their next request.`)) return;
    const btn = $('#auth-save'); btn.disabled = true;
    try { const r = await post({ action: 'save_matrix', matrix: out }); toast(r.message || 'Permissions saved.'); load(); } catch (err) { toast(err.message); syncDirty(); }
  });

  // ---- approval rules ----
  const renderRules = () => {
    rules = JSON.parse(JSON.stringify(data.rules || []));
    $('#auth-rules-count').textContent = rules.filter(r => Number(r.enabled)).length;
    $('#auth-rules').innerHTML = rules.map(r => `<div class="auth-rule"><div><strong>${esc(r.label)}</strong><span class="d">${esc(r.description)}</span></div>
      <div class="auth-rule-controls">
        ${r.threshold === undefined ? '' : `<label>Above ₦<input type="number" min="0" step="any" value="${esc(r.threshold ?? 0)}" data-rule-threshold="${esc(r.key)}" aria-label="${esc(r.label)} threshold in naira" ${Number(r.enabled) ? '' : 'disabled'}></label>`}
        <label><input type="checkbox" data-rule-enabled="${esc(r.key)}" ${Number(r.enabled) ? 'checked' : ''}>Require approval</label>
      </div></div>`).join('');
    $('#auth-rules-save').disabled = true;
  };
  $('#auth-rules').addEventListener('input', e => {
    const key = e.target.dataset.ruleEnabled || e.target.dataset.ruleThreshold, r = rules.find(x => x.key === key); if (!r) return;
    if (e.target.dataset.ruleEnabled) { r.enabled = e.target.checked ? 1 : 0; const t = $(`[data-rule-threshold="${key}"]`); if (t) t.disabled = !e.target.checked; }
    else r.threshold = e.target.value === '' ? 0 : Number(e.target.value);
    $('#auth-rules-save').disabled = false;
  });
  $('#auth-rules-save').addEventListener('click', async () => {
    const btn = $('#auth-rules-save'); btn.disabled = true;
    try { const r = await post({ action: 'save_rules', rules: rules.map(x => ({ key: x.key, enabled: x.enabled, ...(x.threshold === undefined ? {} : { threshold: x.threshold }) })) }); toast(r.message || 'Rules saved.'); load(); }
    catch (err) { toast(err.message); btn.disabled = false; }
  });

  const load = () => {
    $('#auth-error').hidden = true; if (!data) $('#auth-loading').hidden = false;
    return request().then(body => { data = body; $('#auth-loading').hidden = true; $('#auth-content').hidden = false; renderQueue(); renderMatrix(); renderRules(); })
      .catch(e => { $('#auth-loading').hidden = true; $('#auth-content').hidden = true; $('#auth-error').hidden = false; $('#auth-error span').textContent = e.message; icons(); });
  };
  $('#auth-retry').addEventListener('click', load);
  load();
})();
