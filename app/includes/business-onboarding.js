(() => {
  const form = document.getElementById('onboardingForm');
  const next = document.getElementById('nextButton');
  const back = document.getElementById('backButton');
  const save = document.getElementById('saveOnboardingDraft');
  const status = document.getElementById('onboardingSaveStatus');
  const retry = document.getElementById('retryOnboardingLoad');
  const panels = [...form.querySelectorAll('.onboarding-step')];
  const uploads = [...form.querySelectorAll('input[type="file"]')];
  let step = 1, savedFiles = {}, ready = false, busy = false;
  const headers = () => ({ Authorization: `Bearer ${localStorage.getItem('flow_token') || ''}` });
  const showStatus = (message, error = false) => {
    status.textContent = message;
    status.classList.toggle('text-red-600', error);
  };
  const escape = value => { const span = document.createElement('span'); span.textContent = value; return span.innerHTML; };
  function updateBusinessType() {
    // Registration and KYC are collected later, when the account is ready for higher limits.
  }
  function syncFiles(clear = false) {
    uploads.forEach(input => {
      if (clear) input.value = '';
      input.required = input.id !== 'businessLogo' && !savedFiles[input.id];
      input.closest('.upload-field').querySelector('.file-name').textContent = input.files[0]?.name || (savedFiles[input.id] ? 'Uploaded securely — choose to replace' : 'Choose file');
    });
  }
  function review() {
    const data = Object.fromEntries(new FormData(form));
    const rows = [['Business type',data.businessType],['Business name',data.businessName],['Owner',data.ownerName],['Email',data.ownerEmail],['Phone',data.phone],['Account holder',data.accountName],['Bank',data.bankName],['Account number',data.accountNumber]];
    const summary = document.getElementById('reviewSummary');
    summary.innerHTML = rows.map(([label,value]) => `<div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3"><span class="block text-xs text-slate-500">${label}</span><strong class="block text-sm break-words text-slate-900 dark:text-white">${escape(value || 'Not provided')}</strong></div>`).join('');
    uploads.forEach(input => {
      if (!savedFiles[input.id]) return;
      const button = document.createElement('button');
      button.type = 'button'; button.className = 'text-left text-sm underline text-sky-700 dark:text-sky-300';
      button.textContent = `Download ${input.dataset.documentLabel || 'business logo'}`;
      button.addEventListener('click', async () => {
        try {
          const response = await fetch(`../api/onboarding/file.php?id=${savedFiles[input.id].id}`, { headers: headers() });
          if (!response.ok) throw new Error('Unable to download this document.');
          const url = URL.createObjectURL(await response.blob());
          const link = document.createElement('a'); link.href = url;
          const extensions = {'application/pdf':'pdf','image/png':'png','image/jpeg':'jpg','image/webp':'webp'};
          link.download = `${input.id}.${extensions[savedFiles[input.id].mime] || 'bin'}`;
          document.body.append(link); link.click(); link.remove(); setTimeout(() => URL.revokeObjectURL(url), 1000);
        } catch (error) { showStatus(error.message, true); }
      });
      summary.append(button);
    });
  }
  function render() {
    panels.forEach(panel => panel.classList.toggle('hidden', Number(panel.dataset.step) !== step));
    back.classList.toggle('hidden', step === 1);
    next.textContent = step === panels.length ? 'Submit for review' : 'Next';
    next.disabled = save.disabled = !ready || busy;
    back.disabled = busy;
    if (step === panels.length) review();
  }
  function submitted() {
    document.getElementById('onboardingWelcome').classList.add('hidden');
    document.getElementById('onboardingApp').classList.remove('hidden');
    document.getElementById('onboardingApp').classList.add('flex');
    form.classList.add('hidden');
    document.getElementById('submittedState').classList.remove('hidden');
  }
  async function load() {
    retry.classList.add('hidden'); showStatus('Loading saved details…');
    try {
      const response = await fetch('../api/onboarding/business.php', { headers: headers() });
      const result = await response.json();
      if (!response.ok || !result.success) throw new Error(result.error || 'Unable to load saved details.');
      Object.entries(result.data || {}).forEach(([name,value]) => {
        const input = form.elements[name];
        if (!input || input.type === 'file') return;
        if (input.type === 'checkbox') input.checked = false;
        else input.value = value;
      });
      savedFiles = result.files || {}; syncFiles(); updateBusinessType(); ready = true;
      // Remove the old browser-only draft; account data now comes from the authenticated API.
      localStorage.removeItem('flowwithlitOnboardingDraft');
      showStatus('Your details and files are saved when you select Next or Save draft.');
      if (['submitted','under_review','approved'].includes(result.status)) submitted();
    } catch (error) { showStatus(error.message, true); retry.classList.remove('hidden'); }
    render();
  }
  async function persist(submit = false) {
    const body = new FormData(form);
    body.set('action', submit ? 'submit' : 'draft');
    body.set('declaration', document.getElementById('declaration').checked ? '1' : '0');
    uploads.forEach(input => { if (!input.files.length) body.delete(input.name); });
    const response = await fetch('../api/onboarding/business.php', { method:'POST', headers:headers(), body });
    const result = await response.json();
    if (!response.ok || !result.success) throw new Error(result.error || 'Unable to save. Please try again.');
    savedFiles = result.files || {}; syncFiles(true);
    showStatus(submit ? 'Submitted securely for review.' : 'Draft and uploads saved securely.');
    return result;
  }
  async function advance(draftOnly = false) {
    if (!ready || busy) return;
    if (!draftOnly) {
      const invalid = [...panels[step - 1].querySelectorAll('input,select,textarea')].find(input => !input.checkValidity());
      if (invalid) { invalid.reportValidity(); return; }
    }
    busy = true; render(); showStatus('Saving securely…');
    // Keep the request snapshot and visible form aligned during file uploads.
    const controls = [...form.querySelectorAll('input,select,textarea')];
    try {
      const request = persist(!draftOnly && step === panels.length);
      controls.forEach(input => input.disabled = true);
      const result = await request;
      if (['submitted','under_review','approved'].includes(result.status)) submitted();
      else if (!draftOnly) { step++; form.scrollTop = 0; }
    } catch (error) { showStatus(error.message, true); }
    finally { controls.forEach(input => input.disabled = false); busy = false; render(); }
  }
  uploads.forEach(input => {
    input.name = input.id;
    input.addEventListener('change', () => {
      input.setCustomValidity(input.files[0]?.size > 10 * 1024 * 1024 ? 'Choose a file no larger than 10MB.' : '');
      syncFiles();
    });
  });
  document.getElementById('declaration').name = 'declaration';
  form.elements.businessType.addEventListener('change', updateBusinessType);
  form.addEventListener('submit', event => { event.preventDefault(); advance(); });
  next.addEventListener('click', () => advance());
  save.addEventListener('click', () => advance(true));
  back.addEventListener('click', () => { if (!busy && step > 1) { step--; render(); form.scrollTop = 0; } });
  retry.addEventListener('click', load);
  document.getElementById('startActivation').addEventListener('click', () => {
    document.getElementById('onboardingWelcome').classList.add('hidden');
    document.getElementById('onboardingApp').classList.remove('hidden');
    document.getElementById('onboardingApp').classList.add('flex');
  });
  window.addEventListener('flowwithlit:session-ready', () => {
    if (!document.documentElement.classList.contains('personal-setup-active')) load();
  });
  render();
})();
