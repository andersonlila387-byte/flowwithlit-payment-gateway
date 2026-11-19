<?php
declare(strict_types=1);

/**
 * Two-factor authentication with an authenticator app (TOTP).
 *
 *   setup   -> creates a new secret (saved encrypted, still switched off) and returns the QR/setup key
 *   enable  -> checks a code from the app against that secret, then switches 2FA on
 *   disable -> needs a current app code (and the password, when the account has one)
 *
 * Sign-in then asks for the app code (api/auth/totp-login.php).
 */
require_once __DIR__ . '/_settings.php';
require_once __DIR__ . '/../../services/TotpService.php';
require_once __DIR__ . '/../../services/ApiRateLimit.php';
$input = settingsMethod(['POST']);
$pdo = Database::getInstance();
$session = requireSettingsUser($pdo, $input);
$userId = (int) $session['user']['id'];
$email = (string) $session['user']['email'];
$action = (string) ($input['action'] ?? '');

$statement = $pdo->prepare('SELECT two_factor_enabled, two_factor_secret, password_hash FROM users WHERE id=?');
$statement->execute([$userId]);
$user = $statement->fetch();
if (!$user) jsonResponse(['success'=>false, 'error'=>'Account not found.'], 404);
$enabled = (int) $user['two_factor_enabled'] === 1;

if (in_array($action, ['enable', 'disable'], true) && !ApiRateLimit::consume($pdo, '2fa_settings', (string) $userId, 10, 600)) {
    jsonResponse(['success'=>false, 'error'=>'Too many attempts. Please wait a few minutes and try again.'], 429);
}

if ($action === 'setup') {
    if ($enabled) jsonResponse(['success'=>false, 'error'=>'Two-factor authentication is already enabled.'], 409);
    $secret = TotpService::secret();
    try {
        $pdo->prepare('UPDATE users SET two_factor_secret=? WHERE id=? AND two_factor_enabled=0')->execute([TotpService::protect($secret), $userId]);
    } catch (Throwable $error) {
        error_log('2FA setup failed: '.$error->getMessage());
        jsonResponse(['success'=>false, 'error'=>'Two-factor setup is unavailable right now. Please try again later.'], 503);
    }
    jsonResponse(['success'=>true, 'manual_key'=>trim(chunk_split($secret, 4, ' ')), 'otpauth_uri'=>TotpService::uri($email, $secret), 'issuer'=>'Flowwithlit', 'account'=>$email]);
}

if ($action === 'enable') {
    if ($enabled) jsonResponse(['success'=>false, 'error'=>'Two-factor authentication is already enabled.'], 409);
    $secret = TotpService::reveal($user['two_factor_secret']);
    if ($secret === null) jsonResponse(['success'=>false, 'error'=>'Start the setup again to get a new QR code.'], 409);
    if (!TotpService::verify($secret, $input['code'] ?? '')) jsonResponse(['success'=>false, 'error'=>'That code is not correct. Enter the current 6-digit code from your authenticator app.'], 422);
    $pdo->prepare('UPDATE users SET two_factor_enabled=1 WHERE id=?')->execute([$userId]);
    settingsAudit($pdo, $userId, 'settings.two_factor_enabled', ['method'=>'totp']);
    jsonResponse(['success'=>true, 'two_factor_enabled'=>true, 'message'=>'Two-factor authentication is on. You will need your authenticator app each time you sign in.']);
}

if ($action === 'disable') {
    if (!$enabled) jsonResponse(['success'=>true, 'two_factor_enabled'=>false, 'message'=>'Two-factor authentication is already off.']);
    if (!empty($user['password_hash']) && !verifyPassword((string) ($input['current_password'] ?? ''), (string) $user['password_hash'])) {
        jsonResponse(['success'=>false, 'error'=>'Your current password is not correct.'], 403);
    }
    if (!TotpService::verify(TotpService::reveal($user['two_factor_secret']), $input['code'] ?? '')) {
        jsonResponse(['success'=>false, 'error'=>'Enter the current 6-digit code from your authenticator app.'], 422);
    }
    $pdo->prepare('UPDATE users SET two_factor_enabled=0, two_factor_secret=NULL WHERE id=?')->execute([$userId]);
    settingsAudit($pdo, $userId, 'settings.two_factor_disabled');
    jsonResponse(['success'=>true, 'two_factor_enabled'=>false, 'message'=>'Two-factor authentication is off.']);
}

jsonResponse(['success'=>false, 'error'=>'Unsupported two-factor action.'], 422);
