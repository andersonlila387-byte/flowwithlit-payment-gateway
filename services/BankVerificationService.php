<?php
declare(strict_types=1);
require_once __DIR__.'/PaystackClient.php';

final class BankVerificationService {
    public function __construct(private PDO $db,private ?PaystackClient $client=null){}
    private function provider():PaystackClient {
        return $this->client??new PaystackClient(new ProviderConfig($this->db,'paystack','live'));
    }
    public function banks():array {
        $banks=[];foreach($this->provider()->banks() as $b){
            if(!empty($b['active'])&&preg_match('/^[0-9]{3,6}$/D',(string)($b['code']??'')))$banks[]=['code'=>(string)$b['code'],'name'=>(string)$b['name']];
        }
        usort($banks,fn($a,$b)=>strcmp($a['name'],$b['name']));return $banks;
    }
    public function resolve(int $uid,string $account,string $bank):array {
        if(!preg_match('/^[0-9]{10}$/D',$account)||!preg_match('/^[0-9]{3,6}$/D',$bank))throw new InvalidArgumentException('Select a bank and enter a ten-digit account number.');
        $result=$this->provider()->resolveAccount($account,$bank);
        if(($result['account_number']??'')!==$account||trim((string)($result['account_name']??''))==='')throw new DomainException('The bank could not verify this account.');
        $destination=['account_number'=>$account,'bank_code'=>$bank,'account_name'=>mb_substr(trim($result['account_name']),0,191)];
        $token=bin2hex(random_bytes(32));$hash=hash('sha256',$token);$expires=gmdate('Y-m-d H:i:s',time()+600);
        $this->db->prepare("INSERT INTO bank_destination_verifications(token_hash,user_id,provider,environment,bank_code,destination_encrypted,expires_at) VALUES(?,?,'paystack','live',?,?,?)")->execute([$hash,$uid,$bank,PrivateStorage::encrypt(json_encode($destination,JSON_THROW_ON_ERROR),'bank-verification:'.$hash),$expires]);
        return ['success'=>true,'verified'=>true,'verification_token'=>$token,'expires_at'=>$expires,'account_name'=>$destination['account_name'],'account_number_last4'=>substr($account,-4),'bank_code'=>$bank,'rail'=>'NIP'];
    }
    public function verified(int $uid,string $token,string $account,string $bank):array {
        if(!preg_match('/^[a-f0-9]{64}$/D',$token))throw new DomainException('Verify the destination bank account first.');
        $hash=hash('sha256',$token);
        $q=$this->db->prepare("SELECT destination_encrypted FROM bank_destination_verifications WHERE token_hash=? AND user_id=? AND bank_code=? AND environment='live' AND expires_at>UTC_TIMESTAMP()");$q->execute([$hash,$uid,$bank]);$cipher=$q->fetchColumn();
        if(!$cipher)throw new DomainException('Account verification expired. Verify the destination again.');
        $dest=json_decode(PrivateStorage::decrypt($cipher,'bank-verification:'.$hash),true,512,JSON_THROW_ON_ERROR);
        if($dest['account_number']!==$account||$dest['bank_code']!==$bank)throw new DomainException('Destination changed after verification.');
        return $dest;
    }
}
