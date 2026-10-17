# Payment maintenance cron

Run this command once per minute from the Hostinger cron scheduler:

```text
/usr/bin/php /home/USER/domains/flowwithlit.com/public_html/cron/payment-worker.php
```

Replace the path with the absolute path shown by Hostinger for the `flowwithlit.com` website. The script is CLI-only, prevents overlapping runs with a database lock, and expires up to 250 pending/processing payments per run when their checkout session or payment link has passed its expiry time.

It does not process OnePipe notifications. Keep `api/webhooks/onepipe.php` blocked until OnePipe provides its official webhook signature/authentication specification and a sandbox event sample. This is required before live payment crediting can be enabled.
