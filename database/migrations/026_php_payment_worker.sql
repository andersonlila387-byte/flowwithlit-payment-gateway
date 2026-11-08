-- PHP-only payment worker (replaces the retired Go service).
-- Retry bookkeeping for provider webhook events lives in its own table so this
-- migration stays additive and safe to run more than once.
CREATE TABLE IF NOT EXISTS payment_provider_event_attempts (
 event_row_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
 attempts INT UNSIGNED NOT NULL DEFAULT 0,
 last_error VARCHAR(255) NULL,
 next_attempt_at DATETIME(6) NULL,
 updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
 CONSTRAINT fk_provider_event_attempts_event FOREIGN KEY (event_row_id)
   REFERENCES payment_provider_events(id) ON DELETE CASCADE
) ENGINE=InnoDB;
