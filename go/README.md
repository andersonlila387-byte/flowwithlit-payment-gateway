# Flowwithlit Go backend

This is the initial Go service boundary for the financial backend migration.

Current scope:

- GET /healthz
- GET /readyz
- request ID propagation

Financial routes are intentionally not enabled yet. Wallet, ledger, transfer, provider, webhook, and cache packages will be added behind authenticated contracts and tests before any money-changing route is exposed.

Run locally:

~~~text
go run ./cmd/api
~~~

Default address: :8081. Override with GO_BACKEND_ADDR.