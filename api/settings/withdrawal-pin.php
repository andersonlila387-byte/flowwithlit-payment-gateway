<?php
declare(strict_types=1);

require_once __DIR__ . '/_settings.php';
$input = settingsMethod(['POST']);
$pdo = Database::getInstance();
$session = requireSettingsUser($pdo, $input);
$pin = (string) ($input['pin'] ?? '');
if (!preg_match('/^\d{4}$/', $pin)) jsonResponse(['success'=>false, 'error'=>'PIN must contain exactly four digits.'], 422);
$pdo->prepare('UPDATE users SET withdrawal_pin_hash=? WHERE id=?')->execute([hashPassword($pin), (int) $session['user']['id']]);
settingsAudit($pdo, (int) $session['user']['id'], 'settings.withdrawal_pin_set');
jsonResponse(['success'=>true, 'message'=>'Withdrawal PIN saved.']);
