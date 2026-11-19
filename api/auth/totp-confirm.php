<?php
/** Retired: authenticator confirmation now lives in api/settings/two-factor.php (action "enable"). */
require_once __DIR__ . '/../../config/response.php';
jsonResponse(['success' => false, 'error' => 'Manage two-factor authentication from Settings → Security.'], 410);
