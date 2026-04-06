<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/shop_bootstrap.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/product_admin_helpers.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: products.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$name = trim((string) ($_POST['name'] ?? ''));
$category = trim((string) ($_POST['category'] ?? 'robe-soiree'));
$price = (float) ($_POST['price'] ?? 0);
$oldPrice = (float) ($_POST['old_price'] ?? 0);
$stock = max(0, (int) ($_POST['stock'] ?? 0));
$color = trim((string) ($_POST['color'] ?? 'Noir'));
$badge = trim((string) ($_POST['badge'] ?? 'Nouveauté'));
$description = trim((string) ($_POST['description'] ?? ''));
$featured = isset($_POST['featured']);
$currentImage = trim((string) ($_POST['current_image'] ?? ''));

$errors = admin_validate_product_input($_POST);
if ($errors) {
    set_flash('error', implode(' ', $errors));
    header('Location: ' . ($id > 0 ? 'product-edit.php?id=' . $id : 'product-add.php'));
    exit;
}

$slug = admin_slugify($name);
$imageResult = admin_handle_product_image_upload($slug, $currentImage);

if (!empty($imageResult['error'])) {
    set_flash('error', $imageResult['error']);
    header('Location: ' . ($id > 0 ? 'product-edit.php?id=' . $id : 'product-add.php'));
    exit;
}

$payload = [
    'id' => $id,
    'slug' => $slug,
    'name' => $name,
    'category' => $category,
    'categoryLabel' => admin_category_label($category),
    'price' => $price,
    'oldPrice' => $oldPrice,
    'badge' => $badge,
    'color' => $color,
    'stock' => $stock,
    'description' => $description,
    'image' => $imageResult['path'],
    'featured' => $featured,
];

try {
    $savedId = admin_save_product_db($pdo, $payload);
    set_flash('success', $id > 0 ? 'Produit modifié en BDD avec succès.' : ('Produit ajouté en BDD avec succès. ID : ' . $savedId));
} catch (Throwable $e) {
    set_flash('error', 'Erreur pendant la sauvegarde en BDD : ' . $e->getMessage());
}

header('Location: products.php');
exit;
?>