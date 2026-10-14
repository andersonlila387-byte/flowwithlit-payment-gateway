USE flowwithlit_db;

CREATE TABLE IF NOT EXISTS admin_charge_rules (
 uuid CHAR(36) PRIMARY KEY,
 operation VARCHAR(50) NOT NULL UNIQUE,
 fee_type ENUM('flat','percent','percent_plus_flat','tiered') NOT NULL DEFAULT 'flat',
 flat_amount DECIMAL(36,18) NULL,
 percent DECIMAL(8,4) NULL,
 min_fee DECIMAL(36,18) NULL,
 max_fee DECIMAL(36,18) NULL,
 bearer ENUM('on_top','deducted') NOT NULL DEFAULT 'on_top',
 bands JSON NOT NULL,
 enabled TINYINT(1) NOT NULL DEFAULT 1,
 updated_by INT UNSIGNED NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(updated_by) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS admin_charge_changes (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 rule_uuid CHAR(36) NOT NULL,
 admin_user_id INT UNSIGNED NOT NULL,
 before_config JSON NOT NULL,
 after_config JSON NOT NULL,
 summary VARCHAR(500) NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(rule_uuid) REFERENCES admin_charge_rules(uuid) ON DELETE CASCADE,
 FOREIGN KEY(admin_user_id) REFERENCES admin_users(id)
) ENGINE=InnoDB;

INSERT IGNORE INTO admin_charge_rules(uuid,operation,fee_type,flat_amount,percent,min_fee,max_fee,bearer,bands,enabled) VALUES
 ('00000000-0000-4000-8000-000000000001','merchant_payment','percent_plus_flat',0,1.5,0,NULL,'on_top','[]',1),
 ('00000000-0000-4000-8000-000000000002','payment_link','percent_plus_flat',0,1.5,0,NULL,'on_top','[]',1),
 ('00000000-0000-4000-8000-000000000003','bank_transfer','flat',50,0,0,NULL,'deducted','[]',1),
 ('00000000-0000-4000-8000-000000000004','crypto_swap','percent',0,0.75,0,NULL,'deducted','[]',1);

INSERT IGNORE INTO schema_migrations(version) VALUES ('018_admin_charges');
