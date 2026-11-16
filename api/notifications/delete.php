<?php
declare(strict_types=1);
/**
 * Delete one or more of the signed-in user's notifications.
 * POST { "ids": [12, 15] }   — up to 100 ids; ids belonging to other users are ignored.
 */
require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';
header('Cache-Control: no-store');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') jsonResponse(['success' => false, 'error' => 'Method not allowed.'], 405);

$input = getJsonInput();
$pdo = Database::getInstance();
$session = validateSession($pdo, getBearerToken() ?: (string) ($input['token'] ?? ''), 15);
if (!$session['valid'] || !empty($session['is_2fa_pending'])) jsonResponse(['success' => false, 'error' => 'Please sign in to continue.'], 401);

$ids = array_values(array_unique(array_filter(array_map('intval', (array) ($input['ids'] ?? [])), static fn(int $id): bool => $id > 0)));
if ($ids === [] || count($ids) > 100) jsonResponse(['success' => false, 'error' => 'Select between 1 and 100 notifications to delete.'], 422);

$placeholders = implode(',', array_fill(0, count($ids), '?'));
$statement = $pdo->prepare("DELETE FROM notifications WHERE user_id = ? AND id IN ({$placeholders})");
$statement->execute([(int) $session['user']['id'], ...$ids]);
jsonResponse(['success' => true, 'deleted' => $statement->rowCount()]);
