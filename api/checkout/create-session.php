<?php
require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';

header('Cache-Control: no-store');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') jsonResponse(['success'=>false,'error'=>'Method not allowed.'],405);

$secret = getBearerToken() ?: '';
if (!preg_match('/^sk_(test|live)_[a-f0-9]{48}$/i', $secret)) jsonResponse(['success'=>false,'error'=>'A valid secret API key is required.'],401);
$pdo = Database::getInstance();
$key = $pdo->prepare("SELECT k.id,k.business_id,k.environment,b.owner_user_id
    FROM api_keys k JOIN businesses b ON b.id=k.business_id
    WHERE k.secret_hash=? AND k.revoked_at IS NULL LIMIT 1");
$key->execute([hash('sha256',$secret)]);
$apiKey = $key->fetch();
if (!$apiKey) jsonResponse(['success'=>false,'error'=>'API key is invalid or revoked.'],401);

$input = getJsonInput();
$title = trim((string)($input['title'] ?? ''));
$amount = $input['amount'] ?? null;
$currency = strtoupper(trim((string)($input['currency'] ?? 'NGN')));
if ($title === '' || mb_strlen($title) > 191) jsonResponse(['success'=>false,'error'=>'A payment title is required.'],422);
if (!is_numeric($amount) || (float)$amount <= 0) jsonResponse(['success'=>false,'error'=>'Amount must be a positive number.'],422);
$currencyQuery = $pdo->prepare("SELECT code FROM currencies WHERE code=? AND is_enabled=1 AND (code='NGN' OR kind='crypto') LIMIT 1");
$currencyQuery->execute([$currency]);
if (!$currencyQuery->fetchColumn()) jsonResponse(['success'=>false,'error'=>'Currency is not enabled.'],422);

$urls = [];
foreach (['return_url','cancel_url'] as $field) {
    $url = trim((string)($input[$field] ?? ''));
    if ($url !== '' && (!filter_var($url,FILTER_VALIDATE_URL) || !in_array(strtolower((string)parse_url($url,PHP_URL_SCHEME)),['http','https'],true))) jsonResponse(['success'=>false,'error'=>str_replace('_',' ',$field).' must be a valid HTTP or HTTPS URL.'],422);
    $urls[$field] = $url ?: null;
}
$expiresAt = gmdate('Y-m-d H:i:s', time() + 1800);
if (!empty($input['expires_at'])) {
    $expires = strtotime((string)$input['expires_at']);
    if ($expires === false || $expires <= time() || $expires > time() + 86400) jsonResponse(['success'=>false,'error'=>'Expiry must be within the next 24 hours.'],422);
    $expiresAt = gmdate('Y-m-d H:i:s', $expires);
}

$token = strtoupper(bin2hex(random_bytes(32)));
$insert = $pdo->prepare('INSERT INTO checkout_sessions(uuid,public_token,business_id,user_id,api_key_id,payment_link_id,origin,environment,title,description,amount,currency_code,return_url,cancel_url,status,expires_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
$insert->execute([generateUUID(),$token,(int)$apiKey['business_id'],(int)$apiKey['owner_user_id'],(int)$apiKey['id'],null,'api',$apiKey['environment'],$title,mb_substr(trim((string)($input['description'] ?? '')),0,2000),(string)$amount,$currency,$urls['return_url'],$urls['cancel_url'],'active',$expiresAt]);

$checkoutBase = rtrim((string)(getenv('FLOW_CHECKOUT_URL') ?: ''), '/');
$appBase = rtrim(str_replace('/api/checkout/create-session.php','',(string)($_SERVER['SCRIPT_NAME'] ?? '')),'/');
$checkoutUrl = $checkoutBase !== '' ? $checkoutBase . '/' . $token : $appBase . '/checkout/' . $token;
jsonResponse(['success'=>true,'checkout_url'=>$checkoutUrl,'reference'=>$token,'environment'=>$apiKey['environment'],'expires_at'=>$expiresAt],201);
