# PHP-only payment production plan

## Decision and launch boundary

For the first launch, PHP is the only authoritative payment processor. The Go service remains available for later work, but it must not run a payment worker or receive live provider callbacks while PHP owns payment state.

This plan does not claim that a payment system can be made hack-proof. Its goal is to reduce risk with verified provider events, one authoritative write path, least-privilege secrets, auditability, and launch tests.

## PHP-only architecture

```text
Checkout browser
  -> PHP checkout API
  -> MySQL payment and transaction records
  -> OnePipe API

OnePipe callback
  -> PHP webhook receiver
  -> verification + durable event inbox
  -> PHP cron worker
  -> atomic ledger/payment update
  -> checkout status polling response
```

The browser polls `GET /api/checkout/payment-status.php?token=...` every 3-5 seconds while a payment is pending. It stops on a terminal status (`completed`, `failed`, `expired`, or `cancelled`). No PHP WebSocket server is required for launch.

## Required implementation

### 1. OnePipe adapter and webhook

- Replace the placeholder `api/webhooks/onepipe.php`, which currently returns `503`.
- Add `services/OnePipeClient.php` for server-side requests only. Browser code must never receive OnePipe credentials.
- Add `services/OnePipeWebhookVerifier.php` after obtaining the account-specific OnePipe signature/header specification.
- Verify the exact signature over the raw request body before parsing or crediting any event.
- Return a non-success response for an invalid signature; never accept a provider event merely because it contains a matching account number or reference.
- Persist a verified callback in a durable inbox before processing it. Preserve only encrypted/redacted payload fields where required by the data-retention policy.
- Deduplicate by the provider event ID and provider reference using database unique constraints.
- Re-query OnePipe for transaction status when the callback is incomplete, unexpected, delayed, or disputed. A timeout is `pending`, never `completed`.

### 2. Atomic payment settlement

- Keep `DepositService` as the only path that posts provider-confirmed deposits to the ledger and wallet balance.
- Lock the receiving account/payment rows inside a single MySQL transaction.
- Record a local transaction and ledger entry once only.
- Treat duplicate deliveries as successful no-ops.
- Route unmatched, malformed, or ambiguous events to a reconciliation queue; they must never auto-credit a wallet.

### 3. PHP cron worker

Add `cron/payment-worker.php`. It must be CLI-only and refuse HTTP execution.

The worker runs these bounded, idempotent tasks:

1. expire overdue pending checkout sessions and payment links;
2. process verified events from the webhook inbox;
3. re-query/reconcile pending OnePipe operations;
4. retry safe, leased background jobs with exponential backoff;
5. record failures for an administrator to review.

Use a database lease/lock so two cron invocations cannot process the same job or event at once. Process small batches and release locks in `finally` blocks.

Create the Hostinger cron only after the worker has CLI tests. It should run once per minute using the hosting account's PHP binary and the absolute path to `cron/payment-worker.php`; it must not call a public URL and must not place secrets in a command line.

### 4. Payment-state polling

Keep `api/checkout/payment-status.php` as the temporary real-time mechanism.

- Return only the current payment status and safe references.
- Apply rate limiting per checkout token and IP address.
- Poll every 3 seconds initially, then back off to 5-10 seconds after a short period.
- Stop on terminal state or expiry.
- Do not expose internal provider payloads, account numbers, customer data, or webhook errors.

## Security controls

- Enforce HTTPS and redirect HTTP to HTTPS.
- Store provider credentials only in server environment configuration outside the public document root.
- Use separate sandbox and live credentials; never mix environments.
- Rotate provider secrets after any suspected exposure.
- Require exact webhook signature verification from the provider specification. IP allowlists are optional defence-in-depth, not the source of trust.
- Use prepared statements, strict input validation, and DECIMAL/string money values; never use floating point for money.
- Apply idempotency keys and database unique constraints to every payment-changing operation.
- Use least-privilege database credentials, restricted file permissions, and separate admin/session identities.
- Log request IDs, provider references, result codes, and safe error classes. Never log API secrets, bearer tokens, full account numbers, card data, or raw credentials.
- Rate-limit checkout creation, status polling, login, and webhook ingress; add WAF rules appropriate to the hosting plan.
- Keep the Go service disabled while PHP cron is active so two workers cannot update the same payment records.

## Required provider inputs before live enablement

Do not guess these values:

- the enabled OnePipe `auth_provider` value;
- the live/sandbox base URL supplied for the account;
- webhook URL registration procedure;
- webhook signature headers, canonicalization, and secret;
- transaction-notification payload fields and status meanings;
- status-query endpoint and reconciliation rules;
- retry/redelivery behavior and expected acknowledgement response.

## Test and launch gates

### Functional tests

- one successful sandbox payment reaches `completed` exactly once;
- repeated callback delivers no additional credit;
- invalid/missing signature is rejected and changes no balance;
- wrong amount, currency, receiving account, or reference is held for review;
- provider timeout remains pending and is reconciled later;
- expired checkout cannot be credited by a stale/unrelated callback;
- user A cannot view user B's payment status or account details;
- cron can run twice concurrently without double-processing work.

### Capacity and operational tests

- measure the Hostinger plan's PHP worker, CPU, database, and concurrent-request limits;
- load-test checkout creation, status polling, and webhook ingestion at expected peak traffic before inviting users;
- monitor 5xx rate, response time, pending-payment age, duplicate events, cron failures, and webhook verification failures;
- create a rollback switch that disables new payment initiation while preserving reconciliation access.

## Go/no-go for live payments

Do not enable live OnePipe payments until every item below passes:

- [ ] OnePipe account/provider configuration is verified in sandbox.
- [ ] The PHP webhook verifies a real signed sandbox callback.
- [ ] A duplicate callback cannot double-credit.
- [ ] The reconciliation path handles delayed/ambiguous provider results.
- [ ] Cron processing is locked, observed, and tested.
- [ ] Checkout status polling is rate-limited and user-safe.
- [ ] Database migrations are backed up, applied, and verified.
- [ ] Load test results fit the expected launch peak or the launch is throttled to a safe cohort.
- [ ] Go payment worker is disabled while PHP is authoritative.
