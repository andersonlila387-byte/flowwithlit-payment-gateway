<?php
declare(strict_types=1);
require_once __DIR__.'/LedgerService.php';
require_once __DIR__.'/PrivateStorage.php';
require_once __DIR__.'/BankVerificationService.php';
require_once __DIR__.'/AccountLimits.php';

final class WalletOperations {
    private LedgerService $ledger;
    public function __construct(private PDO $db) { $this->ledger=new LedgerService($db); }

    public function currency(string $code): array {
        $q=$this->db->prepare('SELECT code,decimals,kind FROM currencies WHERE code=? AND is_enabled=1'); $q->execute([$code]);
        $c=$q->fetch(); if (!$c) throw new InvalidArgumentException('Currency is unavailable.'); return $c;
    }

    /** Serialized PIN checks prevent simultaneous attempts bypassing the lockout. */
    public function authorize(int $uid, string $pin): void {
        $this->db->beginTransaction();
        try {
            $q=$this->db->prepare('SELECT status,email_verified,withdrawal_pin_hash,pin_failed_attempts,pin_locked_until FROM users WHERE id=? FOR UPDATE'); $q->execute([$uid]); $u=$q->fetch();
            if (!$u || $u['status']!=='active' || !$u['email_verified']) throw new DomainException('A verified active account is required.');
            if ($u['pin_locked_until'] && strtotime($u['pin_locked_until'].' UTC')>time()) throw new DomainException('PIN attempts are temporarily locked. Try again later.');
            if (!preg_match('/^[0-9]{4}$/D',$pin) || !password_verify($pin,(string)$u['withdrawal_pin_hash'])) {
                $attempts=(int)$u['pin_failed_attempts']+1;
                $this->db->prepare('UPDATE users SET pin_failed_attempts=?,pin_locked_until=? WHERE id=?')->execute([$attempts>=5?0:$attempts,$attempts>=5?gmdate('Y-m-d H:i:s',time()+900):null,$uid]);
                $this->db->commit(); throw new DomainException('Invalid withdrawal PIN.');
            }
            $this->db->prepare('UPDATE users SET pin_failed_attempts=0,pin_locked_until=NULL WHERE id=?')->execute([$uid]);
            $this->db->commit();
        } catch(Throwable $e) { if($this->db->inTransaction())$this->db->rollBack(); throw $e; }
    }

    private function key(int $uid, string $operation, string $key): string {
        if (!preg_match('/^[A-Za-z0-9_.:-]{16,128}$/D',$key)) throw new InvalidArgumentException('Supply a stable Idempotency-Key of 16–128 characters.');
        return hash('sha256',$uid.'|'.$operation.'|'.$key);
    }
    private function lockUsers(array $ids): void {
        sort($ids,SORT_NUMERIC); $q=$this->db->prepare('SELECT status,email_verified FROM users WHERE id=? FOR UPDATE');
        foreach(array_unique($ids) as $id) { $q->execute([$id]); $u=$q->fetch(); if(!$u||$u['status']!=='active'||!$u['email_verified']) throw new DomainException('A verified active account is required.'); }
    }
    private function replay(string $key,string $fingerprint): ?array {
        $q=$this->db->prepare('SELECT reference,status,request_hash FROM transactions WHERE idempotency_key=?'); $q->execute([$key]); $row=$q->fetch();
        if(!$row)return null;
        if(!hash_equals((string)$row['request_hash'],$fingerprint))throw new DomainException('Idempotency key already used for a different request.');
        return ['success'=>true,'reference'=>$row['reference'],'status'=>$row['status'],'duplicate'=>true];
    }
    private function personal(int $uid,string $currency): array {
        $q=$this->db->prepare("SELECT * FROM wallets WHERE user_id=? AND currency_code=? AND business_id IS NULL AND wallet_type='personal' AND status='active' ORDER BY id FOR UPDATE"); $q->execute([$uid,$currency]); $rows=$q->fetchAll();
        if(count($rows)!==1)throw new DomainException('Exactly one active personal wallet is required for this currency.');
        return $rows[0];
    }
    private function createTransaction(int $uid,string $type,string $currency,string $amount,?int $src,?int $dst,string $key,string $hash,array $meta,string $status): array {
        $ref=strtoupper(substr($type,0,3)).'-'.strtoupper(bin2hex(random_bytes(12)));
        $q=$this->db->prepare('INSERT INTO transactions(uuid,user_id,type,status,source_wallet_id,destination_wallet_id,currency_code,amount,fee_amount,reference,idempotency_key,request_hash,provider,metadata,completed_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $q->execute([generateUUID(),$uid,$type,$status,$src,$dst,$currency,$amount,'0',$ref,$key,$hash,'flowwithlit',json_encode($meta,JSON_THROW_ON_ERROR),$status==='completed'?gmdate('Y-m-d H:i:s'):null]);
        return [(int)$this->db->lastInsertId(),$ref];
    }
    private function audit(int $uid,string $action,string $reference,array $context=[]): void {
        $this->db->prepare('INSERT INTO audit_logs(user_id,action,entity_type,entity_id,context) VALUES(?,?,?,?,?)')->execute([$uid,$action,'transaction',$reference,json_encode($context,JSON_THROW_ON_ERROR)]);
    }

    public function transfer(int $uid,string $recipient,string $currency,mixed $value,string $key,string $note=''): array {
        $c=$this->currency($currency); $amount=Money::amount($value,(int)$c['decimals']);
        $idempotency=$this->key($uid,'internal_transfer',$key);
        $recipient=strtolower(trim($recipient));
        if(!preg_match('/^@[a-z0-9_]{3,50}$/D',$recipient)) throw new InvalidArgumentException('Enter the recipient’s Flow Tag.');
        $q=$this->db->prepare('SELECT id FROM users WHERE flow_tag=?');$q->execute([$recipient]);$dest=(int)$q->fetchColumn();
        if(!$dest||$dest===$uid)throw new InvalidArgumentException('Choose another valid Flowwithlit recipient.');
        $hash=hash('sha256',json_encode([$dest,$currency,$amount,$note]));
        $this->db->beginTransaction();
        try {
            $this->lockUsers([$uid,$dest]);
            if($prior=$this->replay($idempotency,$hash)){ $this->db->commit(); return $prior; }
            $ids=[$uid,$dest];sort($ids,SORT_NUMERIC);$wallets=[];foreach($ids as $id)$wallets[$id]=$this->personal($id,$currency);
            $src=$wallets[$uid];$dst=$wallets[$dest];
            (new AccountLimits($this->db))->outgoing($uid,'transfer',$currency,$amount);
            (new AccountLimits($this->db))->incoming($dest,(int)$dst['id'],$currency,$amount,(string)$dst['available_balance']);
            if(Money::compare($src['available_balance'],$amount)<0)throw new DomainException('Insufficient wallet balance.');
            [$tx,$ref]=$this->createTransaction($uid,'transfer',$currency,$amount,(int)$src['id'],(int)$dst['id'],$idempotency,$hash,['channel'=>'internal','recipient_tag'=>$recipient,'note'=>mb_substr($note,0,191)],'completed');
            $this->ledger->post($tx,'internal:'.$tx,$currency,[[$this->ledger->wallet((int)$src['id'],$currency),'debit',$amount],[$this->ledger->wallet((int)$dst['id'],$currency),'credit',$amount]]);
            $this->db->prepare('UPDATE wallets SET available_balance=available_balance-? WHERE id=?')->execute([$amount,$src['id']]);
            $this->db->prepare('UPDATE wallets SET available_balance=available_balance+? WHERE id=?')->execute([$amount,$dst['id']]);
            $this->audit($uid,'wallet.internal_transfer',$ref,['recipient_user_id'=>$dest]);
            $this->db->prepare('INSERT INTO notifications(user_id,type,title,body,data) VALUES(?,?,?,?,?)')->execute([$dest,'transfer.received','Transfer received','You received '.$amount.' '.$currency,json_encode(['reference'=>$ref])]);
            $this->db->commit();return ['success'=>true,'reference'=>$ref,'status'=>'completed','fee'=>'0'];
        }catch(Throwable $e){if($this->db->inTransaction())$this->db->rollBack();throw $e;}
    }

    public function requestWithdrawal(int $uid,string $currency,mixed $value,array $destination,string $key): array {
        $c=$this->currency($currency);$amount=Money::amount($value,(int)$c['decimals']);$idempotency=$this->key($uid,'withdrawal',$key);
        if($c['kind']==='fiat') {
            if($currency!=='NGN'||!preg_match('/^[0-9]{10}$/D',(string)($destination['account_number']??''))||!preg_match('/^[0-9]{3,6}$/D',(string)($destination['bank_code']??'')))throw new InvalidArgumentException('A Nigerian bank code and ten-digit account number are required.');
            $dest=['account_number'=>$destination['account_number'],'bank_code'=>$destination['bank_code']];
            $type='bank';$display=$dest['bank_code'].' / ****'.substr($dest['account_number'],-4);
        } else {
            $address=trim((string)($destination['address']??''));$network=strtoupper(trim((string)($destination['network']??'')));
            if(!preg_match('/^[A-Za-z0-9:_-]{16,180}$/D',$address)||!preg_match('/^[A-Z0-9_-]{2,30}$/D',$network))throw new InvalidArgumentException('A destination address and network are required.');
            $dest=['address'=>$address,'network'=>$network,'memo'=>mb_substr((string)($destination['memo']??''),0,100)];$type='crypto';$display=$network.' / '.substr($address,0,6).'…'.substr($address,-6);
        }
        $hash=hash('sha256',json_encode([$currency,$amount,$dest]));
        $this->db->beginTransaction();
        try {
            $this->lockUsers([$uid]);
            if($prior=$this->replay($idempotency,$hash)){ $this->db->commit();return $prior; }
            if($type==='bank')$dest=(new BankVerificationService($this->db))->verified($uid,(string)($destination['verification_token']??''),$dest['account_number'],$dest['bank_code']);
            $w=$this->personal($uid,$currency);if(Money::compare($w['available_balance'],$amount)<0)throw new DomainException('Insufficient wallet balance.');
            (new AccountLimits($this->db))->outgoing($uid,'withdrawal',$currency,$amount);
            [$tx,$ref]=$this->createTransaction($uid,'withdrawal',$currency,$amount,(int)$w['id'],null,$idempotency,$hash,['channel'=>$type==='bank'?'bank_transfer':'crypto','destination_display'=>$display],'pending');
            $due=gmdate('Y-m-d H:i:s',time()+86400);
            $q=$this->db->prepare("INSERT INTO payout_requests(uuid,user_id,wallet_id,currency_code,amount,fee_amount,status,transaction_id,execution_mode,destination_type,destination_encrypted,destination_display,due_at) VALUES(?,?,?,?,?,0,'pending',?,'manual',?,?,?,?)");
            $q->execute([generateUUID(),$uid,$w['id'],$currency,$amount,$tx,$type,PrivateStorage::encrypt(json_encode($dest,JSON_THROW_ON_ERROR),'payout:'.$ref),$display,$due]);
            $payout=(int)$this->db->lastInsertId();
            $this->ledger->post($tx,'reserve:'.$tx,$currency,[[$this->ledger->wallet((int)$w['id'],$currency),'debit',$amount],[$this->ledger->wallet((int)$w['id'],$currency,true),'credit',$amount]]);
            $this->db->prepare('UPDATE wallets SET available_balance=available_balance-?,pending_balance=pending_balance+? WHERE id=?')->execute([$amount,$amount,$w['id']]);
            $this->audit($uid,'wallet.withdrawal_requested',$ref,['payout_id'=>$payout,'due_at'=>$due]);
            $this->db->commit();return ['success'=>true,'reference'=>$ref,'status'=>'pending','payout_id'=>$payout,'due_at'=>$due,'execution_mode'=>'manual','message'=>'Withdrawal queued for administrator processing within 24 hours.'];
        }catch(Throwable $e){if($this->db->inTransaction())$this->db->rollBack();throw $e;}
    }

    /** Caller authenticates finance/super-admin before calling. No unverified auto refunds. */
    public function reviewWithdrawal(int $payoutId,int $adminId,string $status,string $note,string $providerReference=''): array {
        if(!in_array($status,['approved','processing','paid','failed','cancelled'],true)||mb_strlen($note)<10||mb_strlen($note)>500)throw new InvalidArgumentException('Select a valid status and include a review note (10–500 characters).');
        if($status==='paid'&&($providerReference===''||strlen($providerReference)>191))throw new InvalidArgumentException('A bank or blockchain payout reference is required.');
        $this->db->beginTransaction();
        try {
            $q=$this->db->prepare('SELECT * FROM payout_requests WHERE id=? FOR UPDATE');$q->execute([$payoutId]);$p=$q->fetch();
            if(!$p||!$p['transaction_id'])throw new DomainException('Payout has no linked reservation; reconcile the legacy request first.');
            if(in_array($p['status'],['paid','failed','cancelled'],true)){
                if($p['status']!==$status)throw new DomainException('Payout is already finalized.');
                $this->db->commit();return ['success'=>true,'status'=>$status,'duplicate'=>true];
            }
            if($p['execution_mode']!=='manual')throw new DomainException('Automated payouts must be reconciled with the provider.');
            $allowed=['pending'=>['approved','processing','paid','failed','cancelled'],'approved'=>['processing','paid','failed','cancelled'],'processing'=>['paid','failed','cancelled']];
            if(!in_array($status,$allowed[$p['status']]??[],true))throw new DomainException('Invalid payout status transition.');
            $q=$this->db->prepare('SELECT id,status FROM transactions WHERE id=? FOR UPDATE');$q->execute([$p['transaction_id']]);$t=$q->fetch();
            if(!$t||!in_array($t['status'],['pending','processing'],true))throw new DomainException('Reservation state mismatch.');
            $total=Money::add($p['amount'],$p['fee_amount']);$currency=$p['currency_code'];$wallet=(int)$p['wallet_id'];
            if(in_array($status,['paid','failed','cancelled'],true)){
                $q=$this->db->prepare('SELECT pending_balance FROM wallets WHERE id=? FOR UPDATE');$q->execute([$wallet]);$pending=$q->fetchColumn();
                if($pending===false||Money::compare($pending,$total)<0)throw new DomainException('Reserved balance mismatch; reconciliation is required.');
                $debit=$this->ledger->wallet($wallet,$currency,true);
                $credit=$status==='paid'?$this->ledger->account('settlement:manual:'.$currency,'asset',$currency):$this->ledger->wallet($wallet,$currency);
                $this->ledger->post((int)$t['id'],'payout-final:'.$t['id'],$currency,[[$debit,'debit',$total],[$credit,'credit',$total]]);
                $this->db->prepare('UPDATE wallets SET pending_balance=pending_balance-?,available_balance=available_balance+? WHERE id=?')->execute([$total,$status==='paid'?'0':$total,$wallet]);
            }
            $txStatus=match($status){'paid'=>'completed','failed'=>'failed','cancelled'=>'cancelled',default=>'processing'};
            $this->db->prepare('UPDATE transactions SET status=?,provider_reference=?,completed_at=? WHERE id=?')->execute([$txStatus,$providerReference?:null,in_array($status,['paid','failed','cancelled'],true)?gmdate('Y-m-d H:i:s'):null,$t['id']]);
            $this->db->prepare('UPDATE payout_requests SET status=?,reviewed_by_admin_id=?,reviewed_at=UTC_TIMESTAMP(),review_note=?,provider_reference=? WHERE id=?')->execute([$status,$adminId,$note,$providerReference?:null,$payoutId]);
            $this->db->prepare('INSERT INTO audit_logs(admin_user_id,action,entity_type,entity_id,context) VALUES(?,?,?,?,?)')->execute([$adminId,'payout.reviewed','payout',(string)$payoutId,json_encode(['status'=>$status,'note'=>$note,'provider_reference'=>$providerReference])]);
            $this->db->commit();return ['success'=>true,'status'=>$status];
        }catch(Throwable $e){if($this->db->inTransaction())$this->db->rollBack();throw $e;}
    }
}
