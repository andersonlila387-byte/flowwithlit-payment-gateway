<?php
declare(strict_types=1);
require_once __DIR__.'/../../config/response.php';
require_once __DIR__.'/../../config/security.php';
require_once __DIR__.'/../../services/WalletOperations.php';
header('Cache-Control: no-store');
if (($_SERVER['REQUEST_METHOD']??'GET')!=='POST')jsonResponse(['success'=>false,'error'=>'Method not allowed.'],405);
$db=Database::getInstance();$input=getJsonInput();
$session=validateSession($db,getBearerToken()?:'',15);
if(!$session['valid']||!empty($session['is_2fa_pending']))jsonResponse(['success'=>false,'error'=>'Please sign in to continue.'],401);
$uid=(int)$session['user']['id'];$action=(string)($input['action']??'');
$currency=strtoupper(trim((string)($input['currency']??'NGN')));
$ops=new WalletOperations($db);
try {
    if(in_array($action,['transfer','withdraw'],true)) {
        $ops->authorize($uid,(string)($input['pin']??''));
        $key=(string)($_SERVER['HTTP_IDEMPOTENCY_KEY']??$input['idempotency_key']??'');
        if($action==='transfer')jsonResponse($ops->transfer($uid,(string)($input['recipient_account']??''),$currency,$input['amount']??null,$key,(string)($input['note']??'')));
        jsonResponse($ops->requestWithdrawal($uid,$currency,$input['amount']??null,(array)($input['destination']??[]),$key),202);
    }
    if($action==='fund') {
        // Funding instructions cannot themselves create a confirmed balance or payment.
        $q=$db->prepare("SELECT r.account_number_encrypted,r.account_name,r.bank_name FROM wallet_receiving_accounts r JOIN wallets w ON w.id=r.wallet_id WHERE r.user_id=? AND r.currency_code=? AND r.status='active' AND w.status='active' AND w.business_id IS NULL AND w.wallet_type='personal' LIMIT 1");
        $q->execute([$uid,$currency]);$account=$q->fetch();
        if(!$account)jsonResponse(['success'=>false,'error'=>'Your receiving account is not ready yet.'],409);
        jsonResponse(['success'=>true,'status'=>'awaiting_transfer','account'=>['account_number'=>PrivateStorage::decrypt($account['account_number_encrypted'],'wallet-account'),'account_name'=>$account['account_name'],'bank_name'=>$account['bank_name']]]);
    }
    if($action==='swap')jsonResponse(['success'=>false,'error'=>'A server-issued executable quote is required. Conversion is being configured.','code'=>'QUOTE_REQUIRED'],409);
    jsonResponse(['success'=>false,'error'=>'Unsupported financial action.'],422);
}catch(InvalidArgumentException|DomainException $e){jsonResponse(['success'=>false,'error'=>$e->getMessage()],422);}
catch(Throwable $e){error_log('Wallet operation failed: '.get_class($e));jsonResponse(['success'=>false,'error'=>'Unable to process this request. Retry using the same request key.'],500);}
