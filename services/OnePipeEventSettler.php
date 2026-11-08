<?php
declare(strict_types=1);
require_once __DIR__ . '/DepositService.php';
require_once __DIR__ . '/ProviderHttp.php';

/**
 * Turns one recorded OnePipe event into a wallet credit through DepositService.
 *
 * Only events that reached the inbox through the verified webhook are settled.
 * Anything incomplete or unmatched throws, so the worker keeps it for review
 * instead of guessing a payment.
 */
final class OnePipeEventSettler {
    public function __construct(private PDO $db) {}

    public function settle(string $eventId, string $rawPayload): array {
        // ProviderJson keeps every JSON number as an exact string, so amounts are never rounded.
        $payload = ProviderJson::decode($rawPayload);
        $data = is_array($payload['data'] ?? null) ? $payload['data'] : $payload;
        $transaction = is_array($data['transaction'] ?? null) ? $data['transaction'] : [];

        $account = (string) ($data['account_number'] ?? $data['accountNumber'] ?? $data['destination_account'] ?? '');
        $amount = $data['amount'] ?? $transaction['amount'] ?? null;
        $currency = strtoupper((string) ($data['currency'] ?? $data['currency_code'] ?? $transaction['currency'] ?? 'NGN'));
        $reference = (string) ($data['provider_reference'] ?? $data['transaction_ref'] ?? $transaction['transaction_ref'] ?? $eventId);

        if ($account === '' || $amount === null) {
            throw new DomainException('Unmatched provider deposit payload.');
        }
        return (new DepositService($this->db))->credit('onepipe', $eventId, $reference, $account, $currency, $amount, $payload);
    }
}
