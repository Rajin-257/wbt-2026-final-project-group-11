CREATE DATABASE IF NOT EXISTS medicine_db;
use medicine_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'customer') NOT NULL DEFAULT 'customer',
    profile_picture VARCHAR(500) NULL,
    address TEXT NULL,
    phone VARCHAR(30) NULL,
    remember_token VARCHAR(64) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category_type ENUM('liquid', 'solid') NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category_id INT  NOT NULL,
    vendor_name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    availability INT  NOT NULL DEFAULT 0 COMMENT 'stock quantity',
    description TEXT NULL,
    image_path VARCHAR(500) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE cart (
    id INT  AUTO_INCREMENT PRIMARY KEY,
    user_id INT  NOT NULL,
    medicine_id INT  NOT NULL,
    quantity INT  NOT NULL DEFAULT 1,
    added_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    id INT  AUTO_INCREMENT PRIMARY KEY,
    user_id INT  NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    shipping_address TEXT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
    payment_method VARCHAR(50) NOT NULL,
    order_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ;

CREATE TABLE order_items (
    id INT  AUTO_INCREMENT PRIMARY KEY,
    order_id INT  NOT NULL,
    medicine_id INT  NOT NULL,
    quantity INT  NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(100) NULL,
    payment_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

