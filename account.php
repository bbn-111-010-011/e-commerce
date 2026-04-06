<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_login();

$user = current_user();

if (($user['role'] ?? '') === 'admin') {
    header('Location: admin/dashboard.php');
    exit;
}

$pageTitle = 'Mon compte - Chacha';
require_once __DIR__ . '/includes/header.php';
?>

<section class="card">
    <span class="muted">Espace client sécurisé</span>
    <h1>Bonjour <?= htmlspecialchars($user['first_name']) ?></h1>
    <p>Bienvenue dans votre espace client Chacha V7.0.</p>

    <div class="kpis">
        <div class="kpi">
            <div class="muted">Nom complet</div>
            <strong><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></strong>
        </div>
        <div class="kpi">
            <div class="muted">Email</div>
            <strong><?= htmlspecialchars($user['email']) ?></strong>
        </div>
        <div class="kpi">
            <div class="muted">Téléphone</div>
            <strong><?= htmlspecialchars((string) ($user['phone'] ?? 'Non renseigné')) ?></strong>
        </div>
        <div class="kpi">
            <div class="muted">Rôle</div>
            <strong><?= htmlspecialchars($user['role']) ?></strong>
        </div>
    </div>

    <div class="actions" style="margin-top:20px;">
        <a class="btn btn-dark" href="account-orders.php">Mes commandes</a>
        <a class="btn btn-light" href="account-profile.php">Mes coordonnées</a>
        <a class="btn btn-light" href="account-addresses.php">Mes adresses</a>
        <a class="btn btn-light" href="favorites.php">Mes favoris</a>
        <a class="btn btn-light" href="account-password.php">Mot de passe</a>
        <a class="btn btn-light" href="logout.php">Déconnexion</a>
    </div>
</section>

<section class="card" style="margin-top:24px;">
    <span class="muted">Tunnel de commande</span>
    <h2>Ce que V7.0 ajoute</h2>
    <ul class="list">
        <li>mise à jour des coordonnées client</li>
        <li>checkout plus complet avec adresses livraison / facturation</li>
        <li>modes de livraison et paiement concrets</li>
        <li>détail commande enrichi côté client et côté admin</li>
    </ul>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>