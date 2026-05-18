-- ============================================================
-- Online Medicine Shop — Shared Database Schema
-- Group 11 | Task 3 | Student: 23-54596-3
-- ============================================================

CREATE DATABASE IF NOT EXISTS online_medicine_shop
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE online_medicine_shop;

CREATE TABLE IF NOT EXISTS users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(120)  NOT NULL,
    email           VARCHAR(180)  NOT NULL UNIQUE,
    password_hash   VARCHAR(255)  NOT NULL,
    role            ENUM('admin','customer') NOT NULL DEFAULT 'customer',
    profile_picture VARCHAR(255)  DEFAULT NULL,
    address         TEXT          DEFAULT NULL,
    phone           VARCHAR(20)   DEFAULT NULL,
    created_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS categories (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(120)  NOT NULL,
    category_type   ENUM('liquid','solid') NOT NULL,
    created_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS medicines (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(180)  NOT NULL,
    category_id     INT UNSIGNED  NOT NULL,
    vendor_name     VARCHAR(120)  NOT NULL,
    price           DECIMAL(10,2) NOT NULL CHECK (price > 0),
    availability    INT UNSIGNED  NOT NULL DEFAULT 0,
    description     TEXT          DEFAULT NULL,
    image_path      VARCHAR(255)  DEFAULT NULL,
    created_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cart (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NOT NULL,
    medicine_id     INT UNSIGNED NOT NULL,
    quantity        INT UNSIGNED NOT NULL DEFAULT 1,
    added_at        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_cart_item (user_id, medicine_id),
    FOREIGN KEY (user_id)     REFERENCES users(id)     ON DELETE CASCADE,
    FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id          INT UNSIGNED  NOT NULL,
    total_amount     DECIMAL(12,2) NOT NULL,
    shipping_address TEXT          NOT NULL,
    status           ENUM('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
    payment_method   VARCHAR(60)   NOT NULL,
    order_date       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id        INT UNSIGNED  NOT NULL,
    medicine_id     INT UNSIGNED  NOT NULL,
    quantity        INT UNSIGNED  NOT NULL,
    unit_price      DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id)    REFERENCES orders(id)   ON DELETE CASCADE,
    FOREIGN KEY (medicine_id) REFERENCES medicines(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payments (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id        INT UNSIGNED  NOT NULL,
    amount          DECIMAL(12,2) NOT NULL,
    payment_method  VARCHAR(60)   NOT NULL,
    transaction_id  VARCHAR(80)   NOT NULL,
    payment_date    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;


INSERT IGNORE INTO users (name, email, password_hash, role, address, phone) VALUES
('Admin User',  'admin@medishop.com',    '$2y$12$examplehashADMIN',    'admin',    'Admin Office, Dhaka', '01700000000'),
('Test Customer','customer@medishop.com','$2y$12$examplehashCUSTOMER', 'customer', '12 Main Road, Dhaka', '01800000000');

