-- Launch scope: NGN plus crypto. Additional fiat rails can be enabled by an admin later.
UPDATE currencies SET is_enabled = CASE WHEN code = 'NGN' OR kind = 'crypto' THEN 1 ELSE 0 END;
INSERT IGNORE INTO schema_migrations(version) VALUES ('005_launch_currencies');
