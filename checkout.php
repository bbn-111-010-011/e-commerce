<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/order_helpers.php';

require_login();
require_cart_not_empty();

$user = current_user();
$cart = cart_total_detailed();
$prefill = build_checkout_prefill($pdo, $user);
$pageTitle = 'Validation de commande - Chacha';
$error = get_flash('error');
$success = get_flash('success');

require_once __DIR__ . '/includes/header.php';
?>

<section class="card">
    <span class="muted">Checkout V7.0</span>
    <h1>Finaliser ma commande</h1>
    <p class="muted">Complétez vos coordonnées, choisissez la livraison et le paiement, puis validez votre commande.</p>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form action="actions/place_order.php" method="post" novalidate>
        <?= csrf_input() ?>

        <div class="grid grid-2">
            <div class="card">
                <h2>1. Coordonnées</h2>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="first_name">Prénom</label>
                        <input id="first_name" name="first_name" type="text" value="<?= htmlspecialchars($prefill['first_name']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="last_name">Nom</label>
                        <input id="last_name" name="last_name" type="text" value="<?= htmlspecialchars($prefill['last_name']) ?>" required>
                    </div>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" value="<?= htmlspecialchars($prefill['email']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Téléphone</label>
                        <input id="phone" name="phone" type="text" value="<?= htmlspecialchars($prefill['phone']) ?>" required>
                    </div>
                </div>

                <h2 style="margin-top:24px;">2. Adresse de livraison</h2>

                <div class="form-group">
                    <label for="recipient_name">Nom du destinataire</label>
                    <input id="recipient_name" name="recipient_name" type="text" value="<?= htmlspecialchars($prefill['recipient_name']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="address_line_1">Adresse</label>
                    <input id="address_line_1" name="address_line_1" type="text" value="<?= htmlspecialchars($prefill['address_line_1']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="address_line_2">Complément d'adresse</label>
                    <input id="address_line_2" name="address_line_2" type="text" value="<?= htmlspecialchars($prefill['address_line_2']) ?>">
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="postal_code">Code postal</label>
                        <input id="postal_code" name="postal_code" type="text" value="<?= htmlspecialchars($prefill['postal_code']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="city">Ville</label>
                        <input id="city" name="city" type="text" value="<?= htmlspecialchars($prefill['city']) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="country">Pays</label>
                    <input id="country" name="country" type="text" value="<?= htmlspecialchars($prefill['country']) ?>" required>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="billing_same_as_shipping" value="1" checked>
                        Utiliser la même adresse pour la facturation
                    </label>
                </div>

                <h2 style="margin-top:24px;">3. Adresse de facturation</h2>

                <div class="form-group">
                    <label for="billing_recipient_name">Nom du destinataire</label>
                    <input id="billing_recipient_name" name="billing_recipient_name" type="text" value="<?= htmlspecialchars($prefill['billing_recipient_name']) ?>">
                </div>

                <div class="form-group">
                    <label for="billing_address_line_1">Adresse</label>
                    <input id="billing_address_line_1" name="billing_address_line_1" type="text" value="<?= htmlspecialchars($prefill['billing_address_line_1']) ?>">
                </div>

                <div class="form-group">
                    <label for="billing_address_line_2">Complément d'adresse</label>
                    <input id="billing_address_line_2" name="billing_address_line_2" type="text" value="<?= htmlspecialchars($prefill['billing_address_line_2']) ?>">
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="billing_postal_code">Code postal</label>
                        <input id="billing_postal_code" name="billing_postal_code" type="text" value="<?= htmlspecialchars($prefill['billing_postal_code']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="billing_city">Ville</label>
                        <input id="billing_city" name="billing_city" type="text" value="<?= htmlspecialchars($prefill['billing_city']) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="billing_country">Pays</label>
                    <input id="billing_country" name="billing_country" type="text" value="<?= htmlspecialchars($prefill['billing_country']) ?>">
                </div>
            </div>

            <div class="card">
                <h2>4. Livraison et paiement</h2>

                <div class="form-group">
                    <label for="shipping_method">Mode de livraison</label>
                    <select id="shipping_method" name="shipping_method" class="select" style="width:100%;height:48px;">
                        <option value="standard">Livraison standard — 0,00 €</option>
                        <option value="express">Livraison express — 9,90 €</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="payment_method">Mode de paiement</label>
                    <select id="payment_method" name="payment_method" class="select" style="width:100%;height:48px;">
                        <option value="cash_on_delivery">Paiement à la livraison</option>
                        <option value="bank_transfer">Virement bancaire</option>
                        <option value="manual_pending">Paiement à confirmer</option>
                    </select>
                </div>

                <div class="alert alert-success">
                    Paiement à la livraison : vous réglez à réception.<br>
                    Virement bancaire : règlement après validation de commande.<br>
                    Paiement à confirmer : traitement manuel par la boutique.
                </div>

                <div class="form-group">
                    <label for="notes">Notes de commande</label>
                    <input id="notes" name="notes" type="text" placeholder="Exemple : étage, code porte, créneau préféré...">
                </div>

                <h2 style="margin-top:24px;">5. Récapitulatif</h2>

                <?php foreach ($cart['items'] as $item): ?>
                    <div style="padding: 12px 0; border-bottom: 1px solid #e8e2da;">
                        <strong><?= htmlspecialchars($item['product_name']) ?></strong><br>
                        <span class="muted">Taille : <?= htmlspecialchars($item['size_label']) ?> | Qté : <?= (int) $item['quantity'] ?></span><br>
                        <span><?= format_eur((float) $item['line_total']) ?></span>
                    </div>
                <?php endforeach; ?>

                <div style="padding-top:16px;">
                    <p><strong>Sous-total :</strong> <?= format_eur((float) $cart['subtotal']) ?></p>
                    <p><strong>Livraison :</strong> standard ou express selon votre choix</p>
                    <p><strong>Total final :</strong> calculé à la validation</p>
                </div>

                <div class="actions">
                    <button class="btn btn-dark" type="submit">Valider la commande</button>
                    <a class="btn btn-light" href="panier.php">Retour panier</a>
                </div>
            </div>
        </div>
    </form>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>