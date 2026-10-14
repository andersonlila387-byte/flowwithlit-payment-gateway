USE flowwithlit_db;
CREATE TABLE IF NOT EXISTS admin_transfer_followups (
 transaction_id INT UNSIGNED PRIMARY KEY,
 status ENUM('open','resolved') NOT NULL DEFAULT 'open',
 note VARCHAR(500) NOT NULL,
 handled_by INT UNSIGNED NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 FOREIGN KEY(transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
 FOREIGN KEY(handled_by) REFERENCES admin_users(id)
) ENGINE=InnoDB;
INSERT IGNORE INTO schema_migrations(version) VALUES ('019_admin_transfer_followups');
