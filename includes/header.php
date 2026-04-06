<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
$user = current_user();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Chacha Boutique') ?></title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
    <div class="topbar">
        <div class="container topbar-inner">
            <span>06 14 36 49 62</span>
            <span>Chacha V1 — Boutique + espace client</span>
        </div>
    </div>

    <header class="site-header">
        <div class="container header-inner">
            <a href="shop.php" class="brand">
                <span class="brand-kicker">Boutique mode</span>
                <strong>CHACHA</strong>
            </a>

            <nav class="nav">
                <a href="shop.php">Boutique</a>
                <a href="catalogue.php">Catalogue</a>
                <a href="contact.php">Contact</a>
                <?php if (!$user): ?>
                    <a href="login.php">Connexion</a>
                    <a href="register.php">Créer un compte</a>
                <?php else: ?>
                    <a href="account.php">Mon compte</a>
                    <a href="logout.php">Déconnexion</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="page-shell">
        <div class="container">
