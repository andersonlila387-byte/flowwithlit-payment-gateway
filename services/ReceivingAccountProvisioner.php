<?php
declare(strict_types=1);
require_once __DIR__.'/OnePipeClient.php';
require_once __DIR__.'/PrivateStorage.php';
require_once __DIR__.'/GatewayJobQueue.php';

final class ReceivingAccountProvisioner {
    public function __construct(private PDO $db) {}
    public function provision(int $uid,string $currency='NGN'):array {
        if($currency!=='NGN')throw new InvalidArgumentException('Only NGN receiving accounts are currently supported.');
        $u=$this->db->prepare("SELECT id,full_name,email,phone FROM users WHERE id=? AND status='active' AND email_verified=1");$u->execute([$uid]);$user=$u->fetch();
        if(!$user||!$user['phone'])throw new DomainException('A verified phone number is required before provisioning.');
        $w=$this->db->prepare("SELECT id FROM wallets WHERE user_id=? AND currency_code=? AND status='active' AND business_id IS NULL AND wallet_type='personal' ORDER BY id LIMIT 1");$w->execute([$uid,$currency]);$walletId=(int)$w->fetchColumn();
        if(!$walletId){$this->db->prepare("INSERT INTO wallets(uuid,user_id,currency_code,wallet_type,status) VALUES(?,?,?,'personal','active')")->execute([generateUUID(),$uid,$currency]);$walletId=(int)$this->db->lastInsertId();}
        $existing=$this->db->prepare("SELECT account_name,bank_name,account_number_display,status FROM wallet_receiving_accounts WHERE wallet_id=? AND provider='onepipe' AND currency_code=? LIMIT 1");$existing->execute([$walletId,$currency]);if($row=$existing->fetch())return ['success'=>true,'account'=>$row,'duplicate'=>true];
        $ref='FWL-'.strtoupper(bin2hex(random_bytes(16)));$parts=preg_split('/\s+/',trim($user['full_name']),2);$customer=['name'=>$user['full_name'],'first_name'=>$parts[0]??$user['full_name'],'last_name'=>$parts[1]??'','email'=>$user['email'],'phone'=>$user['phone']];
        $this->db->prepare("INSERT INTO wallet_receiving_accounts(uuid,wallet_id,user_id,provider,currency_code,status) VALUES(?,?,?,?,?,'provisioning')")->execute([generateUUID(),$walletId,$uid,'onepipe',$currency]);$id=(int)$this->db->lastInsertId();
        try{$response=(new OnePipeClient($this->db,'live'))->openAccount($customer,$ref);$account=OnePipeClient::extractAccount($response);if(!preg_match('/^[0-9]{10,20}$/',$account['account_number']))throw new RuntimeException('Provider did not return a valid account number.');$last4=substr($account['account_number'],-4);$this->db->prepare("UPDATE wallet_receiving_accounts SET provider_account_ref=?,account_name=?,bank_name=?,bank_code=?,account_number_encrypted=?,account_number_last4=?,account_number_hash=?,account_number_display=?,status='active',provider_payload=? WHERE id=?")->execute([$account['account_ref']?:$account['account_number'],$account['account_name'],$account['bank_name'],$account['bank_code'],PrivateStorage::encrypt($account['account_number'],'wallet-account'),$last4,hash('sha256',$account['account_number']),'••••'.$last4,json_encode($response,JSON_THROW_ON_ERROR),$id]);return ['success'=>true,'account'=>['account_name'=>$account['account_name'],'bank_name'=>$account['bank_name'],'account_number'=>$account['account_number'],'status'=>'active']];}catch(Throwable $e){$this->db->prepare("UPDATE wallet_receiving_accounts SET status='failed' WHERE id=?")->execute([$id]);throw $e;}
    }
}
