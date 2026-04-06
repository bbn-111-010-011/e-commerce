<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

require_login();
$user = current_user();
$pageTitle = 'Mes adresses - Chacha';

$stmt = $pdo->prepare("
    SELECT *
    FROM user_addresses
    WHERE user_id = :user_id
    ORDER BY id DESC
");
$stmt->execute(['user_id' => (int) $user['id']]);
$addresses = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<section class="card">
    <span class="muted">Espace client</span>
    <h1>Mes adresses</h1>
    <p class="muted">Toutes les adresses utilisées dans vos commandes récentes apparaissent ici.</p>

    <?php if (!$addresses): ?>
        <div class="alert alert-error">Aucune adresse enregistrée pour le moment.</div>
    <?php else: ?>
        <div style="display:grid; gap:16px;">
            <?php foreach ($addresses as $address): ?>
                <div class="card" style="padding:18px;">
                    <div class="small"><?= htmlspecialchars((string) ($address['label'] ?? 'Adresse')) ?></div>
                    <strong><?= htmlspecialchars($address['recipient_name']) ?></strong><br>
                    <?= htmlspecialchars($address['address_line_1']) ?><br>
                    <?php if (!empty($address['address_line_2'])): ?>
                        <?= htmlspecialchars($address['address_line_2']) ?><br>
                    <?php endif; ?>
                    <?= htmlspecialchars($address['postal_code']) ?> <?= htmlspecialchars($address['city']) ?><br>
                    <?= htmlspecialchars($address['country']) ?><br>
                    <span class="muted"><?= htmlspecialchars((string) $address['phone']) ?></span><br>
                    <span class="muted">
                        Facturation par défaut : <?= (int) ($address['is_default_billing'] ?? 0) === 1 ? 'Oui' : 'Non' ?> |
                        Livraison par défaut : <?= (int) ($address['is_default_shipping'] ?? 0) === 1 ? 'Oui' : 'Non' ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="actions" style="margin-top:18px;">
        <a class="btn btn-light" href="account.php">Retour compte</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>