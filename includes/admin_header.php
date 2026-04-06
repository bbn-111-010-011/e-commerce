<?php
declare(strict_types=1);

require_once __DIR__ . '/shop_bootstrap.php';
require_once __DIR__ . '/admin_auth.php';

require_admin();
$user = current_user();
$pageTitle = $pageTitle ?? 'Admin Chacha';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .admin-shell { display:grid; grid-template-columns:280px 1fr; min-height:100vh; }
        .admin-sidebar { background:#111; color:#fff; padding:24px; }
        .admin-sidebar a { display:block; padding:12px 14px; border-radius:12px; margin-bottom:8px; background:rgba(255,255,255,.04); }
        .admin-content { padding:28px; background:#f8f6f3; }
        .admin-brand { font-size:28px; font-weight:800; margin-bottom:20px; display:block; }
        .admin-kpis { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; }
        .admin-table { width:100%; border-collapse:collapse; background:#fff; border-radius:18px; overflow:hidden; }
        .admin-table th,.admin-table td { padding:14px; border-bottom:1px solid #e8e2da; text-align:left; }
        @media (max-width: 900px) {
            .admin-shell { grid-template-columns:1fr; }
            .admin-kpis { grid-template-columns:1fr 1fr; }
        }
        @media (max-width: 650px) {
            .admin-kpis { grid-template-columns:1fr; }
        }
    </style>
</head>
<body>
<div class="admin-shell">
    <aside class="admin-sidebar">
        <a href="../shop.php" class="admin-brand">CHACHA ADMIN</a>
        <p style="opacity:.8;">Connecté : <?= htmlspecialchars($user['first_name']) ?></p>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">Produits</a>
            <a href="product-add.php">Ajouter produit</a>
            <a href="orders.php">Commandes</a>
            <a href="customers.php">Clients</a>
            <a href="../account.php">Espace client</a>
            <a href="../logout.php">Déconnexion</a>
        </nav>
    </aside>
    <main class="admin-content">
