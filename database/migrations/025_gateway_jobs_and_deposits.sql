CREATE TABLE IF NOT EXISTS gateway_jobs (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 job_key VARCHAR(191) NOT NULL UNIQUE,
 job_type VARCHAR(80) NOT NULL,
 payload JSON NOT NULL,
 status ENUM('queued','running','completed','failed') NOT NULL DEFAULT 'queued',
 attempts INT UNSIGNED NOT NULL DEFAULT 0,
 available_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
 locked_at DATETIME(6) NULL,
 locked_by VARCHAR(100) NULL,
 last_error VARCHAR(500) NULL,
 completed_at DATETIME(6) NULL,
 created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
 updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_gateway_jobs_ready(status,available_at),
 INDEX idx_gateway_jobs_locked(status,locked_at)
) ENGINE=InnoDB;
ALTER TABLE wallet_receiving_accounts ADD COLUMN IF NOT EXISTS account_number_hash CHAR(64) NULL AFTER account_number_last4;
ALTER TABLE wallet_receiving_accounts ADD INDEX IF NOT EXISTS idx_receiving_account_hash(provider,account_number_hash);
CREATE TABLE IF NOT EXISTS gateway_deposit_events (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 provider VARCHAR(50) NOT NULL,
 provider_reference VARCHAR(191) NOT NULL,
 event_id VARCHAR(191) NULL,
 receiving_account_id BIGINT UNSIGNED NOT NULL,
 transaction_id INT UNSIGNED NULL,
 currency_code VARCHAR(12) NOT NULL,
 amount DECIMAL(36,18) NOT NULL,
 payload JSON NOT NULL,
 created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
 UNIQUE KEY uq_gateway_deposit_provider_ref(provider,provider_reference),
 UNIQUE KEY uq_gateway_deposit_event(provider,event_id),
 FOREIGN KEY(receiving_account_id) REFERENCES wallet_receiving_accounts(id),
 FOREIGN KEY(transaction_id) REFERENCES transactions(id),
 FOREIGN KEY(currency_code) REFERENCES currencies(code)
) ENGINE=InnoDB;
INSERT IGNORE INTO schema_migrations(version) VALUES ('025_gateway_jobs_and_deposits');
