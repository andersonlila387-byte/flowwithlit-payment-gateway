USE flowwithlit_db;
ALTER TABLE users ADD COLUMN IF NOT EXISTS onboarding_completed_at DATETIME NULL AFTER last_login_at;
INSERT IGNORE INTO schema_migrations(version) VALUES ('003_onboarding_state');
