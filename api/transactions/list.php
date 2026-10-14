<?php
require_once __DIR__ . '/_common.php';
$pdo = Database::getInstance();
[$input,$userId] = transactionRequest($pdo);
$f = transactionFilters($input, $pdo); $params = [];
$where = transactionWhere($f, $userId, $params); $base = transactionBaseSql($where);
$countStmt = $pdo->prepare("SELECT COUNT(DISTINCT t.id) {$base}"); $countStmt->execute($params); $total = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($total / $f['pageSize'])); $page = min($f['page'], $totalPages); $offset = ($page-1)*$f['pageSize'];
$summaryStmt = $pdo->prepare("SELECT
    COALESCE(SUM(CASE WHEN t.status='completed' THEN t.amount + t.fee_amount ELSE 0 END),0) gross_volume,
    COALESCE(SUM(CASE WHEN t.status='completed' THEN t.amount - t.fee_amount ELSE 0 END),0) net_settled,
    COALESCE(SUM(CASE WHEN t.status='completed' THEN t.fee_amount ELSE 0 END),0) fees,
    SUM(CASE WHEN t.status='completed' THEN 1 ELSE 0 END) successful_count,
    SUM(CASE WHEN t.status IN ('failed','reversed','cancelled') THEN 1 ELSE 0 END) failed_count
    {$base}"); $summaryStmt->execute($params); $summary = $summaryStmt->fetch() ?: [];
$sql = "SELECT t.id,t.uuid,t.reference,t.type,t.status,t.currency_code,t.amount,t.fee_amount,t.provider,t.created_at,t.completed_at,
    p.customer_name,p.customer_email,p.status payment_status, card.last4,
    CASE WHEN ct.id IS NOT NULL THEN 'card' WHEN c.kind='crypto' OR t.type='swap' THEN 'crypto'
         WHEN JSON_UNQUOTE(JSON_EXTRACT(t.metadata, '$.channel'))='apple_pay' THEN 'apple_pay' ELSE 'bank_transfer' END channel
    {$base} ORDER BY t.created_at DESC, t.id DESC LIMIT {$f['pageSize']} OFFSET {$offset}";
$stmt = $pdo->prepare($sql); $stmt->execute($params); $rows = [];
foreach ($stmt->fetchAll() as $row) {
    $raw = (string)$row['status']; $uiStatus = transactionStatus($raw, (string)$row['type'], $row['payment_status'] ?? null);
    $rows[] = ['id'=>(int)$row['id'],'uuid'=>$row['uuid'],'reference'=>$row['reference'],'type'=>$row['type'],'status'=>$uiStatus,'raw_status'=>$raw,
        'currency'=>$row['currency_code'],'amount'=>(string)$row['amount'],'fee'=>(string)$row['fee_amount'],'net'=>(string)((float)$row['amount']-(float)$row['fee_amount']),
        'channel'=>$row['channel'],'provider'=>$row['provider'] ?: 'Flowwithlit','customer_name'=>$row['customer_name'] ?: '—','customer_email'=>maskEmail($row['customer_email'] ?? null),
        'card_last4'=>$row['last4'] ?: null,'created_at'=>$row['created_at'],'completed_at'=>$row['completed_at']];
}
$successful=(int)($summary['successful_count']??0); $failed=(int)($summary['failed_count']??0); $den=$successful+$failed;
$currencies=$pdo->query("SELECT code,name,kind,decimals FROM currencies WHERE is_enabled=1 ORDER BY kind='crypto', code")->fetchAll();
jsonResponse(['success'=>true,'summary'=>['gross_volume'=>(string)($summary['gross_volume']??0),'net_settled'=>(string)($summary['net_settled']??0),'fees'=>(string)($summary['fees']??0),'successful_count'=>$successful,'failed_count'=>$failed,'success_rate'=>$den?round($successful/$den*100,1):0], 'transactions'=>$rows, 'currencies'=>$currencies, 'pagination'=>['page'=>$page,'page_size'=>$f['pageSize'],'total_count'=>$total,'total_pages'=>$totalPages,'from'=>$total?($offset+1):0,'to'=>min($offset+$f['pageSize'],$total)], 'filters'=>$f]);
