<?php
declare(strict_types=1);

require_once __DIR__ . '/_settings.php';
$input = settingsMethod(['GET', 'POST']);
$pdo = Database::getInstance();
$session = requireSettingsUser($pdo, $input);
$userId = (int) $session['user']['id'];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
    $statement = $pdo->prepare('SELECT u.uuid,u.full_name,u.email,u.phone,u.avatar_url,u.two_factor_enabled,u.kyc_tier,u.status,u.withdrawal_pin_hash,b.legal_name,b.trading_name FROM users u LEFT JOIN businesses b ON b.id=(SELECT id FROM businesses WHERE owner_user_id=u.id ORDER BY id ASC LIMIT 1) WHERE u.id=? LIMIT 1');
    $statement->execute([$userId]);
    $profile = $statement->fetch();
    if (!$profile) jsonResponse(['success'=>false, 'error'=>'Your profile could not be found.'], 404);
    $profile['withdrawal_pin_set'] = !empty($profile['withdrawal_pin_hash']);
    unset($profile['withdrawal_pin_hash']);
    jsonResponse(['success'=>true, 'profile'=>$profile]);
}

$fullName = trim((string) ($input['full_name'] ?? ''));
$phone = trim((string) ($input['phone'] ?? ''));
$businessName = trim((string) ($input['business_name'] ?? ''));
if ($fullName === '' || mb_strlen($fullName) > 150) jsonResponse(['success'=>false, 'error'=>'Enter a full name of up to 150 characters.'], 422);
if ($phone !== '' && (!preg_match('/^\+?[0-9][0-9 .()\-]{5,29}$/', $phone) || mb_strlen($phone) > 30)) jsonResponse(['success'=>false, 'error'=>'Enter a valid phone number.'], 422);
if (mb_strlen($businessName) > 191) jsonResponse(['success'=>false, 'error'=>'Business name is too long.'], 422);
try {
    $pdo->beginTransaction();
    $pdo->prepare('UPDATE users SET full_name=?, phone=? WHERE id=?')->execute([$fullName, $phone === '' ? null : $phone, $userId]);
    if ($businessName !== '') {
        $business = $pdo->prepare('SELECT id FROM businesses WHERE owner_user_id=? ORDER BY id ASC LIMIT 1');
        $business->execute([$userId]);
        $businessId = $business->fetchColumn();
        if ($businessId) $pdo->prepare('UPDATE businesses SET legal_name=?,trading_name=? WHERE id=?')->execute([$businessName, $businessName, $businessId]);
    }
    settingsAudit($pdo, $userId, 'settings.profile_updated', ['changed'=>['full_name'=>true, 'phone'=>true, 'business_name'=>$businessName !== '']]);
    $pdo->commit();
    jsonResponse(['success'=>true, 'message'=>'Profile saved.']);
} catch (PDOException $error) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    if ($error->getCode() === '23000') jsonResponse(['success'=>false, 'error'=>'That phone number is already in use.'], 409);
    error_log('Profile update failed: '.$error->getMessage());
    jsonResponse(['success'=>false, 'error'=>'Unable to save your profile.'], 500);
} catch (Throwable $error) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('Profile update failed: '.$error->getMessage());
    jsonResponse(['success'=>false, 'error'=>'Unable to save your profile.'], 500);
}
