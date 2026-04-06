<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/shop_bootstrap.php';

function json_response(array $payload): void
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../catalogue.php');
    exit;
}

$productId = (int) ($_POST['product_id'] ?? 0);
$qty = max(1, (int) ($_POST['qty'] ?? 1));
$size = trim((string) ($_POST['size'] ?? ''));
$sizeQty = $_POST['size_qty'] ?? null;
$isAjax = isset($_POST['ajax']);

if (!is_logged_in()) {
    if ($isAjax) {
        json_response([
            'success' => false,
            'redirect' => 'login.php?redirect=panier.php',
            'message' => 'Veuillez vous connecter pour utiliser le panier.',
        ]);
    }

    set_flash('error', 'Veuillez vous connecter ou créer un compte pour utiliser le panier.');
    header('Location: ../login.php?redirect=panier.php');
    exit;
}

$product = shop_find_product($productId);
if (!$product) {
    if ($isAjax) {
        json_response(['success' => false, 'message' => 'Produit introuvable']);
    }
    set_flash('error', 'Produit introuvable.');
    header('Location: ../catalogue.php');
    exit;
}

$availableSizes = $product['sizes'] ?? ['S','M','L','XL','XXL'];
$stock = (int) ($product['stock'] ?? 0);

if ($stock <= 0) {
    if ($isAjax) {
        json_response(['success' => false, 'message' => 'Produit en rupture de stock.']);
    }
    set_flash('error', 'Produit en rupture de stock.');
    header('Location: ../produit.php?id=' . $productId);
    exit;
}

$selections = [];

if (is_array($sizeQty)) {
    foreach ($sizeQty as $sizeLabel => $requestedQty) {
        $sizeLabel = trim((string) $sizeLabel);
        $requestedQty = (int) $requestedQty;
        if ($requestedQty <= 0) {
            continue;
        }
        if (!in_array($sizeLabel, $availableSizes, true)) {
            if ($isAjax) {
                json_response(['success' => false, 'message' => 'Taille non disponible : ' . $sizeLabel]);
            }
            set_flash('error', 'Taille non disponible : ' . $sizeLabel);
            header('Location: ../produit.php?id=' . $productId);
            exit;
        }
        $selections[] = ['size' => $sizeLabel, 'qty' => $requestedQty];
    }
} else {
    if ($size === '') {
        if ($isAjax) {
            json_response(['success' => false, 'message' => 'Veuillez sélectionner une taille.']);
        }
        set_flash('error', 'Veuillez sélectionner une taille.');
        header('Location: ../produit.php?id=' . $productId);
        exit;
    }

    if (!in_array($size, $availableSizes, true)) {
        if ($isAjax) {
            json_response(['success' => false, 'message' => 'Taille non disponible pour ce produit.']);
        }
        set_flash('error', 'Taille non disponible pour ce produit.');
        header('Location: ../produit.php?id=' . $productId);
        exit;
    }

    $selections[] = ['size' => $size, 'qty' => $qty];
}

if (!$selections) {
    if ($isAjax) {
        json_response(['success' => false, 'message' => 'Veuillez choisir au moins une quantité.']);
    }
    set_flash('error', 'Veuillez choisir au moins une quantité.');
    header('Location: ../produit.php?id=' . $productId);
    exit;
}

$totalRequested = array_sum(array_map(fn($s) => (int) $s['qty'], $selections));
$currentQty = cart_quantity_for_product($productId);

if (($currentQty + $totalRequested) > $stock) {
    if ($isAjax) {
        json_response(['success' => false, 'message' => 'Quantité demandée supérieure au stock disponible.']);
    }
    set_flash('error', 'Quantité demandée supérieure au stock disponible.');
    header('Location: ../produit.php?id=' . $productId);
    exit;
}

foreach ($selections as $selection) {
    cart_add_item($productId, (int) $selection['qty'], (string) $selection['size']);
}

if ($isAjax) {
    json_response([
        'success' => true,
        'message' => 'Sélection ajoutée au panier',
        'cart_count' => cart_count(),
    ]);
}

header('Location: ../panier.php');
exit;
?>