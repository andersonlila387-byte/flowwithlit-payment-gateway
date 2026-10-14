<?php
require_once __DIR__ . '/../../config/response.php'; require_once __DIR__ . '/../../config/security.php';
$pdo=Database::getInstance(); $input=getJsonInput(); $session=validateSession($pdo,getBearerToken() ?: ($input['token']??''),15);
if(!$session['valid']) jsonResponse(['success'=>false,'error'=>$session['error']],401);
$userId=(int)$session['user']['id'];
$stmt=$pdo->prepare('SELECT b.id FROM businesses b LEFT JOIN business_members m ON m.business_id=b.id AND m.user_id=? WHERE b.owner_user_id=? OR m.user_id=? ORDER BY b.id LIMIT 1'); $stmt->execute([$userId,$userId,$userId]); $business=$stmt->fetch();
if(!$business) jsonResponse(['success'=>true,'api_keys'=>[],'webhooks'=>[],'deliveries'=>[]]);
$businessId=(int)$business['id'];
$keys=$pdo->prepare('SELECT id,name,key_prefix,environment,last_used_at,created_at FROM api_keys WHERE business_id=? AND revoked_at IS NULL ORDER BY created_at DESC'); $keys->execute([$businessId]);
$hooks=$pdo->prepare('SELECT id,url,status,events,created_at FROM webhook_endpoints WHERE business_id=? ORDER BY created_at DESC'); $hooks->execute([$businessId]);
$deliveries=$pdo->prepare('SELECT d.event_type,d.response_status,d.attempts,d.delivered_at,d.created_at FROM webhook_deliveries d JOIN webhook_endpoints w ON w.id=d.webhook_endpoint_id WHERE w.business_id=? ORDER BY d.created_at DESC LIMIT 30'); $deliveries->execute([$businessId]);
jsonResponse(['success'=>true,'api_keys'=>$keys->fetchAll(),'webhooks'=>$hooks->fetchAll(),'deliveries'=>$deliveries->fetchAll()]);
