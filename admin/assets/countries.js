(() => {
  'use strict';
  const $ = (s, r = document) => r.querySelector(s);
  const api = `${window.dashboardConfig.apiBase}/countries.php`;
  const token = () => localStorage.getItem('flow_admin_token') || '';
  const modal = $('#country-modal'), form = $('#country-form');
  const icons = () => window.lucide?.createIcons({ attrs: { 'stroke-width': 1.8 } });
  const toast = m => (window.dashboardShowToast ? window.dashboardShowToast(m) : null);
  const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  // Flag emoji from the 2-letter code (regional indicator symbols).
  const flag = code => String(code).toUpperCase().replace(/./g, ch => String.fromCodePoint(127397 + ch.charCodeAt(0)));
  let countries = [];

  const renderRows = () => {
    const q = $('#country-search').value.trim().toLowerCase(), status = $('#country-status-filter').value;
    const rows = countries.filter(c => (!q || `${c.name} ${c.code} ${c.dial_code}`.toLowerCase().includes(q)) && (status === '' || String(Number(c.is_enabled)) === status));
    $('#country-rows').innerHTML = rows.map(c => {
      const on = Number(c.is_enabled) === 1;
      return `<tr>
        <td><strong><span aria-hidden="true">${flag(c.code)}</span> ${esc(c.name)}</strong><small>${esc(c.code)}</small></td>
        <td>${esc(c.dial_code)}</td>
        <td>${Number(c.users || 0).toLocaleString('en-NG')}</td>
        <td>${esc(c.sort_order)}</td>
        <td><span class="promo-status ${on ? 'active' : 'paused'}">${on ? 'Approved' : 'Not approved'}</span></td>
        <td><div class="promo-actions">
          <button class="button ${on ? '' : 'slate-button'}" type="button" data-toggle="${esc(c.code)}">${on ? 'Pause' : 'Approve'}</button>
          <button class="icon-button" type="button" data-edit="${esc(c.code)}" aria-label="Edit ${esc(c.name)}"><i data-lucide="pencil" class="icon" aria-hidden="true"></i></button>
        </div></td></tr>`;
    }).join('');
    const empty = $('#country-empty');
    empty.hidden = rows.length > 0;
    empty.textContent = countries.length ? 'No countries match these filters.' : 'No countries yet. Add one to get started.';
    icons();
  };

  const render = data => {
    countries = data.countries || [];
    const approved = countries.filter(c => Number(c.is_enabled) === 1).length;
    $('#country-approved').textContent = approved;
    $('#country-paused').textContent = countries.length - approved;
    $('#country-users').textContent = countries.reduce((n, c) => n + Number(c.users || 0), 0).toLocaleString('en-NG');
    $('#country-dials').textContent = new Set(countries.map(c => c.dial_code)).size;
    $('#country-loading').hidden = true; $('#country-error').hidden = true; $('#country-content').hidden = false;
    renderRows();
  };

  const request = async (options = {}) => {
    const res = await fetch(api, { ...options, headers: { Authorization: `Bearer ${token()}`, ...(options.body ? { 'Content-Type': 'application/json' } : {}) } });
    const body = await res.json().catch(() => ({}));
    if (!res.ok || !body.success) throw new Error(body.error || 'Something went wrong. Try again.');
    return body;
  };
  const load = () => {
    $('#country-error').hidden = true; if (!countries.length) $('#country-loading').hidden = false;
    return request().then(render).catch(e => { $('#country-loading').hidden = true; $('#country-content').hidden = true; $('#country-error').hidden = false; $('#country-error span').textContent = e.message; icons(); });
  };

  // ---- modal ----
  const openModal = country => {
    form.reset();
    $('#country-modal-title').textContent = country ? `Edit ${country.name}` : 'Add country';
    if (country) {
      form.code.value = country.code; form.name.value = country.name; form.dial_code.value = country.dial_code;
      form.sort_order.value = country.sort_order; form.is_enabled.value = String(Number(country.is_enabled));
    }
    form.code.readOnly = Boolean(country);
    modal.hidden = false; setTimeout(() => (country ? form.name : form.code).focus(), 0);
  };
  const closeModal = () => { modal.hidden = true; };
  $('#open-country-modal').addEventListener('click', () => openModal());
  document.querySelectorAll('[data-close-country]').forEach(b => b.addEventListener('click', closeModal));
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && !modal.hidden) closeModal(); });
  form.code.addEventListener('input', () => { form.code.value = form.code.value.toUpperCase().replace(/[^A-Z]/g, ''); });

  form.addEventListener('submit', async e => {
    e.preventDefault();
    if (!form.reportValidity()) return;
    const f = Object.fromEntries(new FormData(form));
    if (!form.code.readOnly && countries.some(c => c.code === f.code.toUpperCase())) { toast(`${f.code.toUpperCase()} is already in the list — use Edit instead.`); return; }
    const btn = $('#country-submit'), label = btn.innerHTML; btn.disabled = true; btn.innerHTML = '<span class="spinner"></span> Saving…';
    try {
      const r = await request({ method: 'POST', body: JSON.stringify({ action: 'save', ...f, is_enabled: f.is_enabled === '1' }) });
      closeModal(); toast(`${f.name} saved.`); render(r);
    } catch (err) { toast(err.message); } finally { btn.disabled = false; btn.innerHTML = label; icons(); }
  });

  // ---- table actions ----
  $('#country-rows').addEventListener('click', async e => {
    const btn = e.target.closest('button'); if (!btn) return;
    if (btn.dataset.edit) return openModal(countries.find(c => c.code === btn.dataset.edit));
    if (btn.dataset.toggle) {
      const c = countries.find(x => x.code === btn.dataset.toggle), next = Number(c.is_enabled) !== 1;
      btn.disabled = true;
      try { const r = await request({ method: 'POST', body: JSON.stringify({ action: 'toggle', code: c.code, is_enabled: next }) }); toast(next ? `${c.name} approved — it now shows at signup.` : `${c.name} paused — it no longer shows at signup.`); render(r); }
      catch (err) { toast(err.message); btn.disabled = false; }
    }
  });
  ['#country-search', '#country-status-filter'].forEach(s => $(s).addEventListener('input', renderRows));
  $('#country-retry').addEventListener('click', load);
  load();
})();
