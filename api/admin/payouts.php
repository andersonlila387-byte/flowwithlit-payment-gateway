<?php
declare(strict_types=1);
require_once __DIR__ . '/../_admin.php';
require_once __DIR__.'/../../services/WalletOperations.php';
header('Cache-Control: no-store');
$input=adminMethod(['GET','POST']);$pdo=Database::getInstance();[$session,$role]=requireAdmin($pdo,$input);
requireAdminRole($role,['finance','super_admin'],'manage payouts');
if($_SERVER['REQUEST_METHOD']==='GET'){
    // Explicit projection prevents encrypted destinations leaking into list responses.
    $q=$pdo->query("SELECT p.id,p.uuid,p.currency_code,p.amount,p.fee_amount,p.status,p.provider_reference,p.review_note,p.created_at,p.updated_at,p.due_at,p.execution_mode,p.destination_type,p.destination_display,p.reviewed_at,u.full_name,u.email,ba.bank_name,ba.account_name,ba.account_number_last4,(p.due_at<UTC_TIMESTAMP() AND p.status IN ('pending','approved','processing')) overdue FROM payout_requests p JOIN users u ON u.id=p.user_id LEFT JOIN bank_accounts ba ON ba.id=p.bank_account_id ORDER BY p.created_at DESC LIMIT 200");
    jsonResponse(['success'=>true,'payouts'=>$q->fetchAll()]);
}
try{
    if(($input['action']??'')==='destination'){
        $q=$pdo->prepare('SELECT p.destination_encrypted,t.reference FROM payout_requests p JOIN transactions t ON t.id=p.transaction_id WHERE p.id=?');$q->execute([(int)($input['payout_id']??0)]);$p=$q->fetch();
        if(!$p||!$p['destination_encrypted'])throw new DomainException('Payout destination is unavailable.');
        adminAudit($pdo,(int)$session['admin']['id'],'payout.destination_viewed','payout',(string)$input['payout_id']);
        jsonResponse(['success'=>true,'destination'=>json_decode(PrivateStorage::decrypt($p['destination_encrypted'],'payout:'.$p['reference']),true,512,JSON_THROW_ON_ERROR)]);
    }
    $status=(string)($input['status']??'');
    if(in_array($status,['failed','cancelled'],true)&&empty($input['confirmed_not_paid']))throw new InvalidArgumentException('Confirm no payment was sent before releasing reserved funds.');
    jsonResponse((new WalletOperations($pdo))->reviewWithdrawal((int)($input['payout_id']??0),(int)$session['admin']['id'],$status,trim((string)($input['review_note']??'')),trim((string)($input['provider_reference']??''))));
}catch(InvalidArgumentException|DomainException $e){jsonResponse(['success'=>false,'error'=>$e->getMessage()],422);}
catch(Throwable $e){error_log('Payout review failed: '.get_class($e));jsonResponse(['success'=>false,'error'=>'Payout could not be updated.'],500);}
