<?php
/** Complete a pending login with a Google Authenticator TOTP code. */
require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../services/TotpService.php';
require_once __DIR__ . '/../../services/LoginAlertService.php';
require_once __DIR__ . '/../../services/ApiRateLimit.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['success'=>false,'error'=>'Method not allowed.'],405);
$input = getJsonInput(); $token = getBearerToken() ?: ($input['token'] ?? ''); $code = trim((string)($input['code'] ?? ''));
$pdo = Database::getInstance(); $session = validateSession($pdo, $token, 15);
if (!$session['valid']) jsonResponse(['success'=>false,'error'=>$session['error']],401);
if (empty($session['is_2fa_pending'])) jsonResponse(['success'=>false,'error'=>'This session does not require two-factor verification.'],400);
$stmt = $pdo->prepare('SELECT two_factor_secret, two_factor_enabled FROM users WHERE id = ?'); $stmt->execute([$session['user']['id']]); $user = $stmt->fetch();
if (!ApiRateLimit::consume($pdo, 'totp_login', (string)$session['user']['id'], 10, 600)) jsonResponse(['success'=>false,'error'=>'Too many attempts. Please wait a few minutes and sign in again.'],429);
if (!$user || !$user['two_factor_enabled'] || !TotpService::verify(TotpService::reveal($user['two_factor_secret']), $code)) jsonResponse(['success'=>false,'error'=>'That code is not correct. Enter the current 6-digit code from your authenticator app.'],400);
$completed = $pdo->prepare('UPDATE user_sessions SET is_2fa_verified=1, last_activity=NOW() WHERE token_hash=? AND is_2fa_verified=0');
$completed->execute([hash('sha256',$token)]);
if ($completed->rowCount() === 1) LoginAlertService::send($pdo, (int)$session['user']['id'], $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown App Client');
jsonResponse(['success'=>true,'message'=>'Two-factor authentication verified.']);
