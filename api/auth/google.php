<?php
/**
 * Flowwithlit API - Google sign-in / sign-up.
 *
 * SECURITY: the client must send the ID token ("credential") issued by Google Identity Services.
 * The token is verified with Google (audience = our client ID, issuer, expiry, verified email)
 * before anything is trusted. Plain google_id / email values from the client are ignored:
 * accepting them let anyone sign in as any user by email.
 *
 * Config: FLOW_GOOGLE_CLIENT_ID = one or more OAuth client IDs (comma-separated, e.g. web + mobile).
 * Without it, Google sign-in is disabled.
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../services/LoginAlertService.php';
require_once __DIR__ . '/../../services/ApiRateLimit.php';
require_once __DIR__ . '/../../services/TotpService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed. Use POST.'], 405);
}

$clientIds = array_values(array_filter(array_map('trim', explode(',', (string) (getenv('FLOW_GOOGLE_CLIENT_ID') ?: '')))));
if (!$clientIds) {
    jsonResponse(['success' => false, 'error' => 'Google sign-in is not available. Please sign in with your email and password.'], 503);
}

$pdo = Database::getInstance();
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
if (!ApiRateLimit::consume($pdo, 'google-signin', $ipAddress, 30, 900)) {
    header('Retry-After: 900');
    jsonResponse(['success' => false, 'error' => 'Too many sign-in attempts. Please wait a few minutes.'], 429);
}

$input = getJsonInput();
$idToken = (string) ($input['credential'] ?? $input['id_token'] ?? '');
if ($idToken === '' || strlen($idToken) > 4096 || substr_count($idToken, '.') !== 2) {
    jsonResponse(['success' => false, 'error' => 'Google sign-in failed. Please try again.'], 400);
}

/** Verifies a Google ID token with Google and returns its claims, or null if it is not valid for us. */
function verifyGoogleIdToken(string $idToken, array $clientIds): ?array {
    $curl = curl_init('https://oauth2.googleapis.com/tokeninfo?' . http_build_query(['id_token' => $idToken]));
    curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10, CURLOPT_CONNECTTIMEOUT => 5, CURLOPT_SSL_VERIFYPEER => true]);
    $body = curl_exec($curl);
    $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    if ($body === false || $status !== 200) return null;
    $claims = json_decode((string) $body, true);
    if (!is_array($claims)) return null;
    if (!in_array((string) ($claims['aud'] ?? ''), $clientIds, true)) return null;
    if (!in_array((string) ($claims['iss'] ?? ''), ['accounts.google.com', 'https://accounts.google.com'], true)) return null;
    if ((int) ($claims['exp'] ?? 0) < time()) return null;
    if (($claims['email_verified'] ?? '') !== 'true' && ($claims['email_verified'] ?? null) !== true) return null;
    if (!preg_match('/^[0-9]{5,64}$/', (string) ($claims['sub'] ?? ''))) return null;
    if (!filter_var((string) ($claims['email'] ?? ''), FILTER_VALIDATE_EMAIL)) return null;
    return $claims;
}

$claims = verifyGoogleIdToken($idToken, $clientIds);
if (!$claims) {
    jsonResponse(['success' => false, 'error' => 'Google could not confirm this sign-in. Please try again.'], 401);
}

// Identity comes only from the verified token.
$googleId  = (string) $claims['sub'];
$email     = strtolower((string) $claims['email']);
$fullName  = trim(sanitizeInput((string) ($claims['name'] ?? ''))) ?: explode('@', $email)[0];
$avatarUrl = filter_var((string) ($claims['picture'] ?? ''), FILTER_VALIDATE_URL) ? (string) $claims['picture'] : null;
$deviceInfo = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown Google App Client';

try {
    $stmt = $pdo->prepare("SELECT id, uuid, full_name, email, phone, google_id, flow_tag, account_type, avatar_url, email_verified, two_factor_enabled, status, onboarding_completed_at
                           FROM users WHERE google_id = ? OR email = ? ORDER BY (google_id = ?) DESC LIMIT 1");
    $stmt->execute([$googleId, $email, $googleId]);
    $user = $stmt->fetch();

    if ($user) {
        $linkedId = (string) ($user['google_id'] ?? '');
        // Old builds stored made-up "google_user_…" IDs; treat those as not linked.
        $isPlaceholder = $linkedId === '' || str_starts_with($linkedId, 'google_user_');
        if (!$isPlaceholder && !hash_equals($linkedId, $googleId)) {
            jsonResponse(['success' => false, 'error' => 'This email is linked to a different Google account. Sign in with your email and password instead.'], 409);
        }
        if ($user['status'] === 'suspended') {
            jsonResponse(['success' => false, 'error' => 'Your Flowwithlit account has been suspended. Please contact compliance.'], 403);
        }
        if ($isPlaceholder) {
            // Google has verified this email, so it is safe to link and mark the email verified.
            $pdo->prepare("UPDATE users SET google_id = ?, avatar_url = COALESCE(avatar_url, ?), email_verified = 1,
                           status = IF(status = 'pending_verification', 'active', status) WHERE id = ?")
                ->execute([$googleId, $avatarUrl, $user['id']]);
        }
        $userId = (int) $user['id'];
    } else {
        $flowTag = generateFlowTag($fullName, $pdo);
        $pdo->prepare("INSERT INTO users (uuid, account_type, full_name, email, flow_tag, auth_provider, google_id, avatar_url, email_verified, status)
                       VALUES (?, 'individual', ?, ?, ?, 'google', ?, ?, 1, 'active')")
            ->execute([generateUUID(), $fullName, $email, $flowTag, $googleId, $avatarUrl]);
        $userId = (int) $pdo->lastInsertId();
    }

    $q = $pdo->prepare('SELECT uuid, full_name, email, phone, flow_tag, account_type, avatar_url, email_verified, two_factor_enabled, status, onboarding_completed_at FROM users WHERE id = ?');
    $q->execute([$userId]);
    $account = $q->fetch();

    // Same two-factor rule as password sign-in: a session stays pending until the TOTP code is entered.
    $requires2fa = TotpService::signInRequiresCode($pdo, $userId);
    $account['two_factor_enabled'] = $requires2fa ? 1 : 0;
    $session = createSession($pdo, $userId, $deviceInfo, $ipAddress, $requires2fa ? 0 : 1);
    if (!$requires2fa) LoginAlertService::send($pdo, $userId, $deviceInfo);

    $payload = [
        'success' => true,
        'message' => $requires2fa ? 'Enter the current code from your authenticator app.' : 'Signed in with Google.',
        'token' => $session['token'],
        'expires_at' => $session['expires_at'],
        'requires_2fa' => $requires2fa,
        'user' => [
            'uuid' => $account['uuid'],
            'full_name' => $account['full_name'],
            'email' => $account['email'],
            'phone' => $account['phone'],
            'flow_tag' => $account['flow_tag'],
            'account_type' => $account['account_type'],
            'avatar_url' => $account['avatar_url'],
            'email_verified' => (bool) $account['email_verified'],
            'two_factor_enabled' => (bool) $account['two_factor_enabled'],
            'status' => $account['status'],
            'onboarding_complete' => !empty($account['onboarding_completed_at']),
        ],
    ];
    if ($requires2fa) $payload['two_factor_method'] = 'totp';
    jsonResponse($payload, 200);
} catch (Throwable $e) {
    error_log('Google Auth Exception: ' . get_class($e));
    jsonResponse(['success' => false, 'error' => 'An internal error occurred during Google authentication.'], 500);
}
