-- Legacy authentication-only bootstrap.
-- Do not use this file to reset a live database. Apply database/migrations/001_core_platform.sql instead.

CREATE DATABASE IF NOT EXISTS `flowwithlit_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `flowwithlit_db`;

-- No DROP statements here: migrations must preserve existing accounts and financial records.

-- 1. Users Table (Individual Users & Registered Merchants)
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `uuid` CHAR(36) NOT NULL UNIQUE,
  `account_type` ENUM('individual', 'business') NOT NULL DEFAULT 'individual',
  `full_name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(191) NOT NULL UNIQUE,
  `phone` VARCHAR(30) NULL UNIQUE,
  `password_hash` VARCHAR(255) NULL, -- Can be NULL for Google Auth users
  `flow_tag` VARCHAR(50) NOT NULL UNIQUE,
  `auth_provider` ENUM('email', 'google') NOT NULL DEFAULT 'email',
  `google_id` VARCHAR(255) NULL UNIQUE,
  `avatar_url` VARCHAR(500) NULL,
  `email_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `two_factor_enabled` TINYINT(1) NOT NULL DEFAULT 0,
  `two_factor_secret` VARCHAR(255) NULL,
  `kyc_tier` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `status` ENUM('active', 'pending_verification', 'suspended') NOT NULL DEFAULT 'pending_verification',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_phone` (`phone`),
  INDEX `idx_flow_tag` (`flow_tag`),
  INDEX `idx_google_id` (`google_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Auth Tokens & Session Management (Powers Web Sessions + Mobile App Bearer Tokens)
CREATE TABLE `user_sessions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `token_hash` VARCHAR(64) NOT NULL UNIQUE,
  `device_info` VARCHAR(255) NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `is_2fa_verified` TINYINT(1) NOT NULL DEFAULT 1,
  `expires_at` DATETIME NOT NULL,
  `last_activity` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_token_hash` (`token_hash`),
  INDEX `idx_user_expires` (`user_id`, `expires_at`),
  CONSTRAINT `fk_session_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Verification Codes (Email OTPs, Password Reset & 2FA Codes)
CREATE TABLE `verification_codes` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NULL,
  `email` VARCHAR(191) NOT NULL,
  `code` VARCHAR(10) NOT NULL,
  `type` ENUM('email_verification', 'password_reset', '2fa') NOT NULL,
  `is_used` TINYINT(1) NOT NULL DEFAULT 0,
  `expires_at` DATETIME NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_email_code` (`email`, `code`, `type`),
  INDEX `idx_user_type` (`user_id`, `type`),
  INDEX `idx_expires` (`expires_at`),
  CONSTRAINT `fk_verification_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Rate Limiting & Brute-Force Prevention Table
CREATE TABLE `login_attempts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `ip_address` VARCHAR(45) NOT NULL,
  `email` VARCHAR(191) NOT NULL,
  `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `last_attempt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_ip_email` (`ip_address`, `email`),
  INDEX `idx_last_attempt` (`last_attempt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
