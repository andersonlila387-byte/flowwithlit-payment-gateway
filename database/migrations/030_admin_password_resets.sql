-- One-time admin password reset links. Only a SHA-256 hash of the token is stored;
-- the token itself exists only in the emailed link. Links expire after 30 minutes and
-- are single use (used_at). Requesting a new link invalidates older unused ones.
CREATE TABLE IF NOT EXISTS admin_password_resets (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  admin_user_id INT UNSIGNED NOT NULL,
  token_hash CHAR(64) NOT NULL UNIQUE,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,
  requested_ip VARCHAR(45) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_admin_resets_admin (admin_user_id, used_at),
  INDEX idx_admin_resets_expiry (expires_at),
  FOREIGN KEY (admin_user_id) REFERENCES admin_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO schema_migrations(version) VALUES ('030_admin_password_resets');
