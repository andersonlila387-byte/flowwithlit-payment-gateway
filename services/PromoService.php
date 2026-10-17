<?php
declare(strict_types=1);
require_once __DIR__.'/Money.php';
require_once __DIR__.'/LedgerService.php';
require_once __DIR__.'/AccountLimits.php';

/** Server-side promo eligibility and awarding. Promo codes are never trusted from the browser. */
final class PromoService {
    public function __construct(private PDO $db) {}

    private function promo(string $code): array {
        $q=$this->db->prepare("SELECT * FROM promo_codes WHERE code=? AND status='active' AND (starts_at IS NULL OR starts_at<=UTC_TIMESTAMP()) AND (expires_at IS NULL OR expires_at>=UTC_TIMESTAMP()) FOR UPDATE");
        $q->execute([strtoupper(trim($code))]); $promo=$q->fetch();
        if(!$promo) throw new DomainException('This promo code is unavailable.');
        return $promo;
    }
    private function eligible(array $promo,int $userId,string $amount): void {
        $q=$this->db->prepare('SELECT email_verified,kyc_tier,created_at FROM users WHERE id=? FOR UPDATE');$q->execute([$userId]);$u=$q->fetch();
        if(!$u) throw new DomainException('Account was not found.');
        if($promo['audience']==='verified'&&(!(int)$u['email_verified']||(int)$u['kyc_tier']<2))throw new DomainException('This code requires a verified account.');
        if($promo['audience']==='new_users'&&strtotime((string)$u['created_at'].' UTC')<time()-30*86400)throw new DomainException('This code is only for new accounts.');
        if($promo['min_amount']!==null&&Money::compare($amount,(string)$promo['min_amount'])<0)throw new DomainException('The amount does not qualify for this promo code.');
        $q=$this->db->prepare("SELECT COUNT(*) FROM promo_redemptions WHERE promo_code_id=? AND status IN ('qualified','awarded')");$q->execute([$promo['id']]);
        if($promo['usage_limit']!==null&&(int)$q->fetchColumn()>=(int)$promo['usage_limit'])throw new DomainException('This promo code has reached its redemption limit.');
        $q=$this->db->prepare("SELECT COUNT(*) FROM promo_redemptions WHERE promo_code_id=? AND user_id=? AND status IN ('qualified','awarded')");$q->execute([$promo['id'],$userId]);
        if((int)$q->fetchColumn()>=(int)$promo['per_user_limit'])throw new DomainException('You have already used this promo code.');
    }
    private function reward(array $promo,string $amount): string {
        if($promo['reward_type']==='wallet_bonus') $reward=(string)$promo['reward_value'];
        elseif(in_array($promo['reward_type'],['cashback','fee_discount'],true)) $reward=Money::amount(((float)$amount*(float)$promo['reward_value'])/100,2);
        else $reward='0';
        if($promo['max_reward']!==null&&Money::compare($reward,(string)$promo['max_reward'])>0)$reward=(string)$promo['max_reward'];
        return $reward;
    }

    /** Hosted checkout can apply only a price discount; wallet rewards are awarded by their own flow. */
    public function prepareCheckoutDiscount(int $userId,string $code,string $amount): array {
        if(!$this->db->inTransaction())throw new LogicException('Promo qualification requires a transaction.');
        $promo=$this->promo($code);$this->eligible($promo,$userId,$amount);
        if($promo['reward_type']!=='fee_discount')throw new DomainException('This promo code is not redeemable for a checkout discount.');
        $discount=$this->reward($promo,$amount);
        if(Money::compare($discount,'0')<=0||Money::compare($discount,$amount)>=0)throw new DomainException('This promo code cannot reduce this checkout amount.');
        return ['id'=>(int)$promo['id'],'code'=>$promo['code'],'discount'=>$discount];
    }
    public function recordCheckoutRedemption(array $claim,int $userId,int $transactionId): void {
        $q=$this->db->prepare("INSERT INTO promo_redemptions(promo_code_id,user_id,transaction_id,reward_amount,currency_code,status) VALUES(?,?,?,?, 'NGN','awarded')");
        $q->execute([(int)$claim['id'],$userId,$transactionId,(string)$claim['discount']]);
    }

    /** Direct wallet redemption is deliberately limited to fixed wallet bonuses. */
    public function redeemWallet(int $userId,string $code,string $idempotencyKey): array {
        if(!preg_match('/^[A-Za-z0-9_.:-]{16,128}$/D',$idempotencyKey))throw new InvalidArgumentException('Supply a stable Idempotency-Key of 16–128 characters.');
        $key=hash('sha256',$userId.'|promo_wallet|'.$idempotencyKey);
        $this->db->beginTransaction();
        try {
            $prior=$this->db->prepare('SELECT reference,status FROM transactions WHERE idempotency_key=?');$prior->execute([$key]);
            if($row=$prior->fetch()){ $this->db->commit();return ['success'=>true,'reference'=>$row['reference'],'status'=>$row['status'],'duplicate'=>true]; }
            $promo=$this->promo($code);
            if($promo['reward_type']!=='wallet_bonus')throw new DomainException('This code can only be redeemed at checkout.');
            $this->eligible($promo,$userId,'0');$reward=$this->reward($promo,'0');
            if(Money::compare($reward,'0')<=0)throw new DomainException('This promo code has no wallet reward.');
            $q=$this->db->prepare("SELECT * FROM wallets WHERE user_id=? AND currency_code='NGN' AND wallet_type='personal' AND business_id IS NULL AND status='active' FOR UPDATE");$q->execute([$userId]);$wallet=$q->fetch();
            if(!$wallet)throw new DomainException('An active NGN wallet is required to redeem this code.');
            (new AccountLimits($this->db))->incoming($userId,(int)$wallet['id'],'NGN',$reward,(string)$wallet['available_balance']);
            $ref='PRM-'.strtoupper(bin2hex(random_bytes(10)));$meta=json_encode(['promo_code'=>$promo['code'],'reward_type'=>'wallet_bonus'],JSON_THROW_ON_ERROR);
            $q=$this->db->prepare("INSERT INTO transactions(uuid,user_id,type,status,destination_wallet_id,currency_code,amount,fee_amount,reference,idempotency_key,provider,metadata,completed_at) VALUES(?,?, 'adjustment','completed',?,'NGN',?,0,?,?, 'flowwithlit',?,UTC_TIMESTAMP())");
            $q->execute([generateUUID(),$userId,$wallet['id'],$reward,$ref,$key,$meta]);$transaction=(int)$this->db->lastInsertId();
            (new LedgerService($this->db))->post($transaction,'promo:'.$transaction,'NGN',[[(new LedgerService($this->db))->account('promo:NGN','expense','NGN'),'debit',$reward],[(new LedgerService($this->db))->wallet((int)$wallet['id'],'NGN'),'credit',$reward]]);
            $this->db->prepare('UPDATE wallets SET available_balance=available_balance+?,updated_at=UTC_TIMESTAMP() WHERE id=?')->execute([$reward,$wallet['id']]);
            $this->db->prepare("INSERT INTO promo_redemptions(promo_code_id,user_id,transaction_id,reward_amount,currency_code,status,awarded_at) VALUES(?,?,?,?, 'NGN','awarded',UTC_TIMESTAMP())")->execute([$promo['id'],$userId,$transaction,$reward]);
            $this->db->commit();return ['success'=>true,'reference'=>$ref,'status'=>'awarded','reward_amount'=>$reward,'currency'=>'NGN'];
        }catch(Throwable $e){if($this->db->inTransaction())$this->db->rollBack();throw $e;}
    }
}
