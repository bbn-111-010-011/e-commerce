<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/admin_header.php';

$totalProducts = count(shop_products());

$userCountStmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'client'");
$totalCustomers = (int) $userCountStmt->fetchColumn();

$orderCountStmt = $pdo->query("SELECT COUNT(*) FROM orders");
$totalOrders = (int) $orderCountStmt->fetchColumn();

$pendingStmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'pending'");
$pendingOrders = (int) $pendingStmt->fetchColumn();

$recentStmt = $pdo->query("
    SELECT order_number, customer_email, total_amount, order_status, created_at
    FROM orders
    ORDER BY id DESC
    LIMIT 8
");
$recentOrders = $recentStmt->fetchAll();
?>

<section class="card">
    <span class="muted">Administration</span>
    <h1>Dashboard admin</h1>
    <div class="admin-kpis">
        <div class="kpi"><div class="muted">Produits</div><strong><?= $totalProducts ?></strong></div>
        <div class="kpi"><div class="muted">Clients</div><strong><?= $totalCustomers ?></strong></div>
        <div class="kpi"><div class="muted">Commandes</div><strong><?= $totalOrders ?></strong></div>
        <div class="kpi"><div class="muted">En attente</div><strong><?= $pendingOrders ?></strong></div>
    </div>
</section>

<section class="card" style="margin-top:24px;">
    <span class="muted">Commandes récentes</span>
    <h2>Dernières commandes</h2>

    <?php if (!$recentOrders): ?>
        <p>Aucune commande pour le moment.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Commande</th>
                    <th>Client</th>
                    <th>Total</th>
                    <th>Statut</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentOrders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['order_number']) ?></td>
                        <td><?= htmlspecialchars($order['customer_email']) ?></td>
                        <td><?= format_eur((float) $order['total_amount']) ?></td>
                        <td><?= htmlspecialchars($order['order_status']) ?></td>
                        <td><?= htmlspecialchars($order['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>