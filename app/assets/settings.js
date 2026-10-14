(() => {
  const apiBase = '../api/settings';
  const $ = (selector, root = document) => root.querySelector(selector);
  const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];
  const token = () => localStorage.getItem('flow_token') || '';
  const request = async (path, options = {}) => {
    const response = await fetch(`${apiBase}/${path}`, {
      ...options,
      headers: { Accept: 'application/json', Authorization: `Bearer ${token()}`, ...(options.headers || {}) },
    });
    const data = await response.json().catch(() => ({ error: 'Unexpected server response.' }));
    if (!response.ok || !data.success) throw new Error(data.error || 'Unable to complete this request.');
    return data;
  };
  const setBusy = (button, label) => {
    if (!button || button.disabled) return () => {};
    const original = button.textContent;
    button.disabled = true; button.setAttribute('aria-busy', 'true'); button.textContent = label;
    return () => { button.disabled = false; button.removeAttribute('aria-busy'); button.textContent = original; };
  };
  const profileState = { twoFactorEnabled: false };

  window.showToast = (message) => {
    const toast = $('#settingsToast');
    if (!toast) return;
    $('#settingsToastMessage').textContent = message;
    toast.classList.remove('hidden');
    requestAnimationFrame(() => toast.classList.remove('translate-y-3', 'opacity-0'));
    window.setTimeout(() => { toast.classList.add('translate-y-3', 'opacity-0'); window.setTimeout(() => toast.classList.add('hidden'), 300); }, 2800);
  };
  const setSwitch = (button, enabled) => {
    button.setAttribute('aria-checked', String(Boolean(enabled)));
    button.classList.toggle('bg-[#0b2545]', Boolean(enabled));
    button.classList.toggle('bg-gray-300', !enabled);
    button.classList.toggle('dark:bg-gray-600', !enabled);
    button.querySelector('span')?.classList.toggle('translate-x-5', Boolean(enabled));
  };
  const setPinStatus = (isSet) => {
    const status = $('#pinStatus'); const action = $('#pinActionButton');
    status.textContent = isSet ? 'Set' : 'Not set';
    status.className = isSet
      ? 'text-[10px] font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-900 rounded-full px-2 py-0.5'
      : 'text-[10px] font-bold text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-900 rounded-full px-2 py-0.5';
    action.textContent = isSet ? 'Change PIN' : 'Set PIN';
  };
  const setTwoFactor = (enabled) => {
    profileState.twoFactorEnabled = Boolean(enabled);
    const button = $('#twoFactorButton');
    button.textContent = enabled ? 'Manage 2FA' : 'Enable 2FA';
    button.classList.toggle('bg-emerald-600', enabled); button.classList.toggle('hover:bg-emerald-700', enabled);
    button.classList.toggle('bg-[#0b2545]', !enabled); button.classList.toggle('hover:bg-[#132847]', !enabled);
  };
  const formatDate = (value) => value ? new Intl.DateTimeFormat(undefined, { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(`${value.replace(' ', 'T')}Z`)) : 'Unknown activity';

  window.toggleSwitch = (button) => setSwitch(button, button.getAttribute('aria-checked') !== 'true');
  window.openPinPanel = () => $('#pinPanel').classList.toggle('hidden');
  window.openPasswordPanel = () => $('#passwordPanel').classList.toggle('hidden');
  window.saveWithdrawalPin = async () => {
    const pin = $('#newWithdrawalPin').value; const confirmation = $('#confirmWithdrawalPin').value;
    if (!/^\d{4}$/.test(pin)) return window.showToast('Use exactly four numbers for your PIN.');
    if (pin !== confirmation) return window.showToast('Your PIN entries do not match.');
    const button = $('#pinPanel button'); const done = setBusy(button, 'Saving…');
    try {
      await request('withdrawal-pin.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ pin }) });
      $('#newWithdrawalPin').value = ''; $('#confirmWithdrawalPin').value = ''; $('#pinPanel').classList.add('hidden'); setPinStatus(true); window.showToast('Withdrawal PIN saved.');
    } catch (error) { window.showToast(error.message); } finally { done(); }
  };
  window.savePassword = async () => {
    const currentPassword = $('#currentPassword').value; const newPassword = $('#newPassword').value; const confirmation = $('#confirmPassword').value;
    if (newPassword.length < 12) return window.showToast('Use a new password with at least 12 characters.');
    if (newPassword !== confirmation) return window.showToast('Your new passwords do not match.');
    const button = $('#passwordPanel button'); const done = setBusy(button, 'Updating…');
    try {
      const data = await request('password.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ current_password: currentPassword, new_password: newPassword }) });
      ['#currentPassword', '#newPassword', '#confirmPassword'].forEach((selector) => { $(selector).value = ''; }); $('#passwordPanel').classList.add('hidden'); window.showToast(data.message);
    } catch (error) { window.showToast(error.message); } finally { done(); }
  };
  window.toggleTwoFactor = async () => {
    const panel = $('#twoFactorPanel');
    if (profileState.twoFactorEnabled) {
      $('#twoFactorEnableFields').classList.add('hidden'); $('#twoFactorDisableFields').classList.remove('hidden'); panel.classList.toggle('hidden'); return;
    }
    const button = $('#twoFactorButton'); const done = setBusy(button, 'Sending code…');
    try {
      const data = await request('two-factor.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'request_enable' }) });
      $('#twoFactorDisableFields').classList.add('hidden'); $('#twoFactorEnableFields').classList.remove('hidden'); panel.classList.remove('hidden'); window.showToast(data.message);
    } catch (error) { window.showToast(error.message); } finally { done(); }
  };
  window.confirmTwoFactor = async () => {
    const code = $('#twoFactorCode').value.trim(); const button = $('#twoFactorEnableFields button'); const done = setBusy(button, 'Verifying…');
    try {
      const data = await request('two-factor.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'enable', code }) });
      $('#twoFactorCode').value = ''; $('#twoFactorPanel').classList.add('hidden'); setTwoFactor(true); window.showToast(data.message);
    } catch (error) { window.showToast(error.message); } finally { done(); }
  };
  window.disableTwoFactor = async () => {
    const currentPassword = $('#twoFactorPassword').value; const button = $('#twoFactorDisableFields button'); const done = setBusy(button, 'Disabling…');
    try {
      const data = await request('two-factor.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'disable', current_password: currentPassword }) });
      $('#twoFactorPassword').value = ''; $('#twoFactorPanel').classList.add('hidden'); setTwoFactor(false); window.showToast(data.message);
    } catch (error) { window.showToast(error.message); } finally { done(); }
  };
  const renderSessions = (sessions) => {
    const target = $('#sessionsList');
    target.replaceChildren();
    if (!sessions.length) { target.textContent = 'No active sessions found.'; return; }
    sessions.forEach((session) => {
      const row = document.createElement('div'); row.className = 'flex items-center justify-between gap-3 rounded-xl border border-gray-100 dark:border-gray-700 px-3 py-2.5';
      const detail = document.createElement('p'); detail.className = 'min-w-0';
      detail.innerHTML = `<strong class="block truncate text-gray-700 dark:text-gray-100">${escapeHtml(session.device_info)}</strong><span>${escapeHtml(session.ip_address)} · ${session.is_current ? 'Current session' : `Active ${escapeHtml(formatDate(session.last_activity))}`}</span>`;
      row.append(detail);
      if (session.is_current) { const badge = document.createElement('span'); badge.className = 'shrink-0 text-[10px] font-bold text-emerald-700 dark:text-emerald-300'; badge.textContent = 'Current'; row.append(badge); }
      else { const button = document.createElement('button'); button.type = 'button'; button.className = 'shrink-0 text-xs font-bold text-rose-600 dark:text-rose-300 hover:underline'; button.textContent = 'Remove'; button.addEventListener('click', () => window.revokeSession(session.id, button)); row.append(button); }
      target.append(row);
    });
  };
  const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, (character) => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', "'":'&#39;', '"':'&quot;' })[character]);
  const loadSessions = async () => { try { const data = await request('sessions.php'); renderSessions(data.sessions || []); } catch (error) { $('#sessionsList').textContent = error.message; } };
  window.revokeSession = async (sessionId, button) => { const done = setBusy(button, 'Removing…'); try { const data = await request('sessions.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'revoke', session_id: sessionId }) }); window.showToast(data.message); await loadSessions(); } catch (error) { window.showToast(error.message); } finally { done(); } };
  window.revokeOtherSessions = async () => { const button = $('[onclick="revokeOtherSessions()"]'); const done = setBusy(button, 'Signing out…'); try { const data = await request('sessions.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'revoke_other' }) }); window.showToast(data.message); await loadSessions(); } catch (error) { window.showToast(error.message); } finally { done(); } };
  window.saveSettings = async () => {
    const button = $('[onclick="saveSettings()"]'); const done = setBusy(button, 'Saving…');
    const profile = { full_name: $('#profileFullName').value.trim(), business_name: $('#profileBusinessName').value.trim(), phone: $('#profilePhone').value.trim() };
    const preferences = Object.fromEntries($$('[data-preference]').map((item) => [item.dataset.preference, item.getAttribute('aria-checked') === 'true']));
    try {
      await request('profile.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(profile) });
      await request('preferences.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(preferences) });
      window.showToast('Settings saved successfully.');
    } catch (error) { window.showToast(error.message); } finally { done(); }
  };
  const loadSettings = async () => {
    try {
      const [profileData, preferencesData] = await Promise.all([request('profile.php'), request('preferences.php')]);
      const profile = profileData.profile || {};
      $('#profileFullName').value = profile.full_name || ''; $('#profileBusinessName').value = profile.trading_name || profile.legal_name || '';
      $('#profileEmail').value = profile.email || ''; $('#profilePhone').value = profile.phone || '';
      setPinStatus(Boolean(profile.withdrawal_pin_set)); setTwoFactor(Boolean(Number(profile.two_factor_enabled)));
      $$('[data-preference]').forEach((item) => setSwitch(item, Boolean(preferencesData.preferences?.[item.dataset.preference])));
    } catch (error) { window.showToast(error.message); }
    await loadSessions();
  };
  $('#profileAvatarFile')?.addEventListener('change', async (event) => {
    const file = event.currentTarget.files?.[0];
    if (!file) return;
    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 2 * 1024 * 1024) {
      event.currentTarget.value = ''; return window.showToast('Choose a JPG, PNG, or WEBP image up to 2MB.');
    }
    const form = new FormData(); form.append('avatar', file);
    try {
      const response = await fetch(`${apiBase}/avatar.php`, { method: 'POST', headers: { Accept: 'application/json', Authorization: `Bearer ${token()}` }, body: form });
      const data = await response.json().catch(() => ({ error: 'Unexpected server response.' }));
      if (!response.ok || !data.success) throw new Error(data.error || 'Unable to update your profile photo.');
      window.FlowAvatar?.render({ full_name: $('#profileFullName').value, email: $('#profileEmail').value, avatar_url: data.avatar_url });
      window.showToast(data.message); event.currentTarget.value = '';
    } catch (error) { window.showToast(error.message); }
  });
  $$('.settings-tab').forEach((button) => button.addEventListener('click', () => {
    const tab = button.dataset.settingsTab;
    $$('.settings-tab').forEach((item) => item.className = 'settings-tab flex items-center gap-3 w-full rounded-xl px-3 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-[#13304f] transition');
    button.className = 'settings-tab flex items-center gap-3 w-full rounded-xl px-3 py-3 text-left text-xs font-bold bg-[#0b2545] text-white transition';
    $$('.settings-panel').forEach((panel) => panel.classList.toggle('hidden', panel.dataset.settingsPanel !== tab));
  }));
  loadSettings();
})();
