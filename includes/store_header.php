<?php
declare(strict_types=1);

require_once __DIR__ . '/shop_bootstrap.php';

$user = current_user();

if ($user && ($user['role'] ?? '') === 'admin') {
    header('Location: admin/dashboard.php');
    exit;
}

$favoritesIds = $user ? favorite_ids($pdo, (int) $user['id']) : [];
$cartCount = $user ? cart_count() : 0;
$favoritesCount = $user ? count($favoritesIds) : 0;
$pageTitle = $pageTitle ?? 'Chacha Boutique';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<script>
window.CHACHA_IS_LOGGED_IN = <?= $user ? 'true' : 'false' ?>;
window.CHACHA_FAVORITES = <?= json_encode(array_values($favoritesIds), JSON_UNESCAPED_UNICODE) ?>;
window.CHACHA_CART_COUNT = <?= (int) $cartCount ?>;
try { localStorage.removeItem('chacha_cart'); } catch (e) {}
</script>

<div class="topbar">
  <div class="container">
    <div>06 14 36 49 62</div>
    <div>Chacha V4.3 — admin redirigé directement vers le dashboard</div>
  </div>
</div>

<header class="header">
  <div class="container">
    <a class="logo" href="shop.php"><span>Boutique mode</span><strong>CHACHA</strong></a>
    <nav class="nav">
      <a href="shop.php">Accueil</a>
      <a href="catalogue.php">Catalogue</a>
      <a href="favorites.php">Favoris<?= $user ? ' (' . $favoritesCount . ')' : '' ?></a>
      <a href="contact.php">Contact</a>
      <?php if (!$user): ?>
        <a href="login.php">Connexion</a>
        <a href="register.php">Créer un compte</a>
      <?php else: ?>
        <a href="account.php">Mon compte</a>
        <a href="logout.php">Déconnexion</a>
      <?php endif; ?>
    </nav>
    <div class="actions">
      <?php if (!$user): ?>
        <a class="btn btn-light" href="login.php">Connexion</a>
        <a class="btn btn-light" href="register.php">Inscription</a>
      <?php else: ?>
        <a class="btn btn-light" href="favorites.php">Favoris (<?= $favoritesCount ?>)</a>
        <a class="btn btn-light" href="account.php">Mon compte</a>
        <a class="btn btn-dark" href="panier.php">Panier (<span data-cart-count><?= $cartCount ?></span>)</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<main>
