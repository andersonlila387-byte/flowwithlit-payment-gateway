(() => {
  const $ = (selector, root = document) => root.querySelector(selector);
  const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];
  const apiBase = window.dashboardConfig?.apiBase;
  if (!apiBase || !$('#staff-table-body')) return;

  const state = { allStaff: [], timer: null, openMenu: null };
  const table = $('#staff-table-body');
  const loading = $('#staff-loading');
  const error = $('#staff-error');
  const content = $('#staff-content');
  const searchInput = $('#staff-search-input');
  const filterRole = $('#staff-filter-role');
  const filterStatus = $('#staff-filter-status');

  const clear = (node) => { while (node?.firstChild) node.removeChild(node.firstChild); };
  const humanRole = (val) => ({
    super_admin: 'Super Admin',
    finance: 'Finance',
    support: 'Support',
    reviewer: 'Reviewer'
  }[val] || val || 'Admin');

  const showToast = (msg) => {
    if (window.dashboardShowToast) window.dashboardShowToast(msg);
    else alert(msg);
  };

  const getHeaders = () => {
    const headers = { 'Content-Type': 'application/json', Accept: 'application/json' };
    const token = localStorage.getItem('flow_admin_token');
    if (token) headers.Authorization = `Bearer ${token}`;
    return headers;
  };

  const renderStaff = () => {
    document.querySelectorAll('[data-staff-action-menu]').forEach((menu) => menu.remove());
    state.openMenu = null;
    clear(table);

    // Summary calculations
    $('#summary-total').textContent = state.allStaff.length;
    $('#summary-super').textContent = state.allStaff.filter(s => s.role === 'super_admin').length;
    $('#summary-active').textContent = state.allStaff.filter(s => s.status === 'active').length;
    $('#summary-disabled').textContent = state.allStaff.filter(s => s.status === 'disabled').length;

    let filtered = state.allStaff;
    const query = searchInput.value.trim().toLowerCase();
    const roleQuery = filterRole.value;
    const statusQuery = filterStatus.value;

    if (query) {
      filtered = filtered.filter(s => 
        (s.full_name && s.full_name.toLowerCase().includes(query)) ||
        (s.email && s.email.toLowerCase().includes(query))
      );
    }
    if (roleQuery) filtered = filtered.filter(s => s.role === roleQuery);
    if (statusQuery) filtered = filtered.filter(s => s.status === statusQuery);

    $('#staff-empty').hidden = filtered.length > 0;

    filtered.forEach((staff) => {
      const row = document.createElement('tr');

      // Administrator Info
      const userCell = document.createElement('td');
      userCell.innerHTML = `<div class="font-semibold">${staff.full_name || 'Administrator'}</div><div style="font-size:10px; color:var(--muted);">${staff.email || ''}</div>`;
      row.append(userCell);

      // Role
      const roleCell = document.createElement('td');
      const roleBadge = document.createElement('span');
      roleBadge.className = `role-badge ${staff.role}`;
      roleBadge.textContent = humanRole(staff.role);
      roleCell.append(roleBadge);
      row.append(roleCell);

      // Status
      const statusCell = document.createElement('td');
      const statusBadge = document.createElement('span');
      statusBadge.className = `status-badge ${staff.status === 'active' ? 'success' : 'failed'}`;
      statusBadge.textContent = staff.status === 'active' ? 'Active' : 'Disabled';
      statusCell.append(statusBadge);
      row.append(statusCell);

      // 2FA Status
      const tfaCell = document.createElement('td');
      tfaCell.textContent = staff.two_factor_enabled ? 'Enabled' : 'Disabled';
      tfaCell.style.color = staff.two_factor_enabled ? '#10b981' : 'var(--muted)';
      row.append(tfaCell);

      // Last Login
      const lastLoginCell = document.createElement('td');
      lastLoginCell.textContent = staff.last_login_at ? staff.last_login_at.split('.')[0].replace('T', ' ') : 'Never';
      row.append(lastLoginCell);

      // Created Date
      const createdCell = document.createElement('td');
      createdCell.textContent = staff.created_at ? staff.created_at.split('T')[0] : '—';
      row.append(createdCell);

      // Actions
      const actionsCell = document.createElement('td');
      actionsCell.className = 'actions-cell';

      // Three-dots button
      const dotsBtn = document.createElement('button');
      dotsBtn.type = 'button';
      dotsBtn.className = 'dots-btn';
      dotsBtn.setAttribute('aria-label', `Actions for ${staff.full_name}`);
      dotsBtn.innerHTML = '<i data-lucide="ellipsis-vertical" class="icon"></i>';

      // Dropdown menu popover
      const menu = document.createElement('div');
      menu.className = 'dropdown-menu';
      menu.dataset.staffActionMenu = 'true';
      menu.hidden = true;

      const isDisable = staff.status === 'active';

      // 1. Update Role option
      const roleItem = document.createElement('button');
      roleItem.type = 'button'; roleItem.className = 'dropdown-item';
      roleItem.innerHTML = '<i data-lucide="shield" class="icon"></i> Change Role';
      roleItem.addEventListener('click', (e) => {
        e.stopPropagation();
        menu.hidden = true;
        openEditRoleModal(staff);
      });
      menu.append(roleItem);

      // 2. Reset Password option
      const passItem = document.createElement('button');
      passItem.type = 'button'; passItem.className = 'dropdown-item';
      passItem.innerHTML = '<i data-lucide="key" class="icon"></i> Reset Password';
      passItem.addEventListener('click', (e) => {
        e.stopPropagation();
        menu.hidden = true;
        openResetPasswordModal(staff);
      });
      menu.append(passItem);

      // 3. Enable / Disable option
      const toggleItem = document.createElement('button');
      toggleItem.type = 'button'; toggleItem.className = 'dropdown-item';
      toggleItem.innerHTML = `<i data-lucide="${isDisable ? 'user-x' : 'user-check'}" class="icon"></i> ${isDisable ? 'Disable Account' : 'Enable Account'}`;
      toggleItem.addEventListener('click', (e) => {
        e.stopPropagation();
        menu.hidden = true;
        toggleStaffStatus(staff);
      });
      menu.append(toggleItem);

      // 4. Delete option
      const delItem = document.createElement('button');
      delItem.type = 'button'; delItem.className = 'dropdown-item danger-item';
      delItem.innerHTML = '<i data-lucide="trash-2" class="icon"></i> Delete Account';
      delItem.addEventListener('click', (e) => {
        e.stopPropagation();
        menu.hidden = true;
        openDeleteModal(staff);
      });
      menu.append(delItem);

      // Toggle menu display
      dotsBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const willOpen = menu.hidden;
        $$('.dropdown-menu').forEach(m => { m.hidden = true; });
        menu.hidden = !willOpen;
        state.openMenu = willOpen ? menu : null;
        if (willOpen) {
          const box = dotsBtn.getBoundingClientRect();
          const width = menu.offsetWidth;
          const height = menu.offsetHeight;
          menu.style.left = `${Math.max(10, Math.min(box.right - width, window.innerWidth - width - 10))}px`;
          menu.style.top = `${Math.max(10, Math.min(box.bottom + 6, window.innerHeight - height - 10))}px`;
        }
      });

      actionsCell.append(dotsBtn);
      document.body.append(menu);
      row.append(actionsCell);
      table.append(row);
    });

    window.lucide?.createIcons({ attrs: { 'stroke-width': 1.8 } });
  };

  const loadStaff = async () => {
    loading.hidden = false; error.hidden = true; content.hidden = true;
    try {
      const res = await fetch(`${apiBase}/staff.php`, { headers: getHeaders(), credentials: 'same-origin' });
      if (!res.ok) throw new Error('Failed to load administrator accounts.');
      const data = await res.json();
      if (!data.success) throw new Error(data.error || 'Failed to load administrators.');

      state.allStaff = data.staff || [];
      renderStaff();
      content.hidden = false;
    } catch (err) {
      error.querySelector('span').textContent = err.message || 'Administrators could not be loaded.';
      error.hidden = false;
    } finally {
      loading.hidden = true;
    }
  };

  // Modal helpers
  const openModal = (id) => { $(`#${id}`).hidden = false; };
  const closeModal = (id) => { $(`#${id}`).hidden = true; };

  $$('.close-modal').forEach(btn => {
    btn.addEventListener('click', () => closeModal(btn.dataset.modal));
  });

  // Add Admin
  $('#open-add-admin-modal')?.addEventListener('click', () => openModal('modal-add-admin'));

  $('#form-add-admin')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = $('#submit-add-admin');
    btn.disabled = true;
    try {
      const body = {
        action: 'create',
        full_name: $('#add-full-name').value,
        email: $('#add-email').value,
        role: $('#add-role').value,
        password: $('#add-password').value
      };
      const res = await fetch(`${apiBase}/staff.php`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify(body),
        credentials: 'same-origin'
      });
      const data = await res.json();
      if (!data.success) throw new Error(data.error || 'Failed to create admin.');

      showToast(data.message || 'Administrator created successfully.');
      closeModal('modal-add-admin');
      $('#form-add-admin').reset();
      loadStaff();
    } catch (err) {
      showToast(`Error: ${err.message}`);
    } finally {
      btn.disabled = false;
    }
  });

  // Edit Role
  const openEditRoleModal = (staff) => {
    $('#edit-role-uuid').value = staff.uuid;
    $('#edit-role-admin-info').textContent = `Updating role for ${staff.full_name} (${staff.email})`;
    $('#edit-role-select').value = staff.role;
    openModal('modal-edit-role');
  };

  $('#form-edit-role')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = $('#submit-edit-role');
    btn.disabled = true;
    try {
      const body = {
        action: 'update_role',
        admin_uuid: $('#edit-role-uuid').value,
        role: $('#edit-role-select').value
      };
      const res = await fetch(`${apiBase}/staff.php`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify(body),
        credentials: 'same-origin'
      });
      const data = await res.json();
      if (!data.success) throw new Error(data.error || 'Failed to update role.');

      showToast(data.message || 'Administrator role updated.');
      closeModal('modal-edit-role');
      loadStaff();
    } catch (err) {
      showToast(`Error: ${err.message}`);
    } finally {
      btn.disabled = false;
    }
  });

  // Reset Password
  const openResetPasswordModal = (staff) => {
    $('#reset-password-uuid').value = staff.uuid;
    $('#reset-password-admin-info').textContent = `Set new password for ${staff.full_name} (${staff.email})`;
    $('#reset-new-password').value = '';
    openModal('modal-reset-password');
  };

  $('#form-reset-password')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = $('#submit-reset-password');
    btn.disabled = true;
    try {
      const body = {
        action: 'reset_password',
        admin_uuid: $('#reset-password-uuid').value,
        password: $('#reset-new-password').value
      };
      const res = await fetch(`${apiBase}/staff.php`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify(body),
        credentials: 'same-origin'
      });
      const data = await res.json();
      if (!data.success) throw new Error(data.error || 'Failed to reset password.');

      showToast(data.message || 'Password reset successfully.');
      closeModal('modal-reset-password');
      loadStaff();
    } catch (err) {
      showToast(`Error: ${err.message}`);
    } finally {
      btn.disabled = false;
    }
  });

  // Enable / Disable toggle
  const toggleStaffStatus = async (staff) => {
    const isDisable = staff.status === 'active';
    const action = isDisable ? 'disable' : 'enable';
    if (!confirm(`Are you sure you want to ${action} ${staff.full_name}'s access?`)) return;

    try {
      const body = { action, admin_uuid: staff.uuid };
      const res = await fetch(`${apiBase}/staff.php`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify(body),
        credentials: 'same-origin'
      });
      const data = await res.json();
      if (!data.success) throw new Error(data.error || `Failed to ${action} administrator.`);

      showToast(data.message || `Administrator ${action}d successfully.`);
      loadStaff();
    } catch (err) {
      showToast(`Error: ${err.message}`);
    }
  };

  // Delete Admin
  const openDeleteModal = (staff) => {
    $('#delete-admin-uuid').value = staff.uuid;
    $('#delete-admin-info').textContent = `Are you sure you want to delete ${staff.full_name} (${staff.email})?`;
    openModal('modal-delete-admin');
  };

  $('#form-delete-admin')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = $('#submit-delete-admin');
    btn.disabled = true;
    try {
      const body = {
        action: 'delete',
        admin_uuid: $('#delete-admin-uuid').value
      };
      const res = await fetch(`${apiBase}/staff.php`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify(body),
        credentials: 'same-origin'
      });
      const data = await res.json();
      if (!data.success) throw new Error(data.error || 'Failed to delete administrator.');

      showToast(data.message || 'Administrator account deleted.');
      closeModal('modal-delete-admin');
      loadStaff();
    } catch (err) {
      showToast(`Error: ${err.message}`);
    } finally {
      btn.disabled = false;
    }
  });

  // Event Listeners for Filters
  searchInput?.addEventListener('input', () => {
    clearTimeout(state.timer);
    state.timer = setTimeout(renderStaff, 250);
  });
  filterRole?.addEventListener('change', renderStaff);
  filterStatus?.addEventListener('change', renderStaff);
  $('[data-staff-retry]')?.addEventListener('click', loadStaff);
  document.addEventListener('click', (event) => {
    if (!event.target.closest('.dots-btn, [data-staff-action-menu]')) {
      $$('.dropdown-menu').forEach((menu) => { menu.hidden = true; }); state.openMenu = null;
    }
  });
  window.addEventListener('resize', () => { if (state.openMenu) state.openMenu.hidden = true; state.openMenu = null; });
  window.addEventListener('scroll', () => { if (state.openMenu) state.openMenu.hidden = true; state.openMenu = null; }, true);

  loadStaff();
})();
