<?php
require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../services/BankVerificationService.php';
require_once __DIR__ . '/../../services/ApiRateLimit.php';
header('Cache-Control: no-store');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') jsonResponse(['success'=>false,'error'=>'Method not allowed.'],405);
$pdo=Database::getInstance(); $input=getJsonInput();
$session=validateSession($pdo,getBearerToken()?:'',15);
if(!$session['valid']||!empty($session['is_2fa_pending'])) jsonResponse(['success'=>false,'error'=>'Please sign in to continue.'],401);
$account=trim((string)($input['account_number']??'')); $bank=trim((string)($input['bank_code']??''));
if(!preg_match('/^\d{10}$/',$account)) jsonResponse(['success'=>false,'error'=>'Enter a valid 10-digit Nigerian account number.'],422);
if(!preg_match('/^\d{3,6}$/',$bank)) jsonResponse(['success'=>false,'error'=>'Select a valid Nigerian bank.'],422);
if($session['user']['status']!=='active'||!$session['user']['email_verified'])jsonResponse(['success'=>false,'error'=>'A verified active account is required.'],403);
try {
    $uid=(int)$session['user']['id'];
    if(!ApiRateLimit::consume($pdo,'bank-resolution',(string)$uid,10,900)){header('Retry-After: 900');jsonResponse(['success'=>false,'error'=>'Too many verification requests. Try again in 15 minutes.'],429);}
    jsonResponse((new BankVerificationService($pdo))->resolve($uid,$account,$bank));
}catch(InvalidArgumentException $e){jsonResponse(['success'=>false,'error'=>$e->getMessage()],422);}
catch(Throwable $e){error_log('Bank verification failed: '.get_class($e));jsonResponse(['success'=>false,'error'=>'Account verification is unavailable or the bank could not resolve this account. Check the details and try again.'],503);}
