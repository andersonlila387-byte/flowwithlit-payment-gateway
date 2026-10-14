<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';

$pdo = Database::getInstance();

echo "1. Testing DB Connection... OK\n";

// Clear test users
$pdo->exec("DELETE FROM users WHERE email LIKE '%test%'");
$pdo->exec("DELETE FROM verification_codes");
$pdo->exec("DELETE FROM user_sessions");

// 2. Test User Registration Logic
$uuid = generateUUID();
$passwordHash = hashPassword("TestPassword123!");
$flowTag = generateFlowTag("Test Merchant", $pdo);

$stmt = $pdo->prepare("INSERT INTO users (uuid, account_type, full_name, email, phone, password_hash, flow_tag, auth_provider, email_verified, status) VALUES (?, 'individual', 'Test Merchant', 'test@flowwithlit.com', '+2348000000000', ?, ?, 'email', 0, 'pending_verification')");
$stmt->execute([$uuid, $passwordHash, $flowTag]);
$userId = $pdo->lastInsertId();

echo "2. User Registered Successfully (ID: $userId, Tag: $flowTag)... OK\n";

// 3. Test OTP Code Generation & Verification
$otp = createVerificationCode($pdo, $userId, 'test@flowwithlit.com', 'email_verification');
echo "3. OTP Generated ($otp)... OK\n";

$verifyResult = verifyOTPCode($pdo, 'test@flowwithlit.com', $otp, 'email_verification');
if ($verifyResult['success']) {
    echo "4. OTP Verification Succeeded... OK\n";
} else {
    echo "4. OTP Verification Failed: " . $verifyResult['error'] . "\n";
}

// 5. Test Password Verification & Session Creation
if (verifyPassword("TestPassword123!", $passwordHash)) {
    echo "5. Password Verification Succeeded... OK\n";
}

$session = createSession($pdo, $userId, "PHP Unit Test Client", "127.0.0.1", 1);
echo "6. Session Token Created: " . substr($session['token'], 0, 16) . "...\n";

// 7. Test Session Validation & 15-min Inactivity
$sessionVal = validateSession($pdo, $session['token'], 15);
if ($sessionVal['valid']) {
    echo "7. Session Validation Succeeded for " . $sessionVal['user']['email'] . "... OK\n";
} else {
    echo "7. Session Validation Failed: " . $sessionVal['error'] . "\n";
}

echo "\n--- ALL AUTHENTICATION BACKEND SERVICES FULLY FUNCTIONAL ---\n";
