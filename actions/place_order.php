<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/shop_bootstrap.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/order_helpers.php';

function table_has_column(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = :table_name
          AND COLUMN_NAME = :column_name
    ");
    $stmt->execute([
        'table_name' => $table,
        'column_name' => $column,
    ]);
    return (int) $stmt->fetchColumn() > 0;
}

function build_insert_sql(string $table, array $data): array
{
    $columns = array_keys($data);
    $placeholders = array_map(fn($c) => ':' . $c, $columns);
    return [
        "INSERT INTO {$table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")",
        $data
    ];
}

require_login();

if (function_exists('verify_csrf_or_abort')) {
    verify_csrf_or_abort();
} elseif (function_exists('csrf_verify_or_abort')) {
    csrf_verify_or_abort();
} elseif (function_exists('verify_csrf')) {
    verify_csrf();
} elseif (function_exists('csrf_verify')) {
    csrf_verify();
} elseif (function_exists('check_csrf_or_abort')) {
    check_csrf_or_abort();
} elseif (function_exists('check_csrf')) {
    check_csrf();
}

require_cart_not_empty();

$user = current_user();
$cart = cart_total_detailed();

$firstName = trim((string) ($_POST['first_name'] ?? ''));
$lastName = trim((string) ($_POST['last_name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$recipientName = trim((string) ($_POST['recipient_name'] ?? ''));
$addressLine1 = trim((string) ($_POST['address_line_1'] ?? ''));
$addressLine2 = trim((string) ($_POST['address_line_2'] ?? ''));
$postalCode = trim((string) ($_POST['postal_code'] ?? ''));
$city = trim((string) ($_POST['city'] ?? ''));
$country = trim((string) ($_POST['country'] ?? 'France'));
$billingSame = isset($_POST['billing_same_as_shipping']) && (string) $_POST['billing_same_as_shipping'] === '1';
$billingRecipientName = trim((string) ($_POST['billing_recipient_name'] ?? ''));
$billingAddressLine1 = trim((string) ($_POST['billing_address_line_1'] ?? ''));
$billingAddressLine2 = trim((string) ($_POST['billing_address_line_2'] ?? ''));
$billingPostalCode = trim((string) ($_POST['billing_postal_code'] ?? ''));
$billingCity = trim((string) ($_POST['billing_city'] ?? ''));
$billingCountry = trim((string) ($_POST['billing_country'] ?? $country));
$shippingMethod = trim((string) ($_POST['shipping_method'] ?? 'standard'));
$paymentMethod = trim((string) ($_POST['payment_method'] ?? 'cash_on_delivery'));
$notes = trim((string) ($_POST['notes'] ?? ''));

if (
    $firstName === '' || $lastName === '' || $email === '' || $phone === '' ||
    $recipientName === '' || $addressLine1 === '' || $postalCode === '' || $city === ''
) {
    set_flash('error', 'Veuillez remplir tous les champs obligatoires.');
    header('Location: ../checkout.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash('error', 'Adresse email invalide.');
    header('Location: ../checkout.php');
    exit;
}

if ($billingSame) {
    $billingRecipientName = $recipientName;
    $billingAddressLine1 = $addressLine1;
    $billingAddressLine2 = $addressLine2;
    $billingPostalCode = $postalCode;
    $billingCity = $city;
    $billingCountry = $country;
}

if (
    $billingRecipientName === '' || $billingAddressLine1 === '' ||
    $billingPostalCode === '' || $billingCity === ''
) {
    set_flash('error', 'Veuillez compléter l’adresse de facturation.');
    header('Location: ../checkout.php');
    exit;
}

$shippingAmount = $shippingMethod === 'express' ? 9.90 : 0.00;
$subtotal = (float) ($cart['subtotal'] ?? 0);
$totalAmount = $subtotal + $shippingAmount;

try {
    $pdo->beginTransaction();

    foreach ($cart['items'] as $item) {
        $productId = (int) ($item['product_id'] ?? 0);
        $quantity = (int) ($item['quantity'] ?? 0);
        $product = shop_find_product($productId);

        if (!$product) {
            throw new RuntimeException('Un produit du panier est introuvable.');
        }

        $currentStock = (int) ($product['stock'] ?? 0);
        if ($currentStock < $quantity) {
            throw new RuntimeException('Stock insuffisant pour : ' . (string) $product['name']);
        }
    }

    $orderNumber = next_order_number($pdo);

    $orderData = [];

    $map = [
        'user_id' => (int) $user['id'],
        'order_number' => $orderNumber,
        'order_status' => 'pending',
        'payment_status' => 'pending',
        'shipping_status' => 'preparing',
        'customer_first_name' => $firstName,
        'customer_last_name' => $lastName,
        'customer_email' => $email,
        'customer_phone' => $phone,
        'shipping_method' => $shippingMethod,
        'payment_method' => $paymentMethod,
        'subtotal_amount' => $subtotal,
        'shipping_amount' => $shippingAmount,
        'total_amount' => $totalAmount,
        'notes' => $notes !== '' ? $notes : null,
    ];

    foreach ($map as $column => $value) {
        if (table_has_column($pdo, 'orders', $column)) {
            $orderData[$column] = $value;
        }
    }

    if (table_has_column($pdo, 'orders', 'created_at')) {
        $orderData['created_at'] = date('Y-m-d H:i:s');
    }
    if (table_has_column($pdo, 'orders', 'updated_at')) {
        $orderData['updated_at'] = date('Y-m-d H:i:s');
    }

    if (!$orderData) {
        throw new RuntimeException('La table orders n\'a pas les colonnes attendues.');
    }

    [$sql, $params] = build_insert_sql('orders', $orderData);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orderId = (int) $pdo->lastInsertId();

    if ((bool) $pdo->query("SHOW TABLES LIKE 'order_addresses'")->fetchColumn()) {
        $shippingAddress = [
            'order_id' => $orderId,
            'type' => 'shipping',
            'recipient_name' => $recipientName,
            'address_line_1' => $addressLine1,
            'address_line_2' => $addressLine2 !== '' ? $addressLine2 : null,
            'postal_code' => $postalCode,
            'city' => $city,
            'country' => $country,
        ];
        $billingAddress = [
            'order_id' => $orderId,
            'type' => 'billing',
            'recipient_name' => $billingRecipientName,
            'address_line_1' => $billingAddressLine1,
            'address_line_2' => $billingAddressLine2 !== '' ? $billingAddressLine2 : null,
            'postal_code' => $billingPostalCode,
            'city' => $billingCity,
            'country' => $billingCountry,
        ];

        foreach ([$shippingAddress, $billingAddress] as $address) {
            $data = [];
            foreach ($address as $column => $value) {
                if (table_has_column($pdo, 'order_addresses', $column)) {
                    $data[$column] = $value;
                }
            }
            if ($data) {
                [$sql, $params] = build_insert_sql('order_addresses', $data);
                $addressStmt = $pdo->prepare($sql);
                $addressStmt->execute($params);
            }
        }
    }

    if ((bool) $pdo->query("SHOW TABLES LIKE 'order_items'")->fetchColumn()) {
        $stockStmt = $pdo->prepare('UPDATE products SET stock = GREATEST(stock - :qty, 0) WHERE id = :product_id');

        foreach ($cart['items'] as $item) {
            $itemMap = [
                'order_id' => $orderId,
                'product_id' => (int) ($item['product_id'] ?? 0),
                'product_name' => (string) ($item['product_name'] ?? ''),
                'product_sku' => (string) ($item['product_sku'] ?? ''),
                'size_label' => (string) ($item['size_label'] ?? 'M'),
                'color_label' => $item['color_label'] !== null ? (string) $item['color_label'] : null,
                'quantity' => (int) ($item['quantity'] ?? 1),
                'unit_price' => (float) ($item['unit_price'] ?? 0),
                'line_total' => (float) ($item['line_total'] ?? 0),
            ];

            $itemData = [];
            foreach ($itemMap as $column => $value) {
                if (table_has_column($pdo, 'order_items', $column)) {
                    $itemData[$column] = $value;
                }
            }
            if ($itemData) {
                [$sql, $params] = build_insert_sql('order_items', $itemData);
                $itemStmt = $pdo->prepare($sql);
                $itemStmt->execute($params);
            }

            $stockStmt->execute([
                'qty' => (int) ($item['quantity'] ?? 1),
                'product_id' => (int) ($item['product_id'] ?? 0),
            ]);
        }
    } else {
        // Even if order_items table does not exist, still decrement stock after successful order insert
        $stockStmt = $pdo->prepare('UPDATE products SET stock = GREATEST(stock - :qty, 0) WHERE id = :product_id');
        foreach ($cart['items'] as $item) {
            $stockStmt->execute([
                'qty' => (int) ($item['quantity'] ?? 1),
                'product_id' => (int) ($item['product_id'] ?? 0),
            ]);
        }
    }

    if (is_logged_in()) {
        $cartId = active_cart_id_for_user($pdo, (int) $user['id']);
        $pdo->prepare('DELETE FROM cart_items WHERE cart_id = :cart_id')->execute(['cart_id' => $cartId]);
    }
    $_SESSION['chacha_cart'] = [];
    $_SESSION['last_order_id'] = $orderId;

    $pdo->commit();

    set_flash('success', 'Commande validée avec succès.');
    header('Location: ../order-success.php');
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    set_flash('error', 'Impossible de valider la commande : ' . $e->getMessage());
    header('Location: ../checkout.php');
    exit;
}
?>