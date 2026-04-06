<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/product_admin_helpers.php';
$categories = admin_categories();
$productId = (int) ($_GET['id'] ?? 0);
$product = admin_find_product_db($pdo, $productId);
?>
<section class="card" style="max-width:980px;">
    <span class="muted">Catalogue MySQL</span>
    <h1>Modifier un produit</h1>
    <p class="muted">Vous pouvez remplacer l’image du produit. Formats autorisés : jpg, jpeg, png, webp.</p>

    <?php if (!$product): ?>
        <div class="alert alert-error">Produit introuvable.</div>
    <?php else: ?>
        <form action="product-save.php" method="post" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
            <input type="hidden" name="current_image" value="<?= htmlspecialchars((string) $product['image']) ?>">
            <div class="grid grid-2">
                <div class="form-group"><label for="name">Nom du produit</label><input id="name" name="name" type="text" value="<?= htmlspecialchars((string) $product['name']) ?>" required></div>
                <div class="form-group"><label for="category">Catégorie</label><select id="category" name="category" class="select" style="width:100%;height:48px;"><?php foreach ($categories as $value => $label): ?><option value="<?= htmlspecialchars($value) ?>" <?= $value === ($product['category'] ?? '') ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option><?php endforeach; ?></select></div>
            </div>
            <div class="grid grid-2">
                <div class="form-group"><label for="price">Prix actuel</label><input id="price" name="price" type="number" step="0.01" min="0" value="<?= htmlspecialchars((string) $product['price']) ?>" required></div>
                <div class="form-group"><label for="old_price">Ancien prix</label><input id="old_price" name="old_price" type="number" step="0.01" min="0" value="<?= htmlspecialchars((string) ($product['oldPrice'] ?? 0)) ?>"></div>
            </div>
            <div class="grid grid-2">
                <div class="form-group"><label for="stock">Stock</label><input id="stock" name="stock" type="number" min="0" value="<?= htmlspecialchars((string) ($product['stock'] ?? 0)) ?>" required></div>
                <div class="form-group"><label for="color">Couleur</label><input id="color" name="color" type="text" value="<?= htmlspecialchars((string) ($product['color'] ?? 'Noir')) ?>" required></div>
            </div>
            <div class="grid grid-2">
                <div class="form-group"><label for="badge">Badge</label><input id="badge" name="badge" type="text" value="<?= htmlspecialchars((string) ($product['badge'] ?? 'Nouveauté')) ?>"></div>
                <div class="form-group"><label for="image">Remplacer l'image</label><input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp"></div>
            </div>
            <div class="form-group"><label for="description">Description</label><input id="description" name="description" type="text" value="<?= htmlspecialchars((string) ($product['description'] ?? '')) ?>"></div>
            <div class="form-group"><label><input type="checkbox" name="featured" value="1" <?= !empty($product['featured']) ? 'checked' : '' ?>> Mettre en vedette</label></div>
            <div class="form-group"><img src="../<?= htmlspecialchars((string) $product['image']) ?>" alt="<?= htmlspecialchars((string) $product['name']) ?>" style="width:120px;height:160px;object-fit:cover;border-radius:12px;"></div>
            <div class="actions"><button class="btn btn-dark" type="submit">Mettre à jour</button><a class="btn btn-light" href="products.php">Annuler</a></div>
        </form>
    <?php endif; ?>
</section>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>