# Flowwithlit Go Plan — 2-Day Test Readiness

Date: 2026-09-28  
Deadline: next test window in approximately two days

## Go decision

Ship a truthful, authenticated NGN wallet flow first:

1. Users see only wallets that actually exist for their account.
2. A new user sees All 0, Fiat 0, and Crypto 0.
3. A user with one NGN wallet sees All 1, Fiat 1, and Crypto 0.
4. NGN receiving-account provisioning is server-side, idempotent, and connected to OnePipe.
5. Balances, account state, transactions, and errors come from authenticated APIs.

Do not expand this two-day release into every currency, card, swap, merchant, or production-grade partner integration.

## Immediate blocker: fake wallet counts

The wallet page contains hardcoded values:

- All 10
- Fiat Currencies 5
- Cryptocurrencies 5
- Filter titles ending in (10), (5), and (5)

The live list API already returns user-scoped wallet rows, but the hardcoded values appear before the response and the filter handler still writes hardcoded totals.

### Required fix

Use the authenticated list response as the only source of truth:

- Initialize badges and section titles to zero.
- After /api/wallets/list.php succeeds, calculate allCount, fiatCount, and cryptoCount from result.wallets.
- Update All, Fiat, Crypto, and the section title from those values.
- Remove every hardcoded 10, 5, and 5 from wallet display/filter logic.
- Query #walletsGrid .wallet-card inside filter handlers, or re-bind after the API replaces the grid. The current walletCards NodeList is captured before API-rendered cards exist.
- Keep the SQL user-scoped and exclude closed wallets. Launch scope is NGN plus crypto.

### Acceptance checks

For a user with one fake NGN wallet:

- Initial state never shows 10/5/5.
- After load: All 1, Fiat 1, Crypto 0.
- Exactly one NGN card is shown; no USD/EUR/GBP/CAD demo cards.
- Fiat filter shows one card; Crypto filter shows an empty state.
- Refresh and tab switching do not restore stale counts.

For a clean user:

- All 0, Fiat 0, Crypto 0.
- Empty state is clear and not presented as provider failure.

## Build order

### Day 1 morning — make the UI truthful

1. Remove hardcoded wallet counts and demo balances.
2. Render loading, empty, success, and failure states from the API.
3. Render counts and filter titles from the returned wallet array.
4. Test no wallet, one NGN wallet, NGN plus one crypto wallet, and API failure/401.
5. Confirm refresh and tab switching do not restore stale demo counts.

### Day 1 afternoon — lock NGN account flow

1. Confirm authentication on:
   - GET /api/wallets/list.php
   - POST /api/wallets/provision-fiat.php
   - POST /api/wallets/activate.php
   - GET /api/dashboard/summary.php
2. Make NGN provisioning idempotent: existing wallet/account is returned on retry; double-clicks never create duplicates.
3. Require a verified phone before OnePipe account opening.
4. Keep provider calls in services/OnePipeClient.php only.
5. Keep provider account numbers encrypted; expose only what the authenticated receive flow needs.
6. On provider failure, mark the local receiving-account row failed or retryable and never display fake success.

### Day 2 morning — test the API contract

Browser/session API:

~~~http
GET /api/wallets/list.php
Authorization: Bearer user-session-token
Accept: application/json
~~~

~~~http
POST /api/wallets/provision-fiat.php
Authorization: Bearer user-session-token
Content-Type: application/json

{"currency":"NGN"}
~~~

~~~http
GET /api/dashboard/summary.php?currency=NGN
Authorization: Bearer user-session-token
~~~

Expected responses:

- 200: success or existing resource returned.
- 401: missing, expired, or pending-auth session.
- 422: unsupported currency, missing phone, or invalid request.
- 502: provider failure; show a retryable error and do not invent account data.

The dashboard summary must aggregate only the authenticated user''s wallet and transaction rows.

### Business/public API direction

The database already has api_keys, webhook_endpoints, and webhook_deliveries. Public integrations should use business API keys, not browser session tokens:

~~~http
Authorization: Bearer business-secret
Idempotency-Key: unique-request-key
~~~

Minimum public API after the test:

- GET /v1/wallets
- GET /v1/wallets/{currency}
- POST /v1/receiving-accounts
- GET /v1/transactions
- POST /v1/transfers
- POST /v1/webhook-endpoints

Every write must authenticate the business, validate ownership/currency/status, require idempotency, write a local transaction first, call the provider server-side, and update status from provider/webhook results.

### Day 2 afternoon — provider/webhook release test

1. Use OnePipe only for NGN account opening and NGN transfer rails in this release candidate.
2. Store request and provider references locally.
3. Convert timeout and non-2xx responses into pending/failed states.
4. Verify provider status and references before state changes.
5. Verify webhooks, deduplicate by provider event/reference, update local state once, and notify the user after the state change.
6. Run the release checklist against one existing-wallet account and one clean account.

## System connection map

~~~text
Browser
  -> authenticated PHP API
     -> MySQL users/wallets/transactions
     -> OnePipeClient on the server
        -> OnePipe account opening / transfer API
  <- verified provider webhook
     -> webhook inbox and transaction update
  <- wallet list / dashboard summary JSON
~~~

Data ownership:

- users: identity and verification.
- wallets: one personal wallet per user/currency/type.
- wallet_receiving_accounts: provider lifecycle and encrypted account data.
- transactions: local operation and idempotency reference.
- provider_webhook_events: provider event deduplication.
- currencies: enabled launch currencies.
- api_keys: business integration authentication.
- webhook_endpoints and webhook_deliveries: outbound business notifications.

## Security and financial rules

- Never trust user_id, balance, status, or provider references from the browser.
- Scope every read/write by authenticated user or business.
- Never expose OnePipe credentials or signatures.
- Keep money in DECIMAL/database decimal strings, not floating point.
- Require idempotency on provisioning, transfers, and balance-changing operations.
- Do not mark transfers complete from a client response alone; use verified provider results/webhooks.
- Do not show balances or account numbers from demo markup while the API is loading.
- Log safe references and error codes, not decrypted account numbers or secrets.

## Release acceptance checklist

- [ ] Clean user sees zero counts and an intentional empty state.
- [ ] One-NGN-wallet user sees exactly All 1 / Fiat 1 / Crypto 0.
- [ ] No 10/5/5 strings remain in rendered wallet UI or filter logic.
- [ ] No hardcoded demo balances/cards remain.
- [ ] Desktop shows four cards per row; mobile remains one/two columns.
- [ ] NGN provisioning requires authenticated, verified-phone user.
- [ ] Repeating provisioning returns the existing account.
- [ ] OnePipe credentials remain server-side.
- [ ] Provider failure is visible as a retryable error.
- [ ] 401, 422, 502, and success responses are tested.
- [ ] User A cannot see User B''s wallet, account, or transactions.
- [ ] Dashboard totals match wallet rows for NGN.
- [ ] Duplicate request/idempotency behavior is tested.
- [ ] Duplicate webhook delivery does not double-credit.

## Go/no-go

Go for the next test only if wallet counts are data-driven, NGN account provisioning is idempotent, and failure states cannot create or display fake financial data.

If any of those three fail, keep the release in test mode and fix them before adding another product surface.


## 7. Production task: remove demo data and add safe live-data caching

### Objective

Before production, every visible value must come from the authenticated database/provider flow. Cached data may improve loading speed, but it must never become the source of truth for money, permissions, or transaction decisions.

### Workstream A — audit and remove demo content

Search the full application for:

- Hardcoded balances, account numbers, transaction amounts, customer names, dates, and statuses.
- Placeholder cards and mock arrays.
- Static counts such as 10, 5, and 5.
- Demo notifications, fake transactions, fake charts, fake exchange rates, and fake API responses.
- UI fallback text that looks like a successful financial result.
- Seed/test data that is loaded into a production user account.

For each result, choose one of three outcomes:

1. Replace it with a database/API value.
2. Replace it with a truthful empty state such as “No transactions yet”.
3. Remove it if the feature is not ready for production.

Required route sweep:

- /app/index.php
- /app/wallets.php
- /app/transactions.php
- /app/send-money.php
- /app/request-money.php
- /app/add-money.php
- /app/payments.php
- /app/payment-links.php
- /app/cards.php
- /app/swap.php
- /app/exchange.php
- /app/customers.php
- /app/notifications.php
- /app/developer.php
- /app/settings.php

No page should display fake success, fake balance, fake account data, or fake activity while an API request is pending or unavailable.

### Workstream B — define the caching policy

Use two separate concepts:

1. Browser/application display cache: only to make the interface load quickly.
2. Server/database/provider truth: always used for authorization and money-changing actions.

Recommended display-cache rules:

| Data | Display cache | Background refresh | Action rule |
| --- | ---: | ---: | --- |
| Wallet balances | 15–30 seconds | On page load, focus, and interval | Re-read server before send/withdraw |
| Wallet list | 30–60 seconds | On page load and wallet changes | Never create from cache |
| Dashboard summary | 15–30 seconds | On page load and focus | Display only |
| Transactions | 30–60 seconds | On page load and filter change | Detail/status from server |
| Notifications | 30–60 seconds | On focus | Mark-read request goes to server |
| Exchange rates | Do not use stale rates for trades | Refresh before quote | Trade uses a fresh server quote |
| Account numbers/secrets | Do not cache | Fetch authenticated data only | Server validates ownership |

Cache records should include:

- user ID/session scope;
- resource name and request parameters;
- response data;
- saved-at timestamp;
- schema/version number;
- optional ETag or last-modified value.

Never use one user''s cache for another user. Clear user-scoped cache on logout, session change, account switch, or authentication failure.

### Workstream C — implement stale-while-revalidate

Create one small client data layer instead of separate cache logic on every page.

Required behavior:

1. Read a user-scoped cached response.
2. If present, render it immediately.
3. Show a subtle “Updating…” or “Last updated” state.
4. Fetch fresh data with the authenticated session.
5. On success, update the page and replace the cache.
6. On failure:
   - keep cached data only if it is not beyond the maximum stale window;
   - show “Unable to refresh” and the last-updated time;
   - never show a cached empty state as if it were confirmed current.
7. Prevent duplicate refresh requests with one in-flight request per resource.
8. Invalidate related cache entries after a successful wallet activation, account provisioning, transfer, deposit, payment, or transaction update.

Suggested cache keys:

- flowwithlit:{user-session-id}:wallets
- flowwithlit:{user-session-id}:dashboard:NGN
- flowwithlit:{user-session-id}:transactions:{filters}
- flowwithlit:{user-session-id}:notifications

Do not put bearer tokens, OnePipe credentials, full account numbers, or decrypted provider payloads into cache.

### Workstream D — server/API headers and validation

Keep financial API responses protected:

- Use no-store for sensitive responses where required.
- Use ETag or conditional requests only where the response is safe and user-scoped.
- Keep authorization checks on every request.
- Never trust a client-provided cached balance, status, user ID, or transaction result.
- Return clear 401, 403, 404, 409, 422, and 502 responses.
- Add request IDs and safe error logging for production diagnosis.

The server must remain authoritative even if the browser displays stale data for a few seconds.

### Workstream E — live provider and webhook truth

For OnePipe and other providers:

- Store the local transaction before calling the provider.
- Store request reference and provider reference.
- Treat timeout or uncertain provider responses as pending, not successful.
- Verify webhook authenticity and deduplicate events.
- Update the local wallet/transaction once per verified event.
- Invalidate wallet, dashboard, transaction, and notification caches after state changes.
- Never credit a wallet from a browser response or an unverified webhook.

### Two-day execution schedule

#### Day 1 — truth first

Morning:

- Audit all app routes and APIs for demo content.
- Fix wallet count and filter logic.
- Remove static demo cards and balances.
- Replace fake fallbacks with loading, empty, error, or retry states.
- Verify every visible balance/count is tied to a response or database value.

Afternoon:

- Implement the shared client cache helper.
- Add user-scoped cache keys, timestamps, stale limits, and in-flight request deduplication.
- Add stale-while-revalidate to wallets and dashboard first.
- Add cache invalidation after wallet activation/provisioning.
- Test logout and user switching so cached data cannot leak.

#### Day 2 — complete live refresh and production test

Morning:

- Add caching to transactions and notifications where safe.
- Add background refresh on page focus and a bounded polling interval.
- Add updating/last-updated/error indicators.
- Wire provider/webhook state changes to cache invalidation.
- Confirm send, withdrawal, payment, and account provisioning always use fresh server validation.

Afternoon:

- Run the clean-user and existing-wallet test flows.
- Test expired sessions, API failure, provider timeout, duplicate click, duplicate webhook, and browser refresh.
- Search again for demo strings and placeholder financial values.
- Confirm no secrets or full account numbers are stored in browser cache.
- Capture a production checklist result for each route.

### Definition of done

- [ ] No demo balances, cards, counts, accounts, transactions, notifications, or fake success states remain.
- [ ] A clean account shows truthful zero/empty states.
- [ ] The current test user with one NGN wallet shows All 1 / Fiat 1 / Crypto 0.
- [ ] Cached data appears quickly but is refreshed in the background.
- [ ] Every cached record is user-scoped and time-bounded.
- [ ] Cached data never authorizes a financial action.
- [ ] Wallet, dashboard, and transaction caches invalidate after state changes.
- [ ] Logout and session changes clear or isolate cached data.
- [ ] Provider uncertainty is shown as pending, never fake success.
- [ ] Production route sweep and final no-demo search pass.


## 8. Go kernel and Go backend build plan

### Purpose

The Go plan is not a plan to rewrite the whole PHP application immediately. It is a controlled migration plan for introducing a Go financial backend beside the current PHP application, then moving money-sensitive paths behind Go once the contracts and tests are stable.

The current repository has no Go files. The current production path is PHP API endpoints under api/, PHP service classes under services/, and MySQL. Therefore Go should begin as a separate internal service, not as an unplanned rewrite of existing PHP pages.

### Target architecture

~~~text
Browser
  -> existing PHP pages and session/auth layer
     -> Go backend internal/public API
        -> Go kernel/domain services
           -> MySQL
           -> OnePipe adapter
           -> webhook inbox
           -> outbox/event publisher
     <- JSON response

OnePipe webhook
  -> PHP compatibility receiver or Go webhook endpoint
     -> Go event validation and deduplication
     -> ledger/transaction update
     -> cache invalidation and outbound webhook event
~~~

### Where Go will be added

Create a separate top-level Go service:

~~~text
/go
  /cmd/api                 HTTP server entrypoint
  /cmd/worker              asynchronous jobs and webhook/outbox worker
  /internal/config         environment and configuration
  /internal/httpapi        routes, middleware, request/response DTOs
  /internal/auth           session/API-key verification
  /internal/wallets        wallet application services
  /internal/ledger         balances, double-entry rules, transaction posting
  /internal/transfers      transfer state machine and idempotency
  /internal/providers      provider interfaces and OnePipe implementation
  /internal/webhooks       signature verification, deduplication, handlers
  /internal/notifications  notification events
  /internal/cache          cache interface and invalidation
  /internal/store          MySQL repositories and transactions
  /migrations               only if Go-specific schema changes are required
  go.mod
  Dockerfile
~~~

Go package boundaries must follow business responsibility, not one package per screen. The frontend routes remain PHP routes until each backend capability is migrated and tested.

### What the Go kernel owns

The Go kernel is the financial core, not an operating-system kernel. It owns the rules that must be identical for browser actions, public API calls, provider callbacks, and background workers:

- authenticated actor and business ownership checks;
- wallet and currency eligibility;
- transaction lifecycle and allowed state transitions;
- idempotency keys and duplicate request handling;
- balance reservation, debit, credit, and reversal rules;
- double-entry ledger posting;
- provider request/reference correlation;
- webhook verification and event deduplication;
- safe error codes and audit events;
- outbox events for cache invalidation and customer webhooks.

No handler should directly change balances with ad-hoc SQL. Handlers call Go application services; application services use repositories and a database transaction; the ledger is the only balance-changing path.

### First Go slice: NGN wallet truth

The first slice should be deliberately narrow:

1. List the authenticated user''s wallets.
2. Return the NGN wallet and actual user-scoped counts.
3. Provision or return the user''s existing NGN receiving account.
4. Return the NGN dashboard summary.
5. Receive and process the relevant provider webhook.
6. Invalidate wallet/dashboard/transaction cache after a confirmed state change.

The first slice must not include every currency, cards, swaps, or a complete PHP replacement.

### Communication between PHP and Go

Use versioned JSON over internal HTTP during migration:

- PHP browser pages call the Go service through a server-side proxy or configured internal base URL.
- PHP forwards the authenticated user/session context after it has validated the current session.
- Go re-validates the actor and resource ownership; it must not trust arbitrary user IDs from PHP.
- Service-to-service requests include a signed internal header or private network authentication.
- Go returns stable JSON envelopes with success, data, error code, request ID, and optional retry information.
- PHP maps Go errors to the current browser UX without changing financial meaning.

Example internal request headers:

~~~http
Authorization: Bearer user-session-token
X-Flow-Service: php-web
X-Flow-Request-Id: request-id
Idempotency-Key: unique-write-key
~~~

The browser must not receive internal service credentials. The Go base URL and service secret belong only in server environment configuration.

### Database connection and ownership

For the first migration, Go and PHP may share the existing MySQL schema, but ownership must be explicit:

- Go owns new money-sensitive writes after a route is migrated.
- PHP may read Go-owned data through Go APIs or read-only repositories during transition.
- Do not allow PHP and Go to implement separate balance mutations for the same operation.
- Use MySQL transactions and row locks for balance/ledger changes.
- Preserve existing tables: wallets, transactions, ledger_accounts, ledger_entries, ledger_lines, wallet_receiving_accounts, provider_webhook_events, api_keys, webhook_endpoints, and webhook_deliveries.
- Add migrations only for missing constraints, event records, idempotency records, or outbox records.
- Every migration must be backward-compatible while PHP and Go run together.

### Provider connection

Define a provider interface in Go:

~~~text
OpenReceivingAccount
LookupBankAccount
CreateTransfer
GetTransferStatus
VerifyWebhook
~~~

The first implementation is OnePipe. The rest of the Go kernel must not depend directly on OnePipe request shapes.

Provider flow:

1. Go validates the authenticated request and idempotency key.
2. Go writes a pending local operation and request reference.
3. Go calls the provider adapter.
4. Go stores the provider reference and safe provider status.
5. A verified webhook or trusted provider response advances the state machine.
6. Go posts the ledger entry exactly once.
7. Go publishes cache invalidation and customer webhook events.

Provider timeout means pending or retryable, never completed.

### API layers

Keep three API layers distinct:

1. Browser API: authenticated user session, used by current PHP pages.
2. Internal Go API: private PHP-to-Go calls during migration.
3. Public business API: API-key authenticated, versioned under /v1, with idempotency and webhooks.

Initial Go endpoints:

~~~text
GET  /internal/v1/wallets
GET  /internal/v1/dashboard/summary?currency=NGN
POST /internal/v1/receiving-accounts
POST /internal/v1/transfers
POST /internal/v1/webhooks/onepipe
GET  /healthz
GET  /readyz
~~~

Later public endpoints can be exposed only after the internal contract, authentication, rate limits, audit logs, and webhook delivery behavior are tested.

### Caching and Go

The Go service is the source of truth. Caching is an optimization:

- Go may use a cache interface for read models such as wallet lists and dashboard summaries.
- Cache keys must include authenticated user/business scope, resource, currency, and schema version.
- Writes publish invalidation events after the database transaction commits.
- Do not cache authorization decisions, provider secrets, full account numbers, or balances used to authorize a write.
- If Redis is not available for the first test, use short-lived browser display caching only and keep Go reads database-backed.
- A cache outage must degrade to a database read, not to fake or empty financial data.

### Go implementation order

#### Phase 1 — foundation

- [ ] Add go/go.mod with pinned Go version.
- [ ] Add cmd/api and cmd/worker entrypoints.
- [ ] Add configuration, structured logging, request IDs, health checks, and graceful shutdown.
- [ ] Add MySQL connection pool and transaction helper.
- [ ] Add repository interfaces and test database configuration.
- [ ] Add stable error envelope and API versioning.
- [ ] Add internal authentication and service-to-service authentication.

#### Phase 2 — financial kernel

- [ ] Add wallet repository and user/business ownership checks.
- [ ] Add transaction and idempotency repository.
- [ ] Add ledger posting service with atomic database transactions.
- [ ] Add state-machine validation for pending, processing, completed, failed, reversed, and cancelled.
- [ ] Add audit/event records for every money-sensitive transition.
- [ ] Add unit tests for duplicate requests, insufficient balance, retries, and reversals.

#### Phase 3 — NGN provider slice

- [ ] Add provider interface.
- [ ] Add OnePipe adapter using server-only configuration.
- [ ] Migrate NGN account provisioning to Go.
- [ ] Migrate NGN wallet list and dashboard read models to Go.
- [ ] Add verified OnePipe webhook handling and deduplication.
- [ ] Add cache invalidation/outbox event after confirmed changes.
- [ ] Keep PHP compatibility endpoints until Go has passed shadow and acceptance tests.

#### Phase 4 — cutover

- [ ] Route one endpoint at a time from PHP to Go.
- [ ] Compare PHP and Go read results in test mode without double-writing.
- [ ] Enable Go writes for one operation only, starting with NGN account provisioning.
- [ ] Monitor errors, latency, pending provider operations, and duplicate events.
- [ ] Remove the old PHP write path only after rollback has been tested.
- [ ] Expand to transfers and public API after the NGN slice is stable.

### Two-day reality check

A complete Go backend cannot be safely built and production-proven in two days. The two-day target is:

- define the Go service boundary;
- scaffold health/config/logging;
- define contracts and repositories;
- fix the current PHP demo-data problem;
- prove the NGN wallet/account path;
- optionally implement Go in shadow/read-only mode;
- do not cut over money-changing writes until idempotency, ledger, provider, and rollback tests pass.

The current production test should use the reliable existing PHP path while Go is introduced behind a feature flag or internal route. Go becomes the production money path only after it passes the same acceptance tests and has a tested rollback route.

### Go completion criteria

- [ ] Go service starts with health and readiness checks.
- [ ] No browser-visible route depends on hardcoded demo financial data.
- [ ] Go contracts are documented and versioned.
- [ ] Go re-validates authentication and ownership.
- [ ] One operation has one authoritative write path.
- [ ] All money-changing writes are idempotent and ledger-backed.
- [ ] OnePipe secrets remain server-side.
- [ ] Webhooks are verified and deduplicated.
- [ ] Cache invalidation occurs after committed state changes.
- [ ] PHP-to-Go rollback is possible without data loss.
