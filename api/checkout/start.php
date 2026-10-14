<?php
require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';

header('Cache-Control: no-store');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed.'], 405);
}

$input = getJsonInput();
$token = strtoupper(trim((string)($input['token'] ?? '')));
$name = trim((string)($input['customer_name'] ?? ''));
$email = strtolower(trim((string)($input['customer_email'] ?? '')));
$method = strtolower(trim((string)($input['method'] ?? 'bank_transfer')));
if (!preg_match('/^[A-F0-9]{24,64}$/', $token) || $name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'error' => 'Enter your name and a valid email address.'], 422);
}
if (!in_array($method, ['card','bank_transfer','crypto'], true)) jsonResponse(['success' => false, 'error' => 'Unsupported payment method.'], 422);

$pdo = Database::getInstance();
$stmt = $pdo->prepare("SELECT NULL id,cs.id checkout_session_id,cs.user_id,cs.business_id,cs.amount,cs.currency_code,cs.status,cs.expires_at
                       FROM checkout_sessions cs WHERE cs.public_token=? LIMIT 1");
$stmt->execute([$token]);
$link = $stmt->fetch();
if (!$link) {
    $stmt = $pdo->prepare("SELECT pl.id,NULL checkout_session_id,pl.user_id,pl.business_id,pl.amount,pl.currency_code,pl.status,pl.expires_at FROM payment_links pl WHERE pl.public_token=? LIMIT 1");
    $stmt->execute([$token]); $link = $stmt->fetch();
}
if (!$link || $link['status'] !== 'active' || ($link['expires_at'] && strtotime($link['expires_at']) <= time())) {
    jsonResponse(['success' => false, 'error' => 'This checkout link has expired or is no longer available.'], 410);
}

$amount = $link['amount'];
if ($amount === null) {
    $amount = $input['amount'] ?? null;
}
if (!is_numeric($amount) || (float)$amount <= 0 || (float)$amount > 1000000000) {
    jsonResponse(['success' => false, 'error' => 'Enter a valid payment amount.'], 422);
}

$wallet = $pdo->prepare("SELECT id FROM wallets WHERE user_id=? AND currency_code=? AND status='active' ORDER BY id LIMIT 1");
$wallet->execute([(int)$link['user_id'], (string)$link['currency_code']]);
$walletId = $wallet->fetchColumn();
if (!$walletId) {
    jsonResponse(['success' => false, 'error' => 'This merchant has not enabled this payment currency yet.'], 409);
}

$reference = 'CHK-' . strtoupper(bin2hex(random_bytes(10)));
try {
    $pdo->beginTransaction();
    $tx = $pdo->prepare("INSERT INTO transactions
        (uuid,user_id,business_id,type,status,destination_wallet_id,currency_code,amount,fee_amount,reference,idempotency_key,provider,metadata)
        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $tx->execute([generateUUID(), (int)$link['user_id'], $link['business_id'] ? (int)$link['business_id'] : null,
        'payment', 'pending', (int)$walletId, $link['currency_code'], (string)$amount, '0', $reference,
        hash('sha256', 'checkout|' . $token . '|' . $email . '|' . $reference), 'flowwithlit',
        json_encode(['channel' => 'hosted_checkout', 'payment_link_token' => $token], JSON_UNESCAPED_SLASHES)]);
    $transactionId = (int)$pdo->lastInsertId();
    $payment = $pdo->prepare('INSERT INTO payments(payment_link_id,checkout_session_id,transaction_id,customer_email,customer_name,payment_method,status,provider) VALUES(?,?,?,?,?,?,?,?)');
    $payment->execute([$link['id'] ? (int)$link['id'] : null, $link['checkout_session_id'] ? (int)$link['checkout_session_id'] : null, $transactionId, $email, substr($name, 0, 191), $method, 'pending', $method === 'crypto' ? 'breet' : 'flowwithlit']);
    $pdo->commit();
    jsonResponse(['success' => true, 'status' => 'pending', 'reference' => $reference,
        'message' => 'Payment request created. Complete the bank transfer using the instructions provided by the merchant.']);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('Hosted checkout start failed: ' . $e->getMessage());
    jsonResponse(['success' => false, 'error' => 'We could not start this payment. Please try again.'], 500);
}
