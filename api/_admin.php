<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/response.php';
require_once __DIR__ . '/../config/security.php';

function createAdminSession(PDO $pdo, int $adminUserId, ?string $deviceInfo, string $ipAddress, bool $isTwoFactorVerified = true, int $durationDays = 1): array {
    $token = bin2hex(random_bytes(32));
    $expiresAt = gmdate('Y-m-d H:i:s', strtotime('+'.$durationDays.' days'));
    $statement = $pdo->prepare('INSERT INTO admin_sessions(admin_user_id,token_hash,device_info,ip_address,is_2fa_verified,expires_at,last_activity) VALUES(?,?,?,?,?,?,UTC_TIMESTAMP())');
    $statement->execute([$adminUserId, hash('sha256', $token), substr((string) $deviceInfo, 0, 255), $ipAddress, $isTwoFactorVerified ? 1 : 0, $expiresAt]);
    return ['token'=>$token, 'expires_at'=>$expiresAt];
}

function validateAdminSession(PDO $pdo, string $token, int $maxInactivityMinutes = 15): array {
    if ($token === '') return ['valid'=>false, 'error'=>'Administrator session is required.'];
    $statement = $pdo->prepare("SELECT s.id session_id,s.admin_user_id,s.is_2fa_verified,s.last_activity,a.uuid,a.full_name,a.email,a.avatar_url,a.role,a.status,a.two_factor_enabled
        FROM admin_sessions s INNER JOIN admin_users a ON a.id=s.admin_user_id
        WHERE s.token_hash=? AND s.expires_at>UTC_TIMESTAMP() LIMIT 1");
    $statement->execute([hash('sha256', $token)]);
    $session = $statement->fetch();
    if (!$session || $session['status'] !== 'active') return ['valid'=>false, 'error'=>'Invalid or expired administrator session.'];
    if ((time() - strtotime($session['last_activity'])) > $maxInactivityMinutes * 60) {
        $pdo->prepare('DELETE FROM admin_sessions WHERE id=?')->execute([(int) $session['session_id']]);
        return ['valid'=>false, 'error'=>'Administrator session expired due to inactivity.'];
    }
    $pdo->prepare('UPDATE admin_sessions SET last_activity=UTC_TIMESTAMP() WHERE id=?')->execute([(int) $session['session_id']]);
    return [
        'valid'=>true,
        'is_2fa_pending'=>!(bool) $session['is_2fa_verified'],
        'admin'=>[
            'id'=>(int) $session['admin_user_id'], 'uuid'=>$session['uuid'], 'full_name'=>$session['full_name'],
            'email'=>$session['email'], 'avatar_url'=>$session['avatar_url'], 'role'=>$session['role'],
        ],
    ];
}

function requireAdmin(PDO $pdo, array $input = []): array {
    $session = validateAdminSession($pdo, getBearerToken() ?: (string) ($input['token'] ?? ''), 15);
    if (!$session['valid'] || $session['is_2fa_pending']) {
        jsonResponse(['success'=>false,'error'=>'Please sign in with an administrator account.'], 401);
    }
    return [$session, (string) $session['admin']['role']];
}

function requireAdminRole(string $role, array $allowedRoles, string $capability): void {
    if (!in_array($role, $allowedRoles, true)) jsonResponse(['success'=>false,'error'=>"Your role cannot {$capability}."],403);
}

function adminAudit(PDO $pdo, int $adminUserId, string $action, string $entity, ?string $entityId = null, array $context = []): void {
    $statement = $pdo->prepare('INSERT INTO audit_logs(admin_user_id,action,entity_type,entity_id,ip_address,user_agent,context) VALUES(?,?,?,?,?,?,?)');
    $statement->execute([$adminUserId, $action, $entity, $entityId, $_SERVER['REMOTE_ADDR'] ?? null, substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255), $context ? json_encode($context, JSON_UNESCAPED_SLASHES) : null]);
}

function adminMethod(array $allowed = ['GET']): array {
    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    if (!in_array($method, $allowed, true)) jsonResponse(['success'=>false,'error'=>'Method not allowed.'],405);
    return $method === 'GET' ? $_GET : getJsonInput();
}

