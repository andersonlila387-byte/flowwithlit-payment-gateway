-- Provider-owned receiving accounts and webhook inbox. Safe to run repeatedly.
CREATE TABLE IF NOT EXISTS wallet_receiving_accounts (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 uuid CHAR(36) NOT NULL UNIQUE,
 wallet_id INT UNSIGNED NOT NULL,
 user_id INT UNSIGNED NOT NULL,
 provider VARCHAR(50) NOT NULL,
 provider_customer_ref VARCHAR(191) NULL,
 provider_account_ref VARCHAR(191) NULL,
 currency_code VARCHAR(12) NOT NULL,
 account_name VARCHAR(191) NULL,
 bank_name VARCHAR(120) NULL,
 bank_code VARCHAR(30) NULL,
 account_number_encrypted TEXT NULL,
 account_number_last4 CHAR(4) NULL,
 account_number_display VARCHAR(32) NULL,
 status ENUM('provisioning','active','inactive','failed') NOT NULL DEFAULT 'provisioning',
 provider_payload JSON NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 UNIQUE KEY uq_wallet_provider_currency(wallet_id,provider,currency_code),
 UNIQUE KEY uq_provider_account_ref(provider,provider_account_ref),
 FOREIGN KEY(wallet_id) REFERENCES wallets(id) ON DELETE CASCADE,
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 FOREIGN KEY(currency_code) REFERENCES currencies(code)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS provider_webhook_events (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 provider VARCHAR(50) NOT NULL,
 event_hash CHAR(64) NOT NULL,
 event_type VARCHAR(100) NULL,
 payload JSON NOT NULL,
 processed_at DATETIME NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_provider_event(provider,event_hash)
) ENGINE=InnoDB;

INSERT IGNORE INTO schema_migrations(version) VALUES ('006_provider_wallets');
