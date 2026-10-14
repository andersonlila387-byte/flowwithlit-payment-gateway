USE flowwithlit_db;

CREATE TABLE IF NOT EXISTS admin_users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 uuid CHAR(36) NOT NULL UNIQUE,
 full_name VARCHAR(150) NOT NULL,
 email VARCHAR(191) NOT NULL UNIQUE,
 avatar_url VARCHAR(500) NULL,
 password_hash VARCHAR(255) NOT NULL,
 role ENUM('support','reviewer','finance','super_admin') NOT NULL DEFAULT 'support',
 status ENUM('active','disabled') NOT NULL DEFAULT 'active',
 two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0,
 two_factor_secret VARCHAR(255) NULL,
 last_login_at DATETIME NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS admin_sessions (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 admin_user_id INT UNSIGNED NOT NULL,
 token_hash CHAR(64) NOT NULL UNIQUE,
 device_info VARCHAR(255) NULL,
 ip_address VARCHAR(45) NOT NULL,
 is_2fa_verified TINYINT(1) NOT NULL DEFAULT 1,
 expires_at DATETIME NOT NULL,
 last_activity DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_admin_session_expiry(admin_user_id,expires_at),
 FOREIGN KEY(admin_user_id) REFERENCES admin_users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payout_requests (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 uuid CHAR(36) NOT NULL UNIQUE,
 user_id INT UNSIGNED NOT NULL,
 wallet_id INT UNSIGNED NULL,
 bank_account_id INT UNSIGNED NULL,
 currency_code VARCHAR(12) NOT NULL,
 amount DECIMAL(36,18) NOT NULL,
 fee_amount DECIMAL(36,18) NOT NULL DEFAULT 0,
 status ENUM('pending','approved','processing','paid','failed','cancelled') NOT NULL DEFAULT 'pending',
 provider_reference VARCHAR(191) NULL,
 review_note VARCHAR(500) NULL,
 reviewed_by INT UNSIGNED NULL,
 reviewed_at DATETIME NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_payout_status(status,created_at),
 FOREIGN KEY(user_id) REFERENCES users(id), FOREIGN KEY(wallet_id) REFERENCES wallets(id),
 FOREIGN KEY(bank_account_id) REFERENCES bank_accounts(id), FOREIGN KEY(currency_code) REFERENCES currencies(code),
 FOREIGN KEY(reviewed_by) REFERENCES users(id)
) ENGINE=InnoDB;

INSERT IGNORE INTO schema_migrations(version) VALUES ('007_admin_operations');
