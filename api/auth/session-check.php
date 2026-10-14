<?php
/**
 * Flowwithlit API - Session Check Endpoint
 * Validates token & enforces 15-minute automatic inactivity logout
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';

$token = getBearerToken();
if (!$token) {
    $input = getJsonInput();
    $token = sanitizeInput($input['token'] ?? $_GET['token'] ?? '');
}

$pdo = Database::getInstance();
$sessionValidation = validateSession($pdo, $token, 15);

if (!$sessionValidation['valid']) {
    jsonResponse([
        'success' => false,
        'error'   => $sessionValidation['error'],
        'code'    => $sessionValidation['code'] ?? 'UNAUTHORIZED'
    ], 401);
}

if (!empty($sessionValidation['is_2fa_pending']) && $sessionValidation['is_2fa_pending'] === true) {
    jsonResponse([
        'success'        => true,
        'is_2fa_pending' => true,
        'user'           => $sessionValidation['user'],
        'message'        => 'Session active but pending 2FA code verification.'
    ]);
}

jsonResponse([
    'success'        => true,
    'is_2fa_pending' => false,
    'user'           => $sessionValidation['user'],
    'message'        => 'Session active and valid.'
]);
