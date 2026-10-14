<?php
require_once __DIR__ . '/_common.php';
$pdo = Database::getInstance();
header('Cache-Control: no-store');
if (!in_array(($_SERVER['REQUEST_METHOD'] ?? 'GET'), ['GET','POST'], true)) { http_response_code(405); exit('Method not allowed.'); }
$input = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' ? getJsonInput() : $_GET; $session = validateSession($pdo, getBearerToken() ?: ($input['token'] ?? ''), 15);
if (!$session['valid'] || !empty($session['is_2fa_pending'])) { http_response_code(401); exit('Unauthorized.'); }
$f=transactionFilters($input,$pdo); $params=[]; $where=transactionWhere($f,(int)$session['user']['id'],$params); $base=transactionBaseSql($where);
$sql="SELECT t.reference,t.type,t.status,t.currency_code,t.amount,t.fee_amount,t.created_at,p.customer_name,p.customer_email,CASE WHEN ct.id IS NOT NULL THEN 'card' WHEN c.kind='crypto' OR t.type='swap' THEN 'crypto' WHEN JSON_UNQUOTE(JSON_EXTRACT(t.metadata,'$.channel'))='apple_pay' THEN 'apple_pay' ELSE 'bank_transfer' END channel {$base} ORDER BY t.created_at DESC,t.id DESC LIMIT 10000";
$stmt=$pdo->prepare($sql); $stmt->execute($params); $safe=function($v){$v=(string)$v; return preg_match('/^[=+\-@]/',$v)?"'".$v:$v;};
header('Content-Type: text/csv; charset=UTF-8'); header('Content-Disposition: attachment; filename="flowwithlit-transactions-'.gmdate('Y-m-d').'.csv"');
$out=fopen('php://output','w'); fputcsv($out,['Reference','Type','Status','Currency','Amount','Fee','Net','Channel','Customer','Customer Email','Created At']);
foreach($stmt->fetchAll() as $r) fputcsv($out,[$safe($r['reference']),$safe($r['type']),$safe(transactionStatus($r['status'],$r['type'],null)),$safe($r['currency_code']),$safe($r['amount']),$safe($r['fee_amount']),$safe((string)((float)$r['amount']-(float)$r['fee_amount'])),$safe($r['channel']),$safe($r['customer_name']?:'—'),$safe(maskEmail($r['customer_email']??null)),$safe($r['created_at'])]);
fclose($out);
