<?php
declare(strict_types=1);

require_once __DIR__ . '/shop_bootstrap.php';

function fetch_user_default_address(PDO $pdo, int $userId): ?array
{
    $stmt = $pdo->prepare('
        SELECT *
        FROM user_addresses
        WHERE user_id = :user_id
        ORDER BY is_default_shipping DESC, id DESC
        LIMIT 1
    ');
    $stmt->execute(['user_id' => $userId]);

    $row = $stmt->fetch();

    return $row ?: null;
}

function build_checkout_prefill(PDO $pdo, array $user): array
{
    $address = fetch_user_default_address($pdo, (int) $user['id']);

    return [
        'first_name' => $user['first_name'] ?? '',
        'last_name' => $user['last_name'] ?? '',
        'email' => $user['email'] ?? '',
        'phone' => $address['phone'] ?? ($user['phone'] ?? ''),
        'recipient_name' => $address['recipient_name'] ?? trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')),
        'address_line_1' => $address['address_line_1'] ?? '',
        'address_line_2' => $address['address_line_2'] ?? '',
        'postal_code' => $address['postal_code'] ?? '',
        'city' => $address['city'] ?? '',
        'country' => $address['country'] ?? 'France',
        'billing_same_as_shipping' => true,
        'billing_recipient_name' => $address['recipient_name'] ?? trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')),
        'billing_address_line_1' => $address['address_line_1'] ?? '',
        'billing_address_line_2' => $address['address_line_2'] ?? '',
        'billing_postal_code' => $address['postal_code'] ?? '',
        'billing_city' => $address['city'] ?? '',
        'billing_country' => $address['country'] ?? 'France',
        'shipping_method' => 'standard',
        'payment_method' => 'cash_on_delivery',
        'notes' => '',
    ];
}

function shipping_methods(): array
{
    return [
        'standard' => ['label' => 'Livraison standard', 'amount' => 0.00],
        'express' => ['label' => 'Livraison express', 'amount' => 9.90],
    ];
}

function payment_methods(): array
{
    return [
        'cash_on_delivery' => 'Paiement à la livraison',
        'bank_transfer' => 'Virement bancaire',
        'manual_pending' => 'Paiement à confirmer',
    ];
}

function compute_cart_totals_with_shipping(array $cart, string $shippingMethod): array
{
    $methods = shipping_methods();
    $shippingAmount = $methods[$shippingMethod]['amount'] ?? 0.00;
    $subtotal = (float) ($cart['subtotal'] ?? 0);
    $total = $subtotal + $shippingAmount;

    return [
        'subtotal' => $subtotal,
        'shipping_amount' => $shippingAmount,
        'total_amount' => $total,
        'shipping_label' => $methods[$shippingMethod]['label'] ?? 'Livraison standard',
    ];
}
?>