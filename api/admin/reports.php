<?php
declare(strict_types=1);
require_once __DIR__.'/../_admin.php';
header('Cache-Control: no-store');
$input=adminMethod(['GET']);$pdo=Database::getInstance();[$session,$role]=requireAdmin($pdo,$input);requireAdminRole($role,['super_admin','finance','reviewer'],'view reports');
$from=(string)($input['from']??'');$to=(string)($input['to']??'');$group=(string)($input['group_by']??'day');
if(!preg_match('/^\\d{4}-\\d{2}-\\d{2}$/',$from)||!preg_match('/^\\d{4}-\\d{2}-\\d{2}$/',$to)||$from>$to)jsonResponse(['success'=>false,'error'=>'Choose a valid date range.'],422);
if(!in_array($group,['day','provider','type','status'],true))jsonResponse(['success'=>false,'error'=>'Invalid report grouping.'],422);
$end=(new DateTimeImmutable($to,new DateTimeZone('UTC')))->modify('+1 day')->format('Y-m-d');
$label=['day'=>'DATE(t.created_at)','provider'=>"COALESCE(NULLIF(t.provider,''),'Platform')",'type'=>'t.type','status'=>'t.status'][$group];
$sql="SELECT $label label,COUNT(p.id) payments,SUM(p.status='succeeded') successful,SUM(p.status='failed') failed,COALESCE(SUM(CASE WHEN p.status='succeeded' THEN t.amount ELSE 0 END),0) volume,COALESCE(SUM(CASE WHEN p.status='succeeded' THEN t.fee_amount ELSE 0 END),0) fees FROM payments p JOIN transactions t ON t.id=p.transaction_id WHERE t.created_at>=? AND t.created_at<? AND t.currency_code='NGN' GROUP BY label ORDER BY label";$stmt=$pdo->prepare($sql);$stmt->execute([$from,$end]);$rows=$stmt->fetchAll();
$sum=$pdo->prepare("SELECT COALESCE(SUM(CASE WHEN p.status='succeeded' THEN t.amount ELSE 0 END),0) volume,COALESCE(SUM(p.status='succeeded'),0) successful,COALESCE(SUM(p.status='failed'),0) failed,COALESCE(SUM(CASE WHEN p.status='succeeded' THEN t.fee_amount ELSE 0 END),0) fees,COALESCE(SUM(p.status='pending' OR t.status IN ('pending','processing')),0) processing FROM payments p JOIN transactions t ON t.id=p.transaction_id WHERE t.created_at>=? AND t.created_at<? AND t.currency_code='NGN'");$sum->execute([$from,$end]);$s=$sum->fetch()?:[];
jsonResponse(['success'=>true,'currency'=>'NGN','group_by'=>$group,'generated_at'=>gmdate('Y-m-d H:i:s'),'summary'=>['total_volume'=>(string)($s['volume']??0),'successful_count'=>(int)($s['successful']??0),'failed_count'=>(int)($s['failed']??0),'fees_collected'=>(string)($s['fees']??0),'rewards_paid'=>'0','processing_count'=>(int)($s['processing']??0)],'rows'=>$rows]);
