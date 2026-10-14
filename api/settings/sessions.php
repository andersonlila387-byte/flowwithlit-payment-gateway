<?php
declare(strict_types=1);

require_once __DIR__ . '/_settings.php';
$input = settingsMethod(['GET', 'POST']);
$pdo = Database::getInstance();
$session = requireSettingsUser($pdo, $input);
$userId = (int) $session['user']['id'];
$currentSessionId = (int) $session['session_id'];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
    $statement = $pdo->prepare('SELECT id,device_info,ip_address,last_activity,created_at,expires_at FROM user_sessions WHERE user_id=? AND expires_at>UTC_TIMESTAMP() ORDER BY last_activity DESC LIMIT 20');
    $statement->execute([$userId]);
    $sessions = array_map(static fn(array $row): array => ['id'=>(int) $row['id'], 'device_info'=>$row['device_info'] ?: 'Unknown device', 'ip_address'=>$row['ip_address'], 'last_activity'=>$row['last_activity'], 'created_at'=>$row['created_at'], 'expires_at'=>$row['expires_at'], 'is_current'=>(int) $row['id'] === $currentSessionId], $statement->fetchAll());
    jsonResponse(['success'=>true, 'sessions'=>$sessions]);
}
$action = (string) ($input['action'] ?? '');
if ($action === 'revoke_other') {
    $statement = $pdo->prepare('DELETE FROM user_sessions WHERE user_id=? AND id<>?');
    $statement->execute([$userId, $currentSessionId]);
    settingsAudit($pdo, $userId, 'settings.other_sessions_revoked', ['count'=>$statement->rowCount()]);
    jsonResponse(['success'=>true, 'message'=>'Other signed-in devices were removed.']);
}
if ($action === 'revoke') {
    $sessionId = filter_var($input['session_id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
    if (!$sessionId) jsonResponse(['success'=>false, 'error'=>'Invalid session.'], 422);
    if ((int) $sessionId === $currentSessionId) jsonResponse(['success'=>false, 'error'=>'Use Sign out to end this current session.'], 422);
    $statement = $pdo->prepare('DELETE FROM user_sessions WHERE id=? AND user_id=?');
    $statement->execute([(int) $sessionId, $userId]);
    if ($statement->rowCount() === 0) jsonResponse(['success'=>false, 'error'=>'Session was not found.'], 404);
    settingsAudit($pdo, $userId, 'settings.session_revoked', ['session_id'=>(int) $sessionId]);
    jsonResponse(['success'=>true, 'message'=>'Device session removed.']);
}
jsonResponse(['success'=>false, 'error'=>'Unsupported session action.'], 422);
