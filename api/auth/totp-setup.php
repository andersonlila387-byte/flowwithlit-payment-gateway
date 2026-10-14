<?php
require_once __DIR__ . '/../../config/response.php'; require_once __DIR__ . '/../../config/security.php'; require_once __DIR__ . '/../../services/TotpService.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['success'=>false,'error'=>'Method not allowed.'],405);
$pdo=Database::getInstance(); $session=validateSession($pdo,getBearerToken() ?: (getJsonInput()['token'] ?? ''),15);
if (!$session['valid']) jsonResponse(['success'=>false,'error'=>$session['error']],401);
if(session_status()!==PHP_SESSION_ACTIVE) session_start();
$secret=TotpService::secret();
$_SESSION['flow_totp_pending_'.$session['user']['id']]=$secret;
jsonResponse(['success'=>true,'manual_key'=>$secret,'otpauth_uri'=>TotpService::uri($session['user']['email'],$secret),'issuer'=>'Flowwithlit','message'=>'Add this key to Google Authenticator, then confirm its current code.']);
