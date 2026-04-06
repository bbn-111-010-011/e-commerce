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
if ($id <= 0) {
    set_flash('error', 'Produit introuvable.');
    header('Location: products.php');
    exit;
}

try {
    admin_delete_product_db($pdo, $id);
    set_flash('success', 'Produit supprimé en BDD avec succès.');
} catch (Throwable $e) {
    set_flash('error', 'Erreur pendant la suppression en BDD : ' . $e->getMessage());
}

header('Location: products.php');
exit;
?>