// Account verification onboarding for both account types.
// The flow is chosen by the account type from signup (never by screen size):
//   personal → identity → review;  business → business details → identity → payout → review.
// Signup details (name, email, phone, country, business name/type) are shown, not re-asked.
(() => {
  const form = document.getElementById('onboardingForm');
  const next = document.getElementById('nextButton');
  const back = document.getElementById('backButton');
  const save = document.getElementById('saveOnboardingDraft');
  const status = document.getElementById('onboardingSaveStatus');
  const retry = document.getElementById('retryOnboardingLoad');
  const start = document.getElementById('startActivation');
  const allPanels = [...form.querySelectorAll('.onboarding-step')];
  const uploads = [...form.querySelectorAll('input[type="file"]')];
  const STAGES = { starter: 'Starter Business', registered: 'Registered Business' };
  let panels = allPanels, step = 1, savedFiles = {}, ready = false, busy = false, profile = null, kind = 'individual';

  const $ = id => document.getElementById(id);
  const headers = () => ({ Authorization: `Bearer ${localStorage.getItem('flow_token') || ''}` });
  const showStatus = (message, error = false) => { status.textContent = message; status.classList.toggle('text-red-600', error); };
  const escape = value => { const span = document.createElement('span'); span.textContent = value ?? ''; return span.innerHTML; };
  const isBusiness = () => kind === 'business';
  const stage = () => profile?.business_stage || form.querySelector('input[name="businessStage"]:checked')?.value || '';

  // Hide (and disable, so they are neither validated nor sent) anything that is not part of this flow.
  function setShown(el, shown) {
    el.toggleAttribute('data-hidden-by-flow', !shown);
    el.querySelectorAll('input,select,textarea').forEach(input => { input.disabled = !shown; });
    if (el.matches('input,select,textarea')) el.disabled = !shown;
  }
  function applyFlow() {
    document.querySelectorAll('[data-kind="business"]').forEach(el => setShown(el, isBusiness()));
    const registered = stage() === 'registered';
    form.querySelectorAll('[data-stage="registered"]').forEach(el => setShown(el, isBusiness() && registered));
    // Stage: show the signup answer, or ask for it if this account predates the new signup form.
    $('stageSummary').classList.toggle('hidden', !profile?.business_stage);
    $('stageSummaryText').textContent = STAGES[profile?.business_stage] || '';
    setShown($('stageChoice'), isBusiness() && !profile?.business_stage);
    $('stageChoice').classList.toggle('hidden', !isBusiness() || Boolean(profile?.business_stage));
    form.querySelectorAll('input[name="businessStage"]').forEach(r => { r.required = !profile?.business_stage; });
    // Phone is only asked if signup did not capture one (older accounts).
    const needsPhone = !profile?.phone;
    $('phoneField').classList.toggle('hidden', !needsPhone);
    $('phone').disabled = !needsPhone; $('phone').required = needsPhone;
    panels = allPanels.filter(p => p.dataset.kind !== 'business' || isBusiness());
    syncFiles();
  }

  function applyCopy() {
    const first = profile?.first_name || (profile?.full_name || '').split(' ')[0];
    // Personal accounts show two welcome cards; keep them centred.
    $('welcomeCards').classList.toggle('lg:grid-cols-3', isBusiness());
    $('welcomeCards').classList.toggle('max-w-5xl', isBusiness());
    $('welcomeCards').classList.toggle('lg:grid-cols-2', !isBusiness());
    $('welcomeCards').classList.toggle('max-w-3xl', !isBusiness());
    $('welcomeName').textContent = first ? `, ${first}` : '';
    if (isBusiness()) {
      $('welcomeBadge').textContent = 'Test mode';
      $('welcomeText').textContent = 'Your business account is in Test mode. Complete verification to unlock live payments and payouts.';
      $('activationTitle').textContent = 'Activate your business';
      $('activationText').textContent = 'Add your business details, verify the owner, and choose where payouts go.';
      $('identityHeading').textContent = "Owner's identity";
      $('identityIntro').textContent = 'We verify the person who owns or runs the business.';
      $('nextStepsText').innerHTML = 'Your business will be marked as <strong>under review</strong>. Test mode stays available, but live keys and live payouts stay locked until Flowwithlit approves your verification.';
      $('submittedText').textContent = 'Your business verification is under review. You can keep using test tools, but Live mode and live keys stay locked until it is approved.';
    } else {
      $('welcomeBadge').textContent = 'Personal account';
      $('welcomeText').textContent = 'Your wallet is ready to use. Verify your identity to raise your sending, receiving and balance limits.';
      $('activationTitle').textContent = 'Verify your identity';
      $('activationText').textContent = 'Add your date of birth, home address and a photo of your ID. It takes about two minutes.';
      $('exploreText').textContent = 'Fund your wallet, send money with Flow Tags, and swap between Naira and crypto.';
      $('identityHeading').textContent = 'Verify your identity';
      $('identityIntro').textContent = 'We need to confirm who you are before raising your limits.';
      $('nextStepsText').innerHTML = 'Your details will be <strong>reviewed</strong>, usually within one business day. You can keep using your wallet at your current limits while you wait.';
      $('submittedText').textContent = 'Your identity details are under review. You can keep using your wallet at your current limits, and we will let you know once your higher limits are active.';
    }
    const rows = [['Name', profile?.full_name], ['Email', profile?.email], ['Phone', profile?.phone], ['Country', profile?.country_name || (profile?.country_code ? profile.country_code : 'Other')]];
    if (isBusiness()) rows.push(['Business', profile?.business_name]);
    $('signupDetails').innerHTML = rows.filter(([, v]) => v).map(([label, value]) => `<div><dt class="text-[11px] text-slate-500">${label}</dt><dd class="text-sm font-semibold break-words text-slate-900 dark:text-white">${escape(value)}</dd></div>`).join('');
  }

  // ---- Payout account: Paystack bank list + account-name lookup, with a manual fallback ----
  const MANUAL_BANKS = ['Access Bank', 'Fidelity Bank', 'First Bank', 'GTBank', 'Kuda Bank', 'Moniepoint', 'Opay', 'Palmpay', 'Stanbic IBTC', 'Sterling Bank', 'UBA', 'Union Bank', 'Wema Bank', 'Zenith Bank', 'Other'];
  let bankMode = null, lookupSeq = 0, savedPayout = {};
  const bankSelect = () => $('bankSelect');
  function lookupStatus(message, tone) {
    const el = $('payoutLookupStatus');
    el.classList.toggle('hidden', !message);
    el.className = el.className.replace(/\b(bg|text)-(emerald|red|slate)-\d+\b/g, '').trim();
    if (message) el.classList.add(...({ ok: ['bg-emerald-50', 'text-emerald-700'], error: ['bg-red-50', 'text-red-700'], wait: ['bg-slate-50', 'text-slate-600'] }[tone] || []));
    el.textContent = message || '';
  }
  function setManualMode(reason) {
    bankMode = 'manual';
    const current = $('bankName').value;
    bankSelect().innerHTML = '<option value="">Select</option>' + MANUAL_BANKS.map(n => `<option>${escape(n)}</option>`).join('');
    if (current) bankSelect().value = MANUAL_BANKS.includes(current) ? current : 'Other';
    $('accountName').readOnly = false; $('accountName').placeholder = 'Name on account';
    $('accountNameHint').textContent = reason || 'Type the name exactly as it appears at your bank.';
    $('bankCode').value = ''; $('bankVerificationToken').value = '';
    syncBank();
  }
  async function loadBanks() {
    if (!isBusiness() || bankMode) return;
    try {
      const response = await fetch('../api/transfers/banks.php', { headers: headers() });
      const result = await response.json();
      if (!response.ok || !result.success || !result.banks?.length) throw new Error(result.error || 'unavailable');
      bankMode = 'lookup';
      bankSelect().innerHTML = '<option value="">Select your bank</option>' + result.banks.map(b => `<option value="${escape(b.code)}">${escape(b.name)}</option>`).join('');
      if ($('bankCode').value) bankSelect().value = $('bankCode').value;
      $('accountName').readOnly = true; $('accountName').placeholder = 'Filled in automatically from your bank';
      $('accountNameHint').textContent = 'We confirm the account name with your bank. It must match your business or your own name.';
      syncBank();
      if (savedMatches()) { $('accountName').value = savedPayout.name; lookupStatus(`✓ ${savedPayout.name} — name confirmed by your bank`, 'ok'); }
      else { $('accountName').value = ''; lookupAccount(); }
    } catch {
      setManualMode('Automatic account check is unavailable right now. Type the name exactly as it appears at your bank.');
    }
  }
  function syncBank() {
    const opt = bankSelect().selectedOptions[0];
    if (bankMode === 'lookup') { $('bankCode').value = bankSelect().value; $('bankName').value = bankSelect().value ? opt.textContent : ''; }
    else { $('bankName').value = bankSelect().value; $('bankCode').value = ''; }
  }
  const savedMatches = () => savedPayout.verified && savedPayout.number === $('accountNumber').value && savedPayout.code === $('bankCode').value;
  const payoutReady = () => bankMode !== 'lookup' || Boolean($('bankVerificationToken').value) || savedMatches();
  async function lookupAccount() {
    $('bankVerificationToken').value = '';
    if (bankMode !== 'lookup') return;
    const number = $('accountNumber').value, code = $('bankCode').value;
    if (savedMatches()) { $('accountName').value = savedPayout.name; lookupStatus(`✓ ${savedPayout.name} — name confirmed by your bank`, 'ok'); return; }
    $('accountName').value = '';
    if (!/^\d{10}$/.test(number) || !code) { lookupStatus(''); return; }
    const seq = ++lookupSeq;
    lookupStatus('Checking account name with your bank…', 'wait');
    try {
      const response = await fetch('../api/transfers/lookup-account.php', { method: 'POST', headers: { ...headers(), 'Content-Type': 'application/json' }, body: JSON.stringify({ account_number: number, bank_code: code }) });
      const result = await response.json();
      if (seq !== lookupSeq) return; // a newer lookup is in flight
      if (response.ok && result.success) {
        $('accountName').value = result.account_name; $('bankVerificationToken').value = result.verification_token;
        lookupStatus(`✓ ${result.account_name} — name confirmed by your bank`, 'ok');
      } else if (response.status === 503) {
        setManualMode('We could not reach your bank to confirm the name. Type it exactly as it appears at your bank.');
        lookupStatus('');
      } else {
        lookupStatus(result.error || 'We could not confirm this account. Check the number and bank.', 'error');
      }
    } catch {
      if (seq === lookupSeq) lookupStatus('Could not check the account. Check your connection and try again.', 'error');
    }
  }

  function syncFiles(clear = false) {
    uploads.forEach(input => {
      if (clear) input.value = '';
      const field = input.closest('.upload-field');
      input.required = !input.disabled && !field.hasAttribute('data-optional') && !savedFiles[input.id];
      field.querySelector('.file-name').textContent = input.files[0]?.name || (savedFiles[input.id] ? 'Uploaded securely — choose to replace' : 'Choose file');
    });
  }

  function review() {
    const data = Object.fromEntries(new FormData(form));
    const rows = [];
    if (isBusiness()) {
      rows.push(['Business type', STAGES[stage()]], ['Business name', data.businessName], ['Industry', data.industry]);
      if (data.website) rows.push(['Website', data.website]);
      if (stage() === 'registered') rows.push(['CAC number', data.registrationNumber]);
    }
    rows.push(['Name', profile.full_name], ['Phone', profile.phone || data.phone], ['Date of birth', data.dateOfBirth], ['Home address', [data.addressLine1, data.city, data.stateRegion].filter(Boolean).join(', ')]);
    if (isBusiness()) rows.push(['Payout account', [data.accountName, data.bankName, data.accountNumber ? `••••${String(data.accountNumber).slice(-4)}` : '', savedMatches() || data.bankVerificationToken ? 'name confirmed by bank' : ''].filter(Boolean).join(' · ')]);
    const docs = uploads.filter(i => !i.disabled && (i.files.length || savedFiles[i.id])).map(i => i.dataset.documentLabel);
    rows.push(['Documents', docs.length ? docs.join(', ') : 'None yet']);
    $('reviewSummary').innerHTML = rows.map(([label, value]) => `<div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3"><span class="block text-xs text-slate-500">${label}</span><strong class="block text-sm break-words text-slate-900 dark:text-white">${escape(value || 'Not provided')}</strong></div>`).join('');
  }

  function render() {
    allPanels.forEach(panel => panel.classList.add('hidden'));
    panels[step - 1]?.classList.remove('hidden');
    $('stepLabel').textContent = panels.length ? `Step ${step} of ${panels.length} · ${panels[step - 1].dataset.title}` : '';
    back.classList.toggle('hidden', step === 1);
    next.textContent = step === panels.length ? 'Submit for review' : 'Next';
    next.disabled = save.disabled = !ready || busy;
    back.disabled = busy;
    if (step === panels.length && profile) review();
  }

  function showForm() {
    $('onboardingWelcome').classList.add('hidden');
    $('onboardingApp').classList.remove('hidden');
    $('onboardingApp').classList.add('flex');
  }
  function submitted(state) {
    showForm();
    form.classList.add('hidden');
    $('submittedState').classList.remove('hidden');
    if (state === 'approved') { $('submittedTitle').textContent = 'You are verified'; $('submittedText').textContent = isBusiness() ? 'Your business is approved. Live mode is now available.' : 'Your identity is verified and your higher limits are active.'; }
  }

  function prefill(data) {
    const fromProfile = {
      businessName: profile.business_name, accountName: profile.business_name || profile.full_name,
      dateOfBirth: profile.date_of_birth, addressLine1: profile.address_line1, city: profile.city, stateRegion: profile.state_region,
    };
    const values = { ...Object.fromEntries(Object.entries(fromProfile).filter(([, v]) => v)), ...Object.fromEntries(Object.entries(data || {}).filter(([, v]) => v !== '' && v !== null)) };
    Object.entries(values).forEach(([name, value]) => {
      const input = form.elements[name];
      if (!input || input.type === 'file' || input.type === 'checkbox') return;
      if (input instanceof RadioNodeList) { [...input].forEach(r => { r.checked = r.value === value; }); return; }
      input.value = value;
    });
    $('dateOfBirth').max = new Date(Date.now() - 18 * 365.25 * 864e5).toISOString().slice(0, 10);
  }

  async function load() {
    retry.classList.add('hidden'); showStatus('Loading saved details…');
    try {
      const response = await fetch('../api/onboarding/business.php', { headers: headers() });
      const result = await response.json();
      if (!response.ok || !result.success) throw new Error(result.error || 'Unable to load saved details.');
      profile = result.profile; kind = profile.account_type === 'business' ? 'business' : 'individual';
      savedFiles = result.files || {};
      const d = result.data || {};
      savedPayout = { verified: Boolean(d.payoutVerified), number: d.accountNumber || '', code: d.bankCode || '', name: d.accountName || '' };
      applyCopy(); prefill(result.data); applyFlow(); loadBanks();
      ready = true; start.disabled = false;
      localStorage.removeItem('flowwithlitOnboardingDraft');
      showStatus('Your details and files are saved when you select Next or Save draft.');
      if (result.status === 'rejected' && result.review_note) { $('reviewNote').classList.remove('hidden'); $('reviewNote').querySelector('span').textContent = result.review_note; }
      if (['submitted','under_review','approved'].includes(result.status)) submitted(result.status);
      window.lucide?.createIcons?.();
    } catch (error) { showStatus(error.message, true); retry.classList.remove('hidden'); $('welcomeText').textContent = error.message; }
    render();
  }

  async function persist(submit = false) {
    const body = new FormData(form);
    body.set('action', submit ? 'submit' : 'draft');
    body.set('declaration', $('declaration').checked ? '1' : '0');
    uploads.forEach(input => { if (!input.files.length || input.disabled) body.delete(input.name); });
    const response = await fetch('../api/onboarding/business.php', { method:'POST', headers:headers(), body });
    const result = await response.json();
    if (!response.ok || !result.success) throw new Error(result.error || 'Unable to save. Please try again.');
    savedFiles = result.files || {}; syncFiles(true);
    const d = result.data || {};
    savedPayout = { verified: Boolean(d.payoutVerified), number: d.accountNumber || '', code: d.bankCode || '', name: d.accountName || '' };
    if (d.accountName) $('accountName').value = d.accountName; // the server keeps the bank-confirmed name
    $('bankVerificationToken').value = '';
    showStatus(submit ? 'Submitted securely for review.' : 'Draft and uploads saved securely.');
    return result;
  }

  async function advance(draftOnly = false) {
    if (!ready || busy) return;
    if (!draftOnly && panels[step - 1].contains($('accountNumber')) && !payoutReady()) {
      showStatus('Enter your account number and bank so we can confirm the account name.', true);
      $('accountNumber').focus(); return;
    }
    if (!draftOnly) {
      const invalid =[...panels[step - 1].querySelectorAll('input,select,textarea')].find(input => !input.disabled && !input.checkValidity());
      if (invalid) { invalid.reportValidity(); if (invalid.type === 'file') showStatus(`Please upload the ${invalid.dataset.documentLabel}.`, true); return; }
    }
    busy = true; render(); showStatus('Saving securely…');
    const controls = [...form.querySelectorAll('input,select,textarea')].filter(input => !input.disabled);
    try {
      const request = persist(!draftOnly && step === panels.length);
      controls.forEach(input => input.disabled = true);
      const result = await request;
      if (['submitted','under_review','approved'].includes(result.status)) submitted(result.status);
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
  $('declaration').name = 'declaration';
  form.querySelectorAll('input[name="businessStage"]').forEach(r => r.addEventListener('change', applyFlow));
  bankSelect().addEventListener('change', () => { syncBank(); lookupAccount(); });
  $('accountNumber').addEventListener('input', () => {
    $('accountNumber').value = $('accountNumber').value.replace(/\D/g, '').slice(0, 10);
    lookupAccount();
  });
  form.addEventListener('submit', event => { event.preventDefault(); advance(); });
  next.addEventListener('click', () => advance());
  save.addEventListener('click', () => advance(true));
  back.addEventListener('click', () => { if (!busy && step > 1) { step--; render(); form.scrollTop = 0; } });
  retry.addEventListener('click', load);
  start.addEventListener('click', showForm);
  window.addEventListener('flowwithlit:session-ready', load, { once: true });
  render();
})();
