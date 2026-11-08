# Payment worker cron

Run this command once per minute from the server's cron scheduler:

```text
/usr/bin/php /home/USER/domains/flowwithlit.com/public_html/cron/payment-worker.php
```

Replace the path with the absolute path of the deployed site. The script is CLI-only (it returns 404 over HTTP), and a database lock prevents two runs overlapping. Each run stops after about 50 seconds and does three independent tasks:

1. **Expiry:** expires up to 250 pending payments whose checkout session or payment link has passed its expiry time.
2. **Gateway jobs:** runs up to 20 queued jobs (for example, provisioning NGN receiving accounts) with exponential-backoff retries. It returns jobs left "running" by a crashed run to the queue after 15 minutes.
3. **OnePipe events:** settles up to 50 recorded OnePipe deposit events through `DepositService`. Unmatched or malformed events retry with backoff, then wait in `payment_provider_event_attempts` for manual review after 10 attempts.

OnePipe events only reach the inbox once `api/webhooks/onepipe.php` is enabled. Keep it blocked until OnePipe provides its official webhook signature specification and a sandbox event sample.

Breet events are recorded by `api/webhooks/breet.php` but not yet settled; crypto settlement will be added here once Breet trade verification is implemented.

Run the production database in UTC (`default-time-zone='+00:00'` in the MySQL config). The code compares times with `UTC_TIMESTAMP()`, while column defaults use the server's local time; on a Lagos-time server (UTC+1) a job created without an explicit time would wait an extra hour.

Test the whole pipeline locally with `php tests/payment-worker.php` (creates and then drops a temporary schema).

Apply the new retry table once with `php database/apply-payment-migrations.php`.
