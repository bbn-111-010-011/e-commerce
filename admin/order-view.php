<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/admin_header.php';

$orderId = (int) ($_GET['id'] ?? 0);

if ($orderId <= 0) {
    echo '<section class="card"><div class="alert alert-error">Commande introuvable.</div></section>';
    require_once __DIR__ . '/../includes/admin_footer.php';
    exit;
}

$orderStmt = $pdo->prepare("
    SELECT o.*,
           u.first_name,
           u.last_name,
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
    LEFT JOIN users u ON u.id = o.user_id
    LEFT JOIN user_addresses sa ON sa.id = o.shipping_address_id
    LEFT JOIN user_addresses ba ON ba.id = o.billing_address_id
    WHERE o.id = :id
    LIMIT 1
");
$orderStmt->execute(['id' => $orderId]);
$order = $orderStmt->fetch();

$itemStmt = $pdo->prepare("
    SELECT *
    FROM order_items
    WHERE order_id = :order_id
    ORDER BY id ASC
");
$itemStmt->execute(['order_id' => $orderId]);
$items = $itemStmt->fetchAll();
?>

<section class="card">
    <?php if (!$order): ?>
        <div class="alert alert-error">Commande introuvable.</div>
    <?php else: ?>
        <span class="muted">Commande admin V6.1</span>
        <h1><?= htmlspecialchars($order['order_number']) ?></h1>

        <div class="grid grid-2">
            <div>
                <p><strong>Client :</strong> <?= htmlspecialchars(trim((string) ($order['first_name'] . ' ' . $order['last_name']))) ?></p>
                <p><strong>Email :</strong> <?= htmlspecialchars($order['customer_email']) ?></p>
                <p><strong>Téléphone :</strong> <?= htmlspecialchars((string) $order['customer_phone']) ?></p>
                <p><strong>Statut :</strong> <?= htmlspecialchars($order['order_status']) ?></p>
                <p><strong>Paiement :</strong> <?= htmlspecialchars($order['payment_status']) ?></p>
                <p><strong>Mode paiement :</strong> <?= htmlspecialchars((string) $order['payment_method']) ?></p>
                <p><strong>Mode livraison :</strong> <?= htmlspecialchars((string) $order['shipping_method']) ?></p>
                <?php if (!empty($order['notes'])): ?>
                    <p><strong>Notes client :</strong> <?= htmlspecialchars((string) $order['notes']) ?></p>
                <?php endif; ?>
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

        <form action="order-update-status.php" method="post" class="actions" style="margin:20px 0;">
            <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
            <select class="select" name="order_status">
                <?php foreach (['pending','confirmed','preparing','shipped','delivered','cancelled'] as $status): ?>
                    <option value="<?= $status ?>" <?= $status === $order['order_status'] ? 'selected' : '' ?>><?= $status ?></option>
                <?php endforeach; ?>
            </select>

            <select class="select" name="payment_status">
                <?php foreach (['pending','paid','failed','refunded'] as $status): ?>
                    <option value="<?= $status ?>" <?= $status === $order['payment_status'] ? 'selected' : '' ?>><?= $status ?></option>
                <?php endforeach; ?>
            </select>

            <button class="btn btn-dark" type="submit">Mettre à jour</button>
        </form>

        <h2>Articles commandés</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Taille</th>
                    <th>Couleur</th>
                    <th>Qté</th>
                    <th>Prix</th>
                    <th>Total ligne</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= htmlspecialchars((string) $item['size_label']) ?></td>
                        <td><?= htmlspecialchars((string) $item['color_label']) ?></td>
                        <td><?= (int) $item['quantity'] ?></td>
                        <td><?= format_eur((float) $item['unit_price']) ?></td>
                        <td><?= format_eur((float) $item['line_total']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top:18px;">
            <p><strong>Sous-total :</strong> <?= format_eur((float) $order['subtotal']) ?></p>
            <p><strong>Livraison :</strong> <?= format_eur((float) $order['shipping_amount']) ?></p>
            <p style="font-size:20px;"><strong>Total :</strong> <?= format_eur((float) $order['total_amount']) ?></p>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>