# Admin dashboard review

## Purpose

The Flowwithlit admin dashboard is an internal operations workspace for a Nigerian payments and crypto platform. It should help an authorised staff member spot work that needs attention, open the relevant record, and leave the financial decision to the proper workflow. It is not a customer wallet screen, a betting dashboard, or a marketing analytics page.

The first supported value types are **NGN** and enabled crypto assets. A value in one currency must never be silently added to a value in another currency.

## Corrections required before connecting live data

| Current dashboard wording or area | Replace with | Reason |
| --- | --- | --- |
| `Welcome To Dashboard` | `Operations overview` | Describes the purpose clearly. |
| `Search for anythings...` | `Search customers, transactions, payments…` | Fixes the wording and explains the search scope. |
| `Current Balance` | Remove from the company overview, or show `Platform reserve balance` only on a finance-only page, grouped by currency | A combined platform balance can mislead staff and must come from the ledger. |
| `Total Recharge` and `Recharge` | `Payment volume` and no recharge action | Recharge is a consumer-wallet term and does not belong in payment-gateway operations. |
| `Total Profit`, `Total Income`, `Total Expenses` | `Successful payment volume`, `Fees collected`, and `Refunded volume`, each grouped by currency | These are measurable payment operations. Profit needs an accounting definition and should not be inferred from transactions. |
| `Total Deposit`, `Total Withdraw`, `Transfer Charge` | Put deposits, payouts, transfers, and fees in separate operational sections | They are different flows with different risk and approval rules. |
| `Top Users` with `Bet Placed`, `Win`, `Loss`, and `Level: Beginner` | `Top merchants` with successful payment volume, successful payments, and payment-success rate | The present wording comes from a betting product and is unrelated to Flowwithlit. |
| `Statistics` deposit/withdrawal chart | `Payment activity` showing successful, failed, and pending payment counts or volume | The dashboard’s main health signal is payment processing, not a decorative transaction chart. |
| `Sell Overview` | `Payment methods` or remove until there is real data | “Sell” is not a Flowwithlit payment operation. Useful eventual data: card, bank transfer, crypto, and their success rates. |
| `Order Placed`, `Bet Type`, `Single`, `Combo`, and sport categories | `Payment links` with Active, Disabled, Expired, Paid, and Pending totals | These labels must all be removed; they are betting-specific. |
| `Pending Transactions` | `Attention queue` | The queue should include pending payouts, failed/processing transactions, payment disputes or refunds when supported, KYC reviews, and frozen-wallet events. |
| `Account ID` in the table | `Customer / merchant` and a safe internal reference | Do not expose full account numbers in a broad dashboard table. |
| USD defaults and `Country` filters | Default to NGN; show only active currencies and use a currency filter | Nigeria and crypto are the current scope. Countries should appear only after multi-country support is enabled. |

## Recommended first dashboard layout

### 1. Action queue — highest priority

Show cards or a compact list for only work the signed-in role may act on:

- Pending payout requests: count, NGN total, oldest request age.
- Processing or failed transactions: count, latest failures, provider where available.
- KYC or onboarding submissions awaiting review: count and oldest submission age.
- Frozen wallets or suspended accounts: count and latest changes.

Every item links to a filtered list. Summary cards must not approve, pay, freeze, or reverse anything directly.

### 2. Payment health

For the selected reporting window (`Today`, `7 days`, `This month`), show:

- Successful payment count and successful payment volume by currency.
- Failed payment count and failure rate.
- Processing payment count.
- Fees collected by currency.
- Active, disabled, and expired payment links.

Show `No activity in this period` when no qualifying records exist. Do not draw a made-up zero-filled graph.

### 3. Recent operational activity

Use a small, role-aware table with:

- Time
- Safe transaction or payout reference
- Type
- Status
- Currency and amount
- Merchant or customer name when the viewer is authorised
- Provider
- A link to the record

For super admins, add a separate audit feed with actor, action, affected record, time, and a record link. Do not display secrets, KYC payloads, full bank account numbers, crypto addresses, webhook contents, or API keys.

### 4. Role-specific views

- **Super admin:** operational summary, staff/access management, currency controls, audit activity.
- **Finance:** payout queue, wallet status, payment and transaction exceptions.
- **Reviewer:** onboarding and KYC review queue and recent decisions.
- **Support:** customer account status, customer notifications, and support work.

Navigation visibility is helpful for usability, but the API must enforce these roles on every request.

## Backend contract to build first

Replace the current broad-count overview with one authenticated endpoint:

`GET /api/admin/overview.php?period=today|7d|month&currency=NGN`

The response should be calculated on the server and contain only fields permitted for the signed-in admin role:

```json
{
  "generated_at": "2026-09-20T12:00:00Z",
  "period": "month",
  "active_currencies": ["NGN", "BTC", "USDT"],
  "currency": "NGN",
  "payment_health": {
    "successful_count": 0,
    "successful_volume": "0.00",
    "failed_count": 0,
    "processing_count": 0,
    "fees_collected": "0.00"
  },
  "payment_links": {
    "active": 0,
    "disabled": 0,
    "expired": 0,
    "paid": 0,
    "pending": 0
  },
  "queues": [],
  "recent_activity": []
}
```

Implementation rules:

- Authenticate the admin session, resolve its role, then reject unauthorised fields and actions on the server.
- Use parameterised database queries and a strict whitelist for `period` and `currency`.
- Query completed, pending, processing, failed, reversed, and refunded records using their real database status; do not infer status from display labels.
- Return monetary values as strings, with their currency, to avoid floating-point errors.
- Return bounded recent lists and use a detail endpoint or pagination for additional records.
- Read balances from wallets and ledger-backed records only. Never calculate them in browser JavaScript or from an unbounded transaction list.
- Do not expose encrypted values or raw provider/webhook payloads.
- Make the client render a clear loading, empty, and retryable error state. Disable any action button while its request is pending.

## Product decisions to keep explicit

- **NGN and crypto remain separate balances and reports.** There must be no “total balance” across them until an approved pricing and reporting rule exists.
- **Payouts, account funding, customer-to-customer transfers, merchant payments, and crypto swaps are distinct transaction types.** Keep their queues and permissions distinct.
- **Payment-link checkout is live by default when the merchant is active.** API checkout can use its API key mode. This distinction belongs in checkout processing and reporting, not as an arbitrary dashboard toggle.
- **Admin actions that affect money, access, or verification require a record-level workflow, confirmation, server-side authorisation, and an audit-log entry.**

## Build order

1. Replace the reference/betting wording and remove all demo financial values from `admin/index.php`.
2. Implement the role-aware overview API and test it with an empty database and with representative NGN records.
3. Bind the action queue and payment-health panels to the API, with loading, empty, and error states.
4. Add record detail pages and approval workflows before allowing financial actions from the dashboard.
5. Add crypto reporting only per asset/network, then later add extra fiat currencies through the currency controls.
