-- Backfill payment links created before they stored their merchant business.
-- Only update owners with exactly one business, so an ambiguous account is never
-- attached to the wrong merchant.
UPDATE payment_links pl
JOIN (
    SELECT owner_user_id, MIN(id) AS business_id
    FROM businesses
    GROUP BY owner_user_id
    HAVING COUNT(*) = 1
) business ON business.owner_user_id = pl.user_id
SET pl.business_id = business.business_id
WHERE pl.business_id IS NULL;

INSERT IGNORE INTO schema_migrations(version) VALUES ('010_link_business_ownership');
