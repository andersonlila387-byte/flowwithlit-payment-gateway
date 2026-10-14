<section id="personalOnboarding" class="hidden flex-1 overflow-y-auto px-5 py-8">
  <form id="personalSetupForm" class="mx-auto max-w-lg space-y-5">
    <div><h1 class="text-2xl font-bold text-slate-900 dark:text-white">Let's get you started</h1><p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Confirm your personal details and home address to finish setting up your account.</p></div>
    <label class="form-field block"><span>Full name</span><input name="full_name" autocomplete="name" maxlength="150" required></label>
    <label class="form-field block"><span>Email address</span><input name="email" type="email" autocomplete="email" readonly required><small class="text-slate-500">The email used for your account.</small></label>
    <label class="form-field block"><span>Phone number</span><input name="phone" type="tel" autocomplete="tel" maxlength="30" required placeholder="+234 801 234 5678"></label>
    <label class="form-field block"><span>Home address</span><input name="address_line1" autocomplete="address-line1" maxlength="191" required placeholder="Street address"></label>
    <div class="grid grid-cols-2 gap-4">
      <label class="form-field"><span>City</span><input name="city" autocomplete="address-level2" maxlength="100" required></label>
      <label class="form-field"><span>State / region</span><input name="state_region" autocomplete="address-level1" maxlength="100" required></label>
    </div>
    <label class="form-field block"><span>Country</span><select name="country_code" autocomplete="country" required><option value="">Select</option><option value="NG">Nigeria</option><option value="GH">Ghana</option><option value="KE">Kenya</option><option value="ZA">South Africa</option></select></label>
    <p id="personalSetupError" role="alert" class="hidden text-sm text-red-600 dark:text-red-400"></p>
    <div class="flex justify-end"><button id="personalSetupSubmit" type="submit" disabled class="rounded-xl bg-[#0b2545] px-5 py-3 text-sm font-semibold text-white disabled:opacity-50">Finish setup</button></div>
  </form>
</section>
<style>
  .personal-setup-active #onboardingWelcome, .personal-setup-active #onboardingApp { display:none !important; }
  .personal-setup-active #personalOnboarding { display:block; }
</style>
<script>
(() => {
  const personalForm = document.getElementById('personalSetupForm');
  const submit = document.getElementById('personalSetupSubmit');
  const error = document.getElementById('personalSetupError');
  // Keep the chosen flow stable if the phone rotates or the viewport changes.
  const mobileSetup = window.matchMedia('(max-width:767px)').matches && new URLSearchParams(location.search).get('mode') !== 'business';
  if (mobileSetup) document.documentElement.classList.add('personal-setup-active');
  window.addEventListener('flowwithlit:session-ready', event => {
    const user = event.detail;
    if (mobileSetup && user.onboarding_complete) { window.location.replace('index.php'); return; }
    personalForm.elements.full_name.value = user.full_name || '';
    personalForm.elements.email.value = user.email || '';
    personalForm.elements.phone.value = user.phone || '';
    submit.disabled = false;
  });
  personalForm.addEventListener('submit', async event => {
    event.preventDefault();
    if (!personalForm.reportValidity() || submit.disabled) return;
    submit.disabled = true;
    submit.textContent = 'Saving…';
    error.classList.add('hidden');
    try {
      const response = await fetch('../api/auth/complete-personal-onboarding.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${localStorage.getItem('flow_token') || ''}` },
        body: JSON.stringify(Object.fromEntries(new FormData(personalForm)))
      });
      const result = await response.json();
      if (!response.ok || !result.success) throw new Error(result.error || 'Unable to save your details. Please try again.');
      window.location.replace('index.php');
    } catch (failure) {
      error.textContent = failure.message || 'Unable to connect. Please try again.';
      error.classList.remove('hidden');
      submit.disabled = false;
      submit.textContent = 'Finish setup';
    }
  });
})();
</script>
