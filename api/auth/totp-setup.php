<?php
/** Retired: authenticator setup now lives in api/settings/two-factor.php (action "setup"). */
require_once __DIR__ . '/../../config/response.php';
jsonResponse(['success' => false, 'error' => 'Manage two-factor authentication from Settings → Security.'], 410);
