<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/shop_bootstrap.php';

$pageTitle = 'Produit - Chacha';
$error = get_flash('error');
$success = get_flash('success');
require_once __DIR__ . '/includes/store_header.php';
?>
<section class="container section">
  <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

  <div class="product-layout product-layout-v73">
    <div class="product-gallery-card">
      <div class="product-image product-image-main">
        <img id="product-image" src="" alt="">
      </div>
      <div id="product-thumbs" class="product-thumbs"></div>
    </div>

    <div class="panel product-box product-box-v73">
      <div class="product-topline">
        <div id="product-category" class="small"></div>
        <span id="product-badge" class="badge-inline"></span>
      </div>

      <h1 id="product-name"></h1>

      <div class="price-row price-row-v73">
        <div class="price" id="product-price"></div>
        <div class="old-price" id="product-old-price"></div>
      </div>

      <div class="product-status-row">
        <span id="product-stock-badge" class="stock-badge"></span>
        <span id="product-stock-message" class="muted"></span>
      </div>

      <p id="product-description" class="muted product-description-v73"></p>

      <div class="product-meta-grid">
        <div class="meta-card"><strong>Couleur</strong><span id="product-color"></span></div>
        <div class="meta-card"><strong>SKU</strong><span id="product-sku"></span></div>
        <div class="meta-card"><strong>Catégorie</strong><span id="product-category-2"></span></div>
        <div class="meta-card"><strong>Stock</strong><span id="product-stock"></span></div>
      </div>

      <div class="choice-box">
        <strong>Commander plusieurs tailles</strong>
        <p class="small muted" style="margin-top:8px">Exemple : 1 en S + 1 en L pour le même produit.</p>
        <div id="multi-size-grid" class="multi-size-grid"></div>
        <div id="size-help" class="small muted"></div>
      </div>

      <div class="qty-row product-actions-v73">
        <button id="addBtn" class="btn btn-dark" type="button">Ajouter la sélection au panier</button>
        <button id="favoriteBtn" class="btn btn-light" type="button">Ajouter aux favoris</button>
      </div>
    </div>
  </div>

  <section class="section">
    <div class="section-head">
      <div>
        <div class="small">Même catégorie</div>
        <h2>Produits similaires</h2>
      </div>
    </div>
    <div id="related-grid" class="grid products"></div>
  </section>
</section>
<script src="assets/js/product.js?v=7.3.3"></script>
<?php require_once __DIR__ . '/includes/store_footer.php'; ?>