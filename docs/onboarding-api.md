# Business onboarding

Business onboarding is now intentionally short. It saves business type and name, owner name, email and phone, plus one payout account through the same authenticated API for web and mobile clients. KYC documents, address, business logo and extended business details stay out of the launch flow until higher limits or regulated features require them. A submitted business is awaiting review; submission does not grant live payment access or change the account's KYC tier.

## Setup

Apply the existing core migrations (001–003), then run:

```powershell
C:\xampp\php\php.exe database/setup-onboarding.php
```

The setup script applies migration 004 and provisions encrypted storage. The local default is `C:\xampp\flowwithlit-private`, outside Apache's `htdocs` directory. For other deployments set `FLOW_PRIVATE_STORAGE` to an absolute path outside the public document root and run setup there. PHP must have read/write access to this directory. Back up the database, private uploads, and `encryption.key` together; losing the key makes existing files and onboarding snapshots unreadable. Never publish or commit the key.

PHP requires PDO MySQL, OpenSSL, Fileinfo and mbstring. Allow 10MB per file, at least 64MB per multipart request and five files. Local Apache overrides are provided in `api/onboarding/.htaccess`; configure these limits in PHP-FPM/server settings on other hosts. Runtime encryption fails closed if storage or its key is missing.

## Authentication

Every request requires `Authorization: Bearer <session-token>`. Pending two-factor sessions are rejected. No endpoint accepts a user ID to select someone else's submission. Responses use `Cache-Control: no-store`.

## Endpoints

- `GET /api/onboarding/business.php`: returns `success`, `status`, saved `data`, and `files` keyed by upload field. File metadata contains only an ID, MIME type and byte count, not a storage path.
- `POST /api/onboarding/business.php`: multipart form data with `action=draft` or `action=submit`. Send the complete current field snapshot; omitted text fields become empty. Uploaded fields replace the existing file for that field. Omitted files retain existing saved uploads.
- `GET /api/onboarding/file.php?id=<file-id>`: authenticated owner-only download, served as an attachment with a generated descriptive filename. A different owner receives 404.
- `/api/auth/complete-onboarding.php`: compatibility entry point to the same validated API; a token-only request can no longer mark business onboarding complete.

Text fields: `businessType`, `businessName`, `ownerName`, `ownerEmail`, `phone`, `accountName`, `bankName`, `accountNumber`. Final submission requires `declaration=1`.

`businessType` is `Personal business` or `Registered company`. The Naira payout account number is exactly ten digits. The account holder name, bank name and account number are stored for payout setup; account number encryption remains enabled.

File fields: `businessLogo` (optional PNG/JPEG/WEBP), `identityDocument`, `addressDocument`, `registrationDocument`, `bankStatement` (required PDF/PNG/JPEG/WEBP). Actual MIME and image validity are checked server-side; client filenames and MIME declarations are not trusted. Each file is limited to 10MB. Files use 256-bit random storage names and AES-256-GCM authenticated encryption; original filenames are not persisted. The complete form snapshot and bank account number are also encrypted. Direct public access to storage and server configuration directories is denied.

Drafts and normalized submission records are committed transactionally. Final submission writes business, membership, owner contact details and a pending settlement account. Repeated final submissions return the existing record without creating another business or bank account.

## Web behavior

Next and Save draft persist the current fields and selected files. Reloading restores them from the server without browser-local financial/KYC drafts. Uploaded documents can be downloaded from the review step. Use `app/onboarding.php?mode=business` to open business onboarding on a phone; the default mobile signup flow remains personal setup.

## Verification

The minimal launch flow is validated with PHP syntax checks and the service validation harness. Run `C:\xampp\php\php.exe database/setup-onboarding.php` after migrations, then exercise draft and submit requests against local Apache/MySQL. This implementation stores optional future documents for review; it does not perform automated identity verification or malware scanning.
