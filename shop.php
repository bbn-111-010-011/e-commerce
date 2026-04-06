<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/store_header.php';
$pageTitle = 'Accueil - Chacha';
?>
<section class="hero">
  <div class="container hero-grid">
    <div class="panel hero-main">
      <span class="badge">Collection moderne & élégante</span>
      <h1>Boutique avec panier protégé et favoris</h1>
      <p>Quand un client ajoute un article au panier ou aux favoris, il doit être connecté. Sinon il est redirigé vers la page de connexion ou de création de compte.</p>
      <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:18px">
        <a class="btn btn-light" href="catalogue.php">Voir le catalogue</a>
        <a class="btn btn-dark" href="favorites.php">Voir mes favoris</a>
      </div>
    </div>
    <div class="panel hero-card">
      <div class="small">À la une</div>
      <h2 style="margin:8px 0 0">Sélection rapide</h2>
      <div id="hero-spotlight" class="hero-list"></div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <div><div class="small">Vedette</div><h2>Produits mis en avant</h2></div>
      <a class="btn btn-light" href="catalogue.php">Tout voir</a>
    </div>
    <div id="home-featured" class="grid products"></div>
  </div>
</section>

<section class="section">
  <div class="container panel banner">
    <div class="feature"><strong>Connexion requise</strong><div class="muted">pour panier et favoris</div></div>
    <div class="feature"><strong>Favoris</strong><div class="muted">consultables dans une page dédiée</div></div>
    <div class="feature"><strong>Tailles</strong><div class="muted">S, M, L, XL, XXL</div></div>
    <div class="feature"><strong>Panier</strong><div class="muted">lié à la session du client connecté</div></div>
  </div>
</section>

<section class="section"><div class="container"><div class="section-head"><div><div class="small">Catégorie</div><h2>Caftan femme</h2></div></div><div id="home-caftan-femme" class="grid products"></div></div></section>
<section class="section"><div class="container"><div class="section-head"><div><div class="small">Catégorie</div><h2>Robes de soirée</h2></div></div><div id="home-robe" class="grid products"></div></div></section>
<section class="section"><div class="container"><div class="section-head"><div><div class="small">Catégorie</div><h2>Karakou</h2></div></div><div id="home-karakou" class="grid products"></div></div></section>

<script src="assets/js/home.js"></script>
<?php require_once __DIR__ . '/includes/store_footer.php'; ?>