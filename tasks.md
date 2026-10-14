# Flowwithlit shared app-data rollout

- [x] Create MySQL schema for identity, businesses, wallets, transactions, notifications, and audit records.
- [x] Create JSON session-check API shared by web and mobile clients.
- [x] Extend session payload with onboarding state and live user profile data.
- [x] Add a single app-shell session guard for every `app/` page.
- [x] Replace header placeholder name, email, and account type with API data.
- [x] Create notifications list/read APIs and bind the header dropdown to them.
- [x] Convert onboarding to a full-screen locked flow and save completion server-side.
- [ ] Replace browser-only onboarding draft/documents with authenticated API persistence.
- [ ] Add server-side tests for session, 2FA, onboarding, and authorization.
- [x] Document the shared website/mobile money model and API blueprint.

## Remaining page connection tasks

Launch scope is NGN plus crypto. Every page must use authenticated APIs, return an empty state when there is no data, and avoid browser-only demo balances. Provider credentials stay server-side.

### Foundation

- [ ] Add shared API helpers for authentication, validation, pagination, currency formatting, idempotency keys, and audit logging.
- [ ] Add authorization and rate-limit checks to every write endpoint.
- [x] Add a provider capability/configuration endpoint so pages can show unavailable rails without fake controls.
- [ ] Add realtime refresh events (websocket/SSE where available, visibility refresh fallback) for balances, transactions, and notifications.

### Dashboard and identity

- [x] `app/index.php`: connect summary cards, charts, selected NGN currency, and empty states to `api/dashboard/summary.php`.
- [x] `app/settings.php`: connect profile, password, withdrawal PIN, 2FA, notification preferences, and account closure to authenticated APIs.
- [x] `app/notifications.php`: connect list, read state, filters, and login notifications to the notifications APIs.
- [x] `app/developer.php`: connect API key creation, revocation, scopes, webhook settings, and delivery logs to a server-side developer API.

### Money movement

- [x] `app/payments.php`: finish Nigerian bank account lookup and transfer initiation through a server-side provider adapter; keep e-transfer disabled for launch.
- [x] `app/send-money.php`: connect internal transfers and Nigerian bank transfers, beneficiary verification, withdrawal PIN, idempotency, and transaction status.
- [x] `app/add-money.php`: connect NGN funding methods, provider references, webhook reconciliation, and pending/failed states.
- [x] `app/request-money.php`: persist payment requests, expiry, payer notifications, and request status.
- [x] `app/payment-links.php`: persist links, amounts, currency, expiry, payment status, and public checkout events.
- [x] `app/exchange.php` and `app/swap.php`: connect quote, rate lock, fee, confirmation, and settlement APIs for NGN/crypto only. Live rates require `FLOW_RATE_PROVIDER_URL` or `EXCHANGE_RATES_URL`.
- [x] `app/wallets.php`: finish wallet activation, receiving details, crypto addresses, deposits, withdrawals, and live history.
- [x] `app/transactions.php`: connect filters, detail view, exports, and reconciliation status to transaction APIs.

### Cards and card payments

- [x] `app/cards.php`: connect card inventory, issue/freeze/terminate actions, limits, controls, and card transaction history.
- [x] Add a server-side card-provider adapter with tokenized card references only; never store PAN, CVV, or raw track data.
- [ ] Add 3DS/webhook handling, authorization/capture/refund flows, dispute events, and ledger reconciliation.
- [x] Add card-specific API authorization, idempotency, audit logs, and masked-card response fields.

### Customers and administration

- [x] `app/customers.php`: connect customer search, profile, balances, transactions, and status actions.
- [x] `admin/`: add secure backend primitives for users, onboarding/KYC review, currencies, wallets, transactions, payouts, notifications, and audit logs.
- [x] Add admin-controlled currency/provider capabilities while keeping NGN plus crypto as the active launch scope.

### Verification

- [x] Add security checks for admin authorization, customer ownership, idempotency, and empty datasets.
- [ ] Expand integration tests for every read/write endpoint, authorization boundaries, idempotent retries, webhook signatures, and empty datasets.
- [ ] Add end-to-end smoke checks for login, onboarding, NGN wallet, bank lookup, transfer, crypto swap, card payment, notification, and admin review.

### External activation still required

- [ ] Supply approved OnePipe credentials, auth provider, webhook secret, and optional bank-catalog URL in the server environment.
- [ ] Supply `FLOW_RATE_PROVIDER_URL` or `EXCHANGE_RATES_URL` for live crypto/NGN quotes.
- [ ] Complete issuing-provider activation for 3DS, card authorization/capture/refunds, disputes, and secure card-detail reveal.
