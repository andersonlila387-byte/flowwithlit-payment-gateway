(() => {
  'use strict';
  const $ = (s, r = document) => r.querySelector(s);
  const api = `${window.dashboardConfig.apiBase}/api-connect.php`;
  const token = () => localStorage.getItem('flow_admin_token') || '';
  const icons = () => window.lucide?.createIcons({ attrs: { 'stroke-width': 1.8 } });
  const toast = m => window.dashboardShowToast?.(m);
  const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const CATS = { payments: ['Payments', 'credit-card'], banking: ['Banking & transfers', 'landmark'], crypto: ['Crypto', 'bitcoin'], email: ['Email', 'mail'], sms: ['SMS & OTP', 'message-square'], other: ['Other', 'plug-zap'] };
  const cat = k => CATS[k] || [k ? k[0].toUpperCase() + k.slice(1) : 'Other', 'plug-zap'];
  const when = v => v ? new Date(String(v).replace(' ', 'T') + 'Z').toLocaleString() : '';
  const modal = $('#api-modal'), form = $('#api-form');
  let providers = [], conns = {}, category = '', current = null;

  const request = async (options = {}) => {
    const res = await fetch(api, { ...options, headers: { Authorization: `Bearer ${token()}`, ...(options.body ? { 'Content-Type': 'application/json' } : {}) } });
    const body = await res.json().catch(() => ({}));
    if (!res.ok || !body.success) throw new Error(body.error || 'Something went wrong. Try again.');
    return body;
  };
  const post = p => request({ method: 'POST', body: JSON.stringify(p) });

  const statusOf = c => {
    if (!c || c.status === 'not_configured') return ['not_configured', 'Not set up'];
    if (!Number(c.enabled)) return ['disabled', 'Disabled'];
    if (c.last_test_ok === null || c.last_test_ok === undefined) return ['untested', 'Not tested'];
    return Number(c.last_test_ok) ? ['connected', 'Connected'] : ['error', 'Test failed'];
  };

  const renderSummary = () => {
    // SMTP is configured in Settings, where the same credentials are used by
    // EmailService. Do not count the legacy integration row as a second,
    // independent connection.
    const st = providers.filter(p => p.key !== 'smtp').map(p => statusOf(conns[p.key])[0]);
    $('#api-connected').textContent = st.filter(s => s === 'connected').length;
    $('#api-attention').textContent = st.filter(s => s === 'error').length;
    $('#api-unset').textContent = st.filter(s => s === 'not_configured').length;
    $('#api-test-mode').textContent = Number(data?.usage?.test_keys || 0).toLocaleString('en-NG'); $('#api-live-mode').textContent = Number(data?.usage?.live_keys || 0).toLocaleString('en-NG');
  };
  const renderCats = () => {
    const keys = [...new Set(providers.map(p => p.category))];
    $('#api-cats').innerHTML = [['', 'All'], ...keys.map(k => [k, cat(k)[0]])].map(([k, l]) => `<button type="button" data-cat="${esc(k)}" aria-pressed="${k === category}">${esc(l)}</button>`).join('');
  };
  const renderGrid = () => {
    const q = $('#api-search').value.trim().toLowerCase();
    const list = providers.filter(p => (!category || p.category === category) && (!q || `${p.name} ${p.description}`.toLowerCase().includes(q)));
    $('#api-empty').hidden = list.length > 0;
    $('#api-grid').innerHTML = list.map(p => {
      if (p.key === 'smtp') return `<article class="api-card">
        <div class="api-top"><span class="api-logo"><i data-lucide="mail-check" class="icon" aria-hidden="true"></i></span><div><h3>SMTP email</h3><span class="api-cat">Email</span></div></div>
        <p class="api-desc">Transactional email is managed from the central Settings page so the saved credentials are the ones EmailService uses.</p>
        <div class="api-badges"><span class="api-badge untested">Managed in Settings</span></div>
        <div class="api-test">Save the provider details there, then send a real test email to verify inbox delivery.</div>
        <div class="api-actions"><a class="button primary" href="settings.php#smtp-panel">Manage email</a></div>
      </article>`;
      const c = conns[p.key], [st, label] = statusOf(c), configured = st !== 'not_configured', [catLabel, icon] = cat(p.category);
      const note = c?.last_tested_at ? `Tested ${esc(when(c.last_tested_at))}${c.last_test_message ? `: ${esc(c.last_test_message)}` : ''}` : configured ? 'This connection has not been tested yet.' : '';
      return `<article class="api-card">
        <div class="api-top"><span class="api-logo"><i data-lucide="${esc(icon)}" class="icon" aria-hidden="true"></i></span><div><h3>${esc(p.name)}</h3><span class="api-cat">${esc(catLabel)}</span></div></div>
        <p class="api-desc">${esc(p.description)}</p>
        <div class="api-badges"><span class="api-badge ${st}">${label}</span>${configured && c.environment ? `<span class="api-badge ${c.environment === 'live' ? 'live' : ''}">${c.environment === 'live' ? 'Live mode' : 'Test mode'}</span>` : ''}</div>
        <div class="api-test">${note}</div>
        <div class="api-actions">
          <button class="button primary" type="button" data-configure="${esc(p.key)}">${configured ? 'Edit keys' : 'Connect'}</button>
          ${configured ? `<button class="button" type="button" data-test="${esc(p.key)}">Test</button><button class="button" type="button" data-toggle="${esc(p.key)}">${Number(c.enabled) ? 'Disable' : 'Enable'}</button>` : ''}
        </div></article>`;
    }).join('');
    icons();
  };
  let data = null;
  const render = d => {
    data = d; providers = d.providers || []; conns = Object.fromEntries((d.connections || []).map(c => [c.provider_key, c]));
    $('#api-loading').hidden = true; $('#api-error').hidden = true; $('#api-content').hidden = false;
    renderSummary(); renderCats(); renderGrid();
    const clients = d.clients || [];
    $('#api-clients').innerHTML = clients.map(c => `<tr><td>${esc(c.merchant_name || '—')}</td><td>${esc(c.key_prefix || '—')}••••</td><td><span class="api-badge ${c.environment === 'live' ? 'live' : ''}">${c.environment === 'live' ? 'Live' : 'Test'}</span></td><td>${c.last_used_at ? esc(when(c.last_used_at)) : 'Never'}</td><td>${esc(c.delivery_status || 'No delivery yet')}</td></tr>`).join(''); $('#api-clients-empty').hidden = clients.length > 0;
  };
  const load = () => {
    $('#api-error').hidden = true; if (!providers.length) $('#api-loading').hidden = false;
    return request().then(render).catch(e => { $('#api-loading').hidden = true; $('#api-content').hidden = true; $('#api-error').hidden = false; $('#api-error span').textContent = e.message; icons(); });
  };

  // ---- configure modal (form is generated from the provider's field list) ----
  const fieldHtml = (f, c) => {
    const id = `apif-${f.key}`, v = c?.values?.[f.key] ?? f.default ?? '', help = f.help ? `<span class="api-help">${esc(f.help)}</span>` : '';
    if (f.type === 'boolean') return `<label class="api-check"><input type="checkbox" name="${esc(f.key)}" ${Number(v) ? 'checked' : ''}>${esc(f.label)}</label>`;
    if (f.type === 'select') return `<label>${esc(f.label)}<select name="${esc(f.key)}" ${f.required ? 'required' : ''}>${(f.options || []).map(o => `<option value="${esc(o.value ?? o)}" ${String(o.value ?? o) === String(v) ? 'selected' : ''}>${esc(o.label ?? o)}</option>`).join('')}</select>${help}</label>`;
    if (f.type === 'secret') { const set = c?.secrets_set?.[f.key]; return `<label>${esc(f.label)}${set ? ' <span class="api-saved">Saved</span>' : ''}<input type="password" name="${esc(f.key)}" autocomplete="new-password" spellcheck="false" placeholder="${set ? 'Saved. Leave empty to keep it' : esc(f.placeholder || '')}" ${f.required && !set ? 'required' : ''}>${help}</label>`; }
    const type = f.type === 'url' ? 'url' : f.type === 'number' ? 'number' : f.type === 'email' ? 'email' : 'text';
    return `<label>${esc(f.label)}<input type="${type}" name="${esc(f.key)}" value="${esc(v)}" placeholder="${esc(f.placeholder || '')}" ${type === 'number' ? 'step="any"' : ''} ${f.required ? 'required' : ''} autocomplete="off" spellcheck="false">${help}</label>`;
  };
  const openModal = key => {
    const p = providers.find(x => x.key === key), c = conns[key]; if (!p) return;
    current = p; form.reset();
    $('#api-modal-title').textContent = `${c ? 'Edit' : 'Connect'} ${p.name}`;
    $('#api-modal-sub').textContent = p.description;
    const env = p.environments?.length ? `<label>Environment<select name="__environment">${p.environments.map(e => `<option value="${esc(e)}" ${(c?.environment || p.environments[0]) === e ? 'selected' : ''}>${e === 'live' ? 'Live: real money' : 'Test: sandbox'}</option>`).join('')}</select><span class="api-help">Keys differ between test and live, so re-enter secrets when you switch.</span></label>` : '';
    const hook = p.webhook_url ? `<label>Webhook URL<span class="api-webhook"><input type="text" readonly value="${esc(p.webhook_url)}" aria-label="Webhook URL"><button class="button" type="button" data-copy-hook>Copy</button></span><span class="api-help">Paste this into ${esc(p.name)}'s dashboard so it can notify you about payments.</span></label>` : '';
    $('#api-fields').innerHTML = env + p.fields.map(f => fieldHtml(f, c)).join('') + hook + `<label class="api-check"><input type="checkbox" name="__enabled" ${!c || Number(c.enabled) ? 'checked' : ''}>Enable this connection</label>`;
    modal.hidden = false; setTimeout(() => form.querySelector('input:not([readonly]),select')?.focus(), 0);
  };
  const closeModal = () => { modal.hidden = true; current = null; form.reset(); };   // reset clears any typed secrets
  document.querySelectorAll('[data-close-api]').forEach(b => b.addEventListener('click', closeModal));
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && !modal.hidden) closeModal(); });
  $('#api-fields').addEventListener('click', async e => { if (e.target.closest('[data-copy-hook]')) { try { await navigator.clipboard.writeText(current.webhook_url); toast('Webhook URL copied.'); } catch { toast('Copy is not available in this browser.'); } } });

  let then = 'none';
  form.addEventListener('click', e => { const b = e.target.closest('button[type=submit]'); if (b) then = b.dataset.then; });
  form.addEventListener('submit', async e => {
    e.preventDefault(); if (!current || !form.reportValidity()) return;
    const p = current, c = conns[p.key], env = form.elements.__environment?.value || null, values = {};
    for (const f of p.fields) {
      const el = form.elements[f.key]; if (!el) continue;
      if (f.type === 'boolean') values[f.key] = el.checked ? 1 : 0;
      else if (f.type === 'secret') { if (el.value !== '') values[f.key] = el.value; }
      else values[f.key] = f.type === 'number' ? (el.value === '' ? null : Number(el.value)) : el.value.trim();
      if (f.type === 'url' && values[f.key] && !/^https:\/\//i.test(values[f.key])) { toast(`${f.label} must start with https://`); return; }
    }
    const envChanged = c && env && c.environment && c.environment !== env;
    if (envChanged) { const miss = p.fields.find(f => f.type === 'secret' && f.required && !values[f.key]); if (miss) { toast(`Enter ${miss.label} again. Keys are different in ${env} mode.`); return; } }
    if (env === 'live' && c?.environment !== 'live' && !window.confirm(`Switch ${p.name} to live mode? Payments will use real money.`)) return;
    const btns = form.querySelectorAll('button[type=submit]'); btns.forEach(b => { b.disabled = true; });
    try {
      let r = await post({ action: 'save', provider: p.key, environment: env, enabled: form.elements.__enabled.checked ? 1 : 0, values });
      let msg = r.message || `${p.name} saved.`;
      if (then === 'test') { try { const t = await post({ action: 'test', provider: p.key }); msg = t.message || 'Connection test passed.'; } catch (err) { msg = `Saved, but the test failed: ${err.message}`; } }
      closeModal(); toast(msg); load();
    } catch (err) { toast(err.message); } finally { btns.forEach(b => { b.disabled = false; }); }
  });

  // ---- card actions ----
  $('#api-grid').addEventListener('click', async e => {
    const b = e.target.closest('button'); if (!b) return;
    if (b.dataset.configure) return openModal(b.dataset.configure);
    const key = b.dataset.test || b.dataset.toggle; if (!key) return;
    const p = providers.find(x => x.key === key), c = conns[key], old = b.textContent; b.disabled = true;
    try {
      if (b.dataset.test) { b.innerHTML = '<span class="spinner"></span> Testing'; const r = await post({ action: 'test', provider: key }); toast(r.message || `${p.name} connection works.`); }
      else { const r = await post({ action: 'toggle', provider: key, enabled: Number(c.enabled) ? 0 : 1 }); toast(r.message || (Number(c.enabled) ? `${p.name} disabled.` : `${p.name} enabled.`)); }
      load();
    } catch (err) { toast(err.message); load(); } finally { b.disabled = false; b.textContent = old; }
  });
  $('#api-cats').addEventListener('click', e => { const b = e.target.closest('[data-cat]'); if (!b) return; category = b.dataset.cat; renderCats(); renderGrid(); });
  $('#api-search').addEventListener('input', renderGrid);
  $('#api-retry').addEventListener('click', load);
  load();
})();
