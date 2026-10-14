<?php
// Legacy direct-credit handler removed: it matched account suffixes and accepted
// unverified monetary fields. The provider-specific inbox/verification adapter is
// being implemented under docs/payment-gateway-plan.md. 503 requests redelivery.
require_once __DIR__.'/../../config/response.php';
header('Cache-Control: no-store');
jsonResponse(['success'=>false,'error'=>'Provider verification is being configured.'],503);
