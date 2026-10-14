<?php
declare(strict_types=1);

require_once __DIR__.'/../_admin.php';
$input = adminMethod(['GET','POST']);
$pdo = Database::getInstance();
[$session, $role] = requireAdmin($pdo, $input);
if ($role !== 'super_admin') {
    jsonResponse(['success'=>false,'error'=>'Only super administrators can manage administrator access.'], 403);
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
    $staff = $pdo->query('SELECT uuid,full_name,email,role,status,two_factor_enabled,last_login_at,created_at,updated_at FROM admin_users ORDER BY created_at ASC')->fetchAll();
    jsonResponse(['success'=>true, 'staff'=>$staff]);
}

$action = (string) ($input['action'] ?? 'create');

if ($action === 'create') {
    $fullName = trim((string) ($input['full_name'] ?? ''));
    $email = strtolower(trim((string) ($input['email'] ?? '')));
    $password = (string) ($input['password'] ?? '');
    $newRole = (string) ($input['role'] ?? 'support');
    if ($fullName === '' || mb_strlen($fullName) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12 || !in_array($newRole, ['support','reviewer','finance','super_admin'], true)) {
        jsonResponse(['success'=>false,'error'=>'Enter a name, valid work email, password of at least 12 characters, and valid role.'], 422);
    }
    try {
        $statement = $pdo->prepare("INSERT INTO admin_users(uuid,full_name,email,password_hash,role,status) VALUES(?,?,?,?,?,'active')");
        $statement->execute([generateUUID(), $fullName, $email, hashPassword($password), $newRole]);
        $adminId = (int) $pdo->lastInsertId();
        adminAudit($pdo, (int) $session['admin']['id'], 'admin.staff_created', 'admin_user', (string) $adminId, ['role'=>$newRole]);
        jsonResponse(['success'=>true,'message'=>'Administrator account created successfully.'], 201);
    } catch (PDOException $error) {
        jsonResponse(['success'=>false,'error'=>'An administrator already uses that email address.'], 409);
    }
}

if ($action === 'enable') {
    $uuid = trim((string) ($input['admin_uuid'] ?? ''));
    if (!preg_match('/^[a-f0-9-]{36}$/i', $uuid)) jsonResponse(['success'=>false,'error'=>'Invalid administrator reference.'], 422);
    $target = $pdo->prepare('SELECT id,role,status FROM admin_users WHERE uuid=? LIMIT 1');
    $target->execute([$uuid]); 
    $admin = $target->fetch();
    if (!$admin) jsonResponse(['success'=>false,'error'=>'Administrator record not found.'], 404);

    $pdo->prepare("UPDATE admin_users SET status='active' WHERE id=?")->execute([(int) $admin['id']]);
    adminAudit($pdo, (int) $session['admin']['id'], 'admin.staff_enabled', 'admin_user', (string) $admin['id']);
    jsonResponse(['success'=>true,'message'=>'Administrator access enabled.']);
}

if ($action === 'disable') {
    $uuid = trim((string) ($input['admin_uuid'] ?? ''));
    if (!preg_match('/^[a-f0-9-]{36}$/i', $uuid)) jsonResponse(['success'=>false,'error'=>'Invalid administrator reference.'], 422);
    $target = $pdo->prepare('SELECT id,role,status FROM admin_users WHERE uuid=? LIMIT 1');
    $target->execute([$uuid]); 
    $admin = $target->fetch();
    if (!$admin) jsonResponse(['success'=>false,'error'=>'Administrator record not found.'], 404);
    if ($admin['role'] === 'super_admin' && $admin['status'] === 'active' && (int) $pdo->query("SELECT COUNT(*) FROM admin_users WHERE role='super_admin' AND status='active'")->fetchColumn() <= 1) {
        jsonResponse(['success'=>false,'error'=>'At least one active super administrator is required.'], 409);
    }
    $pdo->prepare("UPDATE admin_users SET status='disabled' WHERE id=?")->execute([(int) $admin['id']]);
    $pdo->prepare('DELETE FROM admin_sessions WHERE admin_user_id=?')->execute([(int) $admin['id']]);
    adminAudit($pdo, (int) $session['admin']['id'], 'admin.staff_disabled', 'admin_user', (string) $admin['id']);
    jsonResponse(['success'=>true,'message'=>'Administrator access disabled.']);
}

if ($action === 'delete') {
    $uuid = trim((string) ($input['admin_uuid'] ?? ''));
    if (!preg_match('/^[a-f0-9-]{36}$/i', $uuid)) jsonResponse(['success'=>false,'error'=>'Invalid administrator reference.'], 422);
    $target = $pdo->prepare('SELECT id,role,status FROM admin_users WHERE uuid=? LIMIT 1');
    $target->execute([$uuid]); 
    $admin = $target->fetch();
    if (!$admin) jsonResponse(['success'=>false,'error'=>'Administrator record not found.'], 404);
    
    if ((int) $admin['id'] === (int) $session['admin']['id']) {
        jsonResponse(['success'=>false,'error'=>'You cannot delete your own active administrator account.'], 409);
    }
    if ($admin['role'] === 'super_admin' && (int) $pdo->query("SELECT COUNT(*) FROM admin_users WHERE role='super_admin'")->fetchColumn() <= 1) {
        jsonResponse(['success'=>false,'error'=>'Cannot delete the only remaining super administrator.'], 409);
    }
    
    $pdo->prepare('DELETE FROM admin_sessions WHERE admin_user_id=?')->execute([(int) $admin['id']]);
    $pdo->prepare('DELETE FROM admin_users WHERE id=?')->execute([(int) $admin['id']]);
    adminAudit($pdo, (int) $session['admin']['id'], 'admin.staff_deleted', 'admin_user', (string) $admin['id']);
    jsonResponse(['success'=>true,'message'=>'Administrator account deleted successfully.']);
}

if ($action === 'update_role') {
    $uuid = trim((string) ($input['admin_uuid'] ?? ''));
    $newRole = (string) ($input['role'] ?? '');
    if (!preg_match('/^[a-f0-9-]{36}$/i', $uuid)) jsonResponse(['success'=>false,'error'=>'Invalid administrator reference.'], 422);
    if (!in_array($newRole, ['support','reviewer','finance','super_admin'], true)) {
        jsonResponse(['success'=>false,'error'=>'Invalid role selected.'], 422);
    }
    $target = $pdo->prepare('SELECT id,role,status FROM admin_users WHERE uuid=? LIMIT 1');
    $target->execute([$uuid]); 
    $admin = $target->fetch();
    if (!$admin) jsonResponse(['success'=>false,'error'=>'Administrator record not found.'], 404);

    if ($admin['role'] === 'super_admin' && $newRole !== 'super_admin' && (int) $pdo->query("SELECT COUNT(*) FROM admin_users WHERE role='super_admin' AND status='active'")->fetchColumn() <= 1) {
        jsonResponse(['success'=>false,'error'=>'At least one active super administrator is required.'], 409);
    }

    $pdo->prepare("UPDATE admin_users SET role=? WHERE id=?")->execute([$newRole, (int) $admin['id']]);
    adminAudit($pdo, (int) $session['admin']['id'], 'admin.staff_role_updated', 'admin_user', (string) $admin['id'], ['old_role'=>$admin['role'], 'new_role'=>$newRole]);
    jsonResponse(['success'=>true,'message'=>'Administrator role updated successfully.']);
}

if ($action === 'reset_password') {
    $uuid = trim((string) ($input['admin_uuid'] ?? ''));
    $password = (string) ($input['password'] ?? '');
    if (!preg_match('/^[a-f0-9-]{36}$/i', $uuid)) jsonResponse(['success'=>false,'error'=>'Invalid administrator reference.'], 422);
    if (strlen($password) < 12) jsonResponse(['success'=>false,'error'=>'New password must be at least 12 characters.'], 422);

    $target = $pdo->prepare('SELECT id FROM admin_users WHERE uuid=? LIMIT 1');
    $target->execute([$uuid]); 
    $admin = $target->fetch();
    if (!$admin) jsonResponse(['success'=>false,'error'=>'Administrator record not found.'], 404);

    $pdo->prepare("UPDATE admin_users SET password_hash=? WHERE id=?")->execute([hashPassword($password), (int) $admin['id']]);
    $pdo->prepare('DELETE FROM admin_sessions WHERE admin_user_id=?')->execute([(int) $admin['id']]);
    adminAudit($pdo, (int) $session['admin']['id'], 'admin.staff_password_reset', 'admin_user', (string) $admin['id']);
    jsonResponse(['success'=>true,'message'=>'Administrator password reset successfully.']);
}

jsonResponse(['success'=>false,'error'=>'Unsupported staff action.'], 422);
