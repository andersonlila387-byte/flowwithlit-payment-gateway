(() => {
  const $ = (selector, root = document) => root.querySelector(selector);
  const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];
  const apiBase = window.dashboardConfig?.apiBase;
  if (!apiBase || !$('#ua-body')) return;

  const state = { page: 1, totalPages: 1, timer: null, openUuid: null };
  const body = $('#ua-body');
  const search = $('#ua-search');
  const clearSearch = $('#ua-clear-search');
  const filter = $('#ua-filter');
  const drawer = $('#user-drawer');
  const backdrop = $('#user-drawer-backdrop');
  const drawerContent = $('#user-drawer-content');

  const request = async (query = '', payload = null) => {
    const headers = { Accept: 'application/json' };
    const token = localStorage.getItem('flow_admin_token');
    if (token) headers.Authorization = `Bearer ${token}`;
    const options = { credentials: 'same-origin', headers };
    if (payload) { options.method = 'POST'; headers['Content-Type'] = 'application/json'; options.body = JSON.stringify(payload); }
    const response = await fetch(`${apiBase}/user-accounts.php${query}`, options);
    const data = await response.json().catch(() => ({}));
    if (!response.ok || !data.success) throw new Error(data.error || 'The request could not be completed.');
    return data;
  };
  const toast = (message) => window.dashboardShowToast?.(message) || alert(message);
  const icons = () => window.lucide?.createIcons({ attrs: { 'stroke-width': 1.8 } });
  const clear = (node) => { while (node?.firstChild) node.removeChild(node.firstChild); };
  const el = (tag, text, className) => { const node = document.createElement(tag); if (text !== undefined && text !== null) node.textContent = text; if (className) node.className = className; return node; };
  const human = (value) => String(value || '').replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase());
  const date = (value) => value ? new Date(`${String(value).replace(' ', 'T')}Z`).toLocaleString() : '—';
  const badge = (status) => el('span', human(status), `user-status ua-status-${status}`);
  const iconButton = (icon, label, className, data = {}) => {
    const button = el('button', null, className); button.type = 'button'; button.setAttribute('aria-label', label); button.title = label;
    Object.assign(button.dataset, data); button.innerHTML = `<i data-lucide="${icon}" class="icon" aria-hidden="true"></i>`; return button;
  };
  const textButton = (icon, label, className, data = {}) => {
    const button = el('button', null, `button ${className}`); button.type = 'button'; Object.assign(button.dataset, data);
    button.innerHTML = `<i data-lucide="${icon}" class="icon" aria-hidden="true"></i>`; button.append(el('span', label)); return button;
  };
  const copyButton = (value) => iconButton('copy', 'Copy account number', 'ua-copy', { copy: value });
  const reasonText = { same_account_number: 'Same account number on two records', multiple_active_accounts: 'Customer has more than one active account', same_crypto_address: 'Same crypto address on two wallets' };

  // ---- List ----
  const render = (data) => {
    Object.entries(data.summary || {}).forEach(([key, value]) => $$(`[data-ua-summary="${key}"]`).forEach((n) => { n.textContent = String(value); }));
    const provider = $('#ua-provider');
    provider.hidden = false; provider.textContent = `New accounts via ${data.active_provider === 'paystack' ? 'Paystack' : 'OnePipe'}`;

    // Duplicate monitor
    const dups = data.duplicates || [];
    $('#ua-dup-panel').hidden = dups.length === 0;
    const dupBody = $('#ua-dup-body'); clear(dupBody);
    dups.forEach((d) => {
      const row = el('tr');
      row.append(el('td', reasonText[d.reason] || human(d.reason), 'ua-dup-reason'));
      const who = el('td'); const id = el('span', null, 'customer-identity'); id.append(el('strong', d.full_name || 'Customer'), el('small', d.email || '')); who.append(id); row.append(who);
      row.append(el('td', d.bank_name || '—'));
      const num = el('td', null, 'ua-number'); num.append(el('span', d.account_number || '—')); row.append(num);
      const st = el('td'); st.append(badge(d.status)); row.append(st);
      const act = el('td', null, 'customer-actions'); act.append(iconButton('eye', `View ${d.full_name}`, 'user-view-button', { userUuid: d.user_uuid })); row.append(act);
      dupBody.append(row);
    });

    // Customers
    clear(body);
    const users = data.users || [];
    $('#ua-empty').hidden = users.length > 0;
    users.forEach((u) => {
      const row = el('tr');
      if (u.has_duplicates) row.className = 'ua-row-duplicate';
      const who = el('td'); const initials = el('span', (u.full_name || '?').split(/\s+/).slice(0, 2).map((w) => w[0]).join('').toUpperCase(), 'customer-initials');
      const id = el('span', null, 'customer-identity'); id.append(el('strong', u.full_name || 'Unnamed customer'), el('small', u.email || 'No email'));
      if (u.flow_tag) id.append(el("small", u.flow_tag, "ua-tag"));
      who.append(initials, id); row.append(who);
      row.append(el('td', u.country_name ? `${u.country_name}${u.country_code ? ` (${u.country_code})` : ''}` : (u.country_code || '—')));

      const account = (u.receiving_accounts || []).find((a) => a.status === 'active') || (u.receiving_accounts || [])[0];
      row.append(el('td', account?.bank_name || '—'));
      const num = el('td', null, 'ua-number');
      if (account?.account_number) { num.append(el('span', account.account_number)); if (/^\d+$/.test(account.account_number)) num.append(copyButton(account.account_number)); if (account.account_name) num.append(el('small', account.account_name)); }
      else num.append(el('span', 'Not generated', 'ua-muted'));
      if (u.has_duplicates) num.append(el('small', 'Duplicate alert', 'ua-dup-flag'));
      row.append(num);

      const crypto = u.crypto_addresses || [];
      row.append(el('td', crypto.length ? crypto.map((c) => `${c.currency_code}/${c.network}`).join(', ') : '—'));
      const banks = u.withdrawal_accounts || [];
      row.append(el('td', banks.length ? banks.map((b) => `${b.bank_name} ••••${b.account_number_last4}`).join(', ') : '—'));

      const st = el('td'); st.append(account ? badge(account.status) : badge(u.approved ? 'missing' : u.status)); row.append(st);

      const act = el('td', null, 'customer-actions');
      if (u.can_regenerate) act.append(iconButton('refresh-cw', `Generate account for ${u.full_name}`, 'user-view-button ua-regenerate', { userUuid: u.uuid, userName: u.full_name || 'this customer' }));
      act.append(iconButton('eye', `View ${u.full_name}`, 'user-view-button', { userUuid: u.uuid }));
      row.append(act);
      body.append(row);
    });
    const p = data.pagination || {};
    state.totalPages = p.total_pages || 1;
    $('#ua-pagination-summary').textContent = p.total ? `Showing ${p.from}–${p.to} of ${p.total} customers` : 'No customers found';
    $('#ua-page-current').textContent = `Page ${p.page || 1} of ${state.totalPages}`;
    $('#ua-previous').disabled = state.page <= 1;
    $('#ua-next').disabled = state.page >= state.totalPages;
    icons();
  };
  const load = async () => {
    $('#ua-loading').hidden = false; $('#ua-error').hidden = true;
    try {
      const params = new URLSearchParams({ page: String(state.page), limit: '25', q: search.value.trim(), filter: filter.value });
      render(await request(`?${params}`));
      $('#ua-content').hidden = false;
    } catch (error) {
      $('#ua-error span').textContent = error.message || 'Accounts could not be loaded.';
      $('#ua-error').hidden = false; $('#ua-content').hidden = true;
    } finally { $('#ua-loading').hidden = true; }
  };

  // ---- Drawer ----
  const section = (title) => { const s = el('section', null, 'user-detail-section'); s.append(el('h3', title)); drawerContent.append(s); return s; };
  const openDrawer = async (uuid) => {
    state.openUuid = uuid;
    drawer.classList.add('open'); drawer.setAttribute('aria-hidden', 'false'); backdrop.hidden = false; backdrop.classList.add('open');
    $('#user-drawer-title').textContent = 'Loading customer…'; clear(drawerContent);
    try {
      const data = await request(`?uuid=${encodeURIComponent(uuid)}`);
      const u = data.user;
      $('#user-drawer-title').textContent = u.full_name || 'Customer';
      const list = el('dl', null, 'user-detail-list');
      [['Email', u.email || '—'], ['Phone', u.phone || '—'], ['Flow tag', u.flow_tag || "—"], ['Country', u.country_name || u.country_code || '—'],
       ['Customer status', human(u.status)], ['Email verified', Number(u.email_verified) ? 'Yes' : 'No'], ['Approved for account', u.approved ? 'Yes' : 'No — needs active status, verified email and phone'],
       ['Joined', date(u.created_at)]].forEach(([k, v]) => { const item = el('div'); item.append(el('dt', k), el('dd', v)); list.append(item); });
      drawerContent.append(list);
      if (u.has_duplicates) drawerContent.append(el('p', 'This customer appears in the duplicate account monitor. Check the accounts below.', 'ua-alert'));

      const bank = section('Bank account numbers (receiving)');
      if (data.can_regenerate) {
        const regen = textButton('refresh-cw', `Generate account number (${data.active_provider === 'paystack' ? 'Paystack' : 'OnePipe'})`, 'primary-button ua-regenerate', { userUuid: u.uuid, userName: u.full_name || 'this customer' });
        bank.append(regen);
      }
      const accounts = data.receiving_accounts || [];
      if (!accounts.length) bank.append(el('p', 'No account number has been generated.', 'user-detail-empty'));
      accounts.forEach((a) => {
        const card = el('div', null, `ua-account-card${a.duplicate ? ' is-duplicate' : ''}`);
        const top = el('div', null, 'ua-account-top'); top.append(el('strong', a.bank_name || human(a.provider)), badge(a.status)); card.append(top);
        const num = el('div', null, 'ua-number'); num.append(el('span', a.account_number || 'No number')); if (/^\d+$/.test(a.account_number || '')) num.append(copyButton(a.account_number)); card.append(num);
        card.append(el('small', `${a.account_name || '—'} · ${human(a.provider)} · ${a.currency_code} · ${a.deposits} deposit(s) · updated ${date(a.updated_at)}`));
        if (a.duplicate) card.append(el('small', 'This number is also on another record.', 'ua-dup-flag'));
        if (!a.deposits) card.append(textButton('trash-2', 'Delete account', 'ghost-button ua-danger ua-delete', { accountUuid: a.uuid, label: `${a.bank_name || a.provider} ${a.account_number || ''}`.trim() }));
        bank.append(card);
      });
      const job = data.provisioning_job;
      if (job) {
        const j = el('p', `Background job: ${human(job.status)} · ${job.attempts} attempt(s)${job.last_error ? ` · Last error: ${job.last_error}` : ''}`, 'ua-job');
        bank.append(j);
      }

      const crypto = section('Crypto deposit addresses');
      const addresses = data.crypto_addresses || [];
      if (!addresses.length) crypto.append(el('p', 'No crypto address assigned.', 'user-detail-empty'));
      addresses.forEach((c) => {
        const card = el('div', null, 'ua-account-card');
        const top = el('div', null, 'ua-account-top'); top.append(el('strong', `${c.currency_code} · ${c.network}`), badge(c.status)); card.append(top);
        card.append(el('div', `${c.address_display}${c.tag_memo ? ` · Memo ${c.tag_memo}` : ''}`, 'ua-number'));
        if (c.status === 'active') card.append(textButton('archive', 'Retire address', 'ghost-button ua-danger ua-retire', { addressId: String(c.id), label: `${c.currency_code} ${c.network}` }));
        crypto.append(card);
      });

      const banks = section('Withdrawal bank accounts (customer’s own)');
      const own = data.withdrawal_accounts || [];
      if (!own.length) banks.append(el('p', 'None added.', 'user-detail-empty'));
      const dl = el('dl', null, 'user-detail-list');
      own.forEach((b) => { const item = el('div'); item.append(el('dt', `${b.bank_name} · ${b.currency_code}`), el('dd', `${b.account_name} · ••••${b.account_number_last4} · ${human(b.status)}`)); dl.append(item); });
      banks.append(dl);
      icons();
    } catch (error) {
      $('#user-drawer-title').textContent = 'Customer unavailable';
      drawerContent.append(el('p', error.message, 'dashboard-empty'));
    }
  };
  const closeDrawer = () => { state.openUuid = null; drawer.classList.remove('open'); drawer.setAttribute('aria-hidden', 'true'); backdrop.classList.remove('open'); backdrop.hidden = true; };
  const refresh = async () => { await load(); if (state.openUuid) await openDrawer(state.openUuid); };

  // ---- Actions ----
  const busy = async (button, work) => {
    const original = button.innerHTML; button.disabled = true; button.innerHTML = '<span class="spinner" aria-hidden="true"></span>';
    try { await work(); } catch (error) { toast(error.message); } finally { if (button.isConnected) { button.disabled = false; button.innerHTML = original; icons(); } }
  };
  const regenerate = (button) => {
    if (!confirm(`Generate a bank account number for ${button.dataset.userName}?`)) return;
    busy(button, async () => { const r = await request('', { action: 'regenerate', user_uuid: button.dataset.userUuid }); toast(`${r.message} ${r.account?.bank_name || ''} ${r.account?.account_number || ''}`.trim()); await refresh(); });
  };
  const remove = (button) => {
    if (!confirm(`Delete ${button.dataset.label}?\n\nThe customer will no longer see this number and deposits to it will not be credited automatically. This cannot be undone.`)) return;
    busy(button, async () => { const r = await request('', { action: 'delete_receiving', account_uuid: button.dataset.accountUuid }); toast(r.message); await refresh(); });
  };
  const retire = (button) => {
    if (!confirm(`Retire the ${button.dataset.label} address? The customer will no longer use it.`)) return;
    busy(button, async () => { const r = await request('', { action: 'retire_crypto', address_id: Number(button.dataset.addressId) }); toast(r.message); await refresh(); });
  };

  document.addEventListener('click', (event) => {
    const copy = event.target.closest('.ua-copy');
    if (copy) { navigator.clipboard?.writeText(copy.dataset.copy).then(() => toast('Account number copied.')); return; }
    const regen = event.target.closest('.ua-regenerate'); if (regen) return regenerate(regen);
    const del = event.target.closest('.ua-delete'); if (del) return remove(del);
    const ret = event.target.closest('.ua-retire'); if (ret) return retire(ret);
    const view = event.target.closest('.user-view-button[data-user-uuid]'); if (view) return openDrawer(view.dataset.userUuid);
    const card = event.target.closest('[data-ua-filter-card]'); if (card) { filter.value = card.dataset.uaFilterCard; state.page = 1; load(); }
  });
  $('#close-user-drawer').addEventListener('click', closeDrawer); backdrop.addEventListener('click', closeDrawer);
  document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeDrawer(); });
  search.addEventListener('input', () => { clearSearch.hidden = !search.value; clearTimeout(state.timer); state.timer = setTimeout(() => { state.page = 1; load(); }, 300); });
  clearSearch.addEventListener('click', () => { search.value = ''; clearSearch.hidden = true; state.page = 1; load(); search.focus(); });
  filter.addEventListener('change', () => { state.page = 1; load(); });
  $('#ua-previous').addEventListener('click', () => { if (state.page > 1) { state.page -= 1; load(); } });
  $('#ua-next').addEventListener('click', () => { if (state.page < state.totalPages) { state.page += 1; load(); } });
  $('[data-ua-retry]').addEventListener('click', load);
  load();
})();
