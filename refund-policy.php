<?php
$pageTitle = 'Refund & Reversal Policy — Flowwithlit';
$pageDesc  = 'Clear rules regarding failed transfers, reversals, and blockchain swap finality.';
$activePage = 'refund-policy';
require_once __DIR__ . '/includes/header.php';
?>

  <!-- HERO SECTION -->
  <section class="hero-bg text-white pt-36 pb-20 px-6 text-center">
    <div class="max-w-4xl mx-auto">
      <div class="reveal badge-pill bg-white/10 text-white border-white/20 mb-4 inline-flex">
        <span class="badge-dot bg-white"></span> Payment Terms
      </div>
      <h1 class="reveal text-3xl sm:text-5xl font-bold tracking-tight">Refund &amp; Reversal Policy</h1>
      <p class="reveal mt-4 text-white/80 text-sm sm:text-base max-w-2xl mx-auto">
        Last Updated: September 17, 2026. Clear rules regarding failed transfers, reversals, and blockchain swap finality.
      </p>
    </div>
  </section>

  <!-- CONTENT SECTION -->
  <section class="py-16 sm:py-24 px-6 bg-white">
    <div class="max-w-4xl mx-auto">
      
      <!-- Plain English Summary Box -->
      <div class="reveal bg-mint border border-brand-500/10 rounded-2xl p-6 sm:p-8 mb-12 space-y-3">
        <div class="text-xs font-bold uppercase tracking-wider text-blue-600">Plain English Summary</div>
        <h2 class="text-lg font-bold text-ink">How Refunds Work on Flowwithlit</h2>
        <p class="text-ink/80 text-xs sm:text-sm leading-relaxed text-justify">
          If a bank transfer fails, Flowwithlit automatically credits 100% of your funds back to your wallet instantly. Cryptocurrency transactions on the blockchain are final and non-reversible by nature. For accidental duplicate payments or merchant settlement issues, our team investigates and resolves requests within 24 hours.
        </p>
      </div>

      <!-- Legal Text Clauses -->
      <div class="space-y-10 text-justify text-ink/85 text-xs sm:text-sm leading-relaxed">
        
        <div>
          <h2 class="text-xl font-bold text-ink mb-3">1. Automated Bank Transfer Failures</h2>
          <p>
            When initiating a payout to a local bank account, if the destination bank network rejects the credit (e.g., account invalid, name mismatch, bank network downtime), Flowwithlit's automated engine detects the failure instantly and credits 100% of the principal transfer amount back to your Flowwithlit balance within seconds.
          </p>
        </div>

        <div>
          <h2 class="text-xl font-bold text-ink mb-3">2. Cryptocurrency Transaction Finality</h2>
          <p>
            Cryptocurrency networks (including Bitcoin, USDT TRC20/ERC20/Solana, Ethereum, and Solana) operate as immutable decentralized ledgers. Once a currency swap or crypto transfer is broadcasted and confirmed on the blockchain, the transaction is irreversible. Users are solely responsible for ensuring destination wallet addresses are accurate prior to confirmation.
          </p>
        </div>

        <div>
          <h2 class="text-xl font-bold text-ink mb-3">3. Duplicate or Erroneous Transactions</h2>
          <p>
            If you accidentally execute a duplicate payment or transfer due to a system glitch, you must notify Flowwithlit support within 48 hours. Upon verification of the duplicate transaction log, Flowwithlit will process a reversal for the duplicate amount.
          </p>
        </div>

        <div>
          <h2 class="text-xl font-bold text-ink mb-3">4. Merchant Settlement &amp; Chargebacks</h2>
          <p>
            Merchants utilizing Flowwithlit to accept payments from customers are responsible for their own customer service and product delivery policies. In the event of a customer dispute or chargeback claim, Flowwithlit provides cryptographic transaction logs to assist in fair resolution.
          </p>
        </div>

        <div>
          <h2 class="text-xl font-bold text-ink mb-3">5. Processing Fees</h2>
          <p>
            Standard platform service fees associated with successfully completed transactions are non-refundable. Network blockchain gas fees paid to external network miners/validators cannot be refunded under any circumstances.
          </p>
        </div>

        <div>
          <h2 class="text-xl font-bold text-ink mb-3">6. Submitting a Refund Claim</h2>
          <p>
            To submit a manual refund or transaction review claim, email <strong>support@flowwithlit.com</strong> with your Transaction ID, user @handle, and supporting bank statement/receipt.
          </p>
        </div>

      </div>
    </div>
  </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
