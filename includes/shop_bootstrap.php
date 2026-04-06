<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/auth.php';

function shop_products_file(): string
{
    return __DIR__ . '/../data/products.json';
}

function shop_normalize_category(string $value): string
{
    $v = mb_strtolower(trim($value), 'UTF-8');
    $map = [
        'robe de soirée' => 'robe-soiree',
        'robe soiree' => 'robe-soiree',
        'robe-soiree' => 'robe-soiree',
        'caftan femme' => 'caftan-femme',
        'caftan-femme' => 'caftan-femme',
        'caftan enfant' => 'caftan-enfant',
        'caftan-enfant' => 'caftan-enfant',
        'karakou femme' => 'karakou-femme',
        'karakou-femme' => 'karakou-femme',
        'karakou enfant' => 'karakou-enfant',
        'karakou-enfant' => 'karakou-enfant',
    ];
    return $map[$v] ?? ($v !== '' ? str_replace(' ', '-', $v) : 'robe-soiree');
}

function shop_category_label_from_slug(string $slug): string
{
    $map = [
        'robe-soiree' => 'Robe de soirée',
        'caftan-femme' => 'Caftan femme',
        'caftan-enfant' => 'Caftan enfant',
        'karakou-femme' => 'Karakou femme',
        'karakou-enfant' => 'Karakou enfant',
    ];
    return $map[$slug] ?? $slug;
}

function shop_stock_status(int $stock): string
{
    if ($stock <= 0) {
        return 'out';
    }
    if ($stock <= 3) {
        return 'low';
    }
    return 'in';
}

function shop_stock_label(int $stock): string
{
    return match (shop_stock_status($stock)) {
        'out' => 'Rupture de stock',
        'low' => 'Stock faible',
        default => 'En stock',
    };
}

function shop_product_gallery_from_db(PDO $pdo, int $productId, string $fallbackImage): array
{
    $images = [];
    if ((bool) $pdo->query("SHOW TABLES LIKE 'product_images'")->fetchColumn()) {
        try {
            $stmt = $pdo->prepare("SELECT image_path, is_primary, sort_order FROM product_images WHERE product_id = :product_id ORDER BY is_primary DESC, sort_order ASC, id ASC");
            $stmt->execute(['product_id' => $productId]);
            foreach ($stmt->fetchAll() ?: [] as $row) {
                $path = trim((string) ($row['image_path'] ?? ''));
                if ($path !== '') {
                    $images[] = $path;
                }
            }
        } catch (Throwable $e) {
        }
    }

    if (!$images) {
        $images[] = $fallbackImage !== '' ? $fallbackImage : 'assets/img/products/robe-soiree.jpg';
    }

    return array_values(array_unique($images));
}

function shop_products_from_db(PDO $pdo): array
{
    if (!(bool) $pdo->query("SHOW TABLES LIKE 'products'")->fetchColumn()) {
        return [];
    }

    $stmt = $pdo->query("SELECT * FROM products WHERE COALESCE(is_active, 1) = 1 ORDER BY id DESC");
    $products = $stmt->fetchAll() ?: [];
    $hasSizesTable = (bool) $pdo->query("SHOW TABLES LIKE 'product_sizes'")->fetchColumn();

    foreach ($products as &$product) {
        $sizes = ['S','M','L','XL','XXL'];

        if ($hasSizesTable) {
            $sizesStmt = $pdo->prepare("SELECT size_label FROM product_sizes WHERE product_id = :product_id ORDER BY sort_order ASC, id ASC");
            $sizesStmt->execute(['product_id' => (int) $product['id']]);
            $fetched = array_column($sizesStmt->fetchAll() ?: [], 'size_label');
            if ($fetched) {
                $sizes = array_values(array_unique(array_map('strval', $fetched)));
            }
        }

        $rawCategory = (string) ($product['category'] ?? $product['category_label'] ?? '');
        $normalizedCategory = shop_normalize_category($rawCategory);
        $image = trim((string) ($product['image'] ?? ''));
        if ($image === '') {
            $image = 'assets/img/products/robe-soiree.jpg';
        }

        $stock = (int) ($product['stock'] ?? 0);

        $product['category'] = $normalizedCategory;
        $product['categoryLabel'] = shop_category_label_from_slug($normalizedCategory);
        $product['sizes'] = $sizes;
        $product['price'] = (float) ($product['price'] ?? 0);
        $product['oldPrice'] = (float) ($product['old_price'] ?? 0);
        $product['featured'] = !empty($product['featured']);
        $product['stock'] = $stock;
        $product['stock_status'] = shop_stock_status($stock);
        $product['stock_label'] = shop_stock_label($stock);
        $product['image'] = $image;
        $product['images'] = shop_product_gallery_from_db($pdo, (int) $product['id'], $image);
        if (admin_table_has_column_fallback($pdo, 'products', 'sku')) {
            $product['sku'] = (string) ($product['sku'] ?? '');
        }
        unset($product['old_price'], $product['category_label']);
    }

    return array_values($products);
}

function admin_table_has_column_fallback(PDO $pdo, string $table, string $column): bool
{
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table_name AND COLUMN_NAME = :column_name");
        $stmt->execute(['table_name' => $table, 'column_name' => $column]);
        return (int) $stmt->fetchColumn() > 0;
    } catch (Throwable $e) {
        return false;
    }
}

function shop_products_from_json(): array
{
    $file = shop_products_file();
    if (!is_file($file)) {
        return [];
    }
    $json = file_get_contents($file);
    $data = json_decode((string) $json, true);
    if (!is_array($data)) {
        return [];
    }

    foreach ($data as &$product) {
        $stock = (int) ($product['stock'] ?? 0);
        $product['stock_status'] = shop_stock_status($stock);
        $product['stock_label'] = shop_stock_label($stock);
        if (empty($product['image'])) {
            $product['image'] = 'assets/img/products/robe-soiree.jpg';
        }
        if (empty($product['images']) || !is_array($product['images'])) {
            $product['images'] = [$product['image']];
        }
        if (empty($product['sizes']) || !is_array($product['sizes'])) {
            $product['sizes'] = ['S','M','L','XL','XXL'];
        }
        if (!isset($product['sku'])) {
            $product['sku'] = '';
        }
    }

    return $data;
}

function shop_products(): array
{
    global $pdo;

    try {
        $dbProducts = shop_products_from_db($pdo);
        if ($dbProducts) {
            return $dbProducts;
        }
    } catch (Throwable $e) {
    }

    return shop_products_from_json();
}

function shop_find_product(int $productId): ?array
{
    foreach (shop_products() as $product) {
        if ((int) ($product['id'] ?? 0) === $productId) {
            return $product;
        }
    }
    return null;
}

function active_cart_id_for_user(PDO $pdo, int $userId): int
{
    $stmt = $pdo->prepare("SELECT id FROM carts WHERE user_id = :user_id AND status = 'active' ORDER BY id DESC LIMIT 1");
    $stmt->execute(['user_id' => $userId]);
    $id = (int) ($stmt->fetchColumn() ?: 0);

    if ($id > 0) {
        return $id;
    }

    $insert = $pdo->prepare("INSERT INTO carts (user_id, session_token, status) VALUES (:user_id, NULL, 'active')");
    $insert->execute(['user_id' => $userId]);
    return (int) $pdo->lastInsertId();
}

function cart_items_from_session(): array
{
    global $pdo;

    if (is_logged_in()) {
        $user = current_user();
        $cartId = active_cart_id_for_user($pdo, (int) $user['id']);

        $stmt = $pdo->prepare('
            SELECT product_id, quantity, variant_id
            FROM cart_items
            WHERE cart_id = :cart_id
            ORDER BY id ASC
        ');
        $stmt->execute(['cart_id' => $cartId]);
        $rows = $stmt->fetchAll();

        $items = [];
        foreach ($rows as $row) {
            $size = 'M';
            if (!empty($row['variant_id'])) {
                $v = $pdo->prepare('
                    SELECT s.label
                    FROM product_variants pv
                    LEFT JOIN sizes s ON s.id = pv.size_id
                    WHERE pv.id = :id
                    LIMIT 1
                ');
                $v->execute(['id' => (int) $row['variant_id']]);
                $sizeLabel = $v->fetchColumn();
                if ($sizeLabel) {
                    $size = (string) $sizeLabel;
                }
            }

            $items[] = [
                'productId' => (int) $row['product_id'],
                'qty' => max(1, (int) $row['quantity']),
                'size' => $size,
            ];
        }

        $_SESSION['chacha_cart'] = $items;
        return $items;
    }

    $cart = $_SESSION['chacha_cart'] ?? [];
    return is_array($cart) ? $cart : [];
}

function cart_quantity_for_product(int $productId): int
{
    $qty = 0;
    foreach (cart_items_from_session() as $item) {
        if ((int) ($item['productId'] ?? 0) === $productId) {
            $qty += max(1, (int) ($item['qty'] ?? 1));
        }
    }
    return $qty;
}

function resolve_variant_id(PDO $pdo, int $productId, string $size): ?int
{
    $stmt = $pdo->prepare('
        SELECT pv.id
        FROM product_variants pv
        LEFT JOIN sizes s ON s.id = pv.size_id
        WHERE pv.product_id = :product_id
          AND s.label = :size
        LIMIT 1
    ');
    $stmt->execute([
        'product_id' => $productId,
        'size' => $size,
    ]);
    $variantId = $stmt->fetchColumn();

    return $variantId ? (int) $variantId : null;
}

function cart_save_to_session(array $items): void
{
    global $pdo;

    $_SESSION['chacha_cart'] = array_values($items);

    if (!is_logged_in()) {
        return;
    }

    $user = current_user();
    $cartId = active_cart_id_for_user($pdo, (int) $user['id']);

    $delete = $pdo->prepare('DELETE FROM cart_items WHERE cart_id = :cart_id');
    $delete->execute(['cart_id' => $cartId]);

    $insert = $pdo->prepare('
        INSERT INTO cart_items (cart_id, product_id, variant_id, quantity, unit_price)
        VALUES (:cart_id, :product_id, :variant_id, :quantity, :unit_price)
    ');

    foreach ($items as $item) {
        $productId = (int) ($item['productId'] ?? 0);
        $qty = max(1, (int) ($item['qty'] ?? 1));
        $size = (string) ($item['size'] ?? 'M');
        $product = shop_find_product($productId);

        if (!$product) {
            continue;
        }

        $variantId = resolve_variant_id($pdo, $productId, $size);
        $insert->execute([
            'cart_id' => $cartId,
            'product_id' => $productId,
            'variant_id' => $variantId,
            'quantity' => $qty,
            'unit_price' => (float) ($product['price'] ?? 0),
        ]);
    }
}

function cart_add_item(int $productId, int $qty = 1, string $size = 'M'): void
{
    $qty = max(1, $qty);
    $size = trim($size) !== '' ? trim($size) : 'M';
    $items = cart_items_from_session();

    foreach ($items as &$item) {
        if ((int) ($item['productId'] ?? 0) === $productId && (string) ($item['size'] ?? 'M') === $size) {
            $item['qty'] = max(1, (int) ($item['qty'] ?? 1) + $qty);
            cart_save_to_session($items);
            return;
        }
    }

    $items[] = ['productId' => $productId, 'qty' => $qty, 'size' => $size];
    cart_save_to_session($items);
}

function cart_remove_item(int $productId, string $size = 'M'): void
{
    $items = array_filter(
        cart_items_from_session(),
        fn(array $item): bool => !(
            (int) ($item['productId'] ?? 0) === $productId
            && (string) ($item['size'] ?? 'M') === $size
        )
    );
    cart_save_to_session(array_values($items));
}

function cart_update_item(int $productId, string $oldSize, int $qty, string $newSize): void
{
    $qty = max(1, $qty);
    $newSize = trim($newSize) !== '' ? trim($newSize) : 'M';
    $oldSize = trim($oldSize) !== '' ? trim($oldSize) : 'M';

    $items = cart_items_from_session();
    $updated = [];

    foreach ($items as $item) {
        $sameLine = (int) ($item['productId'] ?? 0) === $productId && (string) ($item['size'] ?? 'M') === $oldSize;
        if ($sameLine) {
            continue;
        }
        $updated[] = $item;
    }

    $merged = false;
    foreach ($updated as &$item) {
        if ((int) ($item['productId'] ?? 0) === $productId && (string) ($item['size'] ?? 'M') === $newSize) {
            $item['qty'] = max(1, (int) ($item['qty'] ?? 1) + $qty);
            $merged = true;
            break;
        }
    }

    if (!$merged) {
        $updated[] = ['productId' => $productId, 'qty' => $qty, 'size' => $newSize];
    }

    cart_save_to_session($updated);
}

function cart_count(): int
{
    $count = 0;
    foreach (cart_items_from_session() as $item) {
        $count += max(1, (int) ($item['qty'] ?? 1));
    }
    return $count;
}

function cart_total_detailed(): array
{
    $cart = cart_items_from_session();
    $lines = [];
    $subtotal = 0.0;

    foreach ($cart as $item) {
        $productId = (int) ($item['productId'] ?? 0);
        $qty = max(1, (int) ($item['qty'] ?? 1));
        $size = (string) ($item['size'] ?? 'M');
        $product = shop_find_product($productId);

        if (!$product) {
            continue;
        }

        $unitPrice = (float) ($product['price'] ?? 0);
        $lineTotal = $unitPrice * $qty;
        $subtotal += $lineTotal;

        $lines[] = [
            'product_id' => $productId,
            'product_name' => $product['name'],
            'product_sku' => strtoupper((string) ($product['sku'] ?? $product['slug'] ?? ('P-' . $productId))),
            'size_label' => $size,
            'available_sizes' => $product['sizes'] ?? ['S','M','L','XL','XXL'],
            'color_label' => $product['color'] ?? null,
            'quantity' => $qty,
            'unit_price' => $unitPrice,
            'line_total' => $lineTotal,
            'image' => $product['image'] ?? null,
            'stock' => (int) ($product['stock'] ?? 0),
            'stock_status' => (string) ($product['stock_status'] ?? 'in'),
        ];
    }

    $shipping = $subtotal > 0 ? 0.00 : 0.00;
    $total = $subtotal + $shipping;

    return ['items' => $lines, 'subtotal' => $subtotal, 'shipping_amount' => $shipping, 'total_amount' => $total];
}

function favorites_table_exists(PDO $pdo): bool
{
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'wishlist_items'");
        return (bool) $stmt->fetchColumn();
    } catch (Throwable $e) {
        return false;
    }
}

function favorite_ids(PDO $pdo, int $userId): array
{
    if (!favorites_table_exists($pdo)) {
        return [];
    }
    $stmt = $pdo->prepare('SELECT product_id FROM wishlist_items WHERE user_id = :user_id ORDER BY id DESC');
    $stmt->execute(['user_id' => $userId]);
    return array_map('intval', array_column($stmt->fetchAll(), 'product_id'));
}

function favorites_count(PDO $pdo, int $userId): int
{
    if (!favorites_table_exists($pdo)) {
        return 0;
    }
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM wishlist_items WHERE user_id = :user_id');
    $stmt->execute(['user_id' => $userId]);
    return (int) $stmt->fetchColumn();
}

function is_favorite(PDO $pdo, int $userId, int $productId): bool
{
    if (!favorites_table_exists($pdo)) {
        return false;
    }
    $stmt = $pdo->prepare('SELECT id FROM wishlist_items WHERE user_id = :user_id AND product_id = :product_id LIMIT 1');
    $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
    return (bool) $stmt->fetch();
}

function toggle_favorite(PDO $pdo, int $userId, int $productId): bool
{
    if (!favorites_table_exists($pdo)) {
        throw new RuntimeException('La table wishlist_items n\'existe pas encore. Importez le SQL chacha-v3-favoris.sql');
    }

    if (is_favorite($pdo, $userId, $productId)) {
        $stmt = $pdo->prepare('DELETE FROM wishlist_items WHERE user_id = :user_id AND product_id = :product_id');
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
        return false;
    }

    $stmt = $pdo->prepare('INSERT INTO wishlist_items (user_id, product_id) VALUES (:user_id, :product_id)');
    $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
    return true;
}

function favorite_products(PDO $pdo, int $userId): array
{
    $ids = favorite_ids($pdo, $userId);
    if (!$ids) {
        return [];
    }

    $products = shop_products();
    $mapped = [];
    foreach ($products as $product) {
        $mapped[(int) $product['id']] = $product;
    }

    $result = [];
    foreach ($ids as $id) {
        if (isset($mapped[$id])) {
            $result[] = $mapped[$id];
        }
    }

    return $result;
}

function format_eur(float $amount): string
{
    return number_format($amount, 2, ',', ' ') . ' €';
}

function require_cart_not_empty(): void
{
    $cart = cart_total_detailed();
    if (empty($cart['items'])) {
        set_flash('error', 'Votre panier est vide.');
        header('Location: panier.php');
        exit;
    }
}

function next_order_number(PDO $pdo): string
{
    $datePrefix = 'CH' . date('Ymd');
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE order_number LIKE :prefix");
    $stmt->execute(['prefix' => $datePrefix . '%']);
    $count = (int) $stmt->fetchColumn();
    return $datePrefix . '-' . str_pad((string) ($count + 1), 4, '0', STR_PAD_LEFT);
}
?>