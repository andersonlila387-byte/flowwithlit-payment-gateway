<?php
declare(strict_types=1);

require_once __DIR__.'/../_admin.php';

$input = adminMethod(['GET']);
$pdo = Database::getInstance();
[$session, $role] = requireAdmin($pdo, $input);

$period = (string) ($input['period'] ?? 'month');
$periods = [
    'today' => ['start'=>gmdate('Y-m-d 00:00:00'), 'end'=>gmdate('Y-m-d 00:00:00', strtotime('+1 day'))],
    '7d' => ['start'=>gmdate('Y-m-d H:i:s', strtotime('-7 days')), 'end'=>gmdate('Y-m-d H:i:s')],
    'month' => ['start'=>gmdate('Y-m-01 00:00:00'), 'end'=>gmdate('Y-m-d H:i:s')],
];
if (!isset($periods[$period])) jsonResponse(['success'=>false, 'error'=>'Invalid reporting period.'], 422);
$window = $periods[$period];

$activeCurrencies = $pdo->query("SELECT code,name,kind,decimals FROM currencies WHERE is_enabled=1 AND (code='NGN' OR kind='crypto') ORDER BY CASE WHEN code='NGN' THEN 0 ELSE 1 END,code")->fetchAll();
$activeCodes = array_column($activeCurrencies, 'code');
$currency = strtoupper(trim((string) ($input['currency'] ?? 'NGN')));
if (!in_array($currency, $activeCodes, true)) jsonResponse(['success'=>false, 'error'=>'That currency is not enabled for operations reporting.'], 422);

$healthQuery = $pdo->prepare("SELECT
    COALESCE(SUM(CASE WHEN p.status='succeeded' THEN 1 ELSE 0 END),0) successful_count,
    COALESCE(SUM(CASE WHEN p.status='succeeded' THEN t.amount ELSE 0 END),0) successful_volume,
    COALESCE(SUM(CASE WHEN p.status='succeeded' THEN t.fee_amount ELSE 0 END),0) fees_collected,
    COALESCE(SUM(CASE WHEN p.status='failed' OR t.status='failed' THEN 1 ELSE 0 END),0) failed_count,
    COALESCE(SUM(CASE WHEN p.status='pending' OR t.status IN ('initiated','pending','processing') THEN 1 ELSE 0 END),0) processing_count,
    COUNT(*) total_count
 FROM payments p INNER JOIN transactions t ON t.id=p.transaction_id
 WHERE t.currency_code=? AND t.created_at>=? AND t.created_at<?");
$healthQuery->execute([$currency, $window['start'], $window['end']]);
$health = $healthQuery->fetch() ?: [];
$totalCount = (int) ($health['total_count'] ?? 0);
$failedCount = (int) ($health['failed_count'] ?? 0);

$linkQuery = $pdo->prepare("SELECT
 COALESCE(SUM(status='active'),0) active,COALESCE(SUM(status='disabled'),0) disabled,COALESCE(SUM(status='expired'),0) expired
 FROM payment_links WHERE currency_code=?");
$linkQuery->execute([$currency]);
$links = $linkQuery->fetch() ?: [];
$linkActivity = $pdo->prepare("SELECT COALESCE(SUM(p.status='succeeded'),0) paid,COALESCE(SUM(p.status='pending'),0) pending
 FROM payments p INNER JOIN transactions t ON t.id=p.transaction_id WHERE t.currency_code=? AND t.created_at>=? AND t.created_at<?");
$linkActivity->execute([$currency, $window['start'], $window['end']]);
$links = array_merge($links, $linkActivity->fetch() ?: []);

$queues = [];
if (in_array($role, ['finance','super_admin'], true)) {
    $query = $pdo->prepare("SELECT COUNT(*) count,COALESCE(SUM(amount+fee_amount),0) total,MIN(created_at) oldest_at FROM payout_requests WHERE currency_code=? AND status IN ('pending','approved','processing')");
    $query->execute([$currency]); $row = $query->fetch() ?: [];
    $queues[] = ['label'=>'Payout requests awaiting completion','count'=>(int)($row['count']??0),'currency'=>$currency,'total'=>(string)($row['total']??'0'),'oldest_at'=>$row['oldest_at']??null,'href'=>'withdraw.php'];
    $query = $pdo->prepare("SELECT COUNT(*) count,MIN(created_at) oldest_at FROM transactions WHERE currency_code=? AND status IN ('processing','failed') AND created_at>=?");
    $query->execute([$currency, $window['start']]); $row = $query->fetch() ?: [];
    $queues[] = ['label'=>'Processing or failed transactions','count'=>(int)($row['count']??0),'currency'=>null,'total'=>null,'oldest_at'=>$row['oldest_at']??null,'href'=>'transfer.php'];
}
if (in_array($role, ['reviewer','super_admin'], true)) {
    $row = $pdo->query("SELECT COUNT(*) count,MIN(created_at) oldest_at FROM businesses WHERE verification_status IN ('submitted','under_review')")->fetch() ?: [];
    $queues[] = ['label'=>'Business verification submissions','count'=>(int)($row['count']??0),'currency'=>null,'total'=>null,'oldest_at'=>$row['oldest_at']??null,'href'=>'users.php#identity-verification'];
}
if (in_array($role, ['support','super_admin'], true)) {
    $row = $pdo->query("SELECT COUNT(*) count,MIN(updated_at) oldest_at FROM users WHERE status='suspended'")->fetch() ?: [];
    $queues[] = ['label'=>'Suspended customer accounts','count'=>(int)($row['count']??0),'currency'=>null,'total'=>null,'oldest_at'=>$row['oldest_at']??null,'href'=>'users.php#users-list'];
}

$activity = [];
if (in_array($role, ['finance','super_admin'], true)) {
    $recent = $pdo->prepare("SELECT reference,type,status,currency_code,amount,provider,created_at FROM transactions WHERE currency_code=? ORDER BY created_at DESC LIMIT 12");
    $recent->execute([$currency]); $activity = $recent->fetchAll();
}

jsonResponse([
 'success'=>true,'role'=>$role,'generated_at'=>gmdate('c'),'period'=>$period,'currency'=>$currency,'active_currencies'=>$activeCurrencies,
 'payment_health'=>[
  'successful_count'=>(int)($health['successful_count']??0),'successful_volume'=>(string)($health['successful_volume']??'0'),
  'failed_count'=>$failedCount,'processing_count'=>(int)($health['processing_count']??0),'fees_collected'=>(string)($health['fees_collected']??'0'),
  'failure_rate'=>$totalCount ? round($failedCount/$totalCount*100,1) : 0,
 ],
 'payment_links'=>['active'=>(int)($links['active']??0),'disabled'=>(int)($links['disabled']??0),'expired'=>(int)($links['expired']??0),'paid'=>(int)($links['paid']??0),'pending'=>(int)($links['pending']??0)],
 'queues'=>$queues,'recent_activity'=>$activity,
]);

