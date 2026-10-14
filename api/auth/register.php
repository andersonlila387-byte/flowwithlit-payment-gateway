<?php
/**
 * Flowwithlit API - Register Endpoint
 * Powers Web UI and Mobile Apps (Returns JSON + CORS)
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../services/EmailService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed. Use POST.'], 405);
}

$input = getJsonInput();

$fullName    = sanitizeInput($input['full_name'] ?? '');
$email       = strtolower(trim(sanitizeInput($input['email'] ?? '')));
$phone       = sanitizeInput($input['phone'] ?? '');
$password    = $input['password'] ?? '';
$accountType = sanitizeInput($input['account_type'] ?? 'individual');
$referralCode = strtoupper(trim(sanitizeInput($input['referral_code'] ?? '')));

// Validation
if (empty($fullName)) {
    jsonResponse(['success' => false, 'error' => 'Full name is required.'], 400);
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'error' => 'A valid email address is required.'], 400);
}

if (empty($password) || strlen($password) < 8) {
    jsonResponse(['success' => false, 'error' => 'Password must be at least 8 characters long.'], 400);
}

if (!in_array($accountType, ['individual', 'business'])) {
    $accountType = 'individual';
}

$pdo = Database::getInstance();

// Check if Email already exists
$stmtEmail = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmtEmail->execute([$email]);
if ($stmtEmail->fetch()) {
    jsonResponse(['success' => false, 'error' => 'An account with this email address already exists.'], 409);
}

// Check Phone if provided
if (!empty($phone)) {
    $stmtPhone = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
    $stmtPhone->execute([$phone]);
    if ($stmtPhone->fetch()) {
        jsonResponse(['success' => false, 'error' => 'An account with this phone number already exists.'], 409);
    }
} else {
    $phone = null;
}

$uuid = generateUUID();
$passwordHash = hashPassword($password);
$flowTag = generateFlowTag($fullName, $pdo);

try {
    $pdo->beginTransaction();

    // Create User
    $stmt = $pdo->prepare("INSERT INTO users (uuid, account_type, full_name, email, phone, password_hash, flow_tag, auth_provider, email_verified, status) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, 'email', 0, 'pending_verification')");
    $stmt->execute([$uuid, $accountType, $fullName, $email, $phone, $passwordHash, $flowTag]);
    $userId = $pdo->lastInsertId();
    $pdo->prepare('INSERT INTO referral_codes(user_id,code) VALUES(?,?)')->execute([$userId,'FWL'.strtoupper(substr(hash('sha256',$userId.'|'.bin2hex(random_bytes(8))),0,10))]);
    if ($referralCode !== '') {
        $referrer = $pdo->prepare('SELECT user_id FROM referral_codes WHERE code=? LIMIT 1'); $referrer->execute([$referralCode]); $referrerId = (int)$referrer->fetchColumn();
        if ($referrerId && $referrerId !== (int)$userId) $pdo->prepare("INSERT INTO referrals(referrer_user_id,referred_user_id,referral_code) VALUES(?,?,?)")->execute([$referrerId,$userId,$referralCode]);
    }

    // Generate 6-Digit Email Verification OTP Code
    $otpCode = createVerificationCode($pdo, $userId, $email, 'email_verification', 15);

    // Create Session Token
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $deviceInfo = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown App Client';
    $session = createSession($pdo, $userId, $deviceInfo, $ipAddress, 1);

    $pdo->commit();

    try {
        EmailService::send($email, 'Verify your Flowwithlit email', 'verify-email', ['name' => $fullName, 'code' => $otpCode]);
    } catch (Throwable $mailError) {
        error_log('Verification email error: ' . $mailError->getMessage());
    }

    jsonResponse([
        'success' => true,
        'message' => 'Registration successful! Verification code sent to email.',
        'token' => $session['token'],
        'expires_at' => $session['expires_at'],
        'user' => [
            'uuid' => $uuid,
            'full_name' => $fullName,
            'email' => $email,
            'flow_tag' => $flowTag,
            'account_type' => $accountType,
            'email_verified' => false,
            'two_factor_enabled' => false
        ]
    ], 201);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Registration Exception: " . $e->getMessage());
    jsonResponse(['success' => false, 'error' => 'An internal error occurred during registration.'], 500);
}
