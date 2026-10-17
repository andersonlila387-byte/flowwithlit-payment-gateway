<?php
declare(strict_types=1);

require_once __DIR__.'/Money.php';

/** Enforces the configured account-level controls inside the same DB transaction as a money movement. */
final class AccountLimits {
    public function __construct(private PDO $db) {}

    private function level(int $userId): array {
        $user=$this->db->prepare('SELECT account_type,kyc_tier FROM users WHERE id=?'.($this->db->inTransaction()?' FOR UPDATE':''));
        $user->execute([$userId]); $user=$user->fetch();
        if(!$user) throw new DomainException('Account was not found.');
        $type=$user['account_type']==='business'?'business':'individual';
        $level=$this->db->prepare('SELECT * FROM admin_account_levels WHERE account_type=? AND rank=? LIMIT 1');
        $level->execute([$type,max(1,(int)$user['kyc_tier'])]); $level=$level->fetch();
        if(!$level) throw new DomainException('Your account level is not configured. Please contact support.');
        return $level;
    }

    private function enforce(string $limit, string $amount, string $message): void {
        if(Money::compare($limit,'0')>0 && Money::compare($amount,$limit)>0) throw new DomainException($message);
    }

    private function dailyTotal(int $userId, array $types, string $currency, bool $incoming): string {
        $marks=implode(',',array_fill(0,count($types),'?'));
        if($incoming)$sql="SELECT COALESCE(SUM(t.amount+t.fee_amount),0) FROM transactions t JOIN wallets w ON w.id=t.destination_wallet_id WHERE w.user_id=? AND t.type IN ($marks) AND t.currency_code=? AND t.status IN ('pending','processing','completed') AND t.created_at>=UTC_DATE()";
        else $sql="SELECT COALESCE(SUM(amount+fee_amount),0) FROM transactions WHERE user_id=? AND type IN ($marks) AND currency_code=? AND status IN ('pending','processing','completed') AND source_wallet_id IS NOT NULL AND created_at>=UTC_DATE()";
        $q=$this->db->prepare($sql);
        $q->execute([$userId,...$types,$currency]); return (string)$q->fetchColumn();
    }

    /** Account-level monetary limits are NGN-denominated; non-NGN rails have their own provider controls. */
    public function outgoing(int $userId, string $operation, string $currency, string $amount): void {
        if($currency!=='NGN') return;
        $level=$this->level($userId); $prefix=$operation==='withdrawal'?'withdraw':'transfer';
        $this->enforce((string)$level[$prefix.'_single'],$amount,'This amount exceeds your account-level '.$operation.' limit.');
        $used=$this->dailyTotal($userId,[$operation],$currency,false);
        $this->enforce((string)$level[$prefix.'_daily'],Money::add($used,$amount),'This would exceed your daily account-level '.$operation.' limit.');
    }

    public function incoming(int $userId, int $walletId, string $currency, string $amount, ?string $existingBalance=null): void {
        if($currency!=='NGN') return;
        $level=$this->level($userId);
        $this->enforce((string)$level['deposit_single'],$amount,'This deposit exceeds the recipient account-level limit.');
        $used=$this->dailyTotal($userId,['deposit','payment','adjustment','transfer'],$currency,true);
        $this->enforce((string)$level['deposit_daily'],Money::add($used,$amount),'This deposit would exceed the recipient daily account-level limit.');
        if($existingBalance===null){$q=$this->db->prepare('SELECT available_balance+pending_balance FROM wallets WHERE id=? FOR UPDATE');$q->execute([$walletId]);$existingBalance=(string)$q->fetchColumn();}
        $max=(string)$level['max_balance'];
        if($max!==''&&Money::compare($max,'0')>0&&Money::compare(Money::add($existingBalance,$amount),$max)>0) throw new DomainException('This deposit would exceed the recipient maximum wallet balance.');
    }

    public function canCreateCheckout(int $userId): void {
        $level=$this->level($userId);
        if(!(int)$level['api_access']) throw new DomainException('Your account level does not have API checkout access.');
    }
}
