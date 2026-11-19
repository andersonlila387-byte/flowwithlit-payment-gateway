<?php
/**
 * Called by the checkout page after Paystack's window reports success (or after Paystack redirects back).
 * Nothing from the browser is trusted: the reference is re-verified with Paystack before anything is credited.
 */
require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../services/ApiRateLimit.php';
require_once __DIR__ . '/../../services/PaystackCheckout.php';

header('Cache-Control: no-store');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') jsonResponse(['success' => false, 'error' => 'Method not allowed.'], 405);
$input = getJsonInput();
$token = strtoupper(trim((string)($input['token'] ?? '')));
$reference = strtoupper(trim((string)($input['reference'] ?? '')));
if (!preg_match('/^[A-F0-9]{24,64}$/', $token) || !preg_match('/^FWLP-[A-F0-9]{24}$/', $reference)) {
    jsonResponse(['success' => false, 'error' => 'Invalid payment reference.'], 422);
}

$pdo = Database::getInstance();
if (!ApiRateLimit::consume($pdo, 'checkout_confirm', hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . '|' . $token), 30, 600)) {
    jsonResponse(['success' => false, 'error' => 'Too many attempts. Please wait a moment.'], 429);
}

// The reference must belong to this checkout link / session.
$q = $pdo->prepare("SELECT t.reference FROM payments p JOIN transactions t ON t.id=p.transaction_id
                    LEFT JOIN payment_links pl ON pl.id=p.payment_link_id
                    LEFT JOIN checkout_sessions cs ON cs.id=p.checkout_session_id
                    WHERE p.provider='paystack' AND p.provider_reference=? AND (pl.public_token=? OR cs.public_token=?) LIMIT 1");
$q->execute([$reference, $token, $token]);
$txReference = $q->fetchColumn();
if (!$txReference) jsonResponse(['success' => false, 'error' => 'Payment not found.'], 404);

try {
    $result = (new PaystackCheckout($pdo))->settle($reference, 'checkout_confirm');
} catch (Throwable $e) {
    error_log('Checkout confirm failed for ' . $reference . ': ' . get_class($e) . ' ' . $e->getMessage());
    jsonResponse(['success' => true, 'status' => 'pending', 'reference' => $txReference]); // the webhook will settle it
}
jsonResponse(['success' => true, 'status' => $result['status'], 'reference' => $txReference]);
