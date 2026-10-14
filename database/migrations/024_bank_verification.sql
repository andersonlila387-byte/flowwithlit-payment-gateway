CREATE TABLE IF NOT EXISTS api_rate_limits (
 scope VARCHAR(64) NOT NULL,
 subject_hash CHAR(64) NOT NULL,
 window_start BIGINT UNSIGNED NOT NULL,
 attempts INT UNSIGNED NOT NULL DEFAULT 1,
 expires_at DATETIME NOT NULL,
 PRIMARY KEY(scope,subject_hash,window_start),
 INDEX idx_api_limit_expiry(expires_at)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS bank_destination_verifications (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 token_hash CHAR(64) NOT NULL UNIQUE,
 user_id INT UNSIGNED NOT NULL,
 provider VARCHAR(40) NOT NULL,
 environment ENUM('test','live') NOT NULL,
 bank_code VARCHAR(12) NOT NULL,
 destination_encrypted MEDIUMBLOB NOT NULL,
 expires_at DATETIME NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_bank_verify_user(user_id,expires_at),
 FOREIGN KEY(user_id) REFERENCES users(id)
) ENGINE=InnoDB;
INSERT IGNORE INTO schema_migrations(version) VALUES ('024_bank_verification');
