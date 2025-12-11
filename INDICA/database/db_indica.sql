-- Active: 1764529663494@@127.0.0.1@3306@db_indica
CREATE DATABASE IF NOT EXISTS db_indica CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE db_indica;

-- =========================================================
-- 1. TABLE: categories (dibutuhkan oleh products)
-- =========================================================
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 2. TABLE: users
-- =========================================================
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role`            ENUM('admin', 'employee') NOT NULL DEFAULT 'employee',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =========================================================
-- 3. TABLE: products
-- =========================================================
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori_id` int DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_products_categories` (`kategori_id`),
  CONSTRAINT `fk_products_categories`
    FOREIGN KEY (`kategori_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 4. TABLE: transactions
-- =========================================================
CREATE TABLE `transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `tanggal` datetime DEFAULT CURRENT_TIMESTAMP,
  `metode_pembayaran` enum('QRIS','Cash') COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` int NOT NULL,
  `status` enum('berhasil','batal') COLLATE utf8mb4_unicode_ci DEFAULT 'berhasil',
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tanggal` (`tanggal`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `transactions_ibfk_1`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 5. TABLE: transaction_details
-- =========================================================
CREATE TABLE `transaction_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `transaction_id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `nama_produk` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga` int NOT NULL,
  `jumlah` int NOT NULL DEFAULT '1',
  `subtotal` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `qty` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `idx_transaction` (`transaction_id`),
  CONSTRAINT `transaction_details_ibfk_1`
    FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transaction_details_ibfk_2`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 6. TABLE: stock_logs
-- =========================================================
CREATE TABLE `stock_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `tipe` enum('masuk','keluar') COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` int NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `stock_logs_ibfk_1`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- INSERT DEFAULT ADMIN
-- =========================================================
INSERT INTO users (name, username, password_hash, role)
VALUES ('Administrator', 'admin', '$2y$12$p84tez6nrwTVvn1ifsU8futn.5eJIZoOudxWOdF1HKjwfkUl2ONoO', 'admin');

INSERT INTO users (name, username, password_hash, role) 
VALUES ('Employee', 'pegawai', '$2y$12$YRyAukxYq3M.JoQKIKl.J.o5EmGoGl2c1Pf17lBSYqVlRna5iRgk2', 'employee');

INSERT INTO categories (nama) VALUES
('Minuman'),
('Makanan');

INSERT INTO products (nama, kategori_id, harga, stok) VALUES
('Arenicano', 1, 19000, 999),
('KSM', 1, 20000, 999),
('Brew Beer', 1, 22000, 999),
('Kopi Susu Jamaika', 1, 24000, 999),
('Kopi Susu Gula Aren', 1, 20000, 999),
('Kopi Susu Vanilla', 1, 22000, 999),
('Kopi Susu Hazelnut', 1, 22000, 999),
('Kopi Susu Caramel', 1, 22000, 999),
('Kopi Susu Mocha', 1, 24000, 999),
('Es Kopi Susu', 1, 20000, 999),
('Es Kopi Hitam', 1, 15000, 999),
('Es Kopi Vietnam', 1, 18000, 999),
('Es Kopi Tubruk', 1, 15000, 999),
('Es Kopi Melaka', 1, 20000, 999),
('Teh Tarik', 1, 15000, 999),
('Teh Tarik Coklat', 1, 18000, 999),
('Teh Tarik Greentea', 1, 18000, 999),
('Teh Tarik Oreo', 1, 20000, 999),
('Choco Original', 1, 18000, 999),  
('Choco Lava', 1, 20000, 999),
('Choco Oreo', 1, 22000, 999),
('Choco Hazelnut', 1, 22000, 999),
('Choco Caramel', 1, 22000, 999),
('Choco Mint', 1, 22000, 999),
('Lemonade Honey', 1, 15000, 999),
('Lemonade Mint', 1, 15000, 999),
('Lemonade Strawberry', 1, 18000, 999),
('Lemonade Mango', 1, 18000, 999),
('Susu Kurma', 1, 18000, 999),
('Susu Coklat', 1, 15000, 999),
('Susu Strawberry', 1, 18000, 999),
('Susu Vanilla', 1, 18000, 999),
('Susu Caramel', 1, 18000, 999),
('Susu Hazelnut', 1, 18000, 999),
('Susu Mereun', 1, 13000, 999),
('Spanish Latte', 1, 20000,999),
('Brown Sugar Latte', 1, 22000, 999),
('Taro Latte', 1, 20000, 999),
('Purple Sweet Latte', 1, 20000, 999),
('Oreo Latte', 1, 22000, 999),
('Avocado Latte', 1, 24000, 999),
('Red Velvet Latte', 1, 22000, 999),
('Choco Banana Latte', 1, 24000, 999),
('Espresso', 1, 15000, 999),
('Americano', 1, 12000, 999),
('Cafe Latte', 1, 20000, 999),
('Cafe Mocha', 1, 22000, 999),
('Flat White', 1, 22000, 999),
('Cappuccino', 1, 22000, 999),
('Magic', 1, 24000, 999),
('Moccacino', 1, 24000, 999),
('Flavored Cafe Latte (Butterscotch)', 1, 25000, 999),
('Flavored Cafe Latte (Caramel)', 1, 25000, 999),
('Flavored Cafe Latte (Hazelnut)', 1, 25000, 999),
('Flavored Cafe Latte (Vanilla)', 1, 25000, 999),
('Filter', 1, 22000, 999),
('Cold Brew', 1, 18000, 999),
('Matcha Latte', 1, 20000, 999),
('Chai Latte', 1, 20000, 999),
('Cocoa Latte', 1, 18000, 999),
('Caramel Latte', 1, 18000, 999),
('Vanilla Latte', 1, 18000, 999),
('Mocha Latte', 1, 20000, 999),
('Salted Caramel Latte', 1, 18000, 999),
('Hazelnut Latte', 1, 18000, 999),
('Choco Rum', 1, 20000, 999);

INSERT INTO products (nama, kategori_id, harga, stok) VALUES
('Donat Kampung', 2, 10000, 15),
('Kentang Goreng', 2, 15000, 15),
('Mie Nyemek', 2, 17000, 10);

