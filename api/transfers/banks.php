<?php
declare(strict_types=1);
require_once __DIR__.'/../../config/response.php';
require_once __DIR__.'/../../config/security.php';
require_once __DIR__.'/../../services/BankVerificationService.php';
require_once __DIR__.'/../../services/ApiRateLimit.php';
header('Cache-Control: no-store');
if(($_SERVER['REQUEST_METHOD']??'GET')!=='GET')jsonResponse(['success'=>false,'error'=>'Method not allowed.'],405);
$pdo=Database::getInstance();$session=validateSession($pdo,getBearerToken()?:'',15);
if(!$session['valid']||!empty($session['is_2fa_pending']))jsonResponse(['success'=>false,'error'=>'Please sign in to continue.'],401);
try {
    if(!ApiRateLimit::consume($pdo,'bank-catalog',(string)$session['user']['id'],20,60)){header('Retry-After: 60');jsonResponse(['success'=>false,'error'=>'Please wait a minute before reloading banks.'],429);}
    $banks=(new BankVerificationService($pdo))->banks();
    if(!$banks)throw new DomainException('Empty bank catalog.');
    jsonResponse(['success'=>true,'country'=>'NG','banks'=>$banks]);
}catch(Throwable $e){error_log('Bank catalog unavailable: '.get_class($e));jsonResponse(['success'=>false,'error'=>'Bank verification is not available. An administrator must enable the bank-verification provider.'],503);}
