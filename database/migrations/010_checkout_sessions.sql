-- Hosted checkout sessions created through the merchant API.
CREATE TABLE IF NOT EXISTS checkout_sessions (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 uuid CHAR(36) NOT NULL UNIQUE,
 public_token CHAR(64) NOT NULL UNIQUE,
 business_id INT UNSIGNED NOT NULL,
 user_id INT UNSIGNED NOT NULL,
 api_key_id INT UNSIGNED NULL,
 payment_link_id INT UNSIGNED NULL,
 origin ENUM('api','payment_link') NOT NULL,
 environment ENUM('test','live') NOT NULL,
 title VARCHAR(191) NOT NULL,
 description TEXT NULL,
 amount DECIMAL(36,18) NOT NULL,
 currency_code VARCHAR(12) NOT NULL,
 return_url VARCHAR(500) NULL,
 cancel_url VARCHAR(500) NULL,
 status ENUM('active','completed','expired','disabled') NOT NULL DEFAULT 'active',
 expires_at DATETIME NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(business_id) REFERENCES businesses(id),
 FOREIGN KEY(user_id) REFERENCES users(id),
 FOREIGN KEY(api_key_id) REFERENCES api_keys(id),
 FOREIGN KEY(payment_link_id) REFERENCES payment_links(id),
 FOREIGN KEY(currency_code) REFERENCES currencies(code),
 INDEX idx_checkout_session_lookup(public_token,status,expires_at)
) ENGINE=InnoDB;
INSERT IGNORE INTO schema_migrations(version) VALUES ('010_checkout_sessions');
