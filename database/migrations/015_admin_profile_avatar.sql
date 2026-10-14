USE flowwithlit_db;

-- A profile image is optional. The interface uses a deterministic initials
-- avatar whenever this field is empty.
ALTER TABLE admin_users
  ADD COLUMN IF NOT EXISTS avatar_url VARCHAR(500) NULL AFTER email;

INSERT IGNORE INTO schema_migrations(version) VALUES ('015_admin_profile_avatar');
