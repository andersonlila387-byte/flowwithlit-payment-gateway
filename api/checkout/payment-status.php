<?php
require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/database.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') jsonResponse(['success'=>false,'error'=>'Method not allowed.'],405);
$token = strtoupper(trim((string)($_GET['token'] ?? '')));
if (!preg_match('/^[A-F0-9]{24,64}$/', $token)) jsonResponse(['success'=>false,'error'=>'Invalid checkout token.'],404);
$pdo = Database::getInstance();
$q = $pdo->prepare("SELECT p.status,p.payment_method,p.provider,p.provider_reference,p.transaction_id,t.reference,
                           p.updated_at,cs.expires_at
                    FROM payments p JOIN transactions t ON t.id=p.transaction_id
                    LEFT JOIN checkout_sessions cs ON cs.id=p.checkout_session_id
                    WHERE cs.public_token=? ORDER BY p.id DESC LIMIT 1");
$q->execute([$token]);
$payment = $q->fetch();
if (!$payment) {
    $q = $pdo->prepare("SELECT p.status,p.payment_method,p.provider,p.provider_reference,p.transaction_id,t.reference,p.updated_at,pl.expires_at
                        FROM payments p JOIN transactions t ON t.id=p.transaction_id JOIN payment_links pl ON pl.id=p.payment_link_id
                        WHERE pl.public_token=? ORDER BY p.id DESC LIMIT 1");
    $q->execute([$token]); $payment = $q->fetch();
}
if (!$payment) jsonResponse(['success'=>true,'payment'=>null]);
if ($payment['status'] === 'pending' && !empty($payment['expires_at']) && strtotime($payment['expires_at']) <= time()) {
    $pdo->prepare("UPDATE payments SET status='expired' WHERE transaction_id=? AND status IN ('pending','processing')")->execute([(int)$payment['transaction_id']]);
    $pdo->prepare("UPDATE transactions SET status='cancelled' WHERE id=? AND status IN ('pending','processing')")->execute([(int)$payment['transaction_id']]);
    $payment['status']='expired';
}
jsonResponse(['success'=>true,'payment'=>[
    'status'=>$payment['status'],'method'=>$payment['payment_method'],'provider'=>$payment['provider'],
    'provider_reference'=>$payment['provider_reference'],'reference'=>$payment['reference'],'updated_at'=>$payment['updated_at']
]]);
