CREATE DATABASE IF NOT EXISTS moizpayment CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE moizpayment;

CREATE TABLE IF NOT EXISTS mp_roles (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS mp_users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role_id INT UNSIGNED NOT NULL,
  full_name VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(30) NULL,
  photo VARCHAR(255) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES mp_roles(id)
);

CREATE TABLE IF NOT EXISTS mp_settings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  company_name VARCHAR(100) NOT NULL,
  company_tagline VARCHAR(200) NULL,
  company_address TEXT NULL,
  company_city VARCHAR(100) NULL,
  company_province VARCHAR(100) NULL,
  company_postal_code VARCHAR(10) NULL,
  company_phone VARCHAR(30) NULL,
  company_email VARCHAR(100) NULL,
  company_website VARCHAR(100) NULL,
  company_npwp VARCHAR(30) NULL,
  company_logo VARCHAR(255) NULL,
  invoice_prefix VARCHAR(20) NOT NULL DEFAULT 'INV/',
  quotation_prefix VARCHAR(20) NOT NULL DEFAULT 'QUO/',
  default_payment_terms INT NOT NULL DEFAULT 14,
  default_tax_id INT NULL,
  bank_name VARCHAR(100) NULL,
  bank_account_number VARCHAR(50) NULL,
  bank_account_name VARCHAR(100) NULL,
  collector_name VARCHAR(100) NULL,
  collector_title VARCHAR(100) NULL,
  smtp_host VARCHAR(100) NULL,
  smtp_port INT NULL,
  smtp_username VARCHAR(100) NULL,
  smtp_password VARCHAR(255) NULL,
  smtp_encryption VARCHAR(10) NULL,
  wa_api_url VARCHAR(255) NULL,
  wa_api_key VARCHAR(255) NULL,
  wa_sender_number VARCHAR(30) NULL,
  currency_symbol VARCHAR(10) NOT NULL DEFAULT 'Rp',
  currency_code VARCHAR(10) NOT NULL DEFAULT 'IDR',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS mp_clients (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  company_name VARCHAR(100) NULL,
  email VARCHAR(100) NULL,
  phone VARCHAR(30) NULL,
  address TEXT NULL,
  city VARCHAR(100) NULL,
  notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS mp_quotations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  quotation_number VARCHAR(50) NOT NULL UNIQUE,
  client_id INT UNSIGNED NOT NULL,
  quotation_date DATE NOT NULL,
  valid_until DATE NOT NULL,
  subtotal DECIMAL(15,2) NOT NULL DEFAULT 0,
  discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
  discount_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
  tax_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
  tax_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
  total DECIMAL(15,2) NOT NULL DEFAULT 0,
  status ENUM('draft','sent','approved','rejected','expired') NOT NULL DEFAULT 'draft',
  notes TEXT NULL,
  terms TEXT NULL,
  created_by INT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_quotations_client FOREIGN KEY (client_id) REFERENCES mp_clients(id),
  CONSTRAINT fk_quotations_user FOREIGN KEY (created_by) REFERENCES mp_users(id)
);

CREATE TABLE IF NOT EXISTS mp_quotation_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  quotation_id INT UNSIGNED NOT NULL,
  description VARCHAR(255) NOT NULL,
  qty DECIMAL(12,2) NOT NULL DEFAULT 0,
  unit VARCHAR(30) NULL,
  price DECIMAL(15,2) NOT NULL DEFAULT 0,
  discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
  total DECIMAL(15,2) NOT NULL DEFAULT 0,
  CONSTRAINT fk_quotation_items_header FOREIGN KEY (quotation_id) REFERENCES mp_quotations(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS mp_invoices (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  invoice_number VARCHAR(50) NOT NULL UNIQUE,
  client_id INT UNSIGNED NOT NULL,
  quotation_id INT UNSIGNED NULL,
  invoice_date DATE NOT NULL,
  due_date DATE NOT NULL,
  subtotal DECIMAL(15,2) NOT NULL DEFAULT 0,
  discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
  discount_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
  tax_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
  tax_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
  total DECIMAL(15,2) NOT NULL DEFAULT 0,
  paid_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
  status ENUM('draft','sent','partial','paid','overdue','cancelled') NOT NULL DEFAULT 'draft',
  notes TEXT NULL,
  terms TEXT NULL,
  created_by INT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_invoices_client FOREIGN KEY (client_id) REFERENCES mp_clients(id),
  CONSTRAINT fk_invoices_quotation FOREIGN KEY (quotation_id) REFERENCES mp_quotations(id) ON DELETE SET NULL,
  CONSTRAINT fk_invoices_user FOREIGN KEY (created_by) REFERENCES mp_users(id)
);

CREATE TABLE IF NOT EXISTS mp_invoice_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  invoice_id INT UNSIGNED NOT NULL,
  description VARCHAR(255) NOT NULL,
  qty DECIMAL(12,2) NOT NULL DEFAULT 0,
  unit VARCHAR(30) NULL,
  price DECIMAL(15,2) NOT NULL DEFAULT 0,
  discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
  total DECIMAL(15,2) NOT NULL DEFAULT 0,
  CONSTRAINT fk_invoice_items_header FOREIGN KEY (invoice_id) REFERENCES mp_invoices(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS mp_payments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  payment_code VARCHAR(50) NOT NULL,
  invoice_id INT UNSIGNED NULL,
  client_id INT UNSIGNED NULL,
  category_id INT UNSIGNED NULL,
  amount DECIMAL(15,2) NOT NULL DEFAULT 0,
  payment_date DATE NOT NULL,
  payment_method VARCHAR(50) NULL,
  reference_number VARCHAR(100) NULL,
  description TEXT NULL,
  attachment VARCHAR(255) NULL,
  created_by INT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_payments_invoice FOREIGN KEY (invoice_id) REFERENCES mp_invoices(id) ON DELETE SET NULL,
  CONSTRAINT fk_payments_client FOREIGN KEY (client_id) REFERENCES mp_clients(id) ON DELETE SET NULL,
  CONSTRAINT fk_payments_user FOREIGN KEY (created_by) REFERENCES mp_users(id)
);

CREATE TABLE IF NOT EXISTS mp_expenses (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  expense_code VARCHAR(50) NOT NULL,
  category_id INT UNSIGNED NULL,
  vendor_name VARCHAR(100) NOT NULL,
  amount DECIMAL(15,2) NOT NULL DEFAULT 0,
  expense_date DATE NOT NULL,
  payment_method VARCHAR(50) NULL,
  reference_number VARCHAR(100) NULL,
  description TEXT NULL,
  attachment VARCHAR(255) NULL,
  created_by INT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_expenses_user FOREIGN KEY (created_by) REFERENCES mp_users(id)
);

CREATE TABLE IF NOT EXISTS mp_email_logs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  module VARCHAR(30) NOT NULL,
  ref_id INT UNSIGNED NOT NULL,
  recipient VARCHAR(100) NOT NULL,
  subject VARCHAR(200) NOT NULL,
  status VARCHAR(20) NOT NULL,
  response TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS mp_wa_logs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  module VARCHAR(30) NOT NULL,
  ref_id INT UNSIGNED NOT NULL,
  target_number VARCHAR(30) NOT NULL,
  message TEXT NOT NULL,
  status VARCHAR(20) NOT NULL,
  response TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO mp_roles (id, name) VALUES
  (1, 'Super Admin'),
  (2, 'Admin'),
  (3, 'Finance Staff'),
  (4, 'Viewer')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO mp_users (id, role_id, full_name, username, email, password, phone, is_active) VALUES
  (1, 1, 'Super Admin', 'admin', 'admin@moizpayment.test', '$2y$10$PIE6kRqUgYd49F1CXv/DguPXeLQ9RHCc263aK5PHJgzUO2AIS7YiO', '081234567890', 1)
ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), password = VALUES(password), is_active = VALUES(is_active);

INSERT INTO mp_settings
  (id, company_name, company_tagline, company_address, company_city, company_phone, company_email, invoice_prefix, quotation_prefix, default_payment_terms, bank_name, bank_account_number, bank_account_name, collector_name, collector_title, currency_symbol, currency_code)
VALUES
  (1, 'MoizPayment Demo', 'Invoice & Finance Management', 'Jl. Contoh No. 1', 'Jakarta', '021-555000', 'billing@moizpayment.test', 'INV/', 'QUO/', 14, 'Bank BCA', '1234567890', 'MoizPayment Demo', 'Finance Team', 'Finance & Billing Officer', 'Rp', 'IDR')
ON DUPLICATE KEY UPDATE company_name = VALUES(company_name), company_email = VALUES(company_email);
