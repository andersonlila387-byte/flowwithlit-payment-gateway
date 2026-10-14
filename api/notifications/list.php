<?php
require_once __DIR__ . '/../../config/response.php'; require_once __DIR__ . '/../../config/security.php';
$input=getJsonInput(); $pdo=Database::getInstance(); $session=validateSession($pdo,getBearerToken() ?: ($input['token'] ?? ''),15);
if(!$session['valid']) jsonResponse(['success'=>false,'error'=>$session['error']],401);
$stmt=$pdo->prepare('SELECT id,type,title,body,action_url,read_at,created_at FROM notifications WHERE user_id=? ORDER BY created_at DESC LIMIT 30'); $stmt->execute([$session['user']['id']]); $items=$stmt->fetchAll();
$unread=0; foreach($items as &$item){$item['read']=!empty($item['read_at']); if(!$item['read'])$unread++;} unset($item);
jsonResponse(['success'=>true,'notifications'=>$items,'unread_count'=>$unread]);
