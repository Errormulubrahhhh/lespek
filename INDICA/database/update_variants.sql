-- SQL untuk menambah support Iced/Hot variant
-- Jalankan query ini di phpMyAdmin atau MySQL command line

USE db_indica;

-- 1. Tambah kolom ke table products untuk tipe variant
ALTER TABLE products ADD COLUMN `has_variant` ENUM('yes', 'no') DEFAULT 'no' AFTER `stok`;
ALTER TABLE products ADD COLUMN `harga_iced` DECIMAL(10,2) DEFAULT NULL AFTER `harga`;

-- 2. Tambah kolom ke transaction_details untuk menyimpan pilihan variant
ALTER TABLE transaction_details ADD COLUMN `variant` VARCHAR(50) DEFAULT NULL AFTER `nama_produk`;

-- Alternatif: Jika ingin lebih fleksibel, buat table terpisah untuk variants
-- CREATE TABLE `product_variants` (
--   `id` int NOT NULL AUTO_INCREMENT,
--   `product_id` int NOT NULL,
--   `nama_variant` varchar(50) NOT NULL,
--   `harga_tambahan` decimal(10,2) DEFAULT 0,
--   `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`),
--   KEY `product_id` (`product_id`),
--   CONSTRAINT `product_variants_ibfk_1`
--     FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
