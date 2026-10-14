<?php
declare(strict_types=1);

require_once __DIR__.'/../_admin.php';
require_once __DIR__.'/../../services/TotpService.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') jsonResponse(['success'=>false,'error'=>'Method not allowed.'],405);
$input = getJsonInput();
$token = getBearerToken() ?: (string) ($input['token'] ?? '');
$code = trim((string) ($input['code'] ?? ''));
$pdo = Database::getInstance();
$session = validateAdminSession($pdo, $token, 15);
if (!$session['valid']) jsonResponse(['success'=>false,'error'=>$session['error']],401);
if (empty($session['is_2fa_pending'])) jsonResponse(['success'=>false,'error'=>'This administrator session does not require two-factor verification.'],400);
$statement = $pdo->prepare('SELECT two_factor_secret,two_factor_enabled FROM admin_users WHERE id=?');
$statement->execute([(int) $session['admin']['id']]); $admin = $statement->fetch();
if (!$admin || !$admin['two_factor_enabled'] || !TotpService::verify((string) $admin['two_factor_secret'], $code)) jsonResponse(['success'=>false,'error'=>'Invalid authenticator code.'],400);
$pdo->prepare('UPDATE admin_sessions SET is_2fa_verified=1,last_activity=UTC_TIMESTAMP() WHERE token_hash=? AND is_2fa_verified=0')->execute([hash('sha256',$token)]);
adminAudit($pdo, (int) $session['admin']['id'], 'admin.two_factor_verified', 'admin_session', null);
jsonResponse(['success'=>true,'message'=>'Two-factor authentication verified.']);

