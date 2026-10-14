-- Merchant-controlled checkout return destinations.
ALTER TABLE payment_links ADD COLUMN IF NOT EXISTS return_url VARCHAR(500) NULL AFTER expires_at;
ALTER TABLE payment_links ADD COLUMN IF NOT EXISTS cancel_url VARCHAR(500) NULL AFTER return_url;
INSERT IGNORE INTO schema_migrations(version) VALUES ('009_checkout_redirects');
