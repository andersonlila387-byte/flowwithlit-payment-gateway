<?php
require_once __DIR__ . '/environment.php';
/** Shared JSON API helpers for the web app and mobile clients. */
function jsonResponse(array $payload, int $httpCode = 200): void {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowedOrigins = array_filter(array_map('trim', explode(',', getenv('FLOW_ALLOWED_ORIGINS') ?: 'http://localhost,http://127.0.0.1')));
    if ($origin !== '' && in_array($origin, $allowedOrigins, true)) { header("Access-Control-Allow-Origin: {$origin}"); header('Vary: Origin'); }
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, Idempotency-Key');
    header('Content-Type: application/json; charset=UTF-8');
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') { http_response_code(204); exit; }
    http_response_code($httpCode);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}
function getJsonInput(): array { $decoded = json_decode(file_get_contents('php://input'), true); return is_array($decoded) ? $decoded : $_POST; }
function getBearerToken(): ?string {
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    if ($header === '' && function_exists('getallheaders')) {
        foreach (getallheaders() as $name => $value) if (strcasecmp($name, 'Authorization') === 0) { $header = $value; break; }
    }
    return preg_match('/^Bearer\s+(.+)$/i', $header, $matches) ? trim($matches[1]) : null;
}
