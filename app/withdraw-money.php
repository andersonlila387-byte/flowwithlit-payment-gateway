<?php
$pageTitle='Withdraw money — Flowwithlit';$activePage='withdraw';$activeNavTab='Features';
require_once __DIR__.'/includes/header.php';require_once __DIR__.'/includes/sidebar.php';
?>
<main class="flex-1 h-full overflow-y-auto px-4 sm:px-6 py-6 pb-28"><div class="max-w-2xl mx-auto">
<a href="send-money.php" class="text-sm underline">Send money to a Flow Tag</a>
<h1 class="text-2xl font-bold mt-4 mb-2">Withdraw money</h1>
<p class="text-sm text-gray-500 mb-6">Your withdrawal is reserved immediately. Requests awaiting manual processing are due within 24 hours.</p>
<form id="withdraw-form" class="bg-white dark:bg-[#0c1f36] border rounded-2xl p-5 space-y-4">
<label class="block">Wallet<select id="withdraw-wallet" class="block w-full border rounded-lg p-3 mt-1" required><option value="">Loading wallets…</option></select></label>
<label class="block">Amount<input id="withdraw-amount" type="text" inputmode="decimal" class="block w-full border rounded-lg p-3 mt-1" placeholder="0.00" required></label>
<fieldset id="withdraw-bank" class="space-y-3"><legend class="font-semibold">Bank destination</legend>
<label class="block">Bank<select id="withdraw-bank-code" class="block w-full border rounded-lg p-3 mt-1"><option value="">Loading banks…</option></select></label>
<label class="block">Account number<input id="withdraw-account" inputmode="numeric" pattern="[0-9]{10}" class="block w-full border rounded-lg p-3 mt-1" maxlength="10"></label>
<button id="withdraw-verify" type="button" class="border rounded-lg px-4 py-2 disabled:opacity-50" disabled>Verify account</button>
<p id="withdraw-verification-status" role="status" aria-live="polite" class="text-sm"></p>
<label class="block">Verified account holder<input id="withdraw-name" class="block w-full border rounded-lg p-3 mt-1 bg-gray-50 text-gray-900" readonly placeholder="Verify the account to see its registered name"></label>
<p class="text-xs text-gray-500">Confirm this is your intended recipient. Resolving a name does not prove ownership or guarantee a successful payout.</p></fieldset>
<fieldset id="withdraw-crypto" class="space-y-3" hidden><legend class="font-semibold">Crypto destination</legend>
<label class="block">Network<input id="withdraw-network" class="block w-full border rounded-lg p-3 mt-1" placeholder="e.g. TRON"></label>
<label class="block">Wallet address<input id="withdraw-address" class="block w-full border rounded-lg p-3 mt-1" maxlength="180"></label>
<label class="block">Memo or tag (if required)<input id="withdraw-memo" class="block w-full border rounded-lg p-3 mt-1" maxlength="100"></label></fieldset>
<label class="block">Withdrawal PIN<input id="withdraw-pin" type="password" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" class="block w-full border rounded-lg p-3 mt-1" required autocomplete="off"></label>
<button id="withdraw-submit" type="submit" class="bg-[#0b2545] text-white rounded-lg px-5 py-3 w-full disabled:opacity-50" disabled>Request withdrawal</button>
<p id="withdraw-status" role="status" aria-live="polite" class="text-sm"></p>
</form></div></main></div></div>
<script src="assets/withdraw-money.js?v=2"></script>
<?php require_once __DIR__.'/includes/bottom_nav.php';require_once __DIR__.'/includes/footer.php'; ?>
