<?php
declare(strict_types=1);
require_once __DIR__.'/PrivateStorage.php';

/**
 * Reads the full account number of a wallet_receiving_accounts row.
 *
 * The number is stored encrypted. Rows written by older code can fail to decrypt (raw ciphertext
 * saved in a text column, or saved before PrivateStorage understood base64). For those rows the number
 * is recovered from the provider's own response (provider_payload), but only when it matches the
 * stored account_number_hash, and the row is then re-encrypted so the next read is direct.
 *
 * $row needs: id, account_number_encrypted, account_number_hash, provider_payload.
 */
final class ReceivingAccountNumber {
    public static function reveal(PDO $db, array $row): ?string {
        $encrypted = (string) ($row['account_number_encrypted'] ?? '');
        if ($encrypted !== '') {
            try {
                return PrivateStorage::decrypt($encrypted, 'wallet-account');
            } catch (Throwable $e) {
                error_log('Receiving account '.(int) ($row['id'] ?? 0).' decrypt failed: '.$e->getMessage());
            }
        }

        $hash = (string) ($row['account_number_hash'] ?? '');
        $payload = json_decode((string) ($row['provider_payload'] ?? ''), true);
        if ($hash === '' || !is_array($payload)) return null;

        foreach (self::candidates($payload) as $number) {
            if (!hash_equals($hash, hash('sha256', $number))) continue;
            if (!empty($row['id'])) {
                try {
                    $db->prepare('UPDATE wallet_receiving_accounts SET account_number_encrypted=? WHERE id=?')
                        ->execute([base64_encode(PrivateStorage::encrypt($number, 'wallet-account')), (int) $row['id']]);
                } catch (Throwable $e) {
                    error_log('Receiving account '.(int) $row['id'].' re-encrypt failed: '.$e->getMessage());
                }
            }
            return $number;
        }
        return null;
    }

    /** Every 10-20 digit value stored under an account-number key anywhere in the provider response. */
    private static function candidates(array $data): array {
        $found = [];
        array_walk_recursive($data, function ($value, $key) use (&$found) {
            $name = strtolower(str_replace(['_', '-'], '', (string) $key));
            if (in_array($name, ['accountnumber', 'accountno', 'nuban'], true)
                && (is_string($value) || is_int($value)) && preg_match('/^[0-9]{10,20}$/D', (string) $value)) {
                $found[] = (string) $value;
            }
        });
        return array_values(array_unique($found));
    }
}
