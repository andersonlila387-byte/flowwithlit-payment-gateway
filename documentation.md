# Flowwithlit Architecture & System Specification

> **Tagline:** Banking. Payments. Crypto. One Platform.  
> **Mission:** A unified multi-rail payment gateway and financial ecosystem empowering merchants and consumers with seamless Fiat (NGN) banking, direct payment integrations, non-custodial zero-fee-leakage Crypto infrastructure, Flow Tag payments, automated settlement, and AI-powered support.

---

## 1. Executive Summary & Core Objectives

Flowwithlit addresses the fragmentation between traditional banking, digital merchant payments, and cryptocurrency settlements. The platform operates across two strategic phases:
1. **Fiat Banking & Virtual Accounts:** 
   * **Primary/Launch Partner:** **OnePipe** (Virtual account issuance, NGN collection, dynamic checkout accounts, bank verification, transfers).
   * **Long-Term Scaling Partner:** **PalmPay** (Deep payment infrastructure, low-cost switching, high-availability agency/merchant banking network).
2. **Crypto Gateway (Zero Third-Party Fee Leakage):**
   * Built on a **Self-Hosted Non-Custodial HD Wallet (BIP-44/BIP-32)** infrastructure managed by high-speed Go daemons.
   * Eliminates intermediary aggregator fees (e.g., NOWPayments, BitPay percentages) by listening directly to blockchain RPCs/Mempools, generating dynamic derivation addresses per invoice, and sweeping securely with configurable gas thresholds.

---

## 2. Technology Stack & Concurrency Architecture

Flowwithlit uses a modular, decoupled architecture where **Pure/Modern Vanilla PHP (OOP MVC without Laravel)** handles 100% of web delivery, unified auth, mobile REST APIs, and merchant operations, while **Go (Golang)** runs autonomously as a background companion daemon, and **MySQL 8.0** provides ACID-compliant ledger storage with pessimistic row-locking.

### 2.1 Backend Concurrency & Unified API Gateway

```
[ Web Browser ]             [ Mobile App (iOS / Android) ]       [ External Merchant / Plugin ]
       │                                   │                                    │
       │ (Session / Web MVC)               │ (Bearer JWT Token)                 │ (API Secret Key)
       ▼                                   ▼                                    ▼
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                    FLOWWITHLIT VANILLA PHP CORE GATEWAY (PHP-FPM)                       │
│  - Unified Authentication Engine (Single Account for Web, Mobile App & Dashboard)       │
│  - RESTful Mobile App Endpoints (`/api/v1/...`)                                         │
│  - Zero Framework Overhead (Sub-20ms ultra-fast execution time)                        │
│  - High-Concurrency Safety: Pessimistic Row Locking (`SELECT ... FOR UPDATE`)          │
│  - Anti-Race Condition & Double-Spend Prevention inside ACID Transactions               │
└────────────────────────────────────────────┬────────────────────────────────────────────┘
                                             │
                       ┌─────────────────────┴─────────────────────┐
                       ▼                                           ▼
            ┌─────────────────────┐                     ┌─────────────────────┐
            │ MySQL 8.0 Database  │                     │  Go (Golang) Engine │
            │ (ACID Ledger Core)  │                     │ (Background Daemon) │
            └─────────────────────┘                     └──────────┬──────────┘
                                                                   │
                                                                   ▼
                                                       - Blockchain RPC Listeners
                                                       - High-Speed Webhook Dispatcher
                                                       - 24-Hour Settlement Auto-Scheduler
```

#### A. PHP Core (Vanilla OOP MVC Structure - Zero Laravel)
* **Architecture:** Clean Object-Oriented PHP 8.2+, standard class autoloading, lightweight Router, PDO abstraction with prepared statements, modular controllers, services, and view templates.
* **Concurrency & Scale Capabilities:**
  * Powered by **PHP-FPM worker pools** capable of handling thousands of concurrent requests across multi-threaded worker pools.
  * **Strict Financial Locking:** All wallet mutations execute within database transactions utilizing `SELECT ... FOR UPDATE` row locks, preventing race conditions or double-spending when multiple requests arrive in the same millisecond.
* **Responsibilities:**
  * **Unified Auth & Account Management:** Single identity for Web, Mobile App, and Merchant Dashboard. Password hashing via `PASSWORD_BCRYPT`, 2FA, session & JWT issuance.
  * **Mobile App REST API (`/api/v1/`):** Dedicated JSON endpoints for Mobile App developers (iOS/Android) sharing the exact same database and logic.
  * **Landing Page & Marketing:** Public website, pricing, feature showcases, documentation portal for developers.
  * **Merchant Dashboard:** API keys management (Public/Secret keys, Test/Live modes), webhooks setup, analytics, invoice generator.
  * **Customer Portal:** Virtual account display, transaction history, send/receive funds, Flow Tag management.
  * **Admin Panel:** Platform overview, KYC tier verifications, reserve balances, manual audit overrides, dispute management.
  * **AI Customer Support:** Context-aware AI engine interacting with merchant/customer queries, transaction troubleshooting, and automated FAQ responses.
  * **Partner Integrations:** OnePipe API client, PalmPay integration bridge, bank payout queues.

#### B. Go (Golang) Engine (High-Performance Daemon)
* **Responsibilities:**
  * **Crypto Watcher & Mempool Monitor:** Monitors Bitcoin (BTC), Ethereum/EVM (USDT ERC-20, ETH), TRON (USDT TRC-20), and Binance Smart Chain (USDT BEP-20) via direct RPC nodes / WebSockets.
  * **Address Generator:** Generates derivation addresses on the fly from a master extended public key (xPub) without exposing master private keys online.
  * **Webhook Delivery Worker:** High-throughput retry-safe webhook engine with exponential backoff and cryptographic HMAC SHA-256 signatures.
  * **Settlement Engine:** Automated 24-hour settlement processing, fee computation, and ledger balance reconciliation.

#### C. Database: MySQL 8.0 (ACID Compliant)
* **Engine:** InnoDB with strict foreign keys, indexing on high-cardinality columns (`reference`, `flow_tag`, `status`, `user_id`).
* **Design Philosophy:** Strict double-entry ledger bookkeeping. Every credit must have a balancing debit. Balances are never updated with a blind `UPDATE balance = balance + X`; they are computed and verified against ledger entries.

---

## 3. Financial Infrastructure & Payment Rails

### 3.1 Fiat Rail (Naira - NGN)
* **Launch Partner (OnePipe):**
  * Dynamic virtual bank accounts per transaction or static dedicated virtual accounts per registered customer/merchant (Wema, Providus, Sterling, etc.).
  * Real-time NGN bank transfer processing (Inbound collections).
  * Direct debit / Account linking where supported.
* **Strategic Partner (PalmPay):**
  * Long-term high-speed switching for merchant POS, QR payments, mobile wallet settlements, and lower transaction fees.
* **Settlement & 24-Hour Withdrawal Engine:**
  * Rolling 24-hour withdrawal lock on settled funds to mitigate fraud, chargebacks, and unconfirmed reversals.
  * Automated batch payout triggers after the 24-hour maturity period via NIP (NIBSS Instant Payments) through OnePipe/PalmPay APIs.

### 3.2 Clean Crypto Rail (Zero Intermediary Fee Leakage)
* **The Problem with 3rd-Party Crypto Services (NOWPayments, CoinPayments, etc.):**
  * They charge 0.5% - 1.5% on top of network fees.
  * They hold merchant custody and enforce withdrawal limits/minimums.
* **Flowwithlit Clean Crypto Architecture:**
  * **Master Seed Offline:** Master private key / seed phrase is kept securely in cold storage / Vault HSM.
  * **xPub in Go Daemon:** Go service holds only the `Extended Public Key (xPub)`.
  * **Unique Address per Invoice:** For every crypto transaction, the system derives a child public address (BIP-44: `m/44'/60'/0'/0/i` for EVM, `m/44'/195'/0'/0/i` for TRON).
  * **Direct Blockchain Monitoring:** Go monitors transfers directly to the derived address. Once confirmations reach threshold (e.g., 2 confirmations for BSC, 12 for ETH, 19 for TRON):
    1. Transaction is marked as `PAID`.
    2. Event is published to the MySQL ledger.
    3. Merchant webhook is fired immediately.
  * **Zero Platform Deductions:** 100% of customer funds belong to the merchant/system; only pure network miner/gas fees are incurred when sweeping funds to central cold/hot storage.
  * **Supported Assets:**
    * USDT (TRC-20) – High volume, minimal fees (~$1).
    * USDT (BEP-20) – Ultra-low fees (~$0.15), rapid confirmation.
    * USDT / USDC (ERC-20) – Enterprise and high-ticket settlements.
    * Bitcoin (BTC) & Native ETH / BNB.

---

## 4. Key Platform Features & Transfer Rails

### 4.1 Comprehensive e-Transfer Engine
1. **Flow Tag Transfer (`@tag` to `@tag`)**:
   * Instant, zero-fee peer-to-peer and peer-to-merchant transfers within the Flowwithlit ledger.
   * Eliminates bank network delays.
2. **Outbound Bank e-Transfer (Flowwithlit $\rightarrow$ Any Nigerian Bank / PalmPay)**:
   * Direct transfer to any bank via NIBSS/OnePipe/PalmPay.
   * **Automated Name Inquiry**: Real-time account number verification displaying beneficiary name before transfer confirmation.
3. **Cross-Site / Merchant Checkout e-Transfer ("Pay with Flowwithlit")**:
   * External websites and e-commerce stores can integrate Flowwithlit checkout. Customers can pay using their Flowwithlit balance, virtual account, or crypto.
4. **On-Chain Crypto e-Transfer**:
   * External wallet withdrawal and deposit tracking for USDT and BTC with automated confirmation feeds.

### 4.2 "Flow Tag" Payment Ecosystem (`@flowtag`)
* Every merchant and verified customer receives a unique, human-readable handle (e.g., `@litmerchant`, `@david`).
* **Direct Web & Mobile Checkout:** Customers can pay online merchants simply by entering their Flow Tag and approving the push notification / 2FA in their app.
* **QR Code Support:** Dynamic and static QR codes tied to `@flowtag` for in-person POS retail scanning.

### 4.3 24-Hour Withdrawal & Settlement Cycle
* **Security Buffer:** Funds collected via fiat or crypto undergo automated anti-fraud and risk assessment.
* **Maturity Timer:** Visible countdown on the merchant dashboard (`Settling in 23h 45m`).
* **Auto-Disbursement or Manual Trigger:** Merchants can toggle "Auto-Withdraw after 24h" to their linked corporate bank account or request instant manual withdrawal upon maturity.

### 4.4 Unified Mobile Application API (`/api/v1/`)
* **Single Account Credential:** The exact same email, phone, password, and KYC tier are used across Web, Mobile App (iOS/Android), and Merchant Dashboard.
* **Token Authentication:** Secure JWT / Bearer tokens issued upon login, enabling mobile developers to interact smoothly with all wallet, transfer, and transaction resources.

### 4.5 AI Customer Support Agent
* **Context-Aware Engine:** Connected to platform read-only data (transaction status lookup, FAQ knowledge base, API error codes).
* **Multi-Channel Availability:** Embedded in Merchant Dashboard, Customer Portal, and Public Landing page.
* **Automated Triage:** Resolves 80% of common inquiries. Escalates disputes/edge cases to human super-admins with full ticket history.

### 4.6 Admin Control & Risk Management Center
* **Super-Admin Analytics:** Real-time platform volume (Fiat vs. Crypto), liquidity ratios, partner API health.
* **Merchant KYC Tiers:**
  * *Tier 1 (Starter):* Phone/Email verification, limited daily volume (e.g., ₦50,000 / $100).
  * *Tier 2 (Verified Individual):* BVN/NIN verification, proof of address.
  * *Tier 3 (Registered Business):* CAC registration, Tax ID (TIN), corporate bank verification, unlimited throughput.
* **Dispute & Freeze Controls:** Ability to lock suspicious Flow Tags, hold suspicious payouts, and inspect audit trails.

### 4.7 Developer Platform, SDKs & Plugins
* **RESTful Developer API:** Idempotent endpoints with test/live secret keys (`sec_test_...`, `sec_live_...`).
* **Webhook System:** Cryptographic verification (`X-Flowwithlit-Signature: sha256(...)`), event triggers (`charge.success`, `transfer.completed`, `crypto.confirmed`).
* **E-Commerce Plugins:**
  * WooCommerce (WordPress) PHP Plugin.
  * WHMCS Gateway Module.
  * Embeddable Drop-in Checkout JS (`Flowwithlit.openCheckout({ ... })`).

---

## 5. Website & Application Sitemap

### 5.1 Public Website (PHP Views)
* `/` – High-converting Homepage / Landing page.
* `/features` – Breakdown of Fiat, Crypto, and Flow Tag capabilities.
* `/pricing` – Transparent fee breakdown (Zero-leakage crypto, competitive fiat MDR).
* `/developers` – Interactive API documentation, quick-start guides, SDK downloads.
* `/support` – Help Center with AI chat assistant.
* `/login` & `/register` – Multi-role onboarding (Merchant vs Individual).

### 5.2 Merchant / Customer Portal (PHP Dashboard)
* `/dashboard` – Real-time revenue charts, balance overview (NGN + Crypto).
* `/transfers` – Comprehensive e-Transfer hub (Flow Tag, Bank NIP with name inquiry, Crypto send).
* `/transactions` – Searchable, filterable ledger history with exportable CSV/PDF receipts.
* `/wallets` – Virtual account details (OnePipe/PalmPay), crypto deposit addresses, balance breakdown.
* `/flowtag` – Custom tag claim, QR code generator, quick P2P transfer interface.
* `/withdrawals` – 24-hour settlement schedule, matured funds, bank account linkage.
* `/developer/api-keys` – Live & Sandbox keys, webhook URL configuration, webhook test log simulator.
* `/settings/kyc` – Tier progression, BVN/NIN validation, business documentation upload.

### 5.3 Mobile App API Spec (`/api/v1/`)
* `POST /api/v1/auth/login` – Returns Bearer JWT + User Profile
* `POST /api/v1/auth/register` – Single account creation
* `GET  /api/v1/wallets` – Returns balances (NGN, USDT, BTC)
* `POST /api/v1/transfer/flowtag` – Instant internal P2P
* `POST /api/v1/transfer/bank/inquiry` – Account name lookup
* `POST /api/v1/transfer/bank/send` – Outbound bank transfer
* `GET  /api/v1/crypto/address` – Dynamic deposit QR & address

### 5.4 Super Admin Portal (PHP Restricted)
* `/admin/overview` – Platform-wide liquidity, daily turnover, active accounts.
* `/admin/merchants` – Account moderation, KYC review, tier upgrades/rejections.
* `/admin/settlements` – 24-hour payout queue monitor, batch approval / rejection.
* `/admin/blockchain` – Hot/Cold wallet balances, gas reserve monitoring, address derivation tree.
* `/admin/support` – AI chat logs, open escalation tickets, dispute arbitration.

---

## 6. Database Schema Overview (MySQL 8.0 InnoDB)

Key core tables designed for ACID reliability & row-locking:

1. `users` – Core user credentials, role (`merchant`, `individual`, `admin`), status (`active`, `suspended`, `pending_kyc`).
2. `merchant_profiles` – Business name, CAC number, website, tier level, settlement preferences.
3. `api_keys` – Client public/secret keys, environment (`sandbox`, `live`), IP whitelist.
4. `wallets` – Currency code (`NGN`, `USDT_TRC20`, `USDT_BEP20`, `BTC`), user association. Locked via `FOR UPDATE` on transfers.
5. `ledger_entries` – Double-entry records: `id`, `wallet_id`, `type` (`credit`, `debit`), `amount`, `balance_after`, `reference`, `created_at`.
6. `virtual_accounts` – Account number, bank name, provider (`onepipe`, `palmpay`), account reference.
7. `invoices` – Merchant invoices, amount, currency, status (`pending`, `paid`, `expired`, `refunded`), metadata.
8. `transfers` – Comprehensive e-transfer tracking (`flowtag`, `bank_nip`, `crypto_withdrawal`), beneficiary details, status, timestamps.
9. `crypto_deposits` – Derived public address, chain, derivation_index, tx_hash, confirmations, amount_received.
10. `settlements` – Matured balance payouts, status (`holding`, `matured`, `disbursed`), 24h release timestamp.
11. `flow_tags` – Unique handle string, user_id, active status.
12. `webhooks` – Target URL, events subscribed, secret hash, delivery retry logs.
13. `support_tickets` & `ai_conversations` – Support chat logs, intent classification, resolution status.

---

## 7. Migration & Scalability Roadmap

1. **Phase 1: Foundation & Public Landing Page** (Modern responsive UI, Flowwithlit branding, features & pricing showcase).
2. **Phase 2: Database Schema & Core Architecture** (MySQL tables, PDO wrapper, PSR-4/clean OOP structure, Router, Concurrency Row Locks).
3. **Phase 3: Auth & Unified Account System** (Single account login for Web & Mobile App, Sessions & JWT Bearer tokens).
4. **Phase 4: User/Merchant Dashboard** (Virtual accounts overview, balances, Flow Tag hub, API keys).
5. **Phase 5: Comprehensive e-Transfer Engine** (Flow Tag P2P, Bank NIP with Name Inquiry, Cross-site checkout).
6. **Phase 6: Fiat Banking Rail** (OnePipe virtual account creation & webhook reception).
7. **Phase 7: Clean Crypto Engine** (Go daemon for xPub derivation and multi-chain confirmations).
8. **Phase 8: 24-Hour Settlement Scheduler** (Auto-release pipeline to corporate accounts).
9. **Phase 9: AI Customer Support Agent** (Embedded support widget, contextual inquiry triage).
10. **Phase 10: Super Admin Panel** (Full monitoring, KYC review, manual overrides).
11. **Phase 11: Developer Plugins & Client SDKs** (Checkout Modal, WooCommerce PHP plugin, WHMCS module).
