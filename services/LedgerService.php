<?php
declare(strict_types=1);
require_once __DIR__.'/Money.php';
require_once __DIR__.'/../config/security.php';

/** All callers must use the same DB transaction for ledger and wallet snapshots. */
final class LedgerService {
    public function __construct(private PDO $db) {}

    public function account(string $code, string $type, string $currency, ?int $walletId = null): int {
        if (!$this->db->inTransaction()) throw new LogicException('Ledger requires a transaction.');
        $q=$this->db->prepare('INSERT INTO ledger_accounts(wallet_id,account_code,account_type,name,currency_code) VALUES(?,?,?,?,?) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)');
        $q->execute([$walletId,$code,$type,$code,$currency]);
        $q=$this->db->prepare('SELECT id,account_type,currency_code FROM ledger_accounts WHERE account_code=?');
        $q->execute([$code]); $row=$q->fetch();
        if (!$row || $row['currency_code']!==$currency || $row['account_type']!==$type) throw new LogicException('Ledger account mismatch.');
        return (int)$row['id'];
    }

    public function wallet(int $id, string $currency, bool $reserved = false): int {
        return $this->account(($reserved?'wallet_pending:':'wallet_available:').$id,'liability',$currency,$reserved?null:$id);
    }

    /** Lines: [account ID, debit|credit, amount]. A posting is immutable and unique. */
    public function post(int $transactionId, string $key, string $currency, array $lines): void {
        if (!$this->db->inTransaction()) throw new LogicException('Ledger requires a transaction.');
        $sum='0';
        foreach ($lines as [$id,$direction,$amount]) {
            $amount=Money::amount($amount);
            if (!in_array($direction,['debit','credit'],true)) throw new LogicException('Invalid ledger direction.');
            $sum=$direction==='debit'?Money::add($sum,$amount):Money::sub($sum,$amount);
            $q=$this->db->prepare('SELECT currency_code FROM ledger_accounts WHERE id=?'); $q->execute([$id]);
            if ($q->fetchColumn()!==$currency) throw new LogicException('Mixed-currency posting.');
        }
        if (count($lines)<2 || Money::compare($sum,'0')!==0) throw new LogicException('Ledger posting must balance.');
        $this->db->prepare('INSERT INTO ledger_entries(uuid,transaction_id,description,posting_key,posted_at) VALUES(?,?,?,?,UTC_TIMESTAMP())')->execute([generateUUID(),$transactionId,$key,$key]);
        $entry=(int)$this->db->lastInsertId();
        $q=$this->db->prepare('INSERT INTO ledger_lines(ledger_entry_id,ledger_account_id,direction,amount) VALUES(?,?,?,?)');
        foreach($lines as [$account,$direction,$amount]) $q->execute([$entry,$account,$direction,$amount]);
    }
}
