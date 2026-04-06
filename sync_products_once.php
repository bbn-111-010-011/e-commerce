<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';

if (!(bool) $pdo->query("SHOW TABLES LIKE 'products'")->fetchColumn()) {
    exit("La table products n'existe pas encore. Importez d'abord sql/chacha-v7-2-products-mysql-stable.sql");
}

$jsonFile = __DIR__ . '/data/products.json';
if (!is_file($jsonFile)) {
    exit('Fichier data/products.json introuvable.');
}

$data = json_decode((string) file_get_contents($jsonFile), true);
if (!is_array($data)) {
    exit('JSON produits invalide.');
}

$insertProduct = $pdo->prepare("
    INSERT INTO products (
        slug, name, category, category_label, price, old_price, badge, color, stock, description, image, featured, is_active
    ) VALUES (
        :slug, :name, :category, :category_label, :price, :old_price, :badge, :color, :stock, :description, :image, :featured, 1
    )
    ON DUPLICATE KEY UPDATE
        name = VALUES(name),
        category = VALUES(category),
        category_label = VALUES(category_label),
        price = VALUES(price),
        old_price = VALUES(old_price),
        badge = VALUES(badge),
        color = VALUES(color),
        stock = VALUES(stock),
        description = VALUES(description),
        image = VALUES(image),
        featured = VALUES(featured),
        is_active = 1
");

$findId = $pdo->prepare("SELECT id FROM products WHERE slug = :slug LIMIT 1");
$hasSizes = (bool) $pdo->query("SHOW TABLES LIKE 'product_sizes'")->fetchColumn();
$deleteSizes = $hasSizes ? $pdo->prepare("DELETE FROM product_sizes WHERE product_id = :product_id") : null;
$insertSize = $hasSizes ? $pdo->prepare("INSERT INTO product_sizes (product_id, size_label, sort_order) VALUES (:product_id, :size_label, :sort_order)") : null;

$count = 0;
foreach ($data as $i => $product) {
    $slug = (string) ($product['slug'] ?? ('product-' . ($i + 1)));
    if ($slug === '') $slug = 'product-' . ($i + 1);

    $insertProduct->execute([
        'slug' => $slug,
        'name' => $product['name'] ?? 'Produit',
        'category' => $product['category'] ?? 'robe-soiree',
        'category_label' => $product['categoryLabel'] ?? 'Robe de soirée',
        'price' => (float) ($product['price'] ?? 0),
        'old_price' => (float) ($product['oldPrice'] ?? 0),
        'badge' => $product['badge'] ?? 'Nouveauté',
        'color' => $product['color'] ?? 'Noir',
        'stock' => (int) ($product['stock'] ?? 0),
        'description' => $product['description'] ?? 'Produit importé.',
        'image' => $product['image'] ?? 'assets/img/products/robe-soiree.jpg',
        'featured' => !empty($product['featured']) ? 1 : 0,
    ]);

    $findId->execute(['slug' => $slug]);
    $productId = (int) ($findId->fetchColumn() ?: 0);

    if ($productId > 0 && $hasSizes) {
        $deleteSizes->execute(['product_id' => $productId]);
        $sizes = isset($product['sizes']) && is_array($product['sizes']) && $product['sizes'] ? $product['sizes'] : ['S','M','L','XL','XXL'];

        foreach (array_values($sizes) as $index => $size) {
            $insertSize->execute([
                'product_id' => $productId,
                'size_label' => (string) $size,
                'sort_order' => $index + 1,
            ]);
        }
    }

    $count++;
}

echo "Import terminé. Produits synchronisés : " . $count . "<br>";
echo "Supprimez ce fichier après utilisation.";
?>