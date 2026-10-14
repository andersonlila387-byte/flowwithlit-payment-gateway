<?php
/**
 * Flowwithlit API - Forgot Password Endpoint
 * Initiates Password Reset OTP
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../services/EmailService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed. Use POST.'], 405);
}

$input = getJsonInput();

$email = strtolower(trim(sanitizeInput($input['email'] ?? '')));

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'error' => 'A valid email address is required.'], 400);
}

$pdo = Database::getInstance();

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    // For security anti-enumeration, return success even if user not found, but skip generating OTP
    jsonResponse([
        'success' => true,
        'message' => 'If an account exists with this email, a 6-digit password reset code has been generated.'
    ]);
}

$otpCode = createVerificationCode($pdo, $user['id'], $email, 'password_reset', 15);
try {
    EmailService::send($email, 'Reset your Flowwithlit password', 'password-reset', ['code' => $otpCode]);
} catch (Throwable $mailError) {
    error_log('Password reset email error: ' . $mailError->getMessage());
}

jsonResponse([
    'success' => true,
    'message' => 'Password reset verification code generated.',
]);
