<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/shop_bootstrap.php';

require_login();

$user = current_user();
$orderId = (int) ($_GET['id'] ?? 0);

if ($orderId <= 0) {
    set_flash('error', 'Commande introuvable.');
    header('Location: account-orders.php');
    exit;
}

$orderStmt = $pdo->prepare('
    SELECT o.*, 
           sa.recipient_name AS shipping_recipient_name,
           sa.address_line_1 AS shipping_address_line_1,
           sa.address_line_2 AS shipping_address_line_2,
           sa.postal_code AS shipping_postal_code,
           sa.city AS shipping_city,
           sa.country AS shipping_country,
           ba.recipient_name AS billing_recipient_name,
           ba.address_line_1 AS billing_address_line_1,
           ba.address_line_2 AS billing_address_line_2,
           ba.postal_code AS billing_postal_code,
           ba.city AS billing_city,
           ba.country AS billing_country
    FROM orders o
    LEFT JOIN user_addresses sa ON sa.id = o.shipping_address_id
    LEFT JOIN user_addresses ba ON ba.id = o.billing_address_id
    WHERE o.id = :id AND o.user_id = :user_id
    LIMIT 1
');
$orderStmt->execute([
    'id' => $orderId,
    'user_id' => (int) $user['id'],
]);
$order = $orderStmt->fetch();

if (!$order) {
    set_flash('error', 'Commande introuvable.');
    header('Location: account-orders.php');
    exit;
}

$itemStmt = $pdo->prepare('
    SELECT *
    FROM order_items
    WHERE order_id = :order_id
    ORDER BY id ASC
');
$itemStmt->execute(['order_id' => $orderId]);
$items = $itemStmt->fetchAll();

$pageTitle = 'Détail commande - Chacha';
require_once __DIR__ . '/includes/header.php';
?>

<section class="card">
    <span class="muted">Commande</span>
    <h1><?= htmlspecialchars($order['order_number']) ?></h1>

    <div class="grid grid-2">
        <div>
            <p><strong>Date :</strong> <?= htmlspecialchars($order['created_at']) ?></p>
            <p><strong>Statut commande :</strong> <?= htmlspecialchars($order['order_status']) ?></p>
            <p><strong>Statut paiement :</strong> <?= htmlspecialchars($order['payment_status']) ?></p>
            <p><strong>Mode de paiement :</strong> <?= htmlspecialchars((string) $order['payment_method']) ?></p>
            <p><strong>Mode de livraison :</strong> <?= htmlspecialchars((string) $order['shipping_method']) ?></p>
            <p><strong>Email :</strong> <?= htmlspecialchars((string) $order['customer_email']) ?></p>
            <p><strong>Téléphone :</strong> <?= htmlspecialchars((string) $order['customer_phone']) ?></p>
        </div>
        <div>
            <div class="kpi">
                <div class="muted">Adresse de livraison</div>
                <strong><?= htmlspecialchars((string) $order['shipping_recipient_name']) ?></strong><br>
                <?= htmlspecialchars((string) $order['shipping_address_line_1']) ?><br>
                <?php if (!empty($order['shipping_address_line_2'])): ?><?= htmlspecialchars((string) $order['shipping_address_line_2']) ?><br><?php endif; ?>
                <?= htmlspecialchars((string) $order['shipping_postal_code']) ?> <?= htmlspecialchars((string) $order['shipping_city']) ?><br>
                <?= htmlspecialchars((string) $order['shipping_country']) ?>
            </div>

            <div class="kpi" style="margin-top:12px;">
                <div class="muted">Adresse de facturation</div>
                <strong><?= htmlspecialchars((string) $order['billing_recipient_name']) ?></strong><br>
                <?= htmlspecialchars((string) $order['billing_address_line_1']) ?><br>
                <?php if (!empty($order['billing_address_line_2'])): ?><?= htmlspecialchars((string) $order['billing_address_line_2']) ?><br><?php endif; ?>
                <?= htmlspecialchars((string) $order['billing_postal_code']) ?> <?= htmlspecialchars((string) $order['billing_city']) ?><br>
                <?= htmlspecialchars((string) $order['billing_country']) ?>
            </div>
        </div>
    </div>

    <div style="display:grid; gap:12px; margin-top:20px;">
        <?php foreach ($items as $item): ?>
            <div class="card" style="padding:18px;">
                <strong><?= htmlspecialchars($item['product_name']) ?></strong><br>
                <span class="muted">SKU : <?= htmlspecialchars($item['product_sku']) ?></span><br>
                <span class="muted">Taille : <?= htmlspecialchars((string) $item['size_label']) ?></span><br>
                <span class="muted">Couleur : <?= htmlspecialchars((string) $item['color_label']) ?></span><br>
                <span class="muted">Quantité : <?= (int) $item['quantity'] ?></span><br>
                <strong>Total ligne : <?= format_eur((float) $item['line_total']) ?></strong>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="margin-top:20px;">
        <p><strong>Sous-total :</strong> <?= format_eur((float) $order['subtotal']) ?></p>
        <p><strong>Livraison :</strong> <?= format_eur((float) $order['shipping_amount']) ?></p>
        <p style="font-size:20px;"><strong>Total :</strong> <?= format_eur((float) $order['total_amount']) ?></p>
        <?php if (!empty($order['notes'])): ?>
            <p><strong>Notes :</strong> <?= htmlspecialchars((string) $order['notes']) ?></p>
        <?php endif; ?>
    </div>

    <div class="actions">
        <a class="btn btn-light" href="account-orders.php">Retour à mes commandes</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>