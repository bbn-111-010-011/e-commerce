<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/product_admin_helpers.php';
$categories = admin_categories();
?>
<section class="card" style="max-width:980px;">
    <span class="muted">Catalogue MySQL</span>
    <h1>Ajouter un produit</h1>
    <p class="muted">Formats image autorisés : jpg, jpeg, png, webp. Taille max : 5 Mo.</p>

    <form action="product-save.php" method="post" enctype="multipart/form-data" novalidate>
        <div class="grid grid-2">
            <div class="form-group"><label for="name">Nom du produit</label><input id="name" name="name" type="text" required></div>
            <div class="form-group"><label for="category">Catégorie</label><select id="category" name="category" class="select" style="width:100%;height:48px;"><?php foreach ($categories as $value => $label): ?><option value="<?= htmlspecialchars($value) ?>"><?= htmlspecialchars($label) ?></option><?php endforeach; ?></select></div>
        </div>
        <div class="grid grid-2">
            <div class="form-group"><label for="price">Prix actuel</label><input id="price" name="price" type="number" step="0.01" min="0" required></div>
            <div class="form-group"><label for="old_price">Ancien prix</label><input id="old_price" name="old_price" type="number" step="0.01" min="0"></div>
        </div>
        <div class="grid grid-2">
            <div class="form-group"><label for="stock">Stock</label><input id="stock" name="stock" type="number" min="0" value="0" required></div>
            <div class="form-group"><label for="color">Couleur</label><input id="color" name="color" type="text" value="Noir" required></div>
        </div>
        <div class="grid grid-2">
            <div class="form-group"><label for="badge">Badge</label><input id="badge" name="badge" type="text" value="Nouveauté"></div>
            <div class="form-group"><label for="image">Image produit</label><input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp"></div>
        </div>
        <div class="form-group"><label for="description">Description</label><input id="description" name="description" type="text" value="Produit élégant pour la boutique Chacha."></div>
        <div class="form-group"><label><input type="checkbox" name="featured" value="1"> Mettre en vedette</label></div>
        <div class="actions"><button class="btn btn-dark" type="submit">Enregistrer</button><a class="btn btn-light" href="products.php">Annuler</a></div>
    </form>
</section>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>