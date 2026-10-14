<?php
require_once __DIR__ . '/../../config/response.php'; require_once __DIR__ . '/../../config/security.php'; require_once __DIR__ . '/../../services/TotpService.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['success'=>false,'error'=>'Method not allowed.'],405);
if(session_status()!==PHP_SESSION_ACTIVE) session_start(); $input=getJsonInput(); $pdo=Database::getInstance(); $session=validateSession($pdo,getBearerToken() ?: ($input['token'] ?? ''),15);
if (!$session['valid']) jsonResponse(['success'=>false,'error'=>$session['error']],401);
$secret=$_SESSION['flow_totp_pending_'.$session['user']['id']] ?? ''; if(!$secret || !TotpService::verify($secret,$input['code'] ?? '')) jsonResponse(['success'=>false,'error'=>'Invalid authenticator code.'],400);
$pdo->prepare('UPDATE users SET two_factor_secret=?, two_factor_enabled=1 WHERE id=?')->execute([$secret,$session['user']['id']]); unset($_SESSION['flow_totp_pending_'.$session['user']['id']]);
jsonResponse(['success'=>true,'message'=>'Google Authenticator is now enabled.']);
