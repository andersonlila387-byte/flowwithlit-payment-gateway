<?php
declare(strict_types=1);
/**
 * Create (or return) the signed-in user's NGN receiving account through OnePipe.
 * Uses the same ReceivingAccountProvisioner as the background worker, so there is one code path.
 */
require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../services/ReceivingAccountProvisioner.php';
require_once __DIR__ . '/../../services/ApiRateLimit.php';

header('Cache-Control: no-store');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') jsonResponse(['success' => false, 'error' => 'Method not allowed.'], 405);
$pdo = Database::getInstance();
$input = getJsonInput();
$session = validateSession($pdo, getBearerToken() ?: (string) ($input['token'] ?? ''), 15);
if (!$session['valid'] || !empty($session['is_2fa_pending'])) jsonResponse(['success' => false, 'error' => 'Please sign in to continue.'], 401);
$uid = (int) $session['user']['id'];

$currency = strtoupper(trim((string) ($input['currency'] ?? 'NGN')));
if ($currency !== 'NGN') jsonResponse(['success' => false, 'error' => 'Receiving accounts are currently available for NGN only.'], 422);
if (!ApiRateLimit::consume($pdo, 'provision_fiat', (string) $uid, 5, 600)) {
    jsonResponse(['success' => false, 'error' => 'Too many attempts. Please wait a few minutes and try again.'], 429);
}

try {
    jsonResponse((new ReceivingAccountProvisioner($pdo))->provision($uid, $currency));
} catch (InvalidArgumentException | DomainException $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 409);
} catch (Throwable $e) {
    error_log('NGN receiving account provisioning failed: ' . get_class($e));
    jsonResponse(['success' => false, 'error' => 'Your receiving account could not be created right now. Please try again later.'], 502);
}
