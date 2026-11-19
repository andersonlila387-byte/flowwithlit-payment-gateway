<?php
declare(strict_types=1);
require_once __DIR__.'/Money.php';
require_once __DIR__.'/LedgerService.php';
require_once __DIR__.'/PaystackClient.php';

/**
 * Collects payment-link / hosted-checkout payments through Paystack.
 *
 * The customer pays inside Paystack's secure checkout (card details or Paystack's one-time transfer account
 * number never touch Flowwithlit). A payment is credited to the merchant only after Flowwithlit itself
 * verifies the reference with Paystack (status, amount, currency), so the browser, the callback and the
 * webhook can all trigger settle() safely: it is idempotent per payment.
 */
final class PaystackCheckout {
    public function __construct(private PDO $db, private ?PaystackClient $client = null) {}

    /** Paystack client for the environment enabled in Admin → API Connect, or null when Paystack is off. */
    public static function client(PDO $db): ?PaystackClient {
        $env = ProviderConfig::activeEnvironment($db, 'paystack');
        if (!$env) return null;
        return new PaystackClient(new ProviderConfig($db, 'paystack', $env));
    }

    private function paystack(): PaystackClient {
        $this->client ??= self::client($this->db);
        if (!$this->client) throw new DomainException('Card and bank transfer payments are not available right now.');
        return $this->client;
    }

    public static function newReference(): string { return 'FWLP-'.strtoupper(bin2hex(random_bytes(12))); }

    /** Ask Paystack for a checkout. $method is 'card' or 'bank_transfer'. Returns access_code + authorization_url. */
    public function initialize(string $reference, string $email, string $amount, string $callbackUrl, string $method): array {
        $data = $this->paystack()->initialize($reference, $email, bcadd($amount, '0', 2), $callbackUrl, $method);
        if (empty($data['access_code']) || empty($data['authorization_url'])) throw new RuntimeException('Paystack did not return a checkout.');
        return ['access_code' => (string) $data['access_code'], 'authorization_url' => (string) $data['authorization_url']];
    }

    /**
     * Verify $reference with Paystack and, when paid, credit the merchant exactly once.
     * Returns ['status' => 'succeeded'|'pending'|'failed'|'unknown', 'duplicate' => bool].
     */
    public function settle(string $reference, string $source): array {
        if (!preg_match('/^FWLP-[A-F0-9]{24}$/D', $reference)) return ['status' => 'unknown', 'duplicate' => false];
        $q = $this->db->prepare("SELECT p.id,p.status FROM payments p WHERE p.provider='paystack' AND p.provider_reference=? LIMIT 1");
        $q->execute([$reference]);
        $known = $q->fetch();
        if (!$known) return ['status' => 'unknown', 'duplicate' => false];
        if ($known['status'] === 'succeeded') return ['status' => 'succeeded', 'duplicate' => true];

        $verified = $this->paystack()->verify($reference); // network call outside any DB transaction
        $state = (string) ($verified['status'] ?? '');
        if ($state !== 'success') {
            // "abandoned" only means the customer closed Paystack's window; they can still retry, so stay pending.
            if (in_array($state, ['failed', 'reversed'], true)) {
                $this->db->prepare("UPDATE payments SET status='failed',last_event_at=UTC_TIMESTAMP(6) WHERE id=? AND status IN ('pending','processing')")->execute([(int) $known['id']]);
                return ['status' => 'failed', 'duplicate' => false];
            }
            return ['status' => 'pending', 'duplicate' => false];
        }

        $this->db->beginTransaction();
        try {
            $q = $this->db->prepare("SELECT p.id,p.status,p.transaction_id,p.checkout_session_id,t.amount,t.currency_code,t.destination_wallet_id,t.status tx_status
                                     FROM payments p JOIN transactions t ON t.id=p.transaction_id
                                     WHERE p.provider='paystack' AND p.provider_reference=? FOR UPDATE");
            $q->execute([$reference]);
            $p = $q->fetch();
            if (!$p) throw new LogicException('Payment disappeared.');
            if ($p['status'] === 'succeeded') { $this->db->commit(); return ['status' => 'succeeded', 'duplicate' => true]; }

            $expectedKobo = bcmul(bcadd((string) $p['amount'], '0', 2), '100', 0);
            if (strtoupper((string) ($verified['currency'] ?? '')) !== 'NGN' || (string) $p['currency_code'] !== 'NGN'
                || (string) ($verified['amount'] ?? '') !== $expectedKobo || strcasecmp((string) ($verified['reference'] ?? ''), $reference) !== 0) {
                // Money moved but does not match what we asked for: never auto-credit, flag for review.
                $this->history((int) $p['id'], 'review', $source, ['reason' => 'amount_or_currency_mismatch', 'paid_kobo' => (string) ($verified['amount'] ?? ''), 'expected_kobo' => $expectedKobo]);
                $this->db->commit();
                error_log('Paystack checkout NEEDS REVIEW: '.$reference.' amount/currency mismatch.');
                return ['status' => 'pending', 'duplicate' => false];
            }

            $amount = (string) $p['amount'];
            $walletId = (int) $p['destination_wallet_id'];
            $txId = (int) $p['transaction_id'];
            $ledger = new LedgerService($this->db);
            $ledger->post($txId, 'checkout:paystack:'.$reference, 'NGN', [
                [$ledger->account('settlement:paystack:NGN', 'asset', 'NGN'), 'debit', $amount],
                [$ledger->wallet($walletId, 'NGN'), 'credit', $amount],
            ]);
            $this->db->prepare('UPDATE wallets SET available_balance=available_balance+?,updated_at=UTC_TIMESTAMP() WHERE id=?')->execute([$amount, $walletId]);
            $this->db->prepare("UPDATE transactions SET status='completed',provider='paystack',provider_reference=?,completed_at=UTC_TIMESTAMP() WHERE id=?")->execute([$reference, $txId]);
            $this->db->prepare("UPDATE payments SET status='succeeded',last_event_at=UTC_TIMESTAMP(6) WHERE id=?")->execute([(int) $p['id']]);
            if ($p['checkout_session_id']) $this->db->prepare("UPDATE checkout_sessions SET status='completed' WHERE id=? AND status='active'")->execute([(int) $p['checkout_session_id']]);
            $this->history((int) $p['id'], 'succeeded', $source, ['channel' => $verified['channel'] ?? null, 'paid_at' => $verified['paid_at'] ?? null,
                'paystack_fee_kobo' => $verified['fees'] ?? null, 'late' => in_array($p['status'], ['expired', 'failed'], true)]);
            $this->db->commit();
            return ['status' => 'succeeded', 'duplicate' => false];
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        }
    }

    private function history(int $paymentId, string $status, string $source, array $details): void {
        $this->db->prepare('INSERT INTO payment_status_history(payment_id,status,source,details) VALUES(?,?,?,?)')
            ->execute([$paymentId, $status, substr($source, 0, 50), json_encode($details, JSON_UNESCAPED_SLASHES)]);
    }
}
