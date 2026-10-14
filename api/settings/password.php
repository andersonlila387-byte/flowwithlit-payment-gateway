<?php
declare(strict_types=1);

require_once __DIR__ . '/_settings.php';
$input = settingsMethod(['POST']);
$pdo = Database::getInstance();
$session = requireSettingsUser($pdo, $input);
$userId = (int) $session['user']['id'];
$currentPassword = (string) ($input['current_password'] ?? '');
$newPassword = (string) ($input['new_password'] ?? '');
if (strlen($newPassword) < 12) jsonResponse(['success'=>false, 'error'=>'Use a new password with at least 12 characters.'], 422);
if ($currentPassword === $newPassword) jsonResponse(['success'=>false, 'error'=>'Choose a new password that differs from the current password.'], 422);
$statement = $pdo->prepare('SELECT password_hash FROM users WHERE id=? LIMIT 1');
$statement->execute([$userId]);
$hash = $statement->fetchColumn();
if (!$hash || !verifyPassword($currentPassword, (string) $hash)) jsonResponse(['success'=>false, 'error'=>'Your current password is incorrect.'], 403);
try {
    $pdo->beginTransaction();
    $pdo->prepare('UPDATE users SET password_hash=? WHERE id=?')->execute([hashPassword($newPassword), $userId]);
    $otherSessions = $pdo->prepare('DELETE FROM user_sessions WHERE user_id=? AND id<>?');
    $otherSessions->execute([$userId, (int) $session['session_id']]);
    settingsAudit($pdo, $userId, 'settings.password_changed', ['other_sessions_revoked'=>$otherSessions->rowCount()]);
    $pdo->commit();
    jsonResponse(['success'=>true, 'message'=>'Password changed. Other devices have been signed out.']);
} catch (Throwable $error) {
    if ($pdo->inTransaction()) $pdo->rollBack(); error_log('Password change failed: '.$error->getMessage());
    jsonResponse(['success'=>false, 'error'=>'Unable to change your password.'], 500);
}
