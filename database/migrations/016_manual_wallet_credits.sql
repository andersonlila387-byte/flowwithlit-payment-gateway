CREATE TABLE IF NOT EXISTS manual_wallet_credits (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid CHAR(36) NOT NULL,
    transaction_id INT UNSIGNED NOT NULL,
    wallet_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    admin_user_id INT UNSIGNED NOT NULL,
    currency_code VARCHAR(12) NOT NULL,
    amount DECIMAL(36,18) NOT NULL,
    external_reference VARCHAR(120) NOT NULL,
    reason VARCHAR(500) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_manual_wallet_credits_uuid (uuid),
    UNIQUE KEY uq_manual_wallet_credit_transaction (transaction_id),
    UNIQUE KEY uq_manual_wallet_credit_reference (wallet_id, external_reference),
    KEY idx_manual_wallet_credits_created (created_at),
    CONSTRAINT fk_manual_wallet_credit_transaction FOREIGN KEY (transaction_id) REFERENCES transactions(id),
    CONSTRAINT fk_manual_wallet_credit_wallet FOREIGN KEY (wallet_id) REFERENCES wallets(id),
    CONSTRAINT fk_manual_wallet_credit_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_manual_wallet_credit_admin FOREIGN KEY (admin_user_id) REFERENCES admin_users(id),
    CONSTRAINT fk_manual_wallet_credit_currency FOREIGN KEY (currency_code) REFERENCES currencies(code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO schema_migrations (version) VALUES ('016_manual_wallet_credits');
