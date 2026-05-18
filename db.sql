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

-- ---------------------------------------------------------------------------
-- Dummy data (all test accounts use password: password123)
-- ---------------------------------------------------------------------------

INSERT INTO users (name, email, password_hash, role, address, phone) VALUES
('Admin User', 'admin@pharmacy.test', '$2y$10$5Xl7B8WoOQ3ipMQ2Lw8A8.80ayCRMiudhWR3bUyEccdtUW5aUnCRO', 'admin', '12 Admin Street, Dhaka', '01700000001'),
('Jane Doe', 'jane@example.com', '$2y$10$5Xl7B8WoOQ3ipMQ2Lw8A8.80ayCRMiudhWR3bUyEccdtUW5aUnCRO', 'customer', '45 Lake Road, Chittagong', '01711111111'),
('John Smith', 'john@example.com', '$2y$10$5Xl7B8WoOQ3ipMQ2Lw8A8.80ayCRMiudhWR3bUyEccdtUW5aUnCRO', 'customer', '78 Green Avenue, Sylhet', '01722222222'),
('Aisha Rahman', 'aisha@example.com', '$2y$10$5Xl7B8WoOQ3ipMQ2Lw8A8.80ayCRMiudhWR3bUyEccdtUW5aUnCRO', 'customer', '3 Park Lane, Rajshahi', '01733333333');

INSERT INTO categories (name, category_type) VALUES
('Pain Relief', 'solid'),
('Antibiotics', 'solid'),
('Vitamins', 'solid'),
('Cough Syrup', 'liquid'),
('Antacids', 'liquid'),
('Skin Care', 'solid');

INSERT INTO medicines (name, category_id, vendor_name, price, availability, description, image_path) VALUES
('Paracetamol 500mg', 1, 'Square Pharmaceuticals', 2.50, 200, 'Fast-acting pain and fever relief tablets.', NULL),
('Ibuprofen 400mg', 1, 'Beximco Pharma', 4.00, 150, 'Anti-inflammatory pain relief.', NULL),
('Amoxicillin 250mg', 2, 'Incepta Pharmaceuticals', 12.00, 80, 'Broad-spectrum antibiotic capsules.', NULL),
('Azithromycin 500mg', 2, 'Renata Limited', 18.50, 60, 'Macrolide antibiotic for bacterial infections.', NULL),
('Vitamin C 1000mg', 3, 'ACI Limited', 8.00, 120, 'Immune support chewable tablets.', NULL),
('Multivitamin Daily', 3, 'Opsonin Pharma', 15.00, 90, 'Complete daily vitamin supplement.', NULL),
('Benadryl Cough Syrup', 4, 'GlaxoSmithKline', 6.50, 75, 'Relieves dry cough and throat irritation.', NULL),
('Gaviscon Liquid', 5, 'Reckitt Benckiser', 9.00, 50, 'Antacid for heartburn and indigestion.', NULL),
('Hydrocortisone Cream 1%', 6, 'Eskayef Pharmaceuticals', 5.50, 100, 'Topical cream for mild skin irritation.', NULL),
('Antiseptic Liquid 100ml', 5, 'Unilever Bangladesh', 3.25, 180, 'First-aid antiseptic for cuts and wounds.', NULL);

INSERT INTO cart (user_id, medicine_id, quantity) VALUES
(2, 1, 2),
(2, 5, 1),
(3, 3, 1),
(3, 7, 2),
(4, 6, 1);

INSERT INTO orders (user_id, total_amount, shipping_address, status, payment_method, order_date) VALUES
(2, 13.00, '45 Lake Road, Chittagong', 'accepted', 'card', '2026-05-10 14:30:00'),
(2, 24.50, '45 Lake Road, Chittagong', 'pending', 'cash_on_delivery', '2026-05-15 09:15:00'),
(3, 31.00, '78 Green Avenue, Sylhet', 'accepted', 'mobile_banking', '2026-05-12 11:00:00'),
(4, 15.00, '3 Park Lane, Rajshahi', 'rejected', 'card', '2026-05-08 16:45:00');

INSERT INTO order_items (order_id, medicine_id, quantity, unit_price) VALUES
(1, 1, 2, 2.50),
(1, 5, 1, 8.00),
(2, 4, 1, 18.50),
(2, 7, 1, 6.50),
(3, 3, 1, 12.00),
(3, 4, 1, 18.50),
(3, 9, 1, 5.50),
(4, 6, 1, 15.00);

INSERT INTO payments (order_id, amount, payment_method, transaction_id, payment_date) VALUES
(1, 13.00, 'card', 'TXN-20260510-001', '2026-05-10 14:31:00'),
(3, 31.00, 'mobile_banking', 'BKASH-88291045', '2026-05-12 11:02:00'),
(4, 15.00, 'card', 'TXN-20260508-099', '2026-05-08 16:46:00');
