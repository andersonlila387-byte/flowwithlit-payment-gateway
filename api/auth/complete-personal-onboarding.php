<?php
require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['success' => false, 'error' => 'Method not allowed.'], 405);
$input = getJsonInput();
$pdo = Database::getInstance();
$session = validateSession($pdo, getBearerToken() ?: '', 15);
if (!$session['valid'] || !empty($session['is_2fa_pending'])) jsonResponse(['success' => false, 'error' => 'Please sign in again.'], 401);
$user = $session['user'];
$values = [];
foreach (['full_name' => 150, 'phone' => 30, 'address_line1' => 191, 'city' => 100, 'state_region' => 100, 'country_code' => 2] as $field => $limit) {
    $value = $input[$field] ?? '';
    if (!is_string($value) || trim($value) === '' || mb_strlen(trim($value)) > $limit) {
        jsonResponse(['success' => false, 'error' => 'Please enter a valid ' . str_replace('_', ' ', $field) . '.'], 422);
    }
    $values[$field] = trim($value);
}
if (!in_array($values['country_code'], ['NG', 'GH', 'KE', 'ZA'], true)) jsonResponse(['success' => false, 'error' => 'Please select a supported country.'], 422);
if (!preg_match('/^\+?[0-9 ()-]{7,30}$/', $values['phone']) || strlen(preg_replace('/\D/', '', $values['phone'])) < 7) jsonResponse(['success' => false, 'error' => 'Please enter a valid phone number.'], 422);
// Email is displayed for confirmation; changing a verified email requires its own verification flow.
if (($input['email'] ?? '') !== $user['email']) jsonResponse(['success' => false, 'error' => 'Please use your account email address.'], 422);
try {
    $pdo->beginTransaction();
    $pdo->prepare('UPDATE users SET full_name = ?, phone = ?, onboarding_completed_at = NOW() WHERE id = ?')->execute([$values['full_name'], $values['phone'], $user['id']]);
    $pdo->prepare('INSERT INTO user_profiles (user_id, address_line1, city, state_region, country_code) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE address_line1 = VALUES(address_line1), city = VALUES(city), state_region = VALUES(state_region), country_code = VALUES(country_code)')->execute([$user['id'], $values['address_line1'], $values['city'], $values['state_region'], $values['country_code']]);
    $pdo->commit();
    jsonResponse(['success' => true, 'message' => 'Personal setup completed.']);
} catch (PDOException $error) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    if (($error->errorInfo[1] ?? null) === 1062) jsonResponse(['success' => false, 'error' => 'This phone number is already linked to an account.'], 409);
    error_log('Personal onboarding save failed: ' . $error->getCode());
    jsonResponse(['success' => false, 'error' => 'Unable to save your details. Please try again.'], 500);
}
