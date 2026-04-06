<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

require_login();

$user = current_user();
$pageTitle = 'Mes commandes - Chacha';

$stmt = $pdo->prepare('
    SELECT id, order_number, subtotal, shipping_amount, total_amount,
           payment_status, order_status, payment_method, shipping_method, created_at
    FROM orders
    WHERE user_id = :user_id
    ORDER BY id DESC
');
$stmt->execute(['user_id' => (int) $user['id']]);
$orders = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<section class="card">
    <span class="muted">Historique client</span>
    <h1>Mes commandes</h1>

    <?php if (!$orders): ?>
        <div class="alert alert-error">Aucune commande enregistrée pour le moment.</div>
        <div class="actions">
            <a class="btn btn-dark" href="shop.php">Retour boutique</a>
        </div>
    <?php else: ?>
        <div style="display:grid; gap:16px;">
            <?php foreach ($orders as $order): ?>
                <div class="card" style="padding:18px;">
                    <strong><?= htmlspecialchars($order['order_number']) ?></strong><br>
                    <span class="muted">Date : <?= htmlspecialchars($order['created_at']) ?></span><br>
                    <span class="muted">Statut commande : <?= htmlspecialchars($order['order_status']) ?></span><br>
                    <span class="muted">Statut paiement : <?= htmlspecialchars($order['payment_status']) ?></span><br>
                    <span class="muted">Paiement : <?= htmlspecialchars((string) $order['payment_method']) ?></span><br>
                    <span class="muted">Livraison : <?= htmlspecialchars((string) $order['shipping_method']) ?></span><br>
                    <strong>Total : <?= format_eur((float) $order['total_amount']) ?></strong><br>

                    <div class="actions" style="margin-top:12px;">
                        <a class="btn btn-light" href="order-details.php?id=<?= (int) $order['id'] ?>">Voir le détail</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>