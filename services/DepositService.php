<?php
declare(strict_types=1);
require_once __DIR__.'/Money.php';require_once __DIR__.'/LedgerService.php';

final class DepositService {
    public function __construct(private PDO $db) {}
    /** Credits only a provider-confirmed receiving account deposit. Unknown matches are rejected for review. */
    public function credit(string $provider,string $eventId,string $providerReference,string $accountNumber,string $currency,mixed $value,array $payload):array {
        if(!preg_match('/^[A-Za-z0-9_.:-]{3,191}$/D',$providerReference))throw new InvalidArgumentException('Provider reference is required.');
        $amount=Money::amount($value,18);$hash=hash('sha256',preg_replace('/\s+/','',$accountNumber));
        $this->db->beginTransaction();
        try {
            $q=$this->db->prepare("SELECT r.*,w.currency_code,w.available_balance FROM wallet_receiving_accounts r JOIN wallets w ON w.id=r.wallet_id WHERE r.provider=? AND r.account_number_hash=? AND r.currency_code=? AND r.status='active' FOR UPDATE");$q->execute([$provider,$hash,$currency]);$r=$q->fetch();
            if(!$r)throw new DomainException('Deposit destination could not be matched.');
            $q=$this->db->prepare('INSERT INTO gateway_deposit_events(provider,provider_reference,event_id,receiving_account_id,currency_code,amount,payload) VALUES(?,?,?,?,?,?,?)');$q->execute([$provider,$providerReference,$eventId?:null,$r['id'],$currency,$amount,json_encode($payload,JSON_THROW_ON_ERROR)]);$event=(int)$this->db->lastInsertId();
            $ref='DEP-'.strtoupper(bin2hex(random_bytes(10)));$key=hash('sha256','deposit|'.$provider.'|'.$providerReference);
            $this->db->prepare("INSERT INTO transactions(uuid,user_id,type,status,destination_wallet_id,currency_code,amount,fee_amount,reference,idempotency_key,provider,provider_reference,metadata,completed_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,UTC_TIMESTAMP())")->execute([generateUUID(),$r['user_id'],'deposit','completed',$r['wallet_id'],$currency,$amount,'0',$ref,$key,$provider,$providerReference,json_encode(['channel'=>'provider_deposit','event_id'=>$eventId])]);$tx=(int)$this->db->lastInsertId();
            $ledger=new LedgerService($this->db);$ledger->post($tx,'deposit:'.$provider.':'.$providerReference,$currency,[[$ledger->account('settlement:'.$provider.':'.$currency,'asset',$currency),'debit',$amount],[$ledger->wallet((int)$r['wallet_id'],$currency),'credit',$amount]]);
            $this->db->prepare('UPDATE wallets SET available_balance=available_balance+?,updated_at=UTC_TIMESTAMP() WHERE id=?')->execute([$amount,$r['wallet_id']]);$this->db->prepare('UPDATE gateway_deposit_events SET transaction_id=? WHERE id=?')->execute([$tx,$event]);
            $this->db->commit();return ['success'=>true,'duplicate'=>false,'reference'=>$ref,'transaction_id'=>$tx,'amount'=>$amount,'currency'=>$currency];
        }catch(PDOException $e){if($this->db->inTransaction())$this->db->rollBack();if((int)$e->errorInfo[1]===1062){$this->db->beginTransaction();$q=$this->db->prepare('SELECT t.reference FROM transactions t WHERE t.provider=? AND t.provider_reference=? LIMIT 1');$q->execute([$provider,$providerReference]);$ref=$q->fetchColumn();$this->db->commit();return ['success'=>true,'duplicate'=>true,'reference'=>$ref];}throw $e;}catch(Throwable $e){if($this->db->inTransaction())$this->db->rollBack();throw $e;}
    }
}
