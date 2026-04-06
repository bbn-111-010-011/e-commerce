<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/admin_header.php';

$stmt = $pdo->query("
    SELECT id, order_number, customer_email, customer_phone, total_amount, payment_method, shipping_method, payment_status, order_status, created_at
    FROM orders
    ORDER BY id DESC
");
$orders = $stmt->fetchAll();
?>

<section class="card">
    <span class="muted">Commandes</span>
    <h1>Gestion des commandes</h1>

    <?php if (!$orders): ?>
        <p>Aucune commande enregistrée.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Commande</th>
                    <th>Client</th>
                    <th>Téléphone</th>
                    <th>Total</th>
                    <th>Paiement</th>
                    <th>Livraison</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['order_number']) ?></td>
                        <td><?= htmlspecialchars($order['customer_email']) ?></td>
                        <td><?= htmlspecialchars((string) $order['customer_phone']) ?></td>
                        <td><?= format_eur((float) $order['total_amount']) ?></td>
                        <td><?= htmlspecialchars((string) $order['payment_method']) ?> / <?= htmlspecialchars((string) $order['payment_status']) ?></td>
                        <td><?= htmlspecialchars((string) $order['shipping_method']) ?></td>
                        <td><?= htmlspecialchars($order['order_status']) ?></td>
                        <td><?= htmlspecialchars($order['created_at']) ?></td>
                        <td><a class="btn btn-light" href="order-view.php?id=<?= (int) $order['id'] ?>">Ouvrir</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>