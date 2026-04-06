<?php
declare(strict_types=1);

require_once __DIR__ . '/shop_bootstrap.php';

function admin_categories(): array
{
    return [
        'robe-soiree' => 'Robe de soirée',
        'caftan-femme' => 'Caftan femme',
        'caftan-enfant' => 'Caftan enfant',
        'karakou-femme' => 'Karakou femme',
        'karakou-enfant' => 'Karakou enfant',
    ];
}

function admin_category_label(string $category): string
{
    $categories = admin_categories();
    return $categories[$category] ?? $category;
}

function admin_default_sizes(): array
{
    return ['S', 'M', 'L', 'XL', 'XXL'];
}

function admin_slugify(string $text): string
{
    $text = mb_strtolower(trim($text), 'UTF-8');
    $replacements = [
        'à'=>'a','â'=>'a','ä'=>'a','á'=>'a',
        'ç'=>'c',
        'é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
        'î'=>'i','ï'=>'i','í'=>'i',
        'ô'=>'o','ö'=>'o','ó'=>'o',
        'ù'=>'u','û'=>'u','ü'=>'u','ú'=>'u',
        'ÿ'=>'y','ñ'=>'n',
        "'" => '', '"' => ''
    ];
    $text = strtr($text, $replacements);
    $text = preg_replace('/[^a-z0-9]+/u', '-', $text) ?? $text;
    return trim($text, '-') ?: 'produit';
}

function admin_validate_product_input(array $post): array
{
    $errors = [];
    $name = trim((string) ($post['name'] ?? ''));
    $category = trim((string) ($post['category'] ?? ''));
    $price = (float) ($post['price'] ?? -1);
    $oldPrice = (float) ($post['old_price'] ?? 0);
    $stock = (int) ($post['stock'] ?? -1);
    $color = trim((string) ($post['color'] ?? ''));
    $badge = trim((string) ($post['badge'] ?? ''));
    $description = trim((string) ($post['description'] ?? ''));

    if ($name === '') $errors[] = 'Le nom du produit est obligatoire.';
    if (!array_key_exists($category, admin_categories())) $errors[] = 'La catégorie sélectionnée est invalide.';
    if ($price < 0) $errors[] = 'Le prix doit être supérieur ou égal à 0.';
    if ($oldPrice < 0) $errors[] = 'L’ancien prix doit être supérieur ou égal à 0.';
    if ($stock < 0) $errors[] = 'Le stock doit être supérieur ou égal à 0.';
    if ($color === '') $errors[] = 'La couleur est obligatoire.';
    if ($badge === '') $errors[] = 'Le badge est obligatoire.';
    if ($description === '') $errors[] = 'La description est obligatoire.';
    return $errors;
}

function admin_image_upload_error_message(int $code): string
{
    return match ($code) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'L’image est trop volumineuse.',
        UPLOAD_ERR_PARTIAL => 'Le fichier image est incomplet.',
        UPLOAD_ERR_NO_TMP_DIR => 'Le dossier temporaire est manquant.',
        UPLOAD_ERR_CANT_WRITE => 'Impossible d’écrire le fichier sur le disque.',
        UPLOAD_ERR_EXTENSION => 'Upload image bloqué par une extension PHP.',
        default => 'Erreur inconnue pendant l’upload image.',
    };
}

function admin_handle_product_image_upload(string $slug, string $currentImage = ''): array
{
    $defaultImage = $currentImage !== '' ? $currentImage : 'assets/img/products/' . $slug . '.png';

    if (empty($_FILES['image']) || !isset($_FILES['image']['tmp_name'])) {
        return ['path' => $defaultImage, 'error' => null];
    }
    if ((int) $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        return ['path' => $defaultImage, 'error' => null];
    }
    if ((int) $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        return ['path' => $defaultImage, 'error' => admin_image_upload_error_message((int) $_FILES['image']['error'])];
    }

    $uploadDir = __DIR__ . '/../assets/img/products/';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
        return ['path' => $defaultImage, 'error' => 'Impossible de créer le dossier d’upload des images.'];
    }

    $originalName = (string) ($_FILES['image']['name'] ?? 'image.png');
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
        return ['path' => $defaultImage, 'error' => 'Format image non autorisé.'];
    }

    $mime = mime_content_type($_FILES['image']['tmp_name']) ?: '';
    $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($mime, $allowedMime, true)) {
        return ['path' => $defaultImage, 'error' => 'Le fichier envoyé n’est pas une image valide.'];
    }

    if ((int) ($_FILES['image']['size'] ?? 0) > 5 * 1024 * 1024) {
        return ['path' => $defaultImage, 'error' => 'L’image dépasse 5 Mo.'];
    }

    $filename = $slug . '.' . $extension;
    $destination = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
        return ['path' => 'assets/img/products/' . $filename, 'error' => null];
    }

    return ['path' => $defaultImage, 'error' => 'Impossible d’enregistrer l’image sur le serveur.'];
}

function admin_table_has_column(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table_name AND COLUMN_NAME = :column_name");
    $stmt->execute(['table_name' => $table, 'column_name' => $column]);
    return (int) $stmt->fetchColumn() > 0;
}

function admin_generate_sku(string $category, string $slug, int $id = 0): string
{
    $prefixMap = [
        'robe-soiree' => 'RBS',
        'caftan-femme' => 'CAF',
        'caftan-enfant' => 'CAE',
        'karakou-femme' => 'KAF',
        'karakou-enfant' => 'KAE',
    ];
    $prefix = $prefixMap[$category] ?? 'PRD';
    if ($id > 0) {
        return $prefix . '-' . str_pad((string) $id, 3, '0', STR_PAD_LEFT);
    }
    $tail = strtoupper(substr(preg_replace('/[^a-z0-9]/i', '', $slug), 0, 6));
    return $prefix . '-' . ($tail !== '' ? $tail : 'NEW');
}

function admin_get_category_id(PDO $pdo, string $categorySlug, string $categoryLabel): ?int
{
    if (!admin_table_has_column($pdo, 'products', 'category_id')) {
        return null;
    }
    $hasCategories = (bool) $pdo->query("SHOW TABLES LIKE 'categories'")->fetchColumn();
    if (!$hasCategories) {
        return null;
    }

    $hasSlug = admin_table_has_column($pdo, 'categories', 'slug');
    $hasName = admin_table_has_column($pdo, 'categories', 'name');

    if ($hasSlug) {
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = :slug LIMIT 1");
        $stmt->execute(['slug' => $categorySlug]);
        $id = $stmt->fetchColumn();
        if ($id) return (int) $id;
    }

    if ($hasName) {
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = :name LIMIT 1");
        $stmt->execute(['name' => $categoryLabel]);
        $id = $stmt->fetchColumn();
        if ($id) return (int) $id;
    }

    if ($hasSlug && $hasName) {
        $stmt = $pdo->prepare("INSERT INTO categories (slug, name) VALUES (:slug, :name)");
        $stmt->execute(['slug' => $categorySlug, 'name' => $categoryLabel]);
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = :slug LIMIT 1");
        $stmt->execute(['slug' => $categorySlug]);
        $id = $stmt->fetchColumn();
        return $id ? (int) $id : null;
    }

    if ($hasName) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
        $stmt->execute(['name' => $categoryLabel]);
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = :name LIMIT 1");
        $stmt->execute(['name' => $categoryLabel]);
        $id = $stmt->fetchColumn();
        return $id ? (int) $id : null;
    }

    return null;
}

function admin_load_products_db(PDO $pdo): array
{
    $stmt = $pdo->query("SELECT * FROM products WHERE COALESCE(is_active, 1) = 1 ORDER BY id DESC");
    $products = $stmt->fetchAll() ?: [];
    $hasSizesTable = (bool) $pdo->query("SHOW TABLES LIKE 'product_sizes'")->fetchColumn();

    foreach ($products as &$product) {
        $sizes = admin_default_sizes();
        if ($hasSizesTable) {
            $sizesStmt = $pdo->prepare("SELECT size_label FROM product_sizes WHERE product_id = :product_id ORDER BY sort_order ASC, id ASC");
            $sizesStmt->execute(['product_id' => (int) $product['id']]);
            $fetched = array_column($sizesStmt->fetchAll() ?: [], 'size_label');
            if ($fetched) $sizes = $fetched;
        }
        $product['sizes'] = $sizes;
        $product['price'] = (float) ($product['price'] ?? 0);
        $product['oldPrice'] = (float) ($product['old_price'] ?? 0);
        $product['featured'] = !empty($product['featured']);
        $product['stock'] = (int) ($product['stock'] ?? 0);
        $product['categoryLabel'] = admin_category_label((string) ($product['category'] ?? 'robe-soiree'));
        if (empty($product['image'])) $product['image'] = 'assets/img/products/robe-soiree.jpg';
        unset($product['old_price'], $product['category_label']);
    }

    return $products;
}

function admin_find_product_db(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch();
    if (!$product) return null;

    $hasSizesTable = (bool) $pdo->query("SHOW TABLES LIKE 'product_sizes'")->fetchColumn();
    $sizes = admin_default_sizes();
    if ($hasSizesTable) {
        $sizesStmt = $pdo->prepare("SELECT size_label FROM product_sizes WHERE product_id = :product_id ORDER BY sort_order ASC, id ASC");
        $sizesStmt->execute(['product_id' => $id]);
        $fetched = array_column($sizesStmt->fetchAll() ?: [], 'size_label');
        if ($fetched) $sizes = $fetched;
    }

    $product['sizes'] = $sizes;
    $product['price'] = (float) ($product['price'] ?? 0);
    $product['oldPrice'] = (float) ($product['old_price'] ?? 0);
    $product['featured'] = !empty($product['featured']);
    $product['stock'] = (int) ($product['stock'] ?? 0);
    $product['categoryLabel'] = admin_category_label((string) ($product['category'] ?? 'robe-soiree'));
    if (empty($product['image'])) $product['image'] = 'assets/img/products/robe-soiree.jpg';
    unset($product['old_price'], $product['category_label']);

    return $product;
}

function admin_save_product_db(PDO $pdo, array $payload): int
{
    $id = (int) ($payload['id'] ?? 0);
    $categoryId = admin_get_category_id($pdo, (string) $payload['category'], (string) $payload['categoryLabel']);

    if ($id > 0) {
        $updateParts = [];
        $params = ['id' => $id];

        $map = [
            'slug' => $payload['slug'],
            'name' => $payload['name'],
            'category' => $payload['category'],
            'category_label' => $payload['categoryLabel'],
            'price' => $payload['price'],
            'old_price' => $payload['oldPrice'],
            'badge' => $payload['badge'],
            'color' => $payload['color'],
            'stock' => $payload['stock'],
            'description' => $payload['description'],
            'image' => $payload['image'],
            'featured' => $payload['featured'] ? 1 : 0,
            'is_active' => 1,
        ];

        foreach ($map as $column => $value) {
            if (admin_table_has_column($pdo, 'products', $column)) {
                $updateParts[] = $column . ' = :' . $column;
                $params[$column] = $value;
            }
        }

        if (admin_table_has_column($pdo, 'products', 'category_id') && $categoryId !== null) {
            $updateParts[] = 'category_id = :category_id';
            $params['category_id'] = $categoryId;
        }

        if (admin_table_has_column($pdo, 'products', 'short_description')) {
            $updateParts[] = 'short_description = :short_description';
            $params['short_description'] = mb_substr((string) $payload['description'], 0, 255);
        }

        if (admin_table_has_column($pdo, 'products', 'sku')) {
            $updateParts[] = 'sku = :sku';
            $params['sku'] = admin_generate_sku((string) $payload['category'], (string) $payload['slug'], $id);
        }

        $stmt = $pdo->prepare("UPDATE products SET " . implode(', ', $updateParts) . " WHERE id = :id");
        $stmt->execute($params);
    } else {
        $columns = [];
        $placeholders = [];
        $params = [];

        $map = [
            'slug' => $payload['slug'],
            'name' => $payload['name'],
            'category' => $payload['category'],
            'category_label' => $payload['categoryLabel'],
            'price' => $payload['price'],
            'old_price' => $payload['oldPrice'],
            'badge' => $payload['badge'],
            'color' => $payload['color'],
            'stock' => $payload['stock'],
            'description' => $payload['description'],
            'image' => $payload['image'],
            'featured' => $payload['featured'] ? 1 : 0,
            'is_active' => 1,
        ];

        foreach ($map as $column => $value) {
            if (admin_table_has_column($pdo, 'products', $column)) {
                $columns[] = $column;
                $placeholders[] = ':' . $column;
                $params[$column] = $value;
            }
        }

        if (admin_table_has_column($pdo, 'products', 'category_id')) {
            $columns[] = 'category_id';
            $placeholders[] = ':category_id';
            $params['category_id'] = $categoryId ?? 0;
        }

        if (admin_table_has_column($pdo, 'products', 'short_description')) {
            $columns[] = 'short_description';
            $placeholders[] = ':short_description';
            $params['short_description'] = mb_substr((string) $payload['description'], 0, 255);
        }

        if (admin_table_has_column($pdo, 'products', 'sku')) {
            $columns[] = 'sku';
            $placeholders[] = ':sku';
            $params['sku'] = admin_generate_sku((string) $payload['category'], (string) $payload['slug']);
        }

        $stmt = $pdo->prepare("INSERT INTO products (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")");
        $stmt->execute($params);
        $id = (int) $pdo->lastInsertId();

        if (admin_table_has_column($pdo, 'products', 'sku')) {
            $sku = admin_generate_sku((string) $payload['category'], (string) $payload['slug'], $id);
            $pdo->prepare("UPDATE products SET sku = :sku WHERE id = :id")->execute(['sku' => $sku, 'id' => $id]);
        }
    }

    if ((bool) $pdo->query("SHOW TABLES LIKE 'product_sizes'")->fetchColumn()) {
        $pdo->prepare("DELETE FROM product_sizes WHERE product_id = :product_id")->execute(['product_id' => $id]);
        $insertSize = $pdo->prepare("INSERT INTO product_sizes (product_id, size_label, sort_order) VALUES (:product_id, :size_label, :sort_order)");
        foreach (admin_default_sizes() as $i => $size) {
            $insertSize->execute(['product_id' => $id, 'size_label' => $size, 'sort_order' => $i + 1]);
        }
    }

    return $id;
}

function admin_delete_product_db(PDO $pdo, int $id): void
{
    $pdo->prepare("DELETE FROM products WHERE id = :id")->execute(['id' => $id]);
}
?>