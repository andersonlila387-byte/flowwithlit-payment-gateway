<?php
/**
 * Flowwithlit API - Register Endpoint
 * Powers Web UI and Mobile Apps (Returns JSON + CORS)
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../services/EmailService.php';
require_once __DIR__ . '/../../config/countries.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed. Use POST.'], 405);
}

$input = getJsonInput();

$firstName   = trim(sanitizeInput($input['first_name'] ?? ''));
$lastName    = trim(sanitizeInput($input['last_name'] ?? ''));
// Older clients (mobile apps) still send a single full_name.
$fullName    = trim($firstName . ' ' . $lastName) ?: trim(sanitizeInput($input['full_name'] ?? ''));
$email       = strtolower(trim(sanitizeInput($input['email'] ?? '')));
$phone       = sanitizeInput($input['phone'] ?? '');
$phoneCode   = trim((string) ($input['phone_code'] ?? ''));
$password    = $input['password'] ?? '';
$accountType = sanitizeInput($input['account_type'] ?? 'individual');
$referralCode = strtoupper(trim(sanitizeInput($input['referral_code'] ?? '')));
$countryInput = strtoupper(trim((string) ($input['country'] ?? '')));
$businessName = trim(sanitizeInput($input['business_name'] ?? ''));
$businessStage = strtolower(trim((string) ($input['business_stage'] ?? '')));

// Validation
if (($firstName !== '' || $lastName !== '') && ($firstName === '' || $lastName === '')) {
    jsonResponse(['success' => false, 'error' => 'First name and last name are both required.'], 400);
}

if (empty($fullName)) {
    jsonResponse(['success' => false, 'error' => 'Full name is required.'], 400);
}

if (mb_strlen($firstName) > 75 || mb_strlen($lastName) > 75) {
    jsonResponse(['success' => false, 'error' => 'First and last name must be 75 characters or fewer.'], 400);
}

// Signup offers the admin-approved countries (Admin → Countries) or "Other countries";
// only an approved ISO code is stored.
$countryPdo = Database::getInstance();
$approvedCodes = array_column(approvedCountries($countryPdo), 'code');
if ($countryInput !== '' && $countryInput !== 'OTHER' && !in_array($countryInput, $approvedCodes, true)) {
    jsonResponse(['success' => false, 'error' => 'Signups are not open in that country yet. Choose "Other countries" to continue.'], 400);
}
$countryCode = in_array($countryInput, $approvedCodes, true) ? $countryInput : null;

// Phone: a dialling code from the picker plus the local number, stored as +<code><digits>.
if ($phoneCode !== '') {
    $allowedCodes = phoneDialCodes($countryPdo);
    if (!in_array($phoneCode, $allowedCodes, true)) {
        jsonResponse(['success' => false, 'error' => 'Please choose a valid phone country code.'], 400);
    }
    $digits = ltrim(preg_replace('/\D+/', '', (string) $phone), '0');
    if ($digits !== '' && (strlen($digits) < 6 || strlen($digits) > 14)) {
        jsonResponse(['success' => false, 'error' => 'Please enter a valid phone number.'], 400);
    }
    $phone = $digits === '' ? '' : $phoneCode . $digits;
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'error' => 'A valid email address is required.'], 400);
}

if (empty($password) || strlen($password) < 8) {
    jsonResponse(['success' => false, 'error' => 'Password must be at least 8 characters long.'], 400);
}

if (!in_array($accountType, ['individual', 'business'])) {
    $accountType = 'individual';
}

if ($accountType === 'business') {
    if ($businessName === '' || mb_strlen($businessName) > 191) {
        jsonResponse(['success' => false, 'error' => 'Please enter your business name.'], 400);
    }
    if (!in_array($businessStage, ['starter', 'registered'], true)) {
        jsonResponse(['success' => false, 'error' => 'Please tell us what type of business you own.'], 400);
    }
} else {
    $businessName = '';
    $businessStage = '';
}

$pdo = Database::getInstance();

// Check if Email already exists
$stmtEmail = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmtEmail->execute([$email]);
if ($stmtEmail->fetch()) {
    jsonResponse(['success' => false, 'error' => 'An account with this email address already exists.'], 409);
}

// Check Phone if provided
if (!empty($phone)) {
    $stmtPhone = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
    $stmtPhone->execute([$phone]);
    if ($stmtPhone->fetch()) {
        jsonResponse(['success' => false, 'error' => 'An account with this phone number already exists.'], 409);
    }
} else {
    $phone = null;
}

$uuid = generateUUID();
$passwordHash = hashPassword($password);
$flowTag = generateFlowTag($fullName, $pdo);

try {
    $pdo->beginTransaction();

    // Create User
    $stmt = $pdo->prepare("INSERT INTO users (uuid, account_type, full_name, first_name, last_name, signup_business_name, business_stage, email, phone, password_hash, flow_tag, auth_provider, email_verified, status)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'email', 0, 'pending_verification')");
    $stmt->execute([
        $uuid, $accountType, $fullName,
        $firstName !== '' ? $firstName : null,
        $lastName !== '' ? $lastName : null,
        $businessName !== '' ? $businessName : null,
        $businessStage !== '' ? $businessStage : null,
        $email, $phone, $passwordHash, $flowTag,
    ]);
    $userId = $pdo->lastInsertId();
    if ($countryCode !== null) {
        $pdo->prepare('INSERT INTO user_profiles (user_id, country_code) VALUES (?, ?) ON DUPLICATE KEY UPDATE country_code = VALUES(country_code)')
            ->execute([$userId, $countryCode]);
    }
    $pdo->prepare('INSERT INTO referral_codes(user_id,code) VALUES(?,?)')->execute([$userId,'FWL'.strtoupper(substr(hash('sha256',$userId.'|'.bin2hex(random_bytes(8))),0,10))]);
    if ($referralCode !== '') {
        $referrer = $pdo->prepare('SELECT user_id FROM referral_codes WHERE code=? LIMIT 1'); $referrer->execute([$referralCode]); $referrerId = (int)$referrer->fetchColumn();
        if ($referrerId && $referrerId !== (int)$userId) $pdo->prepare("INSERT INTO referrals(referrer_user_id,referred_user_id,referral_code) VALUES(?,?,?)")->execute([$referrerId,$userId,$referralCode]);
    }

    // Generate 6-Digit Email Verification OTP Code
    $otpCode = createVerificationCode($pdo, $userId, $email, 'email_verification', 15);

    // Create Session Token
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $deviceInfo = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown App Client';
    $session = createSession($pdo, $userId, $deviceInfo, $ipAddress, 1);

    $pdo->commit();

    try {
        EmailService::send($email, 'Verify your Flowwithlit email', 'verify-email', ['name' => $fullName, 'code' => $otpCode]);
    } catch (Throwable $mailError) {
        error_log('Verification email error: ' . $mailError->getMessage());
    }

    jsonResponse([
        'success' => true,
        'message' => 'Registration successful! Verification code sent to email.',
        'token' => $session['token'],
        'expires_at' => $session['expires_at'],
        'user' => [
            'uuid' => $uuid,
            'full_name' => $fullName,
            'first_name' => $firstName !== '' ? $firstName : null,
            'last_name' => $lastName !== '' ? $lastName : null,
            'business_name' => $businessName !== '' ? $businessName : null,
            'business_stage' => $businessStage !== '' ? $businessStage : null,
            'email' => $email,
            'flow_tag' => $flowTag,
            'account_type' => $accountType,
            'email_verified' => false,
            'two_factor_enabled' => false
        ]
    ], 201);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Registration Exception: " . $e->getMessage());
    jsonResponse(['success' => false, 'error' => 'An internal error occurred during registration.'], 500);
}
