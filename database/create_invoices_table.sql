-- Migration: create invoices table
CREATE TABLE IF NOT EXISTS `invoices` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `invoice_number` VARCHAR(100) NOT NULL,
    `reservation_id` INT DEFAULT NULL,
    `user_id` INT NOT NULL,
    `payment_intent_id` VARCHAR(255) DEFAULT NULL,
    `amount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `currency` VARCHAR(10) DEFAULT 'usd',
    `status` VARCHAR(50) DEFAULT 'PENDING',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_invoice_number` (`invoice_number`),
    KEY `idx_user_id` (`user_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;