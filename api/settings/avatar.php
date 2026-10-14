<?php
declare(strict_types=1);

require_once __DIR__ . '/_settings.php';
require_once __DIR__ . '/../../services/PrivateStorage.php';

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
if (!in_array($method, ['GET', 'POST'], true)) jsonResponse(['success'=>false, 'error'=>'Method not allowed.'], 405);
$input = $method === 'POST' ? $_POST : $_GET;
$pdo = Database::getInstance();
$session = requireSettingsUser($pdo, $input);
$userId = (int) $session['user']['id'];

$statement = $pdo->prepare('SELECT avatar_url FROM users WHERE id=? LIMIT 1');
$statement->execute([$userId]);
$currentAvatar = (string) $statement->fetchColumn();

if ($method === 'GET') {
    if (!preg_match('/^private-avatar:([a-f0-9]{64})$/D', $currentAvatar, $matches)) { http_response_code(404); exit; }
    try { $contents = PrivateStorage::get($matches[1]); }
    catch (Throwable) { http_response_code(404); exit; }
    $finfo = new finfo(FILEINFO_MIME_TYPE); $mime = (string) $finfo->buffer($contents);
    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) { http_response_code(404); exit; }
    header('Content-Type: '.$mime); header('Content-Length: '.strlen($contents)); header('Cache-Control: private, no-store');
    echo $contents; exit;
}

if (!isset($_FILES['avatar']) || !is_array($_FILES['avatar']) || ($_FILES['avatar']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) jsonResponse(['success'=>false, 'error'=>'Choose an image to upload.'], 422);
$upload = $_FILES['avatar'];
if (!is_uploaded_file((string) $upload['tmp_name']) || (int) $upload['size'] < 1 || (int) $upload['size'] > 2 * 1024 * 1024) jsonResponse(['success'=>false, 'error'=>'Use a JPG, PNG, or WEBP image up to 2MB.'], 422);
$contents = file_get_contents((string) $upload['tmp_name']);
if ($contents === false) jsonResponse(['success'=>false, 'error'=>'Unable to read the uploaded image.'], 422);
$finfo = new finfo(FILEINFO_MIME_TYPE); $mime = (string) $finfo->buffer($contents);
if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) jsonResponse(['success'=>false, 'error'=>'Use a JPG, PNG, or WEBP image.'], 422);
$dimensions = @getimagesizefromstring($contents);
if (!$dimensions || $dimensions[0] > 4096 || $dimensions[1] > 4096 || ((int) $dimensions[0] * (int) $dimensions[1]) > 12000000) jsonResponse(['success'=>false, 'error'=>'Use an image no larger than 4096px on either side.'], 422);

try {
    $newKey = PrivateStorage::put($contents);
    $pdo->prepare('UPDATE users SET avatar_url=? WHERE id=?')->execute(['private-avatar:'.$newKey, $userId]);
    if (preg_match('/^private-avatar:([a-f0-9]{64})$/D', $currentAvatar, $matches)) PrivateStorage::remove($matches[1]);
    settingsAudit($pdo, $userId, 'settings.avatar_updated');
    jsonResponse(['success'=>true, 'avatar_url'=>'private-avatar:'.$newKey, 'message'=>'Profile photo updated.']);
} catch (Throwable $error) {
    error_log('Avatar upload failed: '.$error->getMessage());
    jsonResponse(['success'=>false, 'error'=>'Unable to update your profile photo.'], 500);
}
