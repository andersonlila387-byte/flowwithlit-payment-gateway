USE flowwithlit_db;

-- Remove customer-notification residue created by the former customer-backed admin login.
DELETE n FROM notifications n
INNER JOIN users u ON u.id=n.user_id
INNER JOIN admin_users au ON au.email=u.email
WHERE NOT EXISTS (SELECT 1 FROM businesses b WHERE b.owner_user_id=u.id)
  AND NOT EXISTS (SELECT 1 FROM wallets w WHERE w.user_id=u.id)
  AND NOT EXISTS (SELECT 1 FROM transactions t WHERE t.user_id=u.id)
  AND NOT EXISTS (SELECT 1 FROM payment_links p WHERE p.user_id=u.id)
  AND NOT EXISTS (SELECT 1 FROM onboarding_submissions os WHERE os.user_id=u.id);

DELETE u FROM users u
INNER JOIN admin_users au ON au.email=u.email
WHERE NOT EXISTS (SELECT 1 FROM businesses b WHERE b.owner_user_id=u.id)
  AND NOT EXISTS (SELECT 1 FROM wallets w WHERE w.user_id=u.id)
  AND NOT EXISTS (SELECT 1 FROM transactions t WHERE t.user_id=u.id)
  AND NOT EXISTS (SELECT 1 FROM payment_links p WHERE p.user_id=u.id)
  AND NOT EXISTS (SELECT 1 FROM onboarding_submissions os WHERE os.user_id=u.id)
  AND NOT EXISTS (SELECT 1 FROM notifications n WHERE n.user_id=u.id)
  AND NOT EXISTS (SELECT 1 FROM audit_logs a WHERE a.user_id=u.id);

INSERT IGNORE INTO schema_migrations(version) VALUES ('013_cleanup_legacy_admin_users');

