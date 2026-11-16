<?php
declare(strict_types=1);
require_once __DIR__.'/OnePipeClient.php';
require_once __DIR__.'/PrivateStorage.php';
require_once __DIR__.'/GatewayJobQueue.php';

/**
 * Opens a user's NGN receiving account (a real Nigerian bank account number) through OnePipe.
 *
 * This is the only code path that creates receiving accounts. Account numbers are never generated
 * locally: a number is stored only if OnePipe returns a valid 10-20 digit account number.
 *
 * Flow:
 *  1. Lock the user's receiving-account row in a short transaction and decide:
 *     active -> return it; recent "provisioning" -> another attempt is running, stop;
 *     failed or stale "provisioning" -> retry it; none -> create it.
 *  2. Call OnePipe outside the transaction (no database lock held during the network call).
 *  3. Save the real account details, or mark the row failed so the next run can retry.
 */
final class ReceivingAccountProvisioner {
    /** Minutes after which a "provisioning" row is treated as abandoned (e.g. the worker crashed). */
    private const STALE_MINUTES = 15;

    /** @param ?Closure $openAccount test hook: fn(array $customer, string $requestRef): array (OnePipe response) */
    public function __construct(private PDO $db, private ?Closure $openAccount = null) {}

    public function provision(int $uid, string $currency = 'NGN'): array {
        if ($currency !== 'NGN') throw new InvalidArgumentException('Only NGN receiving accounts are currently supported.');

        $u = $this->db->prepare("SELECT id,full_name,email,phone FROM users WHERE id=? AND status='active' AND email_verified=1");
        $u->execute([$uid]);
        $user = $u->fetch();
        if (!$user) throw new DomainException('A verified, active account is required before provisioning.');
        if (!$user['phone']) throw new DomainException('A verified phone number is required before provisioning.');

        // Step 1: claim the work under a row lock.
        $this->db->beginTransaction();
        try {
            $walletId = $this->personalWallet($uid, $currency);
            $q = $this->db->prepare("SELECT id,status,account_name,bank_name,account_number_display,updated_at,
                                            updated_at < DATE_SUB(NOW(), INTERVAL ? MINUTE) AS stale
                                     FROM wallet_receiving_accounts WHERE wallet_id=? AND provider='onepipe' AND currency_code=? FOR UPDATE");
            $q->execute([self::STALE_MINUTES, $walletId, $currency]);
            $row = $q->fetch();

            if ($row && $row['status'] === 'active') {
                $this->db->commit();
                return ['success' => true, 'duplicate' => true, 'account' => [
                    'account_name' => $row['account_name'], 'bank_name' => $row['bank_name'],
                    'account_number_display' => $row['account_number_display'], 'status' => 'active']];
            }
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
                $this->db->prepare("INSERT INTO wallet_receiving_accounts(uuid,wallet_id,user_id,provider,currency_code,status) VALUES(?,?,?,?,?,'provisioning')")
                    ->execute([generateUUID(), $walletId, $uid, 'onepipe', $currency]);
                $id = (int) $this->db->lastInsertId();
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

        // Step 2: ask OnePipe for the real account (outside any transaction).
        $ref = 'FWL-'.strtoupper(bin2hex(random_bytes(16)));
        $parts = preg_split('/\s+/', trim((string) $user['full_name']), 2);
        $customer = ['name' => $user['full_name'], 'first_name' => $parts[0] ?? $user['full_name'], 'last_name' => $parts[1] ?? '',
                     'email' => $user['email'], 'phone' => $user['phone']];
        try {
            $response = $this->openAccount
                ? ($this->openAccount)($customer, $ref)
                : (new OnePipeClient($this->db, 'auto'))->openAccount($customer, $ref);
            $account = OnePipeClient::extractAccount($response);
            if (!preg_match('/^[0-9]{10,20}$/D', $account['account_number'])) {
                throw new RuntimeException('Provider did not return a valid account number.');
            }
        } catch (Throwable $e) {
            $this->db->prepare("UPDATE wallet_receiving_accounts SET status='failed', updated_at=NOW() WHERE id=?")->execute([$id]);
            throw $e;
        }

        // Step 3: store the real account details (number encrypted; only last 4 shown in lists).
        $last4 = substr($account['account_number'], -4);
        $this->db->prepare("UPDATE wallet_receiving_accounts SET provider_account_ref=?,account_name=?,bank_name=?,bank_code=?,account_number_encrypted=?,
                                   account_number_last4=?,account_number_hash=?,account_number_display=?,status='active',provider_payload=?,updated_at=NOW() WHERE id=?")
            ->execute([$account['account_ref'] ?: $account['account_number'], $account['account_name'], $account['bank_name'], $account['bank_code'],
                       PrivateStorage::encrypt($account['account_number'], 'wallet-account'), $last4, hash('sha256', $account['account_number']),
                       '••••'.$last4, json_encode($response, JSON_THROW_ON_ERROR), $id]);
        return ['success' => true, 'duplicate' => false, 'account' => [
            'account_name' => $account['account_name'], 'bank_name' => $account['bank_name'],
            'account_number' => $account['account_number'], 'status' => 'active']];
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
