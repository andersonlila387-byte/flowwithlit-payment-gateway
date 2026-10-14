<?php
declare(strict_types=1);
require_once __DIR__.'/../_admin.php';
header('Cache-Control: no-store');
$input = adminMethod(['GET','POST']);
$pdo = Database::getInstance();
[$session,$role] = requireAdmin($pdo,$input);
requireAdminRole($role,['finance','super_admin'],'manage transfer operations');
$adminId = (int)$session['admin']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string)($input['action'] ?? ''); $uuid=trim((string)($input['uuid'] ?? '')); $note=trim((string)($input['note'] ?? ''));
    if ($uuid==='' || strlen($note)<10 || strlen($note)>500) jsonResponse(['success'=>false,'error'=>'Add an internal note between 10 and 500 characters.'],422);
    if ($action === 'follow_up') {
        $stmt=$pdo->prepare("SELECT id FROM transactions WHERE uuid=? AND type='transfer' LIMIT 1"); $stmt->execute([$uuid]); $transactionId=(int)$stmt->fetchColumn();
        if (!$transactionId) jsonResponse(['success'=>false,'error'=>'Transfer not found.'],404);
        $save=$pdo->prepare("INSERT INTO admin_transfer_followups(transaction_id,status,note,handled_by) VALUES(?,'open',?,?) ON DUPLICATE KEY UPDATE status='open',note=VALUES(note),handled_by=VALUES(handled_by),updated_at=UTC_TIMESTAMP()");
        $save->execute([$transactionId,$note,$adminId]); adminAudit($pdo,$adminId,'transfer.follow_up','transaction',$uuid,['note'=>$note]);
        jsonResponse(['success'=>true,'message'=>'Provider follow-up recorded.']);
    }
    if ($action === 'reverse') {
        if (empty($input['provider_confirmed_unsettled'])) jsonResponse(['success'=>false,'error'=>'Provider confirmation is required before returning funds.'],422);
        try {
            $pdo->beginTransaction();
            $stmt=$pdo->prepare("SELECT id,source_wallet_id,status,amount,fee_amount,currency_code FROM transactions WHERE uuid=? AND type='transfer' FOR UPDATE"); $stmt->execute([$uuid]); $transfer=$stmt->fetch();
            if (!$transfer || !in_array($transfer['status'],['pending','processing'],true) || !$transfer['source_wallet_id']) throw new RuntimeException('Only an unsettled pending or processing transfer can be returned.');
            $wallet=$pdo->prepare('SELECT id,status FROM wallets WHERE id=? FOR UPDATE'); $wallet->execute([(int)$transfer['source_wallet_id']]); $wallet=$wallet->fetch();
            if (!$wallet || $wallet['status']==='closed') throw new RuntimeException('The source wallet is not available for a return.');
            $total=(string)((float)$transfer['amount']+(float)$transfer['fee_amount']);
            $updateWallet=$pdo->prepare('UPDATE wallets SET available_balance=available_balance+?,pending_balance=GREATEST(0,pending_balance-?),updated_at=UTC_TIMESTAMP() WHERE id=?');
            $updateWallet->execute([$total,$total,(int)$wallet['id']]);
            $update=$pdo->prepare("UPDATE transactions SET status='reversed',metadata=JSON_SET(COALESCE(metadata,JSON_OBJECT()),'$.admin_reversal_reason',?,'$.admin_reversed_by',?),completed_at=UTC_TIMESTAMP() WHERE id=? AND status IN ('pending','processing')");
            $update->execute([$note,$session['admin']['uuid'],(int)$transfer['id']]);
            if (!$update->rowCount()) throw new RuntimeException('The transfer status changed before the return could be completed.');
            $follow=$pdo->prepare("INSERT INTO admin_transfer_followups(transaction_id,status,note,handled_by) VALUES(?,'resolved',?,?) ON DUPLICATE KEY UPDATE status='resolved',note=VALUES(note),handled_by=VALUES(handled_by),updated_at=UTC_TIMESTAMP()");
            $follow->execute([(int)$transfer['id'],$note,$adminId]);
            adminAudit($pdo,$adminId,'transfer.reversed','transaction',$uuid,['amount'=>$transfer['amount'],'currency'=>$transfer['currency_code'],'reason'=>$note]);
            $pdo->commit(); jsonResponse(['success'=>true,'message'=>'Reserved transfer funds were returned to the customer wallet.']);
        } catch (Throwable $e) { if($pdo->inTransaction())$pdo->rollBack(); jsonResponse(['success'=>false,'error'=>$e instanceof RuntimeException?$e->getMessage():'Unable to return these funds.'],422); }
    }
    jsonResponse(['success'=>false,'error'=>'Unknown transfer action.'],422);
}

$query=$pdo->query("SELECT t.uuid,t.reference,t.status,t.currency_code,t.amount,t.fee_amount,t.provider,t.provider_reference,t.created_at,t.completed_at,t.metadata,u.full_name customer_name,u.email customer_email,f.status follow_up_status,f.note follow_up_note,f.updated_at follow_up_at FROM transactions t INNER JOIN users u ON u.id=t.user_id LEFT JOIN admin_transfer_followups f ON f.transaction_id=t.id WHERE t.type='transfer' ORDER BY t.created_at DESC LIMIT 200");
$transfers=[];
foreach($query->fetchAll() as $row){$meta=json_decode((string)$row['metadata'],true) ?: []; $row['recipient_name']=$meta['recipient_name']??null; $row['recipient_last4']=$meta['recipient_account_last4']??null; $row['can_reverse']=in_array($row['status'],['pending','processing'],true); unset($row['metadata']); $transfers[]=$row;}
jsonResponse(['success'=>true,'transfers'=>$transfers]);
