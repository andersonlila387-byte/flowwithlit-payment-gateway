-- Reviewer decisions must retain the reason supplied to the business.
ALTER TABLE onboarding_submissions
  ADD COLUMN IF NOT EXISTS review_note VARCHAR(500) NULL AFTER status;

INSERT IGNORE INTO schema_migrations(version) VALUES ('011_onboarding_review_notes');
