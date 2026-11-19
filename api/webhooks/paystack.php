<?php
declare(strict_types=1);
/**
 * Paystack webhook: credits a user's NGN wallet when money is transferred into their
 * Dedicated Virtual Account (event "charge.success", channel "dedicated_nuban").
 *
 * Safety:
 *  - The body must carry a valid x-paystack-signature (HMAC-SHA512 with our secret key).
 *  - The transaction is re-verified with Paystack (status, amount, currency) before crediting.
 *  - DepositService matches the account number to exactly one active receiving account and is
 *    idempotent per Paystack reference, so redeliveries never double-credit.
 *
 * Set this URL in Paystack Dashboard → Settings → API Keys & Webhooks:
 *   https://YOUR-DOMAIN/api/webhooks/paystack.php
 */
require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../services/PaystackClient.php';
require_once __DIR__ . '/../../services/DepositService.php';
header('Cache-Control: no-store');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') jsonResponse(['success' => false, 'error' => 'Method not allowed.'], 405);

$raw = (string) file_get_contents('php://input');
if ($raw === '' || strlen($raw) > 1048576) jsonResponse(['success' => false, 'error' => 'Invalid payload.'], 400);

$pdo = Database::getInstance();
try {
    $env = ProviderConfig::activeEnvironment($pdo, 'paystack');
    if (!$env) throw new DomainException('Paystack is not enabled.');
    $client = new PaystackClient(new ProviderConfig($pdo, 'paystack', $env));
} catch (Throwable $e) {
    error_log('Paystack webhook: not configured (' . get_class($e) . ')');
    jsonResponse(['success' => false, 'error' => 'Not configured.'], 503); // Paystack retries later
}

if (!$client->validSignature($raw, (string) ($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? ''))) {
    jsonResponse(['success' => false, 'error' => 'Invalid signature.'], 401);
}

$event = json_decode($raw, true);
$data = is_array($event['data'] ?? null) ? $event['data'] : [];
if (($event['event'] ?? '') !== 'charge.success' || ($data['channel'] ?? '') !== 'dedicated_nuban') {
    jsonResponse(['success' => true, 'ignored' => true]); // not a dedicated-account deposit
}

$reference = (string) ($data['reference'] ?? '');
$accountNumber = (string) ($data['metadata']['receiver_account_number'] ?? $data['authorization']['receiver_bank_account_number'] ?? '');
if ($reference === '' || !preg_match('/^[0-9]{10}$/', $accountNumber)) {
    error_log('Paystack webhook: dedicated_nuban event without reference/account number');
    jsonResponse(['success' => true, 'ignored' => true]);
}

try {
    // Never trust the webhook's own figures: confirm with Paystack.
    $verified = $client->verify($reference);
    $kobo = (string) ($verified['amount'] ?? '');
    if (($verified['status'] ?? '') !== 'success' || strtoupper((string) ($verified['currency'] ?? '')) !== 'NGN' || !preg_match('/^[0-9]+$/', $kobo)
        || $kobo !== (string) ($data['amount'] ?? '')) {
        error_log('Paystack webhook: verification mismatch for ' . $reference);
        jsonResponse(['success' => true, 'ignored' => true]);
    }
    $naira = bcdiv($kobo, '100', 2);
    $result = (new DepositService($pdo))->credit('paystack', (string) ($data['id'] ?? ''), $reference, $accountNumber, 'NGN', $naira,
        ['event' => 'charge.success', 'channel' => 'dedicated_nuban', 'reference' => $reference, 'amount_kobo' => $kobo,
         'paid_at' => $verified['paid_at'] ?? null, 'sender' => $data['authorization']['sender_name'] ?? null,
         'sender_bank' => $data['authorization']['sender_bank'] ?? null]);
    jsonResponse(['success' => true, 'duplicate' => $result['duplicate'] ?? false]);
} catch (DomainException $e) {
    // Unmatched account or blocked by account limits: money arrived, so flag loudly for manual review.
    error_log('Paystack webhook: DEPOSIT NEEDS REVIEW reference=' . $reference . ' last4=' . substr($accountNumber, -4) . ' reason=' . $e->getMessage());
    jsonResponse(['success' => true, 'review' => true]); // stop retries; admins resolve it manually
} catch (ProviderRequestException $e) {
    error_log('Paystack webhook: verify failed for ' . $reference . ': ' . $e->getMessage());
    jsonResponse(['success' => false, 'error' => 'Try again.'], 503); // let Paystack retry
} catch (Throwable $e) {
    error_log('Paystack webhook: error for ' . $reference . ': ' . get_class($e));
    jsonResponse(['success' => false, 'error' => 'Try again.'], 500);
}
