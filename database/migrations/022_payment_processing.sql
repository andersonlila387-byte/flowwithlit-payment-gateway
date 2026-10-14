-- Payment processing state, event inbox, and an outbox for retries/notifications.
ALTER TABLE payments
  ADD COLUMN IF NOT EXISTS checkout_session_id BIGINT UNSIGNED NULL AFTER payment_link_id,
  ADD COLUMN IF NOT EXISTS payment_method VARCHAR(40) NULL AFTER customer_name,
  ADD COLUMN IF NOT EXISTS provider VARCHAR(50) NULL AFTER payment_method,
  ADD COLUMN IF NOT EXISTS provider_reference VARCHAR(191) NULL AFTER provider,
  ADD COLUMN IF NOT EXISTS last_event_at DATETIME(6) NULL AFTER provider_reference,
  ADD COLUMN IF NOT EXISTS updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

ALTER TABLE payments MODIFY status ENUM('pending','processing','succeeded','failed','refunded','expired') NOT NULL DEFAULT 'pending';
ALTER TABLE payments ADD UNIQUE KEY IF NOT EXISTS uq_payment_checkout_session(checkout_session_id);
ALTER TABLE payments ADD INDEX IF NOT EXISTS idx_payment_provider_reference(provider,provider_reference);

CREATE TABLE IF NOT EXISTS payment_provider_events (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 provider VARCHAR(50) NOT NULL,
 event_id VARCHAR(191) NOT NULL,
 event_type VARCHAR(100) NULL,
 subject_reference VARCHAR(191) NULL,
 event_time DATETIME(6) NULL,
 payload JSON NOT NULL,
 processed_at DATETIME(6) NULL,
 created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
 UNIQUE KEY uq_payment_provider_event(provider,event_id),
 INDEX idx_payment_subject(provider,subject_reference,event_time)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payment_status_history (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 payment_id INT UNSIGNED NOT NULL,
 status VARCHAR(32) NOT NULL,
 source VARCHAR(50) NOT NULL,
 provider_event_id VARCHAR(191) NULL,
 details JSON NULL,
 created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
 INDEX idx_payment_history_payment(payment_id,created_at),
 FOREIGN KEY(payment_id) REFERENCES payments(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payment_outbox (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 topic VARCHAR(100) NOT NULL,
 aggregate_type VARCHAR(50) NOT NULL,
 aggregate_id BIGINT UNSIGNED NOT NULL,
 payload JSON NOT NULL,
 attempts INT UNSIGNED NOT NULL DEFAULT 0,
 available_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
 processed_at DATETIME(6) NULL,
 last_error VARCHAR(500) NULL,
 created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
 INDEX idx_payment_outbox_ready(processed_at,available_at)
) ENGINE=InnoDB;

INSERT IGNORE INTO schema_migrations(version) VALUES ('022_payment_processing');
