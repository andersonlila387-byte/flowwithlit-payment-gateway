<?php
/**
 * Flowwithlit API - OTP Verification Endpoint
 * Verifies 6-digit codes for Email Verification, Password Resets & 2FA Login
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../services/EmailService.php';
require_once __DIR__ . '/../../services/LoginAlertService.php';
require_once __DIR__ . '/../../services/GatewayJobQueue.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed. Use POST.'], 405);
}

$input = getJsonInput();

$email = strtolower(trim(sanitizeInput($input['email'] ?? '')));
$code  = trim(sanitizeInput($input['code'] ?? ''));
$type  = sanitizeInput($input['type'] ?? 'email_verification'); // 'email_verification', 'password_reset', '2fa'

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'error' => 'A valid email address is required.'], 400);
}

if (empty($code) || strlen($code) !== 6) {
    jsonResponse(['success' => false, 'error' => 'Please enter a valid 6-digit verification code.'], 400);
}

if (!in_array($type, ['email_verification', 'password_reset', '2fa'])) {
    jsonResponse(['success' => false, 'error' => 'Invalid verification type.'], 400);
}

$pdo = Database::getInstance();

if ($type === '2fa') {
    $pendingSession = validateSession($pdo, getBearerToken() ?: '', 15);
    if (!$pendingSession['valid'] || empty($pendingSession['is_2fa_pending']) || strcasecmp($pendingSession['user']['email'], $email) !== 0) {
        jsonResponse(['success' => false, 'error' => 'A matching pending sign-in is required.'], 401);
    }
}

$result = verifyOTPCode($pdo, $email, $code, $type);

if (!$result['success']) {
    jsonResponse(['success' => false, 'error' => $result['error']], 400);
}

// Post-verification actions
if ($type === 'email_verification') {
    $stmt = $pdo->prepare("UPDATE users SET email_verified = 1, status = 'active' WHERE email = ?");
    $stmt->execute([$email]);
    GatewayJobQueue::enqueue($pdo,'provision_receiving_account','provision:user:'.(int)$result['user_id'],['user_id'=>(int)$result['user_id'],'currency'=>'NGN']);
    $userStmt = $pdo->prepare("SELECT full_name FROM users WHERE email = ?");
    $userStmt->execute([$email]);
    $verifiedUser = $userStmt->fetch();
    $referral=$pdo->prepare("SELECT r.id,r.referrer_user_id,r.reward_amount,r.currency_code FROM referrals r WHERE r.referred_user_id=(SELECT id FROM users WHERE email=?) AND r.status='pending' LIMIT 1");$referral->execute([$email]);$ref=$referral->fetch();
    if($ref){$pdo->beginTransaction();try{$wallet=$pdo->prepare("SELECT w.id FROM wallets w WHERE w.user_id=? AND w.currency_code='NGN' AND w.status='active' LIMIT 1 FOR UPDATE");$wallet->execute([(int)$ref['referrer_user_id']]);$walletId=(int)$wallet->fetchColumn();if($walletId){$reference='REF-'.strtoupper(bin2hex(random_bytes(8)));$tx=$pdo->prepare("INSERT INTO transactions(uuid,user_id,type,status,destination_wallet_id,currency_code,amount,fee_amount,reference,idempotency_key,metadata,completed_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,UTC_TIMESTAMP())");$tx->execute([generateUUID(),(int)$ref['referrer_user_id'],'deposit','completed',$walletId,'NGN',$ref['reward_amount'],0,$reference,hash('sha256','referral|'.$ref['id']),json_encode(['source'=>'referral','referred_user_id'=>(int)$result['user_id']])]);$txId=(int)$pdo->lastInsertId();$pdo->prepare('UPDATE wallets SET available_balance=available_balance+?,updated_at=UTC_TIMESTAMP() WHERE id=?')->execute([$ref['reward_amount'],$walletId]);$pdo->prepare("UPDATE referrals SET status='qualified',rewarded_at=UTC_TIMESTAMP() WHERE id=?")->execute([(int)$ref['id']]);$pdo->prepare('INSERT INTO referral_rewards(referral_id,user_id,transaction_id,amount,currency_code) VALUES(?,?,?,?,?)')->execute([(int)$ref['id'],(int)$ref['referrer_user_id'],$txId,$ref['reward_amount'],'NGN']);$pdo->commit();}else{$pdo->rollBack();}}catch(Throwable $rewardError){if($pdo->inTransaction())$pdo->rollBack();error_log('Referral reward failed: '.$rewardError->getMessage());}}
    try {
        EmailService::send($email, 'Welcome to Flowwithlit', 'welcome', ['name' => $verifiedUser['full_name'] ?? 'there', 'url' => '../app/index.php']);
    } catch (Throwable $mailError) {
        error_log('Welcome email error: ' . $mailError->getMessage());
    }
} else if ($type === '2fa') {
    // If bearer token provided, mark current session as 2fa verified
    $token = getBearerToken();
    if ($token) {
        $tokenHash = hash('sha256', $token);
        $stmt = $pdo->prepare("UPDATE user_sessions SET is_2fa_verified = 1 WHERE token_hash = ? AND is_2fa_verified = 0");
        $stmt->execute([$tokenHash]);
        if ($stmt->rowCount() === 1) LoginAlertService::send($pdo, (int)$pendingSession['user']['id'], $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown App Client');
    }
}

jsonResponse([
    'success' => true,
    'type' => $type,
    'message' => ($type === 'email_verification') ? 'Email verified successfully! Welcome to Flowwithlit.' : (($type === '2fa') ? 'Two-Factor Authentication code verified successfully.' : 'Verification code confirmed.')
]);
