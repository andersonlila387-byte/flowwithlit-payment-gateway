<?php
require_once __DIR__ . '/../../config/response.php'; require_once __DIR__ . '/../../config/security.php'; require_once __DIR__ . '/../../services/ReceivingAccountNumber.php';
header('Cache-Control: no-store'); $pdo=Database::getInstance(); $input=$_SERVER['REQUEST_METHOD']==='POST'?getJsonInput():$_GET; $session=validateSession($pdo,getBearerToken()?:($input['token']??''),15); if(!$session['valid']||!empty($session['is_2fa_pending'])) jsonResponse(['success'=>false,'error'=>'Please sign in to continue.'],401); $uid=(int)$session['user']['id'];

// One row per wallet. A wallet can hold several receiving-account rows (e.g. an old OnePipe attempt and a
// Paystack account), so pick its best one: active first, then the most recently updated.
$stmt=$pdo->prepare("SELECT w.uuid,w.currency_code,c.name,c.kind,c.decimals,w.available_balance,w.pending_balance,w.status,
        wra.id receiving_id,wra.provider,wra.account_name,wra.bank_name,wra.account_number_encrypted,wra.account_number_hash,wra.provider_payload,wra.account_number_display,wra.status receiving_status
    FROM wallets w
    JOIN currencies c ON c.code=w.currency_code
    LEFT JOIN wallet_receiving_accounts wra ON wra.id=(
        SELECT r.id FROM wallet_receiving_accounts r
        WHERE r.wallet_id=w.id AND r.status IN ('provisioning','active')
        ORDER BY (r.status='active') DESC, r.updated_at DESC, r.id DESC LIMIT 1)
    WHERE w.user_id=? AND w.status<>'closed' AND (c.kind='crypto' OR c.code='NGN')
    ORDER BY c.kind='fiat' DESC, c.code, (w.business_id IS NULL AND w.wallet_type='personal') DESC, (wra.status='active') DESC, w.id");
$stmt->execute([$uid]); $wallets=$stmt->fetchAll();
foreach($wallets as &$wallet){
    $wallet['account_number']=null;
    if($wallet['receiving_status']==='active') $wallet['account_number']=ReceivingAccountNumber::reveal($pdo,['id'=>$wallet['receiving_id'],'account_number_encrypted'=>$wallet['account_number_encrypted'],'account_number_hash'=>$wallet['account_number_hash'],'provider_payload'=>$wallet['provider_payload']]);
    unset($wallet['receiving_id'],$wallet['account_number_encrypted'],$wallet['account_number_hash'],$wallet['provider_payload']);
}
unset($wallet);
$currencies=$pdo->query("SELECT code,name,kind,decimals,network FROM currencies WHERE is_enabled=1 AND (kind='crypto' OR code='NGN') ORDER BY kind='fiat' DESC,code")->fetchAll();
jsonResponse(['success'=>true,'wallets'=>$wallets,'currencies'=>$currencies]);
