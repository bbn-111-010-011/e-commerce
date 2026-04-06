<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/shop_bootstrap.php';
require_login();

$productId = (int) ($_POST['product_id'] ?? 0);
$size = trim((string) ($_POST['size'] ?? 'M'));

if ($productId > 0) {
    cart_remove_item($productId, $size !== '' ? $size : 'M');
    set_flash('success', 'Article retiré du panier.');
}

header('Location: ../panier.php');
exit;
?>