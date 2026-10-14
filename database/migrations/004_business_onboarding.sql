CREATE TABLE IF NOT EXISTS onboarding_submissions (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id INT UNSIGNED NOT NULL UNIQUE,
 business_id INT UNSIGNED NULL UNIQUE,
 payload_encrypted MEDIUMTEXT NOT NULL,
 status ENUM('draft','submitted','under_review','approved','rejected') NOT NULL DEFAULT 'draft',
 submitted_at DATETIME NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
 FOREIGN KEY (business_id) REFERENCES businesses(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS onboarding_files (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 submission_id INT UNSIGNED NOT NULL,
 field_name VARCHAR(40) NOT NULL,
 storage_key CHAR(64) NOT NULL UNIQUE,
 mime_type VARCHAR(60) NOT NULL,
 byte_size INT UNSIGNED NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_onboarding_file (submission_id,field_name),
 FOREIGN KEY (submission_id) REFERENCES onboarding_submissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT IGNORE INTO schema_migrations(version) VALUES ('004_business_onboarding');
