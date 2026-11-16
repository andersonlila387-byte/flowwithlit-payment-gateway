-- Countries Flowwithlit can serve. `is_enabled` = approved for signup (shown in the
-- signup country list). Unapproved rows stay as a ready catalogue so an admin can
-- switch a country on once a bank partner covers it. dial_code feeds the phone picker.
CREATE TABLE IF NOT EXISTS countries (
  code CHAR(2) NOT NULL PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  dial_code VARCHAR(6) NOT NULL,
  is_enabled TINYINT(1) NOT NULL DEFAULT 0,
  sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 100,
  updated_by INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_countries_enabled (is_enabled, sort_order, name),
  FOREIGN KEY (updated_by) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- INSERT IGNORE: re-running never overwrites an admin's approvals.
INSERT IGNORE INTO countries (code, name, dial_code, is_enabled, sort_order) VALUES
  ('NG', 'Nigeria', '+234', 1, 1),
  ('GH', 'Ghana', '+233', 0, 100),
  ('KE', 'Kenya', '+254', 0, 100),
  ('ZA', 'South Africa', '+27', 0, 100),
  ('CI', 'Côte d''Ivoire', '+225', 0, 100),
  ('SN', 'Senegal', '+221', 0, 100),
  ('BJ', 'Benin', '+229', 0, 100),
  ('TG', 'Togo', '+228', 0, 100),
  ('CM', 'Cameroon', '+237', 0, 100),
  ('RW', 'Rwanda', '+250', 0, 100),
  ('UG', 'Uganda', '+256', 0, 100),
  ('TZ', 'Tanzania', '+255', 0, 100),
  ('EG', 'Egypt', '+20', 0, 100),
  ('GB', 'United Kingdom', '+44', 0, 100),
  ('US', 'United States', '+1', 0, 100),
  ('CA', 'Canada', '+1', 0, 100);

INSERT IGNORE INTO schema_migrations(version) VALUES ('029_countries');
