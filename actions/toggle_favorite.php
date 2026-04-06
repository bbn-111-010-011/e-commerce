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
$isAjax = isset($_POST['ajax']);

if (!is_logged_in()) {
    if ($isAjax) {
        json_response([
            'success' => false,
            'redirect' => 'login.php?redirect=favorites.php',
            'message' => 'Connexion requise',
        ]);
    }

    set_flash('error', 'Veuillez vous connecter ou créer un compte pour utiliser les favoris.');
    header('Location: ../login.php?redirect=favorites.php');
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

try {
    $isFavorite = toggle_favorite($pdo, (int) current_user()['id'], $productId);
} catch (Throwable $e) {
    if ($isAjax) {
        json_response(['success' => false, 'message' => $e->getMessage()]);
    }
    set_flash('error', $e->getMessage());
    header('Location: ../catalogue.php');
    exit;
}

if ($isAjax) {
    json_response([
        'success' => true,
        'is_favorite' => $isFavorite,
        'favorite_ids' => favorite_ids($pdo, (int) current_user()['id']),
        'message' => $isFavorite ? 'Produit ajouté aux favoris' : 'Produit retiré des favoris',
        'refresh' => strpos((string) ($_SERVER['HTTP_REFERER'] ?? ''), 'favorites.php') !== false,
    ]);
}

header('Location: ../favorites.php');
exit;
?>