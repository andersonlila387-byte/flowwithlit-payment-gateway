<?php
require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';

header('Cache-Control: no-store');
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['success'=>false,'error'=>'Method not allowed.'],405);
$input = $_SERVER['REQUEST_METHOD'] === 'POST' ? getJsonInput() : $_GET;
$pdo = Database::getInstance();
$session = validateSession($pdo, getBearerToken() ?: ($input['token'] ?? ''), 15);
if (!$session['valid'] || !empty($session['is_2fa_pending'])) jsonResponse(['success'=>false,'error'=>'Please sign in to continue.'],401);
$userId = (int)$session['user']['id'];
$currency = strtoupper(trim((string)($input['currency'] ?? 'NGN')));

$currencyQuery = $pdo->prepare("SELECT code,name,kind,decimals FROM currencies WHERE code=? AND kind='fiat' AND is_enabled=1");
$currencyQuery->execute([$currency]);
$currencyInfo = $currencyQuery->fetch();
if (!$currencyInfo) jsonResponse(['success'=>false,'error'=>'That currency is not currently available.'],422);

$walletQuery = $pdo->prepare('SELECT COALESCE(SUM(available_balance),0) available, COALESCE(SUM(pending_balance),0) pending FROM wallets WHERE user_id=? AND currency_code=? AND status IN (\'active\',\'frozen\')');
$walletQuery->execute([$userId,$currency]);
$wallet = $walletQuery->fetch() ?: ['available'=>0,'pending'=>0];
$start = new DateTimeImmutable('first day of this month 00:00:00', new DateTimeZone('UTC'));
$previousStart = $start->modify('-1 month');
$now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
$sum = function(array $types, DateTimeImmutable $from, DateTimeImmutable $to) use ($pdo,$userId,$currency): string {
    $placeholders = implode(',', array_fill(0,count($types),'?'));
    $query = $pdo->prepare("SELECT COALESCE(SUM(amount + fee_amount),0) FROM transactions WHERE user_id=? AND currency_code=? AND status='completed' AND type IN ($placeholders) AND created_at>=? AND created_at<?");
    $query->execute(array_merge([$userId,$currency],$types,[$from->format('Y-m-d H:i:s'),$to->format('Y-m-d H:i:s')]));
    return (string)$query->fetchColumn();
};
$spending = $sum(['withdrawal','transfer','payment','fee'],$start,$now);
$previousSpending = $sum(['withdrawal','transfer','payment','fee'],$previousStart,$start);
$payments = $sum(['payment'],$start,$now);
$earnings = $sum(['deposit','refund'],$start,$now);
$cardQuery = $pdo->prepare("SELECT COALESCE(SUM(t.amount+t.fee_amount),0) FROM transactions t JOIN cards c ON c.wallet_id=t.source_wallet_id WHERE t.user_id=? AND t.currency_code=? AND t.status='completed' AND t.created_at>=? AND t.created_at<?");
$cardQuery->execute([$userId,$currency,$start->format('Y-m-d H:i:s'),$now->format('Y-m-d H:i:s')]);
$cardUsage = (string)$cardQuery->fetchColumn();
$trend = ((float)$previousSpending > 0) ? round((((float)$spending - (float)$previousSpending) / (float)$previousSpending) * 100, 1) : 0;
$months = [];
$monthQuery = $pdo->prepare("SELECT DATE_FORMAT(created_at,'%Y-%m') month, SUM(CASE WHEN type IN ('withdrawal','transfer','payment','fee') THEN amount+fee_amount ELSE 0 END) spending, SUM(CASE WHEN type IN ('deposit','refund') THEN amount ELSE 0 END) earnings FROM transactions WHERE user_id=? AND currency_code=? AND status='completed' AND created_at>=DATE_SUB(UTC_TIMESTAMP(), INTERVAL 12 MONTH) GROUP BY DATE_FORMAT(created_at,'%Y-%m') ORDER BY month ASC");
$monthQuery->execute([$userId,$currency]);
foreach ($monthQuery->fetchAll() as $month) $months[] = ['month'=>$month['month'],'spending'=>(string)$month['spending'],'earnings'=>(string)$month['earnings']];
$feeQuery = $pdo->prepare("SELECT COALESCE(SUM(fee_amount),0) FROM transactions WHERE user_id=? AND currency_code=? AND status='completed' AND created_at>=? AND created_at<?");
$feeQuery->execute([$userId,$currency,$start->format('Y-m-d H:i:s'),$now->format('Y-m-d H:i:s')]);
$processingCost = (string)$feeQuery->fetchColumn();
$active = $pdo->query("SELECT code,name,kind,decimals FROM currencies WHERE kind='fiat' AND code='NGN' AND is_enabled=1 ORDER BY code")->fetchAll();

jsonResponse(['success'=>true,'currency'=>$currencyInfo,'available_currencies'=>$active,'summary'=>[
    'total_balance'=>(string)$wallet['available'], 'pending_balance'=>(string)$wallet['pending'], 'monthly_spending'=>$spending,
    'card_usage'=>$cardUsage, 'payment_overview'=>$payments, 'earning_overview'=>$earnings, 'processing_cost'=>$processingCost,
    'pipeline_output'=>$payments, 'net_efficiency'=>(string)((float)$earnings-(float)$spending), 'spending_change_percent'=>$trend,
    'period_start'=>$start->format(DateTimeInterface::ATOM), 'period_end'=>$now->format(DateTimeInterface::ATOM), 'months'=>$months
]]);
