<?php
require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';

header('Cache-Control: no-store');
$pdo = Database::getInstance();
$input = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' ? getJsonInput() : $_GET;
$session = validateSession($pdo, getBearerToken() ?: ($input['token'] ?? ''), 15);
if (!$session['valid'] || !empty($session['is_2fa_pending'])) {
    jsonResponse(['success' => false, 'error' => 'Please sign in to continue.'], 401);
}
$uid = (int)$session['user']['id'];

$sql = "SELECT c.id,c.uuid,c.provider,c.card_type,c.last4,c.status,c.spending_limit,c.created_at,
        w.currency_code,w.available_balance,
        COALESCE((SELECT SUM(ct.amount) FROM card_transactions ct
          WHERE ct.card_id=c.id AND ct.status IN ('pending','completed')
          AND ct.occurred_at >= DATE_FORMAT(UTC_TIMESTAMP(), '%Y-%m-01')),0) month_spend,
        COALESCE((SELECT COUNT(*) FROM card_transactions ct2 WHERE ct2.card_id=c.id),0) transaction_count
        FROM cards c JOIN wallets w ON w.id=c.wallet_id
        WHERE c.user_id=? AND c.status <> 'terminated'
        ORDER BY c.created_at DESC,c.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$uid]);
$cards = [];
foreach ($stmt->fetchAll() as $row) {
    $limit = $row['spending_limit'] !== null ? (string)$row['spending_limit'] : null;
    $spend = (string)$row['month_spend'];
    $cards[] = [
        'id' => (int)$row['id'], 'uuid' => $row['uuid'], 'provider' => $row['provider'],
        'type' => $row['card_type'], 'last4' => $row['last4'], 'status' => $row['status'],
        'currency' => $row['currency_code'], 'spending_limit' => $limit, 'month_spend' => $spend,
        'available_balance' => (string)$row['available_balance'], 'transaction_count' => (int)$row['transaction_count'],
        'created_at' => $row['created_at'],
        // PAN, expiry, CVV, PIN and provider identifiers are intentionally never returned.
    ];
}
$summaryStmt = $pdo->prepare("SELECT COALESCE(SUM(ct.amount),0) total_spend, COUNT(DISTINCT c.id) card_count,
    SUM(CASE WHEN c.status='active' THEN 1 ELSE 0 END) active_count,
    COALESCE(SUM(CASE WHEN c.status='active' THEN c.spending_limit ELSE 0 END),0) total_limit
    FROM cards c LEFT JOIN card_transactions ct ON ct.card_id=c.id AND ct.status IN ('pending','completed')
    AND ct.occurred_at >= DATE_FORMAT(UTC_TIMESTAMP(), '%Y-%m-01') WHERE c.user_id=? AND c.status<>'terminated'");
$summaryStmt->execute([$uid]);
$summary = $summaryStmt->fetch() ?: [];
jsonResponse(['success'=>true,'cards'=>$cards,'summary'=>[
    'total_spend'=>(string)($summary['total_spend']??0), 'card_count'=>(int)($summary['card_count']??0),
    'active_count'=>(int)($summary['active_count']??0), 'total_limit'=>(string)($summary['total_limit']??0)
]]);
