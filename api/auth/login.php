<?php
/**
 * Flowwithlit API - Login Endpoint
 * Powers Web UI and Mobile Apps with BCrypt, Rate Limiting, 2FA & 15-min Inactivity Sessions
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../services/LoginAlertService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed. Use POST.'], 405);
}

$input = getJsonInput();

$email    = strtolower(trim(sanitizeInput($input['email'] ?? '')));
$password = $input['password'] ?? '';

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'error' => 'A valid email address is required.'], 400);
}

if (empty($password)) {
    jsonResponse(['success' => false, 'error' => 'Password is required.'], 400);
}

$pdo = Database::getInstance();
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

// 1. Check Rate Limit (5 attempts / 15 mins)
$rateLimit = checkRateLimit($pdo, $ipAddress, $email);
if (!$rateLimit['allowed']) {
    jsonResponse([
        'success' => false,
        'error'   => $rateLimit['message'],
        'remaining_seconds' => $rateLimit['remaining_seconds'],
        'code'    => 'RATE_LIMITED'
    ], 429);
}

// 2. Lookup User
$stmt = $pdo->prepare("SELECT id, uuid, full_name, email, phone, password_hash, flow_tag, account_type, avatar_url, email_verified, two_factor_enabled, status, onboarding_completed_at FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !verifyPassword($password, $user['password_hash'])) {
    recordFailedAttempt($pdo, $ipAddress, $email);
    jsonResponse(['success' => false, 'error' => 'Invalid email or password.'], 401);
}

if ($user['status'] === 'suspended') {
    jsonResponse(['success' => false, 'error' => 'Your Flowwithlit account has been suspended. Please contact compliance.'], 403);
}

// Reset rate limit on successful credentials check
resetRateLimit($pdo, $ipAddress, $email);

$deviceInfo = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown App Client';

// 3. Check 2FA requirement
$requires2fa = ($user['two_factor_enabled'] == 1);
$is2faVerified = $requires2fa ? 0 : 1;

// Create Session Token
$session = createSession($pdo, $user['id'], $deviceInfo, $ipAddress, $is2faVerified);

$responsePayload = [
    'success'      => true,
    'token'        => $session['token'],
    'expires_at'   => $session['expires_at'],
    'requires_2fa' => $requires2fa,
    'user' => [
        'uuid'               => $user['uuid'],
        'full_name'          => $user['full_name'],
        'email'             => $user['email'],
        'phone'              => $user['phone'],
        'flow_tag'           => $user['flow_tag'],
        'account_type'       => $user['account_type'],
        'avatar_url'         => $user['avatar_url'],
        'email_verified'     => (bool)$user['email_verified'],
        'two_factor_enabled' => (bool)$user['two_factor_enabled'],
        'status'             => $user['status'],
        'onboarding_complete' => !empty($user['onboarding_completed_at'])
    ]
];

if ($requires2fa) {
    $responsePayload['message'] = 'Enter the current code from your authenticator app.';
    $responsePayload['two_factor_method'] = 'totp';
} else {
    $responsePayload['message'] = 'Login successful!';
    LoginAlertService::send($pdo, (int)$user['id'], $deviceInfo);
}

jsonResponse($responsePayload, 200);
