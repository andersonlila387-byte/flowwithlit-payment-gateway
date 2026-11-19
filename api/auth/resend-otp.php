<?php
/**
 * Flowwithlit API - Resend OTP Endpoint
 * Generates and returns a fresh 6-digit OTP code for Email Verification, 2FA, or Password Reset
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed. Use POST.'], 405);
}

$input = getJsonInput();

$email = strtolower(trim(sanitizeInput($input['email'] ?? '')));
$type  = sanitizeInput($input['type'] ?? 'email_verification');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'error' => 'A valid email address is required.'], 400);
}

// Sign-in 2FA uses the authenticator app only; emailing a "2fa" code would let email replace the app.
if ($type === '2fa') {
    jsonResponse(['success' => false, 'error' => 'Use the 6-digit code from your authenticator app.'], 400);
}
if (!in_array($type, ['email_verification', 'password_reset'], true)) {
    jsonResponse(['success' => false, 'error' => 'Invalid verification type.'], 400);
}

$pdo = Database::getInstance();

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    jsonResponse(['success' => false, 'error' => 'No account found with this email address.'], 444);
}

$otpCode = createVerificationCode($pdo, $user['id'], $email, $type, 15);

jsonResponse([
    'success' => true,
    'message' => 'A fresh 6-digit verification code has been generated.',
]);
