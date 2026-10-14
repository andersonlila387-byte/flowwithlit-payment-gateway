USE flowwithlit_db;

ALTER TABLE payout_requests ADD COLUMN IF NOT EXISTS reviewed_by_admin_id INT UNSIGNED NULL;
ALTER TABLE payout_requests ADD INDEX IF NOT EXISTS idx_payout_reviewer_admin(reviewed_by_admin_id);
SET @payout_admin_fk := (SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payout_requests' AND COLUMN_NAME='reviewed_by_admin_id' AND REFERENCED_TABLE_NAME='admin_users' LIMIT 1);
SET @payout_admin_fk_sql := IF(@payout_admin_fk IS NULL, 'ALTER TABLE payout_requests ADD CONSTRAINT fk_payout_reviewer_admin FOREIGN KEY(reviewed_by_admin_id) REFERENCES admin_users(id)', 'SELECT 1');
PREPARE payout_admin_fk_stmt FROM @payout_admin_fk_sql; EXECUTE payout_admin_fk_stmt; DEALLOCATE PREPARE payout_admin_fk_stmt;

ALTER TABLE business_documents ADD COLUMN IF NOT EXISTS reviewed_by_admin_id INT UNSIGNED NULL;
ALTER TABLE business_documents ADD INDEX IF NOT EXISTS idx_document_reviewer_admin(reviewed_by_admin_id);
SET @document_admin_fk := (SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='business_documents' AND COLUMN_NAME='reviewed_by_admin_id' AND REFERENCED_TABLE_NAME='admin_users' LIMIT 1);
SET @document_admin_fk_sql := IF(@document_admin_fk IS NULL, 'ALTER TABLE business_documents ADD CONSTRAINT fk_document_reviewer_admin FOREIGN KEY(reviewed_by_admin_id) REFERENCES admin_users(id)', 'SELECT 1');
PREPARE document_admin_fk_stmt FROM @document_admin_fk_sql; EXECUTE document_admin_fk_stmt; DEALLOCATE PREPARE document_admin_fk_stmt;

SET @payout_legacy_fk := (SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payout_requests' AND COLUMN_NAME='reviewed_by' AND REFERENCED_TABLE_NAME='users' LIMIT 1);
SET @payout_legacy_fk_sql := IF(@payout_legacy_fk IS NULL, 'SELECT 1', CONCAT('ALTER TABLE payout_requests DROP FOREIGN KEY `', @payout_legacy_fk, '`'));
PREPARE payout_legacy_fk_stmt FROM @payout_legacy_fk_sql; EXECUTE payout_legacy_fk_stmt; DEALLOCATE PREPARE payout_legacy_fk_stmt;
ALTER TABLE payout_requests DROP COLUMN IF EXISTS reviewed_by;

SET @document_legacy_fk := (SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='business_documents' AND COLUMN_NAME='reviewed_by' AND REFERENCED_TABLE_NAME='users' LIMIT 1);
SET @document_legacy_fk_sql := IF(@document_legacy_fk IS NULL, 'SELECT 1', CONCAT('ALTER TABLE business_documents DROP FOREIGN KEY `', @document_legacy_fk, '`'));
PREPARE document_legacy_fk_stmt FROM @document_legacy_fk_sql; EXECUTE document_legacy_fk_stmt; DEALLOCATE PREPARE document_legacy_fk_stmt;
ALTER TABLE business_documents DROP COLUMN IF EXISTS reviewed_by;

INSERT IGNORE INTO schema_migrations(version) VALUES ('014_admin_reviewer_references');

