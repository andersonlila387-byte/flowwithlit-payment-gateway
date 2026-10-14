<?php
declare(strict_types=1);

require_once __DIR__.'/../_admin.php';
header('Cache-Control: no-store');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') jsonResponse(['success'=>false,'error'=>'Method not allowed.'],405);

$input = getJsonInput();
$email = strtolower(trim((string) ($input['email'] ?? '')));
$password = (string) ($input['password'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') jsonResponse(['success'=>false,'error'=>'Enter your administrator email and password.'],422);
$pdo = Database::getInstance();
$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$attempt = $pdo->prepare('SELECT attempts,last_attempt FROM admin_login_attempts WHERE ip_address=? AND email=? LIMIT 1');
$attempt->execute([$ip, $email]); $row = $attempt->fetch();
if ($row && strtotime((string) $row['last_attempt']) > time() - 900 && (int) $row['attempts'] >= 5) jsonResponse(['success'=>false,'error'=>'Too many sign-in attempts. Please try again later.'],429);

$statement = $pdo->prepare('SELECT id,uuid,full_name,email,password_hash,two_factor_enabled,status,role FROM admin_users WHERE email=? LIMIT 1');
$statement->execute([$email]); $admin = $statement->fetch();
if (!$admin || $admin['status'] !== 'active' || !verifyPassword($password, (string) $admin['password_hash'])) {
    $pdo->prepare('INSERT INTO admin_login_attempts(ip_address,email,attempts,last_attempt) VALUES(?,?,1,UTC_TIMESTAMP()) ON DUPLICATE KEY UPDATE attempts=attempts+1,last_attempt=UTC_TIMESTAMP()')->execute([$ip, $email]);
    jsonResponse(['success'=>false,'error'=>'Invalid administrator credentials.'],401);
}
$pdo->prepare('DELETE FROM admin_login_attempts WHERE ip_address=? AND email=?')->execute([$ip, $email]);
$pdo->prepare('UPDATE admin_users SET last_login_at=UTC_TIMESTAMP() WHERE id=?')->execute([(int) $admin['id']]);
$session = createAdminSession($pdo, (int) $admin['id'], $_SERVER['HTTP_USER_AGENT'] ?? 'Admin console', $ip, !(bool) $admin['two_factor_enabled']);
jsonResponse(['success'=>true,'token'=>$session['token'],'expires_at'=>$session['expires_at'],'requires_2fa'=>(bool)$admin['two_factor_enabled'],'role'=>$admin['role'],'admin'=>['uuid'=>$admin['uuid'],'full_name'=>$admin['full_name'],'email'=>$admin['email']]]);

