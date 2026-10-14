<?php
declare(strict_types=1);
require_once __DIR__.'/../_admin.php';
$input=adminMethod(['GET']); $pdo=Database::getInstance(); [$session,$role]=requireAdmin($pdo,$input); requireAdminRole($role,['super_admin'],'view audit logs');
$limit=max(1,min(200,(int)($input['limit']??100)));
$stmt=$pdo->query("SELECT a.id,a.action,a.entity_type,a.entity_id,a.ip_address,a.context,a.created_at,au.full_name admin_name,au.email admin_email,u.email customer_email FROM audit_logs a LEFT JOIN admin_users au ON au.id=a.admin_user_id LEFT JOIN users u ON u.id=a.user_id ORDER BY a.created_at DESC LIMIT {$limit}");
jsonResponse(['success'=>true,'audit_logs'=>$stmt->fetchAll()]);
