USE flowwithlit_db;

CREATE TABLE IF NOT EXISTS admin_permission_overrides (
 role VARCHAR(32) NOT NULL,
 permission_key VARCHAR(80) NOT NULL,
 level ENUM('none','view','manage') NOT NULL DEFAULT 'none',
 updated_by INT UNSIGNED NULL,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (role, permission_key),
 FOREIGN KEY (updated_by) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS admin_approval_rules (
 rule_key VARCHAR(80) PRIMARY KEY,
 enabled TINYINT(1) NOT NULL DEFAULT 0,
 threshold DECIMAL(36,18) NULL,
 updated_by INT UNSIGNED NULL,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 FOREIGN KEY (updated_by) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT IGNORE INTO schema_migrations(version) VALUES ('017_admin_authorization');
