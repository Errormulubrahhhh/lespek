-- PANDUAN SETUP FITUR ICED/HOT VARIANT
-- =====================================
-- Jalankan 3 query di bawah ini di phpMyAdmin atau MySQL command line

USE db_indica;

-- 1. Tambah kolom has_variant ke table products
ALTER TABLE products ADD COLUMN `has_variant` ENUM('yes', 'no') DEFAULT 'no' AFTER `stok`;

-- 2. Tambah kolom harga_iced ke table products (untuk harga variant iced)
ALTER TABLE products ADD COLUMN `harga_iced` DECIMAL(10,2) DEFAULT NULL AFTER `harga`;

-- 3. Tambah kolom variant ke transaction_details (untuk menyimpan pilihan iced/hot)
ALTER TABLE transaction_details ADD COLUMN `variant` VARCHAR(50) DEFAULT NULL AFTER `nama_produk`;

-- Contoh: Update produk yang ada dengan variant
-- UPDATE products SET has_variant = 'yes', harga_iced = 18000 WHERE id = 1;
-- UPDATE products SET has_variant = 'yes', harga_iced = 16000 WHERE id = 2;
