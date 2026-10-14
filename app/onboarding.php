<?php
/**
 * Flowwithlit business verification onboarding.
 * Authenticated database-backed onboarding with encrypted private uploads.
 * Submissions require review before live access can be approved.
 */
$pageTitle = 'Business verification — Flowwithlit';
$activePage = 'settings';
$activeNavTab = 'Features';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="relative flex min-w-0 flex-1 flex-col overflow-hidden bg-[#f8fbff] dark:bg-[#071322]">
  <?php require __DIR__ . '/includes/personal-onboarding.php'; ?>
  <section id="onboardingWelcome" class="flex-1 overflow-y-auto px-5 py-10 sm:px-8 lg:px-12 lg:py-14">
    <div class="mx-auto max-w-6xl">
      <div class="mx-auto max-w-3xl text-center"><span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Test mode</span><h1 class="mt-6 text-3xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-4xl">Welcome to Flowwithlit.</h1><p class="mx-auto mt-3 max-w-2xl text-base leading-7 text-slate-500 dark:text-slate-400">Your account is in Test mode. Complete a few guided tasks to activate your business and unlock live payments when you are verified.</p></div>
      <div class="mx-auto mt-10 grid max-w-5xl gap-5 lg:grid-cols-3">
        <article class="flex min-h-[310px] flex-col rounded-2xl border border-slate-200 bg-white p-7 text-center shadow-sm dark:border-slate-800 dark:bg-[#0c1f36]"><span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-[#eef4ff] text-[#0b2545] dark:bg-[#13304f] dark:text-slate-200"><i data-lucide="badge-check" class="h-8 w-8"></i></span><h2 class="mt-6 text-xl font-bold text-slate-900 dark:text-white">Activate your business</h2><p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Add the essentials for your business profile, contact details, and payouts.</p><button id="startActivation" type="button" class="mt-auto inline-flex items-center justify-center gap-2 rounded-xl bg-[#0b2545] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#132847]">Continue <i data-lucide="arrow-right" class="h-4 w-4"></i></button></article>
        <article class="flex min-h-[310px] flex-col rounded-2xl border border-slate-200 bg-white p-7 text-center shadow-sm dark:border-slate-800 dark:bg-[#0c1f36]"><span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-[#eef4ff] text-[#0b2545] dark:bg-[#13304f] dark:text-slate-200"><i data-lucide="book-open-check" class="h-8 w-8"></i></span><h2 class="mt-6 text-xl font-bold text-slate-900 dark:text-white">Explore Flowwithlit</h2><p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Learn how collections, wallets, transfers, and payment links work together.</p><a href="index.php" class="mt-auto inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-[#13304f]">View dashboard <i data-lucide="layout-dashboard" class="h-4 w-4"></i></a></article>
        <article class="flex min-h-[310px] flex-col rounded-2xl border border-slate-200 bg-white p-7 text-center shadow-sm dark:border-slate-800 dark:bg-[#0c1f36]"><span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-[#eef4ff] text-[#0b2545] dark:bg-[#13304f] dark:text-slate-200"><i data-lucide="code-2" class="h-8 w-8"></i></span><h2 class="mt-6 text-xl font-bold text-slate-900 dark:text-white">Build your integration</h2><p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Use test API keys to make your first payment request in a safe environment.</p><a href="developer.php" class="mt-auto inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-[#13304f]">Open developer tools <i data-lucide="external-link" class="h-4 w-4"></i></a></article>
      </div>
    </div>
  </section>
  <div id="onboardingApp" class="hidden h-full min-h-0 w-full overflow-hidden">
<section class="flex min-w-0 flex-1 min-h-0 flex-col bg-[#f8fbff] dark:bg-[#071322]">
<form id="onboardingForm" class="min-h-0 flex-1 overflow-y-auto">
        <div class="mx-auto w-full max-w-3xl px-5 py-8 sm:px-8 lg:mx-auto lg:max-w-3xl lg:px-12 lg:py-10">
        <section class="onboarding-step space-y-5" data-step="1" data-title="Business information">
          <div><h2 class="text-lg font-bold text-gray-900 dark:text-white">Business information</h2><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Start with the basic information we need to create your business profile.</p></div>
          <div class="grid sm:grid-cols-2 gap-4"><label class="form-field"><span>Business type</span><select id="businessType" name="businessType" required><option value="">Select</option><option>Personal business</option><option>Registered company</option></select></label><label class="form-field"><span>Business name</span><input id="businessName" name="businessName" required placeholder="e.g. Flowwithlit Technologies Ltd" type="text"></label></div>
        </section>

        <section class="onboarding-step hidden space-y-5" data-step="2" data-title="Personal information">
          <div><h2 class="text-lg font-bold text-gray-900 dark:text-white">Personal information</h2><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Just the contact details needed to reach the account owner.</p></div>
          <div class="grid sm:grid-cols-2 gap-4"><label class="form-field"><span>Full name</span><input id="ownerName" name="ownerName" required placeholder="Your full name" type="text"></label><label class="form-field"><span>Email address</span><input id="ownerEmail" name="ownerEmail" required placeholder="you@company.com" type="email"></label><label class="form-field"><span>Phone number</span><input id="phone" name="phone" required placeholder="+234 801 234 5678" type="tel"></label></div>
        </section>

        <section class="onboarding-step hidden space-y-5" data-step="3" data-title="Payout account">
          <div><h2 class="text-lg font-bold text-gray-900 dark:text-white">Payout account</h2><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Add the account where you want to receive payouts. You can update it later.</p></div>
          <div class="grid sm:grid-cols-2 gap-4"><label class="form-field"><span>Account holder name</span><input id="accountName" name="accountName" required placeholder="Name on account" type="text"></label><label class="form-field"><span>Bank name</span><select id="bankName" name="bankName" required><option value="">Select</option><option>Access Bank</option><option>GTBank</option><option>First Bank</option><option>Zenith Bank</option><option>Kuda Bank</option><option>Other</option></select></label><label class="form-field"><span>Account number</span><input id="accountNumber" name="accountNumber" required inputmode="numeric" minlength="10" maxlength="10" placeholder="10-digit account number" type="text"></label></div>
        </section>

        <!-- Review -->
        <section class="onboarding-step hidden space-y-5" data-step="4" data-title="Review and submit">
          <div><h2 class="text-lg font-bold text-gray-900 dark:text-white">Review your submission</h2><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Check the essentials before sending them for review. You can edit any step before submission.</p></div>
          <div id="reviewSummary" class="grid sm:grid-cols-2 gap-4"></div>
          <label class="flex items-start gap-3 rounded-xl border border-gray-200 dark:border-gray-700 p-4 cursor-pointer"><input id="declaration" required class="mt-0.5 w-4 h-4 accent-[#0b2545]" type="checkbox"><span class="text-xs text-gray-600 dark:text-gray-300">I confirm that the information I provided is accurate and may be reviewed before payouts are enabled.</span></label>
          <div class="rounded-xl bg-gray-50 dark:bg-[#13304f] border border-gray-200 dark:border-gray-700 p-4"><div class="flex gap-3"><i data-lucide="clock-3" class="w-5 h-5 text-[#0b2545] dark:text-sky-300 shrink-0"></i><div><h3 class="text-xs font-bold text-gray-900 dark:text-white">What happens next?</h3><p class="mt-1 text-xs text-gray-500 dark:text-gray-300">Your profile will be marked as <strong>under review</strong>. Test mode remains available, but live keys and live payouts stay locked until Flowwithlit approves your verification.</p></div></div></div>
        </section>

        <p id="onboardingSaveStatus" role="status" aria-live="polite" class="mt-5 text-xs text-slate-500 dark:text-slate-400"></p><button id="retryOnboardingLoad" type="button" class="hidden text-sm underline">Retry loading</button><div class="mt-8 flex flex-wrap items-center justify-end gap-3"><button id="saveOnboardingDraft" type="button" class="rounded-xl border border-slate-300 px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200">Save draft</button><button id="backButton" type="button" class="hidden px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-xs font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-[#0c1f36] dark:text-gray-100 dark:hover:bg-[#13304f]">Back</button><button id="nextButton" type="button" class="inline-flex items-center gap-2 rounded-xl bg-[#0b2545] px-5 py-2.5 text-xs font-semibold text-white transition hover:bg-[#132847]">Next <i data-lucide="arrow-right" class="w-4 h-4"></i></button></div>
        </div>
      </form>

      <section id="submittedState" class="hidden flex-1 overflow-y-auto px-5 py-12 text-center sm:px-8 lg:px-12"><div class="mx-auto max-w-xl rounded-2xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-[#0c1f36] sm:p-12"><span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400"><i data-lucide="circle-check-big" class="w-8 h-8"></i></span><h2 class="mt-5 text-2xl font-bold text-gray-900 dark:text-white">Submitted for review</h2><p class="mx-auto mt-2 max-w-lg text-sm text-gray-500 dark:text-gray-400">Your business verification is now under review. You can continue using test tools, but Live mode and live keys will remain locked until your verification is approved.</p><div class="mt-6 flex flex-wrap justify-center gap-3"><a href="index.php" class="rounded-xl bg-[#0b2545] px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-[#132847]">Go to dashboard</a><a href="developer.php" class="rounded-xl border border-gray-200 px-4 py-2.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-100 dark:hover:bg-[#13304f]">Use test API keys</a></div></div></section>
    </section>
    </div>
</main>
</div>
</div>

<style>
  @media (max-width:767px) {
    #onboardingForm, #personalOnboarding, #onboardingWelcome, #submittedState {
      padding-bottom:calc(7rem + env(safe-area-inset-bottom, 0px));
      scroll-padding-bottom:calc(7rem + env(safe-area-inset-bottom, 0px));
      -webkit-overflow-scrolling:touch;
    }
    #onboardingApp { height:100%; min-height:0; }
  }
  .description-field, .logo-field { display:flex; flex-direction:column; min-width:0; }
  .logo-title { display:block; margin-bottom:.4rem; font-size:.78rem; font-weight:600; color:#374151; }
  .logo-title em { font-style:normal; color:#9ca3af; font-weight:500; }
  .description-field textarea, .logo-field .logo-upload { flex:1; min-height:112px; width:100%; box-sizing:border-box; }
  .description-field textarea { display:block; }
  .logo-field .logo-upload { align-items:center; border-style:solid; border-radius:.75rem; background:#f9fafb; padding:.68rem .75rem; }
  .logo-upload-copy { min-width:0; overflow-wrap:anywhere; }
  .logo-field .file-name { margin-top:0; }
  .logo-upload:focus-within { border-color:#0b2545; box-shadow:0 0 0 1px #0b2545; }
  .dark .logo-title { color:#e5e7eb; }
  .dark .logo-field .logo-upload { background:#13304f; }
  .form-field span { display:block; margin-bottom:.4rem; font-size:.78rem; font-weight:600; color:#374151; }
  .form-field em { font-style:normal; color:#9ca3af; font-weight:500; }
  .form-field input, .form-field select, .form-field textarea { width:100%; border:1px solid #94a3b8; border-radius:.75rem; background:#f9fafb; color:#111827; padding:.68rem .75rem; font-size:.84rem; outline:none; }
  .form-field input:focus, .form-field select:focus, .form-field textarea:focus { border-color:#0b2545; box-shadow:0 0 0 1px #0b2545; }
  .upload-field { display:flex; gap:.75rem; align-items:flex-start; border:1px dashed #94a3b8; border-radius:1rem; padding:1rem; cursor:pointer; transition:.2s; }
  .upload-field:hover { border-color:#0b2545; background:#f8fbff; }
  .upload-field input { position:absolute; width:1px; height:1px; overflow:hidden; clip:rect(0,0,0,0); }
  .upload-icon { width:2.4rem; height:2.4rem; flex:none; border-radius:.75rem; display:flex; align-items:center; justify-content:center; background:#eef4ff; color:#0b2545; }
  .upload-field strong, .upload-field small, .upload-field b { display:block; }
  .upload-field strong { font-size:.8rem; color:#111827; }
  .upload-field small { margin-top:.15rem; color:#6b7280; font-size:.7rem; }
  .upload-field b { margin-top:.4rem; color:#0b2545; font-size:.72rem; }
  .dark .form-field span { color:#e5e7eb; } .dark .form-field input, .dark .form-field select, .dark .form-field textarea { background:#13304f; border-color:#64748b; color:#f9fafb; }
  .dark .upload-field { border-color:#64748b; } .dark .upload-field:hover { background:#13304f; border-color:#7dd3fc; } .dark .upload-icon { background:#13304f; color:#7dd3fc; } .dark .upload-field strong { color:#f9fafb; } .dark .upload-field small { color:#cbd5e1; } .dark .upload-field b { color:#7dd3fc; }
</style>

<script src="includes/business-onboarding.js"></script>

<?php
require_once __DIR__ . '/includes/bottom_nav.php';
require_once __DIR__ . '/includes/footer.php';
?>
