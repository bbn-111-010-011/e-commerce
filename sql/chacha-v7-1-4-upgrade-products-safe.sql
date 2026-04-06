-- CHACHA V7.1.4 - Mise à niveau compatible de la table products
USE boutique_chacha;

CREATE TABLE IF NOT EXISTS products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @db := DATABASE();

-- helper pattern repeated for each column
SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='products' AND COLUMN_NAME='slug');
SET @sql := IF(@exists=0, 'ALTER TABLE products ADD COLUMN slug VARCHAR(191) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='products' AND COLUMN_NAME='name');
SET @sql := IF(@exists=0, 'ALTER TABLE products ADD COLUMN name VARCHAR(255) NOT NULL DEFAULT "Produit"', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='products' AND COLUMN_NAME='category');
SET @sql := IF(@exists=0, 'ALTER TABLE products ADD COLUMN category VARCHAR(100) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='products' AND COLUMN_NAME='category_label');
SET @sql := IF(@exists=0, 'ALTER TABLE products ADD COLUMN category_label VARCHAR(150) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='products' AND COLUMN_NAME='price');
SET @sql := IF(@exists=0, 'ALTER TABLE products ADD COLUMN price DECIMAL(10,2) NOT NULL DEFAULT 0.00', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='products' AND COLUMN_NAME='old_price');
SET @sql := IF(@exists=0, 'ALTER TABLE products ADD COLUMN old_price DECIMAL(10,2) NOT NULL DEFAULT 0.00', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='products' AND COLUMN_NAME='badge');
SET @sql := IF(@exists=0, 'ALTER TABLE products ADD COLUMN badge VARCHAR(100) DEFAULT "Nouveauté"', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='products' AND COLUMN_NAME='color');
SET @sql := IF(@exists=0, 'ALTER TABLE products ADD COLUMN color VARCHAR(100) DEFAULT "Noir"', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='products' AND COLUMN_NAME='stock');
SET @sql := IF(@exists=0, 'ALTER TABLE products ADD COLUMN stock INT NOT NULL DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='products' AND COLUMN_NAME='description');
SET @sql := IF(@exists=0, 'ALTER TABLE products ADD COLUMN description TEXT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='products' AND COLUMN_NAME='image');
SET @sql := IF(@exists=0, 'ALTER TABLE products ADD COLUMN image VARCHAR(255) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='products' AND COLUMN_NAME='featured');
SET @sql := IF(@exists=0, 'ALTER TABLE products ADD COLUMN featured TINYINT(1) NOT NULL DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='products' AND COLUMN_NAME='is_active');
SET @sql := IF(@exists=0, 'ALTER TABLE products ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='products' AND COLUMN_NAME='created_at');
SET @sql := IF(@exists=0, 'ALTER TABLE products ADD COLUMN created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='products' AND COLUMN_NAME='updated_at');
SET @sql := IF(@exists=0, 'ALTER TABLE products ADD COLUMN updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE products SET slug = CONCAT('product-', id) WHERE slug IS NULL OR slug = '';
UPDATE products SET category = 'robe-soiree' WHERE category IS NULL OR category = '';
UPDATE products SET category_label = 'Robe de soirée' WHERE category_label IS NULL OR category_label = '';
UPDATE products SET badge = 'Nouveauté' WHERE badge IS NULL OR badge = '';
UPDATE products SET color = 'Noir' WHERE color IS NULL OR color = '';
UPDATE products SET image = 'assets/img/products/robe-soiree.jpg' WHERE image IS NULL OR image = '';
UPDATE products SET is_active = 1 WHERE is_active IS NULL;

SET @idx_exists := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='products' AND INDEX_NAME='uq_products_slug');
SET @sql := IF(@idx_exists=0, 'ALTER TABLE products ADD UNIQUE KEY uq_products_slug (slug)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS product_sizes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  size_label VARCHAR(20) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  UNIQUE KEY uq_product_size (product_id, size_label),
  CONSTRAINT fk_product_sizes_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
