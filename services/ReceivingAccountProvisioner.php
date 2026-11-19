<?php
declare(strict_types=1);
require_once __DIR__.'/OnePipeClient.php';
require_once __DIR__.'/PaystackClient.php';
require_once __DIR__.'/PrivateStorage.php';
require_once __DIR__.'/GatewayJobQueue.php';

/**
 * Opens a user's NGN receiving account (a real Nigerian bank account number).
 *
 * Provider: Paystack Dedicated Virtual Accounts when Paystack is enabled in Admin → API Connect with
 * "dedicated account numbers" switched on; otherwise OnePipe. See activeProvider().
 *
 * This is the only code path that creates receiving accounts. Account numbers are never generated
 * locally: a number is stored only if the provider returns a valid 10-20 digit account number.
 *
 * Flow:
 *  1. Lock the user's receiving-account row in a short transaction and decide:
 *     active -> return it; recent "provisioning" -> another attempt is running, stop;
 *     failed or stale "provisioning" -> retry it; none -> create it.
 *  2. Call the provider outside the transaction (no database lock held during the network call).
 *  3. Save the real account details, or mark the row failed so the next run can retry.
 */
final class ReceivingAccountProvisioner {
    /** Minutes after which a "provisioning" row is treated as abandoned (e.g. the worker crashed). */
    private const STALE_MINUTES = 15;

    /**
     * @param ?Closure $openAccount     test hook: fn(array $customer, string $requestRef): array (OnePipe response)
     * @param ?PaystackClient $paystack test hook: a Paystack client with a fake transport
     */
    public function __construct(private PDO $db, private ?Closure $openAccount = null, private ?PaystackClient $paystack = null) {}

    /** 'paystack' when Paystack is enabled with dedicated account numbers switched on, else 'onepipe'. */
    public static function activeProvider(PDO $db): string {
        $q = $db->prepare("SELECT values_json FROM admin_integration_connections WHERE provider_key='paystack' AND enabled=1 LIMIT 1");
        $q->execute();
        $values = json_decode((string) $q->fetchColumn(), true) ?: [];
        return !empty($values['account_numbers']) ? 'paystack' : 'onepipe';
    }

    public function provision(int $uid, string $currency = 'NGN'): array {
        if ($currency !== 'NGN') throw new InvalidArgumentException('Only NGN receiving accounts are currently supported.');
        $provider = $this->paystack ? 'paystack' : self::activeProvider($this->db);

        $u = $this->db->prepare("SELECT u.id,u.full_name,u.first_name,u.last_name,u.email,u.phone FROM users u WHERE u.id=? AND u.status='active' AND u.email_verified=1");
        $u->execute([$uid]);
        $user = $u->fetch();
        if (!$user) throw new DomainException('A verified, active account is required before provisioning.');
        if (!$user['phone']) throw new DomainException('A verified phone number is required before provisioning.');

        // Step 1: claim the work under a row lock.
        $this->db->beginTransaction();
        try {
            $walletId = $this->personalWallet($uid, $currency);
            // An account from either provider counts: a user keeps the number they already have.
            $q = $this->db->prepare("SELECT id,provider,status,account_name,bank_name,account_number_display,updated_at,
                                            updated_at < DATE_SUB(NOW(), INTERVAL ? MINUTE) AS stale
                                     FROM wallet_receiving_accounts WHERE wallet_id=? AND currency_code=?
                                     ORDER BY (status='active') DESC, (provider=?) DESC, id DESC LIMIT 1 FOR UPDATE");
            $q->execute([self::STALE_MINUTES, $walletId, $currency, $provider]);
            $row = $q->fetch();

            if ($row && $row['status'] === 'active') {
                $this->db->commit();
                return ['success' => true, 'duplicate' => true, 'account' => [
                    'account_name' => $row['account_name'], 'bank_name' => $row['bank_name'],
                    'account_number_display' => $row['account_number_display'], 'status' => 'active']];
            }
            if ($row && $row['provider'] !== $provider) $row = null; // an old failed row from the other provider: start fresh
            if ($row && $row['status'] === 'provisioning' && !$row['stale']) {
                $this->db->commit();
                throw new DomainException('Your receiving account is already being created. Please try again shortly.');
            }
            if ($row && $row['status'] === 'inactive') {
                $this->db->commit();
                throw new DomainException('This receiving account has been deactivated. Please contact support.');
            }
            if ($row) {
                $id = (int) $row['id'];
                $this->db->prepare("UPDATE wallet_receiving_accounts SET status='provisioning', updated_at=NOW() WHERE id=?")->execute([$id]);
            } else {
                $existing = $this->db->prepare('SELECT id FROM wallet_receiving_accounts WHERE wallet_id=? AND provider=? AND currency_code=? FOR UPDATE');
                $existing->execute([$walletId, $provider, $currency]);
                $id = (int) $existing->fetchColumn();
                if ($id) {
                    $this->db->prepare("UPDATE wallet_receiving_accounts SET status='provisioning', updated_at=NOW() WHERE id=?")->execute([$id]);
                } else {
                    $this->db->prepare("INSERT INTO wallet_receiving_accounts(uuid,wallet_id,user_id,provider,currency_code,status) VALUES(?,?,?,?,?,'provisioning')")
                        ->execute([generateUUID(), $walletId, $uid, $provider, $currency]);
                    $id = (int) $this->db->lastInsertId();
                }
            }
            $this->db->commit();
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            if ((int) ($e->errorInfo[1] ?? 0) === 1062) throw new DomainException('Your receiving account is already being created. Please try again shortly.');
            throw $e;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        }

        // Step 2: ask the provider for the real account (outside any transaction).
        $parts = preg_split('/\s+/', trim((string) $user['full_name']), 2);
        $customer = ['name' => $user['full_name'],
                     'first_name' => $user['first_name'] ?: ($parts[0] ?? $user['full_name']),
                     'last_name' => $user['last_name'] ?: ($parts[1] ?? ''),
                     'email' => $user['email'], 'phone' => $user['phone']];
        try {
            [$account, $response] = $provider === 'paystack'
                ? $this->openWithPaystack($customer)
                : $this->openWithOnePipe($customer);
            if (!preg_match('/^[0-9]{10,20}$/D', $account['account_number'])) {
                throw new RuntimeException('Provider did not return a valid account number.');
            }
        } catch (Throwable $e) {
            $this->db->prepare("UPDATE wallet_receiving_accounts SET status='failed', updated_at=NOW() WHERE id=?")->execute([$id]);
            error_log('Receiving account (' . $provider . ') failed for user ' . $uid . ': ' . $e->getMessage());
            throw $e;
        }

        // Step 3: store the real account details (number encrypted; only last 4 shown in lists).
        $last4 = substr($account['account_number'], -4);
        $this->db->prepare("UPDATE wallet_receiving_accounts SET provider_account_ref=?,account_name=?,bank_name=?,bank_code=?,account_number_encrypted=?,
                                   account_number_last4=?,account_number_hash=?,account_number_display=?,status='active',provider_payload=?,updated_at=NOW() WHERE id=?")
            ->execute([$account['account_ref'] ?: $account['account_number'], $account['account_name'], $account['bank_name'], $account['bank_code'],
                       // base64: the column is utf8mb4 text, which corrupts (or, in strict mode, rejects) raw ciphertext bytes.
                       base64_encode(PrivateStorage::encrypt($account['account_number'], 'wallet-account')), $last4, hash('sha256', $account['account_number']),
                       '••••'.$last4, json_encode($response, JSON_THROW_ON_ERROR), $id]);
        return ['success' => true, 'duplicate' => false, 'account' => [
            'account_name' => $account['account_name'], 'bank_name' => $account['bank_name'],
            'account_number' => $account['account_number'], 'status' => 'active']];
    }

    /** @return array{0: array, 1: array} [normalised account, raw provider response] */
    private function openWithOnePipe(array $customer): array {
        $ref = 'FWL-'.strtoupper(bin2hex(random_bytes(16)));
        $response = $this->openAccount
            ? ($this->openAccount)($customer, $ref)
            : (new OnePipeClient($this->db, 'auto'))->openAccount($customer, $ref);
        return [OnePipeClient::extractAccount($response), $response];
    }

    /**
     * Paystack Dedicated Virtual Account: create (or reuse) the Paystack customer, then assign them a
     * dedicated NUBAN at the configured bank. If Paystack says the customer already has one, reuse it.
     *
     * @return array{0: array, 1: array}
     */
    private function openWithPaystack(array $customer): array {
        $client = $this->paystack;
        if (!$client) {
            $env = ProviderConfig::activeEnvironment($this->db, 'paystack');
            if (!$env) throw new DomainException('Paystack is not enabled.');
            $client = new PaystackClient(new ProviderConfig($this->db, 'paystack', $env));
        }
        $bank = $this->paystackBank($client);
        $phone = $this->paystackPhone((string) $customer['phone']);
        $payCustomer = $client->customer($customer['email'], $customer['first_name'], $customer['last_name'] ?: $customer['first_name'], $phone);
        if (empty($payCustomer['customer_code'])) throw new RuntimeException('Paystack did not return a customer code.');

        try {
            $dva = $client->receivingAccount((string) $payCustomer['customer_code'], $bank);
        } catch (ProviderRequestException $e) {
            // Already assigned? Fetch the existing active account instead of failing.
            $list = !empty($payCustomer['id']) ? $client->receivingAccounts((string) $payCustomer['id']) : [];
            $dva = is_array($list) ? (array_values(array_filter($list, fn ($a) => is_array($a) && !empty($a['account_number'])))[0] ?? null) : null;
            if (!$dva) throw $e;
        }
        $account = [
            'account_ref' => (string) ($dva['id'] ?? ''),
            'account_number' => (string) ($dva['account_number'] ?? ''),
            'account_name' => (string) ($dva['account_name'] ?? $customer['name']),
            'bank_name' => (string) ($dva['bank']['name'] ?? ''),
            'bank_code' => (string) ($dva['bank']['slug'] ?? $bank),
        ];
        // 'environment' records whether this is a Paystack test (demo) or live account.
        return [$account, ['provider' => 'paystack', 'environment' => $client->environment(), 'customer_code' => $payCustomer['customer_code'], 'dedicated_account' => $dva]];
    }

    /** Bank slug from Admin → API Connect; test mode always uses Paystack's test bank. */
    private function paystackBank(PaystackClient $client): string {
        if ($client->environment() === 'test') return 'test-bank';
        $q = $this->db->prepare("SELECT values_json FROM admin_integration_connections WHERE provider_key='paystack' LIMIT 1");
        $q->execute();
        $bank = (string) ((json_decode((string) $q->fetchColumn(), true) ?: [])['preferred_bank'] ?? '');
        return preg_match('/^[a-z0-9-]{3,40}$/', $bank) ? $bank : 'wema-bank';
    }

    /** Paystack wants a Nigerian number like +2348012345678. */
    private function paystackPhone(string $phone): string {
        $digits = preg_replace('/\D+/', '', $phone);
        if (str_starts_with($digits, '0') && strlen($digits) === 11) $digits = '234' . substr($digits, 1);
        return '+' . $digits;
    }

    private function personalWallet(int $uid, string $currency): int {
        $w = $this->db->prepare("SELECT id FROM wallets WHERE user_id=? AND currency_code=? AND status='active' AND business_id IS NULL AND wallet_type='personal' ORDER BY id LIMIT 1 FOR UPDATE");
        $w->execute([$uid, $currency]);
        $walletId = (int) $w->fetchColumn();
        if ($walletId) return $walletId;
        $this->db->prepare("INSERT INTO wallets(uuid,user_id,currency_code,wallet_type,status) VALUES(?,?,?,'personal','active')")->execute([generateUUID(), $uid, $currency]);
        return (int) $this->db->lastInsertId();
    }
}
