<?php
/**
 * Flowwithlit account verification onboarding.
 * One page, two flows chosen by the account type picked at signup:
 *  - Personal: identity step (date of birth, address, ID) → review.
 *  - Business: business details → owner identity → payout → review.
 * Details already collected at signup are shown for confirmation, never re-asked.
 * Submissions go to admin review; uploads are encrypted in private storage.
 */
require_once __DIR__ . '/../services/OnboardingService.php';
$pageTitle = 'Account verification — Flowwithlit';
$activePage = 'settings';
$activeNavTab = 'Features';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="relative flex min-w-0 flex-1 flex-col overflow-hidden bg-[#f8fbff] dark:bg-[#071322]">
  <section id="onboardingWelcome" class="flex-1 overflow-y-auto px-5 py-10 sm:px-8 lg:px-12 lg:py-14">
    <div class="mx-auto max-w-6xl">
      <div class="mx-auto max-w-3xl text-center"><span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> <span id="welcomeBadge">Getting started</span></span><h1 class="mt-6 text-3xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-4xl">Welcome to Flowwithlit<span id="welcomeName"></span>.</h1><p id="welcomeText" class="mx-auto mt-3 max-w-2xl text-base leading-7 text-slate-500 dark:text-slate-400">Loading your account…</p></div>
      <div id="welcomeCards" class="mx-auto mt-10 grid max-w-5xl gap-5 lg:grid-cols-3">
        <article class="flex min-h-[310px] flex-col rounded-2xl border border-slate-200 bg-white p-7 text-center shadow-sm dark:border-slate-800 dark:bg-[#0c1f36]"><span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-[#eef4ff] text-[#0b2545] dark:bg-[#13304f] dark:text-slate-200"><i data-lucide="badge-check" class="h-8 w-8"></i></span><h2 id="activationTitle" class="mt-6 text-xl font-bold text-slate-900 dark:text-white">Verify your account</h2><p id="activationText" class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">A few quick details to unlock higher limits.</p><button id="startActivation" type="button" disabled class="mt-auto inline-flex items-center justify-center gap-2 rounded-xl bg-[#0b2545] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#132847] disabled:opacity-50">Continue <i data-lucide="arrow-right" class="h-4 w-4"></i></button></article>
        <article class="flex min-h-[310px] flex-col rounded-2xl border border-slate-200 bg-white p-7 text-center shadow-sm dark:border-slate-800 dark:bg-[#0c1f36]"><span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-[#eef4ff] text-[#0b2545] dark:bg-[#13304f] dark:text-slate-200"><i data-lucide="book-open-check" class="h-8 w-8"></i></span><h2 class="mt-6 text-xl font-bold text-slate-900 dark:text-white">Explore Flowwithlit</h2><p id="exploreText" class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Learn how collections, wallets, transfers, and payment links work together.</p><a href="./" class="mt-auto inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-[#13304f]">View dashboard <i data-lucide="layout-dashboard" class="h-4 w-4"></i></a></article>
        <article data-kind="business" class="flex min-h-[310px] flex-col rounded-2xl border border-slate-200 bg-white p-7 text-center shadow-sm dark:border-slate-800 dark:bg-[#0c1f36]"><span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-[#eef4ff] text-[#0b2545] dark:bg-[#13304f] dark:text-slate-200"><i data-lucide="code-2" class="h-8 w-8"></i></span><h2 class="mt-6 text-xl font-bold text-slate-900 dark:text-white">Build your integration</h2><p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Use test API keys to make your first payment request in a safe environment.</p><a href="developer.php" class="mt-auto inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-[#13304f]">Open developer tools <i data-lucide="external-link" class="h-4 w-4"></i></a></article>
      </div>
    </div>
  </section>
  <div id="onboardingApp" class="hidden h-full min-h-0 w-full overflow-hidden">
<section class="flex min-w-0 flex-1 min-h-0 flex-col bg-[#f8fbff] dark:bg-[#071322]">
<form id="onboardingForm" class="min-h-0 flex-1 overflow-y-auto" novalidate>
        <div class="mx-auto w-full max-w-3xl px-5 py-8 sm:px-8 lg:mx-auto lg:max-w-3xl lg:px-12 lg:py-10">
        <p id="stepLabel" class="mb-4 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400"></p>
        <div id="reviewNote" class="mb-5 hidden rounded-xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-800 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-200"><strong class="block">Changes requested</strong><span></span></div>

        <!-- Business details (business accounts only) -->
        <section class="onboarding-step space-y-5" data-kind="business" data-title="Business details">
          <div><h2 class="text-lg font-bold text-gray-900 dark:text-white">Business details</h2><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Tell us a little more about the business you named at signup.</p></div>
          <div id="stageSummary" class="hidden flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-[#0c1f36]"><div><span class="block text-xs text-slate-500">Business type</span><strong id="stageSummaryText" class="block text-sm text-slate-900 dark:text-white"></strong></div><span class="text-[11px] text-slate-400">From signup</span></div>
          <fieldset id="stageChoice" class="hidden"><legend class="mb-2 block text-[.78rem] font-semibold text-gray-700 dark:text-gray-200">What type of business do you own?</legend>
            <div class="grid gap-3 sm:grid-cols-2">
              <label class="stage-card"><input type="radio" name="businessStage" value="starter" class="sr-only peer"><span class="peer-checked:border-[#0b2545] peer-checked:bg-[#eef4ff]"><strong>Starter Business</strong><small>I'm testing my ideas with real customers, and preparing to register my company</small></span></label>
              <label class="stage-card"><input type="radio" name="businessStage" value="registered" class="sr-only peer"><span class="peer-checked:border-[#0b2545] peer-checked:bg-[#eef4ff]"><strong>Registered Business</strong><small>My business has the approval, documentation, and licences required to operate legally</small></span></label>
            </div>
          </fieldset>
          <div class="grid sm:grid-cols-2 gap-4">
            <label class="form-field"><span>Business name</span><input id="businessName" name="businessName" required maxlength="191" placeholder="Doe Ventures" type="text"><small class="field-hint" data-stage="registered">Use the name on your CAC certificate.</small></label>
            <label class="form-field"><span>Industry</span><select id="industry" name="industry" required><option value="">Select</option><?php foreach (OnboardingService::INDUSTRIES as $industry): ?><option><?= htmlspecialchars($industry, ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></label>
            <label class="form-field sm:col-span-2"><span>Website or social media <em>(optional)</em></span><input id="website" name="website" maxlength="255" placeholder="www.doeventures.com or instagram.com/doeventures" type="text" inputmode="url"></label>
            <label class="form-field sm:col-span-2" data-stage="registered"><span>CAC registration number</span><input id="registrationNumber" name="registrationNumber" required maxlength="40" placeholder="RC 1234567 or BN 1234567" type="text"></label>
          </div>
          <label class="upload-field relative" data-stage="registered"><input type="file" id="registrationDocument" accept="image/png,image/jpeg,image/webp,application/pdf" data-document-label="CAC certificate"><span class="upload-icon"><i data-lucide="file-badge" class="w-5 h-5"></i></span><span><strong>CAC registration certificate</strong><small>PDF or image, up to 10MB</small><b class="file-name">Choose file</b></span></label>
          <label class="upload-field relative" data-optional><input type="file" id="businessLogo" accept="image/png,image/jpeg,image/webp" data-document-label="business logo"><span class="upload-icon"><i data-lucide="image" class="w-5 h-5"></i></span><span><strong>Business logo <em class="font-medium not-italic text-slate-400">(optional)</em></strong><small>Shown on your checkout and payment links · PNG, JPG or WEBP</small><b class="file-name">Choose file</b></span></label>
        </section>

        <!-- Identity (everyone) -->
        <section class="onboarding-step hidden space-y-5" data-kind="all" data-title="Your identity">
          <div><h2 class="text-lg font-bold text-gray-900 dark:text-white" id="identityHeading">Verify your identity</h2><p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="identityIntro">We need to confirm who you are before raising your limits.</p></div>
          <div class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-[#0c1f36]">
            <div class="flex items-center justify-between gap-3"><h3 class="text-xs font-bold text-slate-900 dark:text-white">Details from your signup</h3><a href="settings.php" class="text-[11px] font-semibold text-[#0b2545] underline dark:text-sky-300">Change in settings</a></div>
            <dl id="signupDetails" class="mt-3 grid gap-3 sm:grid-cols-2"></dl>
          </div>
          <div class="grid sm:grid-cols-2 gap-4">
            <label class="form-field hidden" id="phoneField"><span>Phone number</span><input id="phone" name="phone" maxlength="30" placeholder="+234 801 234 5678" type="tel" autocomplete="tel"></label>
            <label class="form-field"><span>Date of birth</span><input id="dateOfBirth" name="dateOfBirth" required type="date" autocomplete="bday"></label>
            <label class="form-field sm:col-span-2"><span>Home address</span><input id="addressLine1" name="addressLine1" required maxlength="191" placeholder="12 Admiralty Way, Lekki Phase 1" type="text" autocomplete="address-line1"></label>
            <label class="form-field"><span>City</span><input id="city" name="city" required maxlength="100" placeholder="Lagos" type="text" autocomplete="address-level2"></label>
            <label class="form-field"><span>State / region</span><input id="stateRegion" name="stateRegion" required maxlength="100" placeholder="Lagos State" type="text" autocomplete="address-level1"></label>
          </div>
          <label class="upload-field relative"><input type="file" id="identityDocument" accept="image/png,image/jpeg,image/webp,application/pdf" data-document-label="ID document"><span class="upload-icon"><i data-lucide="id-card" class="w-5 h-5"></i></span><span><strong>ID document</strong><small>National ID (NIN slip), international passport, driver's licence or voter's card · PDF or image, up to 10MB</small><b class="file-name">Choose file</b></span></label>
          <label class="upload-field relative" data-kind="business"><input type="file" id="addressDocument" accept="image/png,image/jpeg,image/webp,application/pdf" data-document-label="proof of address"><span class="upload-icon"><i data-lucide="house" class="w-5 h-5"></i></span><span><strong>Proof of address</strong><small>Utility bill or bank statement from the last 3 months · PDF or image, up to 10MB</small><b class="file-name">Choose file</b></span></label>
        </section>

        <!-- Payout (business accounts only) -->
        <section class="onboarding-step hidden space-y-5" data-kind="business" data-title="Payout account">
          <div><h2 class="text-lg font-bold text-gray-900 dark:text-white">Payout account</h2><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Add the account where you want to receive payouts. You can update it later.</p></div>
          <div class="grid sm:grid-cols-2 gap-4">
            <label class="form-field"><span>Bank</span><select id="bankSelect" required><option value="">Loading banks…</option></select></label>
            <label class="form-field"><span>Account number</span><input id="accountNumber" name="accountNumber" required inputmode="numeric" minlength="10" maxlength="10" pattern="[0-9]{10}" placeholder="10-digit account number" type="text" autocomplete="off"></label>
            <label class="form-field sm:col-span-2"><span>Account name</span><input id="accountName" name="accountName" required maxlength="191" placeholder="Name on account" type="text"><small id="accountNameHint"></small></label>
          </div>
          <input type="hidden" id="bankName" name="bankName"><input type="hidden" id="bankCode" name="bankCode"><input type="hidden" id="bankVerificationToken" name="bankVerificationToken">
          <p id="payoutLookupStatus" role="status" aria-live="polite" class="hidden flex items-center gap-2 rounded-xl px-3 py-2.5 text-xs font-semibold"></p>
        </section>

        <!-- Review -->
        <section class="onboarding-step hidden space-y-5" data-kind="all" data-title="Review and submit">
          <div><h2 class="text-lg font-bold text-gray-900 dark:text-white">Review your submission</h2><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Check the essentials before sending them for review. You can go back to edit any step.</p></div>
          <div id="reviewSummary" class="grid sm:grid-cols-2 gap-4"></div>
          <label class="flex items-start gap-3 rounded-xl border border-gray-200 dark:border-gray-700 p-4 cursor-pointer"><input id="declaration" required class="mt-0.5 w-4 h-4 accent-[#0b2545]" type="checkbox"><span class="text-xs text-gray-600 dark:text-gray-300">I confirm that the information I provided is accurate and may be reviewed by Flowwithlit.</span></label>
          <div class="rounded-xl bg-gray-50 dark:bg-[#13304f] border border-gray-200 dark:border-gray-700 p-4"><div class="flex gap-3"><i data-lucide="clock-3" class="w-5 h-5 text-[#0b2545] dark:text-sky-300 shrink-0"></i><div><h3 class="text-xs font-bold text-gray-900 dark:text-white">What happens next?</h3><p id="nextStepsText" class="mt-1 text-xs text-gray-500 dark:text-gray-300"></p></div></div></div>
        </section>

        <p id="onboardingSaveStatus" role="status" aria-live="polite" class="mt-5 text-xs text-slate-500 dark:text-slate-400"></p><button id="retryOnboardingLoad" type="button" class="hidden text-sm underline">Retry loading</button><div class="mt-8 flex flex-wrap items-center justify-end gap-3"><button id="saveOnboardingDraft" type="button" class="rounded-xl border border-slate-300 px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200">Save draft</button><button id="backButton" type="button" class="hidden px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-xs font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-[#0c1f36] dark:text-gray-100 dark:hover:bg-[#13304f]">Back</button><button id="nextButton" type="button" class="inline-flex items-center gap-2 rounded-xl bg-[#0b2545] px-5 py-2.5 text-xs font-semibold text-white transition hover:bg-[#132847]">Next</button></div>
        </div>
      </form>

      <section id="submittedState" class="hidden flex-1 overflow-y-auto px-5 py-12 text-center sm:px-8 lg:px-12"><div class="mx-auto max-w-xl rounded-2xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-[#0c1f36] sm:p-12"><span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400"><i data-lucide="circle-check-big" class="w-8 h-8"></i></span><h2 id="submittedTitle" class="mt-5 text-2xl font-bold text-gray-900 dark:text-white">Submitted for review</h2><p id="submittedText" class="mx-auto mt-2 max-w-lg text-sm text-gray-500 dark:text-gray-400"></p><div class="mt-6 flex flex-wrap justify-center gap-3"><a href="./" class="rounded-xl bg-[#0b2545] px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-[#132847]">Go to dashboard</a><a href="developer.php" data-kind="business" class="rounded-xl border border-gray-200 px-4 py-2.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-100 dark:hover:bg-[#13304f]">Use test API keys</a></div></div></section>
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
  .form-field small { display:block; margin-top:.35rem; font-size:.7rem; color:#6b7280; }
  .stage-card { display:block; cursor:pointer; }
  .stage-card > span { display:block; height:100%; border:1px solid #cbd5e1; border-radius:1rem; padding:1rem; background:#fff; transition:.15s; }
  .stage-card strong { display:block; font-size:.85rem; color:#0b2545; }
  .stage-card small { display:block; margin-top:.3rem; font-size:.72rem; line-height:1.5; color:#6b7280; }
  .stage-card input:focus-visible + span { box-shadow:0 0 0 2px #0b2545; }
  .dark .stage-card > span { background:#0c1f36; border-color:#64748b; } .dark .stage-card strong { color:#f9fafb; }
  [data-hidden-by-flow] { display:none !important; }
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
