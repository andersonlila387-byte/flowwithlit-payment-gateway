<?php
/**
 * Retired. This endpoint switched two-factor authentication on without connecting an authenticator app
 * (which locked users out at sign-in) and off without a password or code.
 * Two-factor authentication is managed only through api/settings/two-factor.php.
 */
require_once __DIR__ . '/../../config/response.php';
jsonResponse(['success' => false, 'error' => 'Manage two-factor authentication from Settings → Security.'], 410);
