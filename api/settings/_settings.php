<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';

function requireSettingsUser(PDO $pdo, array $input = []): array {
    $session = validateSession($pdo, getBearerToken() ?: (string) ($input['token'] ?? ''), 15);
    if (!$session['valid'] || !empty($session['is_2fa_pending'])) {
        jsonResponse(['success'=>false, 'error'=>$session['error'] ?? 'Please sign in to manage settings.'], 401);
    }
    return $session;
}

function settingsAudit(PDO $pdo, int $userId, string $action, array $context = []): void {
    $statement = $pdo->prepare('INSERT INTO audit_logs(user_id,action,entity_type,entity_id,ip_address,user_agent,context) VALUES(?,?,?,?,?,?,?)');
    $statement->execute([$userId, $action, 'user_settings', (string) $userId, $_SERVER['REMOTE_ADDR'] ?? null, substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255), $context ? json_encode($context, JSON_UNESCAPED_SLASHES) : null]);
}

function settingsMethod(array $allowed): array {
    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    if (!in_array($method, $allowed, true)) jsonResponse(['success'=>false, 'error'=>'Method not allowed.'], 405);
    return $method === 'GET' ? $_GET : getJsonInput();
}
