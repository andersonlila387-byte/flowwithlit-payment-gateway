<?php
declare(strict_types=1);

/**
 * Admin "Forgot password": emails a one-time reset link (valid 30 minutes).
 * Always answers with the same message so it cannot be used to discover admin emails.
 */
require_once __DIR__ . '/../_admin.php';
require_once __DIR__ . '/../../services/EmailService.php';
require_once __DIR__ . '/../../services/ApiRateLimit.php';
header('Cache-Control: no-store');

$input = adminMethod(['POST']);
$pdo = Database::getInstance();
$generic = ['success' => true, 'message' => 'If that email belongs to an administrator, a reset link is on its way. It expires in 30 minutes.'];

$email = strtolower(trim((string) ($input['email'] ?? '')));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) jsonResponse(['success' => false, 'error' => 'Enter a valid email address.'], 422);

$ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
if (!ApiRateLimit::consume($pdo, 'admin-reset-ip', $ip, 10, 3600) || !ApiRateLimit::consume($pdo, 'admin-reset-email', $email, 3, 3600)) {
    header('Retry-After: 3600');
    jsonResponse(['success' => false, 'error' => 'Too many reset requests. Please try again in an hour.'], 429);
}

/**
 * Base URL for the emailed link. Uses FLOW_APP_URL; only on a localhost request is it derived
 * from the request, so a forged Host header can never redirect a real reset link elsewhere.
 */
function adminResetBaseUrl(): ?string {
    $configured = rtrim((string) (getenv('FLOW_APP_URL') ?: ''), '/');
    if ($configured !== '') return $configured;
    $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
    $hostname = preg_replace('/:\d+$/', '', $host);
    if (!in_array($hostname, ['localhost', '127.0.0.1', '[::1]'], true)) return null;
    $root = rtrim(str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? ''), 3)), '/');
    return 'http://' . $host . $root;
}

$lookup = $pdo->prepare("SELECT id, full_name, email FROM admin_users WHERE LOWER(email) = ? AND status = 'active' LIMIT 1");
$lookup->execute([$email]);
$admin = $lookup->fetch(PDO::FETCH_ASSOC);
if (!$admin) jsonResponse($generic);

$baseUrl = adminResetBaseUrl();
if ($baseUrl === null) {
    error_log('Admin password reset not sent: FLOW_APP_URL is not configured.');
    jsonResponse($generic);
}

$token = bin2hex(random_bytes(32));
try {
    $pdo->beginTransaction();
    // A new link replaces any older unused ones.
    $pdo->prepare('UPDATE admin_password_resets SET used_at = UTC_TIMESTAMP() WHERE admin_user_id = ? AND used_at IS NULL')->execute([$admin['id']]);
    $pdo->prepare('INSERT INTO admin_password_resets (admin_user_id, token_hash, expires_at, requested_ip) VALUES (?, ?, UTC_TIMESTAMP() + INTERVAL 30 MINUTE, ?)')
        ->execute([$admin['id'], hash('sha256', $token), substr($ip, 0, 45)]);
    adminAudit($pdo, (int) $admin['id'], 'admin.password_reset_requested', 'admin_user', (string) $admin['id']);
    $pdo->commit();
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('Admin password reset request failed: ' . get_class($e));
    jsonResponse($generic);
}

try {
    EmailService::send($admin['email'], 'Reset your Flowwithlit admin password', 'admin-password-reset', [
        'name' => $admin['full_name'],
        'url' => $baseUrl . '/admin/reset-password.php?token=' . $token,
    ]);
} catch (Throwable $mailError) {
    error_log('Admin password reset email error: ' . $mailError->getMessage());
}
jsonResponse($generic);
