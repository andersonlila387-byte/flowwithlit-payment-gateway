# Complete payment gateway implementation plan

Status: active implementation; not production ready. This plan preserves the full user request.

## Ownership and flow

PHP owns authentication, business authorization, provider adapters, credential decryption,
money calculations, the double-entry ledger, and transactional state changes. MariaDB is
the source of truth. Go owns durable webhook ingestion, leased background jobs, retries,
reconciliation scheduling and WebSocket delivery. Workers call a signed internal PHP
contract; they do not independently implement wallet balance changes.

Browser -> PHP checkout/user API -> transaction + job in MariaDB -> Go worker -> PHP
provider adapter -> provider. Verified callbacks -> Go durable inbox -> PHP settlement
transaction (ledger + wallet + payment + outbox) -> Go WebSocket/outbound merchant webhook.
The browser may display confirmed DB state, never manufacture a successful payment.

## Acceptance matrix

| Workstream | Required result | Initial evidence / status |
| --- | --- | --- |
| Credentials | Encrypted admin settings, environment separation, actual connection tests, rotation | Existing AES-GCM storage; provider clients ignore settings; Breet enum mismatch |
| Verification | Verified eligible users automatically receive a personal NGN wallet and account-provision job; retries resume after configuration | Missing |
| Fiat accounts | Unique provider-issued personal accounts; exact inbound matching; credits once | OnePipe prototype exists, last-four matching unsafe |
| Wallet accounting | Decimal strings, atomic row locks, balanced ledger, immutable operation references | Existing money writes mostly bypass ledger and use floats |
| User transfers | PIN, recipient lookup/ownership, atomic debit/credit, fee, idempotency | Existing generic transfer only reserves sender funds |
| Withdrawals | Reserve once; auto payout where configured; encrypted manual request otherwise; 24-hour due date; admin settle/release once | Admin status-only route exists; reservation lifecycle missing |
| Checkout | Card via provider-hosted/tokenized flow; per-order bank account; Breet address/asset/quote; no PAN/CVV on Flow backend | Placeholder start; fake browser test success |
| Breet | Discover supported assets/networks; create addresses; verify trade; net settlement accounting; crypto/fiat withdrawals | No client; initial Go handler unsafe |
| OnePipe | Correct approved service payloads, signatures, minor units, query/reconciliation, sandbox | Prototype client; partner-specific contract needs validation |
| PalmPay | Verify official contract including signing and environment; integrate supported rails | Official docs identified, detailed contract still to inspect |
| Merchant API | Scoped keys, idempotent create, signed verifiable status, refund/verification, examples | API keys and create-session exist; incomplete |
| Merchant webhooks | Encrypted signing key, transactional outbox, delivery leases, bounded retries, SSRF protection | Tables only |
| Go webhooks | Provider-specific auth, bounded raw body, durable inbox, replay/order handling, no wrong-payment matches | Insecure first draft; must replace |
| Reconciliation | Provider query after timeout/missed webhook, unmatched payments in review, late money never lost | Missing; current retry function only updates an error string |
| WebSockets | Scoped short-lived tickets, origin checks, single writer per client, reconnect and DB catch-up | Initial socket accepts any origin and unvalidated tokens |
| Admin | Queues, age/SLA, provider config health, retries, exceptions, audit, ledger reconciliation | Partial admin pages, status-only actions |
| Remaining UI | Funding, bank/crypto transfers, swaps, cards, payment links, balances/history all truthful and wired | Inventory ongoing; swaps trust client rate, cards only local status |
| Verification | Real DB integration, concurrent duplicate/spend tests, mocked HTTP contracts, WS/restart tests, required payment scenarios | Old static string checks insufficient |
| Operations | Repeatable migrations, sandbox seed, secrets outside webroot, reverse proxy, workers, runbook | Incomplete |

## Execution order

1. Audit all money entry points and provider contracts; close confirmed unsafe paths.
2. Implement shared decimal accounting, ledger, idempotency, user transfers and payout reservation/finalization; test on an isolated MariaDB schema.
3. Connect verified signup, durable jobs, encrypted admin credential resolver and providers.
4. Build checkout attempts, merchant API, sandbox simulator and authenticated reconciliation.
5. Replace Go prototype with inbox/jobs/WebSocket transport and exercise retries/crash recovery.
6. Wire every existing relevant UI and admin screen; remove browser-only success and fake financial values.
7. Run full acceptance matrix, document configuration and exact blockers, then reassess launch readiness.

## Research notes (2026-09-28)

- https://docs.breet.io/deposits: addresses can be per-user or per-order; completed
  deposits convert into configured receive currency and fees apply. Amount/rate at
  completion is authoritative; flagged funds are not spendable. Query by trade ID.
- https://docs.breet.io/webhooks: IP allowlist + secret header; eventId deduplication,
  eventTime ordering, durable acceptance before 2xx.
- https://docs.breet.io/api-reference/crypto-wallet/generate-wallet-address:
  POST /trades/sell/assets/{id}/generate-address; label and optional auto-settlement.
- https://v2.docs.onepipe.io/: OnePipe v2, partner-host approval and API key/secret,
  provider-specific service configuration; request reference signature and query endpoint.
- https://docs.palmpay.com/: PalmPay official open-platform documentation. Do not
  substitute third-party PalmPay resellers' contracts for this integration.
- https://paystack.com/docs/payments/accept-payments/: existing Paystack admin entry
  makes hosted checkout an available card adapter; initialization is server-side and
  payment verification must check amount/currency/reference before fulfilling.
- https://paystack.com/docs/payments/dedicated-virtual-accounts/: dedicated accounts
  require provider eligibility/activation. An API key alone does not confer approval.

## Evidence log

- 2026-09-28: PHP 8.2.12 with bcmath, PDO MySQL and OpenSSL available; MariaDB 10.4.32 reachable.
- 2026-09-28: Go 1.27.1 `go test ./...` completed successfully, but reports no test files.
  Earlier cache/time-window failures do not establish a current blocker.
- Full goal remains active. Neither lint nor static file scans prove financial correctness.
