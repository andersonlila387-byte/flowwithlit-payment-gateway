<?php
require_once __DIR__ . '/../../config/response.php'; require_once __DIR__ . '/../../config/security.php';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') jsonResponse(['success'=>false,'error'=>'Method not allowed.'],405);
$pdo=Database::getInstance(); $input=getJsonInput(); $session=validateSession($pdo,getBearerToken() ?: ($input['token']??''),15); if(!$session['valid']||!empty($session['is_2fa_pending'])) jsonResponse(['success'=>false,'error'=>'Please sign in to continue.'],401);
$business=$pdo->prepare('SELECT id FROM businesses WHERE owner_user_id=? LIMIT 1'); $business->execute([(int)$session['user']['id']]); $businessId=(int)$business->fetchColumn(); if(!$businessId) jsonResponse(['success'=>false,'error'=>'Complete business onboarding first.'],422);
$environment=strtolower(trim((string)($input['environment']??'test'))); if(!in_array($environment,['test','live'],true)) $environment='test';
$prefix='pk_'.$environment.'_'.bin2hex(random_bytes(12)); $secret='sk_'.$environment.'_'.bin2hex(random_bytes(24));
$stmt=$pdo->prepare('INSERT INTO api_keys(business_id,name,key_prefix,secret_hash,environment) VALUES(?,?,?,?,?)'); $stmt->execute([$businessId,trim((string)($input['name']??'Default integration')),$prefix,hash('sha256',$secret),$environment]);
jsonResponse(['success'=>true,'key'=>['id'=>(int)$pdo->lastInsertId(),'public_key'=>$prefix,'secret_key'=>$secret,'environment'=>$environment]],201);
