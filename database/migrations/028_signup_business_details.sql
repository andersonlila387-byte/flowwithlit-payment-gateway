-- Paystack-style signup: split names and capture the business answers given at signup.
-- full_name stays the canonical display name; first/last are kept for KYC and onboarding prefill.
-- signup_business_name / business_stage are the self-declared signup answers only; the verified
-- legal entity is still created in `businesses` during onboarding.
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS first_name VARCHAR(75) NULL AFTER full_name,
  ADD COLUMN IF NOT EXISTS last_name VARCHAR(75) NULL AFTER first_name,
  ADD COLUMN IF NOT EXISTS signup_business_name VARCHAR(191) NULL AFTER last_name,
  ADD COLUMN IF NOT EXISTS business_stage ENUM('starter','registered') NULL AFTER signup_business_name;

INSERT IGNORE INTO schema_migrations(version) VALUES ('028_signup_business_details');
