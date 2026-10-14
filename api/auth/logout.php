<?php
/**
 * Flowwithlit API - Logout Endpoint
 * Invalidates Session Token
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';

$token = getBearerToken();
if (!$token) {
    $input = getJsonInput();
    $token = sanitizeInput($input['token'] ?? $_GET['token'] ?? '');
}

$pdo = Database::getInstance();
invalidateSession($pdo, $token);

jsonResponse([
    'success' => true,
    'message' => 'Logged out successfully.'
]);
