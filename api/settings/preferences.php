<?php
declare(strict_types=1);

require_once __DIR__ . '/_settings.php';
$input = settingsMethod(['GET', 'POST']);
$pdo = Database::getInstance();
$session = requireSettingsUser($pdo, $input);
$userId = (int) $session['user']['id'];
$defaults = ['payment_activity'=>true, 'payout_updates'=>true, 'product_news'=>false];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
    $statement = $pdo->prepare('SELECT preferences FROM user_profiles WHERE user_id=? LIMIT 1');
    $statement->execute([$userId]);
    $stored = $statement->fetchColumn();
    try { $preferences = is_string($stored) ? json_decode($stored, true, 32, JSON_THROW_ON_ERROR) : []; }
    catch (Throwable) { $preferences = []; }
    $preferences = array_replace($defaults, is_array($preferences) ? array_intersect_key($preferences, $defaults) : []);
    foreach ($preferences as $key => $value) $preferences[$key] = (bool) $value;
    jsonResponse(['success'=>true, 'preferences'=>$preferences]);
}

$preferences = [];
foreach ($defaults as $key => $default) {
    if (!array_key_exists($key, $input) || !is_bool($input[$key])) jsonResponse(['success'=>false, 'error'=>"{$key} must be true or false."], 422);
    $preferences[$key] = $input[$key];
}
$statement = $pdo->prepare('INSERT INTO user_profiles(user_id,preferences) VALUES(?,?) ON DUPLICATE KEY UPDATE preferences=VALUES(preferences)');
$statement->execute([$userId, json_encode($preferences, JSON_THROW_ON_ERROR)]);
settingsAudit($pdo, $userId, 'settings.preferences_updated', $preferences);
jsonResponse(['success'=>true, 'preferences'=>$preferences, 'message'=>'Notification preferences saved.']);
