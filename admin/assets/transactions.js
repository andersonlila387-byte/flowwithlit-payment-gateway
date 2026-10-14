(() => {
  const $ = (selector, root = document) => root.querySelector(selector);
  const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];
  const apiBase = window.dashboardConfig?.apiBase;
  if (!apiBase || !$('#transactions-table-body')) return;

  const state = { page: 1, totalPages: 1, timer: null, allTransactions: [] };
  const table = $('#transactions-table-body');
  const loading = $('#transactions-loading');
  const error = $('#transactions-error');
  const content = $('#transactions-content');
  const searchUser = $('#transaction-search-user');
  const clearSearch = $('#clear-transaction-search');
  const filterDate = $('#transaction-date');
  const filterStatus = $('#transaction-status');
  
  const clear = (node) => { while (node?.firstChild) node.removeChild(node.firstChild); };
  const humanStatus = (value) => String(value || '').replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase());
  const addText = (parent, tag, value, className) => {
    const node = document.createElement(tag); if (className) node.className = className; node.textContent = value; parent.append(node); return node;
  };

  const renderTransactions = () => {
    clear(table);
    
    // Summary
    $('#summary-total').textContent = state.allTransactions.length;
    $('#summary-success').textContent = state.allTransactions.filter(t => t.status === 'successful').length;
    $('#summary-pending').textContent = state.allTransactions.filter(t => t.status === 'pending').length;
    $('#summary-failed').textContent = state.allTransactions.filter(t => t.status === 'failed').length;

    let filtered = state.allTransactions;
    const query = searchUser.value.trim().toLowerCase();
    const dateQuery = filterDate.value;
    const statusQuery = filterStatus.value;

    if (query) filtered = filtered.filter(t => (t.full_name && t.full_name.toLowerCase().includes(query)) || (t.reference && t.reference.toLowerCase().includes(query)) || (t.email && t.email.toLowerCase().includes(query)));
    if (dateQuery) filtered = filtered.filter(t => t.created_at.startsWith(dateQuery));
    if (statusQuery) filtered = filtered.filter(t => t.status === statusQuery);

    $('#transactions-empty').hidden = filtered.length > 0;
    
    filtered.forEach((t) => {
      const row = document.createElement('tr');
      addText(row, 'td', t.reference);
      
      const userCell = document.createElement('td');
      userCell.className = 'font-semibold';
      userCell.innerHTML = `<div>${t.full_name || 'Unknown'}</div><div style="font-size: 10px; color: var(--muted); font-weight: normal;">${t.email || ''}</div>`;
      row.append(userCell);

      const amountFormatted = new Intl.NumberFormat('en-NG', { style: 'currency', currency: t.currency_code || 'NGN' }).format(t.amount);
      addText(row, 'td', amountFormatted);
      addText(row, 'td', t.type);
      
      const statusCell = document.createElement('td'); 
      addText(statusCell, 'span', humanStatus(t.status), `status-badge ${t.status}`); 
      row.append(statusCell);
      
      const dateFormatted = t.created_at.split('.')[0].replace('T', ' ');
      addText(row, 'td', dateFormatted);
      
      const actions = document.createElement('td');
      const view = document.createElement('button'); view.type = 'button'; view.className = 'user-view-button'; 
      view.setAttribute('aria-label', `View ${t.reference}`); view.innerHTML = '<i data-lucide="eye" class="icon" aria-hidden="true"></i>';
      actions.append(view);
      row.append(actions);
      table.append(row);
    });

    window.lucide?.createIcons({ attrs: { 'stroke-width': 1.8 } });
  };

  const loadTransactions = async () => {
    loading.hidden = false; error.hidden = true; content.hidden = true;
    try {
      const headers = { Accept: 'application/json' };
      const token = localStorage.getItem('flow_admin_token');
      if (token) headers.Authorization = `Bearer ${token}`;
      const res = await fetch(`${apiBase}/transactions.php`, { headers, credentials: 'same-origin' });
      if (!res.ok) throw new Error('API Error');
      const data = await res.json();
      if (!data.success) throw new Error(data.error || 'Failed');
      
      state.allTransactions = data.transactions || [];
      renderTransactions();
      content.hidden = false;
    } catch (err) {
      error.hidden = false;
    } finally {
      loading.hidden = true;
    }
  };

  searchUser.addEventListener('input', () => { clearSearch.hidden = !searchUser.value; clearTimeout(state.timer); state.timer = setTimeout(renderTransactions, 300); });
  clearSearch.addEventListener('click', () => { searchUser.value = ''; clearSearch.hidden = true; renderTransactions(); searchUser.focus(); });
  filterDate.addEventListener('change', renderTransactions);
  filterStatus.addEventListener('change', renderTransactions);
  $('[data-transactions-retry]').addEventListener('click', loadTransactions);
  
  loadTransactions();
})();
