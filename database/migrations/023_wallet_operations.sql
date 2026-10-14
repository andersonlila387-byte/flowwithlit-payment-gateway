-- Additive upgrade for repeat-safe wallet operations. MariaDB 10.4+.
ALTER TABLE transactions ADD COLUMN IF NOT EXISTS request_hash CHAR(64) NULL;
ALTER TABLE ledger_entries ADD COLUMN IF NOT EXISTS posting_key VARCHAR(191) NULL;
ALTER TABLE ledger_entries ADD UNIQUE KEY IF NOT EXISTS uq_ledger_posting_key(posting_key);
ALTER TABLE payout_requests
 ADD COLUMN IF NOT EXISTS transaction_id INT UNSIGNED NULL,
 ADD COLUMN IF NOT EXISTS execution_mode ENUM('manual','automatic') NOT NULL DEFAULT 'manual',
 ADD COLUMN IF NOT EXISTS destination_type ENUM('bank','crypto') NOT NULL DEFAULT 'bank',
 ADD COLUMN IF NOT EXISTS destination_encrypted MEDIUMBLOB NULL,
 ADD COLUMN IF NOT EXISTS destination_display VARCHAR(191) NULL,
 ADD COLUMN IF NOT EXISTS due_at DATETIME NULL,
 ADD COLUMN IF NOT EXISTS updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE payout_requests ADD UNIQUE KEY IF NOT EXISTS uq_payout_transaction(transaction_id);
ALTER TABLE payout_requests ADD INDEX IF NOT EXISTS idx_payout_sla(status,due_at);
ALTER TABLE users ADD COLUMN IF NOT EXISTS pin_failed_attempts SMALLINT UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE users ADD COLUMN IF NOT EXISTS pin_locked_until DATETIME NULL;
-- Admin uses test/live for every provider; adapters translate to provider-specific names.
UPDATE admin_integration_connections SET environment='test' WHERE environment='';
INSERT IGNORE INTO schema_migrations(version) VALUES ('023_wallet_operations');
