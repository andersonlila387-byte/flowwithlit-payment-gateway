<?php
declare(strict_types=1);

require_once __DIR__.'/../_admin.php';
header('Cache-Control: no-store');

$input = adminMethod(['GET']);
$pdo = Database::getInstance();
[$session] = requireAdmin($pdo, $input);

$statement = $pdo->prepare('SELECT uuid,full_name,email,avatar_url,role,two_factor_enabled,last_login_at FROM admin_users WHERE id=? LIMIT 1');
$statement->execute([(int) $session['admin']['id']]);
$admin = $statement->fetch();
if (!$admin) jsonResponse(['success'=>false,'error'=>'Administrator profile was not found.'], 404);

jsonResponse(['success'=>true,'admin'=>$admin]);
