(() => {
  const $ = (selector) => document.querySelector(selector);
  const api = `${window.dashboardConfig.apiBase}/settings.php`;
  const smtpApi = `${window.dashboardConfig.apiBase}/smtp.php`;
  const token = () => localStorage.getItem('flow_admin_token') || '';
  const headers = () => ({ Authorization: `Bearer ${token()}` });
  const form = $('#settings-form');
  const smtpForm = $('#smtp-form');
  const smtpStatus = $('#smtp-status');
  const smtpBadge = $('#smtp-badge');
  const toast = (message) => window.dashboardShowToast?.(message);

  const setSmtpStatus = (message, type = '') => {
    smtpStatus.textContent = message;
    smtpStatus.className = `smtp-status ${type}`.trim();
  };
  const setSmtpBadge = (label, type = 'neutral') => {
    smtpBadge.className = `section-badge ${type === 'neutral' ? 'neutral' : ''}`.trim();
    smtpBadge.textContent = label;
  };
  const setBusy = (button, text) => {
    const original = button.innerHTML;
    button.disabled = true;
    button.setAttribute('aria-busy', 'true');
    button.innerHTML = `<span class="spinner" aria-hidden="true"></span>${text}`;
    return () => {
      button.disabled = false;
      button.removeAttribute('aria-busy');
      button.innerHTML = original;
      window.lucide?.createIcons({ attrs: { 'stroke-width': 1.8 } });
    };
  };
  const smtpPayload = (action) => ({
    action,
    smtp: {
      host: smtpForm.host.value.trim(),
      port: Number(smtpForm.port.value),
      username: smtpForm.username.value.trim(),
      password: smtpForm.password.value,
      encryption: smtpForm.encryption.value,
      from_address: smtpForm.from_address.value.trim(),
      from_name: smtpForm.from_name.value.trim(),
    },
    recipient: smtpForm.recipient.value.trim(),
  });
  async function smtpRequest(action, button) {
    if (!smtpForm.reportValidity()) return;
    const restore = setBusy(button, action === 'test' ? 'Sending test…' : 'Saving…');
    setSmtpStatus(action === 'test' ? 'Connecting to your SMTP provider and sending a real test email…' : 'Saving SMTP settings securely…');
    try {
      const response = await fetch(smtpApi, { method: 'POST', headers: { ...headers(), 'Content-Type': 'application/json' }, body: JSON.stringify(smtpPayload(action)) });
      const data = await response.json().catch(() => ({}));
      if (!response.ok || !data.success) throw new Error(data.error || 'SMTP request failed.');
      setSmtpStatus(data.message, 'success');
      setSmtpBadge(action === 'test' ? 'Test sent' : 'Configured', 'success');
      if (action === 'save') smtpForm.password.value = '';
      toast(data.message);
    } catch (error) {
      const message = error.message || 'SMTP request failed.';
      setSmtpStatus(message, 'error');
      setSmtpBadge('Needs attention', 'error');
      toast(message);
    } finally { restore(); }
  }
  async function loadSmtp() {
    try {
      const response = await fetch(smtpApi, { headers: headers() });
      const data = await response.json().catch(() => ({}));
      if (!response.ok || !data.success) throw new Error(data.error || 'SMTP settings could not be loaded.');
      const smtp = data.smtp || {};
      ['host', 'port', 'username', 'encryption', 'from_address', 'from_name'].forEach((key) => {
        if (smtp[key] !== undefined) smtpForm[key].value = smtp[key];
      });
      if (!data.phpmailer_ready) {
        setSmtpStatus('PHPMailer is not installed on this server. Install Composer dependencies before testing email.', 'warning');
        setSmtpBadge('Dependency missing', 'warning');
      } else if (smtp.configured) {
        setSmtpStatus('SMTP settings are saved. Enter a test recipient and send an email to verify the provider accepts delivery.', 'success');
        setSmtpBadge('Configured', 'success');
      } else {
        setSmtpStatus('Add your SMTP provider details, save them securely, then send a test to a real inbox.', 'warning');
        setSmtpBadge('Not configured', 'warning');
      }
    } catch (error) {
      setSmtpStatus(error.message || 'SMTP settings could not be loaded.', 'error');
      setSmtpBadge('Unavailable', 'error');
    }
  }
  async function load() {
    try {
      const response = await fetch(api, { headers: headers() });
      const data = await response.json().catch(() => ({}));
      if (!response.ok || !data.success) throw new Error(data.error || 'Settings could not be loaded.');
      const settings = data.settings || {}, stats = data.stats || {};
      form.support_email.value = settings.support_email || '';
      form.default_currency.value = settings.default_currency || 'NGN';
      form.payout_review_limit.value = settings.payout_review_limit || 0;
      form.maintenance_mode.checked = Boolean(settings.maintenance_mode);
      $('#settings-users').textContent = Number(stats.users || 0).toLocaleString();
      $('#settings-businesses').textContent = Number(stats.businesses || 0).toLocaleString();
      $('#settings-keys').textContent = Number(stats.api_keys || 0).toLocaleString();
      $('#settings-processing').textContent = Number(stats.processing || 0).toLocaleString();
      $('#settings-updated').textContent = data.updated_at ? `Last updated ${new Date(`${data.updated_at.replace(' ', 'T')}Z`).toLocaleString()}` : 'Using platform defaults until the first save.';
      $('#settings-loading').hidden = true;
      $('#settings-error').hidden = true;
      $('#settings-content').hidden = false;
      await loadSmtp();
    } catch (error) {
      $('#settings-loading').hidden = true;
      $('#settings-error').hidden = false;
      $('#settings-error').firstElementChild.textContent = error.message || 'Settings could not be loaded.';
    }
  }
  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const restore = setBusy($('#settings-save'), 'Saving…');
    try {
      const settings = { support_email: form.support_email.value.trim(), default_currency: form.default_currency.value, payout_review_limit: Number(form.payout_review_limit.value), maintenance_mode: form.maintenance_mode.checked };
      const response = await fetch(api, { method: 'POST', headers: { ...headers(), 'Content-Type': 'application/json' }, body: JSON.stringify({ settings }) });
      const data = await response.json().catch(() => ({}));
      if (!response.ok || !data.success) throw new Error(data.error || 'Could not save settings.');
      toast(data.message || 'Platform settings saved.');
      await load();
    } catch (error) { toast(error.message || 'Could not save settings.'); } finally { restore(); }
  });
  smtpForm.addEventListener('submit', (event) => { event.preventDefault(); smtpRequest('save', $('#smtp-save')); });
  $('#smtp-test').addEventListener('click', () => smtpRequest('test', $('#smtp-test')));
  $('#settings-retry').addEventListener('click', load);
  load();
})();
