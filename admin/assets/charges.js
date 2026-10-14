(() => {
  'use strict';
  const $ = (s, r = document) => r.querySelector(s);
  const api = `${window.dashboardConfig.apiBase}/charges.php`;
  const token = () => localStorage.getItem('flow_admin_token') || '';
  const icons = () => window.lucide?.createIcons({ attrs: { 'stroke-width': 1.8 } });
  const toast = m => window.dashboardShowToast?.(m);
  const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const money = n => new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN', minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(Number(n || 0));
  const when = v => v ? new Date(String(v).replace(' ', 'T') + 'Z').toLocaleString() : '—';
  const BEARER = { on_top: 'Customer, on top', deducted: 'Taken from amount' };
  const modal = $('#charge-modal'), form = $('#charge-form');
  let rules = [], ops = {};

  const request = async (options = {}) => {
    const res = await fetch(api, { ...options, headers: { Authorization: `Bearer ${token()}`, ...(options.body ? { 'Content-Type': 'application/json' } : {}) } });
    const body = await res.json().catch(() => ({}));
    if (!res.ok || !body.success) throw new Error(body.error || 'Something went wrong. Try again.');
    return body;
  };

  // Money maths is done in kobo (whole numbers) so 0.1 + 0.2 style rounding errors cannot creep in.
  const kobo = n => Math.round(Number(n) * 100);
  const computeFee = (c, amount) => {
    const a = kobo(amount); let fee = 0;
    if (c.fee_type === 'flat') fee = kobo(c.flat_amount || 0);
    else if (c.fee_type === 'percent') fee = Math.round(a * Number(c.percent || 0) / 100);
    else if (c.fee_type === 'percent_plus_flat') fee = Math.round(a * Number(c.percent || 0) / 100) + kobo(c.flat_amount || 0);
    else if (c.fee_type === 'tiered') { const band = (c.bands || []).find(b => b.up_to == null || a <= kobo(b.up_to)); fee = band ? kobo(band.fee) : 0; }
    if (c.min_fee != null && c.min_fee !== '') fee = Math.max(fee, kobo(c.min_fee));
    if (c.max_fee != null && c.max_fee !== '') fee = Math.min(fee, kobo(c.max_fee));
    return fee / 100;
  };
  const describe = r => {
    if (r.fee_type === 'flat') return `${money(r.flat_amount)} flat`;
    if (r.fee_type === 'percent') return `${Number(r.percent)}% of the payment`;
    if (r.fee_type === 'percent_plus_flat') return `${Number(r.percent)}% + ${money(r.flat_amount)}`;
    const fees = (r.bands || []).map(b => Number(b.fee)); return fees.length ? `${r.bands.length} bands, ${money(Math.min(...fees))} to ${money(Math.max(...fees))}` : 'No bands set';
  };

  const render = d => {
    rules = d.rules || []; ops = Array.isArray(d.operations) ? Object.fromEntries(d.operations.map(o => [o.key, o.label])) : (d.operations || {});
    const on = rules.filter(r => Number(r.enabled)).length, s = d.summary || {}, last = (d.changes || [])[0];
    $('#charge-active').textContent = on; $('#charge-active-note').textContent = `${rules.length - on} switched off`; $('#charge-types').textContent = rules.length;
    $('#charge-month').textContent = money(s.fees_this_month);
    $('#charge-last').textContent = last ? new Date(String(last.created_at).replace(' ', 'T') + 'Z').toLocaleDateString('en-NG', { day: 'numeric', month: 'short' }) : '—';
    $('#charge-last-by').textContent = last ? `${last.admin_name || 'Administrator'}, ${last.operation_label}` : 'No changes yet';
    $('#charge-rows').innerHTML = rules.map(r => `<tr>
      <td><strong>${esc(ops[r.operation] || r.operation)}</strong></td><td>${esc(describe(r))}</td>
      <td>${r.min_fee != null && r.min_fee !== '' ? `Min ${esc(money(r.min_fee))}` : 'No minimum'}<small>${r.max_fee != null && r.max_fee !== '' ? `Cap ${esc(money(r.max_fee))}` : 'No cap'}</small></td>
      <td>${esc(BEARER[r.bearer] || r.bearer)}</td><td><span class="charge-status ${Number(r.enabled) ? '' : 'off'}">${Number(r.enabled) ? 'Charging' : 'Free'}</span></td>
      <td>${esc(when(r.updated_at))}<small>${esc(r.updated_by || '')}</small></td>
      <td><button class="button" type="button" data-edit="${esc(r.uuid)}"><i data-lucide="pencil" class="icon" aria-hidden="true"></i>Edit</button></td></tr>`).join('');
    $('#charge-empty').hidden = rules.length > 0;
    const ch = d.changes || [];
    $('#charge-changes').innerHTML = ch.map(c => `<tr><td>${esc(when(c.created_at))}</td><td>${esc(c.admin_name || 'Administrator')}</td><td>${esc(c.operation_label)}</td><td>${esc(c.summary)}</td></tr>`).join('');
    $('#charge-changes-empty').hidden = ch.length > 0;
    $('#charge-loading').hidden = true; $('#charge-error').hidden = true; $('#charge-content').hidden = false; icons();
  };
  const load = () => {
    $('#charge-error').hidden = true; if (!rules.length) $('#charge-loading').hidden = false;
    return request().then(render).catch(e => { $('#charge-loading').hidden = true; $('#charge-content').hidden = true; $('#charge-error').hidden = false; $('#charge-error span').textContent = e.message; icons(); });
  };

  // ---- bands editor ----
  const bandRow = (b, last) => `<div class="charge-band"><label>${last ? 'Above the last band' : 'Up to (₦)'}<input data-band="up_to" type="number" min="0" step="any" value="${last ? '' : esc(b.up_to ?? '')}" ${last ? 'disabled placeholder="No limit"' : ''}></label><label>Fee (₦)<input data-band="fee" type="number" min="0" step="any" value="${esc(b.fee ?? '')}"></label><button class="icon-button" type="button" data-remove-band aria-label="Remove band" ${last ? 'disabled' : ''}><i data-lucide="trash-2" class="icon" aria-hidden="true"></i></button></div>`;
  const drawBands = bands => {
    const list = bands.length ? bands : [{ up_to: null, fee: '' }];
    $('#charge-bands').innerHTML = list.map((b, i) => bandRow(b, i === list.length - 1)).join(''); icons();
  };
  const readBands = () => [...document.querySelectorAll('.charge-band')].map((row, i, all) => ({ up_to: i === all.length - 1 ? null : row.querySelector('[data-band=up_to]').value, fee: row.querySelector('[data-band=fee]').value }));
  $('#charge-add-band').addEventListener('click', () => { const b = readBands(); b.splice(b.length - 1, 0, { up_to: '', fee: '' }); drawBands(b); preview(); });
  $('#charge-bands').addEventListener('click', e => { const r = e.target.closest('[data-remove-band]'); if (!r) return; const b = readBands(); b.splice([...document.querySelectorAll('.charge-band')].indexOf(r.closest('.charge-band')), 1); drawBands(b); preview(); });

  // ---- form ----
  const num = v => (v === '' || v == null ? null : Number(v));
  const readConfig = () => {
    const f = form.elements, t = f.fee_type.value;
    return { fee_type: t, flat_amount: num(f.flat_amount.value), percent: num(f.percent.value), min_fee: num(f.min_fee.value), max_fee: num(f.max_fee.value), bearer: f.bearer.value, enabled: f.enabled.checked ? 1 : 0,
      bands: t === 'tiered' ? readBands().map(b => ({ up_to: num(b.up_to), fee: num(b.fee) })) : [] };
  };
  const problem = c => {
    if ((c.fee_type === 'flat' || c.fee_type === 'percent_plus_flat') && c.flat_amount == null) return 'Enter the flat fee.';
    if ((c.fee_type === 'percent' || c.fee_type === 'percent_plus_flat') && (c.percent == null || c.percent > 100)) return 'Enter a percentage from 0 to 100.';
    if (c.min_fee != null && c.max_fee != null && c.min_fee > c.max_fee) return 'The minimum fee cannot be more than the maximum fee.';
    if (c.fee_type === 'tiered') {
      if (c.bands.some(b => b.fee == null)) return 'Enter a fee for every band.';
      const ups = c.bands.slice(0, -1).map(b => b.up_to);
      if (ups.some(u => u == null || u <= 0)) return 'Enter an "up to" amount for every band except the last.';
      if (ups.some((u, i) => i && u <= ups[i - 1])) return 'Band amounts must go up from one band to the next.';
    }
    return '';
  };
  const showFields = () => { const t = form.elements.fee_type.value; form.querySelectorAll('[data-show]').forEach(el => { el.hidden = !el.dataset.show.split(' ').includes(t); }); };
  function preview() {
    const c = readConfig(), out = $('#charge-preview-out'), amt = Number($('#charge-try').value);
    if (problem(c) || !(amt >= 0) || $('#charge-try').value === '') { out.textContent = 'Fill in the fee details to see an example.'; return; }
    const fee = computeFee(c, amt), on = !Number(c.enabled);
    out.textContent = on ? 'This charge is switched off, so the payment is free.' : c.bearer === 'on_top' ? `Fee ${money(fee)}. The customer pays ${money(amt + fee)}.` : `Fee ${money(fee)}. The recipient gets ${money(Math.max(amt - fee, 0))}.`;
  }
  form.addEventListener('input', () => { showFields(); preview(); });
  form.addEventListener('change', () => { showFields(); preview(); });

  const openModal = r => {
    form.reset(); const f = form.elements; f.uuid.value = r.uuid;
    $('#charge-modal-title').textContent = `Edit ${ops[r.operation] || r.operation} charge`;
    f.fee_type.value = r.fee_type; f.flat_amount.value = r.flat_amount ?? ''; f.percent.value = r.percent ?? ''; f.min_fee.value = r.min_fee ?? ''; f.max_fee.value = r.max_fee ?? '';
    f.bearer.value = r.bearer; f.enabled.checked = Boolean(Number(r.enabled));
    drawBands((r.bands || []).map(b => ({ ...b }))); showFields(); preview(); modal.hidden = false; setTimeout(() => f.fee_type.focus(), 0);
  };
  const closeModal = () => { modal.hidden = true; };
  document.querySelectorAll('[data-close-charge]').forEach(b => b.addEventListener('click', closeModal));
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && !modal.hidden) closeModal(); });
  $('#charge-rows').addEventListener('click', e => { const b = e.target.closest('[data-edit]'); if (b) openModal(rules.find(r => r.uuid === b.dataset.edit)); });

  form.addEventListener('submit', async e => {
    e.preventDefault(); const c = readConfig(), p = problem(c); if (p) { toast(p); return; }
    const old = rules.find(r => r.uuid === form.elements.uuid.value);
    if (old && Number(old.enabled) && !c.enabled && !window.confirm('Switch this charge off? Payments of this type will be free until you turn it back on.')) return;
    if ((c.fee_type === 'percent' || c.fee_type === 'percent_plus_flat') && c.percent > 5 && !window.confirm(`${c.percent}% is a high rate for a payment fee. Save it anyway?`)) return;
    const btn = $('#charge-submit'), label = btn.innerHTML; btn.disabled = true; btn.innerHTML = '<span class="spinner"></span> Saving…';
    try { const r = await request({ method: 'POST', body: JSON.stringify({ action: 'update', uuid: form.elements.uuid.value, ...c }) }); closeModal(); toast(r.message || 'Charge saved.'); load(); }
    catch (err) { toast(err.message); } finally { btn.disabled = false; btn.innerHTML = label; icons(); }
  });
  $('#charge-retry').addEventListener('click', load);
  load();
})();
