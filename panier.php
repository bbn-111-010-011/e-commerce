<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/shop_bootstrap.php';

require_login();

$pageTitle = 'Mon panier - Chacha';
$error = get_flash('error');
$success = get_flash('success');
$cart = cart_total_detailed();

require_once __DIR__ . '/includes/store_header.php';
?>
<section class="container section">
  <div class="section-head">
    <div>
      <div class="small">Panier intelligent</div>
      <h1>Mon panier</h1>
      <p class="muted">Chaque taille est gérée comme une ligne indépendante.</p>
    </div>
  </div>

  <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

  <?php if (empty($cart['items'])): ?>
    <div class="panel">
      <p>Votre panier est vide.</p>
      <a class="btn btn-dark" href="catalogue.php">Voir le catalogue</a>
    </div>
  <?php else: ?>
    <div class="cart-layout-v74">
      <div class="cart-lines-v74">
        <?php foreach ($cart['items'] as $item): ?>
          <article class="panel cart-line-v74">
            <div class="cart-line-media">
              <img src="<?= htmlspecialchars((string) ($item['image'] ?? 'assets/img/products/robe-soiree.jpg')) ?>" alt="<?= htmlspecialchars((string) $item['product_name']) ?>">
            </div>

            <div class="cart-line-main">
              <div class="cart-line-top">
                <div>
                  <h3><?= htmlspecialchars((string) $item['product_name']) ?></h3>
                  <div class="small muted">SKU : <?= htmlspecialchars((string) ($item['product_sku'] ?? '—')) ?></div>
                </div>
                <div class="price"><?= format_eur((float) $item['line_total']) ?></div>
              </div>

              <form action="actions/update_cart.php" method="post" class="cart-edit-grid">
                <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>">
                <input type="hidden" name="old_size" value="<?= htmlspecialchars((string) $item['size_label']) ?>">

                <div class="form-group">
                  <label>Taille</label>
                  <select name="new_size" class="select" style="width:100%;height:44px;">
                    <?php foreach (($item['available_sizes'] ?? ['S','M','L','XL','XXL']) as $size): ?>
                      <option value="<?= htmlspecialchars((string) $size) ?>" <?= (string) $size === (string) $item['size_label'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars((string) $size) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="form-group">
                  <label>Quantité</label>
                  <input type="number" name="qty" min="1" max="<?= max(1, (int) ($item['stock'] ?? 1)) ?>" value="<?= (int) $item['quantity'] ?>">
                </div>

                <div class="form-group">
                  <label>Stock</label>
                  <div class="stock-pill <?= htmlspecialchars((string) ($item['stock_status'] ?? 'in')) ?>">
                    <?= (int) ($item['stock'] ?? 0) ?> disponible(s)
                  </div>
                </div>

                <div class="cart-edit-actions">
                  <button class="btn btn-light" type="submit">Mettre à jour</button>
                  <a class="btn btn-light" href="produit.php?id=<?= (int) $item['product_id'] ?>">Voir produit</a>
                </div>
              </form>

              <form action="actions/remove_from_cart.php" method="post" class="cart-remove-form">
                <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>">
                <input type="hidden" name="size" value="<?= htmlspecialchars((string) $item['size_label']) ?>">
                <button class="btn btn-light" type="submit">Supprimer cette ligne</button>
              </form>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

      <aside class="panel cart-summary-v74">
        <h2>Résumé</h2>
        <div class="summary-row"><span>Sous-total</span><strong><?= format_eur((float) $cart['subtotal']) ?></strong></div>
        <div class="summary-row"><span>Livraison</span><strong><?= format_eur((float) $cart['shipping_amount']) ?></strong></div>
        <div class="summary-row total"><span>Total</span><strong><?= format_eur((float) $cart['total_amount']) ?></strong></div>

        <div class="cart-summary-actions">
          <a class="btn btn-dark" href="checkout.php">Passer la commande</a>
          <a class="btn btn-light" href="catalogue.php">Continuer mes achats</a>
        </div>
      </aside>
    </div>
  <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/store_footer.php'; ?>