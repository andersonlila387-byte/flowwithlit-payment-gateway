<?php
declare(strict_types=1);

require_once __DIR__.'/../_admin.php';
require_once __DIR__.'/../../services/PrivateStorage.php';

$input = adminMethod(['GET']);
$pdo = Database::getInstance();
[$session, $role] = requireAdmin($pdo, $input);
requireAdminRole($role, ['reviewer', 'super_admin'], 'view identity documents');

$fileId = (int) ($input['file_id'] ?? 0);
if ($fileId < 1) jsonResponse(['success'=>false, 'error'=>'Invalid identity document.'], 422);
$statement = $pdo->prepare("SELECT f.storage_key,f.mime_type,f.field_name,s.id submission_id
    FROM onboarding_files f INNER JOIN onboarding_submissions s ON s.id=f.submission_id
    WHERE f.id=? AND s.status IN ('submitted','under_review','approved','rejected') LIMIT 1");
$statement->execute([$fileId]);
$file = $statement->fetch();
if (!$file) jsonResponse(['success'=>false, 'error'=>'Identity document not found.'], 404);

try {
    $contents = PrivateStorage::get((string) $file['storage_key']);
    adminAudit($pdo, (int) $session['admin']['id'], 'onboarding.document_viewed', 'onboarding_file', (string) $fileId, ['submission_id'=>(int)$file['submission_id']]);
    header('Content-Type: '.(string) $file['mime_type']);
    header('Content-Disposition: inline; filename="identity-document"');
    header('Cache-Control: private, no-store, max-age=0');
    header('X-Content-Type-Options: nosniff');
    echo $contents;
} catch (Throwable $error) {
    error_log('Admin identity file access failed: '.$error->getMessage());
    jsonResponse(['success'=>false, 'error'=>'Identity document is unavailable.'], 404);
}

