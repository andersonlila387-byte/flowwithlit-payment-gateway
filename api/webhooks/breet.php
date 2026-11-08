<?php
declare(strict_types=1);
/**
 * Breet webhook receiver (replaces the retired Go /webhooks/breet handler).
 *
 * Trust comes from the shared secret header plus Breet's published source IPs.
 * The event is only recorded in the inbox here; nothing is credited from a
 * webhook request. Crypto settlement will be added to the payment worker once
 * the Breet trade-verification flow is implemented.
 */
require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/ProviderEventInbox.php';

header('Cache-Control: no-store');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'method_not_allowed'], 405);
}

$secret = (string) (getenv('BREET_WEBHOOK_SECRET') ?: '');
$given = (string) ($_SERVER['HTTP_X_WEBHOOK_SECRET'] ?? '');
if ($secret === '' || !hash_equals($secret, $given)) {
    jsonResponse(['success' => false, 'error' => 'invalid_webhook_secret'], 401);
}

// REMOTE_ADDR only: forwarded headers are client-controlled and are never trusted here.
$allowedIps = array_filter(array_map('trim', explode(',', (string) (getenv('BREET_ALLOWED_IPS')
    ?: '46.101.201.155,46.101.225.109,46.101.225.97,46.101.225.251,159.89.20.62'))));
if (!in_array((string) ($_SERVER['REMOTE_ADDR'] ?? ''), $allowedIps, true)) {
    jsonResponse(['success' => false, 'error' => 'untrusted_webhook_source'], 403);
}

$rawBody = (string) file_get_contents('php://input', false, null, 0, 2 * 1024 * 1024 + 1);
if ($rawBody === '' || strlen($rawBody) > 2 * 1024 * 1024) {
    jsonResponse(['success' => false, 'error' => 'invalid_body'], 400);
}
try {
    $body = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($body)) throw new JsonException('Body must be a JSON object.');
} catch (JsonException) {
    jsonResponse(['success' => false, 'error' => 'invalid_json'], 400);
}

try {
    $recorded = ProviderEventInbox::record(Database::getInstance(), 'breet', $rawBody, $body);
} catch (Throwable $exception) {
    error_log('Breet webhook inbox unavailable: ' . get_class($exception));
    // A 5xx makes Breet redeliver, so the event is not lost.
    jsonResponse(['success' => false, 'error' => 'inbox_unavailable'], 500);
}

if (!$recorded) jsonResponse(['success' => true, 'duplicate' => true]);
jsonResponse(['success' => true, 'received' => true, 'processing' => 'queued'], 202);
