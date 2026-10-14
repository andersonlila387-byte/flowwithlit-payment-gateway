<?php
declare(strict_types=1);
require_once __DIR__.'/../../config/response.php';
require_once __DIR__.'/../../config/security.php';
require_once __DIR__.'/../../services/WalletOperations.php';
header('Cache-Control: no-store');
if(($_SERVER['REQUEST_METHOD']??'GET')!=='POST')jsonResponse(['success'=>false,'error'=>'Method not allowed.'],405);
$pdo=Database::getInstance();$input=getJsonInput();$session=validateSession($pdo,getBearerToken()?:'',15);
if(!$session['valid']||!empty($session['is_2fa_pending']))jsonResponse(['success'=>false,'error'=>'Please sign in to continue.'],401);
try{
    $ops=new WalletOperations($pdo);$uid=(int)$session['user']['id'];$ops->authorize($uid,(string)($input['pin']??''));
    $destination=['account_number'=>(string)($input['account_number']??''),'bank_code'=>(string)($input['bank_code']??''),'verification_token'=>(string)($input['verification_token']??'')];
    jsonResponse($ops->requestWithdrawal($uid,'NGN',$input['amount']??null,$destination,(string)($_SERVER['HTTP_IDEMPOTENCY_KEY']??$input['idempotency_key']??'')),202);
}catch(InvalidArgumentException|DomainException $e){jsonResponse(['success'=>false,'error'=>$e->getMessage()],422);}
catch(Throwable $e){error_log('Bank payout request failed: '.get_class($e));jsonResponse(['success'=>false,'error'=>'Unable to queue this withdrawal. Retry with the same request key.'],500);}
