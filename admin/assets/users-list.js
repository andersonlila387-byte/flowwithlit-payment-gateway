(() => {
  const $ = (selector, root = document) => root.querySelector(selector);
  const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];
  const apiBase = window.dashboardConfig?.apiBase;
  if (!apiBase || !$('#users-table-body')) return;

  const state = { page: 1, totalPages: 1, selectedUser: null, timer: null };
  const table = $('#users-table-body');
  const loading = $('#users-loading');
  const error = $('#users-error');
  const content = $('#users-content');
  const search = $('#customer-search');
  const clearSearch = $('#clear-customer-search');
  const status = $('#customer-status');
  const drawer = $('#user-drawer');
  const backdrop = $('#user-drawer-backdrop');
  const rowMenu = $('#user-row-menu');

  const request = async (path, options = {}) => {
    const token = localStorage.getItem('flow_admin_token');
    const headers = { Accept: 'application/json', ...(options.headers || {}) };
    if (token) headers.Authorization = `Bearer ${token}`;
    const response = await fetch(`${apiBase}/users.php${path}`, { credentials: 'same-origin', ...options, headers });
    const data = await response.json().catch(() => ({}));
    if (!response.ok || !data.success) throw new Error(data.error || 'The request could not be completed.');
    return data;
  };
  const clear = (node) => { while (node?.firstChild) node.removeChild(node.firstChild); };
  const humanStatus = (value) => String(value || '').replace(/_/g, ' ').replace(/\b\w/g, (letter) => letter.toUpperCase());
  const date = (value) => value ? new Date(`${value.replace(' ', 'T')}Z`).toLocaleDateString() : '—';
  const addText = (parent, tag, value, className) => {
    const node = document.createElement(tag); if (className) node.className = className; node.textContent = value; parent.append(node); return node;
  };
  const formatAccountType = (value) => value === 'business' ? 'Business' : 'Individual';

  const renderUsers = (data) => {
    Object.entries(data.summary || {}).forEach(([key, value]) => $$(`[data-user-summary="${key}"]`).forEach((node) => { node.textContent = String(value); }));
    clear(table);
    const users = data.users || [];
    $('#users-empty').hidden = users.length > 0;
    users.forEach((user) => {
      const row = document.createElement('tr');
      const customer = document.createElement('td');
      const avatar = document.createElement('span'); avatar.className = 'customer-initials'; avatar.textContent = (user.full_name || '?').split(/\s+/).slice(0, 2).map((word) => word[0]).join('').toUpperCase();
      const details = document.createElement('span'); details.className = 'customer-identity';
      addText(details, 'strong', user.full_name || 'Unnamed customer');
      addText(details, 'small', user.email || 'No email address');
      customer.append(avatar, details); row.append(customer);
      addText(row, 'td', user.business_name || '—');
      const typeCell = document.createElement('td'); addText(typeCell, 'span', formatAccountType(user.account_type), `customer-type ${user.account_type}`); row.append(typeCell);
      const verification = document.createElement('td'); addText(verification, 'span', user.email_verified === '1' || user.email_verified === 1 ? `Tier ${user.kyc_tier}` : 'Email unverified', user.email_verified === '1' || user.email_verified === 1 ? 'verification complete' : 'verification pending'); row.append(verification);
      const statusCell = document.createElement('td'); addText(statusCell, 'span', humanStatus(user.status), `user-status ${user.status}`); row.append(statusCell);
      addText(row, 'td', date(user.created_at));
      const actions = document.createElement('td'); actions.className = 'customer-actions';
      const view = document.createElement('button'); view.type = 'button'; view.className = 'user-view-button'; view.dataset.userUuid = user.uuid; view.setAttribute('aria-label', `View ${user.full_name}`); view.innerHTML = '<i data-lucide="eye" class="icon" aria-hidden="true"></i>';
      const more = document.createElement('button'); more.type = 'button'; more.className = 'user-more-button'; more.dataset.userUuid = user.uuid; more.dataset.userStatus = user.status; more.dataset.userName = user.full_name || 'Customer'; more.setAttribute('aria-label', `Actions for ${user.full_name}`); more.innerHTML = '<i data-lucide="ellipsis" class="icon" aria-hidden="true"></i>';
      actions.append(view, more); row.append(actions); table.append(row);
    });
    const pagination = data.pagination || {};
    state.totalPages = pagination.total_pages || 1;
    $('#users-pagination-summary').textContent = pagination.total ? `Showing ${pagination.from}–${pagination.to} of ${pagination.total} customers` : 'No customers found';
    $('#users-page-current').textContent = `Page ${pagination.page || 1} of ${state.totalPages}`;
    $('#users-previous').disabled = state.page <= 1;
    $('#users-next').disabled = state.page >= state.totalPages;
    window.lucide?.createIcons({ attrs: { 'stroke-width': 1.8 } });
  };
  const loadUsers = async () => {
    loading.hidden = false; error.hidden = true; content.hidden = true; rowMenu.hidden = true;
    try {
      const params = new URLSearchParams({ page: String(state.page), limit: '25', q: search.value.trim(), status: status.value });
      const data = await request(`?${params}`);
      renderUsers(data); content.hidden = false;
    } catch (requestError) {
      error.querySelector('span').textContent = requestError.message || 'Customers could not be loaded.';
      error.hidden = false;
    } finally { loading.hidden = true; }
  };
  const openDrawer = async (uuid) => {
    drawer.setAttribute('aria-hidden', 'false'); drawer.classList.add('open'); backdrop.hidden = false; backdrop.classList.add('open');
    $('#user-drawer-title').textContent = 'Loading customer…'; clear($('#user-drawer-content'));
    try {
      const data = await request(`?uuid=${encodeURIComponent(uuid)}`);
      const user = data.user;
      $('#user-drawer-title').textContent = user.full_name || 'Customer';
      const fields = [
        ['Email address', user.email || '—'], ['Phone number', user.phone || '—'], ['Flow tag', user.flow_tag || '—'], ['Account type', formatAccountType(user.account_type)],
        ['Account status', humanStatus(user.status)], ['Verification tier', `Tier ${user.kyc_tier}`], ['Default wallet currency', data.default_currency || 'Not set'],
        ['Email verification', (user.email_verified === 1 || user.email_verified === '1') ? 'Verified' : 'Not verified'], ['Two-factor authentication', data.security?.two_factor_enabled ? 'Enabled' : 'Not enabled'], ['Sign-in method', humanStatus(data.security?.auth_provider || user.auth_provider)],
        ['Business', user.business_name || 'Not connected'], ['Business verification', user.verification_status ? humanStatus(user.verification_status) : '—'],
        ['Joined', date(user.created_at)], ['Last login', user.last_login_at ? date(user.last_login_at) : 'No recorded login'],
      ];
      const list = document.createElement('dl'); list.className = 'user-detail-list';
      fields.forEach(([label, value]) => { const item = document.createElement('div'); addText(item, 'dt', label); addText(item, 'dd', value); list.append(item); });
      $('#user-drawer-content').append(list);
      const appendSection = (title, rows) => { const section = document.createElement('section'); section.className = 'user-detail-section'; const heading = document.createElement('h3'); heading.textContent = title; section.append(heading); if (!rows.length) { addText(section, 'p', 'None set.', 'user-detail-empty'); } else { const entries = document.createElement('dl'); entries.className = 'user-detail-list'; rows.forEach(([label, value]) => { const item = document.createElement('div'); addText(item, 'dt', label); addText(item, 'dd', value); entries.append(item); }); section.append(entries); } $('#user-drawer-content').append(section); };
      appendSection('Flowwithlit wallets', (data.wallets || []).map(w => [`${w.currency_code} · ${humanStatus(w.wallet_type)}`, `${w.status} · Available ${w.available_balance} · Pending ${w.pending_balance}`]));
      appendSection('Generated receiving accounts', (data.receiving_accounts || []).map(a => [`${a.currency_code} · ${a.provider}`, `${a.bank_name || 'Bank'} · ${a.account_name || '—'} · ${a.account_number_display || `•••• ${a.account_number_last4 || '—'}`} · ${humanStatus(a.status)}`]));
      appendSection('Withdrawal bank accounts', (data.withdrawal_accounts || []).map(a => [`${a.bank_name} · ${a.currency_code}`, `${a.account_name} · •••• ${a.account_number_last4} · ${humanStatus(a.status)}`]));
      appendSection('Cards', (data.cards || []).map(c => [`${c.provider} · ${humanStatus(c.card_type)}`, `•••• ${c.last4} · ${c.currency_code} · ${humanStatus(c.status)}`]));
      appendSection('Crypto deposit addresses', (data.crypto_addresses || []).map(a => [`${a.currency_code} · ${a.network}`, `${a.address_display}${a.tag_memo ? ` · Memo ${a.tag_memo}` : ''} · ${humanStatus(a.status)}`]));
    } catch (requestError) {
      $('#user-drawer-title').textContent = 'Customer details unavailable';
      addText($('#user-drawer-content'), 'p', requestError.message || 'The customer could not be loaded.', 'dashboard-empty');
    }
  };
  const closeDrawer = () => { drawer.classList.remove('open'); drawer.setAttribute('aria-hidden', 'true'); backdrop.classList.remove('open'); backdrop.hidden = true; };
  const openRowMenu = (button) => {
    state.selectedUser = { uuid: button.dataset.userUuid, status: button.dataset.userStatus, name: button.dataset.userName };
    const suspend = $('[data-user-action="toggle-status"]', rowMenu);
    const reactivating = state.selectedUser.status === 'suspended';
    suspend.innerHTML = `<i data-lucide="${reactivating ? 'user-round-check' : 'user-round-x'}" class="icon" aria-hidden="true"></i><span>${reactivating ? 'Reactivate customer' : 'Suspend customer'}</span>`;
    const box = button.getBoundingClientRect(); rowMenu.hidden = false;
    rowMenu.style.top = `${Math.min(box.bottom + 6, window.innerHeight - rowMenu.offsetHeight - 10)}px`;
    rowMenu.style.left = `${Math.max(10, Math.min(box.right - rowMenu.offsetWidth, window.innerWidth - rowMenu.offsetWidth - 10))}px`;
    window.lucide?.createIcons({ attrs: { 'stroke-width': 1.8 } });
  };
  const changeStatus = async () => {
    if (!state.selectedUser) return;
    const nextStatus = state.selectedUser.status === 'suspended' ? 'active' : 'suspended';
    const action = nextStatus === 'suspended' ? 'suspend' : 'reactivate';
    if (!window.confirm(`Are you sure you want to ${action} ${state.selectedUser.name}?`)) return;
    const button = $('[data-user-action="toggle-status"]', rowMenu);
    const original = button.innerHTML;
    button.disabled = true;
    button.setAttribute('aria-busy', 'true');
    button.innerHTML = '<span class="spinner" aria-hidden="true"></span>Updating…';
    try {
      await request('', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ user_uuid: state.selectedUser.uuid, status: nextStatus }) });
      rowMenu.hidden = true; window.dashboardShowToast?.(`Customer ${nextStatus === 'suspended' ? 'suspended' : 'reactivated'}.`) || alert(`Customer ${nextStatus === 'suspended' ? 'suspended' : 'reactivated'}.`);
      await loadUsers();
    } catch (requestError) { window.dashboardShowToast?.(requestError.message) || alert(requestError.message); }
    finally { button.disabled = false; button.removeAttribute('aria-busy'); button.innerHTML = original; }
  };

  table.addEventListener('click', (event) => {
    const view = event.target.closest('.user-view-button'); if (view) return openDrawer(view.dataset.userUuid);
    const more = event.target.closest('.user-more-button'); if (more) openRowMenu(more);
  });
  $('[data-user-action="view"]', rowMenu).addEventListener('click', () => { rowMenu.hidden = true; if (state.selectedUser) openDrawer(state.selectedUser.uuid); });
  $('[data-user-action="toggle-status"]', rowMenu).addEventListener('click', changeStatus);
  document.addEventListener('click', (event) => { if (!event.target.closest('#user-row-menu,.user-more-button')) rowMenu.hidden = true; });
  $('#close-user-drawer').addEventListener('click', closeDrawer); backdrop.addEventListener('click', closeDrawer);
  document.addEventListener('keydown', (event) => { if (event.key === 'Escape') { rowMenu.hidden = true; closeDrawer(); } });
  search.addEventListener('input', () => { clearSearch.hidden = !search.value; clearTimeout(state.timer); state.timer = setTimeout(() => { state.page = 1; loadUsers(); }, 300); });
  clearSearch.addEventListener('click', () => { search.value = ''; clearSearch.hidden = true; state.page = 1; loadUsers(); search.focus(); });
  status.addEventListener('change', () => { state.page = 1; loadUsers(); });
  $('#users-previous').addEventListener('click', () => { if (state.page > 1) { state.page -= 1; loadUsers(); } });
  $('#users-next').addEventListener('click', () => { if (state.page < state.totalPages) { state.page += 1; loadUsers(); } });
  $('[data-users-retry]').addEventListener('click', loadUsers);
  loadUsers();
})();

