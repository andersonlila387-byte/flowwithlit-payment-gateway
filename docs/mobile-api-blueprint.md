# Flowwithlit mobile API blueprint

## Purpose

The website payment gateway and mobile banking app share one identity service and one financial ledger. APIs return JSON only; they never redirect clients. Web JavaScript chooses web routes, while the mobile app chooses native screens.

## Core money model

Money is recorded once in the ledger and presented differently by each client.

```text
Payment link / checkout API / virtual account
                    ↓
       destination business or personal wallet
                    ↓
           immutable ledger transaction
                    ↓
      website gateway view + mobile banking view
```

Never duplicate money between the website and mobile app. A wallet is the balance owner; a transaction is the immutable movement.

### Wallet separation

Each user may have:

- Personal wallets, e.g. personal NGN wallet and virtual account, for normal banking.
- Business wallets, e.g. business NGN wallet and virtual account, for payment collections.
- Crypto wallets by asset/network.

An explicit internal transfer moves money between a business wallet and a personal wallet. It is never silently merged.

### Transaction classification

Every transaction must include `type`, `source_channel`, `wallet_id`, `status`, `reference`, and `counterparty`.

| type | source_channel examples | Mobile presentation |
| --- | --- | --- |
| `deposit` | `virtual_account`, `bank_transfer`, `crypto_deposit` | Incoming transfer |
| `payment` | `payment_link`, `api_checkout`, `card_checkout` | Payment received |
| `transfer` | `internal_transfer`, `bank_payout` | Wallet transfer / sent money |
| `withdrawal` | `bank_payout`, `crypto_withdrawal` | Withdrawal |
| `swap` | `exchange` | Currency swap |
| `refund` | `checkout_refund` | Refund |

Payment links and API checkout payments should credit the **business wallet**. A normal transfer to the user’s virtual account credits the wallet attached to that account number.

## Authentication contract

Target base path: `/api/v1`. The current PHP auth endpoints are the initial implementation and should be migrated/aliased to this stable contract before mobile release.

| Endpoint | Method | Use |
| --- | --- | --- |
| `/api/v1/auth/register` | POST | Create account; returns JSON only |
| `/api/v1/auth/login` | POST | Returns access token, refresh token, user, and `onboarding_complete` |
| `/api/v1/auth/refresh` | POST | Exchange refresh token for new access token |
| `/api/v1/auth/logout` | POST | Revoke current refresh/session token |
| `/api/v1/auth/session` | GET | Current authenticated user/session state |
| `/api/v1/auth/totp/setup` | POST | Start Google Authenticator setup; returns manual key and `otpauth_uri` |
| `/api/v1/auth/totp/confirm` | POST | Confirm first TOTP code and enable 2FA |
| `/api/v1/auth/totp/login` | POST | Complete a pending login using a TOTP code |
| `/api/v1/auth/password/forgot` | POST | Start password reset email flow |
| `/api/v1/auth/password/reset` | POST | Reset password using verified code |

Mobile sends `Authorization: Bearer <access_token>`. It stores refresh tokens in the device secure storage, never in ordinary app preferences. No endpoint returns an OTP, password, SMTP credential, or API secret after creation.

Login handling:

```text
login response
 ├─ requires_2fa = true  → show native authenticator-code screen → /auth/totp/login
 ├─ onboarding_complete = false → show native onboarding flow
 └─ onboarding_complete = true  → show banking home
```

## Core mobile endpoints

| Endpoint | Method | Purpose |
| --- | --- | --- |
| `/api/v1/me` | GET | Name, email, avatar, account type, verification/onboarding state |
| `/api/v1/wallets` | GET | All personal/business wallets and balances |
| `/api/v1/wallets/{id}` | GET | Wallet details, virtual account, deposit instructions |
| `/api/v1/transactions` | GET | Cursor-paginated transaction history with filters |
| `/api/v1/transactions/{uuid}` | GET | Receipt/details for one transaction |
| `/api/v1/transfers` | POST | Create internal/bank transfer; requires idempotency key and Withdrawal PIN/TOTP where applicable |
| `/api/v1/payment-links` | GET/POST | List/create business collection links |
| `/api/v1/notifications` | GET | Paginated notification feed and unread count |
| `/api/v1/notifications/read` | POST | Mark one/all notifications read |
| `/api/v1/onboarding` | GET/PUT | Read/save onboarding draft and completion state |

## Response and error shape

Success:

```json
{ "success": true, "data": {}, "meta": { "next_cursor": null } }
```

Failure:

```json
{ "success": false, "error": { "code": "INSUFFICIENT_FUNDS", "message": "Your available balance is too low." } }
```

Use HTTP status codes: `400` validation, `401` unauthenticated, `403` forbidden/verification restriction, `409` conflict/idempotency, `422` business rule, `429` rate limit, `500` unexpected failure.

Every money-creating request must include `Idempotency-Key`. The same key must return the original transaction result, not create a second payment.

## Notifications and high-volume payments

The app receives transaction history from `/transactions`; notifications are a separate delivery layer. Do not send a push for every small merchant payment by default.

- Always push: withdrawals, transfers, failed/reversed payments, security events, verification decisions.
- User-configurable: individual collection/payment alerts.
- Merchant default for high volume: grouped pushes such as “12 payments received · ₦245,000 total”.
- Individual records always remain visible in transaction history.

Push notifications should carry only a transaction UUID/deep link—not amount, account number, or other sensitive data on the lock screen unless the user opts in.

## Security and ownership rules

- The Go banking engine is the source of truth for balances, ledger posting, fees, limits, provider webhooks, reversals, and idempotency.
- PHP currently owns the web auth/API layer; it must delegate/validate identity consistently with the Go engine, never create a second user identity.
- The mobile app must not calculate balances or mark payments complete locally.
- Do not expose internal database IDs when a UUID/reference is available.
- Onboarding, verification, live API key access, and business membership are enforced server-side, not only in the UI.

## Implementation status

- Current: PHP JSON authentication/session API, MySQL baseline schema, basic onboarding completion state, notification list/read APIs.
- Next before mobile handoff: version `/api/v1`, add refresh-token rotation, build Go ledger/wallet APIs, provider webhooks, server-side onboarding persistence/uploads, and automated API tests.
