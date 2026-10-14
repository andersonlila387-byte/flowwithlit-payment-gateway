(() => {
  const releaseAdminAuthGate = () => document.documentElement.classList.remove('admin-auth-pending');
  const token = localStorage.getItem('flow_admin_token');
  if (!window.dashboardConfig?.localPreview && !token) {
    window.location.replace('login.php');
    return;
  }
  if (window.dashboardConfig?.localPreview) {
    releaseAdminAuthGate();
  } else {
    fetch(`${window.dashboardConfig?.apiBase || '../api/admin'}/profile.php`, {
      headers: { Accept: 'application/json', Authorization: `Bearer ${token}` },
      credentials: 'same-origin',
    }).then(async (response) => {
      const payload = await response.json().catch(() => null);
      if (!response.ok || !payload?.success || !payload.admin) throw new Error('Invalid session');
      releaseAdminAuthGate();
    }).catch(() => {
      localStorage.removeItem('flow_admin_token');
      localStorage.removeItem('flow_admin_role');
      window.location.replace('login.php?reason=session-expired');
    });
  }
  const $ = (selector, root = document) => root.querySelector(selector);
  const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];
  let adminSessionRedirecting = false;
  const redirectExpiredAdminSession = () => {
    if (window.dashboardConfig?.localPreview || adminSessionRedirecting) return;
    adminSessionRedirecting = true;
    localStorage.removeItem('flow_admin_token');
    localStorage.removeItem('flow_admin_role');
    window.location.replace('login.php?reason=session-expired');
  };
  const nativeFetch = window.fetch.bind(window);
  window.fetch = async (...args) => {
    const response = await nativeFetch(...args);
    if (response.status === 401) redirectExpiredAdminSession();
    return response;
  };
  window.lucide?.createIcons({ attrs: { 'stroke-width': 1.8 } });
  const toast = $('#toast');
  let toastTimer;
  const sidebarStateKey = 'flow_admin_sidebar_collapsed';
  try {
    if (localStorage.getItem(sidebarStateKey) === '1') document.body.classList.add('sidebar-collapsed');
  } catch (_) {}
  const fullscreenButton = $('#fullscreen');
  const syncFullscreenButton = () => {
    const active = Boolean(document.fullscreenElement);
    if (!fullscreenButton) return;
    fullscreenButton.setAttribute('aria-label', active ? 'Exit browser full screen' : 'Enter browser full screen');
    fullscreenButton.innerHTML = `<i data-lucide="${active ? 'minimize-2' : 'maximize'}" class="icon" aria-hidden="true"></i>`;
    window.lucide?.createIcons({ attrs: { 'stroke-width': 1.8 } });
  };
  document.addEventListener('fullscreenchange', syncFullscreenButton);
  document.addEventListener('fullscreenerror', () => {
    syncFullscreenButton();
    showToast('Browser full screen is not available in this browser.');
  });
  window.addEventListener('focus', syncFullscreenButton);
  syncFullscreenButton();
  const navigationTooltip = document.createElement('div');
  navigationTooltip.className = 'navigation-tooltip';
  navigationTooltip.hidden = true;
  navigationTooltip.setAttribute('role', 'tooltip');
  document.body.append(navigationTooltip);

  const hideNavigationTooltip = () => { navigationTooltip.hidden = true; };
  const showNavigationTooltip = (item) => {
    if (!document.body.classList.contains('sidebar-collapsed') || window.innerWidth <= 800) return;
    const label = item.dataset.tooltip;
    if (!label) return;
    const box = item.getBoundingClientRect();
    navigationTooltip.textContent = label;
    navigationTooltip.style.left = `${box.right + 10}px`;
    navigationTooltip.style.top = `${Math.max(8, Math.min(box.top + (box.height / 2), window.innerHeight - 8))}px`;
    navigationTooltip.hidden = false;
  };
  $$('.nav-item[data-tooltip]').forEach((item) => {
    item.addEventListener('pointerenter', () => showNavigationTooltip(item));
    item.addEventListener('pointerleave', hideNavigationTooltip);
    item.addEventListener('focus', () => showNavigationTooltip(item));
    item.addEventListener('blur', hideNavigationTooltip);
  });
  window.addEventListener('resize', hideNavigationTooltip);

  const showToast = (message) => {
    clearTimeout(toastTimer);
    toast.textContent = message;
    toast.hidden = false;
    toastTimer = setTimeout(() => { toast.hidden = true; }, 2800);
  };
  window.dashboardShowToast = showToast;

  const adminRoleLabel = (role) => ({
    super_admin: 'Super Administrator',
    admin: 'Administrator',
    support: 'Support',
    reviewer: 'Identity Reviewer',
    finance: 'Finance',
  }[role] || 'Administrator');
  const adminInitials = (name) => {
    const parts = String(name || 'Administrator').trim().split(/\s+/).filter(Boolean);
    return ((parts[0]?.[0] || 'A') + (parts[1]?.[0] || parts[0]?.[1] || 'D')).toUpperCase();
  };
  const adminAvatarColors = ['#0b5875', '#3e4f8a', '#7a4c86', '#276749', '#8a5a31', '#8b3f55'];
  const avatarColorFor = (name) => {
    let hash = 0;
    for (const character of String(name || 'Administrator')) hash = ((hash << 5) - hash) + character.charCodeAt(0);
    return adminAvatarColors[Math.abs(hash) % adminAvatarColors.length];
  };
  const safeAvatarUrl = (value) => {
    if (!value) return '';
    try {
      const url = new URL(value, window.location.origin);
      return ['http:', 'https:'].includes(url.protocol) ? url.href : '';
    } catch (_) { return ''; }
  };
  const renderAdminAvatar = (name, imageUrl = '') => {
    const initials = adminInitials(name);
    const color = avatarColorFor(name);
    const source = safeAvatarUrl(imageUrl);
    $$('[data-admin-avatar]').forEach((avatar) => {
      avatar.style.setProperty('--admin-avatar-color', color);
      avatar.replaceChildren();
      avatar.textContent = initials;
      avatar.setAttribute('aria-label', `${name || 'Administrator'} profile image`);
      if (!source) return;
      const image = document.createElement('img');
      image.className = 'admin-avatar-image';
      image.src = source;
      image.alt = '';
      image.addEventListener('error', () => image.remove(), { once: true });
      avatar.append(image);
    });
  };
  const hydrateAdminProfile = async () => {
    const token = localStorage.getItem('flow_admin_token');
    const apiBase = window.dashboardConfig?.apiBase || '../api/admin';
    if (!token || window.dashboardConfig?.localPreview) return;
    try {
      const response = await fetch(`${apiBase}/profile.php`, {
        headers: { Accept: 'application/json', Authorization: `Bearer ${token}` },
        credentials: 'same-origin',
      });
      const payload = await response.json();
      if (!response.ok || !payload.success || !payload.admin) return;
      const admin = payload.admin;
      const fullName = String(admin.full_name || 'Administrator');
      $$('[data-admin-name]').forEach((element) => { element.textContent = fullName; });
      $$('[data-admin-role]').forEach((element) => { element.textContent = adminRoleLabel(admin.role); });
      renderAdminAvatar(fullName, admin.avatar_url || '');
    } catch (_) {
      // The menu remains usable with its initials fallback if the profile request fails.
    }
  };
  renderAdminAvatar('Administrator');
  hydrateAdminProfile();
  // A session can expire while an administrator is reading a page. Check it
  // regularly so the console exits cleanly instead of leaving stale controls.
  if (!window.dashboardConfig?.localPreview) {
    window.setInterval(() => {
      const token = localStorage.getItem('flow_admin_token');
      if (!token) return redirectExpiredAdminSession();
      fetch(`${window.dashboardConfig?.apiBase || '../api/admin'}/profile.php`, {
        headers: { Accept: 'application/json', Authorization: `Bearer ${token}` }, credentials: 'same-origin',
      }).catch(() => {});
    }, 60000);
  }

  const setBusy = (button, message) => {
    if (button.disabled) return () => {};
    button.dataset.originalContent = button.innerHTML;
    button.disabled = true;
    button.setAttribute('aria-busy', 'true');
    button.innerHTML = `<span class="spinner" aria-hidden="true"></span>${message}`;
    return () => {
      button.disabled = false;
      button.removeAttribute('aria-busy');
      button.innerHTML = button.dataset.originalContent;
    };
  };

  const closePopovers = (except) => $$('.popover').forEach((popover) => {
    if (popover.id !== except) {
      popover.hidden = true;
      $(`[data-popover="${popover.id}"]`)?.setAttribute('aria-expanded', 'false');
    }
  });

  $$('[data-popover]').forEach((button) => {
    button.addEventListener('click', () => {
      const popover = $(`#${button.dataset.popover}`);
      if (!popover) return;
      const willOpen = popover.hidden;
      closePopovers(button.dataset.popover);
      popover.hidden = !willOpen;
      button.setAttribute('aria-expanded', String(willOpen));
      if (willOpen) {
        const box = button.getBoundingClientRect();
        const width = Math.min(240, window.innerWidth - 24);
        popover.style.width = `${width}px`;
        popover.style.left = `${Math.max(12, Math.min(box.right - width, window.innerWidth - width - 12))}px`;
        popover.style.top = `${Math.min(box.bottom + 8, window.innerHeight - 48)}px`;
      }
    });
  });
  document.addEventListener('click', (event) => {
    if (!event.target.closest('[data-popover], .popover')) closePopovers();
  });

  const menuSearch = $('#menu-search');
  menuSearch?.addEventListener('input', () => {
    const query = menuSearch.value.trim().toLowerCase();
    let visible = 0;
    $$('.nav-item').forEach((item) => {
      const matches = item.textContent.toLowerCase().includes(query);
      item.hidden = !matches;
      if (matches) visible += 1;
    });
    $('.no-menus').hidden = visible !== 0;
  });

  $$('.nav-item:not(.nav-parent)').forEach((item) => item.addEventListener('click', () => {
    $$('.nav-item').forEach((entry) => { entry.classList.remove('active'); entry.removeAttribute('aria-current'); });
    item.classList.add('active'); item.setAttribute('aria-current', 'page');
    showToast(`${item.dataset.section} selected in this UI preview.`);
    document.body.classList.remove('mobile-nav-open');
  }));

  const sidebarToggle = $('.sidebar-toggle');
  sidebarToggle?.addEventListener('click', () => {
    const collapsed = document.body.classList.toggle('sidebar-collapsed');
    hideNavigationTooltip();
    sidebarToggle.setAttribute('aria-expanded', String(!collapsed));
    try { localStorage.setItem(sidebarStateKey, collapsed ? '1' : '0'); } catch (_) {}
  });
  $$('[data-submenu-toggle]').forEach((button) => button.addEventListener('click', () => {
    const submenu = $(`#${button.dataset.submenuToggle}`);
    if (!submenu) return;
    const willOpen = submenu.hidden;
    submenu.hidden = !willOpen;
    button.setAttribute('aria-expanded', String(willOpen));
  }));
  $('.mobile-menu')?.addEventListener('click', () => document.body.classList.toggle('mobile-nav-open'));

  $('#theme-toggle')?.addEventListener('click', () => {
    document.documentElement.classList.toggle('dark');
    const isDark = document.documentElement.classList.contains('dark');
    try { localStorage.setItem('flow_admin_theme', isDark ? 'dark' : 'light'); } catch (_) {}
    showToast(isDark ? 'Dark mode enabled.' : 'Light mode enabled.');
  });
  fullscreenButton?.addEventListener('click', async () => {
    try {
      if (document.fullscreenElement) await document.exitFullscreen();
      else await document.documentElement.requestFullscreen();
      window.setTimeout(syncFullscreenButton, 120);
    } catch (_) {
      syncFullscreenButton();
      showToast('Browser full screen is not available in this browser.');
    }
  });

  $('#users-collapse')?.addEventListener('click', (event) => {
    const list = $('.top-users-list');
    const isOpen = !list.hidden;
    list.hidden = isOpen;
    event.currentTarget.setAttribute('aria-expanded', String(!isOpen));
  });

  $$('.copy-button').forEach((button) => button.addEventListener('click', async () => {
    try { await navigator.clipboard.writeText(button.dataset.copy); showToast(`Copied ${button.dataset.copy}`); }
    catch (_) { showToast(`Transaction reference: ${button.dataset.copy}`); }
  }));

  const filterTransactions = () => {
    const filter = $('#transaction-filter').value.toLowerCase();
    let visible = 0;
    $$('#transactions-table tbody tr').forEach((row) => {
      const matches = !filter || row.textContent.toLowerCase().includes(filter);
      row.hidden = !matches;
      if (matches) visible += 1;
    });
    const emptyState = $('#no-transactions');
    const entryCount = $('#entry-count');
    if (emptyState) emptyState.hidden = visible !== 0;
    if (entryCount) entryCount.textContent = visible ? `Displaying 1 to ${visible} of ${visible} entries` : 'No matching entries';
    closePopovers();
  };
  $('#apply-filter')?.addEventListener('click', filterTransactions);

  $$('[data-column-toggle]').forEach((checkbox) => checkbox.addEventListener('change', () => {
    const column = checkbox.dataset.columnToggle;
    $$(`[data-column="${column}"]`).forEach((cell) => { cell.hidden = !checkbox.checked; });
  }));

  $$('#transactions-table th [data-sort]').forEach((button) => button.addEventListener('click', () => {
    const column = Number(button.dataset.sort);
    const body = $('#transactions-table tbody');
    const rows = [...body.rows];
    const ascending = button.dataset.direction !== 'asc';
    rows.sort((a, b) => a.cells[column].textContent.trim().localeCompare(b.cells[column].textContent.trim(), undefined, { numeric: true }) * (ascending ? 1 : -1));
    rows.forEach((row) => body.appendChild(row));
    $$('#transactions-table th [data-sort]').forEach((entry) => delete entry.dataset.direction);
    button.dataset.direction = ascending ? 'asc' : 'desc';
  }));

  $$('[data-page]').forEach((button) => button.addEventListener('click', () => {
    $$('.pagination button').forEach((entry) => entry.classList.remove('selected'));
    if (!['previous', 'next'].includes(button.dataset.page)) { button.classList.add('selected'); button.setAttribute('aria-current', 'page'); }
    showToast(`Page ${button.dataset.page} selected in this UI preview.`);
  }));

  $$('.more-button').forEach((button) => button.addEventListener('click', () => showToast(`${button.dataset.metric}: options will be connected with dashboard data.`)));
  $$('[data-preview-action]').forEach((button) => button.addEventListener('click', () => showToast(`${button.dataset.previewAction} will be connected when the admin workflow is implemented.`)));
  $$('[data-message]').forEach((button) => button.addEventListener('click', () => { showToast(button.dataset.message); closePopovers(); }));

  $$('[data-admin-signout]').forEach((button) => button.addEventListener('click', () => {
    if (window.dashboardConfig?.localPreview) { showToast('Preview mode stays open.'); return; }
    localStorage.removeItem('flow_admin_token');
    localStorage.removeItem('flow_admin_role');
    window.location.replace('login.php');
  }));

  $$('[data-export]').forEach((button) => button.addEventListener('click', () => {
    const restore = setBusy(button, 'Preparing…');
    window.setTimeout(() => { restore(); showToast(`${button.dataset.export === 'report' ? 'Report' : 'Table export'} is ready in the connected version.`); }, 650);
  }));

  const globalSearch = $('#global-search');
  globalSearch?.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' && globalSearch.value.trim()) showToast(`Search for “${globalSearch.value.trim()}” is ready for backend connection.`);
  });

  const dashboardCurrency = $('#dashboard-currency');
  const dashboardPeriod = $('#dashboard-period');
  const dashboardContent = $('#dashboard-content');
  const dashboardLoading = $('#dashboard-loading');
  const dashboardError = $('#dashboard-error');
  const formatAmount = (value, currency) => {
    const numeric = Number(value || 0);
    if (!Number.isFinite(numeric)) return `0 ${currency}`;
    if (currency === 'NGN') return new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN', maximumFractionDigits: 2 }).format(numeric);
    return `${new Intl.NumberFormat('en-NG', { maximumFractionDigits: 8 }).format(numeric)} ${currency}`;
  };
  const setDashboardText = (key, value) => $$(`[data-dashboard="${key}"]`).forEach((node) => { node.textContent = value; });
  const clearNode = (node) => { while (node?.firstChild) node.removeChild(node.firstChild); };
  const ageLabel = (value) => {
    if (!value) return 'No items waiting';
    const age = Math.max(0, Date.now() - new Date(`${value.replace(' ', 'T')}Z`).getTime());
    const hours = Math.floor(age / 3600000);
    return hours < 1 ? 'Less than 1 hour waiting' : `${hours < 24 ? hours : Math.floor(hours / 24)} ${hours < 24 ? 'hour' : 'day'}${hours === 1 ? '' : 's'} waiting`;
  };
  const renderDashboard = (data) => {
    const health = data.payment_health || {};
    setDashboardText('successful_volume', formatAmount(health.successful_volume, data.currency));
    setDashboardText('successful_count', String(health.successful_count || 0));
    setDashboardText('processing_count', String(health.processing_count || 0));
    setDashboardText('failed_count', String(health.failed_count || 0));
    setDashboardText('failure_rate', `Failure rate: ${health.failure_rate || 0}%`);
    setDashboardText('fees_collected', formatAmount(health.fees_collected, data.currency));
    $('#dashboard-updated').textContent = data.generated_at ? `Updated ${new Date(data.generated_at).toLocaleString()}` : '';

    const currencies = data.active_currencies || [];
    dashboardCurrency.disabled = false;
    clearNode(dashboardCurrency);
    currencies.forEach((item) => {
      const option = document.createElement('option');
      option.value = item.code;
      option.textContent = item.code;
      option.selected = item.code === data.currency;
      dashboardCurrency.append(option);
    });

    const queue = $('#attention-queue');
    clearNode(queue);
    if (!(data.queues || []).length) {
      const empty = document.createElement('p');
      empty.className = 'dashboard-empty';
      empty.textContent = 'There are no items requiring your attention.';
      queue.append(empty);
    } else data.queues.forEach((item) => {
      const link = document.createElement('a');
      link.className = 'queue-item';
      link.href = item.href || '#';
      const details = document.createElement('span');
      const label = document.createElement('strong'); label.textContent = item.label;
      const note = document.createElement('small');
      note.textContent = item.total !== null && item.total !== undefined ? `${formatAmount(item.total, item.currency)} · ${ageLabel(item.oldest_at)}` : ageLabel(item.oldest_at);
      details.append(label, note);
      const count = document.createElement('b'); count.textContent = String(item.count || 0);
      const arrow = document.createElement('i'); arrow.setAttribute('data-lucide', 'arrow-up-right'); arrow.className = 'icon'; arrow.setAttribute('aria-hidden', 'true');
      link.append(details, count, arrow); queue.append(link);
    });

    const links = $('#payment-links-summary');
    clearNode(links);
    [['Active','active'],['Disabled','disabled'],['Expired','expired'],['Paid','paid'],['Pending','pending']].forEach(([label, key]) => {
      const row = document.createElement('div'); const term = document.createElement('dt'); const value = document.createElement('dd');
      term.textContent = label; value.textContent = String(data.payment_links?.[key] || 0); row.append(term, value); links.append(row);
    });

    const activity = $('#recent-activity'); const activityEmpty = $('#recent-activity-empty');
    clearNode(activity); const rows = data.recent_activity || [];
    activityEmpty.hidden = rows.length > 0;
    rows.forEach((item) => {
      const row = document.createElement('tr');
      [new Date(`${item.created_at.replace(' ', 'T')}Z`).toLocaleString(), item.reference, item.type, item.status, item.currency_code, formatAmount(item.amount, item.currency_code), item.provider || '—'].forEach((value, index) => {
        const cell = document.createElement('td'); cell.textContent = value;
        if (index === 3) {
          const status = document.createElement('span');
          status.className = `operation-status ${String(value).toLowerCase()}`;
          status.textContent = value;
          clearNode(cell); cell.append(status);
        }
        row.append(cell);
      }); activity.append(row);
    });
    window.lucide?.createIcons({ attrs: { 'stroke-width': 1.8 } });
  };
  const loadDashboard = async () => {
    if (!dashboardCurrency || !dashboardPeriod) return;
    dashboardLoading.hidden = false; dashboardError.hidden = true; dashboardContent.hidden = true;
    dashboardCurrency.disabled = true;
    try {
      const params = new URLSearchParams({ currency: dashboardCurrency.value || 'NGN', period: dashboardPeriod.value || 'month' });
      const headers = { Accept: 'application/json' };
      const token = localStorage.getItem('flow_admin_token'); if (token) headers.Authorization = `Bearer ${token}`;
      const response = await fetch(`${window.dashboardConfig.apiBase}/overview.php?${params}`, { headers, credentials: 'same-origin' });
      const data = await response.json().catch(() => ({}));
      if (!response.ok || !data.success) throw new Error(data.error || 'Unable to load operations data.');
      renderDashboard(data); dashboardContent.hidden = false;
    } catch (error) {
      dashboardError.querySelector('span').textContent = error.message || 'Operations data could not be loaded.';
      dashboardError.hidden = false;
    } finally { dashboardLoading.hidden = true; dashboardCurrency.disabled = false; }
  };
  $('#dashboard-refresh')?.addEventListener('click', async (event) => { const restore = setBusy(event.currentTarget, 'Refreshing…'); await loadDashboard(); restore(); });
  $('[data-dashboard-retry]')?.addEventListener('click', loadDashboard);
  dashboardCurrency?.addEventListener('change', loadDashboard);
  dashboardPeriod?.addEventListener('change', loadDashboard);
  loadDashboard();
})();
