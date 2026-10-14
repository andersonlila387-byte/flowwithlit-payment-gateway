<?php
/**
 * Flowwithlit API - Google Authentication Endpoint
 * Single-Tap Google Sign-Up and Sign-In for Web UI and Mobile Apps
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../services/LoginAlertService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed. Use POST.'], 405);
}

$input = getJsonInput();

$googleId  = sanitizeInput($input['google_id'] ?? '');
$email     = strtolower(trim(sanitizeInput($input['email'] ?? '')));
$fullName  = sanitizeInput($input['full_name'] ?? '');
$avatarUrl = sanitizeInput($input['avatar_url'] ?? '');

if (empty($googleId)) {
    jsonResponse(['success' => false, 'error' => 'Google ID is required.'], 400);
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'error' => 'A valid Google email is required.'], 400);
}

if (empty($fullName)) {
    $fullName = explode('@', $email)[0];
}

$pdo = Database::getInstance();

try {
    // Check if user exists by google_id or email
    $stmt = $pdo->prepare("SELECT id, uuid, full_name, email, phone, flow_tag, account_type, avatar_url, email_verified, two_factor_enabled, status FROM users WHERE google_id = ? OR email = ?");
    $stmt->execute([$googleId, $email]);
    $user = $stmt->fetch();

    if ($user) {
        // Link google_id if not linked yet
        if (empty($user['google_id'])) {
            $stmtLink = $pdo->prepare("UPDATE users SET google_id = ?, auth_provider = 'google', avatar_url = COALESCE(avatar_url, ?), email_verified = 1 WHERE id = ?");
            $stmtLink->execute([$googleId, $avatarUrl, $user['id']]);
        }
        $userId = $user['id'];
        $userUuid = $user['uuid'];
        $flowTag = $user['flow_tag'];
        $accountType = $user['account_type'];
        $phone = $user['phone'];
        $twoFactorEnabled = (bool)$user['two_factor_enabled'];
        $status = $user['status'];
    } else {
        // Create new user via Google
        $userUuid = generateUUID();
        $flowTag = generateFlowTag($fullName, $pdo);
        $accountType = 'individual';
        $phone = null;
        $twoFactorEnabled = false;
        $status = 'active';

        $stmtInsert = $pdo->prepare("INSERT INTO users (uuid, account_type, full_name, email, flow_tag, auth_provider, google_id, avatar_url, email_verified, status) 
                                     VALUES (?, ?, ?, ?, ?, 'google', ?, ?, 1, 'active')");
        $stmtInsert->execute([$userUuid, $accountType, $fullName, $email, $flowTag, $googleId, $avatarUrl]);
        $userId = $pdo->lastInsertId();
    }

    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $deviceInfo = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown Google App Client';
    
    // Create session (Google logins auto-verify 2FA unless 2FA explicit requirement requested)
    $session = createSession($pdo, $userId, $deviceInfo, $ipAddress, 1);
    LoginAlertService::send($pdo, (int)$userId, $deviceInfo);

    jsonResponse([
        'success' => true,
        'message' => 'Google Authentication successful!',
        'token' => $session['token'],
        'expires_at' => $session['expires_at'],
        'user' => [
            'uuid' => $userUuid,
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'flow_tag' => $flowTag,
            'account_type' => $accountType,
            'avatar_url' => $avatarUrl,
            'email_verified' => true,
            'two_factor_enabled' => $twoFactorEnabled,
            'status' => $status
        ]
    ], 200);

} catch (Exception $e) {
    error_log("Google Auth Exception: " . $e->getMessage());
    jsonResponse(['success' => false, 'error' => 'An internal error occurred during Google authentication.'], 500);
}
