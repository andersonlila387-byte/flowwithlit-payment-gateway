-- Remaining entities already represented in the Flowwithlit dashboard.
USE flowwithlit_db;

CREATE TABLE IF NOT EXISTS business_addresses (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, business_id INT UNSIGNED NOT NULL, address_type ENUM('registered','operating') NOT NULL DEFAULT 'registered', line1 VARCHAR(191) NOT NULL, line2 VARCHAR(191) NULL, city VARCHAR(100) NOT NULL, state_region VARCHAR(100) NULL, postal_code VARCHAR(30) NULL, country_code CHAR(2) NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS customers (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, business_id INT UNSIGNED NOT NULL, uuid CHAR(36) NOT NULL UNIQUE, email VARCHAR(191) NULL, full_name VARCHAR(191) NULL, phone VARCHAR(30) NULL, metadata JSON NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX idx_customer_business(business_id,created_at), FOREIGN KEY(business_id) REFERENCES businesses(id) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS invoices (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, business_id INT UNSIGNED NOT NULL, customer_id INT UNSIGNED NULL, uuid CHAR(36) NOT NULL UNIQUE, invoice_number VARCHAR(80) NOT NULL, currency_code VARCHAR(12) NOT NULL, subtotal DECIMAL(36,18) NOT NULL DEFAULT 0, tax_amount DECIMAL(36,18) NOT NULL DEFAULT 0, total_amount DECIMAL(36,18) NOT NULL DEFAULT 0, amount_paid DECIMAL(36,18) NOT NULL DEFAULT 0, status ENUM('draft','open','paid','void','overdue') NOT NULL DEFAULT 'draft', due_at DATETIME NULL, paid_at DATETIME NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY uq_invoice_number(business_id,invoice_number), FOREIGN KEY(business_id) REFERENCES businesses(id), FOREIGN KEY(customer_id) REFERENCES customers(id), FOREIGN KEY(currency_code) REFERENCES currencies(code)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS invoice_items (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, invoice_id INT UNSIGNED NOT NULL, description VARCHAR(255) NOT NULL, quantity DECIMAL(18,6) NOT NULL DEFAULT 1, unit_amount DECIMAL(36,18) NOT NULL, total_amount DECIMAL(36,18) NOT NULL, FOREIGN KEY(invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS card_transactions (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, card_id INT UNSIGNED NOT NULL, transaction_id INT UNSIGNED NULL UNIQUE, merchant_name VARCHAR(191) NOT NULL, merchant_category VARCHAR(100) NULL, amount DECIMAL(36,18) NOT NULL, currency_code VARCHAR(12) NOT NULL, status ENUM('pending','completed','reversed','declined') NOT NULL DEFAULT 'pending', occurred_at DATETIME NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(card_id) REFERENCES cards(id), FOREIGN KEY(transaction_id) REFERENCES transactions(id), FOREIGN KEY(currency_code) REFERENCES currencies(code)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS crypto_deposit_addresses (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, wallet_id INT UNSIGNED NOT NULL, network VARCHAR(50) NOT NULL, address_encrypted TEXT NOT NULL, address_display VARCHAR(191) NOT NULL, tag_memo VARCHAR(100) NULL, status ENUM('active','retired') NOT NULL DEFAULT 'active', created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY uq_wallet_network(wallet_id,network), FOREIGN KEY(wallet_id) REFERENCES wallets(id) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS transaction_events (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, transaction_id INT UNSIGNED NOT NULL, event_type VARCHAR(100) NOT NULL, payload JSON NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, INDEX idx_event_transaction(transaction_id,created_at), FOREIGN KEY(transaction_id) REFERENCES transactions(id) ON DELETE CASCADE
) ENGINE=InnoDB;
INSERT IGNORE INTO schema_migrations(version) VALUES ('002_commercial_extensions');
