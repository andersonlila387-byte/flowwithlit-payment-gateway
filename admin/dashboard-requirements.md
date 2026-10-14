# Admin dashboard requirements

## Product purpose

The admin index is an operations workspace, not a customer-facing analytics page. It must help each staff role identify work that needs attention, open the correct record, and leave an auditable trail.

## Existing roles and permitted work

| Role | Primary workspace | Current server-enforced access |
| --- | --- | --- |
| Super admin | Company oversight and administration | All areas, staff management, currency settings, audit logs |
| Support | Customer care | Customer accounts, account-status changes, customer notifications |
| Reviewer | Verification | Onboarding and KYC submissions, approve or reject review decisions |
| Finance | Money operations | Wallets, transactions, payouts, wallet status changes |

The menu and dashboard content must be derived from this role. A hidden menu item is not a permission check; the API remains the source of access control.

## Proposed information architecture

### Shared navigation

- Overview
- My work queue
- Notifications
- Profile and sign out

### Support navigation

- Customers
- Customer messages

### Reviewer navigation

- Verification queue

### Finance navigation

- Payouts
- Transactions
- Wallets
- Currencies (view only unless a super admin)

### Super admin navigation

- All of the above
- Team and access
- Audit log
- Currency controls
- Platform settings (later, when those controls exist)

## Overview content

Show only information that the signed-in role can act on.

### Action queue (highest priority)

- Pending payout requests: count, total amount by currency, oldest request age
- Onboarding submissions awaiting review: count and oldest submission age
- Failed or processing transactions: count and recent items
- Frozen wallets and suspended customer accounts: count and recent changes

Each queue item needs a direct link to the filtered working list. Do not put irreversible actions directly on a summary card.

### Operational snapshot

- New customers today and this month
- Active customer accounts
- Completed transaction count and completed volume today / this month, grouped by currency
- Payment success / failure count where payment records exist
- Active versus disabled payment links
- Open customer notifications or support work, once support tickets exist

No chart should render invented values. Empty data should use a concise empty state instead of a zero-filled graph.

### Recent activity

- Latest audit events visible to super admins only
- Latest payout decisions for finance
- Latest verification decisions for reviewers
- Latest account-status or notification actions for support

Include actor, action, affected record, time, and a route to the relevant record. Avoid putting private payloads, full account numbers, API secrets, or encrypted data in activity feeds.

## Data already available

| Source | Available fields or actions |
| --- | --- |
| `users` | Identity, account type, KYC tier, status, verification state, registration and last-login dates |
| `businesses` | Legal and trading names, business status, industry, support contacts |
| `onboarding_submissions` | Status, submission time, business and user association; payload remains encrypted |
| `payout_requests` | Currency, amount, fee, status, reviewer, review time and note |
| `transactions` | Type, status, currency, amount, fee, provider, reference and creation time |
| `wallets` | Currency, available and pending balances, wallet status |
| `payment_links` and `payments` | Link state, amounts, payment outcome data |
| `notifications` | Customer messages and read status |
| `audit_logs` | Actor, action, entity, IP, user agent, context, timestamp |
| `admin_users` | Active staff membership and role |

## Backend work required before building the dashboard

Create one `GET /api/admin/overview.php` response whose queries are selected by role. It should return only the summary cards, queues, and recent activity the caller may view.

It must provide:

- server-calculated counters and monetary totals, never browser-calculated from full records;
- a selected reporting window such as today, 7 days, and current month;
- totals grouped by currency rather than combining NGN and crypto values;
- bounded recent lists with pagination or a strict limit;
- role-specific action queue counts and records;
- a server-generated timestamp so the interface can show when data was refreshed.

The present overview endpoint exposes only broad record counts. It should be replaced before the full dashboard UI is connected.

## Interface constraints

- Use a persistent left navigation on desktop and a compact mobile navigation pattern.
- Use navy as the primary action color; reserve green, yellow, and red for status meaning.
- Give every server action an immediate disabled/loading state and a clear completion or error result.
- Keep high-risk financial and access actions behind a detail page plus confirmation step.
- Design with dense tables, filters, search, status chips, and expandable details rather than circular dashboard widgets.
- Make the overview useful with no data: show empty state guidance, never demo metrics.
