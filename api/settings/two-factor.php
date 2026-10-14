<?php
declare(strict_types=1);

require_once __DIR__ . '/_settings.php';
require_once __DIR__ . '/../../services/EmailService.php';
$input = settingsMethod(['POST']);
$pdo = Database::getInstance();
$session = requireSettingsUser($pdo, $input);
$userId = (int) $session['user']['id'];
$email = (string) $session['user']['email'];
$action = (string) ($input['action'] ?? '');

if ($action === 'request_enable') {
    $statement = $pdo->prepare('SELECT two_factor_enabled FROM users WHERE id=?'); $statement->execute([$userId]);
    if ((bool) $statement->fetchColumn()) jsonResponse(['success'=>false, 'error'=>'Two-factor authentication is already enabled.'], 409);
    $code = createVerificationCode($pdo, $userId, $email, '2fa', 15);
    try { EmailService::send($email, 'Your Flowwithlit security code', 'two-factor', ['code'=>$code]); }
    catch (Throwable $error) { error_log('2FA email failed: '.$error->getMessage()); jsonResponse(['success'=>false, 'error'=>'We could not send the verification code.'], 503); }
    jsonResponse(['success'=>true, 'message'=>'A verification code was sent to your email.']);
}
if ($action === 'enable') {
    $code = trim((string) ($input['code'] ?? ''));
    if (!preg_match('/^\d{6}$/', $code)) jsonResponse(['success'=>false, 'error'=>'Enter the six-digit verification code.'], 422);
    $verified = verifyOTPCode($pdo, $email, $code, '2fa');
    if (!$verified['success']) jsonResponse(['success'=>false, 'error'=>$verified['error']], 422);
    $pdo->prepare('UPDATE users SET two_factor_enabled=1 WHERE id=?')->execute([$userId]); settingsAudit($pdo, $userId, 'settings.two_factor_enabled');
    jsonResponse(['success'=>true, 'two_factor_enabled'=>true, 'message'=>'Two-factor authentication is enabled.']);
}
if ($action === 'disable') {
    $password = (string) ($input['current_password'] ?? '');
    $statement = $pdo->prepare('SELECT password_hash FROM users WHERE id=?'); $statement->execute([$userId]);
    if (!verifyPassword($password, (string) $statement->fetchColumn())) jsonResponse(['success'=>false, 'error'=>'Enter your current password to disable two-factor authentication.'], 403);
    $pdo->prepare('UPDATE users SET two_factor_enabled=0 WHERE id=?')->execute([$userId]); settingsAudit($pdo, $userId, 'settings.two_factor_disabled');
    jsonResponse(['success'=>true, 'two_factor_enabled'=>false, 'message'=>'Two-factor authentication is disabled.']);
}
jsonResponse(['success'=>false, 'error'=>'Unsupported two-factor action.'], 422);
