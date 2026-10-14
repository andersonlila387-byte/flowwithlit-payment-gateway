<?php
require_once __DIR__ . '/../../config/response.php'; require_once __DIR__ . '/../../config/security.php';
if($_SERVER['REQUEST_METHOD']!=='POST') jsonResponse(['success'=>false,'error'=>'Method not allowed.'],405);
$input=getJsonInput(); $pdo=Database::getInstance(); $session=validateSession($pdo,getBearerToken() ?: ($input['token'] ?? ''),15);
if(!$session['valid']) jsonResponse(['success'=>false,'error'=>$session['error']],401);
if(!empty($input['id'])) $stmt=$pdo->prepare('UPDATE notifications SET read_at=NOW() WHERE id=? AND user_id=?'); else $stmt=$pdo->prepare('UPDATE notifications SET read_at=NOW() WHERE user_id=? AND read_at IS NULL');
!empty($input['id']) ? $stmt->execute([(int)$input['id'],$session['user']['id']]) : $stmt->execute([$session['user']['id']]);
jsonResponse(['success'=>true]);
