<?php
/**
 * Flowwithlit API - Reset Password Endpoint
 * Updates Password after OTP Verification
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed. Use POST.'], 405);
}

$input = getJsonInput();

$email       = strtolower(trim(sanitizeInput($input['email'] ?? '')));
$code        = trim(sanitizeInput($input['code'] ?? ''));
$newPassword = $input['new_password'] ?? '';

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'error' => 'A valid email address is required.'], 400);
}

if (empty($code) || strlen($code) !== 6) {
    jsonResponse(['success' => false, 'error' => 'Please enter a valid 6-digit verification code.'], 400);
}

if (empty($newPassword) || strlen($newPassword) < 8) {
    jsonResponse(['success' => false, 'error' => 'New password must be at least 8 characters long.'], 400);
}

$pdo = Database::getInstance();

$verify = verifyOTPCode($pdo, $email, $code, 'password_reset');

if (!$verify['success']) {
    jsonResponse(['success' => false, 'error' => $verify['error']], 400);
}

$newPasswordHash = hashPassword($newPassword);

$stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
$stmt->execute([$newPasswordHash, $email]);

// Invalidate all active user sessions for security
if ($verify['user_id']) {
    $stmtSession = $pdo->prepare("DELETE FROM user_sessions WHERE user_id = ?");
    $stmtSession->execute([$verify['user_id']]);
}

jsonResponse([
    'success' => true,
    'message' => 'Password reset successfully! You can now log in with your new password.'
]);
