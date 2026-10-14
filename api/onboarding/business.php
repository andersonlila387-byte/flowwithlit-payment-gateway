<?php
require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../services/OnboardingService.php';
header('Cache-Control: no-store');
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET','POST'], true)) jsonResponse(['success'=>false,'error'=>'Method not allowed.'],405);
$pdo = Database::getInstance();
$session = validateSession($pdo, getBearerToken() ?: '', 15);
if (!$session['valid'] || !empty($session['is_2fa_pending'])) jsonResponse(['success'=>false,'error'=>'Please sign in to continue.','code'=>$session['code'] ?? 'TWO_FACTOR_REQUIRED'],401);
try {
    $userId = (int)$session['user']['id'];
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $result = OnboardingService::read($pdo, $userId);
    } else {
        if (empty($_POST) && empty($_FILES)) jsonResponse(['success'=>false,'error'=>'Empty request or upload limit exceeded. Send multipart form data.'],413);
        if (isset($_POST['ownerEmail']) && strtolower(trim((string)$_POST['ownerEmail'])) !== strtolower((string)$session['user']['email'])) {
            throw new DomainException('Use the email address linked to your account.');
        }
        $action = $_POST['action'] ?? '';
        if (!in_array($action, ['draft','submit'], true)) throw new DomainException('Choose draft or submit.');
        $result = OnboardingService::save($pdo, $userId, $_POST, $_FILES, $action === 'submit');
    }
    jsonResponse(['success'=>true] + $result);
} catch (DomainException $error) {
    jsonResponse(['success'=>false,'error'=>$error->getMessage()],422);
} catch (Throwable $error) {
    error_log('Onboarding error: ' . $error->getMessage());
    jsonResponse(['success'=>false,'error'=>'Unable to save or load onboarding. Please try again.'],500);
}
