<?php
declare(strict_types=1);


require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/shop_bootstrap.php';
require_login();

$favorites = favorite_products($pdo, (int) current_user()['id']);
$pageTitle = 'Favoris - Chacha';
$error = get_flash('error');
$success = get_flash('success');

require_once __DIR__ . '/includes/store_header.php';
?>
<section class="container section">
  <div class="section-head"><div><div class="small">Liste de favoris</div><h1>Mes favoris</h1></div></div>

  <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

  <?php if (!$favorites): ?>
    <div class="card">
      <p>Vous n'avez encore aucun favori.</p>
      <div class="actions"><a class="btn btn-dark" href="catalogue.php">Découvrir les produits</a></div>
    </div>
  <?php else: ?>
    <div class="grid products">
      <?php foreach ($favorites as $product): ?>
        <article class="card">
          <div class="card-media">
            <span class="flag"><?= htmlspecialchars((string) $product['badge']) ?></span>
            <img src="<?= htmlspecialchars((string) $product['image']) ?>" alt="<?= htmlspecialchars((string) $product['name']) ?>">
          </div>
          <div class="card-body">
            <div class="small"><?= htmlspecialchars((string) $product['categoryLabel']) ?></div>
            <h3><?= htmlspecialchars((string) $product['name']) ?></h3>
            <div class="price-row">
              <div>
                <div class="price"><?= format_eur((float) $product['price']) ?></div>
                <div class="old-price"><?= format_eur((float) $product['oldPrice']) ?></div>
              </div>
              <div class="muted"><?= htmlspecialchars((string) $product['color']) ?></div>
            </div>
            <div class="card-actions">
              <button class="btn btn-dark" type="button" onclick="addToCartServer(<?= (int) $product['id'] ?>,1,'M')">Ajouter au panier</button>
              <button class="btn btn-light" type="button" data-favorite-id="<?= (int) $product['id'] ?>" onclick="toggleFavoriteServer(<?= (int) $product['id'] ?>)">Retirer des favoris</button>
              <a class="btn btn-light" href="produit.php?id=<?= (int) $product['id'] ?>">Voir</a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/store_footer.php'; ?>