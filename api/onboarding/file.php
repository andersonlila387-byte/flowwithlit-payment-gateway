<?php
require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../services/PrivateStorage.php';
header('Cache-Control: no-store');
if ($_SERVER['REQUEST_METHOD'] !== 'GET') jsonResponse(['success'=>false,'error'=>'Method not allowed.'],405);
$pdo = Database::getInstance();
$session = validateSession($pdo, getBearerToken() ?: '', 15);
if (!$session['valid'] || !empty($session['is_2fa_pending'])) jsonResponse(['success'=>false,'error'=>'Please sign in to continue.'],401);
$id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);
if (!$id || $id < 1) jsonResponse(['success'=>false,'error'=>'File not found.'],404);
$query = $pdo->prepare('SELECT f.* FROM onboarding_files f JOIN onboarding_submissions s ON s.id=f.submission_id WHERE f.id=? AND s.user_id=?');
$query->execute([$id,$session['user']['id']]); $file = $query->fetch();
if (!$file) jsonResponse(['success'=>false,'error'=>'File not found.'],404);
try { $contents = PrivateStorage::get($file['storage_key']); }
catch (Throwable $error) { error_log('Private download failed: ' . $error->getCode()); jsonResponse(['success'=>false,'error'=>'File unavailable.'],500); }
$extensions = ['application/pdf'=>'pdf','image/png'=>'png','image/jpeg'=>'jpg','image/webp'=>'webp'];
header('Content-Type: application/octet-stream');
header('X-Content-Type-Options: nosniff');
header("Content-Security-Policy: default-src 'none'; sandbox");
header('Content-Disposition: attachment; filename="' . $file['field_name'] . '.' . ($extensions[$file['mime_type']] ?? 'bin') . '"');
header('Content-Length: ' . strlen($contents));
echo $contents;
