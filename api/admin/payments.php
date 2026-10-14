<?php
declare(strict_types=1);

require_once __DIR__.'/../_admin.php';
header('Cache-Control: no-store');
$input = adminMethod(['GET']);
$pdo = Database::getInstance();
[$session, $role] = requireAdmin($pdo, $input);
requireAdminRole($role, ['super_admin','finance','reviewer'], 'view payment operations');

$summary = $pdo->query("SELECT
  COUNT(*) AS total_count,
  COALESCE(SUM(CASE WHEN p.status='succeeded' THEN t.amount ELSE 0 END),0) AS collected_amount,
  SUM(p.status='succeeded') AS succeeded_count,
  SUM(p.status='pending') AS pending_count,
  SUM(p.status='failed') AS failed_count
  FROM payments p INNER JOIN transactions t ON t.id=p.transaction_id")->fetch();
$activeLinks = (int) $pdo->query("SELECT COUNT(*) FROM payment_links WHERE status='active' AND (expires_at IS NULL OR expires_at>UTC_TIMESTAMP())")->fetchColumn();
$statement = $pdo->query("SELECT p.id,p.status,p.customer_name,p.customer_email,p.created_at,pl.title,pl.currency_code,t.amount,t.fee_amount,t.reference,
  COALESCE(NULLIF(b.trading_name,''),NULLIF(b.legal_name,''),'Unassigned merchant') merchant_name
  FROM payments p INNER JOIN transactions t ON t.id=p.transaction_id LEFT JOIN payment_links pl ON pl.id=p.payment_link_id LEFT JOIN businesses b ON b.id=pl.business_id
  ORDER BY p.created_at DESC LIMIT 100");
jsonResponse(['success'=>true, 'summary'=>['collected_amount'=>(string)$summary['collected_amount'], 'succeeded_count'=>(int)$summary['succeeded_count'], 'pending_count'=>(int)$summary['pending_count'], 'failed_count'=>(int)$summary['failed_count'], 'active_links'=>$activeLinks], 'payments'=>$statement->fetchAll()]);
