<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/shop_bootstrap.php';
require_once __DIR__ . '/../includes/admin_auth.php';

require_admin();

$orderId = (int) ($_POST['order_id'] ?? 0);
$orderStatus = trim((string) ($_POST['order_status'] ?? 'pending'));
$paymentStatus = trim((string) ($_POST['payment_status'] ?? 'pending'));

$allowedOrderStatuses = ['pending','confirmed','preparing','shipped','delivered','cancelled'];
$allowedPaymentStatuses = ['pending','paid','failed','refunded'];

if ($orderId <= 0 || !in_array($orderStatus, $allowedOrderStatuses, true) || !in_array($paymentStatus, $allowedPaymentStatuses, true)) {
    set_flash('error', 'Données de mise à jour invalides.');
    header('Location: orders.php');
    exit;
}

$stmt = $pdo->prepare("
    UPDATE orders
    SET order_status = :order_status,
        payment_status = :payment_status,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = :id
");
$stmt->execute([
    'order_status' => $orderStatus,
    'payment_status' => $paymentStatus,
    'id' => $orderId,
]);

header('Location: order-view.php?id=' . $orderId);
exit;
?>