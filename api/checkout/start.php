<?php
/**
 * Start a payment on a payment link or hosted checkout.
 *
 * Card and bank transfer (NGN) are collected by Paystack: this endpoint records a pending payment and
 * returns a Paystack access code. The checkout page opens Paystack's secure window with it, where the
 * customer enters card details or receives a one-time Paystack account number to transfer to.
 * The payment is credited only after Paystack confirms it (api/checkout/confirm.php or the webhook).
 */
require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../services/AccountLimits.php';
require_once __DIR__ . '/../../services/PromoService.php';
require_once __DIR__ . '/../../services/Money.php';
require_once __DIR__ . '/../../services/ApiRateLimit.php';
require_once __DIR__ . '/../../services/PaystackCheckout.php';

header('Cache-Control: no-store');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed.'], 405);
}

$input = getJsonInput();
$token = strtoupper(trim((string)($input['token'] ?? '')));
$name = trim((string)($input['customer_name'] ?? ''));
$email = strtolower(trim((string)($input['customer_email'] ?? '')));
$method = strtolower(trim((string)($input['method'] ?? 'card')));
if (!preg_match('/^[A-F0-9]{24,64}$/', $token) || $name === '' || mb_strlen($name) > 191 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'error' => 'Enter your name and a valid email address.'], 422);
}
if (!in_array($method, ['card','bank_transfer'], true)) {
    jsonResponse(['success' => false, 'error' => 'This payment method is not available yet.'], 422);
}

$pdo = Database::getInstance();
if (!ApiRateLimit::consume($pdo, 'checkout_start', hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . '|' . $token), 20, 600)) {
    jsonResponse(['success' => false, 'error' => 'Too many attempts. Please wait a few minutes and try again.'], 429);
}

$stmt = $pdo->prepare("SELECT NULL id,cs.id checkout_session_id,cs.user_id,cs.business_id,cs.amount,cs.currency_code,cs.status,cs.expires_at,cs.environment
                       FROM checkout_sessions cs WHERE cs.public_token=? LIMIT 1");
$stmt->execute([$token]);
$link = $stmt->fetch();
if (!$link) {
    $stmt = $pdo->prepare("SELECT pl.id,NULL checkout_session_id,pl.user_id,pl.business_id,pl.amount,pl.currency_code,pl.status,pl.expires_at,'live' environment FROM payment_links pl WHERE pl.public_token=? LIMIT 1");
    $stmt->execute([$token]); $link = $stmt->fetch();
}
if (!$link || $link['status'] !== 'active' || ($link['expires_at'] && strtotime($link['expires_at']) <= time())) {
    jsonResponse(['success' => false, 'error' => 'This checkout link has expired or is no longer available.'], 410);
}
if ($link['environment'] === 'test') {
    jsonResponse(['success' => false, 'error' => 'Test checkouts are simulated and never charge real money.'], 409);
}
if ($link['currency_code'] !== 'NGN') {
    jsonResponse(['success' => false, 'error' => 'Card and bank transfer payments are available for NGN only.'], 422);
}
$checkout = new PaystackCheckout($pdo);
if (!PaystackCheckout::client($pdo)) {
    jsonResponse(['success' => false, 'error' => 'Card and bank transfer payments are not available right now.'], 503);
}

$amount = $link['amount'] ?? $input['amount'] ?? null;
if (!is_numeric($amount) || (float)$amount < 100 || (float)$amount > 1000000000) {
    jsonResponse(['success' => false, 'error' => 'Enter a valid payment amount (at least NGN 100).'], 422);
}
$amount = bcadd((string)$amount, '0', 2);

$wallet = $pdo->prepare("SELECT id FROM wallets WHERE user_id=? AND currency_code=? AND status='active' ORDER BY (business_id IS NULL AND wallet_type='personal') DESC, id LIMIT 1");
$wallet->execute([(int)$link['user_id'], (string)$link['currency_code']]);
$walletId = $wallet->fetchColumn();
if (!$walletId) {
    jsonResponse(['success' => false, 'error' => 'This merchant has not enabled this payment currency yet.'], 409);
}

// A hosted checkout session holds one payment. If a previous attempt was actually paid, settle it instead
// of starting another charge; otherwise the same payment row is reused with a fresh Paystack reference.
$existing = null;
if ($link['checkout_session_id']) {
    $q = $pdo->prepare('SELECT id,status,provider,provider_reference,transaction_id FROM payments WHERE checkout_session_id=? LIMIT 1');
    $q->execute([(int)$link['checkout_session_id']]);
    $existing = $q->fetch() ?: null;
    if ($existing && $existing['status'] === 'succeeded') jsonResponse(['success' => false, 'error' => 'This checkout has already been paid.'], 409);
    if ($existing && $existing['provider'] === 'paystack' && $existing['provider_reference']) {
        try {
            if ($checkout->settle((string)$existing['provider_reference'], 'checkout_start')['status'] === 'succeeded') {
                jsonResponse(['success' => true, 'status' => 'succeeded', 'message' => 'This checkout has already been paid.']);
            }
        } catch (Throwable $e) { error_log('Checkout previous attempt check failed: ' . get_class($e)); }
    }
}

$reference = PaystackCheckout::newReference();
try {
    $pdo->beginTransaction();
    $promoClaim = null; $customerId = 0;
    $promoCode = trim((string)($input['promo_code'] ?? ''));
    if ($promoCode !== '') {
        $customer = $pdo->prepare('SELECT id FROM users WHERE email=? AND status=\'active\' LIMIT 1 FOR UPDATE'); $customer->execute([$email]); $customerId = (int)$customer->fetchColumn();
        if (!$customerId) throw new DomainException('Sign in with the account that received this promo code before applying it.');
        $promoClaim = (new PromoService($pdo))->prepareCheckoutDiscount($customerId, $promoCode, $amount);
        $amount = bcadd(Money::sub($amount, (string)$promoClaim['discount']), '0', 2);
    }
    (new AccountLimits($pdo))->incoming((int)$link['user_id'], (int)$walletId, 'NGN', $amount);

    if ($existing) {
        $transactionId = (int)$existing['transaction_id'];
        $pdo->prepare("UPDATE transactions SET status='pending',amount=?,destination_wallet_id=? WHERE id=? AND status<>'completed'")->execute([$amount, (int)$walletId, $transactionId]);
        $pdo->prepare("UPDATE payments SET customer_email=?,customer_name=?,payment_method=?,status='pending',provider='paystack',provider_reference=? WHERE id=? AND status<>'succeeded'")
            ->execute([$email, $name, $method, $reference, (int)$existing['id']]);
        $paymentId = (int)$existing['id'];
    } else {
        $txReference = 'CHK-' . strtoupper(bin2hex(random_bytes(10)));
        $pdo->prepare("INSERT INTO transactions (uuid,user_id,business_id,type,status,destination_wallet_id,currency_code,amount,fee_amount,reference,idempotency_key,provider,metadata)
                       VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)")
            ->execute([generateUUID(), (int)$link['user_id'], $link['business_id'] ? (int)$link['business_id'] : null, 'payment', 'pending', (int)$walletId, 'NGN', $amount, '0',
                $txReference, hash('sha256', 'checkout|' . $token . '|' . $reference), 'paystack',
                json_encode(['channel' => 'hosted_checkout', 'method' => $method, 'payment_link_token' => $token], JSON_UNESCAPED_SLASHES)]);
        $transactionId = (int)$pdo->lastInsertId();
        $pdo->prepare('INSERT INTO payments(payment_link_id,checkout_session_id,transaction_id,customer_email,customer_name,payment_method,status,provider,provider_reference) VALUES(?,?,?,?,?,?,?,?,?)')
            ->execute([$link['id'] ? (int)$link['id'] : null, $link['checkout_session_id'] ? (int)$link['checkout_session_id'] : null, $transactionId, $email, $name, $method, 'pending', 'paystack', $reference]);
        $paymentId = (int)$pdo->lastInsertId();
    }
    if ($promoClaim) (new PromoService($pdo))->recordCheckoutRedemption($promoClaim, $customerId, $transactionId);
    $pdo->prepare('INSERT INTO payment_status_history(payment_id,status,source,details) VALUES(?,?,?,?)')
        ->execute([$paymentId, 'pending', 'checkout_start', json_encode(['provider' => 'paystack', 'reference' => $reference, 'method' => $method])]);
    $pdo->commit();
} catch (InvalidArgumentException|DomainException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 422);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('Hosted checkout start failed: ' . $e->getMessage());
    jsonResponse(['success' => false, 'error' => 'We could not start this payment. Please try again.'], 500);
}

// Where Paystack sends the customer back if its window redirects instead of closing.
$checkoutBase = rtrim((string)(getenv('FLOW_CHECKOUT_URL') ?: ''), '/');
if ($checkoutBase === '') {
    $host = preg_replace('/[^A-Za-z0-9.:-]/', '', (string)($_SERVER['HTTP_HOST'] ?? 'localhost')) ?: 'localhost';
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $checkoutBase = $scheme . '://' . $host . rtrim(str_replace('/api/checkout/start.php', '', (string)($_SERVER['SCRIPT_NAME'] ?? '')), '/');
}
$callbackUrl = $checkoutBase . '/checkout/' . rawurlencode($token);

try {
    $session = $checkout->initialize($reference, $email, $amount, $callbackUrl, $method);
} catch (Throwable $e) {
    error_log('Paystack initialize failed for ' . $reference . ': ' . $e->getMessage());
    $pdo->prepare("UPDATE payments SET status='failed' WHERE provider='paystack' AND provider_reference=? AND status='pending'")->execute([$reference]);
    jsonResponse(['success' => false, 'error' => 'We could not reach the payment provider. Please try again in a moment.'], 502);
}

jsonResponse(['success' => true, 'status' => 'pending', 'provider' => 'paystack', 'reference' => $reference,
    'access_code' => $session['access_code'], 'authorization_url' => $session['authorization_url']]);
