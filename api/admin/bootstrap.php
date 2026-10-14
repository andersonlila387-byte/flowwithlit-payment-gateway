<?php
declare(strict_types=1);

require_once __DIR__.'/../_admin.php';
header('Cache-Control: no-store');

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
if (!in_array($method, ['GET','POST'], true)) jsonResponse(['success'=>false,'error'=>'Method not allowed.'],405);
$pdo = Database::getInstance();
$adminCount = (int) $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
$isLocal = in_array((string) ($_SERVER['REMOTE_ADDR'] ?? ''), ['127.0.0.1', '::1'], true);
if ($method === 'GET') jsonResponse(['success'=>true,'bootstrap_available'=>$adminCount === 0 && $isLocal]);
if ($adminCount > 0) jsonResponse(['success'=>false,'error'=>'Initial administrator setup is no longer available.'],403);
if (!$isLocal) jsonResponse(['success'=>false,'error'=>'Initial administrator setup must be completed locally on the server.'],403);

$input = getJsonInput();
$fullName = trim((string) ($input['full_name'] ?? ''));
$email = strtolower(trim((string) ($input['email'] ?? '')));
$password = (string) ($input['password'] ?? '');
if ($fullName === '' || mb_strlen($fullName) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12) {
    jsonResponse(['success'=>false,'error'=>'Enter a name, a valid email, and a password of at least 12 characters.'],422);
}
if ((int) $pdo->query("SELECT GET_LOCK('flowwithlit_admin_bootstrap',10)")->fetchColumn() !== 1) jsonResponse(['success'=>false,'error'=>'Initial setup is busy. Please try again.'],423);
try {
    $pdo->beginTransaction();
    if ((int) $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn() > 0) throw new RuntimeException('already-configured');
    $insert = $pdo->prepare("INSERT INTO admin_users(uuid,full_name,email,password_hash,role,status) VALUES(?,?,?,?, 'super_admin','active')");
    $insert->execute([generateUUID(), $fullName, $email, hashPassword($password)]);
    $adminId = (int) $pdo->lastInsertId();
    adminAudit($pdo, $adminId, 'admin.bootstrap', 'admin_user', (string) $adminId, ['role'=>'super_admin']);
    $session = createAdminSession($pdo, $adminId, $_SERVER['HTTP_USER_AGENT'] ?? 'Admin console', $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
    $pdo->commit();
    jsonResponse(['success'=>true,'token'=>$session['token'],'expires_at'=>$session['expires_at'],'role'=>'super_admin','message'=>'The first super administrator is ready.'],201);
} catch (Throwable $error) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('Admin bootstrap failed: '.$error->getMessage());
    jsonResponse(['success'=>false,'error'=>'Initial administrator setup could not be completed.'],409);
} finally { $pdo->query("SELECT RELEASE_LOCK('flowwithlit_admin_bootstrap')"); }

