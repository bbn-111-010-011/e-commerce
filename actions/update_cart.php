<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/shop_bootstrap.php';
require_login();

$productId = (int) ($_POST['product_id'] ?? 0);
$oldSize = trim((string) ($_POST['old_size'] ?? 'M'));
$newSize = trim((string) ($_POST['new_size'] ?? 'M'));
$qty = max(1, (int) ($_POST['qty'] ?? 1));

if ($productId <= 0) {
    set_flash('error', 'Produit introuvable.');
    header('Location: ../panier.php');
    exit;
}

$product = shop_find_product($productId);
if (!$product) {
    set_flash('error', 'Produit introuvable.');
    header('Location: ../panier.php');
    exit;
}

$availableSizes = $product['sizes'] ?? ['S','M','L','XL','XXL'];
if (!in_array($newSize, $availableSizes, true)) {
    set_flash('error', 'Taille non disponible pour ce produit.');
    header('Location: ../panier.php');
    exit;
}

$stock = (int) ($product['stock'] ?? 0);
if ($stock <= 0) {
    set_flash('error', 'Produit en rupture de stock.');
    header('Location: ../panier.php');
    exit;
}

$cartItems = cart_items_from_session();
$otherQty = 0;
foreach ($cartItems as $item) {
    if ((int) ($item['productId'] ?? 0) === $productId) {
        $sameLine = ((string) ($item['size'] ?? 'M') === ($oldSize !== '' ? $oldSize : 'M'));
        if (!$sameLine) {
            $otherQty += max(1, (int) ($item['qty'] ?? 1));
        }
    }
}

if (($otherQty + $qty) > $stock) {
    set_flash('error', 'Quantité demandée supérieure au stock disponible.');
    header('Location: ../panier.php');
    exit;
}

cart_update_item($productId, $oldSize !== '' ? $oldSize : 'M', $qty, $newSize !== '' ? $newSize : 'M');
set_flash('success', 'Panier mis à jour.');
header('Location: ../panier.php');
exit;
?>