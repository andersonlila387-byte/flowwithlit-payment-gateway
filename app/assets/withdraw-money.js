(() => {
  'use strict';
  const $ = id => document.getElementById(id);
  const auth = () => ({Authorization: `Bearer ${localStorage.getItem('flow_token') || ''}`});
  let fingerprint = '', key = '', pending = false, verifying = false, walletsReady = false;
  let verification = null, generation = 0;
  const isBank = () => $('withdraw-wallet').value === 'NGN';
  const destinationKey = () => JSON.stringify([$('withdraw-bank-code').value, $('withdraw-account').value.trim()]);
  function ready() {
    const verified = verification && verification.destination === destinationKey() && Date.now() < verification.expires;
    $('withdraw-submit').disabled = pending || !walletsReady || (isBank() && !verified);
    $('withdraw-verify').disabled = pending || verifying || !$('withdraw-bank-code').value || !/^[0-9]{10}$/.test($('withdraw-account').value.trim());
  }
  function clearVerification() {
    generation++; verification = null; $('withdraw-name').value = '';
    $('withdraw-verification-status').textContent = 'Verify this destination before continuing.'; ready();
  }
  function selection() {
    const bank = isBank();
    $('withdraw-bank').hidden = !bank; $('withdraw-crypto').hidden = bank;
    for(const id of ['withdraw-bank-code','withdraw-account']) $(id).required = bank;
    for(const id of ['withdraw-network','withdraw-address']) $(id).required = !bank;
    ready();
  }
  async function request(url, options = {}) {
    const r = await fetch(url, {...options, headers: {...auth(), ...options.headers}});
    const data = await r.json(); if(!r.ok || !data.success) throw Error(data.error || 'Request failed.'); return data;
  }
  for(const id of ['withdraw-bank-code','withdraw-account']) $(id).addEventListener('input', clearVerification);
  $('withdraw-verify').addEventListener('click', async () => {
    if(verifying || pending) return;
    clearVerification(); const version = generation, destination = destinationKey();
    verifying = true; ready(); $('withdraw-verify').textContent = 'Verifying…';
    $('withdraw-verification-status').textContent = 'Checking the registered account name…';
    try {
      const data = await request('../api/transfers/lookup-account.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({bank_code:$('withdraw-bank-code').value, account_number:$('withdraw-account').value.trim()})});
      if(version !== generation || destination !== destinationKey()) return;
      if(!data.verified || !data.verification_token || !data.account_name) throw Error('The provider did not verify this account.');
      const expires = Date.parse(data.expires_at.replace(' ','T')+'Z');
      if(!Number.isFinite(expires) || expires <= Date.now()) throw Error('Verification expired. Please try again.');
      verification = {token:data.verification_token, destination, expires}; $('withdraw-name').value = data.account_name;
      $('withdraw-verification-status').textContent = 'Account verified. Confirm the name is correct before submitting.';
    } catch(e) { if(version === generation) $('withdraw-verification-status').textContent = e.message; }
    finally { verifying = false; $('withdraw-verify').textContent = 'Verify account'; ready(); }
  });
  $('withdraw-wallet').addEventListener('change', selection);
  request('../api/transfers/banks.php').then(data => {
    $('withdraw-bank-code').replaceChildren(new Option('Select a bank',''));
    for(const bank of data.banks) $('withdraw-bank-code').add(new Option(bank.name,bank.code)); ready();
  }).catch(e => {
    $('withdraw-bank-code').replaceChildren(new Option('Bank verification unavailable',''));
    $('withdraw-verification-status').textContent = e.message; ready();
  });
  fetch('../api/wallets/list.php', {headers: auth()}).then(async r => {
    const data = await r.json(); if(!r.ok || !data.success) throw Error(data.error || 'Wallets unavailable.');
    $('withdraw-wallet').replaceChildren();
    for(const w of data.wallets.filter(w => w.status === 'active')) $('withdraw-wallet').add(new Option(`${w.currency_code} · ${w.available_balance}`,w.currency_code));
    walletsReady = Boolean($('withdraw-wallet').options.length); selection();
  }).catch(e => $('withdraw-status').textContent = e.message);
  $('withdraw-form').addEventListener('submit', async e => {
    e.preventDefault(); if(pending) return;
    const bank = isBank();
    if(!walletsReady) return;
    if(bank && (!verification || verification.destination !== destinationKey() || Date.now() >= verification.expires)) {
      clearVerification(); $('withdraw-status').textContent = 'Verify the destination bank account before submitting.'; return;
    }
    const payload = {action:'withdraw',currency:$('withdraw-wallet').value,amount:$('withdraw-amount').value.trim(),destination:bank
      ? {bank_code:$('withdraw-bank-code').value.trim(),account_number:$('withdraw-account').value.trim()}
      : {network:$('withdraw-network').value.trim(),address:$('withdraw-address').value.trim(),memo:$('withdraw-memo').value.trim()}};
    const next = JSON.stringify(payload); if(next !== fingerprint) { fingerprint = next; key = crypto.randomUUID(); }
    pending = true; ready(); $('withdraw-submit').textContent = 'Requesting…'; $('withdraw-status').textContent = '';
    try {
      const r = await fetch('../api/financial/action.php', {method:'POST',headers:{...auth(),'Content-Type':'application/json','Idempotency-Key':key},body:JSON.stringify({...payload,destination:{...payload.destination,...(bank?{verification_token:verification.token}:{})},pin:$('withdraw-pin').value})});
      const data = await r.json(); if(!r.ok || !data.success) throw Error(data.error || 'Withdrawal failed.');
      $('withdraw-pin').value = '';
      $('withdraw-status').textContent = `Withdrawal ${data.reference} ${data.status}. ${data.duplicate ? 'This is your existing request.' : 'Manual processing is due within 24 hours.'}`;
    } catch(error) { $('withdraw-status').textContent = error.message; }
    finally { pending = false; ready(); $('withdraw-submit').textContent = 'Request withdrawal'; }
  });
})();
