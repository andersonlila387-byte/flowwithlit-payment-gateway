<?php
/**
 * Flowwithlit Security & Authentication Core Helper Library
 * Enterprise Security: Hashing, Rate Limiting, OTP Codes, Session Management & 2FA Enforcement
 */

require_once __DIR__ . '/database.php';

// Set UTC timezone to ensure 100% time sync between PHP time() and MySQL timestamps
date_default_timezone_set('UTC');


/**
 * Hash Password using BCrypt
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verify Password against BCrypt Hash
 */
function verifyPassword($password, $hash) {
    if (empty($hash)) return false;
    return password_verify($password, $hash);
}

/**
 * Generate cryptographically secure UUID v4
 */
function generateUUID() {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // version 4
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // variant RFC 4122
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

/**
 * Generate cryptographically secure hex token for API & Session tokens
 */
function generateToken($length = 64) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Generate 6-digit numeric OTP code
 */
function generateOTP($length = 6) {
    $min = pow(10, $length - 1);
    $max = pow(10, $length) - 1;
    return str_pad((string)random_int($min, $max), $length, '0', STR_PAD_LEFT);
}

/**
 * Generate unique Fintech Flow Tag (@name_xxxx)
 */
function generateFlowTag($fullName, $pdo) {
    $cleanName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $fullName));
    if (empty($cleanName)) $cleanName = 'flow';
    $cleanName = substr($cleanName, 0, 15);
    
    do {
        $suffix = strtolower(bin2hex(random_bytes(2)));
        $flowTag = '@' . $cleanName . '_' . $suffix;
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE flow_tag = ?");
        $stmt->execute([$flowTag]);
        $exists = $stmt->fetchColumn() > 0;
    } while ($exists);

    return $flowTag;
}

/**
 * Sanitize string input
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

/**
 * Check Rate Limit for IP + Email combinations (Max 5 attempts within 15 minutes)
 */
function checkRateLimit($pdo, $ip, $email, $maxAttempts = 5, $windowMinutes = 15) {
    $stmt = $pdo->prepare("SELECT attempts, last_attempt FROM login_attempts WHERE ip_address = ? AND email = ?");
    $stmt->execute([$ip, $email]);
    $attemptRecord = $stmt->fetch();

    if ($attemptRecord) {
        $lastAttempt = strtotime($attemptRecord['last_attempt']);
        $timeDiffMinutes = (time() - $lastAttempt) / 60;

        if ($timeDiffMinutes < $windowMinutes) {
            if ($attemptRecord['attempts'] >= $maxAttempts) {
                $remainingSeconds = ceil(($windowMinutes * 60) - (time() - $lastAttempt));
                return [
                    'allowed' => false,
                    'remaining_seconds' => max(1, $remainingSeconds),
                    'message' => "Too many failed attempts. Account locked temporarily for $windowMinutes minutes."
                ];
            }
        } else {
            // Reset attempts if window passed
            $stmtReset = $pdo->prepare("UPDATE login_attempts SET attempts = 0, last_attempt = NOW() WHERE ip_address = ? AND email = ?");
            $stmtReset->execute([$ip, $email]);
        }
    }

    return ['allowed' => true];
}

/**
 * Record a failed login attempt
 */
function recordFailedAttempt($pdo, $ip, $email) {
    $stmt = $pdo->prepare("SELECT id, attempts FROM login_attempts WHERE ip_address = ? AND email = ?");
    $stmt->execute([$ip, $email]);
    $record = $stmt->fetch();

    if ($record) {
        $stmtUpdate = $pdo->prepare("UPDATE login_attempts SET attempts = attempts + 1, last_attempt = NOW() WHERE id = ?");
        $stmtUpdate->execute([$record['id']]);
    } else {
        $stmtInsert = $pdo->prepare("INSERT INTO login_attempts (ip_address, email, attempts, last_attempt) VALUES (?, ?, 1, NOW())");
        $stmtInsert->execute([$ip, $email]);
    }
}

/**
 * Reset failed login attempts on successful login
 */
function resetRateLimit($pdo, $ip, $email) {
    $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE ip_address = ? AND email = ?");
    $stmt->execute([$ip, $email]);
}

/**
 * Create a Verification Code (OTP) for Email Verification, Password Reset, or 2FA
 */
function createVerificationCode($pdo, $userId, $email, $type = 'email_verification', $expiryMinutes = 15) {
    // Invalidate old unexpired codes of the same type for this email
    $stmtInvalidate = $pdo->prepare("UPDATE verification_codes SET is_used = 1 WHERE email = ? AND type = ? AND is_used = 0");
    $stmtInvalidate->execute([$email, $type]);

    $code = generateOTP(6);
    $expiresAt = date('Y-m-d H:i:s', strtotime("+$expiryMinutes minutes"));

    $stmt = $pdo->prepare("INSERT INTO verification_codes (user_id, email, code, type, expires_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $email, $code, $type, $expiresAt]);

    return $code;
}

/**
 * Verify an OTP Code
 */
function verifyOTPCode($pdo, $email, $code, $type) {
    $stmt = $pdo->prepare("SELECT id, user_id, expires_at FROM verification_codes WHERE email = ? AND code = ? AND type = ? AND is_used = 0 ORDER BY id DESC LIMIT 1");
    $stmt->execute([$email, $code, $type]);
    $record = $stmt->fetch();

    if (!$record) {
        return ['success' => false, 'error' => 'Invalid or expired verification code.'];
    }

    if (strtotime($record['expires_at']) < time()) {
        return ['success' => false, 'error' => 'Verification code has expired. Please request a new code.'];
    }

    // Mark as used
    $stmtMark = $pdo->prepare("UPDATE verification_codes SET is_used = 1 WHERE id = ?");
    $stmtMark->execute([$record['id']]);

    return [
        'success' => true,
        'user_id' => $record['user_id']
    ];
}

/**
 * Create User Session (Bearer Token)
 */
function createSession($pdo, $userId, $deviceInfo = null, $ipAddress = '127.0.0.1', $is2faVerified = 1, $durationDays = 7) {
    $rawToken = generateToken(64);
    $tokenHash = hash('sha256', $rawToken);
    $now = date('Y-m-d H:i:s');
    $expiresAt = date('Y-m-d H:i:s', strtotime("+$durationDays days"));

    $stmt = $pdo->prepare("INSERT INTO user_sessions (user_id, token_hash, device_info, ip_address, is_2fa_verified, expires_at, last_activity) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $tokenHash, $deviceInfo, $ipAddress, $is2faVerified, $expiresAt, $now]);

    return [
        'token' => $rawToken,
        'expires_at' => $expiresAt
    ];
}

/**
 * Validate User Session Token and Enforce 15-Minute Automatic Inactivity Logout
 */
function validateSession($pdo, $token, $maxInactivityMinutes = 15) {
    if (empty($token)) {
        return ['valid' => false, 'error' => 'Session token is required.', 'code' => 'UNAUTHORIZED'];
    }

    $tokenHash = hash('sha256', $token);
    $nowStr = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare("SELECT s.*, u.uuid, u.full_name, u.email, u.phone, u.flow_tag, u.account_type, u.kyc_tier, u.avatar_url, u.email_verified, u.two_factor_enabled, u.status, u.onboarding_completed_at 
                           FROM user_sessions s 
                           JOIN users u ON s.user_id = u.id 
                           WHERE s.token_hash = ? AND s.expires_at > ?");
    $stmt->execute([$tokenHash, $nowStr]);
    $session = $stmt->fetch();

    if (!$session) {
        return ['valid' => false, 'error' => 'Invalid or expired session.', 'code' => 'SESSION_EXPIRED'];
    }

    // Check account status
    if ($session['status'] === 'suspended') {
        return ['valid' => false, 'error' => 'Your account has been suspended.', 'code' => 'ACCOUNT_SUSPENDED'];
    }

    // Enforce 15-minute inactivity timeout
    $lastActivity = strtotime($session['last_activity']);
    $inactivityMinutes = (time() - $lastActivity) / 60;

    if ($inactivityMinutes > $maxInactivityMinutes) {
        // Delete expired session due to inactivity
        $stmtDelete = $pdo->prepare("DELETE FROM user_sessions WHERE id = ?");
        $stmtDelete->execute([$session['id']]);

        return [
            'valid' => false,
            'error' => 'Logged out due to 15 minutes of inactivity for your account security.',
            'code' => 'INACTIVITY_TIMEOUT'
        ];
    }

    // Check if 2FA verification is pending
    if ($session['is_2fa_verified'] == 0) {
        return [
            'valid' => true,
            'is_2fa_pending' => true,
            'user' => [
                'id' => $session['user_id'],
                'uuid' => $session['uuid'],
                'full_name' => $session['full_name'],
            'email' => $session['email']
            ]
        ];
    }

    // Update last activity timestamp
    $stmtUpdate = $pdo->prepare("UPDATE user_sessions SET last_activity = ? WHERE id = ?");
    $stmtUpdate->execute([$nowStr, $session['id']]);


    return [
        'valid' => true,
        'is_2fa_pending' => false,
        'session_id' => $session['id'],
        'user' => [
            'id' => $session['user_id'],
            'uuid' => $session['uuid'],
            'full_name' => $session['full_name'],
            'email' => $session['email'],
            'phone' => $session['phone'],
            'flow_tag' => $session['flow_tag'],
            'account_type' => $session['account_type'],
            'kyc_tier' => (int)$session['kyc_tier'],
            'avatar_url' => $session['avatar_url'],
            'email_verified' => (bool)$session['email_verified'],
            'two_factor_enabled' => (bool)$session['two_factor_enabled'],
            'status' => $session['status']
            ,'onboarding_complete' => !empty($session['onboarding_completed_at'])
        ]
    ];
}

/**
 * Invalidate Session (Logout)
 */
function invalidateSession($pdo, $token) {
    if (empty($token)) return false;
    $tokenHash = hash('sha256', $token);
    $stmt = $pdo->prepare("DELETE FROM user_sessions WHERE token_hash = ?");
    return $stmt->execute([$tokenHash]);
}
