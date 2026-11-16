<?php
declare(strict_types=1);

/**
 * Admin → Countries. Any administrator can view; only super administrators can
 * approve, pause, add or edit a country. Every change is written to the audit log.
 */
require_once __DIR__ . '/../_admin.php';

$input = adminMethod(['GET', 'POST']);
$pdo = Database::getInstance();
[$session, $role] = requireAdmin($pdo, $input);
$adminId = (int) $session['admin']['id'];

function countryList(PDO $pdo): array {
    return $pdo->query('SELECT c.code, c.name, c.dial_code, c.is_enabled, c.sort_order, c.updated_at,
            (SELECT COUNT(*) FROM user_profiles p WHERE p.country_code = c.code) AS users
        FROM countries c ORDER BY c.is_enabled DESC, c.sort_order, c.name')->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    requireAdminRole($role, ['support', 'reviewer', 'finance', 'super_admin'], 'view countries');
    jsonResponse(['success' => true, 'countries' => countryList($pdo)]);
}

if ($role !== 'super_admin') {
    jsonResponse(['success' => false, 'error' => 'Only super administrators can change countries.'], 403);
}

$action = (string) ($input['action'] ?? '');
$code = strtoupper(trim((string) ($input['code'] ?? '')));
if (!preg_match('/^[A-Z]{2}$/', $code)) {
    jsonResponse(['success' => false, 'error' => 'Use the 2-letter country code, for example GH for Ghana.'], 422);
}

if ($action === 'toggle') {
    $enabled = !empty($input['is_enabled']) ? 1 : 0;
    try {
        $pdo->beginTransaction();
        $current = $pdo->prepare('SELECT is_enabled FROM countries WHERE code = ? FOR UPDATE');
        $current->execute([$code]);
        $was = $current->fetchColumn();
        if ($was === false) throw new DomainException('Country not found.', 404);
        if (!$enabled) {
            $others = (int) $pdo->query('SELECT COUNT(*) FROM countries WHERE is_enabled = 1')->fetchColumn();
            if ((int) $was === 1 && $others <= 1) throw new DomainException('At least one country must stay approved, or nobody can sign up.', 422);
        }
        $pdo->prepare('UPDATE countries SET is_enabled = ?, updated_by = ? WHERE code = ?')->execute([$enabled, $adminId, $code]);
        adminAudit($pdo, $adminId, $enabled ? 'country.approved' : 'country.paused', 'country', $code, ['is_enabled' => $enabled]);
        $pdo->commit();
    } catch (DomainException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        jsonResponse(['success' => false, 'error' => $e->getMessage()], $e->getCode() ?: 422);
    }
    jsonResponse(['success' => true, 'countries' => countryList($pdo)]);
}

if ($action === 'save') {
    $name = trim((string) ($input['name'] ?? ''));
    $dial = trim((string) ($input['dial_code'] ?? ''));
    if ($dial !== '' && $dial[0] !== '+') $dial = '+' . $dial;
    $sort = isset($input['sort_order']) && $input['sort_order'] !== '' ? (int) $input['sort_order'] : 100;
    $enabled = !empty($input['is_enabled']) ? 1 : 0;
    if ($name === '' || mb_strlen($name) > 80) jsonResponse(['success' => false, 'error' => 'Enter the country name (80 characters or fewer).'], 422);
    if (!preg_match('/^\+[1-9][0-9]{0,4}$/', $dial)) jsonResponse(['success' => false, 'error' => 'Enter the dialling code, for example +233.'], 422);
    if ($sort < 1 || $sort > 999) jsonResponse(['success' => false, 'error' => 'Display order must be between 1 and 999.'], 422);

    $exists = $pdo->prepare('SELECT is_enabled FROM countries WHERE code = ?');
    $exists->execute([$code]);
    $was = $exists->fetchColumn();
    if ($was !== false && (int) $was === 1 && !$enabled
        && (int) $pdo->query('SELECT COUNT(*) FROM countries WHERE is_enabled = 1')->fetchColumn() <= 1) {
        jsonResponse(['success' => false, 'error' => 'At least one country must stay approved, or nobody can sign up.'], 422);
    }
    $pdo->prepare('INSERT INTO countries (code, name, dial_code, is_enabled, sort_order, updated_by) VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE name = VALUES(name), dial_code = VALUES(dial_code), is_enabled = VALUES(is_enabled), sort_order = VALUES(sort_order), updated_by = VALUES(updated_by)')
        ->execute([$code, $name, $dial, $enabled, $sort, $adminId]);
    adminAudit($pdo, $adminId, $was === false ? 'country.added' : 'country.updated', 'country', $code,
        ['name' => $name, 'dial_code' => $dial, 'is_enabled' => $enabled, 'sort_order' => $sort]);
    jsonResponse(['success' => true, 'countries' => countryList($pdo)], $was === false ? 201 : 200);
}

jsonResponse(['success' => false, 'error' => 'Unknown action.'], 400);
