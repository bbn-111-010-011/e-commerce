<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
require_login();

$orderId = (int) ($_SESSION['last_order_id'] ?? 0);
$orderNumber = (string) ($_SESSION['last_order_number'] ?? '');

$order = null;
if ($orderId > 0) {
    $stmt = $pdo->prepare('SELECT order_number, payment_method, payment_status, shipping_method, total_amount FROM orders WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $orderId]);
    $order = $stmt->fetch();
}

$pageTitle = 'Commande confirmée - Chacha';
require_once __DIR__ . '/includes/header.php';
?>

<section class="card" style="max-width: 860px; margin: 0 auto;">
    <span class="muted">Commande enregistrée</span>
    <h1>Merci pour votre commande</h1>

    <?php if ($orderNumber !== ''): ?>
        <div class="alert alert-success">
            Votre commande <strong><?= htmlspecialchars($orderNumber) ?></strong> a bien été enregistrée.
        </div>
    <?php endif; ?>

    <?php if ($order): ?>
        <div class="grid grid-2">
            <div class="kpi">
                <div class="muted">Mode de paiement</div>
                <strong><?= htmlspecialchars((string) $order['payment_method']) ?></strong>
            </div>
            <div class="kpi">
                <div class="muted">Statut paiement</div>
                <strong><?= htmlspecialchars((string) $order['payment_status']) ?></strong>
            </div>
            <div class="kpi">
                <div class="muted">Livraison</div>
                <strong><?= htmlspecialchars((string) $order['shipping_method']) ?></strong>
            </div>
            <div class="kpi">
                <div class="muted">Total</div>
                <strong><?= htmlspecialchars(number_format((float) $order['total_amount'], 2, ',', ' ')) ?> €</strong>
            </div>
        </div>

        <div class="alert alert-success" style="margin-top:18px;">
            <?php if (($order['payment_method'] ?? '') === 'Virement bancaire'): ?>
                Merci d’effectuer votre virement en indiquant votre numéro de commande.
            <?php elseif (($order['payment_method'] ?? '') === 'Paiement à la livraison'): ?>
                Le règlement se fera lors de la livraison.
            <?php else: ?>
                Votre commande est enregistrée. Le paiement sera confirmé ultérieurement.
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="actions">
        <a class="btn btn-dark" href="account-orders.php">Voir mes commandes</a>
        <a class="btn btn-light" href="shop.php">Retour boutique</a>
    </div>
</section>

<?php
unset($_SESSION['last_order_id'], $_SESSION['last_order_number']);
require_once __DIR__ . '/includes/footer.php';
?>