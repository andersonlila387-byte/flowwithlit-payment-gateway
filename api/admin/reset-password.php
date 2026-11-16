<?php
declare(strict_types=1);

/**
 * Admin password reset from an emailed link.
 *  GET  ?token=…            → is the link still valid? (returns a masked email)
 *  POST {token, password}   → sets the new password, burns the link, signs out all sessions.
 */
require_once __DIR__ . '/../_admin.php';
require_once __DIR__ . '/../../services/ApiRateLimit.php';
header('Cache-Control: no-store');

$input = adminMethod(['GET', 'POST']);
$pdo = Database::getInstance();
$ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
if (!ApiRateLimit::consume($pdo, 'admin-reset-use', $ip, 20, 900)) {
    header('Retry-After: 900');
    jsonResponse(['success' => false, 'error' => 'Too many attempts. Please wait 15 minutes and try again.'], 429);
}

$token = (string) ($input['token'] ?? '');
if (!preg_match('/^[a-f0-9]{64}$/D', $token)) jsonResponse(['success' => false, 'error' => 'This reset link is invalid. Request a new one from the sign-in page.'], 400);
$hash = hash('sha256', $token);
$invalid = ['success' => false, 'error' => 'This reset link has expired or was already used. Request a new one from the sign-in page.'];

$maskEmail = static function (string $email): string {
    [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');
    return mb_substr($local, 0, 2) . str_repeat('•', max(1, mb_strlen($local) - 2)) . '@' . $domain;
};

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $q = $pdo->prepare("SELECT a.email FROM admin_password_resets r JOIN admin_users a ON a.id = r.admin_user_id
        WHERE r.token_hash = ? AND r.used_at IS NULL AND r.expires_at > UTC_TIMESTAMP() AND a.status = 'active' LIMIT 1");
    $q->execute([$hash]);
    $email = $q->fetchColumn();
    if (!$email) jsonResponse($invalid, 410);
    jsonResponse(['success' => true, 'email' => $maskEmail((string) $email)]);
}

$password = (string) ($input['password'] ?? '');
if (strlen($password) < 12) jsonResponse(['success' => false, 'error' => 'Use at least 12 characters.'], 422);
if (strlen($password) > 200) jsonResponse(['success' => false, 'error' => 'That password is too long.'], 422);

try {
    $pdo->beginTransaction();
    $q = $pdo->prepare("SELECT r.id, r.admin_user_id, a.email FROM admin_password_resets r JOIN admin_users a ON a.id = r.admin_user_id
        WHERE r.token_hash = ? AND r.used_at IS NULL AND r.expires_at > UTC_TIMESTAMP() AND a.status = 'active' LIMIT 1 FOR UPDATE");
    $q->execute([$hash]);
    $reset = $q->fetch(PDO::FETCH_ASSOC);
    if (!$reset) { $pdo->rollBack(); jsonResponse($invalid, 410); }
    $adminId = (int) $reset['admin_user_id'];
    $pdo->prepare('UPDATE admin_users SET password_hash = ? WHERE id = ?')->execute([hashPassword($password), $adminId]);
    $pdo->prepare('UPDATE admin_password_resets SET used_at = UTC_TIMESTAMP() WHERE admin_user_id = ? AND used_at IS NULL')->execute([$adminId]);
    $pdo->prepare('DELETE FROM admin_sessions WHERE admin_user_id = ?')->execute([$adminId]);
    $pdo->prepare('DELETE FROM admin_login_attempts WHERE LOWER(email) = ?')->execute([strtolower((string) $reset['email'])]);
    adminAudit($pdo, $adminId, 'admin.password_reset_completed', 'admin_user', (string) $adminId);
    $pdo->commit();
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('Admin password reset failed: ' . get_class($e));
    jsonResponse(['success' => false, 'error' => 'Your password could not be updated. Please try again.'], 500);
}
jsonResponse(['success' => true, 'message' => 'Your password has been updated. Sign in with your new password.']);
