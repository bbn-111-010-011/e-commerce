<?php
declare(strict_types=1);

$pageTitle = 'Catalogue - Chacha';
require_once __DIR__ . '/includes/store_header.php';
?>
<section class="container page-title">
  <div class="small">Catalogue</div>
  <h1>Tous les produits</h1>
  <p class="muted">Ajout au panier et aux favoris réservé aux clients connectés.</p>
  <input id="searchInput" class="input" type="text" placeholder="Rechercher un produit..." style="width:min(420px,100%);margin-top:12px">
  <div id="catalog-filters" class="filters">
    <button class="pill active" data-category="all">Tous</button>
  </div>
</section>
<section class="container section">
  <div id="catalog-grid" class="grid products"></div>
</section>
<script src="assets/js/catalogue.js?v=7.1.5"></script>
<?php require_once __DIR__ . '/includes/store_footer.php'; ?>