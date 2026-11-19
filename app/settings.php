<?php
/**
 * Flowwithlit Account Settings
 */
$pageTitle = 'Settings — Flowwithlit';
$activePage = 'settings';
$activeNavTab = 'Features';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="flex-1 h-full overflow-y-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 md:pb-6 flex flex-col gap-6">
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <p class="text-xs font-semibold text-gray-400 dark:text-gray-400">Account preferences</p>
      <h1 class="mt-0.5 text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Settings</h1>
      <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Manage your profile, login security, and notifications.</p>
    </div>
    <button type="button" onclick="saveSettings()" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-semibold shadow-sm transition hover:scale-105 active:scale-95">
      <i data-lucide="save" class="w-3.5 h-3.5"></i><span>Save changes</span>
    </button>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    <aside class="lg:col-span-3 bg-white dark:bg-[#0c1f36] rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs p-3">
      <nav class="grid sm:grid-cols-3 lg:grid-cols-1 gap-1" aria-label="Settings sections">
        <button type="button" data-settings-tab="profile" class="settings-tab flex items-center gap-3 w-full rounded-xl px-3 py-3 text-left text-xs font-bold bg-[#0b2545] text-white transition"><i data-lucide="user-round" class="w-4 h-4"></i><span>Profile</span></button>
        <button type="button" data-settings-tab="security" class="settings-tab flex items-center gap-3 w-full rounded-xl px-3 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-[#13304f] transition"><i data-lucide="shield-check" class="w-4 h-4"></i><span>Security</span></button>
        <button type="button" data-settings-tab="notifications" class="settings-tab flex items-center gap-3 w-full rounded-xl px-3 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-[#13304f] transition"><i data-lucide="bell-ring" class="w-4 h-4"></i><span>Notifications</span></button>
        <a href="onboarding.php?mode=business" class="flex items-center gap-3 w-full rounded-xl px-3 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-[#13304f] transition"><i data-lucide="badge-check" class="w-4 h-4"></i><span>Business verification</span></a>
      </nav>
      <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 px-3 pb-2">
        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Account status</p>
        <div class="mt-2 flex items-center gap-2 text-xs font-semibold text-emerald-700 dark:text-emerald-400"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Verified account</div>
      </div>
    </aside>

    <div class="lg:col-span-9 space-y-6">
      <section data-settings-panel="profile" class="settings-panel bg-white dark:bg-[#0c1f36] rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-gray-100 dark:border-gray-800"><h2 class="text-lg font-bold text-gray-900 dark:text-white">Personal profile</h2><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">This information appears on receipts and account notices.</p></div>
        <div class="p-5 sm:p-6 space-y-6">
          <div class="flex flex-col sm:flex-row sm:items-center gap-4"><div class="w-16 h-16 rounded-full overflow-hidden bg-[#d9e6ff] border-2 border-white dark:border-gray-700 shadow-sm"><span data-user-avatar style="font-size:22px"></span></div><div><input id="profileAvatarFile" class="sr-only" type="file" accept="image/jpeg,image/png,image/webp"><button type="button" onclick="document.getElementById('profileAvatarFile').click()" class="px-3.5 py-2 rounded-xl bg-gray-50 dark:bg-[#13304f] hover:bg-gray-100 dark:hover:bg-[#1c4268] border border-gray-200 dark:border-gray-700 text-xs font-semibold text-gray-700 dark:text-gray-100 transition">Change photo</button><p class="mt-1.5 text-[10px] text-gray-400">JPG, PNG, or WEBP. Up to 2MB.</p></div></div>
          <div class="grid sm:grid-cols-2 gap-4"><label class="block"><span class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-200">Full name</span><input id="profileFullName" class="settings-input" type="text" autocomplete="name"></label><label class="block"><span class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-200">Business name</span><input id="profileBusinessName" class="settings-input" type="text"></label><label class="block"><span class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-200">Email address</span><input id="profileEmail" class="settings-input" type="email" readonly></label><label class="block"><span class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-200">Phone number</span><input id="profilePhone" class="settings-input" type="tel" autocomplete="tel"></label></div>
        </div>
      </section>

      <section data-settings-panel="security" class="settings-panel hidden bg-white dark:bg-[#0c1f36] rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs overflow-hidden">
  <div class="p-5 sm:p-6 border-b border-gray-100 dark:border-gray-800"><h2 class="text-lg font-bold text-gray-900 dark:text-white">Login &amp; security</h2><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Control access to your account and protect outgoing funds.</p></div>
  <div class="divide-y divide-gray-100 dark:divide-gray-800">
    <div class="p-5 sm:p-6"><div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4"><div><h3 class="text-sm font-bold text-gray-900 dark:text-white">Password</h3><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Changing it signs out every other device.</p></div><button type="button" onclick="openPasswordPanel()" class="px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs font-semibold text-gray-700 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-[#13304f] transition">Change password</button></div><div id="passwordPanel" class="hidden mt-4 rounded-xl bg-gray-50 dark:bg-[#13304f] border border-gray-200 dark:border-gray-700 p-4"><div class="grid sm:grid-cols-3 gap-3"><label class="form-field"><span>Current password</span><input id="currentPassword" autocomplete="current-password" type="password"></label><label class="form-field"><span>New password</span><input id="newPassword" autocomplete="new-password" type="password" minlength="12"></label><label class="form-field"><span>Confirm password</span><input id="confirmPassword" autocomplete="new-password" type="password" minlength="12"></label></div><div class="mt-3 flex justify-end"><button type="button" onclick="savePassword()" class="px-3.5 py-2 rounded-xl bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-semibold">Update password</button></div></div></div>
    <div class="p-5 sm:p-6"><div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4"><div><h3 class="text-sm font-bold text-gray-900 dark:text-white">Two-factor authentication</h3><p id="twoFactorSummary" class="mt-1 text-xs text-gray-500 dark:text-gray-400">Use an authenticator app (Google Authenticator, Microsoft Authenticator or Authy) to get a sign-in code.</p></div><button type="button" id="twoFactorButton" onclick="toggleTwoFactor()" class="px-3.5 py-2 rounded-xl bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-semibold transition">Enable 2FA</button></div><div id="twoFactorPanel" class="hidden mt-4 rounded-xl bg-gray-50 dark:bg-[#13304f] border border-gray-200 dark:border-gray-700 p-4">
  <div id="twoFactorEnableFields">
    <ol class="space-y-4 text-xs text-gray-600 dark:text-gray-300">
      <li><strong class="block text-gray-900 dark:text-white">1. Install an authenticator app</strong>Google Authenticator, Microsoft Authenticator or Authy on your phone.</li>
      <li><strong class="block text-gray-900 dark:text-white">2. Scan this QR code with the app</strong>
        <div class="mt-3 flex flex-col sm:flex-row sm:items-center gap-4">
          <div id="twoFactorQr" class="w-44 h-44 flex-shrink-0 rounded-xl bg-white p-2 border border-gray-200 grid place-items-center text-[11px] text-gray-400" aria-label="QR code for your authenticator app">Loading…</div>
          <div class="min-w-0"><span class="block text-gray-500 dark:text-gray-400">Can't scan? Enter this setup key in the app instead:</span><code id="twoFactorManualKey" class="mt-1 block break-all font-mono text-sm font-bold text-gray-900 dark:text-white select-all"></code><button type="button" id="twoFactorCopyKey" class="mt-2 text-xs font-bold text-[#0b2545] dark:text-sky-300 hover:underline">Copy key</button></div>
        </div>
      </li>
      <li><strong class="block text-gray-900 dark:text-white">3. Enter the 6-digit code the app shows</strong>
        <label class="form-field max-w-xs mt-2"><span class="sr-only">Authenticator code</span><input id="twoFactorCode" inputmode="numeric" autocomplete="one-time-code" maxlength="6" placeholder="123456"></label>
      </li>
    </ol>
    <div class="mt-4 flex justify-end gap-2"><button type="button" onclick="cancelTwoFactorSetup()" class="px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-xs font-semibold">Cancel</button><button type="button" id="twoFactorEnableButton" onclick="confirmTwoFactor()" class="px-3.5 py-2 rounded-xl bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-semibold">Verify and enable</button></div>
  </div>
  <div id="twoFactorDisableFields" class="hidden">
    <p class="text-xs text-gray-600 dark:text-gray-300">To turn off two-factor authentication, confirm it's you.</p>
    <div class="mt-3 grid gap-3 sm:grid-cols-2">
      <label id="twoFactorPasswordField" class="form-field"><span>Current password</span><input id="twoFactorPassword" autocomplete="current-password" type="password"></label>
      <label class="form-field"><span>Code from your authenticator app</span><input id="twoFactorDisableCode" inputmode="numeric" autocomplete="one-time-code" maxlength="6" placeholder="123456"></label>
    </div>
    <div class="mt-3 flex justify-end"><button type="button" id="twoFactorDisableButton" onclick="disableTwoFactor()" class="px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold">Disable 2FA</button></div>
  </div>
</div></div>
    <div class="p-5 sm:p-6"><div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4"><div><div class="flex items-center gap-2"><h3 class="text-sm font-bold text-gray-900 dark:text-white">Withdrawal PIN</h3><span id="pinStatus" class="text-[10px] font-bold text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-900 rounded-full px-2 py-0.5">Not set</span></div><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Required before sending money, withdrawing, or swapping funds.</p></div><button type="button" onclick="openPinPanel()" id="pinActionButton" class="shrink-0 px-3.5 py-2 rounded-xl bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-semibold transition">Set PIN</button></div><div id="pinPanel" class="hidden mt-4 rounded-xl bg-gray-50 dark:bg-[#13304f] border border-gray-200 dark:border-gray-700 p-4"><div class="grid sm:grid-cols-2 gap-3"><label class="form-field"><span>New 4-digit PIN</span><input id="newWithdrawalPin" inputmode="numeric" maxlength="4" pattern="[0-9]{4}" placeholder="••••" type="password"></label><label class="form-field"><span>Confirm PIN</span><input id="confirmWithdrawalPin" inputmode="numeric" maxlength="4" pattern="[0-9]{4}" placeholder="••••" type="password"></label></div><div class="mt-3 flex items-center justify-between gap-3"><p class="text-[10px] text-gray-500 dark:text-gray-400">Do not reuse or share this code.</p><button type="button" onclick="saveWithdrawalPin()" class="px-3.5 py-2 rounded-xl bg-[#0b2545] hover:bg-[#132847] text-white text-xs font-semibold">Save PIN</button></div></div></div>
    <div class="p-5 sm:p-6"><div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4"><div><h3 class="text-sm font-bold text-gray-900 dark:text-white">Signed-in devices</h3><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Remove access from devices you no longer use.</p></div><button type="button" onclick="revokeOtherSessions()" class="px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs font-semibold text-gray-700 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-[#13304f] transition">Sign out other devices</button></div><div id="sessionsList" class="mt-4 space-y-2 text-xs text-gray-500 dark:text-gray-300"></div></div>
  </div>
</section>

<section data-settings-panel="notifications" class="settings-panel hidden bg-white dark:bg-[#0c1f36] rounded-2xl border border-gray-200/80 dark:border-gray-800 shadow-xs overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-gray-100 dark:border-gray-800"><h2 class="text-lg font-bold text-gray-900 dark:text-white">Notifications</h2><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Choose when you want to hear from us.</p></div>
        <div class="divide-y divide-gray-100 dark:divide-gray-800"><div class="p-5 sm:p-6 flex items-center justify-between gap-4"><div><h3 class="text-sm font-bold text-gray-900 dark:text-white">Payment activity</h3><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Successful payments, refunds, and failed charges.</p></div><button type="button" data-preference="payment_activity" role="switch" aria-checked="true" class="settings-switch bg-[#0b2545]" onclick="toggleSwitch(this)"><span class="translate-x-5"></span></button></div><div class="p-5 sm:p-6 flex items-center justify-between gap-4"><div><h3 class="text-sm font-bold text-gray-900 dark:text-white">Payout updates</h3><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Settlement status and transfer confirmations.</p></div><button type="button" data-preference="payout_updates" role="switch" aria-checked="true" class="settings-switch bg-[#0b2545]" onclick="toggleSwitch(this)"><span class="translate-x-5"></span></button></div><div class="p-5 sm:p-6 flex items-center justify-between gap-4"><div><h3 class="text-sm font-bold text-gray-900 dark:text-white">Product news</h3><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">New features, guides, and helpful product updates.</p></div><button type="button" data-preference="product_news" role="switch" aria-checked="false" class="settings-switch bg-gray-300 dark:bg-gray-600" onclick="toggleSwitch(this)"><span></span></button></div></div>
      </section>
    </div>
  </div>
</main>
</div>
</div>

<div id="settingsToast" class="fixed bottom-6 right-6 z-60 hidden translate-y-3 opacity-0 transition-all duration-300"><div class="bg-gray-900 text-white px-4 py-2.5 rounded-xl shadow-xl flex items-center gap-2 border border-gray-700 text-xs"><span class="w-2 h-2 rounded-full bg-emerald-400"></span><span id="settingsToastMessage">Saved</span></div></div>

<style>
  .settings-input { width: 100%; border-radius: .75rem; border: 1px solid #e5e7eb; background: #f9fafb; color: #111827; padding: .65rem .75rem; font-size: .875rem; outline: none; }
  .settings-input:focus { border-color: #0b2545; box-shadow: 0 0 0 1px #0b2545; }
  .settings-switch { width: 2.8rem; height: 1.5rem; padding: .2rem; border-radius: 9999px; transition: background-color .2s; }
  .settings-switch span { display: block; width: 1.1rem; height: 1.1rem; border-radius: 9999px; background: white; transition: transform .2s; }
  .dark .settings-input { border-color: #374151; background: #13304f; color: #f9fafb; }
</style>

<!-- QR codes are drawn in the browser; the 2FA secret is never sent to a third-party service. -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js" integrity="sha512-ZDSPMa/JM1D+7kdg2x3BsruQ6T/JpJo3jWDWkCZsP+5yVyp1KfESqLI+7RqB5k24F7p2cV7i2YHh/890y6P6Sw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="assets/settings.js?v=2"></script>

<?php
require_once __DIR__ . '/includes/bottom_nav.php';
require_once __DIR__ . '/includes/footer.php';
?>
