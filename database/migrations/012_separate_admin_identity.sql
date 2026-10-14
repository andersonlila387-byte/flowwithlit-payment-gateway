USE flowwithlit_db;

-- Convert the legacy role-link table into a self-contained administrator table.
ALTER TABLE admin_users
  ADD COLUMN IF NOT EXISTS legacy_user_id INT UNSIGNED NULL AFTER id,
  ADD COLUMN IF NOT EXISTS uuid CHAR(36) NULL AFTER legacy_user_id,
  ADD COLUMN IF NOT EXISTS full_name VARCHAR(150) NULL AFTER uuid,
  ADD COLUMN IF NOT EXISTS email VARCHAR(191) NULL AFTER full_name,
  ADD COLUMN IF NOT EXISTS password_hash VARCHAR(255) NULL AFTER email,
  ADD COLUMN IF NOT EXISTS two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER password_hash,
  ADD COLUMN IF NOT EXISTS two_factor_secret VARCHAR(255) NULL AFTER two_factor_enabled,
  ADD COLUMN IF NOT EXISTS last_login_at DATETIME NULL AFTER two_factor_secret;

SET @admin_has_user_id := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='admin_users' AND COLUMN_NAME='user_id');
SET @admin_copy_sql := IF(@admin_has_user_id=1,
  'UPDATE admin_users au JOIN users u ON u.id=au.user_id SET au.legacy_user_id=au.user_id,au.uuid=COALESCE(au.uuid,UUID()),au.full_name=COALESCE(au.full_name,u.full_name),au.email=COALESCE(au.email,u.email),au.password_hash=COALESCE(au.password_hash,u.password_hash),au.two_factor_enabled=COALESCE(au.two_factor_enabled,u.two_factor_enabled),au.two_factor_secret=COALESCE(au.two_factor_secret,u.two_factor_secret),au.last_login_at=COALESCE(au.last_login_at,u.last_login_at)',
  'SELECT 1');
PREPARE admin_copy_stmt FROM @admin_copy_sql; EXECUTE admin_copy_stmt; DEALLOCATE PREPARE admin_copy_stmt;

UPDATE admin_users SET uuid=UUID() WHERE uuid IS NULL;
ALTER TABLE admin_users
  MODIFY uuid CHAR(36) NOT NULL,
  MODIFY full_name VARCHAR(150) NOT NULL,
  MODIFY email VARCHAR(191) NOT NULL,
  MODIFY password_hash VARCHAR(255) NOT NULL;

SET @admin_fk := (SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='admin_users' AND COLUMN_NAME='user_id' AND REFERENCED_TABLE_NAME='users' LIMIT 1);
SET @admin_fk_sql := IF(@admin_fk IS NULL, 'SELECT 1', CONCAT('ALTER TABLE admin_users DROP FOREIGN KEY `', @admin_fk, '`'));
PREPARE admin_fk_stmt FROM @admin_fk_sql; EXECUTE admin_fk_stmt; DEALLOCATE PREPARE admin_fk_stmt;
SET @admin_user_index := (SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='admin_users' AND COLUMN_NAME='user_id' AND INDEX_NAME<>'PRIMARY' LIMIT 1);
SET @admin_user_index_sql := IF(@admin_user_index IS NULL, 'SELECT 1', CONCAT('ALTER TABLE admin_users DROP INDEX `', @admin_user_index, '`'));
PREPARE admin_user_index_stmt FROM @admin_user_index_sql; EXECUTE admin_user_index_stmt; DEALLOCATE PREPARE admin_user_index_stmt;
ALTER TABLE admin_users DROP COLUMN IF EXISTS user_id;
ALTER TABLE admin_users ADD UNIQUE KEY IF NOT EXISTS uq_admin_uuid (uuid), ADD UNIQUE KEY IF NOT EXISTS uq_admin_email (email);

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

CREATE TABLE IF NOT EXISTS admin_login_attempts (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 ip_address VARCHAR(45) NOT NULL,
 email VARCHAR(191) NOT NULL,
 attempts TINYINT UNSIGNED NOT NULL DEFAULT 1,
 last_attempt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_admin_attempt(ip_address,email)
) ENGINE=InnoDB;

ALTER TABLE audit_logs ADD COLUMN IF NOT EXISTS admin_user_id INT UNSIGNED NULL AFTER user_id;
ALTER TABLE audit_logs ADD INDEX IF NOT EXISTS idx_audit_admin(admin_user_id,created_at);
SET @audit_fk := (SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='audit_logs' AND COLUMN_NAME='admin_user_id' AND REFERENCED_TABLE_NAME='admin_users' LIMIT 1);
SET @audit_fk_sql := IF(@audit_fk IS NULL, 'ALTER TABLE audit_logs ADD CONSTRAINT fk_audit_admin FOREIGN KEY(admin_user_id) REFERENCES admin_users(id)', 'SELECT 1');
PREPARE audit_fk_stmt FROM @audit_fk_sql; EXECUTE audit_fk_stmt; DEALLOCATE PREPARE audit_fk_stmt;
UPDATE audit_logs a JOIN admin_users au ON au.legacy_user_id=a.user_id
SET a.admin_user_id=au.id, a.user_id=NULL
WHERE a.admin_user_id IS NULL;

ALTER TABLE payout_requests ADD COLUMN IF NOT EXISTS reviewed_by_admin_id INT UNSIGNED NULL AFTER reviewed_by;
ALTER TABLE payout_requests ADD INDEX IF NOT EXISTS idx_payout_reviewer_admin(reviewed_by_admin_id);
SET @payout_has_legacy_reviewer := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payout_requests' AND COLUMN_NAME='reviewed_by');
SET @payout_copy_reviewer_sql := IF(@payout_has_legacy_reviewer=1,
  'UPDATE payout_requests p JOIN admin_users au ON au.legacy_user_id=p.reviewed_by SET p.reviewed_by_admin_id=au.id WHERE p.reviewed_by_admin_id IS NULL',
  'SELECT 1');
PREPARE payout_copy_reviewer_stmt FROM @payout_copy_reviewer_sql; EXECUTE payout_copy_reviewer_stmt; DEALLOCATE PREPARE payout_copy_reviewer_stmt;
ALTER TABLE business_documents ADD COLUMN IF NOT EXISTS reviewed_by_admin_id INT UNSIGNED NULL AFTER reviewed_by;
SET @document_has_legacy_reviewer := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='business_documents' AND COLUMN_NAME='reviewed_by');
SET @document_copy_reviewer_sql := IF(@document_has_legacy_reviewer=1,
  'UPDATE business_documents d JOIN admin_users au ON au.legacy_user_id=d.reviewed_by SET d.reviewed_by_admin_id=au.id WHERE d.reviewed_by_admin_id IS NULL',
  'SELECT 1');
PREPARE document_copy_reviewer_stmt FROM @document_copy_reviewer_sql; EXECUTE document_copy_reviewer_stmt; DEALLOCATE PREPARE document_copy_reviewer_stmt;

-- Old admin web sessions cannot authenticate after this migration.
DELETE s FROM user_sessions s INNER JOIN admin_users au ON au.legacy_user_id=s.user_id;

-- Remove only legacy bootstrap accounts that were never used as customer accounts.
DELETE u FROM users u INNER JOIN admin_users au ON au.legacy_user_id=u.id
WHERE NOT EXISTS (SELECT 1 FROM businesses b WHERE b.owner_user_id=u.id)
  AND NOT EXISTS (SELECT 1 FROM wallets w WHERE w.user_id=u.id)
  AND NOT EXISTS (SELECT 1 FROM transactions t WHERE t.user_id=u.id)
  AND NOT EXISTS (SELECT 1 FROM payment_links p WHERE p.user_id=u.id)
  AND NOT EXISTS (SELECT 1 FROM onboarding_submissions os WHERE os.user_id=u.id)
  AND NOT EXISTS (SELECT 1 FROM notifications n WHERE n.user_id=u.id);

ALTER TABLE admin_users DROP COLUMN IF EXISTS legacy_user_id;

INSERT IGNORE INTO schema_migrations(version) VALUES ('012_separate_admin_identity');

