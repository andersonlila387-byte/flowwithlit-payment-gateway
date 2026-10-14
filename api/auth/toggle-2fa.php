<?php
/**
 * Flowwithlit API - Toggle Two-Factor Authentication Endpoint
 * Enables or Disables 2FA Security for Web UI & Mobile App
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed. Use POST.'], 405);
}

$token = getBearerToken();
if (!$token) {
    $token = sanitizeInput($_POST['token'] ?? $_GET['token'] ?? '');
}

$pdo = Database::getInstance();
$sessionValidation = validateSession($pdo, $token, 15);

if (!$sessionValidation['valid']) {
    jsonResponse(['success' => false, 'error' => $sessionValidation['error'], 'code' => $sessionValidation['code'] ?? 'UNAUTHORIZED'], 401);
}

$userId = $sessionValidation['user']['id'];
$email  = $sessionValidation['user']['email'];

$input  = getJsonInput();
$enable = isset($input['enable']) ? (bool)$input['enable'] : null;
$code   = trim(sanitizeInput($input['code'] ?? ''));

if ($enable === null) {
    jsonResponse(['success' => false, 'error' => 'Parameter "enable" (true/false) is required.'], 400);
}

if ($enable) {
    // If enabling, verify confirmation OTP code if provided
    if (!empty($code)) {
        $verify = verifyOTPCode($pdo, $email, $code, '2fa');
        if (!$verify['success']) {
            jsonResponse(['success' => false, 'error' => $verify['error']], 400);
        }
    }

    $stmt = $pdo->prepare("UPDATE users SET two_factor_enabled = 1 WHERE id = ?");
    $stmt->execute([$userId]);

    jsonResponse([
        'success' => true,
        'two_factor_enabled' => true,
        'message' => 'Two-Factor Authentication has been successfully enabled on your account.'
    ]);
} else {
    $stmt = $pdo->prepare("UPDATE users SET two_factor_enabled = 0 WHERE id = ?");
    $stmt->execute([$userId]);

    jsonResponse([
        'success' => true,
        'two_factor_enabled' => false,
        'message' => 'Two-Factor Authentication has been disabled on your account.'
    ]);
}
