<?php
require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';

header('Cache-Control: no-store');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') jsonResponse(['success'=>false,'error'=>'Method not allowed.'],405);
$pdo = Database::getInstance();
$input = getJsonInput();
$session = validateSession($pdo, getBearerToken() ?: ($input['token'] ?? ''), 15);
if (!$session['valid'] || !empty($session['is_2fa_pending'])) jsonResponse(['success'=>false,'error'=>'Please sign in to continue.'],401);
$uid = (int)$session['user']['id'];
$cardId = filter_var($input['card_id'] ?? null, FILTER_VALIDATE_INT);
$action = strtolower(trim((string)($input['action'] ?? '')));
if (!$cardId || !in_array($action,['freeze','unfreeze'],true)) jsonResponse(['success'=>false,'error'=>'Invalid card action.'],422);
$status = $action === 'freeze' ? 'frozen' : 'active';
$stmt = $pdo->prepare("UPDATE cards SET status=? WHERE id=? AND user_id=? AND status<>'terminated'");
$stmt->execute([$status,$cardId,$uid]);
if (!$stmt->rowCount()) jsonResponse(['success'=>false,'error'=>'Card not found or cannot be changed.'],404);
jsonResponse(['success'=>true,'card_id'=>(int)$cardId,'status'=>$status]);
